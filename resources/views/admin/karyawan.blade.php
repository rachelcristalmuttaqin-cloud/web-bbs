@extends('layouts.admin')

@section('title', 'Data Karyawan PT')
@section('page-title', 'Data Karyawan PT & Status Keaktifan')

@section('styles')
    <style>
        .employee-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 1.25rem;
            padding: 1rem 1.1rem;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: linear-gradient(135deg, #f8fafc 0%, #fff 75%);
        }
        .employee-pagination-summary { display: grid; gap: 0.2rem; color: #64748b; font-size: 0.78rem; }
        .employee-pagination-summary strong { color: #0f172a; font-size: 0.85rem; }
        .employee-pagination-nav { display: flex; align-items: center; justify-content: flex-end; gap: 0.35rem; }
        .employee-page-link {
            display: inline-flex;
            min-width: 2.35rem;
            height: 2.35rem;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0 0.65rem;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            background: #fff;
            color: #475569;
            font-size: 0.82rem;
            font-weight: 700;
            text-decoration: none;
            transition: border-color 0.18s, background 0.18s, color 0.18s, transform 0.18s, box-shadow 0.18s;
        }
        .employee-page-link:hover {
            transform: translateY(-1px);
            border-color: #c7d2fe;
            background: #eef2ff;
            color: #4338ca;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.1);
        }
        .employee-page-link.is-current {
            border-color: #4f46e5;
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #fff;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.22);
        }
        .employee-page-link.is-disabled { background: #f8fafc; color: #cbd5e1; cursor: not-allowed; }
        .employee-page-link.is-disabled:hover { transform: none; border-color: #e2e8f0; background: #f8fafc; color: #cbd5e1; box-shadow: none; }
        .employee-page-ellipsis { padding: 0 0.15rem; color: #94a3b8; font-size: 0.9rem; }
        .employee-page-link svg { width: 1rem; height: 1rem; flex: none; }
        @media (max-width: 640px) {
            .employee-pagination { align-items: stretch; flex-direction: column; }
            .employee-pagination-nav { justify-content: space-between; }
            .employee-page-link { min-width: 2.15rem; height: 2.15rem; padding: 0 0.5rem; }
            .employee-page-link-label { display: none; }
            .employee-page-number:not(.is-current) { display: none; }
            .employee-page-ellipsis { display: none; }
        }
    </style>
@endsection

@section('content')
    <!-- STATS SUMMARY CARDS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">👥</div>
            <div>
                <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">TOTAL KARYAWAN</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">{{ $totalKaryawan }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #f0fdf4; color: #166534;">✅</div>
            <div>
                <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">STATUS AKTIF</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #166534;">{{ $totalAktif }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fef9c3; color: #854d0e;">⏳</div>
            <div>
                <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">PENDING VERIFIKASI</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #854d0e;">{{ $totalPending }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fef2f2; color: #991b1b;">🚫</div>
            <div>
                <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">NONAKTIF / DITOLAK</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #991b1b;">{{ $totalRejected }}</div>
            </div>
        </div>
    </div>

    <!-- MAIN CARD & FILTER -->
    <div class="card">
        <div class="card-header" style="flex-wrap: wrap; gap: 1rem;">
            <div class="card-title">📋 Daftar Seluruh Karyawan PT</div>
            
            <form action="{{ route('admin.karyawan.index') }}" method="GET" style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
                <select name="status" class="form-control" style="width: auto; padding: 0.45rem 0.75rem; font-size: 0.85rem;" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Nonaktif / Ditolak</option>
                </select>

                <input type="text" name="search" class="form-control" placeholder="Cari nama, dept, nomor badge..." value="{{ request('search') }}" style="width: 220px; padding: 0.45rem 0.75rem; font-size: 0.85rem;">

                <button type="submit" class="btn btn-primary btn-sm">🔍 Cari</button>
                @if(request()->filled('status') || request()->filled('search'))
                    <a href="{{ route('admin.karyawan.index') }}" onclick="window.location.href='{{ route('admin.karyawan.index') }}'; return false;" class="btn btn-secondary btn-sm">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Karyawan</th>
                        <th>No. HP</th>
                        <th>Jabatan & Dept</th>
                        <th>No. Badge</th>
                        <th>Status PT</th>
                        <th>Terdaftar</th>
                        <th style="text-align: center;">Aksi Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($karyawanList as $index => $user)
                        <tr>
                            <td>{{ $karyawanList->firstItem() + $index }}</td>
                            <td>
                                <div><strong>{{ $user->name }}</strong></div>
                                <div style="font-size: 0.78rem; color: #64748b;">{{ $user->email }}</div>
                            </td>
                            <td style="font-size: 0.85rem;">{{ $user->no_hp ?? '—' }}</td>
                            <td>
                                <div style="font-weight: 600; font-size: 0.85rem; color: #334155;">{{ $user->jabatan ?? '—' }}</div>
                                <div style="font-size: 0.78rem; color: #64748b;">{{ $user->unit_departemen ?? '—' }}</div>
                            </td>
                            <td>
                                @if($user->no_batch)
                                    <span class="badge badge-identifier">{{ $user->no_batch }}</span>
                                @else
                                    <span style="color:#94a3b8; font-size:0.8rem;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($user->status === 'active')
                                    <span class="badge badge-active">🟢 Aktif</span>
                                @elseif($user->status === 'pending')
                                    <span class="badge badge-pending">🟡 Pending</span>
                                @else
                                    <span class="badge badge-rejected">🔴 Nonaktif</span>
                                @endif
                            </td>
                            <td style="font-size: 0.8rem; color: #64748b;">
                                {{ $user->created_at ? $user->created_at->format('d M Y') : '—' }}
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 0.4rem; justify-content: center; flex-wrap: wrap;">
                                    @if($user->status !== 'active')
                                        <form action="{{ route('admin.verifikasi.approve', $user->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" title="Aktifkan Karyawan">
                                                ✅ Aktifkan
                                            </button>
                                        </form>
                                    @endif

                                    @if($user->status !== 'rejected')
                                        <form action="{{ route('admin.verifikasi.reject', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan/menolak karyawan ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm" title="Nonaktifkan Karyawan">
                                                🚫 Nonaktifkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: #94a3b8; padding: 2.5rem;">
                                🔍 Tidak ada data karyawan yang sesuai dengan kriteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($karyawanList->hasPages())
            @php
                $currentPage = $karyawanList->currentPage();
                $lastPage = $karyawanList->lastPage();
                $pageStart = max(1, $currentPage - 2);
                $pageEnd = min($lastPage, $currentPage + 2);
            @endphp
            <div class="employee-pagination">
                <div class="employee-pagination-summary">
                    <strong>Halaman {{ $currentPage }} dari {{ $lastPage }}</strong>
                    <span>Menampilkan {{ number_format($karyawanList->firstItem()) }}&ndash;{{ number_format($karyawanList->lastItem()) }} dari {{ number_format($karyawanList->total()) }} karyawan</span>
                </div>

                <nav class="employee-pagination-nav" aria-label="Navigasi halaman data karyawan">
                    @if($karyawanList->onFirstPage())
                        <span class="employee-page-link is-disabled" aria-disabled="true">
                            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M12.5 4.5 7 10l5.5 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span class="employee-page-link-label">Sebelumnya</span>
                        </span>
                    @else
                        <a class="employee-page-link" href="{{ $karyawanList->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya">
                            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M12.5 4.5 7 10l5.5 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span class="employee-page-link-label">Sebelumnya</span>
                        </a>
                    @endif

                    @if($pageStart > 1)
                        <a class="employee-page-link employee-page-number" href="{{ $karyawanList->url(1) }}" aria-label="Halaman 1">1</a>
                        @if($pageStart > 2)<span class="employee-page-ellipsis" aria-hidden="true">&hellip;</span>@endif
                    @endif

                    @foreach(range($pageStart, $pageEnd) as $page)
                        @if($page === $currentPage)
                            <span class="employee-page-link employee-page-number is-current" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="employee-page-link employee-page-number" href="{{ $karyawanList->url($page) }}" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($pageEnd < $lastPage)
                        @if($pageEnd < $lastPage - 1)<span class="employee-page-ellipsis" aria-hidden="true">&hellip;</span>@endif
                        <a class="employee-page-link employee-page-number" href="{{ $karyawanList->url($lastPage) }}" aria-label="Halaman {{ $lastPage }}">{{ $lastPage }}</a>
                    @endif

                    @if($karyawanList->hasMorePages())
                        <a class="employee-page-link" href="{{ $karyawanList->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya">
                            <span class="employee-page-link-label">Berikutnya</span>
                            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m7.5 4.5 5.5 5.5-5.5 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    @else
                        <span class="employee-page-link is-disabled" aria-disabled="true">
                            <span class="employee-page-link-label">Berikutnya</span>
                            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m7.5 4.5 5.5 5.5-5.5 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                    @endif
                </nav>
            </div>
        @endif
    </div>
@endsection
