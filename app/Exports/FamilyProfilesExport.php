<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * Main Export class — returns multiple sheets.
 *
 * Ang isang HOUSEHOLD = isang Household No. (hal. Z2-34004077).
 * Nasa loob nito ang Family A (main) at ang mga sub-household na Family B, C... (hal. Z2-34004077-B),
 * gaya ng ipinapakita sa Household Profiling page.
 *
 * Tinatanggap ang kahit anong collection ng FamilyProfile (lahat ng zone, filtered, o isang household lang).
 * Awtomatikong isinasama ang sub-households (childFamilies) kahit wala sila sa ipinasang collection.
 */
class FamilyProfilesExport implements WithMultipleSheets
{
    protected Collection $households;

    public function __construct($families)
    {
        $this->households = self::groupHouseholds($families);
    }

    public function sheets(): array
    {
        return [
            new CBMSFormSheet($this->households),
            new FamilyProfilesSummarySheet($this->households),
        ];
    }

    /**
     * Gawing mga household ang listahan ng families:
     * [ ['code' => 'Z2-34004077', 'families' => [Family A, Family B, ...]], ... ]
     */
    public static function groupHouseholds($families): Collection
    {
        $natural  = SORT_NATURAL | SORT_FLAG_CASE;
        $families = collect($families)->unique('id')->values();
        $ids      = $families->pluck('id');

        $tops = $families
            ->filter(fn($f) => !$f->parent_family_id || !$ids->contains($f->parent_family_id))
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

    /** A para sa main family; B, C... para sa sub-household (kinukuha sa dulo ng family code). */
    public static function familyLetter($family, int $index): string
    {
        if (!$family->parent_family_id && $index === 0) {
            return 'A';
        }
        return preg_match('/-([A-Z])$/', (string) $family->family_code, $m) ? $m[1] : chr(65 + $index);
    }

    public static function isIp($member): bool
    {
        return (bool) $member->is_ip || strtolower(trim($member->ethnicity ?? '')) === 'ip';
    }

    public static function livingMembers($family): Collection
    {
        return $family->members->where('is_deceased', false)->values();
    }
}

/**
 * =========================================================
 * SHEET 1: CBMS Form — isang row bawat LIVING na member
 * =========================================================
 */
class CBMSFormSheet implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected Collection $households;

    /** Row numbers ng mga member ng sub-household (B, C...) para sa kulay. */
    protected array $subRows = [];

    private const COLORS = [
        // group           => [row 1 (dark), row 2 (light)]
        'Household Info'   => ['065F46', '047857'],
        'Name of Member'   => ['065F46', '10B981'],
        'Member Info'      => ['334155', '475569'],
        'Programs'         => ['3730A3', '4F46E5'],
        'PhilHealth'       => ['155E75', '0891B2'],
        'Health'           => ['9F1239', 'E11D48'],
        'Family Planning'  => ['92400E', 'D97706'],
        'WASH'             => ['075985', '0284C7'],
        'Notes'            => ['334155', '475569'],
    ];

    public function __construct(Collection $households)
    {
        $this->households = $households;
    }

    /**
     * [key, group, heading, width, centered]
     * Isang lugar lang ito para sa headings, widths at alignment — hindi na kailangang
     * baguhin ang column letters sa maraming lugar kapag may idinagdag na column.
     */
    protected static function spec(): array
    {
        return [
            ['visit',      'Household Info', 'Date of Visit',          12, true],
            ['region',     'Household Info', 'Region',                 10, true],
            ['province',   'Household Info', 'Province',               12, false],
            ['city',       'Household Info', 'City / Municipality',    16, false],
            ['barangay',   'Household Info', 'Barangay',               14, false],
            ['zone',       'Household Info', 'Zone / Purok',           10, true],
            ['hh',         'Household Info', 'HH No.',                 16, true],
            ['family',     'Household Info', 'Family',                  8, true],
            ['fcode',      'Household Info', 'Family Code',            18, true],
            ['head',       'Household Info', 'Family Head',            24, false],

            ['last',       'Name of Member', 'Last Name',              18, false],
            ['first',      'Name of Member', 'First Name',             18, false],
            ['middle',     'Name of Member', 'Middle Name',            15, false],

            ['relation',   'Member Info',    'Relation to HH Head',    14, true],
            ['dob',        'Member Info',    'Date of Birth',          12, true],
            ['age',        'Member Info',    'Age',                     6, true],
            ['sex',        'Member Info',    'Sex',                     6, true],
            ['civil',      'Member Info',    'Civil Status',           10, true],
            ['edu',        'Member Info',    'Educational Attainment', 24, false],
            ['relig',      'Member Info',    'Religion',               15, false],
            ['ethn',       'Member Info',    'Ethnicity',              12, false],

            ['4ps',        'Programs',       '4Ps Member?',            10, true],
            ['4ps_id',     'Programs',       '4Ps HH ID',              14, true],
            ['ip',         'Programs',       'IP?',                     7, true],

            ['ph_id',      'PhilHealth',     'PhilHealth ID',          18, true],
            ['ph_member',  'PhilHealth',     'Membership',             12, true],
            ['ph_cat',     'PhilHealth',     'Category',               20, false],

            ['pwd',        'Health',         'With Disability?',       12, true],
            ['med',        'Health',         'Medical History',        22, false],
            ['med_spec',   'Health',         'Medical Specify',        20, false],
            ['class',      'Health',         'Classification',         14, true],
            ['immun',      'Health',         'Immunization',           18, false],

            ['lmp',        'Family Planning','LMP',                    12, true],
            ['use_fp',     'Family Planning','Using FP?',              10, true],
            ['fp_method',  'Family Planning','FP Method',              14, true],
            ['fp_status',  'Family Planning','FP Status',              10, true],

            ['water',      'WASH',           'Water Source',           14, false],
            ['toilet',     'WASH',           'Toilet Facility',        30, false],

            ['notes',      'Notes',          'Notes',                  25, false],
        ];
    }

