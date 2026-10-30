@extends('layouts.karyawan')

@section('title', 'Form Observasi BBS')
@section('page-title', 'Formulir Input Observasi BBS (Dokumen Resmi)')

@section('styles')
<style>
    .bbs-paper-form {
        background: #ffffff;
        color: #000000;
        font-family: 'Arial', sans-serif;
        font-size: 11px;
        line-height: 1.3;
        border: 2px solid #000;
        padding: 14px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        border-radius: 6px;
        max-width: 1150px;
        margin: 0 auto 2rem auto;
    }

    .bbs-header-table {
        width: 100%;
        border-collapse: collapse;
        border: 2px solid #000;
        margin-bottom: 10px;
    }

    .bbs-header-table td {
        border: 1px solid #000;
        padding: 6px 8px;
        vertical-align: middle;
    }

    .bbs-logo-cell {
        width: 32%;
        font-size: 9.5px;
        font-weight: bold;
        text-align: center;
    }

    .bbs-title-cell {
        width: 36%;
        text-align: center;
        font-size: 16px;
        font-weight: 900;
        letter-spacing: 0.5px;
    }

    .bbs-meta-cell {
        width: 32%;
        padding: 0 !important;
    }

    .bbs-inner-meta {
        width: 100%;
        border-collapse: collapse;
        font-size: 9.5px;
    }

    .bbs-inner-meta td {
        border-bottom: 1px solid #000;
        border-right: 1px solid #000;
        padding: 3px 5px;
    }

    .bbs-inner-meta tr:last-child td { border-bottom: none; }
    .bbs-inner-meta td:last-child { border-right: none; }

    .bbs-subhead-grid {
        display: grid;
        grid-template-columns: 2.2fr 2fr 1fr;
        gap: 6px 16px;
        border: 1.5px solid #000;
        padding: 10px 12px;
        margin-bottom: 10px;
        background: #f8fafc;
        font-size: 11px;
    }

    .bbs-subhead-grid div {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .bbs-input-inline {
        border: 1px solid #000;
        padding: 3px 6px;
        font-size: 11px;
        border-radius: 2px;
        width: 100%;
        background: #fff;
    }

    .bbs-main-layout {
        display: grid;
        grid-template-columns: 62% 38%;
        gap: 10px;
        align-items: start;
    }

    .bbs-checklist-table {
        width: 100%;
        border-collapse: collapse;
        border: 1.5px solid #000;
        font-size: 9.5px;
    }

    .bbs-checklist-table th, 
    .bbs-checklist-table td {
        border: 1px solid #000;
        padding: 3px 4px;
        vertical-align: middle;
    }

    .bbs-checklist-table th {
        background: #e2e8f0;
        font-weight: bold;
        text-align: center;
    }

    .bbs-cat-header {
        background: #000000;
        color: #ffffff;
        font-weight: bold;
        padding: 4px 6px !important;
        font-size: 10px;
    }

    .bbs-item-row {
        transition: background 0.15s;
    }

    .bbs-item-row.row-safe {
        background: #f0fdf4 !important;
    }

    .bbs-item-row.row-risk {
        background: #fef2f2 !important;
        font-weight: bold;
    }

    /* Toggle Buttons Safe / At Risk per item */
    .bbs-btn-toggle {
        border: 1.5px solid #cbd5e1;
        background: #ffffff;
        color: #64748b;
        font-weight: 800;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 4px;
        cursor: pointer;
        line-height: 1.2;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        user-select: none;
    }

    .bbs-btn-toggle:hover {
        transform: scale(1.15);
        border-color: #475569;
    }

    .bbs-btn-toggle.active-safe {
        background: #16a34a !important;
        color: #ffffff !important;
        border-color: #15803d !important;
        box-shadow: 0 0 0 2px rgba(22,163,74,0.35);
    }

    .bbs-btn-toggle.active-risk {
        background: #dc2626 !important;
        color: #ffffff !important;
        border-color: #b91c1c !important;
        box-shadow: 0 0 0 2px rgba(220,38,38,0.35);
    }

    .bbs-right-panel {
        border: 1.5px solid #000;
        padding: 10px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        background: #fff;
    }

    .bbs-field-box {
        border: 1px solid #000;
        border-radius: 2px;
        padding: 6px 8px;
    }

    .bbs-field-label {
        font-weight: bold;
        font-size: 10px;
        margin-bottom: 3px;
        color: #1e293b;
    }

    .bbs-textarea-input {
        width: 100%;
        border: 1px solid #94a3b8;
        border-radius: 2px;
        padding: 4px 6px;
        font-size: 11px;
        font-family: inherit;
        resize: vertical;
        background: #fff;
    }

    .bbs-textarea-input:focus {
        outline: 2px solid #2563eb;
        border-color: #2563eb;
    }

    .bbs-footer-section {
        border: 1px solid #000;
        padding: 8px;
        font-size: 10px;
        display: flex;
        flex-direction: column;
        gap: 6px;
        background: #fafafa;
    }

    .bbs-radio-inline {
        display: flex;
        gap: 12px;
        align-items: center;
        font-weight: normal;
    }

    .bbs-radio-inline label {
        display: flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
        font-size: 10px;
    }
</style>
@endsection

@section('content')
<form action="{{ route('karyawan.observasi.store') }}" method="POST" id="bbsForm">
    @csrf
    @error('item_states')
        <div role="alert" style="max-width:1100px;margin:0 auto 1rem;padding:.75rem 1rem;border:1px solid #fecaca;border-radius:8px;background:#fef2f2;color:#991b1b;font-weight:600;">{{ $message }}</div>
    @enderror

    <!-- Form Container Yang Sama Persis Dengan Dokumen Fisik Gambar 1 -->
    <div class="bbs-paper-form">

        <!-- 1. Header Dokumen KSO -->
        <table class="bbs-header-table">
            <tr>
                <td class="bbs-logo-cell">
                    KSO.PT.CATUR ELANG PERKASA - PT.SENTRA MULTIKARYA INFRASTRUKTUR
                </td>
                <td class="bbs-title-cell">
                    FORMULIR OBSERVASI BBS
                </td>
                <td class="bbs-meta-cell">
                    <table class="bbs-inner-meta">
                        <tr>
                            <td style="width: 35%;"><strong>Rev</strong></td>
                            <td>00</td>
                        </tr>
                        <tr>
                            <td><strong>Tgl Terbit</strong></td>
                            <td>01 Agustus 2024</td>
                        </tr>
                        <tr>
                            <td><strong>Hal</strong></td>
                            <td>1</td>
                        </tr>
                        <tr>
                            <td><strong>No. Info Doc</strong></td>
                            <td>KSO CEP SMI-F-HES-014</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- 2. Header Informasi Meta Subheader -->
        <div class="bbs-subhead-grid">
            <div>
                <strong style="white-space: nowrap;">Tanggal:</strong>
                <input type="date" name="tanggal" class="bbs-input-inline" value="{{ old('tanggal', date('Y-m-d')) }}" required>
            </div>
            <div>
                <strong style="white-space: nowrap;">Lokasi:</strong>
                <input type="text" name="area_observasi" id="lokasiInput" class="bbs-input-inline" placeholder="Contoh: Wellpad / Area Process..." value="{{ old('area_observasi', 'Wellpad') }}" oninput="syncLokasiText(this.value)" required>
            </div>
            <div style="justify-content: flex-end; font-weight:bold;">Hasil sistem: <span id="behaviorClassification" style="color:#64748b;">Menunggu checklist</span></div>

            <div>
                <strong style="white-space: nowrap;">Nama:</strong>
                <input type="text" class="bbs-input-inline" value="{{ Auth::user()->name }}" readonly style="background: #e2e8f0;">
            </div>
            <div>
                <strong style="white-space: nowrap;">Unit:</strong>
                <input type="text" class="bbs-input-inline" value="{{ Auth::user()->unit_departemen ?? 'Operasional' }}" readonly style="background: #e2e8f0;">
            </div>
            <div style="justify-content: flex-end; font-size:9px; color:#475569;">Klasifikasi mengikuti status checklist yang jumlahnya lebih banyak.</div>

            <div style="grid-column: span 3;">
                <strong style="white-space: nowrap;">Posisi / jabatan:</strong>
                <input type="text" class="bbs-input-inline" value="{{ Auth::user()->jabatan ?? 'Karyawan Lapangan' }}" readonly style="background: #e2e8f0;">
            </div>
        </div>

        <!-- Hidden input untuk kategori_id yang dipilih dari tabel -->
        <input type="hidden" name="kategori_id" id="selectedKategoriId" value="{{ old('kategori_id', $kategoriList->first()->id ?? 1) }}">

        <!-- 3. Layout Utama: Tabel Checklist 10 Kategori (Kiri) & Narasi (Kanan) -->
        <div class="bbs-main-layout">
            <!-- Kolom Tabel Checklist Item Perilaku -->
            <div>
                <div style="margin-bottom: 6px; display: flex; justify-content: space-between; align-items: center; background: #e2e8f0; padding: 5px 8px; border: 1px solid #000; border-bottom: none;">
                    <span style="font-weight: bold; font-size: 9.5px; color: #0f172a;">
                        Klik Tombol <span style="color:#16a34a; font-weight:bold;">[✓]</span> untuk Safe atau <span style="color:#dc2626; font-weight:bold;">[✗]</span> untuk At Risk pada setiap baris:
                    </span>
                    <div>
                        <button type="button" onclick="bulkSetState('safe')" style="background: #16a34a; color: #fff; border: none; padding: 2px 8px; border-radius: 3px; font-size: 9px; font-weight: bold; cursor: pointer;">✓ Set Semua Safe</button>
                        <button type="button" onclick="bulkSetState('')" style="background: #64748b; color: #fff; border: none; padding: 2px 8px; border-radius: 3px; font-size: 9px; font-weight: bold; cursor: pointer; margin-left: 4px;">✗ Clear Semua</button>
                    </div>
                </div>

                <table class="bbs-checklist-table">
                    <thead>
                        <tr>
                            <th style="width: 28px;">NO.</th>
                            <th>DESKRIPSI PERILAKU</th>
                            <th style="width: 65px;">SAFE / AT RISK</th>
                            <th style="width: 28px;">NO.</th>
                            <th>DESKRIPSI PERILAKU</th>
                            <th style="width: 65px;">SAFE / AT RISK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $catPosisi = $kategoriList->firstWhere(fn($k) => str_contains(strtolower($k->nama_kategori), 'posisi') || str_contains(strtolower($k->nama_kategori), 'ergonomi'))?->id ?? 1;
                            $catErgo = $kategoriList->firstWhere(fn($k) => str_contains(strtolower($k->nama_kategori), 'kantor') || str_contains(strtolower($k->nama_kategori), 'ergonomi'))?->id ?? $catPosisi;
                            $catPerkakas = $kategoriList->firstWhere(fn($k) => str_contains(strtolower($k->nama_kategori), 'perkakas') || str_contains(strtolower($k->nama_kategori), 'alat'))?->id ?? 1;
                            $catProsedur = $kategoriList->firstWhere(fn($k) => str_contains(strtolower($k->nama_kategori), 'prosedur') || str_contains(strtolower($k->nama_kategori), 'izin') || str_contains(strtolower($k->nama_kategori), 'jsa'))?->id ?? 1;
                            $catArea = $kategoriList->firstWhere(fn($k) => str_contains(strtolower($k->nama_kategori), 'housekeeping') || str_contains(strtolower($k->nama_kategori), 'kerapihan') || str_contains(strtolower($k->nama_kategori), 'area'))?->id ?? 1;
                            $catLingkungan = $kategoriList->firstWhere(fn($k) => str_contains(strtolower($k->nama_kategori), 'lingkungan') || str_contains(strtolower($k->nama_kategori), 'b3') || str_contains(strtolower($k->nama_kategori), 'tumpahan'))?->id ?? 1;
                            $catApd = $kategoriList->firstWhere(fn($k) => str_contains(strtolower($k->nama_kategori), 'apd') || str_contains(strtolower($k->nama_kategori), 'pelindung'))?->id ?? 1;
                            $catMengemudi = $kategoriList->firstWhere(fn($k) => str_contains(strtolower($k->nama_kategori), 'kemudi') || str_contains(strtolower($k->nama_kategori), 'mengemudi') || str_contains(strtolower($k->nama_kategori), 'transportasi'))?->id ?? 1;
                            $catLain = $kategoriList->last()->id ?? 1;

                            $renderCell = function($code, $desc, $katId, $isDefaultSafe = false) {
                                $codeId = str_replace('.', '_', $code);
                                $val = $isDefaultSafe ? 'safe' : '';
                                $safeActive = $isDefaultSafe ? 'active-safe' : '';
                                return "
                                <td class=\"bbs-checkbox-cell\" style=\"text-align:center; white-space:nowrap;\">
                                    <input type=\"hidden\" name=\"item_states[$code]\" id=\"state_$codeId\" class=\"bbs-item-state\" data-code=\"$code\" data-codeid=\"$codeId\" data-katid=\"$katId\" value=\"$val\">
                                    <div style=\"display:inline-flex; gap:4px; align-items:center;\">
                                        <button type=\"button\" id=\"btn_safe_$codeId\" class=\"bbs-btn-toggle $safeActive\" onclick=\"toggleItem('$code', 'safe', $katId)\" title=\"Klik Safe (✓)\">✓</button>
                                        <button type=\"button\" id=\"btn_risk_$codeId\" class=\"bbs-btn-toggle\" onclick=\"toggleItem('$code', 'at_risk', $katId)\" title=\"Klik At-Risk (✗)\">✗</button>
                                    </div>
                                </td>";
                            };
                        @endphp

                        <!-- Header 1 & 6 -->
                        <tr>
                            <td colspan="3" class="bbs-cat-header">1. Posisi Badan</td>
                            <td colspan="3" class="bbs-cat-header">6. Ergonomi Kantor (Lanjutan)</td>
                        </tr>
                        <tr class="bbs-item-row row-safe" id="row_1_1">
                            <td>1.1</td><td style="cursor:pointer;" onclick="toggleItem('1.1', 'safe', {{ $catPosisi }})">Menghindari Lintasan Bahaya (line of Fire)</td>{!! $renderCell('1.1', 'Menghindari Lintasan Bahaya', $catPosisi, true) !!}
                            <td>6.4</td><td style="cursor:pointer;" onclick="toggleItem('6.4', 'safe', {{ $catErgo }})">Penyangga Punggung</td>{!! $renderCell('6.4', 'Penyangga Punggung', $catErgo) !!}
                        </tr>
                        <tr class="bbs-item-row" id="row_1_2">
                            <td>1.2</td><td style="cursor:pointer;" onclick="toggleItem('1.2', 'safe', {{ $catPosisi }})">Berjalan / Bergerak dg Pandangan ke Arah Gerakan</td>{!! $renderCell('1.2', 'Berjalan dg Pandangan Arah Gerakan', $catPosisi) !!}
                            <td>6.5</td><td style="cursor:pointer;" onclick="toggleItem('6.5', 'safe', {{ $catErgo }})">Posisi Pundak</td>{!! $renderCell('6.5', 'Posisi Pundak', $catErgo) !!}
                        </tr>
                        <tr class="bbs-item-row" id="row_1_3">
                            <td>1.3</td><td style="cursor:pointer;" onclick="toggleItem('1.3', 'safe', {{ $catPosisi }})">Menjaga Pandangan Mata pada Pekerjaan</td>{!! $renderCell('1.3', 'Menjaga Pandangan Mata', $catPosisi) !!}
                            <td>6.6</td><td style="cursor:pointer;" onclick="toggleItem('6.6', 'safe', {{ $catErgo }})">Posisi Pergelangan Tangan dan Tangan</td>{!! $renderCell('6.6', 'Posisi Pergelangan Tangan', $catErgo) !!}
                        </tr>
                        <tr class="bbs-item-row" id="row_1_4">
                            <td>1.4</td><td style="cursor:pointer;" onclick="toggleItem('1.4', 'safe', {{ $catPosisi }})">Menjaga Badan di Luar Posisi Terjepit</td>{!! $renderCell('1.4', 'Menjaga Badan di Luar Terjepit', $catPosisi) !!}
                            <td>6.7</td><td style="cursor:pointer;" onclick="toggleItem('6.7', 'safe', {{ $catErgo }})">Memegang / Menggerakkan Mouse</td>{!! $renderCell('6.7', 'Memegang / Menggerakkan Mouse', $catErgo) !!}
                        </tr>
                        <tr class="bbs-item-row" id="row_1_5">
                            <td>1.5</td><td style="cursor:pointer;" onclick="toggleItem('1.5', 'safe', {{ $catPosisi }})">Menaiki / Menuruni Tangga/Fasilitas</td>{!! $renderCell('1.5', 'Menaiki / Menuruni Tangga', $catPosisi) !!}
                            <td>6.8</td><td style="cursor:pointer;" onclick="toggleItem('6.8', 'safe', {{ $catErgo }})">Mengenali dan Melaporkan Ketidaknyamanan</td>{!! $renderCell('6.8', 'Mengenali Ketidaknyamanan', $catErgo) !!}
                        </tr>

                        <!-- Header 2 & 7 -->
                        <tr>
                            <td colspan="3" class="bbs-cat-header">2. Menggunakan Badan</td>
                            <td colspan="3" class="bbs-cat-header">7. Pemeliharaan Lingkungan</td>
                        </tr>
                        <tr class="bbs-item-row" id="row_2_1">
                            <td>2.1</td><td style="cursor:pointer;" onclick="toggleItem('2.1', 'safe', {{ $catPosisi }})">Mengangkat / Menurunkan / Menekan / Menarik</td>{!! $renderCell('2.1', 'Mengangkat / Menurunkan / Menekan', $catPosisi) !!}
                            <td>7.1</td><td style="cursor:pointer;" onclick="toggleItem('7.1', 'safe', {{ $catLingkungan }})">Mencegah Tumpahan</td>{!! $renderCell('7.1', 'Mencegah Tumpahan', $catLingkungan) !!}
                        </tr>
                        <tr class="bbs-item-row" id="row_2_2">
                            <td>2.2</td><td style="cursor:pointer;" onclick="toggleItem('2.2', 'safe', {{ $catPosisi }})">Menghindar dari Gerakan Memuntir</td>{!! $renderCell('2.2', 'Menghindar Gerakan Memuntir', $catPosisi) !!}
                            <td>7.2</td><td style="cursor:pointer;" onclick="toggleItem('7.2', 'safe', {{ $catLingkungan }})">Persiapan Membersihkan Tumpahan</td>{!! $renderCell('7.2', 'Persiapan Membersihkan Tumpahan', $catLingkungan) !!}
                        </tr>
                        <tr class="bbs-item-row" id="row_2_3">
                            <td>2.3</td><td style="cursor:pointer;" onclick="toggleItem('2.3', 'safe', {{ $catPosisi }})">Menggapai / Meraih dalam Jangkauan Aman</td>{!! $renderCell('2.3', 'Menggapai / Meraih Jangkauan Aman', $catPosisi) !!}
                            <td>7.3</td><td style="cursor:pointer;" onclick="toggleItem('7.3', 'safe', {{ $catLingkungan }})">Menangani Limbah B3 / Non-B3</td>{!! $renderCell('7.3', 'Menangani Limbah B3', $catLingkungan) !!}
                        </tr>
                        <tr class="bbs-item-row" id="row_2_4">
                            <td>2.4</td><td style="cursor:pointer;" onclick="toggleItem('2.4', 'safe', {{ $catPosisi }})">Menanggapi Risiko Ergonomi Industri</td>{!! $renderCell('2.4', 'Menanggapi Risiko Ergonomi', $catPosisi) !!}
                            <td colspan="3" class="bbs-cat-header">8. Alat Pelindung Diri (APD)</td>
                        </tr>

                        <!-- Header 3 & APD -->
                        <tr>
                            <td colspan="3" class="bbs-cat-header">3. Perkakas dan Peralatan</td>
                            <td>8.1</td><td style="cursor:pointer;" onclick="toggleItem('8.1', 'safe', {{ $catApd }})">Pelindung Kepala (Safety Helmet)</td>{!! $renderCell('8.1', 'Pelindung Kepala', $catApd) !!}
                        </tr>
                        <tr class="bbs-item-row" id="row_3_1">
                            <td>3.1</td><td style="cursor:pointer;" onclick="toggleItem('3.1', 'safe', {{ $catPerkakas }})">Memilih dan Menggunakan Perkakas / Peralatan</td>{!! $renderCell('3.1', 'Memilih dan Menggunakan Perkakas', $catPerkakas) !!}
                            <td>8.2</td><td style="cursor:pointer;" onclick="toggleItem('8.2', 'safe', {{ $catApd }})">Pelindung Mata dan Wajah (Glasses/Shield)</td>{!! $renderCell('8.2', 'Pelindung Mata dan Wajah', $catApd) !!}
                        </tr>
                        <tr class="bbs-item-row" id="row_3_2">
                            <td>3.2</td><td style="cursor:pointer;" onclick="toggleItem('3.2', 'safe', {{ $catPerkakas }})">Menggunakan Pelindung / Barikade / Peringatan</td>{!! $renderCell('3.2', 'Menggunakan Pelindung / Barikade', $catPerkakas) !!}
                            <td>8.3</td><td style="cursor:pointer;" onclick="toggleItem('8.3', 'safe', {{ $catApd }})">Pelindung Pendengaran (Earplug/Muff)</td>{!! $renderCell('8.3', 'Pelindung Pendengaran', $catApd) !!}
                        </tr>

                        <!-- Header 4 & APD Lanjutan -->
                        <tr>
                            <td colspan="3" class="bbs-cat-header">4. Prosedur</td>
                            <td>8.4</td><td style="cursor:pointer;" onclick="toggleItem('8.4', 'safe', {{ $catApd }})">Pelindung Pernafasan (Respirator)</td>{!! $renderCell('8.4', 'Pelindung Pernafasan', $catApd) !!}
                        </tr>
                        <tr class="bbs-item-row" id="row_4_1">
                            <td>4.1</td><td style="cursor:pointer;" onclick="toggleItem('4.1', 'safe', {{ $catProsedur }})">Persiapan Kerja dan JSA (Job Safety Analysis)</td>{!! $renderCell('4.1', 'Persiapan Kerja dan JSA', $catProsedur) !!}
                            <td>8.5</td><td style="cursor:pointer;" onclick="toggleItem('8.5', 'safe', {{ $catApd }})">Pelindung Tangan dan Lengan (Gloves)</td>{!! $renderCell('8.5', 'Pelindung Tangan dan Lengan', $catApd) !!}
                        </tr>
                        <tr class="bbs-item-row" id="row_4_2">
                            <td>4.2</td><td style="cursor:pointer;" onclick="toggleItem('4.2', 'safe', {{ $catProsedur }})">Mengikuti Prosedur Kerja / SOP</td>{!! $renderCell('4.2', 'Mengikuti Prosedur Kerja SOP', $catProsedur) !!}
                            <td>8.6</td><td style="cursor:pointer;" onclick="toggleItem('8.6', 'safe', {{ $catApd }})">Pelindung Jatuh (Full Body Harness)</td>{!! $renderCell('8.6', 'Pelindung Jatuh Harness', $catApd) !!}
                        </tr>
                        <tr class="bbs-item-row" id="row_4_3">
                            <td>4.3</td><td style="cursor:pointer;" onclick="toggleItem('4.3', 'safe', {{ $catProsedur }})">Mengisolasi Energi (Lock-Out / Tag-Out)</td>{!! $renderCell('4.3', 'Mengisolasi Energi LOTO', $catProsedur) !!}
                            <td>8.7</td><td style="cursor:pointer;" onclick="toggleItem('8.7', 'safe', {{ $catApd }})">Pakaian Pelindung (Wearpack)</td>{!! $renderCell('8.7', 'Pakaian Pelindung Wearpack', $catApd) !!}
                        </tr>
                        <tr class="bbs-item-row" id="row_4_4">
                            <td>4.4</td><td style="cursor:pointer;" onclick="toggleItem('4.4', 'safe', {{ $catProsedur }})">Mengerjakan Hot-Work sesuai izin kerja</td>{!! $renderCell('4.4', 'Mengerjakan Hot-Work', $catProsedur) !!}
                            <td>8.8</td><td style="cursor:pointer;" onclick="toggleItem('8.8', 'safe', {{ $catApd }})">Pelindung Kaki (Safety Shoes)</td>{!! $renderCell('8.8', 'Pelindung Kaki Safety Shoes', $catApd) !!}
                        </tr>
                        <tr class="bbs-item-row" id="row_4_5">
                            <td>4.5</td><td style="cursor:pointer;" onclick="toggleItem('4.5', 'safe', {{ $catProsedur }})">Memasuki Ruang Terbatas (Confined Space)</td>{!! $renderCell('4.5', 'Memasuki Ruang Terbatas', $catProsedur) !!}
                            <td colspan="3" class="bbs-cat-header">9. Mengemudi</td>
                        </tr>
                        <tr class="bbs-item-row" id="row_4_7">
                            <td>4.7</td><td style="cursor:pointer;" onclick="toggleItem('4.7', 'safe', {{ $catProsedur }})">Komunikasi antar Rekan Kerja</td>{!! $renderCell('4.7', 'Komunikasi antar Rekan Kerja', $catProsedur) !!}
                            <td>9.1</td><td style="cursor:pointer;" onclick="toggleItem('9.1', 'safe', {{ $catMengemudi }})">Perencanaan Perjalanan & Inspection</td>{!! $renderCell('9.1', 'Perencanaan Perjalanan', $catMengemudi) !!}
                        </tr>

                        <!-- Header 5 & Mengemudi Lanjutan -->
                        <tr>
                            <td colspan="3" class="bbs-cat-header">5. Area Kerja</td>
                            <td>9.2</td><td style="cursor:pointer;" onclick="toggleItem('9.2', 'safe', {{ $catMengemudi }})">Pre-Trip Inspection & Sabuk Safety</td>{!! $renderCell('9.2', 'Pre-Trip Inspection', $catMengemudi) !!}
                        </tr>
                        <tr class="bbs-item-row" id="row_5_1">
                            <td>5.1</td><td style="cursor:pointer;" onclick="toggleItem('5.1', 'safe', {{ $catArea }})">Bekerja pada Posisi Stabil</td>{!! $renderCell('5.1', 'Bekerja pada Posisi Stabil', $catArea) !!}
                            <td>9.3</td><td style="cursor:pointer;" onclick="toggleItem('9.3', 'safe', {{ $catMengemudi }})">Menyesuaikan Kecepatan Kendaraan</td>{!! $renderCell('9.3', 'Menyesuaikan Kecepatan', $catMengemudi) !!}
                        </tr>
                        <tr class="bbs-item-row" id="row_5_2">
                            <td>5.2</td><td style="cursor:pointer;" onclick="toggleItem('5.2', 'safe', {{ $catArea }})">Pengaturan Area Kerja / Housekeeping</td>{!! $renderCell('5.2', 'Housekeeping Area Kerja', $catArea) !!}
                            <td>9.4</td><td style="cursor:pointer;" onclick="toggleItem('9.4', 'safe', {{ $catMengemudi }})">Menjaga Jarak Iring Amannya</td>{!! $renderCell('9.4', 'Menjaga Jarak Iring', $catMengemudi) !!}
                        </tr>
                        <tr class="bbs-item-row" id="row_5_3">
                            <td>5.3</td><td style="cursor:pointer;" onclick="toggleItem('5.3', 'safe', {{ $catArea }})">Bekerja di Lingkungan dg Cahaya Cukup</td>{!! $renderCell('5.3', 'Bekerja dg Cahaya Cukup', $catArea) !!}
                            <td>9.8</td><td style="cursor:pointer;" onclick="toggleItem('9.8', 'safe', {{ $catMengemudi }})">Memundurkan Kendaraan dg Panduan</td>{!! $renderCell('9.8', 'Memundurkan Kendaraan', $catMengemudi) !!}
                        </tr>

                        <!-- Header 6 & Observasi Lainnya -->
                        <tr>
                            <td colspan="3" class="bbs-cat-header">6. Ergonomi Kantor</td>
                            <td colspan="3" class="bbs-cat-header">10. Observasi Perilaku Lainnya</td>
                        </tr>
                        <tr class="bbs-item-row" id="row_6_1">
                            <td>6.1</td><td style="cursor:pointer;" onclick="toggleItem('6.1', 'safe', {{ $catErgo }})">Melakukan Istirahat Berkala</td>{!! $renderCell('6.1', 'Melakukan Istirahat Berkala', $catErgo) !!}
                            <td>10.1</td><td style="cursor:pointer;" onclick="toggleItem('10.1', 'safe', {{ $catLain }})">Lain-lain</td>{!! $renderCell('10.1', 'Observasi Lain-lain', $catLain) !!}
                        </tr>
                        <tr id="observasiLainnyaRow" style="display:none;">
                            <td colspan="3"></td>
                            <td colspan="3">
                                <label for="observasiLainnyaInput"><strong>Keterangan Lain-lain:</strong></label>
                                <textarea id="observasiLainnyaInput" name="observasi_lainnya" class="bbs-textarea-input" rows="2" placeholder="Jelaskan observasi perilaku lainnya...">{{ old('observasi_lainnya') }}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Kolom Kanan: Detail Field Input Sesuai Dokumen Gambar 1 -->
            <div class="bbs-right-panel">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1.5px solid #000; padding-bottom: 6px;">
                    <div><strong>Observasi Perilaku:</strong></div>
                    <strong id="behaviorClassificationPanel" style="color:#64748b;">Menunggu checklist</strong>
                </div>

                <div class="bbs-field-box">
                    <div class="bbs-field-label">Area Observasi :</div>
                    <input type="text" id="areaObservasiDisplay" class="bbs-textarea-input" placeholder="Contoh: Wellpad / Area Process..." value="{{ old('area_observasi', 'Wellpad') }}" oninput="syncLokasiText(this.value)" style="background: #fff; font-weight: bold; color: #0f172a;">
                </div>

                <div class="bbs-field-box" style="background: #fef08a;">
                    <div class="bbs-field-label">Nomor Perilaku (lihat tabel disamping) :</div>
                    <input type="text" id="nomorPerilakuInput" class="bbs-textarea-input" value="1.1" readonly style="font-weight: bold; background: #fff;">
                </div>

                <div class="bbs-field-box">
                    <div class="bbs-field-label">Ketika: (Jelaskan aktivitas yang dilakukan)</div>
                    <textarea name="ketika" class="bbs-textarea-input" rows="3" placeholder="Contoh: Pada saat memandu Foco truck parkir...">{{ old('ketika') }}</textarea>
                </div>

                <div class="bbs-field-box" style="border-left: 3px solid #dc2626;">
                    <div class="bbs-field-label" style="color: #b91c1c;">Ditemukan: (Jelaskan perilaku beresiko) *</div>
                    <textarea name="ditemukan" class="bbs-textarea-input" rows="3" placeholder="Contoh: Op menggunakan light stick / Pekerja tidak memakai kacamata safety..." required>{{ old('ditemukan') }}</textarea>
                </div>

                <div class="bbs-field-box">
                    <div class="bbs-field-label">Sebab: (Alasan mengapa perilaku beresiko dilakukan)</div>
                    <textarea name="sebab" class="bbs-textarea-input" rows="2" placeholder="Contoh: Sebagai penanda signal agar selalu terlihat jelas oleh driver...">{{ old('sebab') }}</textarea>
                </div>

                <div class="bbs-field-box" style="border-left: 3px solid #16a34a;">
                    <div class="bbs-field-label" style="color: #15803d;">Saran: (Solusi agar perilaku beresiko tidak berulang) / Mencoba:</div>
                    <textarea name="saran" class="bbs-textarea-input" rows="2" placeholder="Contoh: Pastikan bekerja di area pencahayaan yang baik...">{{ old('saran') }}</textarea>
                </div>

                <!-- Ceklis Pertanyaan Evaluasi Bawah -->
                <div class="bbs-footer-section">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Setuju perilaku terjadi?</span>
                        <div class="bbs-radio-inline">
                            <label><input type="radio" name="setuju_terjadi" value="ya" checked> Ya</label>
                            <label><input type="radio" name="setuju_terjadi" value="tidak"> Tidak</label>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Setuju perilaku berisiko?</span>
                        <div class="bbs-radio-inline">
                            <label><input type="radio" name="setuju_berisiko" value="ya" checked> Ya</label>
                            <label><input type="radio" name="setuju_berisiko" value="tidak"> Tidak</label>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Perilaku selamat:</span>
                        <div class="bbs-radio-inline">
                            <label><input type="radio" name="perilaku_selamat" value="mudah" checked> Mudah</label>
                            <label><input type="radio" name="perilaku_selamat" value="sukar"> Sukar</label>
                            <label><input type="radio" name="perilaku_selamat" value="tidak_mungkin"> Tdk Mungkin</label>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Tindak lanjut Steering Committee?</span>
                        <div class="bbs-radio-inline">
                            <label><input type="radio" name="tindak_lanjut_sc" value="ya"> Ya</label>
                            <label><input type="radio" name="tindak_lanjut_sc" value="tidak" checked> Tidak</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 12px; text-align: center; font-size: 8.5px; font-style: italic; color: #64748b;">
            Document Uncontrolled When Printed. Dokumen Tidak Terkendali Apabila dicetak.
        </div>
    </div>

    <!-- Submit buttons -->
    <div style="max-width: 1150px; margin: 0 auto 3rem auto; display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ route('karyawan.index') }}" class="btn btn-secondary">← Batal</a>
        <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight: 800; font-size: 0.95rem;">
            🚀 Simpan & Kirim Formulir BBS
        </button>
    </div>
</form>

<script>
    function toggleItem(code, state, katId) {
        const codeId = code.toString().replace('.', '_');
        const input = document.getElementById('state_' + codeId);
        const btnSafe = document.getElementById('btn_safe_' + codeId);
        const btnRisk = document.getElementById('btn_risk_' + codeId);
        const row = document.getElementById('row_' + codeId);

        if (!input) return;

        if (input.value === state) {
            // Toggle OFF
            input.value = '';
            if (btnSafe) btnSafe.classList.remove('active-safe');
            if (btnRisk) btnRisk.classList.remove('active-risk');
            if (row) row.classList.remove('row-safe', 'row-risk');
        } else {
            // Toggle ON
            input.value = state;
            if (state === 'safe') {
                if (btnSafe) btnSafe.classList.add('active-safe');
                if (btnRisk) btnRisk.classList.remove('active-risk');
                if (row) {
                    row.classList.add('row-safe');
                    row.classList.remove('row-risk');
                }
            } else if (state === 'at_risk') {
                if (btnRisk) btnRisk.classList.add('active-risk');
                if (btnSafe) btnSafe.classList.remove('active-safe');
                if (row) {
                    row.classList.add('row-risk');
                    row.classList.remove('row-safe');
                }
            }
        }

        updateSummary();
        updateObservasiLainnyaVisibility();
    }

    function updateObservasiLainnyaVisibility() {
        const state = document.querySelector('.bbs-item-state[data-code="10.1"]');
        const row = document.getElementById('observasiLainnyaRow');
        if (row) row.style.display = state && state.value ? '' : 'none';
    }

    function updateSummary() {
        const allInputs = document.querySelectorAll('.bbs-item-state');
        const safeCodes = [];
        const riskCodes = [];
        let firstKatId = null;

        allInputs.forEach(inp => {
            if (inp.value === 'safe') {
                safeCodes.push(inp.getAttribute('data-code'));
                if (!firstKatId) firstKatId = inp.getAttribute('data-katid');
            } else if (inp.value === 'at_risk') {
                riskCodes.push(inp.getAttribute('data-code') + ' (✗ Risk)');
                if (!firstKatId) firstKatId = inp.getAttribute('data-katid');
            }
        });

        const displayInput = document.getElementById('nomorPerilakuInput');
        const katInput = document.getElementById('selectedKategoriId');

        const totalCount = safeCodes.length + riskCodes.length;
        const behavior = totalCount === 0 ? null : (riskCodes.length >= safeCodes.length ? 'at_risk' : 'safe');
        const behaviorLabel = behavior === 'at_risk' ? 'AT-RISK' : (behavior === 'safe' ? 'SAFE' : 'Menunggu checklist');
        const behaviorColor = behavior === 'at_risk' ? '#dc2626' : (behavior === 'safe' ? '#16a34a' : '#64748b');
        ['behaviorClassification', 'behaviorClassificationPanel'].forEach(id => {
            const el = document.getElementById(id);
            if (el) { el.textContent = behaviorLabel; el.style.color = behaviorColor; }
        });
        if (totalCount > 0) {
            let summaryText = '';
            if (riskCodes.length > 0) {
                summaryText = 'At-Risk: ' + riskCodes.join(', ');
                if (safeCodes.length > 0) {
                    summaryText += ' | Safe: ' + safeCodes.length + ' item';
                }
            } else {
                if (safeCodes.length > 6) {
                    summaryText = safeCodes.length + ' Item Safe Terpilih (' + safeCodes.slice(0, 5).join(', ') + ', ...)';
                } else {
                    summaryText = 'Safe: ' + safeCodes.join(', ');
                }
            }
            displayInput.value = summaryText;
        } else {
            displayInput.value = '- Belum ada item dipilih -';
        }

        if (firstKatId && katInput) {
            katInput.value = firstKatId;
        }
    }

    function bulkSetState(state) {
        document.querySelectorAll('.bbs-item-state').forEach(inp => {
            const code = inp.getAttribute('data-code');
            const katId = inp.getAttribute('data-katid');
            const codeId = code.toString().replace('.', '_');
            const btnSafe = document.getElementById('btn_safe_' + codeId);
            const btnRisk = document.getElementById('btn_risk_' + codeId);
            const row = document.getElementById('row_' + codeId);

            inp.value = state;
            if (state === 'safe') {
                if (btnSafe) btnSafe.classList.add('active-safe');
                if (btnRisk) btnRisk.classList.remove('active-risk');
                if (row) {
                    row.classList.add('row-safe');
                    row.classList.remove('row-risk');
                }
            } else if (state === 'at_risk') {
                if (btnRisk) btnRisk.classList.add('active-risk');
                if (btnSafe) btnSafe.classList.remove('active-safe');
                if (row) {
                    row.classList.add('row-risk');
                    row.classList.remove('row-safe');
                }
            } else {
                if (btnSafe) btnSafe.classList.remove('active-safe');
                if (btnRisk) btnRisk.classList.remove('active-risk');
                if (row) row.classList.remove('row-safe', 'row-risk');
            }
        });
        updateSummary();
    }

    // Two-way sync for Lokasi and Area Observasi fields
    function syncLokasiText(val) {
        const inp1 = document.getElementById('lokasiInput');
        const inp2 = document.getElementById('areaObservasiDisplay');
        if (inp1 && inp1.value !== val) inp1.value = val;
        if (inp2 && inp2.value !== val) inp2.value = val;
    }

    // Run initial update on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateSummary();
        updateObservasiLainnyaVisibility();
    });
</script>
@endsection
