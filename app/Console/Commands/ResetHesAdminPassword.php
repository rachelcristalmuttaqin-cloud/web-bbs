<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetHesAdminPassword extends Command
{
    protected $signature = 'hes:admin-reset {email? : Email akun yang akan direset}';

    protected $description = 'Reset password akun Admin HES atau pengguna secara langsung dari terminal server';

    public function handle(): int
    {
        $email = $this->argument('email') ? strtolower(trim((string) $this->argument('email'))) : null;

        if (!$email) {
            $email = strtolower(trim((string) $this->ask('Masukkan Email akun yang ingin direset passwordnya')));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->components->error('Format email tidak valid.');
            return self::FAILURE;
        }

        $user = User::query()->where('email', $email)->first();

        if (!$user) {
            $this->components->error('Akun dengan email '.$email.' tidak ditemukan di database.');
            return self::FAILURE;
        }

        $this->components->info('Akun ditemukan: '.$user->name.' (Role: '.$user->role.', Status: '.$user->status.')');

        $password = (string) $this->secret('Masukkan Password Baru (minimal 8 karakter)');
        if (strlen($password) < 8) {
            $this->components->error('Password minimal 8 karakter.');
            return self::FAILURE;
        }

        $passwordConfirmation = (string) $this->secret('Ulangi Password Baru');
        if (!hash_equals($password, $passwordConfirmation)) {
            $this->components->error('Konfirmasi password tidak cocok.');
            return self::FAILURE;
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->components->info('Berhasil! Password untuk '.$user->email.' telah berhasil diperbarui.');
        $this->components->info('Pengguna sekarang dapat login menggunakan password baru ini.');

        return self::SUCCESS;
    }
}

