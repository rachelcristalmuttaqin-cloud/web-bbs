@extends('layouts.admin')

@section('title', 'Verifikasi Akun')
@section('page-title', 'Verifikasi Pendaftaran Akun')

@section('content')
    <div class="card">
        <div class="card-header">
            <span class="card-title">👥 Pendaftaran Akun Menunggu Verifikasi</span>
            <span class="badge badge-pending">{{ $pendingUsers->count() }} Pending</span>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Jenis Akun</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th>Jabatan</th>
                        <th>Unit / Dept</th>
                        <th>No. Badge</th>
                        <th>Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingUsers as $user)
                        <tr>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td><span class="badge {{ $user->role === 'admin' ? 'badge-identifier' : '' }}">{{ $user->role === 'admin' ? 'Calon Admin HES' : 'Karyawan' }}</span></td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->no_hp }}</td>
                            <td>{{ $user->jabatan }}</td>
                            <td>{{ $user->unit_departemen }}</td>
                            <td>
                                @if($user->no_batch)
                                    <span class="badge badge-identifier">{{ $user->no_batch }}</span>
                                @else
                                    <span style="color:#94a3b8; font-size:0.8rem;">—</span>
                                @endif
                            </td>
                            <td style="font-size: 0.8rem; color: #64748b;">{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <form action="{{ route('admin.verifikasi.approve', $user->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">✅ Setujui</button>
                                    </form>
                                    <form action="{{ route('admin.verifikasi.reject', $user->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">❌ Tolak</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; color: #94a3b8; padding: 2rem;">
                                🎉 Tidak ada pendaftaran yang menunggu verifikasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
