<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateHesAdmin extends Command
{
    protected $signature = 'hes:admin-create';

    protected $description = 'Create an authorized Admin HES account from the server console';

    public function handle(): int
    {
        $name = trim((string) $this->ask('Nama Admin HES'));
        $email = strtolower(trim((string) $this->ask('Email Admin HES')));

        if ($name === '') {
            $this->components->error('Nama wajib diisi.');

            return self::FAILURE;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->components->error('Format email tidak valid.');

            return self::FAILURE;
        }

        if (User::query()->where('email', $email)->exists()) {
            $this->components->error('Email tersebut sudah terdaftar. Akun yang ada tidak diubah.');

            return self::FAILURE;
        }

        $password = (string) $this->secret('Password Admin HES (minimal 12 karakter)');
        if (strlen($password) < 12) {
            $this->components->error('Password harus memiliki minimal 12 karakter.');

            return self::FAILURE;
        }

        $passwordConfirmation = (string) $this->secret('Ulangi password Admin HES');
        if (!hash_equals($password, $passwordConfirmation)) {
            $this->components->error('Konfirmasi password tidak cocok.');

            return self::FAILURE;
        }

        $user = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->components->info('Akun Admin HES '.$user->email.' berhasil dibuat dan aktif.');

        return self::SUCCESS;
    }
}
