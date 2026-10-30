@extends('layouts.karyawan')

@section('title', 'Riwayat Observasi')
@section('page-title', 'Riwayat Observasi Saya')

@section('content')
    <div class="card">
        <div class="card-header">
            <span class="card-title">📋 Semua Observasi BBS Saya ({{ $myObservasiList->total() }})</span>
            <a href="{{ route('karyawan.observasi.create') }}" class="btn btn-primary btn-sm">+ Observasi Baru</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Lokasi Area</th>
                        <th>Kategori BBS</th>
                        <th>Keterangan Lain-lain</th>
                        <th>Perilaku</th>
                        <th>Risiko (C4.5)</th>
                        <th>Status Tindak Lanjut</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($myObservasiList as $obs)
                        <tr>
                            <td>{{ date('d M Y', strtotime($obs->tanggal)) }}</td>
                            <td>{{ $obs->lokasi->nama_lokasi ?? '-' }}</td>
                            <td>{{ Str::limit($obs->kategori->nama_kategori ?? '-', 22) }}</td>
                            <td>{{ $obs->observasi_lainnya ?: '-' }}</td>
                            <td><span class="badge badge-{{ $obs->jenis_perilaku }}">{{ strtoupper($obs->jenis_perilaku) }}</span></td>
                            <td><span class="badge badge-{{ $obs->tingkat_risiko }}">{{ strtoupper($obs->tingkat_risiko) }}</span></td>
                            <td style="font-size: 0.8rem; color: #64748b; font-weight: 600;">{{ strtoupper($obs->status_tindak_lanjut) }}</td>
                            <td><a href="{{ route('karyawan.observasi.detail', $obs->id) }}" class="btn btn-sm btn-secondary">Detail</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: #94a3b8; padding: 2rem;">Belum ada data observasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1.25rem;">{{ $myObservasiList->links() }}</div>
    </div>
@endsection
