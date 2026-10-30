<?php

namespace Database\Seeders;

use App\Models\KategoriPerilaku;
use App\Models\Lokasi;
use App\Models\ObservasiBbs;
use App\Models\User;
use App\Services\C45Service;
use App\Support\BbsChecklistMapper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class ImportCepSmiBbsSeeder extends Seeder
{
    public function run(): void
    {
        $path = 'C:/Users/Lenovo/Downloads/Data BBS Kru PT CEP-SMI.xlsx';
        if (!is_file($path)) {
            throw new RuntimeException("File Excel tidak ditemukan: {$path}");
        }

        $reader = new XlsxWorkbookReader($path);
        try {
            $crewRows = $reader->rows('Master Kru PT CEP SMI');
            $observationRows = $reader->rows('Data Latih BBS PT CEP SMI');
        } finally {
            $reader->close();
        }

        if ($crewRows === [] || $observationRows === []) {
            throw new RuntimeException('Sheet master kru atau data observasi tidak berisi data.');
        }

        $crewByBatch = [];
        $crewBatchByName = [];
        foreach ($crewRows as $row) {
            $batch = trim($row['Nomor Batch'] ?? '');
            $name = trim($row['Nama Lengkap'] ?? '');
            if ($batch === '' || $name === '') {
                throw new RuntimeException('Ada kru tanpa nomor batch atau nama lengkap.');
            }
            if (isset($crewByBatch[$batch])) {
                throw new RuntimeException("Nomor batch kru duplikat: {$batch}");
            }

            $nameKey = $this->normalizedCrewName($name);
            $existingBatches = $crewBatchByName[$nameKey] ?? [];
            if ($nameKey === 'ririnta'
                    && in_array('CEP-SMI-KRU-0009', $existingBatches, true)
                    && $batch === 'CEP-SMI-KRU-0029') {
                // Batch 0029 was identified by the user as a typo for Ririnta's batch 0009.
                continue;
            }

            $crewByBatch[$batch] = $row;
            $crewBatchByName[$nameKey][] = $batch;
        }

        $adminNames = [];
        foreach ($crewByBatch as $batch => $crew) {
            if ($this->isAdmin(trim($crew['Nama Lengkap']))) {
                $adminNames[] = $batch.' — '.trim($crew['Nama Lengkap']).' ('.trim($crew['Jabatan'] ?? '').')';
            }
        }
        $adminCount = count($adminNames);

        $crewBatchesByName = [];
        foreach ($crewByBatch as $batch => $crew) {
            $crewBatchesByName[$this->normalizedCrewName($crew['Nama Lengkap'])][] = $batch;
        }

        $observationsPerCrew = [];
        $observationsPerName = [];
        $mappedObservationRows = [];
        foreach ($observationRows as $index => $row) {
            $name = trim($row['Nama Kru'] ?? '');
            $nameKey = $this->normalizedCrewName($name);
            $matchingBatches = $crewBatchesByName[$nameKey] ?? [];
            if ($matchingBatches === []) {
                throw new RuntimeException('Nama kru observasi tidak ditemukan di Master Kru pada baris '.($index + 2).": {$name}");
            }

            $sourceBatch = trim($row['Nomor Batch'] ?? '');
            if (isset($crewByBatch[$sourceBatch])
                && $this->normalizedCrewName($crewByBatch[$sourceBatch]['Nama Lengkap']) === $nameKey) {
                $batch = $sourceBatch;
            } elseif (count($matchingBatches) === 1) {
                $batch = $matchingBatches[0];
            } else {
                $occurrence = $observationsPerName[$nameKey] ?? 0;
                $batchIndex = intdiv($occurrence, 2);
                if (!isset($matchingBatches[$batchIndex])) {
                    throw new RuntimeException("Observasi untuk kru bernama {$name} tidak bisa dibagi dua per akun.");
                }
                $batch = $matchingBatches[$batchIndex];
            }

            if (trim($row['Deskripsi Temuan Perilaku'] ?? '') === '') {
                throw new RuntimeException('Deskripsi temuan kosong pada baris Excel '.($index + 2).'.');
            }
            $this->behaviorType($row['Status Perilaku'] ?? '', $index + 2);
            $dateValue = trim($row['Tanggal Observasi'] ?? '');
            if ($dateValue === '') {
                throw new RuntimeException('Tanggal observasi kosong pada baris Excel '.($index + 2).'.');
            }
            $this->observationDate($dateValue, $index + 2);
            $observationsPerCrew[$batch] = ($observationsPerCrew[$batch] ?? 0) + 1;
            $observationsPerName[$nameKey] = ($observationsPerName[$nameKey] ?? 0) + 1;
            $row['_mapped_crew_batch'] = $batch;
            $row['_source_ref'] = 'CEP-SMI-EXCEL-'.($index + 2);
            $mappedObservationRows[] = $row;
        }

        foreach ($crewByBatch as $batch => $crew) {
            $expectedObservations = $batch === 'CEP-SMI-KRU-0009' ? 4 : 2;
            if (($observationsPerCrew[$batch] ?? 0) !== $expectedObservations) {
                throw new RuntimeException("Kru {$crew['Nama Lengkap']} harus memiliki {$expectedObservations} observasi di Excel; ditemukan ".($observationsPerCrew[$batch] ?? 0).".");
            }
        }

        if ($adminCount !== 3) {
            throw new RuntimeException("Master Kru harus memuat tepat 3 admin yang dikenali; ditemukan {$adminCount}: ".implode(', ', $adminNames));
        }
        $observationRows = $mappedObservationRows;

        $generatedCredentials = [];
        $createdObservations = 0;
        $updatedObservations = 0;

        DB::transaction(function () use ($crewByBatch, $observationRows, &$generatedCredentials, &$createdObservations, &$updatedObservations): void {
            $usedEmails = User::query()->pluck('email')->map(fn ($email) => Str::lower($email))->all();
            $usersByBatch = [];

            foreach ($crewByBatch as $batch => $crew) {
                $name = trim($crew['Nama Lengkap']);
                $firstName = preg_split('/\s+/u', $name)[0] ?? $name;
                $existingUser = User::query()
                    ->where('no_batch', $batch)
                    ->where('name', $name)
                    ->first();

                $email = $existingUser?->email ?? $this->uniqueEmail($firstName, $usedEmails);
                $usedEmails[] = Str::lower($email);
                $role = $this->isAdmin($name) ? 'admin' : 'karyawan';
                $password = $firstName.'123';
                $unit = trim($crew['Golongan Kru'] ?? '');

                $attributes = [
                    'no_batch' => $batch,
                    'name' => $name,
                    'email' => $email,
                    'password' => $existingUser?->password ?? Hash::make($password),
                    'role' => $role,
                    'jabatan' => trim($crew['Jabatan'] ?? '') ?: null,
                    'unit_departemen' => $unit !== '' ? 'PT CEP-SMI - '.$unit : 'PT CEP-SMI',
                    'status' => 'active',
                ];
                if ($existingUser) {
                    $existingUser->update($attributes);
                    $user = $existingUser;
                } else {
                    $user = User::query()->create($attributes);
                }

                $usersByBatch[$batch] = $user;
                $generatedCredentials[] = [
                    'Nomor Batch' => $batch,
                    'Nama Lengkap' => $name,
                    'Role' => $role,
                    'Email' => $email,
                    'Password' => $password,
                ];
            }

            $classifier = app(C45Service::class);
            foreach ($observationRows as $row) {
                $batch = $row['_mapped_crew_batch'];
                $user = $usersByBatch[$batch];
                $sourceRef = $row['_source_ref'];
                $categoryName = $this->categoryName(trim($row['Kategori Perilaku'] ?? ''));
                $category = KategoriPerilaku::query()->firstOrCreate(
                    ['nama_kategori' => $categoryName],
                    ['deskripsi' => 'Kategori diimpor dari Data BBS Kru PT CEP-SMI.']
                );
                $locationName = trim($row['Lokasi Pengamatan'] ?? '') ?: 'Lokasi tidak dicantumkan';
                $location = Lokasi::query()->firstOrCreate(
                    ['nama_lokasi' => $locationName],
                    ['area' => 'PT CEP-SMI']
                );
                $behavior = $this->behaviorType($row['Status Perilaku']);
                $date = $this->observationDate($row['Tanggal Observasi']);
                $found = trim($row['Deskripsi Temuan Perilaku']);
                $attributes = [
                    'lokasi_id' => $location->id,
                    'unit_departemen' => $user->unit_departemen ?? 'PT CEP-SMI',
                    'jenis_perilaku' => $behavior,
                    'kategori_id' => $category->id,
                    'item_states' => [BbsChecklistMapper::code($categoryName, $found) => $behavior],
                    'source_ref' => $sourceRef,
                    'ketika' => 'Observasi rutin BBS kru PT CEP-SMI.',
                    'ditemukan' => $found,
                    'sebab' => null,
                    'saran' => trim($row['Tindakan Perbaikan / Apresiasi'] ?? '') ?: null,
                    'tingkat_risiko' => $classifier->predictRisk($behavior, $category->id, $location->id),
                    'status_tindak_lanjut' => 'open',
                ];

                $observation = ObservasiBbs::query()->where('source_ref', $sourceRef)->first();
                if (!$observation) {
                    $observation = ObservasiBbs::query()
                        ->whereNull('source_ref')
                        ->where('user_id', $user->id)
                        ->where('tanggal', $date)
                        ->where('ditemukan', $found)
                        ->first();
                }

                if ($observation) {
                    $observation->update($attributes);
                    $updatedObservations++;
                } else {
                    ObservasiBbs::query()->create($attributes + [
                        'user_id' => $user->id,
                        'tanggal' => $date,
                    ]);
                    $createdObservations++;
                }
            }
        });

        $credentialsPath = storage_path('app/private/akun_kru_cep_smi.csv');
        if (!is_dir(dirname($credentialsPath))) {
            mkdir(dirname($credentialsPath), 0700, true);
        }

        $handle = fopen($credentialsPath, 'wb');
        if ($handle === false) {
            throw new RuntimeException('Tidak dapat membuat file kredensial kru.');
        }
        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, ['Nomor Batch', 'Nama Lengkap', 'Role', 'Email', 'Password']);
        foreach ($generatedCredentials as $credential) {
            fputcsv($handle, array_values($credential));
        }
        fclose($handle);

        $this->command?->info('Akun kru: '.count($generatedCredentials));
        $this->command?->info("Observasi baru: {$createdObservations}; observasi cocok yang diperbarui: {$updatedObservations}");
        $this->command?->info("Daftar login tersimpan di: {$credentialsPath}");
    }

    private function uniqueEmail(string $firstName, array $usedEmails): string
    {
        $base = Str::slug($firstName, '');
        if ($base === '') {
            $base = 'kru';
        }

        $candidate = $base.'@gmail.com';
        $suffix = 2;
        while (in_array(Str::lower($candidate), $usedEmails, true)) {
            $candidate = $base.$suffix.'@gmail.com';
            $suffix++;
        }

        return $candidate;
    }

    private function isAdmin(string $name): bool
    {
        $normalized = Str::lower(trim(preg_replace('/\s+/u', ' ', $name)));
        $firstName = preg_split('/\s+/u', $normalized)[0] ?? '';

        return in_array($firstName, ['imran', 'ririnta'], true)
            || $normalized === 'yulia wibowo'
            || str_starts_with($normalized, 'yulia wibowo ');
    }

    private function normalizedCrewName(string $name): string
    {
        return Str::lower(trim(preg_replace('/\s+/u', ' ', $name)));
    }

    private function behaviorType(string $value, ?int $rowNumber = null): string
    {
        $normalized = Str::lower(trim($value));
        if (str_contains($normalized, 'risk') || str_contains($normalized, 'tidak aman') || str_contains($normalized, 'unsafe')) {
            return 'at_risk';
        }
        if (str_contains($normalized, 'safe') || str_contains($normalized, 'aman')) {
            return 'safe';
        }

        $suffix = $rowNumber ? " pada baris Excel {$rowNumber}" : '';
        throw new RuntimeException("Status perilaku tidak dikenal{$suffix}: {$value}");
    }

    private function observationDate(string $value, ?int $rowNumber = null): string
    {
        if (is_numeric($value)) {
            return (new \DateTimeImmutable('1899-12-30'))->modify('+'.(int) $value.' days')->format('Y-m-d');
        }

        try {
            return (new \DateTimeImmutable($value))->format('Y-m-d');
        } catch (\Throwable $exception) {
            $suffix = $rowNumber ? " pada baris Excel {$rowNumber}" : '';
            throw new RuntimeException("Tanggal observasi tidak valid{$suffix}: {$value}", previous: $exception);
        }
    }

    private function categoryName(string $source): string
    {
        $name = Str::lower($source);

        return match (true) {
            str_contains($name, 'isolasi energi'), str_contains($name, 'loto') => 'Isolasi Energi (LOTO / Lockout Tagout)',
            str_contains($name, 'listrik'), str_contains($name, 'mekanikal') => 'Peralatan Listrik & Mekanikal',
            str_contains($name, 'apd'), str_contains($name, 'alat pelindung diri') => 'Alat Pelindung Diri (APD)',
            str_contains($name, 'perkakas'), str_contains($name, 'peralatan') => 'Alat & Perkakas Kerja',
            str_contains($name, 'izin kerja'), str_contains($name, 'permit'), str_contains($name, 'prosedur') => 'Prosedur & Izin Kerja (JSA/PTW)',
            str_contains($name, 'posisi'), str_contains($name, 'ergonomi') => 'Posisi & Ergonomi Badan',
            str_contains($name, 'housekeeping'), str_contains($name, 'kebersihan') => 'Kerapihan & Kebersihan (Housekeeping)',
            str_contains($name, 'mengemudi'), str_contains($name, 'transportasi') => 'Mengemudi & Transportasi',
            str_contains($name, 'b3'), str_contains($name, 'berbahaya') => 'Bahan Berbahaya & Beracun (B3)',
            str_contains($name, 'darurat'), str_contains($name, 'kesiapsiagaan') => 'Tanggap Darurat & Kesiapsiagaan',
            $source !== '' => $source,
            default => 'Lain-lain',
        };
    }
}
