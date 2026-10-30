@extends('layouts.karyawan')

@section('title', 'Profil Saya')
@section('page-title', 'Pengaturan Profil')

@section('content')
    <div style="max-width: 640px;">
        <div class="card">
            <div class="card-title" style="margin-bottom: 1.5rem;">👤 Edit Profil Karyawan</div>

            <form action="{{ route('karyawan.profil.update') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Email (Akun)</label>
                    <input type="email" class="form-control" value="{{ $user->email }}" disabled style="background: #f8fafc; color: #94a3b8;">
                    <small style="font-size: 0.775rem; color: #94a3b8; margin-top: 0.25rem; display: block;">Email tidak dapat diubah.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">No. Telepon / HP</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $user->no_hp) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Jabatan / Posisi</label>
                    <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $user->jabatan) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Unit / Departemen</label>
                    <input type="text" name="unit_departemen" class="form-control" value="{{ old('unit_departemen', $user->unit_departemen) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">No. Badge <span style="font-weight:400; color:#94a3b8;">(Opsional)</span></label>
                    <input type="text" name="no_batch" class="form-control" placeholder="Masukkan nomor badge" value="{{ old('no_batch', $user->no_batch) }}">
                </div>

                <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 1.5rem 0;">
                <p style="font-size: 0.825rem; color: #64748b; margin-bottom: 1rem;">Kosongkan jika tidak ingin mengganti password.</p>

                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••">
                </div>

                <div class="form-group">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••">
                </div>

                <div style="padding-top: 1rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <a href="{{ route('karyawan.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