    public function title(): string
    {
        return 'CBMS Form';
    }

    public function collection()
    {
        $rows = collect();
        $this->subRows = [];

        foreach ($this->households as $household) {
            foreach ($household['families'] as $i => $family) {
                $letter = FamilyProfilesExport::familyLetter($family, $i);

                foreach (FamilyProfilesExport::livingMembers($family) as $member) {
                    $rows->push($this->row($household['code'], $letter, $family, $member));

                    if ($i > 0) {
                        $this->subRows[] = $rows->count() + 2; // +2 dahil dalawang heading rows
                    }
                }
            }
        }

        return $rows;
    }

    protected function row(string $hhCode, string $letter, $family, $member): array
    {
        $dash = fn($v) => ($v === null || $v === '') ? '—' : $v;
        $upper = fn($v) => ($v === null || $v === '') ? '—' : strtoupper($v);
        $date = fn($v) => $v ? Carbon::parse($v)->format('m/d/Y') : '—';

        $useFp = $member->using_fp ?? null;
        $useFp = is_null($useFp)
            ? '—'
            : (in_array(strtolower((string) $useFp), ['1', 'y', 'yes', 'true'], true) ? 'Yes' : 'No');

        $values = [
            'visit'     => $dash($member->date_of_visit),
            'region'    => 'Region I',
            'province'  => 'Pangasinan',
            'city'      => 'Urdaneta City',
            'barangay'  => 'Bactad East',
            'zone'      => $dash($family->zone_purok),
            'hh'        => $hhCode,
            'family'    => $letter,
            'fcode'     => $family->family_code,
            'head'      => $family->head_formal_name,

            'last'      => $upper($member->last_name),
            'first'     => $upper($member->first_name),
            'middle'    => $upper($member->middle_name),

            'relation'  => $dash($member->relation_to_head),
            'dob'       => $date($member->date_of_birth),
            'age'       => $dash($member->age),
            'sex'       => $dash($member->sex),
            'civil'     => $dash($member->civil_status),
            'edu'       => $dash($member->education),
            'relig'     => $dash($member->religion),
            'ethn'      => $dash($member->ethnicity),

            '4ps'       => $member->is_4ps ? 'YES' : 'NO',
            '4ps_id'    => $dash($member->fourps_household_id ?? null),
            'ip'        => FamilyProfilesExport::isIp($member) ? 'YES' : 'NO',

            'ph_id'     => $dash($member->philhealth_id),
            'ph_member' => $dash($member->philhealth_member),
            'ph_cat'    => $dash($member->philhealth_category),

            'pwd'       => ($member->is_pwd ?? false) ? 'YES' : 'NO',
            'med'       => $dash($member->medical_history),
            'med_spec'  => $dash($member->medical_specify ?? null),
            'class'     => $dash($member->classification),
            'immun'     => $dash($member->immunization ?? null),

            'lmp'       => $date($member->lmp_date),
            'use_fp'    => $useFp,
            'fp_method' => $dash($member->fp_method),
            'fp_status' => $dash($member->fp_status),

            'water'     => $dash($member->water_source),
            'toilet'    => $dash($member->toilet_facility),

            'notes'     => $dash($family->notes ?? null),
        ];

        // Ayon sa pagkakasunod ng spec() para laging tugma ang headings at data
        return array_map(fn($col) => $values[$col[0]] ?? '—', self::spec());
    }

    public function headings(): array
    {
        $spec = self::spec();

        return [
            array_map(fn($c) => $c[1], $spec), // Row 1: groups
            array_map(fn($c) => $c[2], $spec), // Row 2: headings
        ];
    }

    public function columnWidths(): array
    {
        $widths = [];
        foreach (self::spec() as $i => $col) {
            $widths[Coordinate::stringFromColumnIndex($i + 1)] = $col[3];
        }
        return $widths;
    }

