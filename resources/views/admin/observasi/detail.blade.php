@extends('layouts.admin')

@section('title', 'Detail Observasi #' . $observasi->id)
@section('page-title', 'Detail Observasi BBS #' . $observasi->id)

@section('styles')
    <style>
        .observation-detail-layout { display:grid; grid-template-columns:minmax(0,1fr); gap:1.15rem; align-items:start; }
        .hes-review-card { padding:0; overflow:hidden; border:1px solid #dbe4f0; border-radius:18px; background:#fff; box-shadow:0 14px 35px rgba(15,23,42,.07); }
        .hes-review-header { display:flex; gap:.85rem; align-items:center; padding:1.15rem 1.2rem; color:#fff; background:linear-gradient(125deg,#172554 0%,#1e3a8a 58%,#2563eb 100%); }
        .hes-review-icon { display:grid; width:2.65rem; height:2.65rem; flex:none; place-items:center; border:1px solid rgba(255,255,255,.25); border-radius:12px; background:rgba(255,255,255,.14); font-size:1.2rem; }
        .hes-review-title { margin:0; font-size:1rem; font-weight:800; letter-spacing:-.01em; }
        .hes-review-subtitle { margin:.22rem 0 0; color:#dbeafe; font-size:.76rem; }
        .hes-review-body { padding:1.1rem; }
        .hes-review-intro { margin:0 0 1rem; color:#64748b; font-size:.81rem; line-height:1.6; }
        .hes-recommendation { margin-bottom:1.1rem; padding:.9rem; border:1px solid #c7d2fe; border-radius:13px; background:linear-gradient(135deg,#eef2ff,#f8faff); }
        .hes-recommendation-label { display:block; margin-bottom:.6rem; color:#3730a3; font-size:.72rem; font-weight:800; letter-spacing:.07em; text-transform:uppercase; }
        .hes-recommendation-values { display:flex; flex-wrap:wrap; gap:.45rem; }
        .hes-review-divider { height:1px; margin:1.1rem 0; border:0; background:#e8edf4; }
        .hes-review-section-title { display:flex; align-items:center; gap:.5rem; margin:0 0 .85rem; color:#172554; font-size:.9rem; font-weight:800; }
        .hes-review-section-title span { display:grid; width:1.7rem; height:1.7rem; place-items:center; border-radius:8px; background:#eff6ff; color:#2563eb; }
        .hes-review-card .form-group { margin-bottom:.85rem !important; }
        .hes-review-card .form-label { margin-bottom:.4rem; color:#334155; font-size:.78rem; font-weight:750; }
        .hes-review-card .form-control { min-height:43px; border-color:#dbe3ee; border-radius:10px; background:#fbfdff; font-size:.84rem; }
        .hes-review-card textarea.form-control { min-height:96px; line-height:1.5; resize:vertical; }
        .hes-review-card .form-control:focus { border-color:#818cf8; background:#fff; box-shadow:0 0 0 3px rgba(99,102,241,.12); }
        .hes-final-form { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:0 1rem; }
        .hes-final-form .hes-save-button { grid-column:1 / -1; }
        .hes-followup-form { display:grid; grid-template-columns:minmax(180px,.7fr) repeat(2,minmax(0,1fr)); gap:0 1rem; align-items:start; }
        .hes-followup-form .hes-save-button { grid-column:1 / -1; }
        .hes-save-button { min-height:43px; border-radius:10px; font-weight:750; box-shadow:0 5px 12px rgba(37,99,235,.16); }
        .hes-validation-status { display:flex; align-items:center; justify-content:space-between; gap:.6rem; margin-top:.85rem; padding:.75rem .8rem; border:1px solid #dbeafe; border-radius:11px; background:#f8fbff; color:#334155; font-size:.75rem; }
        .hes-pending-status { margin-top:.85rem; padding:.75rem .8rem; border:1px solid #fde68a; border-radius:11px; background:#fffbeb; color:#92400e; font-size:.76rem; line-height:1.5; }
        .hes-draft-callout { margin-bottom:1rem; padding:.8rem .85rem; border:1px solid #fde68a; border-radius:11px; background:#fffbeb; color:#78350f; font-size:.76rem; line-height:1.55; }
        .hes-draft-callout strong { display:block; margin-bottom:.2rem; color:#92400e; }
        .hes-draft-button { margin-top:.4rem; border-radius:8px; font-size:.73rem; }
        .hes-classification { margin-top:1.1rem; padding:.85rem; border:1px solid #e2e8f0; border-radius:11px; background:#f8fafc; color:#475569; font-size:.78rem; }
        .hes-classification strong { display:block; margin-bottom:.5rem; color:#172554; }
        @media (max-width:760px) { .hes-final-form, .hes-followup-form { grid-template-columns:minmax(0,1fr); } .hes-final-form .hes-save-button, .hes-followup-form .hes-save-button { grid-column:auto; } }
        @media (max-width:640px) { .hes-review-body { padding:.9rem; } .hes-review-header { padding:1rem; } .hes-review-title { font-size:.92rem; } .hes-validation-status { align-items:flex-start; flex-direction:column; } }
    </style>
@endsection

@section('content')
    @php
        $draftRisk = $observasi->risiko_hes ?? $observasi->rekomendasi_risiko ?? $observasi->tingkat_risiko;
        $draftCategory = strtolower($observasi->kategori->nama_kategori ?? '');
        if ($observasi->jenis_perilaku === 'safe') {
            $draftCause = 'DRAF - penyebab tidak dapat disimpulkan dari data ini; perilaku aman dapat dipertahankan setelah ditinjau Admin HES.';
            $draftAction = 'DRAF - pertahankan praktik aman; pertimbangkan apresiasi atau berbagi praktik baik setelah verifikasi Admin HES.';
        } else {
            if (str_contains($draftCategory, 'apd') || str_contains($draftCategory, 'pelindung')) {
                $draftCause = 'DRAF - kemungkinan terkait pemahaman, ketersediaan, kecocokan, atau kepatuhan penggunaan APD; perlu diverifikasi di lapangan.';
            } elseif (str_contains($draftCategory, 'prosedur') || str_contains($draftCategory, 'izin') || str_contains($draftCategory, 'jsa')) {
                $draftCause = 'DRAF - kemungkinan terkait pemahaman atau penerapan prosedur, persiapan kerja, izin, maupun pengawasan; perlu dikonfirmasi.';
            } elseif (str_contains($draftCategory, 'kendaraan') || str_contains($draftCategory, 'mengemudi') || str_contains($draftCategory, 'transport')) {
                $draftCause = 'DRAF - kemungkinan terkait perencanaan perjalanan, kondisi pengemudi/kendaraan, atau kepatuhan aturan; perlu diverifikasi.';
            } elseif (str_contains($draftCategory, 'housekeeping') || str_contains($draftCategory, 'lingkungan') || str_contains($draftCategory, 'b3')) {
                $draftCause = 'DRAF - kemungkinan terkait kondisi area, penataan, atau fasilitas kerja; perlu diperiksa langsung.';
            } elseif (str_contains($draftCategory, 'posisi') || str_contains($draftCategory, 'ergonomi') || str_contains($draftCategory, 'tubuh')) {
                $draftCause = 'DRAF - kemungkinan terkait postur atau pengaturan tempat kerja; perlu dikonfirmasi melalui observasi lapangan.';
            } elseif (str_contains($draftCategory, 'alat') || str_contains($draftCategory, 'perkakas') || str_contains($draftCategory, 'listrik') || str_contains($draftCategory, 'mesin')) {
                $draftCause = 'DRAF - kemungkinan terkait pemilihan, kondisi, atau cara penggunaan peralatan; perlu diperiksa dan dikonfirmasi.';
            } else {
                $draftCause = 'DRAF - faktor penyebab belum dapat dipastikan dari isian; perlu klarifikasi kepada kru dan pemeriksaan kondisi kerja.';
            }

            if ($draftRisk === 'tinggi') {
                $draftAction = 'DRAF - prioritaskan pemeriksaan Admin HES; bila bahaya masih berlangsung, kendalikan pekerjaan sesuai prosedur, lakukan koreksi, lalu verifikasi sebelum dilanjutkan.';
            } elseif ($draftRisk === 'sedang') {
                $draftAction = 'DRAF - lakukan koreksi atau coaching sesuai prosedur, periksa faktor pemicu, lalu pantau pada observasi berikutnya.';
            } else {
                $draftAction = 'DRAF - berikan pengingat dan koreksi sesuai prosedur, pastikan kru memahami cara aman, lalu pantau kembali.';
            }
        }
    @endphp
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;" class="no-print">
        <a href="{{ route('admin.observasi.index') }}" class="btn btn-secondary btn-sm">← Kembali ke List Observasi</a>
        <button onclick="window.print()" class="btn btn-primary btn-sm">🖨️ Cetak Formulir BBS Resmi (PDF)</button>
    </div>

    <div class="observation-detail-layout">
        <!-- Official BBS form -->
        <div class="observation-form-panel">
            @include('partials.bbs_formulir')
        </div>

        <!-- Admin HES review appears after the observation form -->
        <div class="card hes-review-card no-print">
            <div class="hes-review-header">
                <div class="hes-review-icon" aria-hidden="true">✓</div>
                <div>
                    <h2 class="hes-review-title">Validasi Risiko oleh Admin HES</h2>
                    <p class="hes-review-subtitle">Tinjau rekomendasi dan tetapkan hasil akhir</p>
                </div>
            </div>
            <div class="hes-review-body">
            <p class="hes-review-intro">
                Sistem menentukan Safe/At-Risk dari checklist. Tingkat risiko mengikuti pola C4.5 jika data label HES cukup; jika belum, sistem memakai aturan dasar. Periksa rekomendasi, lalu simpan pendapat final Admin HES; label risiko final menjadi data pelatihan berikutnya.
            </p>
            <div class="hes-recommendation">
                <span class="hes-recommendation-label">Rekomendasi dari sistem</span>
                <div class="hes-recommendation-values">
                    <span class="badge badge-{{ $observasi->rekomendasi_perilaku ?? $observasi->jenis_perilaku }}">{{ strtoupper($observasi->rekomendasi_perilaku ?? $observasi->jenis_perilaku) }}</span>
                    <span class="badge badge-{{ $observasi->rekomendasi_risiko ?? $observasi->tingkat_risiko }}">RISIKO {{ strtoupper($observasi->rekomendasi_risiko ?? $observasi->tingkat_risiko) }}</span>
                </div>
            </div>
            <h3 class="hes-review-section-title"><span>1</span> Keputusan akhir HES</h3>
            <form action="{{ route('admin.observasi.validasi_risiko', $observasi->id) }}" method="POST" class="hes-final-form">
                @csrf
                <div class="form-group" style="margin-bottom: 0.75rem;">
                    <label class="form-label">Klasifikasi Perilaku Final HES</label>
                    <select name="jenis_perilaku" class="form-control" required>
                        <option value="safe" {{ old('jenis_perilaku', $observasi->jenis_perilaku) === 'safe' ? 'selected' : '' }}>Safe</option>
                        <option value="at_risk" {{ old('jenis_perilaku', $observasi->jenis_perilaku) === 'at_risk' ? 'selected' : '' }}>At-Risk</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0.75rem;">
                    <label class="form-label">Tingkat Risiko Final HES</label>
                    <select name="risiko_hes" class="form-control" required>
                        <option value="">Pilih hasil penilaian</option>
                        <option value="rendah" {{ old('risiko_hes', $observasi->risiko_hes ?? $observasi->rekomendasi_risiko ?? $observasi->tingkat_risiko) === 'rendah' ? 'selected' : '' }}>Rendah</option>
                        <option value="sedang" {{ old('risiko_hes', $observasi->risiko_hes ?? $observasi->rekomendasi_risiko ?? $observasi->tingkat_risiko) === 'sedang' ? 'selected' : '' }}>Sedang</option>
                        <option value="tinggi" {{ old('risiko_hes', $observasi->risiko_hes ?? $observasi->rekomendasi_risiko ?? $observasi->tingkat_risiko) === 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary hes-save-button" style="width: 100%; justify-content: center;">Simpan Validasi HES</button>
            </form>
            @if($observasi->risiko_hes)
                <div class="hes-validation-status">
                    <strong>{{ $observasi->risiko_hes_oleh ? 'Label final HES tersimpan' : 'Label risiko tersimpan' }}</strong>
                    <span class="badge badge-{{ $observasi->risiko_hes }}">{{ strtoupper($observasi->risiko_hes) }}</span>
                </div>
            @else
                <div class="hes-pending-status">Belum masuk ke data pelatihan C4.5 sampai HES menyimpan validasi.</div>
            @endif

            <hr class="hes-review-divider">
            <h3 class="hes-review-section-title"><span>2</span> Status tindak lanjut</h3>
            <div class="hes-draft-callout">
                <strong>Usulan otomatis (DRAF)</strong>
                Usulan sebab dan tindak lanjut dibuat dari kategori, perilaku, dan tingkat risiko. Periksa temuan serta kondisi lapangan, edit bila perlu, dan simpan hanya setelah ditinjau Admin HES.
            </div>
            <form action="{{ route('admin.observasi.tindak_lanjut', $observasi->id) }}" method="POST" class="hes-followup-form">
                @csrf
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Status Tindak Lanjut</label>
                    <select name="status_tindak_lanjut" class="form-control" required>
                        <option value="open" {{ $observasi->status_tindak_lanjut === 'open' ? 'selected' : '' }}>OPEN - Belum Ditindaklanjuti</option>
                        <option value="in_progress" {{ $observasi->status_tindak_lanjut === 'in_progress' ? 'selected' : '' }}>IN PROGRESS - Sedang Ditangani</option>
                        <option value="closed" {{ $observasi->status_tindak_lanjut === 'closed' ? 'selected' : '' }}>CLOSED - Selesai</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Sebab / Faktor Pemicu</label>
                    <textarea id="hes-sebab" name="sebab" class="form-control" rows="4" placeholder="Tulis sebab setelah ditinjau...">{{ old('sebab', $observasi->sebab) }}</textarea>
                    <button type="button" class="btn btn-secondary btn-sm hes-draft-button" onclick="document.getElementById('hes-sebab').value = @js($draftCause)">Gunakan usulan sebab (DRAF)</button>
                </div>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Catatan / Instruksi Steering Committee</label>
                    <textarea id="hes-catatan" name="catatan_tindak_lanjut" class="form-control" rows="4" placeholder="Masukkan catatan penanganan...">{{ old('catatan_tindak_lanjut', $observasi->catatan_tindak_lanjut) }}</textarea>
                    <button type="button" class="btn btn-secondary btn-sm hes-draft-button" onclick="document.getElementById('hes-catatan').value = @js($draftAction)">Gunakan usulan tindak lanjut (DRAF)</button>
                </div>
                <button type="submit" class="btn btn-primary hes-save-button" style="width: 100%; justify-content: center;">Simpan Tindak Lanjut</button>
            </form>

            <div class="hes-classification">
                <strong>Klasifikasi C4.5</strong>
                <div style="margin-top: 0.35rem;">
                    Tingkat Risiko: <span class="badge badge-{{ $observasi->tingkat_risiko }}">{{ strtoupper($observasi->tingkat_risiko) }}</span>
                </div>
            </div>
            </div>
        </div>
    </div>
@endsection
