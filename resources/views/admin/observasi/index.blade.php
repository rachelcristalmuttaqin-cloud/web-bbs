@extends('layouts.admin')

@section('title', 'Data Observasi BBS')
@section('page-title', 'Data Observasi BBS')

@section('styles')
    <style>
        .observasi-checklist-points { display: flex; flex-wrap: wrap; gap: 0.3rem; min-width: 105px; }
        .observasi-checklist-point {
            display: inline-flex;
            align-items: center;
            gap: 0.22rem;
            padding: 0.22rem 0.45rem;
            border: 1px solid transparent;
            border-radius: 7px;
            font-size: 0.72rem;
            font-weight: 800;
            white-space: nowrap;
        }
        .observasi-checklist-point svg { width: 0.8rem; height: 0.8rem; }
        .observasi-checklist-safe { color: #15803d; background: #f0fdf4; border-color: #bbf7d0; }
        .observasi-checklist-risk { color: #b91c1c; background: #fef2f2; border-color: #fecaca; }
        .observasi-pagination {
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
        .observasi-pagination-summary { display: grid; gap: 0.2rem; color: #64748b; font-size: 0.78rem; }
        .observasi-pagination-summary strong { color: #0f172a; font-size: 0.85rem; }
        .observasi-pagination-nav { display: flex; align-items: center; justify-content: flex-end; gap: 0.35rem; }
        .observasi-page-link {
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
        .observasi-page-link:hover { transform: translateY(-1px); border-color: #c7d2fe; background: #eef2ff; color: #4338ca; box-shadow: 0 4px 10px rgba(79,70,229,0.1); }
        .observasi-page-link.is-current { border-color: #4f46e5; background: linear-gradient(135deg,#4f46e5,#6366f1); color: #fff; box-shadow: 0 4px 10px rgba(79,70,229,0.22); }
        .observasi-page-link.is-disabled { background: #f8fafc; color: #cbd5e1; cursor: not-allowed; }
        .observasi-page-link.is-disabled:hover { transform: none; border-color: #e2e8f0; background: #f8fafc; color: #cbd5e1; box-shadow: none; }
        .observasi-page-link svg { width: 1rem; height: 1rem; flex: none; }
        .observasi-page-ellipsis { padding: 0 0.15rem; color: #94a3b8; }
        .risk-review-inline { display: flex; align-items: center; gap: 0.35rem; min-width: 205px; }
        .risk-review-inline .form-control { min-width: 105px; padding: 0.4rem; font-size: 0.78rem; }
        .risk-review-inline .btn { padding: 0.4rem 0.55rem; white-space: nowrap; }
        @media (max-width: 640px) {
            .observasi-pagination { align-items: stretch; flex-direction: column; }
            .observasi-pagination-nav { justify-content: space-between; }
            .observasi-page-link { min-width: 2.15rem; height: 2.15rem; padding: 0 0.5rem; }
            .observasi-page-label, .observasi-page-number:not(.is-current), .observasi-page-ellipsis { display: none; }
        }
    </style>
@endsection

@section('content')
    <!-- Filter -->
    <div class="card" style="margin-bottom: 1.25rem;">
        <form action="{{ route('admin.observasi.index') }}" method="GET" style="display: flex; gap: 0.875rem; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 180px;">
                <label class="form-label">Jenis Perilaku</label>
                <select name="jenis_perilaku" class="form-control">
                    <option value="">Semua Perilaku</option>
                    <option value="safe" {{ request('jenis_perilaku') === 'safe' ? 'selected' : '' }}>SAFE</option>
                    <option value="at_risk" {{ request('jenis_perilaku') === 'at_risk' ? 'selected' : '' }}>AT-RISK</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 180px;">
                <label class="form-label">Prediksi Risiko Awal</label>
                <select name="tingkat_risiko" class="form-control">
                    <option value="">Semua Risiko</option>
                    <option value="rendah" {{ request('tingkat_risiko') === 'rendah' ? 'selected' : '' }}>Rendah</option>
                    <option value="sedang" {{ request('tingkat_risiko') === 'sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="tinggi" {{ request('tingkat_risiko') === 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 180px;">
                <label class="form-label">Validasi Risiko HES</label>
                <select name="validasi_risiko" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('validasi_risiko') === 'pending' ? 'selected' : '' }}>Belum divalidasi</option>
                    <option value="validated" {{ request('validasi_risiko') === 'validated' ? 'selected' : '' }}>Sudah divalidasi</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 160px;">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div style="flex: 1; min-width: 160px;">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary">🔍 Filter</button>
                <a href="{{ route('admin.observasi.index') }}" onclick="window.location.href='{{ route('admin.observasi.index') }}'; return false;" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">📋 Daftar Observasi BBS ({{ $observasiList->total() }})</span>
            <a href="{{ route('admin.laporan.index') }}" class="btn btn-sm btn-secondary">🖨️ Cetak Laporan</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:56px;">No.</th>
                        <th>Tanggal</th>
                        <th>Observer</th>
                        <th>Lokasi</th>
                        <th>Kategori</th>
                        <th>Poin Checklist</th>
                        <th>Perilaku Sistem / Final</th>
                        <th>Risiko C4.5</th>
                        <th>Hasil Validasi HES</th>
                        <th>Tindak Lanjut</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($observasiList as $index => $obs)
                        <tr>
                            <td style="font-weight:700;color:#64748b;">{{ $observasiList->firstItem() + $index }}</td>
                            <td style="font-size: 0.825rem;">{{ date('d M Y', strtotime($obs->tanggal)) }}</td>
                            <td><strong>{{ $obs->user->name ?? '-' }}</strong></td>
                            <td style="font-size: 0.825rem;">{{ $obs->lokasi->nama_lokasi ?? '-' }}</td>
                            <td style="font-size: 0.825rem;">{{ Str::limit($obs->kategori->nama_kategori ?? '-', 20) }}</td>
                            <td>
                                @php
                                    $checkedItems = collect($obs->item_states ?? [])
                                        ->filter(fn ($state) => in_array($state, ['safe', 'at_risk'], true));
                                @endphp
                                @if($checkedItems->isNotEmpty())
                                    <div class="observasi-checklist-points" aria-label="Poin checklist terpilih">
                                        @foreach($checkedItems as $code => $state)
                                            <span class="observasi-checklist-point {{ $state === 'safe' ? 'observasi-checklist-safe' : 'observasi-checklist-risk' }}" title="{{ $state === 'safe' ? 'Safe' : 'At-Risk' }}">
                                                <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="m3.5 8.2 2.8 2.8 6.2-6.3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                {{ $code }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color:#94a3b8;">—</span>
                                @endif
                            </td>
                            <td>
                                <small style="display:block;color:#64748b;">Sistem</small>
                                <span class="badge badge-{{ $obs->rekomendasi_perilaku ?? $obs->jenis_perilaku }}">{{ strtoupper($obs->rekomendasi_perilaku ?? $obs->jenis_perilaku) }}</span>
                                @if($obs->risiko_hes)<small style="display:block;color:#64748b;margin-top:.3rem;">Final HES</small><span class="badge badge-{{ $obs->jenis_perilaku }}">{{ strtoupper($obs->jenis_perilaku) }}</span>@endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $obs->rekomendasi_risiko ?? $obs->tingkat_risiko }}">{{ strtoupper($obs->rekomendasi_risiko ?? $obs->tingkat_risiko) }}</span>
                                @if($obs->risiko_hes)<small style="display:block;color:#64748b;margin-top:.3rem;">Final HES: {{ strtoupper($obs->risiko_hes) }}</small>@endif
                            </td>
                            <td>
                                <form action="{{ route('admin.observasi.validasi_risiko', $obs->id) }}" method="POST" class="risk-review-inline">
                                    @csrf
                                    <select name="risiko_hes" class="form-control" aria-label="Label risiko final HES untuk observasi {{ $obs->id }}" required>
                                        <option value="">Pilih label</option>
                                        <option value="rendah" {{ ($obs->risiko_hes ?? $obs->rekomendasi_risiko ?? $obs->tingkat_risiko) === 'rendah' ? 'selected' : '' }}>Rendah</option>
                                        <option value="sedang" {{ ($obs->risiko_hes ?? $obs->rekomendasi_risiko ?? $obs->tingkat_risiko) === 'sedang' ? 'selected' : '' }}>Sedang</option>
                                        <option value="tinggi" {{ ($obs->risiko_hes ?? $obs->rekomendasi_risiko ?? $obs->tingkat_risiko) === 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">{{ $obs->risiko_hes ? 'Perbarui' : 'Simpan' }}</button>
                                </form>
                                @if($obs->risiko_hes && !$obs->risiko_hes_oleh)
                                    <small style="display:block; color:#64748b; margin-top:0.25rem;">Label data pelatihan</small>
                                @endif
                            </td>
                            <td style="font-size: 0.8rem; color: #64748b;">{{ strtoupper($obs->status_tindak_lanjut) }}</td>
                            <td><a href="{{ route('admin.observasi.detail', $obs->id) }}" class="btn btn-sm btn-secondary">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="11" style="text-align:center; color:#94a3b8; padding: 2rem;">Tidak ada data observasi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($observasiList->hasPages())
            @php
                $currentPage = $observasiList->currentPage();
                $lastPage = $observasiList->lastPage();
                $pageStart = max(1, $currentPage - 2);
                $pageEnd = min($lastPage, $currentPage + 2);
            @endphp
            <div class="observasi-pagination">
                <div class="observasi-pagination-summary">
                    <strong>Halaman {{ $currentPage }} dari {{ $lastPage }}</strong>
                    <span>Menampilkan {{ number_format($observasiList->firstItem()) }}&ndash;{{ number_format($observasiList->lastItem()) }} dari {{ number_format($observasiList->total()) }} observasi</span>
                </div>
                <nav class="observasi-pagination-nav" aria-label="Navigasi halaman data observasi BBS">
                    @if($observasiList->onFirstPage())
                        <span class="observasi-page-link is-disabled" aria-disabled="true">
                            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M12.5 4.5 7 10l5.5 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span class="observasi-page-label">Sebelumnya</span>
                        </span>
                    @else
                        <a class="observasi-page-link" href="{{ $observasiList->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya">
                            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M12.5 4.5 7 10l5.5 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span class="observasi-page-label">Sebelumnya</span>
                        </a>
                    @endif

                    @if($pageStart > 1)
                        <a class="observasi-page-link observasi-page-number" href="{{ $observasiList->url(1) }}" aria-label="Halaman 1">1</a>
                        @if($pageStart > 2)<span class="observasi-page-ellipsis" aria-hidden="true">&hellip;</span>@endif
                    @endif
                    @foreach(range($pageStart, $pageEnd) as $page)
                        @if($page === $currentPage)
                            <span class="observasi-page-link observasi-page-number is-current" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="observasi-page-link observasi-page-number" href="{{ $observasiList->url($page) }}" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if($pageEnd < $lastPage)
                        @if($pageEnd < $lastPage - 1)<span class="observasi-page-ellipsis" aria-hidden="true">&hellip;</span>@endif
                        <a class="observasi-page-link observasi-page-number" href="{{ $observasiList->url($lastPage) }}" aria-label="Halaman {{ $lastPage }}">{{ $lastPage }}</a>
                    @endif

                    @if($observasiList->hasMorePages())
                        <a class="observasi-page-link" href="{{ $observasiList->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya">
                            <span class="observasi-page-label">Berikutnya</span>
                            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m7.5 4.5 5.5 5.5-5.5 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    @else
                        <span class="observasi-page-link is-disabled" aria-disabled="true">
                            <span class="observasi-page-label">Berikutnya</span>
                            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m7.5 4.5 5.5 5.5-5.5 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                    @endif
                </nav>
            </div>
        @endif
    </div>
@endsection
