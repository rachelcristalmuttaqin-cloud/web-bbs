<?php

namespace Database\Seeders;

use App\Models\ObservasiBbs;
use App\Models\User;
use App\Support\BbsChecklistMapper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ApplyImportedBbsChecklistSeeder extends Seeder
{
    public function run(): void
    {
        $credentialsPath = storage_path('app/private/akun_kru_cep_smi.csv');
        if (!is_file($credentialsPath)) {
            throw new RuntimeException('Daftar akun impor tidak ditemukan; belum ada kru yang dapat diperbarui.');
        }

        $handle = fopen($credentialsPath, 'rb');
        if ($handle === false) {
            throw new RuntimeException('Tidak dapat membaca daftar akun impor.');
        }

        fgetcsv($handle);
        $emails = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (!empty($row[3])) {
                $emails[] = $row[3];
            }
        }
        fclose($handle);

        $userIds = User::query()->whereIn('email', $emails)->pluck('id');
        if ($userIds->count() !== count(array_unique($emails))) {
            throw new RuntimeException('Tidak semua akun di daftar kredensial ditemukan di database.');
        }

        $updated = 0;
        DB::transaction(function () use ($userIds, &$updated): void {
            ObservasiBbs::query()
                ->with('kategori')
                ->whereIn('user_id', $userIds)
                ->orderBy('id')
                ->chunkById(100, function ($observations) use (&$updated): void {
                    foreach ($observations as $observation) {
                        $code = BbsChecklistMapper::code(
                            $observation->kategori->nama_kategori ?? '',
                            $observation->ditemukan ?? ''
                        );
                        $observation->item_states = [$code => $observation->jenis_perilaku];
                        $observation->save();
                        $updated++;
                    }
                });
        });

        $this->command?->info("Ceklis checklist diperbarui pada {$updated} observasi.");
    }
}
