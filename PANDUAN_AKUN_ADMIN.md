# Pengelolaan Akun Admin HES

## Pendaftaran kru

Formulir pendaftaran umum hanya membuat akun `karyawan` dengan status `pending`. Peran ditetapkan oleh server, sehingga mengirim nilai `admin` dari browser tidak dapat membuat akun Admin.

Admin HES memeriksa identitas dan nomor badge kru pada menu **Verifikasi User**, lalu menyetujui atau menolak pendaftarannya.

## Membuat akun Admin HES

Akun Admin hanya dibuat oleh operator yang berwenang melalui terminal pada server/project:

```powershell
php artisan hes:admin-create
```

Perintah meminta nama, email, dan password secara interaktif. Password tidak ditampilkan saat diketik, minimal 12 karakter, dan harus dikonfirmasi. Email yang sudah dipakai ditolak tanpa mengubah akun tersebut. Ulangi perintah untuk setiap Admin yang disahkan perusahaan.

Jangan menaruh password di source code, seeder umum, atau file ekspor. Berikan kredensial kepada pemilik akun melalui saluran yang disetujui perusahaan.

## Mengatasi Lupa Password Admin / Pengguna

Jika seorang Admin HES atau Karyawan lupa kata sandi login, ada 2 opsi penanganan mandiri oleh perusahaan tanpa ketergantungan pada pengembang:

### 1. Reset Mandiri via Web (Self-Service)
Pengguna cukup membuka halaman login, lalu mengklik tautan **"Lupa Password?"**. Masukkan email terdaftar, dan sistem akan menyiapkan tautan pembaruan kata sandi baru yang berlaku selama 60 menit.

### 2. Reset Langsung oleh Tim IT Server (Emergency Console Reset)
Jika email kantor tidak dapat diakses atau terjadi kendala darurat, Tim IT perusahaan dapat mereset kata sandi akun secara langsung dari terminal server:

```powershell
php artisan hes:admin-reset
```

Perintah akan meminta email akun serta kata sandi baru (minimal 8 karakter) secara aman dan langsung mengaktifkan kata sandi baru tersebut.

## Seeder demo

`BbsSeeder` tidak lagi membuat akun Admin contoh dengan password bawaan. Akun demo Karyawan dan observasi contoh tetap hanya untuk kebutuhan seeding lokal.
