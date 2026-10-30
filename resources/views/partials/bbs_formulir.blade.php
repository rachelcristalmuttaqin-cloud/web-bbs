<style>
    .bbs-paper {
        background: #ffffff;
        color: #000000;
        font-family: 'Arial', sans-serif;
        font-size: 11px;
        line-height: 1.3;
        border: 2px solid #000;
        padding: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border-radius: 4px;
        max-width: 1100px;
        margin: 0 auto;
    }

    /* === RESPONSIVE WRAPPER === */
    .bbs-paper-scroll-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .bbs-paper-scroll-wrapper .bbs-paper {
        min-width: 820px; /* Pastikan form tidak rusak di HP */
    }

    .bbs-header-table {
        width: 100%;
        border-collapse: collapse;
        border: 2px solid #000;
        margin-bottom: 8px;
    }

    .bbs-header-table td {
        border: 1px solid #000;
        padding: 6px 8px;
        vertical-align: middle;
    }

    .bbs-logo-cell {
        width: 32%;
        font-size: 10px;
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

    .bbs-inner-meta tr:last-child td {
        border-bottom: none;
    }

    .bbs-inner-meta td:last-child {
        border-right: none;
    }

    .bbs-subhead-grid {
        display: grid;
        grid-template-columns: 2.5fr 2fr 1fr;
        gap: 6px 16px;
        border: 1px solid #000;
        padding: 8px 12px;
        margin-bottom: 8px;
        background: #fdfdfd;
        font-size: 11px;
    }

    .bbs-subhead-grid div {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .bbs-main-layout {
        display: grid;
        grid-template-columns: 62% 38%;
        gap: 8px;
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
        padding: 2px 4px;
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
        padding: 3px 6px !important;
        font-size: 10px;
    }

    /* Status cell per-item: safe=hijau, at_risk=merah, kosong=putih */
    .bbs-status-safe {
        background: #dcfce7 !important;
        text-align: center;
        color: #15803d;
        font-weight: bold;
    }

    .bbs-status-risk {
        background: #fee2e2 !important;
        text-align: center;
        color: #b91c1c;
        font-weight: bold;
    }

    /* Row highlight jika item dipilih */
    .bbs-row-safe-item {
        background: #f0fdf4;
    }

    .bbs-row-risk-item {
        background: #fef2f2;
        font-weight: bold;
    }

    .bbs-right-panel {
        border: 1.5px solid #000;
        padding: 8px;
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

    .bbs-field-value {
        font-size: 11px;
        color: #0f172a;
        min-height: 20px;
        word-break: break-word;
    }

    .bbs-footer-section {
        border: 1px solid #000;
        padding: 6px 8px;
        font-size: 10px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .bbs-watermark {
        margin-top: 8px;
        text-align: center;
        font-size: 8.5px;
        font-style: italic;
        color: #64748b;
    }

    @media print {
        body * {
            visibility: hidden;
        }
        .bbs-paper, .bbs-paper * {
            visibility: visible;
        }
        .bbs-paper {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none;
            box-shadow: none;
            padding: 0;
            min-width: unset !important;
        }
        .bbs-paper-scroll-wrapper {
            overflow: visible;
        }
        .no-print {
            display: none !important;
        }
    }
</style>

@php
    /* ============================================================
     * Decode item_states JSON dari database.
     * item_states adalah array asosiatif: ['1.1' => 'safe', '1.2' => 'at_risk', ...]
     * Fallback ke empty array jika null (data lama sebelum fitur ini)
     * ============================================================ */
    $states = is_array($observasi->item_states) ? $observasi->item_states : [];

    /**
     * Helper: return CSS class untuk row berdasarkan state item.
     */
    $rowClass = function(string $code) use ($states): string {
        $state = $states[$code] ?? '';
        if ($state === 'safe') return 'bbs-row-safe-item';
        if ($state === 'at_risk') return 'bbs-row-risk-item';
        return '';
    };

    /**
     * Helper: return simbol + CSS class untuk cell STATUS.
     * Return: ['symbol' => '✓'|'✗'|'', 'class' => 'bbs-status-safe'|'bbs-status-risk'|'']
     */
    $statusCell = function(string $code) use ($states): array {
        $state = $states[$code] ?? '';
        if ($state === 'safe')    return ['symbol' => '✓', 'class' => 'bbs-status-safe'];
        if ($state === 'at_risk') return ['symbol' => '✗', 'class' => 'bbs-status-risk'];
        return ['symbol' => '', 'class' => ''];
    };
@endphp

<div class="bbs-paper-scroll-wrapper">
<div class="bbs-paper">
    <!-- Header Dokumen Resmi -->
    <table class="bbs-header-table">
        <tr>
            <td class="bbs-logo-cell">
                <div style="font-size: 9px; line-height: 1.2;">
                    KSO.PT.CATUR ELANG PERKASA - PT.SENTRA MULTIKARYA INFRASTRUKTUR
                </div>
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

    <!-- Header Informasi Meta Observasi -->
    <div class="bbs-subhead-grid">
        <div><strong>Tanggal:</strong> <span>{{ date('d F Y', strtotime($observasi->tanggal)) }}</span></div>
        <div><strong>Lokasi:</strong> <span>{{ $observasi->lokasi->nama_lokasi ?? '-' }}</span></div>
        <div>
            <strong>Safe:</strong>
            <span style="font-size: 13px; font-weight: bold; color: {{ $observasi->jenis_perilaku === 'safe' ? '#16a34a' : '#aaa' }};">
                {{ $observasi->jenis_perilaku === 'safe' ? '✓' : '—' }}
            </span>
        </div>

        <div><strong>Nama:</strong> <span>{{ $observasi->user->name ?? '-' }}</span></div>
        <div><strong>Unit:</strong> <span>{{ $observasi->unit_departemen ?? 'Operasional' }}</span></div>
        <div>
            <strong>At Risk:</strong>
            <span style="font-size: 13px; font-weight: bold; color: {{ $observasi->jenis_perilaku === 'at_risk' ? '#dc2626' : '#aaa' }};">
                {{ $observasi->jenis_perilaku === 'at_risk' ? '✗' : '—' }}
            </span>
        </div>

        <div style="grid-column: span 3;">
            <strong>Posisi / jabatan:</strong> <span>{{ $observasi->user->jabatan ?? 'Karyawan Lapangan' }}</span>
        </div>
    </div>

    <!-- Layout Utama: Tabel Checklist (Kiri) & Narasi (Kanan) -->
    <div class="bbs-main-layout">
        <!-- Tabel Checklist 10 Kategori -->
        <div>
            <table class="bbs-checklist-table">
                <thead>
                    <tr>
                        <th style="width: 25px;">No.</th>
                        <th>Deskripsi Perilaku</th>
                        <th style="width: 40px;">Status</th>
                        <th style="width: 25px;">No.</th>
                        <th>Deskripsi Perilaku</th>
                        <th style="width: 40px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- === 1. Posisi Badan | 6. Ergonomi Kantor (Lanjutan) === -->
                    <tr>
                        <td colspan="3" class="bbs-cat-header">1. Posisi Badan</td>
                        <td colspan="3" class="bbs-cat-header">6. Ergonomi Kantor (Lanjutan)</td>
                    </tr>
                    @foreach([
                        ['1.1','Menghindari Lintasan Bahaya (line of Fire)', '6.4','Penyangga Punggung'],
                        ['1.2','Berjalan / Bergerak dg Pandangan ke Arah Gerakan', '6.5','Posisi Pundak'],
                        ['1.3','Menjaga Pandangan Mata pada Pekerjaan', '6.6','Posisi Pergelangan Tangan dan Tangan'],
                        ['1.4','Menjaga Badan di Luar Posisi Terjepit', '6.7','Memegang / Menggerakkan Mouse'],
                        ['1.5','Menaiki / Menuruni Tangga/Fasilitas', '6.8','Mengenali dan Melaporkan Ketidaknyamanan'],
                    ] as [$cL, $dL, $cR, $dR])
                    @php $sL = $statusCell($cL); $sR = $statusCell($cR); @endphp
                    <tr class="{{ $rowClass($cL) ?: $rowClass($cR) }}">
                        <td>{{ $cL }}</td><td>{{ $dL }}</td>
                        <td class="{{ $sL['class'] }}" style="text-align:center;">{{ $sL['symbol'] }}</td>
                        <td>{{ $cR }}</td><td>{{ $dR }}</td>
                        <td class="{{ $sR['class'] }}" style="text-align:center;">{{ $sR['symbol'] }}</td>
                    </tr>
                    @endforeach

                    <!-- === 2. Menggunakan Badan | 7. Pemeliharaan Lingkungan === -->
                    <tr>
                        <td colspan="3" class="bbs-cat-header">2. Menggunakan Badan</td>
                        <td colspan="3" class="bbs-cat-header">7. Pemeliharaan Lingkungan</td>
                    </tr>
                    @foreach([
                        ['2.1','Mengangkat / Menurunkan / Menekan / Menarik', '7.1','Mencegah Tumpahan'],
                        ['2.2','Menghindar dari Gerakan Memuntir', '7.2','Persiapan Membersihkan Tumpahan'],
                        ['2.3','Menggapai / Meraih dalam Jangkauan Aman', '7.3','Menangani Limbah B3 / Non-B3'],
                    ] as [$cL, $dL, $cR, $dR])
                    @php $sL = $statusCell($cL); $sR = $statusCell($cR); @endphp
                    <tr class="{{ $rowClass($cL) ?: $rowClass($cR) }}">
                        <td>{{ $cL }}</td><td>{{ $dL }}</td>
                        <td class="{{ $sL['class'] }}" style="text-align:center;">{{ $sL['symbol'] }}</td>
                        <td>{{ $cR }}</td><td>{{ $dR }}</td>
                        <td class="{{ $sR['class'] }}" style="text-align:center;">{{ $sR['symbol'] }}</td>
                    </tr>
                    @endforeach
                    @php $s24 = $statusCell('2.4'); @endphp
                    <tr class="{{ $rowClass('2.4') }}">
                        <td>2.4</td><td>Menanggapi Risiko Ergonomi Industri</td>
                        <td class="{{ $s24['class'] }}" style="text-align:center;">{{ $s24['symbol'] }}</td>
                        <td colspan="3" class="bbs-cat-header">8. Alat Pelindung Diri (APD)</td>
                    </tr>

                    <!-- === 3. Perkakas & Peralatan | 8. APD === -->
                    @php $s81 = $statusCell('8.1'); @endphp
                    <tr>
                        <td colspan="3" class="bbs-cat-header">3. Perkakas dan Peralatan</td>
                        <td>8.1</td><td>Pelindung Kepala (Safety Helmet)</td>
                        <td class="{{ $s81['class'] }}" style="text-align:center;">{{ $s81['symbol'] }}</td>
                    </tr>
                    @foreach([
                        ['3.1','Memilih dan Menggunakan Perkakas / Peralatan', '8.2','Pelindung Mata dan Wajah (Glasses/Shield)'],
                        ['3.2','Menggunakan Pelindung / Barikade / Alat Peringatan', '8.3','Pelindung Pendengaran (Earplug/Muff)'],
                    ] as [$cL, $dL, $cR, $dR])
                    @php $sL = $statusCell($cL); $sR = $statusCell($cR); @endphp
                    <tr class="{{ $rowClass($cL) ?: $rowClass($cR) }}">
                        <td>{{ $cL }}</td><td>{{ $dL }}</td>
                        <td class="{{ $sL['class'] }}" style="text-align:center;">{{ $sL['symbol'] }}</td>
                        <td>{{ $cR }}</td><td>{{ $dR }}</td>
                        <td class="{{ $sR['class'] }}" style="text-align:center;">{{ $sR['symbol'] }}</td>
                    </tr>
                    @endforeach

                    <!-- === 4. Prosedur | 8. APD Lanjutan === -->
                    @php $s84 = $statusCell('8.4'); @endphp
                    <tr>
                        <td colspan="3" class="bbs-cat-header">4. Prosedur</td>
                        <td>8.4</td><td>Pelindung Pernafasan (Masker/Respirator)</td>
                        <td class="{{ $s84['class'] }}" style="text-align:center;">{{ $s84['symbol'] }}</td>
                    </tr>
                    @foreach([
                        ['4.1','Persiapan Kerja dan JSA (Job Safety Analysis)', '8.5','Pelindung Tangan dan Lengan (Gloves)'],
                        ['4.2','Mengikuti Prosedur Kerja / SOP', '8.6','Pelindung Jatuh (Full Body Harness)'],
                        ['4.3','Mengisolasi Energi (Lock-Out / Tag-Out)', '8.7','Pakaian Pelindung yang Sesuai (Wearpack)'],
                        ['4.4','Mengerjakan Hot-Work sesuai izin kerja', '8.8','Pelindung Kaki (Safety Shoes)'],
                    ] as [$cL, $dL, $cR, $dR])
                    @php $sL = $statusCell($cL); $sR = $statusCell($cR); @endphp
                    <tr class="{{ $rowClass($cL) ?: $rowClass($cR) }}">
                        <td>{{ $cL }}</td><td>{{ $dL }}</td>
                        <td class="{{ $sL['class'] }}" style="text-align:center;">{{ $sL['symbol'] }}</td>
                        <td>{{ $cR }}</td><td>{{ $dR }}</td>
                        <td class="{{ $sR['class'] }}" style="text-align:center;">{{ $sR['symbol'] }}</td>
                    </tr>
                    @endforeach
                    @php $s45 = $statusCell('4.5'); @endphp
                    <tr class="{{ $rowClass('4.5') }}">
                        <td>4.5</td><td>Memasuki Ruang Terbatas (Confined Space)</td>
                        <td class="{{ $s45['class'] }}" style="text-align:center;">{{ $s45['symbol'] }}</td>
                        <td colspan="3" class="bbs-cat-header">9. Mengemudi</td>
                    </tr>
                    @php $s47 = $statusCell('4.7'); $s91 = $statusCell('9.1'); @endphp
                    <tr class="{{ $rowClass('4.7') ?: $rowClass('9.1') }}">
                        <td>4.7</td><td>Komunikasi antar Rekan Kerja</td>
                        <td class="{{ $s47['class'] }}" style="text-align:center;">{{ $s47['symbol'] }}</td>
                        <td>9.1</td><td>Perencanaan Perjalanan & Inspection</td>
                        <td class="{{ $s91['class'] }}" style="text-align:center;">{{ $s91['symbol'] }}</td>
                    </tr>

                    <!-- === 5. Area Kerja | 9. Mengemudi Lanjutan === -->
                    @php $s92 = $statusCell('9.2'); @endphp
                    <tr>
                        <td colspan="3" class="bbs-cat-header">5. Area Kerja</td>
                        <td>9.2</td><td>Pre-Trip Inspection & Sabuk Keselamatan</td>
                        <td class="{{ $s92['class'] }}" style="text-align:center;">{{ $s92['symbol'] }}</td>
                    </tr>
                    @foreach([
                        ['5.1','Bekerja pada Posisi Stabil', '9.3','Menyesuaikan Kecepatan Kendaraan'],
                        ['5.2','Pengaturan Area Kerja / Housekeeping', '9.4','Menjaga Jarak Iring Amannya'],
                        ['5.3','Bekerja di Lingkungan dg Cahaya Cukup', '9.8','Memundurkan Kendaraan dg Panduan'],
                    ] as [$cL, $dL, $cR, $dR])
                    @php $sL = $statusCell($cL); $sR = $statusCell($cR); @endphp
                    <tr class="{{ $rowClass($cL) ?: $rowClass($cR) }}">
                        <td>{{ $cL }}</td><td>{{ $dL }}</td>
                        <td class="{{ $sL['class'] }}" style="text-align:center;">{{ $sL['symbol'] }}</td>
                        <td>{{ $cR }}</td><td>{{ $dR }}</td>
                        <td class="{{ $sR['class'] }}" style="text-align:center;">{{ $sR['symbol'] }}</td>
                    </tr>
                    @endforeach

                    <!-- === 6. Ergonomi Kantor | 10. Observasi Perilaku Lainnya === -->
                    <tr>
                        <td colspan="3" class="bbs-cat-header">6. Ergonomi Kantor</td>
                        <td colspan="3" class="bbs-cat-header">10. Observasi Perilaku Lainnya</td>
                    </tr>
                    @php $s61 = $statusCell('6.1'); $s101 = $statusCell('10.1'); @endphp
                    <tr class="{{ $rowClass('6.1') ?: $rowClass('10.1') }}">
                        <td>6.1</td><td>Melakukan Istirahat Berkala</td>
                        <td class="{{ $s61['class'] }}" style="text-align:center;">{{ $s61['symbol'] }}</td>
                        <td>10.1</td><td>Lain-lain</td>
                        <td class="{{ $s101['class'] }}" style="text-align:center;">{{ $s101['symbol'] }}</td>
                    </tr>
                    @if($observasi->observasi_lainnya)
                    <tr>
                        <td colspan="3"></td>
                        <td colspan="3"><strong>Keterangan Lain-lain:</strong> {{ $observasi->observasi_lainnya }}</td>
                    </tr>
                    @endif
                    @php $s62 = $statusCell('6.2'); @endphp
                    <tr class="{{ $rowClass('6.2') }}">
                        <td>6.2</td><td>Posisi Leher dan Punggung Tegak</td>
                        <td class="{{ $s62['class'] }}" style="text-align:center;">{{ $s62['symbol'] }}</td>
                        <td colspan="3" style="background:#f1f5f9; text-align:center; font-size:9px; font-weight:bold;">
                            Rekomendasi Risiko C4.5:
                            <span style="color: {{ ($observasi->rekomendasi_risiko ?? $observasi->tingkat_risiko) === 'tinggi' ? '#dc2626' : (($observasi->rekomendasi_risiko ?? $observasi->tingkat_risiko) === 'sedang' ? '#d97706' : '#16a34a') }};">
                                {{ strtoupper($observasi->rekomendasi_risiko ?? $observasi->tingkat_risiko) }}
                            </span>
                            <br>Rekomendasi Perilaku: {{ strtoupper($observasi->rekomendasi_perilaku ?? $observasi->jenis_perilaku) }}
                            @if($observasi->risiko_hes)<br>Keputusan Final HES: {{ strtoupper($observasi->jenis_perilaku) }} / {{ strtoupper($observasi->risiko_hes) }}@endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Kolom Kanan: Detail Deskripsi & Pertanyaan Evaluasi -->
        <div class="bbs-right-panel">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1.5px solid #000; padding-bottom: 4px;">
                <div><strong>Observasi Perilaku:</strong></div>
                <div style="font-weight: bold; font-size: 12px;">
                    <span style="margin-right: 8px;">{{ $observasi->jenis_perilaku === 'safe' ? '🔴' : '○' }} Safe</span>
                    <span>{{ $observasi->jenis_perilaku === 'at_risk' ? '🔴' : '○' }} At-Risk</span>
                </div>
            </div>

            <div class="bbs-field-box">
                <div class="bbs-field-label">Area Observasi :</div>
                <div class="bbs-field-value">{{ $observasi->lokasi->nama_lokasi ?? 'Wellpad / Area Operasional' }}</div>
            </div>

            <div class="bbs-field-box">
                <div class="bbs-field-label">Nomor Perilaku (lihat tabel disamping) :</div>
                <div class="bbs-field-value">
                    @php
                        $checkedItems = array_keys(array_filter($states, fn($v) => in_array($v, ['safe','at_risk'])));
                        $safeItems = array_keys(array_filter($states, fn($v) => $v === 'safe'));
                        $riskItems = array_keys(array_filter($states, fn($v) => $v === 'at_risk'));
                    @endphp
                    @if(count($riskItems) > 0)
                        <span style="color:#b91c1c; font-weight:bold;">At-Risk: {{ implode(', ', $riskItems) }}</span>
                        @if(count($safeItems) > 0)
                            <br><span style="color:#15803d;">Safe: {{ implode(', ', $safeItems) }}</span>
                        @endif
                    @elseif(count($safeItems) > 0)
                        Safe: {{ implode(', ', $safeItems) }}
                    @else
                        {{ $observasi->kategori->id ?? '—' }} — {{ $observasi->kategori->nama_kategori ?? 'Umum' }}
                    @endif
                </div>
            </div>

            <div class="bbs-field-box">
                <div class="bbs-field-label">Ketika: (Jelaskan aktivitas yang dilakukan)</div>
                <div class="bbs-field-value">{{ $observasi->ketika ?? 'Pada saat memandu aktivitas operasional di lokasi kerja.' }}</div>
            </div>

            <div class="bbs-field-box" style="border-left: 3px solid #dc2626;">
                <div class="bbs-field-label" style="color: #b91c1c;">Ditemukan: (Jelaskan perilaku beresiko)</div>
                <div class="bbs-field-value" style="font-weight: bold;">{{ $observasi->ditemukan }}</div>
            </div>

            <div class="bbs-field-box">
                <div class="bbs-field-label">Sebab: (Alasan mengapa perilaku beresiko dilakukan)</div>
                <div class="bbs-field-value">{{ $observasi->sebab ?? 'Faktor kebiasaan / pencahayaan / APD kurang memadai.' }}</div>
            </div>

            <div class="bbs-field-box" style="border-left: 3px solid #16a34a;">
                <div class="bbs-field-label" style="color: #15803d;">Saran: (Solusi agar perilaku beresiko tidak berulang) / Mencoba:</div>
                <div class="bbs-field-value">{{ $observasi->saran ?? 'Pastikan bekerja di lingkungan kerja dengan standar pencahayaan dan SOP yang baik.' }}</div>
            </div>

            <!-- Pertanyaan Evaluasi Bawah -->
            <div class="bbs-footer-section">
                <div style="display: flex; justify-content: space-between;">
                    <span>Setuju perilaku terjadi?</span>
                    <span><strong>● Ya</strong> &nbsp;&nbsp; ○ Tidak</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Setuju perilaku berisiko?</span>
                    <span><strong>● Ya</strong> &nbsp;&nbsp; ○ Tidak</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 9.5px;">
                    <span>Perilaku selamat:</span>
                    <span><strong>● Mudah/Enabled</strong> &nbsp; ○ Sukar &nbsp; ○ Tidak Mungkin</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Tindak lanjut Steering Committee?</span>
                    <span>{{ $observasi->status_tindak_lanjut !== 'open' ? '● Ya' : '○ Ya' }} &nbsp;&nbsp; {{ $observasi->status_tindak_lanjut === 'open' ? '● Tidak' : '○ Tidak' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bbs-watermark">
        Document Uncontrolled When Printed. Dokumen Tidak Terkendali Apabila dicetak.
    </div>
</div>
</div>
