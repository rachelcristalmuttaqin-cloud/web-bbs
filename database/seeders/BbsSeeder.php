<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\KategoriPerilaku;
use App\Models\Lokasi;
use App\Models\TrainingC45;
use App\Models\ObservasiBbs;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BbsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun demo karyawan. Akun Admin tidak dibuat oleh seeder umum.
        $karyawanActive = User::firstOrCreate(
            ['email' => 'karyawan@example.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'karyawan',
                'no_hp' => '081298765432',
                'jabatan' => 'Teknisi Lapangan',
                'unit_departemen' => 'Operasional Plant A',
                'status' => 'active',
            ]
        );

        $karyawanPending = User::firstOrCreate(
            ['email' => 'pending@example.com'],
            [
                'name' => 'Rizky Pratama',
                'password' => Hash::make('password'),
                'role' => 'karyawan',
                'no_hp' => '085678901234',
                'jabatan' => 'Operator Magang',
                'unit_departemen' => 'Logistik & Warehousing',
                'status' => 'pending',
            ]
        );

        // 2. Master Kategori Perilaku BBS
        $kategoriList = [
            ['nama_kategori' => 'Alat Pelindung Diri (APD)', 'deskripsi' => 'Kepatuhan penggunaan helm, kacamata, sarung tangan, sepatu safety, dan pelindung telinga.'],
            ['nama_kategori' => 'Posisi & Ergonomi Badan', 'deskripsi' => 'Postur kerja, posisi mengangkat benda berat, titik jepit (pinch point), dan posisi terhindar bahaya.'],
            ['nama_kategori' => 'Alat & Perkakas Kerja', 'deskripsi' => 'Kelayakan alat kerja tangan, perkakas listrik, inspeksi tag, dan penggunaan alat tepat guna.'],
            ['nama_kategori' => 'Prosedur & Izin Kerja (JSA/PTW)', 'deskripsi' => 'Penerapan izin kerja khusus (Hot Work, Confined Space), ketersediaan JSA, dan SOP.'],
            ['nama_kategori' => 'Mengemudi & Transportasi', 'deskripsi' => 'Penggunaan sabuk pengaman, batas kecepatan di area plant, inspeksi kendaraan harian.'],
            ['nama_kategori' => 'Kerapihan & Kebersihan (Housekeeping)', 'deskripsi' => 'Penataan material, area jalan bebas hambatan, penanganan ceceran minyak/kimia.'],
            ['nama_kategori' => 'Peralatan Listrik & Mekanikal', 'deskripsi' => 'Pengamanan kabel terkelupas, grounding, penutup panel listrik, dan guard mesin berputar.'],
            ['nama_kategori' => 'Isolasi Energi (LOTO / Lockout Tagout)', 'deskripsi' => 'Pemasangan gembok dan tag isolasi sumber energi saat perawatan atau perbaikan mesin.'],
            ['nama_kategori' => 'Bahan Berbahaya & Beracun (B3)', 'deskripsi' => 'Penyimpanan kimia berlabel, penyediaan MSDS/LDKB, dan penampungan ceceran (spill kit).'],
            ['nama_kategori' => 'Tanggap Darurat & Kesiapsiagaan', 'deskripsi' => 'Keterjangkauan APAR, akses jalur evakuasi, eyewash station, dan kesiapan kotak P3K.'],
        ];

        foreach ($kategoriList as $kat) {
            KategoriPerilaku::firstOrCreate(['nama_kategori' => $kat['nama_kategori']], $kat);
        }

        // 3. Master Lokasi Area Kerja
        $lokasiList = [
            ['nama_lokasi' => 'Workshop Utama (Main Plant)', 'area' => 'Zona 1'],
            ['nama_lokasi' => 'Gudang Material & Logistik', 'area' => 'Zona 2'],
            ['nama_lokasi' => 'Area Process & Tank Farm', 'area' => 'Zona 3'],
            ['nama_lokasi' => 'Laboratorium Pengujian & Quality Control', 'area' => 'Zona 4'],
            ['nama_lokasi' => 'Gedung Administrasi & Kantor Utama', 'area' => 'Zona 5'],
        ];

        foreach ($lokasiList as $lok) {
            Lokasi::firstOrCreate(['nama_lokasi' => $lok['nama_lokasi']], $lok);
        }

        $katApd = KategoriPerilaku::where('nama_kategori', 'like', '%APD%')->first();
        $katProsedur = KategoriPerilaku::where('nama_kategori', 'like', '%Prosedur%')->first();
        $katPosisi = KategoriPerilaku::where('nama_kategori', 'like', '%Posisi%')->first();
        $katHousekeeping = KategoriPerilaku::where('nama_kategori', 'like', '%Housekeeping%')->first();
        $katLoto = KategoriPerilaku::where('nama_kategori', 'like', '%Isolasi%')->first();

        $lokWorkshop = Lokasi::where('nama_lokasi', 'like', '%Workshop%')->first();
        $lokGudang = Lokasi::where('nama_lokasi', 'like', '%Gudang%')->first();
        $lokProcess = Lokasi::where('nama_lokasi', 'like', '%Process%')->first();

        // 4. Dataset Training Algoritma C4.5
        $trainingDataset = [
            // Safe / Patuh -> Rendah
            ['jenis_perilaku' => 'safe', 'kategori_id' => $katApd->id, 'lokasi_id' => $lokWorkshop->id, 'tingkat_potensi_bahaya' => 'rendah', 'kepatuhan_prosedur' => 'patuh', 'tingkat_risiko' => 'rendah'],
            ['jenis_perilaku' => 'safe', 'kategori_id' => $katHousekeeping->id, 'lokasi_id' => $lokGudang->id, 'tingkat_potensi_bahaya' => 'rendah', 'kepatuhan_prosedur' => 'patuh', 'tingkat_risiko' => 'rendah'],
            ['jenis_perilaku' => 'safe', 'kategori_id' => $katPosisi->id, 'lokasi_id' => $lokWorkshop->id, 'tingkat_potensi_bahaya' => 'sedang', 'kepatuhan_prosedur' => 'patuh', 'tingkat_risiko' => 'rendah'],
            ['jenis_perilaku' => 'safe', 'kategori_id' => $katProsedur->id, 'lokasi_id' => $lokProcess->id, 'tingkat_potensi_bahaya' => 'sedang', 'kepatuhan_prosedur' => 'patuh', 'tingkat_risiko' => 'rendah'],
            
            // At-Risk / Melanggar Prosedur Ringan -> Sedang
            ['jenis_perilaku' => 'at_risk', 'kategori_id' => $katHousekeeping->id, 'lokasi_id' => $lokGudang->id, 'tingkat_potensi_bahaya' => 'sedang', 'kepatuhan_prosedur' => 'melanggar', 'tingkat_risiko' => 'sedang'],
            ['jenis_perilaku' => 'at_risk', 'kategori_id' => $katApd->id, 'lokasi_id' => $lokWorkshop->id, 'tingkat_potensi_bahaya' => 'sedang', 'kepatuhan_prosedur' => 'melanggar', 'tingkat_risiko' => 'sedang'],
            ['jenis_perilaku' => 'at_risk', 'kategori_id' => $katPosisi->id, 'lokasi_id' => $lokProcess->id, 'tingkat_potensi_bahaya' => 'sedang', 'kepatuhan_prosedur' => 'melanggar', 'tingkat_risiko' => 'sedang'],
            
            // At-Risk / Potensi Tinggi / Melanggar LOTO/Prosedur -> Tinggi
            ['jenis_perilaku' => 'at_risk', 'kategori_id' => $katLoto->id, 'lokasi_id' => $lokProcess->id, 'tingkat_potensi_bahaya' => 'tinggi', 'kepatuhan_prosedur' => 'melanggar', 'tingkat_risiko' => 'tinggi'],
            ['jenis_perilaku' => 'at_risk', 'kategori_id' => $katProsedur->id, 'lokasi_id' => $lokProcess->id, 'tingkat_potensi_bahaya' => 'tinggi', 'kepatuhan_prosedur' => 'melanggar', 'tingkat_risiko' => 'tinggi'],
            ['jenis_perilaku' => 'at_risk', 'kategori_id' => $katLoto->id, 'lokasi_id' => $lokWorkshop->id, 'tingkat_potensi_bahaya' => 'tinggi', 'kepatuhan_prosedur' => 'melanggar', 'tingkat_risiko' => 'tinggi'],
            ['jenis_perilaku' => 'at_risk', 'kategori_id' => $katApd->id, 'lokasi_id' => $lokProcess->id, 'tingkat_potensi_bahaya' => 'tinggi', 'kepatuhan_prosedur' => 'melanggar', 'tingkat_risiko' => 'tinggi'],
        ];

        foreach ($trainingDataset as $data) {
            TrainingC45::create($data);
        }

        // 5. Sampel Data Observasi Awal
        ObservasiBbs::create([
            'tanggal' => date('Y-m-d'),
            'user_id' => $karyawanActive->id,
            'lokasi_id' => $lokWorkshop->id,
            'unit_departemen' => 'Operasional Plant A',
            'jenis_perilaku' => 'safe',
            'kategori_id' => $katApd->id,
            'ketika' => 'Saat inspeksi rutin perbaikan pompa hidrolic jam 09.00 WIB.',
            'ditemukan' => 'Teknisi menggunakan APD lengkap (kacamata safety, sarung tangan kulit, dan sepatu safety) saat memotong plat besi.',
            'sebab' => 'Peningkatan kesadaran keselamatan pribadi.',
            'saran' => 'Pertahankan kepatuhan penggunaan APD standar.',
            'tingkat_risiko' => 'rendah',
            'status_tindak_lanjut' => 'closed',
            'catatan_tindak_lanjut' => 'Observasi telah diverifikasi dan disetujui HES.',
        ]);

        ObservasiBbs::create([
            'tanggal' => date('Y-m-d'),
            'user_id' => $karyawanActive->id,
            'lokasi_id' => $lokProcess->id,
            'unit_departemen' => 'Operasional Plant A',
            'jenis_perilaku' => 'at_risk',
            'kategori_id' => $katLoto->id,
            'ketika' => 'Saat pemeliharaan katup utama jalur pipa gas cair.',
            'ditemukan' => 'Petunjuk isolasi energi (LOTO) belum dipasang pada katup pasokan utama saat perbaikan berlangsung.',
            'sebab' => 'Terburu-buru mengejar target waktu perbaikan.',
            'saran' => 'Hentikan sementara pekerjaan sampai gembok dan tag LOTO dipasang secara benar.',
            'tingkat_risiko' => 'tinggi',
            'status_tindak_lanjut' => 'in_progress',
            'catatan_tindak_lanjut' => 'Instruksi penghentian pekerjaan sementara dan pemasangan gembok LOTO sedang dijalankan oleh pengawas area.',
        ]);
    }
}