    public function styles(Worksheet $sheet)
    {
        $spec = self::spec();
        $n    = count($spec);
        $last = Coordinate::stringFromColumnIndex($n);
        $black = ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']];

        // ===== Headings (base style) =====
        $sheet->getStyle("A1:{$last}2")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders'   => ['allBorders' => $black],
        ]);

        // ===== Row 1: i-merge ang magkakasunod na parehong group + kulay bawat group =====
        $start = 1;
        while ($start <= $n) {
            $group = $spec[$start - 1][1];
            $end   = $start;
            while ($end < $n && $spec[$end][1] === $group) {
                $end++;
            }

            $a = Coordinate::stringFromColumnIndex($start);
            $b = Coordinate::stringFromColumnIndex($end);
            if ($end > $start) {
                $sheet->mergeCells("{$a}1:{$b}1");
            }

            [$dark, $light] = self::COLORS[$group] ?? ['334155', '475569'];
            $sheet->getStyle("{$a}1:{$b}1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($dark);
            $sheet->getStyle("{$a}2:{$b}2")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($light);

            $start = $end + 1;
        }

        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getRowDimension(2)->setRowHeight(45);

        // ===== Data rows =====
        $lastRow = $sheet->getHighestRow();
        if ($lastRow >= 3) {
            $sheet->getStyle("A3:{$last}{$lastRow}")->applyFromArray([
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '999999']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            foreach ($spec as $i => $col) {
                if ($col[4]) {
                    $letter = Coordinate::stringFromColumnIndex($i + 1);
                    $sheet->getStyle("{$letter}3:{$letter}{$lastRow}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            }

            // Mga member ng Family B, C... — light violet para madaling makita
            foreach ($this->subRows as $r) {
                $sheet->getStyle("A{$r}:{$last}{$r}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F5F3FF');
            }

            $sheet->setAutoFilter("A2:{$last}{$lastRow}");
        }

        $sheet->freezePane('A3');

        return [];
    }
}

/**
 * =========================================================
 * SHEET 2: Household Summary — isang row bawat HOUSEHOLD
 * (kasama na ang Family A at mga sub-household sa bilang)
 * =========================================================
 */
class FamilyProfilesSummarySheet implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected Collection $households;

    public function __construct(Collection $households)
    {
        $this->households = $households;
    }

    /** [heading, width, centered] */
    protected static function spec(): array
    {
        return [
            ['No.',                  5,  true],
            ['Household No.',        16, true],
            ['Head Last Name',       20, false],
            ['Head First Name',      20, false],
            ['Families',             10, true],
            ['Sub-household Heads',  32, false],
            ['Zone / Purok',         12, true],
            ['Complete Address',     30, false],
            ['Contact Number',       15, false],
            ['Living Members',       14, true],
            ['Deceased',             10, true],
            ['Seniors (60+)',        13, true],
            ['PWDs',                 8,  true],
            ['4Ps',                  8,  true],
            ['IP',                   8,  true],
            ['Notes',                30, false],
        ];
    }

    public function title(): string
    {
        return 'Household Summary';
    }

    public function collection()
    {
        $rows = collect();

        foreach ($this->households as $i => $household) {
            $families = $household['families'];
            $main     = $families->first();

            $members = $families->flatMap(fn($f) => $f->members);
            $living  = $members->where('is_deceased', false);

            $letters = $families->map(fn($f, $k) => FamilyProfilesExport::familyLetter($f, $k));

            $subHeads = $families->slice(1)->map(
                fn($f, $k) => FamilyProfilesExport::familyLetter($f, $k) . ': ' . $f->head_formal_name
            )->implode('; ');

            $rows->push([
                $i + 1,
                $household['code'],
                strtoupper($main->head_last_name ?? ''),
                strtoupper($main->head_first_name ?? ''),
                $letters->implode(' · '),
                $subHeads !== '' ? $subHeads : '—',
                $main->zone_purok ?? '—',
                $main->complete_address ?? '—',
                $main->contact_number ?? '—',
                $living->count(),
                $members->where('is_deceased', true)->count(),
                $living->where('age', '>=', 60)->count(),
                $living->filter(fn($m) => $m->is_pwd ?? false)->count(),
                $living->where('is_4ps', true)->count(),
                $living->filter(fn($m) => FamilyProfilesExport::isIp($m))->count(),
                $main->notes ?? '—',
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return array_map(fn($c) => $c[0], self::spec());
    }

    public function columnWidths(): array
    {
        $widths = [];
        foreach (self::spec() as $i => $col) {
            $widths[Coordinate::stringFromColumnIndex($i + 1)] = $col[1];
        }
        return $widths;
    }

    public function styles(Worksheet $sheet)
    {
        $spec = self::spec();
        $last = Coordinate::stringFromColumnIndex(count($spec));

        $sheet->getStyle("A1:{$last}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '065F46']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $lastRow = $sheet->getHighestRow();
        if ($lastRow >= 2) {
            $sheet->getStyle("A2:{$last}{$lastRow}")->applyFromArray([
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '999999']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            foreach ($spec as $i => $col) {
                if ($col[2]) {
                    $letter = Coordinate::stringFromColumnIndex($i + 1);
                    $sheet->getStyle("{$letter}2:{$letter}{$lastRow}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            }

            $sheet->setAutoFilter("A1:{$last}{$lastRow}");
        }

        $sheet->freezePane('A2');

        return [];
    }
}