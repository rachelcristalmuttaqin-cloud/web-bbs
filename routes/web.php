<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminBbsController;
use App\Http\Controllers\KaryawanBbsController;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;

// Redirect root URL ke dashboard jika sudah login, atau ke login jika belum
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        return $user->role === 'admin'
            ? redirect()->route('admin.index')
            : redirect()->route('karyawan.index');
    }
    return redirect()->route('login');
});

// Route Guest (Belum Authenticated)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Route Authenticated (Sudah Login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Route Khusus Admin / HES
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminBbsController::class, 'index'])->name('index');
        
        // Verifikasi User
        Route::get('/verifikasi-user', [AdminBbsController::class, 'verifikasiUser'])->name('verifikasi');
        Route::post('/verifikasi-user/{id}/approve', [AdminBbsController::class, 'approveUser'])->name('verifikasi.approve');
        Route::post('/verifikasi-user/{id}/reject', [AdminBbsController::class, 'rejectUser'])->name('verifikasi.reject');

        // Data Karyawan PT
        Route::get('/karyawan', [AdminBbsController::class, 'daftarKaryawan'])->name('karyawan.index');

        // Kelola Observasi BBS
        Route::get('/observasi', [AdminBbsController::class, 'kelolaObservasi'])->name('observasi.index');
        Route::get('/observasi/{id}', [AdminBbsController::class, 'detailObservasi'])->name('observasi.detail');
        Route::post('/observasi/{id}/tindak-lanjut', [AdminBbsController::class, 'updateTindakLanjut'])->name('observasi.tindak_lanjut');
        Route::post('/observasi/{id}/validasi-risiko', [AdminBbsController::class, 'validasiRisikoHes'])->name('observasi.validasi_risiko');

        // Modul Algoritma C4.5
        Route::get('/c45', [AdminBbsController::class, 'modulC45'])->name('c45.index');
        Route::post('/c45/keputusan', [AdminBbsController::class, 'storeKeputusanHes'])->name('c45.keputusan');

        // Master Data (Kategori & Lokasi)
        Route::get('/master', [AdminBbsController::class, 'masterData'])->name('master.index');
        Route::post('/master/kategori', [AdminBbsController::class, 'storeKategori'])->name('master.kategori.store');
        Route::post('/master/kategori/{id}/delete', [AdminBbsController::class, 'deleteKategori'])->name('master.kategori.delete');
        Route::post('/master/lokasi', [AdminBbsController::class, 'storeLokasi'])->name('master.lokasi.store');
        Route::post('/master/lokasi/{id}/delete', [AdminBbsController::class, 'deleteLokasi'])->name('master.lokasi.delete');

        // Laporan
        Route::get('/laporan', [AdminBbsController::class, 'laporan'])->name('laporan.index');
        Route::get('/laporan/export-excel', [AdminBbsController::class, 'exportLaporanExcel'])->name('laporan.export_excel');
    });

    // Route Khusus Karyawan
    Route::middleware('role:karyawan')->prefix('karyawan')->name('karyawan.')->group(function () {
        Route::get('/', [KaryawanBbsController::class, 'index'])->name('index');
        
        // Observasi BBS Karyawan
        Route::get('/observasi/baru', [KaryawanBbsController::class, 'createObservasi'])->name('observasi.create');
        Route::post('/observasi/baru', [KaryawanBbsController::class, 'storeObservasi'])->name('observasi.store');
        Route::get('/observasi', [KaryawanBbsController::class, 'riwayatObservasi'])->name('observasi.index');
        Route::get('/observasi/{id}', [KaryawanBbsController::class, 'detailObservasi'])->name('observasi.detail');

        // Profil
        Route::get('/profil', [KaryawanBbsController::class, 'profil'])->name('profil');
        Route::post('/profil', [KaryawanBbsController::class, 'updateProfil'])->name('profil.update');
    });
});
