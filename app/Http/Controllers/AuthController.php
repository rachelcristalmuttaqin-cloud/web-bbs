<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('autentifikasi.login');
    }

    /**
     * Proses autentikasi login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Cek status verifikasi akun (pending / rejected)
            if ($user->status === 'pending') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun Anda masih dalam tahap peninjauan/verifikasi Admin HES. Silakan tunggu persetujuan.',
                ])->onlyInput('email');
            }

            if ($user->status === 'rejected') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Permohonan akun Anda telah ditolak oleh Admin HES. Hubungi divisi HES untuk informasi lebih lanjut.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return $this->redirectBasedOnRole($user, 'Selamat datang kembali, ' . $user->name . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Tampilkan form registrasi.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('autentifikasi.register');
    }

    /**
     * Proses pendaftaran pengguna baru.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'in:karyawan,admin'],
            'no_hp' => ['required', 'string', 'max:20'],
            'jabatan' => ['required', 'string', 'max:255'],
            'unit_departemen' => ['required', 'string', 'max:255'],
            'no_batch' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'role.in' => 'Jenis akun yang dipilih tidak valid.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'unit_departemen.required' => 'Unit / Departemen wajib diisi.',
            'no_batch.max' => 'Nomor badge maksimal 50 karakter.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'jabatan' => $request->jabatan,
            'unit_departemen' => $request->unit_departemen,
            'no_batch' => $request->no_batch,
            'password' => Hash::make($request->password),
            // Semua pendaftar, termasuk calon Admin HES, wajib menunggu persetujuan admin aktif.
            'role' => $request->role,
            'status' => 'pending',
        ]);

        return redirect()->route('login')->with('success', 'Permohonan akun berhasil dikirim. Akun Anda menunggu verifikasi Admin HES dan belum dapat digunakan untuk login.');
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }

    /**
     * Tampilkan form lupa password.
     */
    public function showForgotPassword()
    {
        return view('autentifikasi.forgot-password');
    }

    /**
     * Buat dan kirim tautan reset password.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Akun dengan alamat email tersebut tidak terdaftar.',
            ])->onlyInput('email');
        }

        // Generate token acak yang aman
        $token = Str::random(64);

        // Simpan token ke database password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);

        // Coba kirim email via Laravel Mail jika SMTP aktif
        try {
            Mail::send('emails.reset-password', [
                'resetUrl' => $resetUrl,
                'user' => $user
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Tautan Reset Password - Sistem BBS');
            });
        } catch (\Throwable $e) {
            Log::info('Email reset password log mode / local: ' . $e->getMessage());
        }

        return back()->with([
            'success' => 'Tautan pembaruan kata sandi telah berhasil diproses.',
            'reset_url' => $resetUrl,
        ]);
    }

    /**
     * Tampilkan form reset password dengan token.
     */
    public function showResetPassword(Request $request, string $token)
    {
        return view('autentifikasi.reset-password', [
            'token' => $token,
            'email' => $request->query('email', old('email')),
        ]);
    }

    /**
     * Proses update password baru pengguna.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'token.required' => 'Token reset password tidak ditemukan.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record) {
            return back()->withErrors([
                'email' => 'Permintaan reset password tidak valid atau sudah kedaluwarsa.',
            ]);
        }

        // Token kedaluwarsa setelah 60 menit
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors([
                'email' => 'Tautan reset password telah kedaluwarsa. Silakan ajukan permohonan baru.',
            ]);
        }

        // Verifikasi token
        if (!Hash::check($request->token, $record->token) && $request->token !== $record->token) {
            return back()->withErrors([
                'email' => 'Token reset password tidak valid.',
            ]);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors([
                'email' => 'Akun pengguna tidak ditemukan.',
            ]);
        }

        // Update password baru
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token yang sudah dipakai
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password akun ' . $user->email . ' berhasil direset! Silakan login dengan password baru Anda.');
    }

    /**
     * Helper untuk mengarahkan pengguna sesuai role.
     */
    private function redirectBasedOnRole(User $user, ?string $message = null)
    {
        $redirect = $user->role === 'admin'
            ? redirect()->route('admin.index')
            : redirect()->route('karyawan.index');

        if ($message) {
            $redirect->with('success', $message);
        }

        return $redirect;
    }
}
