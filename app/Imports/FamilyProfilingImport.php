<?php

namespace App\Imports;

use App\Models\FamilyProfile;
use App\Models\FamilyMember;
use App\Models\Resident;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class FamilyProfilingImport implements ToCollection, WithChunkReading, WithCalculatedFormulas
{
    public $imported = 0;
    public $skipped  = 0;
    public $families = 0;
    public $errors   = [];
    public $notices  = [];
    public $residents     = 0;
    public $registryCount = 0;

    private const BARANGAY = 'Bactad East';

    private int $rowNo = 0;
    private bool $headerFound = false;
    private bool $isForm2 = false;
    private int $nameCol = 4;

    private string $zone;
    private bool $forceZone = false;
    private bool $zoneRewriteNoticed = false;
    private $currentFamily = null;
    private ?string $currentHH = null;
    private ?string $currentVisit = null;
    private ?int $mainFamilyId = null;
    private $currentWater  = null;
    private $currentToilet = null;
    private array $usedCodes = [];

    /**
     * @param string $defaultZone  Zone na gagamitin kapag walang mabasang zone sa Household No.
     * @param bool   $forceZone    Kapag true, ang $defaultZone ang masusunod at papalitan ang
     *                             "Z#" prefix ng Household No. para tumugma (hal. Z1-xxxx -> Z3-xxxx).
     *                             Default false = dating behavior (zone ay kinukuha sa Household No.).
     */
    public function __construct(string $defaultZone = 'Zone 1', bool $forceZone = false)
    {
        $this->zone      = $defaultZone;
        $this->forceZone = $forceZone;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $this->rowNo++;
                try {
                    $this->processRow($row);
                } catch (\Throwable $e) {
                    $this->skipped++;
                    $this->errors[] = "Row {$this->rowNo}: " . $e->getMessage();
                }
            }
        });
    }

    private function processRow($row): void
    {
        if (!$this->headerFound) {
            $this->detectHeader($row);
            return;
        }

        $L = $this->nameCol;

        $householdNo = $this->clean($row[1] ?? null);
        $householdNo = $this->lockHouseholdZone($householdNo);
        $lastName    = $this->clean($row[$L] ?? null);
        $firstName   = $this->clean($row[$L + 1] ?? null);
        $middleName  = $this->clean($row[$L + 2] ?? null);

        $hasName = $lastName !== '' && $firstName !== ''
            && !is_numeric($lastName) && !is_numeric($firstName)
            && !str_starts_with(strtolower($lastName), 'last name')
            && mb_strlen($lastName) <= 40 && mb_strlen($firstName) <= 40;

        $dobCol = $this->findDobCol($row);
        $c      = $this->columns($dobCol);
        $cell   = fn($i) => $i === null ? '' : $this->clean($row[$i] ?? null);

        $dob         = $this->parseDate($row[$dobCol] ?? null);
        $visit       = $this->isForm2 ? $this->parseVisit($row[0] ?? null) : null;
        $relationRaw = $this->normalizeRelation($cell($c['rel']));
        $relationRaw = $this->fixChildRelation($relationRaw, $this->parseSex($cell($c['sex'])));
        $relationOth = $cell($c['other']);

        $water  = $this->nullable($this->decodeWater($cell($c['water'])));
        $toilet = $this->nullable($this->decodeToilet($cell($c['toilet'])));

        $isHead          = (bool) preg_match('/^1\s*-/', $relationRaw);
        $looksLikeMember = preg_match('/^\d\s*-/', $relationRaw) === 1 || $dob !== null;

        if (!$hasName) {
            if ($dob !== null && $this->currentFamily) {
                $this->skipped++;
                $this->errors[] = "Row {$this->rowNo}: may DOB ({$dob}) pero walang pangalan — hindi na-import.";
                return;
            }
            if (($water || $toilet) && $this->currentFamily && !$looksLikeMember) {
                $this->applyFacilities($water, $toilet);
            }
            return;
        }

        if (!$looksLikeMember) {
            if ($householdNo !== '') {
                $this->skipped++;
                $this->errors[] = "Row {$this->rowNo}: {$firstName} {$lastName} — walang DOB at Relationship, hindi na-import.";
            }
            return;
        }

        $hhValid = $householdNo !== '' && preg_match('/^Z\d/i', $householdNo) === 1;
        if ($hhValid) {
            $z = $this->zoneFromHousehold($householdNo);
            if ($z) {
                $this->zone = $z;
            } elseif ($householdNo !== $this->currentHH) {
                $this->notices[] = "Row {$this->rowNo}: '{$householdNo}' — hindi Z1–Z7, ginamit ang huling zone ({$this->zone}).";
            }
        }

        // Bagong family kapag: Head (Relationship 1) o bagong Household No.
        // Kung Head siya pero walang bagong Household No., sub-household siya ng kasalukuyang household
        // (Z2-xxxx-B, -C, ...) — nasa loob pa rin ng iisang household number.
        if ($isHead || ($hhValid && $householdNo !== $this->currentHH)) {
            $this->startFamily($householdNo, $lastName, $firstName, $isHead);
        }

        if (!$this->currentFamily) {
            $this->skipped++;
            $this->errors[] = "Row {$this->rowNo}: {$firstName} {$lastName} — walang Head/Household bago ang row na ito.";
            return;
        }

        if ($visit !== null) {
            $this->currentVisit = $visit;
        }

        $this->applyFacilities($water, $toilet);

        if ($relationRaw === '') {
            $this->notices[] = "Row {$this->rowNo}: {$firstName} {$lastName} walang Relationship — ginawang 'Member'.";
        }
        if ($dob === null) {
            $this->notices[] = "Row {$this->rowNo}: {$firstName} {$lastName} walang (o maling) DOB — na-import na walang birthday.";
        }

        $medical = $this->isForm2
            ? $this->decodeMedicalForm2($cell($c['med']))
            : $cell($c['med']);
        $medicalOther = $cell($c['med2']);
        if ($medicalOther !== '') {
            $medical = trim($medical . ($medical !== '' ? ', ' : '') . $medicalOther);
        }

        $civilCode = $this->civilCode($cell($c['civil']));

        FamilyMember::updateOrCreate(
            [
                'family_profile_id' => $this->currentFamily->id,
                'first_name'        => $firstName,
                'last_name'         => $lastName,
                'date_of_birth'     => $dob,
            ],
            [
                'middle_name'         => $middleName ?: null,
                'relation_to_head'    => $this->cleanRelation($relationRaw, $relationOth),
                'age'                 => $this->parseAge($row[$c['age']] ?? null, $dob),
                'sex'                 => $this->parseSex($cell($c['sex'])),
                'civil_status'        => $civilCode,
                'education'           => $this->decodeEducation($cell($c['edu'])),
                'religion'            => $this->decodeReligion($cell($c['relig'])),
                'ethnicity'           => $cell($c['ethn']) ?: null,
                'is_4ps'              => strtolower($cell($c['4ps'])) === 'yes' ? 1 : 0,
                'date_of_visit'       => $this->currentVisit,
                'philhealth_member'   => $this->decodeMembership($cell($c['member'])),
                'classification'      => $this->decodeClassification($cell($c['class'])),
                'lmp_date'            => $c['lmp'] !== null && $this->looksLikeDate($row[$c['lmp']] ?? null) ? $this->parseDate($row[$c['lmp']]) : null,
                'using_fp'            => $this->decodeYesNo($cell($c['usefp'])),
                'fp_method'           => $this->decodeFpMethod($cell($c['fpm'])),
                'fp_status'           => $this->decodeFpStatus($cell($c['fps'])),
                'philhealth_category' => $this->decodePhilhealthCategory($cell($c['phcat'])),
                'philhealth_id'       => $this->decodePhilhealthId($cell($c['phid'])),
                'medical_history'     => $medical ?: null,
                'water_source'        => $this->currentWater,
                'toilet_facility'     => $this->currentToilet,
            ]
        );

        $this->syncRegistry([
            'first_name'       => $firstName,
            'last_name'        => $lastName,
            'middle_name'      => $middleName ?: null,
            'date_of_birth'    => $dob,
            'gender'           => $this->parseGender($cell($c['sex'])),
            'civil_status'     => $this->civilFull($civilCode),
            'zone_purok'       => $this->zone,
            'complete_address' => self::BARANGAY,
            'age'              => $this->parseAge($row[$c['age']] ?? null, $dob),
            'is_4ps'           => strtolower($cell($c['4ps'])) === 'yes' ? 1 : 0,
            'religion'         => $this->decodeReligion($cell($c['relig'])),
            'ethnicity'        => $cell($c['ethn']) ?: null,
            'education'        => $this->decodeEducation($cell($c['edu'])),
        ]);

        $this->imported++;
    }

    private function detectHeader($row): void
    {
        foreach ($row as $c => $cell) {
            $text = is_scalar($cell) ? strtolower(trim(rtrim(trim((string) $cell), '*'))) : '';
            if ($text === 'last name') {
                $this->nameCol     = (int) $c;
                $this->isForm2     = ((int) $c === 3);
                $this->headerFound = true;
                return;
            }
        }
    }

    private function findDobCol($row): int
    {
        $L = $this->nameCol;
        for ($c = $L + 3; $c <= $L + 6; $c++) {
            if ($this->looksLikeDate($row[$c] ?? null)) {
                return $c;
            }
        }
        return $this->isForm2 ? $L + 4 : $L + 5;
    }

    private function columns(int $d): array
    {
        if ($this->isForm2) {
            return [
                'rel' => $d - 1, 'other' => null,
                'age' => $d + 1, 'sex' => $d + 2, 'civil' => $d + 3,
                'edu' => $d + 4, 'relig' => $d + 5, 'ethn' => $d + 6,
                '4ps' => $d + 7, 'phid' => $d + 8, 'phcat' => $d + 10,
                'med' => $d + 11, 'med2' => null,
                'member' => $d + 9, 'class' => $d + 12, 'lmp' => $d + 14,
                'usefp' => $d + 15, 'fpm' => $d + 16, 'fps' => $d + 17,
                'water' => $d + 18, 'toilet' => $d + 19,
            ];
        }

        return [
            'rel' => $d - 2, 'other' => $d - 1,
            'age' => $d + 1, 'sex' => $d + 2, 'civil' => $d + 3,
            'edu' => $d + 4, 'relig' => $d + 5, 'ethn' => $d + 6,
            '4ps' => $d + 7, 'phcat' => $d + 9, 'phid' => $d + 10,
            'med' => $d + 11, 'med2' => $d + 12,
            'member' => null, 'class' => null, 'lmp' => null,
            'usefp' => null, 'fpm' => null, 'fps' => null,
            'water' => $d + 19, 'toilet' => $d + 20,
        ];
    }

    private function zoneFromHousehold(string $hh): ?string
    {
        if (preg_match('/^Z\s*-?\s*([1-7])(?!\d)/i', $hh, $m)) {
            return 'Zone ' . $m[1];
        }
        return null;
    }

    /**
     * Kapag $forceZone = true: palitan ang "Z#" prefix ng Household No. para tumugma sa napiling zone.
     * Para hindi mag-overwrite ang Zone 3 file na puro "Z1-" ng totoong Zone 1 households.
     */
    private function lockHouseholdZone(string $householdNo): string
    {
        if (!$this->forceZone || $householdNo === '') {
            return $householdNo;
        }

        $zoneNum = preg_replace('/\D/', '', $this->zone);
        if ($zoneNum === '' || !preg_match('/^Z\s*-?\s*\d/i', $householdNo)) {
            return $householdNo;
        }

        $fixed = preg_replace('/^Z\s*-?\s*\d/i', 'Z' . $zoneNum, $householdNo, 1);

        if ($fixed !== $householdNo && !$this->zoneRewriteNoticed) {
            $this->zoneRewriteNoticed = true;
            $this->notices[] = "Row {$this->rowNo}: Pinilit ang {$this->zone} — pinalitan ang zone prefix ng mga Household No. (hal. '{$householdNo}' → '{$fixed}').";
        }

        return $fixed;
    }

    private function startFamily(string $householdNo, string $last, string $first, bool $isHead): void
    {
        $zoneNum  = preg_replace('/\D/', '', $this->zone) ?: '0';
        $parentId = null;
        $hasHH    = $householdNo !== '' && preg_match('/^Z\d/i', $householdNo) === 1;

        if ($hasHH) {
            $this->usedCodes[$householdNo] = ($this->usedCodes[$householdNo] ?? 0) + 1;
            $n    = $this->usedCodes[$householdNo];
            $code = $householdNo;
            if ($n > 1) {
                $code = $householdNo . '-' . chr(64 + $n);
                $this->notices[] = "Row {$this->rowNo}: Household No. '{$householdNo}' ginamit ulit ni {$first} {$last} — ginawang '{$code}'.";
            }
            $this->currentHH = $householdNo;
        } elseif ($isHead && $this->currentHH !== null && $this->mainFamilyId !== null) {
            $main = $this->currentHH;
            $n    = ($this->usedCodes[$main] ?? 1) + 1;
            $this->usedCodes[$main] = $n;
            $code     = $main . '-' . chr(64 + $n);
            $parentId = $this->mainFamilyId;
            $this->notices[] = "Row {$this->rowNo}: Head na si {$first} {$last} ay walang Household No. — ginawang sub-household '{$code}' ng '{$main}'.";
        } else {
            $code = "Z{$zoneNum}-NOHH-R" . $this->rowNo;
            $this->notices[] = "Row {$this->rowNo}: Head na si {$first} {$last} ay walang Household No. — ginamit ang '{$code}'.";
            $this->currentHH = null;
        }

        if (!$isHead) {
            $this->notices[] = "Row {$this->rowNo}: Walang Head sa household na '{$code}' — ginamit si {$first} {$last} bilang head.";
        }

        $this->currentFamily = FamilyProfile::updateOrCreate(
            ['family_code' => $code],
            [
                'head_last_name'   => $last,
                'head_first_name'  => $first,
                'zone_purok'       => $this->zone,
                'complete_address' => self::BARANGAY,
                'parent_family_id' => $parentId,
            ]
        );

        if ($hasHH) {
            $this->mainFamilyId = $this->currentFamily->id;
        } elseif ($parentId === null) {
            $this->mainFamilyId = null;
        }

        $this->currentWater  = null;
        $this->currentToilet = null;
        $this->currentVisit  = null;
        $this->families++;
    }

    private function syncRegistry(array $d): void
    {
        if (empty($d['first_name']) || empty($d['last_name'])) {
            return;
        }

        $resident = Resident::firstOrCreate(
            [
                'first_name'    => $d['first_name'],
                'last_name'     => $d['last_name'],
                'date_of_birth' => $d['date_of_birth'],
            ],
            [
                'middle_name'      => $d['middle_name'],
                'gender'           => $d['gender'],
                'civil_status'     => $d['civil_status'],
                'zone_purok'       => $d['zone_purok'],
                'complete_address' => $d['complete_address'],
                'password'         => \Illuminate\Support\Facades\Hash::make('resident123'),
                'role'             => 'resident',
                'status'           => 'approved',
                'is_senior'        => ($d['age'] !== null && $d['age'] >= 60) ? 1 : 0,
                'is_4ps'           => $d['is_4ps'] ?? 0,
                'is_pwd'           => 0,
                'is_solo_parent'   => 0,
                'is_voter'         => 0,
                'religion'         => $d['religion'] ?? null,
                'ethnicity'        => $d['ethnicity'] ?? null,
                'education'        => $d['education'] ?? null,
                'family_profile_id'=> $this->currentFamily->id ?? null,
            ]
        );

        if ($resident->wasRecentlyCreated) {
            $this->registryCount++;
        }
    }

    private function applyFacilities(?string $water, ?string $toilet): void
    {
        if (!$this->currentFamily) return;

        if ($water && $this->currentWater === null) {
            $this->currentWater = $water;
            FamilyMember::where('family_profile_id', $this->currentFamily->id)
                ->whereNull('water_source')->update(['water_source' => $water]);
        }
        if ($toilet && $this->currentToilet === null) {
            $this->currentToilet = $toilet;
            FamilyMember::where('family_profile_id', $this->currentFamily->id)
                ->whereNull('toilet_facility')->update(['toilet_facility' => $toilet]);
        }
    }

    private function normalizeRelation(string $raw): string
    {
        if ($this->isForm2 && preg_match('/^([1-5])$/', $raw, $m)) {
            return [1 => '1 - Head', 2 => '2 - Spouse', 3 => '3 - Son', 4 => '4 - Daughter', 5 => '5 - Others'][(int) $m[1]];
        }
        return $raw;
    }

    /**
     * Sa Excel, "3" ay laging "Son" at "4" ay laging "Daughter" kahit mali ang kasarian.
     * Ang Sex column ang mas mapagkakatiwalaan: babae = Daughter, lalaki = Son.
     */
    private function fixChildRelation(string $rel, ?string $sex): string
    {
        if ($sex === 'F' && preg_match('/^3\s*-/', $rel)) {
            return '4 - Daughter';
        }
        if ($sex === 'M' && preg_match('/^4\s*-/', $rel)) {
            return '3 - Son';
        }
        return $rel;
    }

    private function decodeEducation(string $v): ?string
    {
        if ($v === '') return null;
        if (!$this->isForm2 || mb_strlen($v) > 3) return $v;

        return [
            'N'   => 'N/A',
            'PS'  => 'Pre-school',
            'ES'  => 'Elementary Student',
            'EU'  => 'Elementary Under Graduate',
            'EG'  => 'Elementary Graduate',
            'HS'  => 'High School Student',
            'HU'  => 'High School Undergraduate',
            'HG'  => 'High School Graduate',
            'SH'  => 'Senior Highschool',
            'ALS' => 'ALS',
            'V'   => 'Vocational Course',
            'CS'  => 'College Student',
            'CU'  => 'College Under Graduate',
            'CG'  => 'College Graduate',
            'PG'  => 'Post Graduate/Masteral/Doctorate',
        ][strtoupper($v)] ?? null;
    }

    private function decodeReligion(string $v): ?string
    {
        if ($v === '') return null;
        if (!$this->isForm2) return $v;

        return [
            'RC'     => 'Roman Catholic',
            'INC'    => 'Iglesia ni Cristo',
            'JEHOVA' => 'Jehovahs Witness',
        ][strtoupper($v)] ?? $v;
    }

    private function decodePhilhealthCategory(string $v): ?string
    {
        if ($v === '') return null;
        if (!$this->isForm2) return $v;

        return [
            'DC' => 'Direct Contributors',
            'IC' => 'Indirect Contributors',
            'U'  => 'Unknown',
        ][strtoupper($v)] ?? null;
    }

    private function decodePhilhealthId(string $v): ?string
    {
        if ($v === '') return null;
        if (!$this->isForm2) return $v;
        return preg_match('/^[\d\- ]{6,}$/', $v) ? $v : null;
    }

    private function decodeMedicalForm2(string $v): string
    {
        if ($v === '') return '';
        $map = ['HPN' => 'Hypertension', 'DM' => 'Diabetes', 'TB' => 'Tuberculosis', 'O' => 'Others'];
        $out = [];
        foreach (preg_split('/[\s,\/]+/', strtoupper($v)) as $code) {
            if (isset($map[$code])) {
                $out[] = $map[$code];
            }
        }
        return implode(', ', $out);
    }

    private function decodeWater(string $v): string
    {
        if ($v === '' || !$this->isForm2) return $v;

        $u = strtoupper(str_replace(' ', '', $v));
        if ($u === 'O') return 'Unknown';
        if (preg_match('/^(?:LV)?([IL1]{1,3})$/', $u, $m)) {
            return 'Level ' . strlen($m[1]);
        }
        return mb_strlen($v) > 4 ? $v : '';
    }

    private function decodeToilet(string $v): string
    {
        if ($v === '' || !$this->isForm2) return $v;

        $map = [
            'P'   => 'Pour/flush toilet connected to septic tank',
            'PF'  => 'Pour/flush toilet connected to connected to septic tank AND to sewerage system',
            'VIP' => 'Ventilated improved pit latrine (VIP) or Composting toilet',
            'WS'  => 'Water-sealed connected to open drain',
            'OH'  => 'Overhung latrine',
            'OP'  => 'Open-pit Latrine',
            'WO'  => 'Without toilet',
        ];
        $u = strtoupper(str_replace(' ', '', $v));
        return $map[$u] ?? (mb_strlen($v) > 4 ? $v : '');
    }

    private function parseVisit($v): ?string
    {
        if ($v instanceof \DateTimeInterface || (is_numeric($v) && $this->looksLikeDate($v))) {
            return $this->parseDate($v);
        }
        $s = $this->clean($v);
        return ($s !== '' && mb_strlen($s) <= 20) ? $s : null;
    }

    private function decodeMembership(string $v): ?string
    {
        $u = strtoupper(trim($v));
        if (in_array($u, ['M', 'MEMBER'], true)) return 'M';
        if (in_array($u, ['D', 'DEPENDENT'], true)) return 'D';
        return null;
    }

    private function decodeClassification(string $v): ?string
    {
        $u = strtoupper(trim($v));
        return preg_match('/^[A-Z]{1,4}([\s,\/][A-Z]{1,4})*$/', $u) && mb_strlen($u) <= 20 ? $u : null;
    }

    private function decodeYesNo(string $v): ?bool
    {
        $u = strtolower(trim($v));
        if (in_array($u, ['yes', 'y'], true)) return true;
        if (in_array($u, ['no', 'n'], true))  return false;
        return null;
    }

    private function decodeFpMethod(string $v): ?string
    {
        $u = strtoupper(trim($v));
        if ($u === '' || in_array($u, ['N', 'NO', 'NONE'], true)) return null;
        return preg_match('/^[A-Z][A-Z \/]{1,19}$/', $u) ? $u : null;
    }

    private function decodeFpStatus(string $v): ?string
    {
        $u = strtoupper(trim($v));
        return preg_match('/^[A-Z]{1,3}$/', $u) && $u !== 'N' ? $u : null;
    }

    private function clean($value): string
    {
        if ($value === null || !is_scalar($value)) return '';
        $v = trim(preg_replace('/\s+/u', ' ', (string) $value));
        if ($v === '' || $v === '0' || $v === '-' || $v === '?' || $v[0] === '#') {
            return '';
        }
        return $v;
    }

    private function nullable(?string $v): ?string
    {
        return ($v === null || $v === '') ? null : $v;
    }

    private function cleanRelation(string $raw, string $other): string
    {
        if ($raw === '') return 'Member';
        $rel = trim(preg_replace('/^\d+\s*-\s*/', '', $raw));
        if (stripos($rel, 'other') === 0 && $other !== '') {
            return $other;
        }
        return $rel ?: 'Member';
    }

    private function parseSex(string $v): ?string
    {
        $c = strtoupper(substr($v, 0, 1));
        return in_array($c, ['M', 'F']) ? $c : null;
    }

    private function parseGender(string $v): ?string
    {
        $c = strtoupper(substr($v, 0, 1));
        if ($c === 'M') return 'Male';
        if ($c === 'F') return 'Female';
        return null;
    }

    private function civilCode(string $v): ?string
    {
        if ($v === '' || !preg_match('/^\s*([A-Za-z]+)/', $v, $m)) return null;

        $c = strtoupper($m[1]);
        $c = [
            'MARRIED' => 'M', 'SINGLE' => 'S', 'WIDOW' => 'W', 'WIDOWED' => 'W',
            'SEPARATED' => 'SP', 'COHABITATION' => 'C', 'LIVE' => 'L',
        ][$c] ?? $c;

        return in_array($c, ['M', 'S', 'W', 'SP', 'L', 'C'], true) ? $c : null;
    }

    private function civilFull(?string $code): ?string
    {
        if ($code === null) return null;
        return [
            'M' => 'Married', 'S' => 'Single', 'W' => 'Widowed',
            'SP' => 'Separated', 'L' => 'Cohabitation', 'C' => 'Cohabitation',
        ][$code] ?? null;
    }

    private function looksLikeDate($value): bool
    {
        if ($value instanceof \DateTimeInterface) return true;
        if (is_numeric($value)) return $value >= 10000 && $value <= 60000;
        return is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}/', trim($value)) === 1;
    }

    private function parseDate($value): ?string
    {
        if ($value === null || $value === '') return null;
        try {
            if ($value instanceof \DateTimeInterface) {
                $d = Carbon::instance($value);
            } elseif (is_numeric($value)) {
                if ((float) $value < 1000) return null;
                $d = Carbon::instance(ExcelDate::excelToDateTimeObject($value));
            } else {
                $v = trim((string) $value);
                if ($v === '' || $v[0] === '#') return null;
                $d = Carbon::parse($v);
            }
        } catch (\Throwable $e) {
            return null;
        }
        if ($d->year < 1900 || $d->isFuture()) return null;
        return $d->format('Y-m-d');
    }

    /**
     * Kapag may DOB, doon laging kukunin ang age para pare-pareho ang basehan
     * (ang age sa Excel ay snapshot lang noong araw ng visit, kaya luma na).
     * Kapag walang DOB, gagamitin ang nasa Excel.
     */
    private function parseAge($ageRaw, ?string $dob): ?int
    {
        if ($dob) {
            return Carbon::parse($dob)->age;
        }
        if (is_string($ageRaw) && preg_match('/^\s*(\d+)\s*Years?/i', $ageRaw, $m) && (int) $m[1] < 120) {
            return (int) $m[1];
        }
        if (is_numeric($ageRaw) && $ageRaw >= 0 && $ageRaw < 120) {
            return (int) $ageRaw;
        }
        return null;
    }
}