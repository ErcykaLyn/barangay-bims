<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Template-based export (Household_Form_1Page.xlsx, sheet "Form2").
 *
 * Isang sheet = isang HOUSEHOLD (isang Household No.). Nasa iisang sheet ang Family A at ang mga
 * sub-household (Family B, C...), gaya ng sa orihinal na form: ang Household No. ay nasa unang row
 * lang, at ang Head ng sub-household ay may sariling "1 - Head" row na WALANG Household No.
 * (ganito rin ang pagkakabasa ng importer, kaya tugma kapag ni-re-import).
 */
class FamilyProfilingExport
{
    protected $families;
    protected $type;

    // Template: Sheet "Form2", data rows = 11 hanggang 46
    private const START_ROW = 11;
    private const END_ROW   = 46;
    private const BARANGAY  = 'Bactad East';

    public function __construct(Collection $families, string $type = 'zone')
    {
        $this->families = $families;
        $this->type = $type;
    }

    public function download($fileName)
    {
        $templatePath = storage_path('app/templates/Household_Form_1Page.xlsx');

        if (!file_exists($templatePath)) {
            abort(404, 'Excel template not found.');
        }

        $spreadsheet   = IOFactory::load($templatePath);
        $templateSheet = $spreadsheet->getSheetByName('Form2') ?? $spreadsheet->getSheet(0);

        $households = $this->groupHouseholds();
        if ($this->type === 'household') {
            $households = $households->take(1);
        }

        // Hatiin sa pages: 36 rows kada sheet. Kapag sobra ang members, may susunod na sheet
        // (hindi na basta binabalewala ang sobra).
        $capacity = self::END_ROW - self::START_ROW + 1;
        $pages    = [];
        foreach ($households as $household) {
            $chunks = array_chunk($this->householdLines($household), $capacity);
            foreach ($chunks as $p => $chunk) {
                $pages[] = ['code' => $household['code'], 'page' => $p + 1, 'lines' => $chunk];
            }
        }

        $usedTitles = [];
        foreach ($pages as $page) {
            // I-clone ang buong sheet (kasama merged cells, styles, widths, validations)
            $sheet = clone $templateSheet;

            $wanted = $page['code'] . ($page['page'] > 1 ? " ({$page['page']})" : '');
            $sheet->setTitle($this->uniqueTitle($wanted, $usedTitles));
            $spreadsheet->addSheet($sheet);

            $this->fillRows($sheet, $page['code'], $page['lines']);
        }

        // Tanggalin ang original template sheet kung may na-generate na
        if (!empty($pages)) {
            $spreadsheet->removeSheetByIndex($spreadsheet->getIndex($templateSheet));
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer   = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Gawing mga household ang listahan ng families.
     * Awtomatikong isinasama ang sub-households (childFamilies) kahit wala sila sa ipinasang collection,
     * kaya kumpleto ang household kahit isang family lang ang ipinasa (hal. "Export Household").
     *
     * @return Collection [ ['code' => 'Z2-34004077', 'families' => Collection[A, B, ...]], ... ]
     */
    private function groupHouseholds(): Collection
    {
        $natural  = SORT_NATURAL | SORT_FLAG_CASE;
        $families = collect($this->families)->unique('id')->values();
        $ids      = $families->pluck('id');

        $tops = $families
            ->filter(fn ($f) => !$f->parent_family_id || !$ids->contains($f->parent_family_id))
            ->sortBy('family_code', $natural)
            ->values();

        return $tops->map(function ($top) use ($families, $natural) {
            $children = $families->where('parent_family_id', $top->id)
                ->concat($top->childFamilies)
                ->unique('id')
                ->sortBy('family_code', $natural)
                ->values();

            // Kapag sub-household ang top (wala ang parent sa listahan), ang code ng parent ang HH No.
            $code = $top->parent_family_id
                ? (optional($top->parentFamily)->family_code ?? $top->family_code)
                : $top->family_code;

            return [
                'code'     => $code,
                'families' => collect([$top])->concat($children)->values(),
            ];
        })->values();
    }

    /**
     * Lahat ng LIVING members ng household, nakaayos: Family A (head muna), tapos Family B (head muna)...
     * @return array<int, array{member: mixed, family: mixed}>
     */
    private function householdLines(array $household): array
    {
        $lines = [];

        foreach ($household['families'] as $family) {
            $members = collect($family->members)
                ->where('is_deceased', false)
                ->values()
                ->sortBy(function ($m, $k) use ($family) {
                    if ($this->isFamilyHead($m, $family)) {
                        return $k;
                    }
                    $isHeadRel = strtolower(trim((string) $m->relation_to_head)) === 'head';
                    return ($isHeadRel ? 100000 : 200000) + $k; // stable: sunod sa dating pagkakasunod
                })
                ->values();

            foreach ($members as $member) {
                $lines[] = ['member' => $member, 'family' => $family];
            }
        }

        return $lines;
    }

    private function fillRows($sheet, string $householdCode, array $lines): void
    {
        $row = self::START_ROW;

        foreach ($lines as $idx => $line) {
            $member = $line['member'];
            $family = $line['family'];

            // ---------- A-C: Household info ----------
            // A: Date of Visit
            $visit = $member->date_of_visit;
            if ($visit instanceof \DateTimeInterface || (is_string($visit) && preg_match('/^\d{4}-\d{2}-\d{2}/', $visit))) {
                $this->setDate($sheet, 'A' . $row, $visit);
            } elseif (filled($visit)) {
                $this->setText($sheet, 'A' . $row, $visit);
            }

            // B: Household No. — isang beses lang sa unang row ng sheet. Ang Head ng sub-household (Family B...)
            //    ay sadyang walang Household No. para makilala itong sub-household ng nasa itaas.
            if ($idx === 0) {
                $this->setText($sheet, 'B' . $row, $householdCode);
            }

            // C: Barangay
            $this->setText($sheet, 'C' . $row, self::BARANGAY);

            // ---------- D-F: Name ----------
            $sheet->setCellValue('D' . $row, $member->last_name);
            $sheet->setCellValue('E' . $row, $member->first_name);
            $sheet->setCellValue('F' . $row, $member->middle_name);

            // G: Relationship ("1 - Head", "2 - Spouse"... gaya ng listahan sa template)
            $sheet->setCellValue('G' . $row, $this->relationLabel($member, $family));

            // H: Date of Birth
            $this->setDate($sheet, 'H' . $row, $member->date_of_birth);

            // I: Age (naka-date format sa template kaya i-force General)
            $sheet->setCellValue('I' . $row, $this->ageLabel($member));
            $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('General');

            // J-N
            $sheet->setCellValue('J' . $row, $member->sex);
            $sheet->setCellValue('K' . $row, $member->civil_status);
            $sheet->setCellValue('L' . $row, $member->education);
            $sheet->setCellValue('M' . $row, $member->religion);
            $sheet->setCellValue('N' . $row, $member->ethnicity);

            // O: 4Ps
            $sheet->setCellValue('O' . $row, $member->is_4ps ? 'Yes' : 'No');

            // P-R: PhilHealth
            $this->setText($sheet, 'P' . $row, $member->philhealth_id);
            $sheet->setCellValue('Q' . $row, $member->philhealth_member);
            $sheet->setCellValue('R' . $row, $member->philhealth_category);

            // S: Medical History (+ specify kung "Others")
            $medical = trim((string) $member->medical_history);
            if ($medical !== '' && filled($member->medical_specify)) {
                $medical .= ' - ' . $member->medical_specify;
            }
            $sheet->setCellValue('S' . $row, $medical);

            // T:U = Classification by Age/Health Risk Group
            $sheet->setCellValue('T' . $row, $member->classification);

            // Immunization = walang column sa template, kaya hindi na-export

            // V: LMP
            $this->setDate($sheet, 'V' . $row, $member->lmp_date);

            // W: Using any FP method? Y/N  (field ay `using_fp`; kung wala, hulaan sa FP method)
            $sheet->setCellValue('W' . $row, $this->usingFp($member));

            // X: FP Method, Y: FP Status
            $sheet->setCellValue('X' . $row, $member->fp_method);
            $sheet->setCellValue('Y' . $row, $member->fp_status);

            // Z: Water Source, AA: Toilet Facility
            $sheet->setCellValue('Z' . $row, $member->water_source);
            $sheet->setCellValue('AA' . $row, $member->toilet_facility);

            $row++;
        }
    }

    private function isFamilyHead($member, $family): bool
    {
        return strcasecmp(trim((string) $member->first_name), trim((string) $family->head_first_name)) === 0
            && strcasecmp(trim((string) $member->last_name), trim((string) $family->head_last_name)) === 0;
    }

    private function relationLabel($member, $family): string
    {
        // Ang head ng bawat family (A, B, C...) ay laging "1 - Head"
        if ($this->isFamilyHead($member, $family)) {
            return '1 - Head';
        }

        $rel = trim((string) $member->relation_to_head);

        return match (strtolower($rel)) {
            'head'     => '1 - Head',
            'spouse'   => '2 - Spouse',
            'son'      => '3 - Son',
            'daughter' => '4 - Daughter',
            'others'   => '5 - Others',
            'member', '' => '',            // walang Relationship sa orihinal na data
            default    => '5 - ' . $rel,   // hal. "5 - Sister"
        };
    }

    private function ageLabel($member)
    {
        if ($member->date_of_birth) {
            $dob = Carbon::parse($member->date_of_birth);
            if (!$dob->isFuture()) {
                $years = $dob->age;
                if ($years >= 1) {
                    return $years;
                }
                $months = (int) floor(abs($dob->diffInMonths(Carbon::now())));
                return $months . ' ' . ($months === 1 ? 'month' : 'months');
            }
        }

        return $member->age;
    }

    private function usingFp($member): ?string
    {
        $using = $member->using_fp ?? null;

        if (!is_null($using)) {
            return in_array(strtolower((string) $using), ['1', 'y', 'yes', 'true'], true) ? 'Y' : 'N';
        }

        return filled($member->fp_method) ? 'Y' : null;
    }

    private function setText($sheet, string $cell, $value): void
    {
        $sheet->setCellValueExplicit($cell, (string) ($value ?? ''), DataType::TYPE_STRING);
    }

    private function setDate($sheet, string $cell, $value): void
    {
        if (empty($value)) {
            return;
        }
        $sheet->setCellValue($cell, Date::PHPToExcel(Carbon::parse($value)));
        $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('yyyy-mm-dd');
    }

    /** Unique at valid na sheet name (hindi case-sensitive ang Excel). */
    private function uniqueTitle(string $wanted, array &$used): string
    {
        $base  = $this->safeTitle($wanted);
        $title = $base;
        $i     = 2;

        while (in_array(strtolower($title), $used, true)) {
            $suffix = '-' . $i++;
            $title  = substr($base, 0, 31 - strlen($suffix)) . $suffix;
        }

        $used[] = strtolower($title);
        return $title;
    }

    private function safeTitle(?string $title): string
    {
        // Bawal ang : \ / ? * [ ] sa sheet name, max 31 chars
        $clean = preg_replace('/[:\\\\\/\?\*\[\]]/', '-', (string) $title);
        return substr($clean !== '' ? $clean : 'Sheet', 0, 31);
    }
}