@extends('layouts.admin')

@section('title', 'Modul C4.5')
@section('page-title', 'Modul Analitik Algoritma C4.5')

@section('styles')
<style>
    .tree-box { background: #1e293b; color: #f8fafc; padding: 1.5rem; border-radius: 14px; font-family: monospace; font-size: 0.9rem; line-height: 1.8; }
    .tree-node { color: #38bdf8; font-weight: bold; }
    .tree-leaf-rendah { color: #4ade80; font-weight: bold; }
    .tree-leaf-sedang { color: #fbbf24; font-weight: bold; }
    .tree-leaf-tinggi { color: #f87171; font-weight: bold; }
</style>
@endsection

@section('content')
    @php
        $topGainRatio = count($c45Data['attributes'])
            ? max(array_column($c45Data['attributes'], 'gain_ratio'))
            : null;
        $decisionLabels = [
            'prioritaskan_tinggi' => 'Prioritaskan tindak lanjut pada risiko tinggi',
            'pembinaan_at_risk' => 'Lakukan pembinaan kepada kru dengan perilaku At-Risk',
            'perbaikan_kondisi_kerja' => 'Periksa atau perbaiki prosedur, alat, atau kondisi lokasi kerja',
            'pemantauan' => 'Lanjutkan pemantauan dan evaluasi pada observasi berikutnya',
            'apresiasi_safe' => 'Berikan apresiasi dan pertahankan praktik Safe',
            'lainnya' => 'Keputusan lainnya',
        ];
    @endphp

    <div class="card" style="margin-bottom: 1.25rem;">
        <div class="card-header">
            <span class="card-title">🌳 C4.5 untuk Membaca Pola Risiko BBS</span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1rem;">
            <span style="font-size:0.875rem; color:#475569; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:0.55rem 0.75rem;">{{ $c45Data['total'] }} data berlabel dipakai untuk belajar; {{ $pendingReviewCount }} data belum memiliki label risiko.</span>
            <a href="{{ route('admin.observasi.index', ['validasi_risiko' => 'pending']) }}" class="btn btn-primary">Tinjau Observasi</a>
        </div>
        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 1.5rem; line-height: 1.6;">
            Checklist menentukan rekomendasi perilaku berdasarkan status yang jumlahnya lebih banyak; jika jumlah Safe dan At-Risk sama, sistem memilih At-Risk sebagai langkah konservatif. C4.5 mempelajari label tingkat risiko final HES dari perilaku, kategori, dan lokasi untuk memperkirakan risiko Rendah, Sedang, atau Tinggi. Admin HES dapat menerima atau mengubah kedua rekomendasi.
        </p>

        <div style="background: #f8fafc; padding: 1rem 1.1rem; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; line-height: 1.65; color: #334155;">
            <strong style="color: #0f172a;">Hubungan data BBS dengan C4.5</strong>
            <ul style="margin: 0.5rem 0 0; padding-left: 1.25rem; font-size: 0.875rem;">
                <li><strong>Kru mengisi checklist BBS.</strong> Sistem membandingkan jumlah poin Safe dan At-Risk untuk menyarankan klasifikasi keseluruhan.</li>
                <li><strong>Risiko diberi label.</strong> Label Rendah, Sedang, atau Tinggi menjadi contoh yang dipelajari sistem.</li>
                <li><strong>C4.5 mencari pola.</strong> Sistem melihat faktor mana yang sering berkaitan dengan label risiko tertentu.</li>
                <li><strong>Admin HES menetapkan hasil akhir.</strong> Admin dapat menerima atau mengubah rekomendasi perilaku maupun risiko.</li>
            </ul>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
            <div style="background: #f8fafc; padding: 1.1rem; border-radius: 12px; border: 1px solid #e2e8f0;">
                <div style="font-size: 0.725rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 0.35rem;">Jumlah Data BBS yang Dipelajari</div>
                <div style="font-size: 1.75rem; font-weight: 800; color: #0f172a;">{{ $c45Data['total'] }} Baris</div>
                <div style="font-size: 0.8rem; color: #475569; margin-top: 0.25rem;">
                    Rendah: {{ $c45Data['class_counts']['rendah'] }} | Sedang: {{ $c45Data['class_counts']['sedang'] }} | Tinggi: {{ $c45Data['class_counts']['tinggi'] }}
                </div>
            </div>
            <div style="background: #f8fafc; padding: 1.1rem; border-radius: 12px; border: 1px solid #e2e8f0;">
                <div style="font-size: 0.725rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 0.35rem;">Entropy — Campuran Label</div>
                <div style="font-size: 1.75rem; font-weight: 800; color: #4f46e5;">{{ $c45Data['total_entropy'] }}</div>
                <div style="font-size: 0.8rem; color: #475569; margin-top: 0.25rem;">Menunjukkan apakah label risiko bercampur atau cenderung sama. Ini bukan tingkat bahaya.</div>
            </div>
        </div>

        <div style="font-size: 0.95rem; font-weight: 700; color: #0f172a; margin-bottom: 0.35rem;">📊 Faktor yang membantu C4.5 membedakan kelompok risiko</div>
        <p style="font-size:0.85rem; color:#64748b; margin:0 0 0.875rem;">Gain Ratio tertinggi menunjukkan faktor yang paling membantu menemukan pola pada data ini. Angka ini bukan nilai risiko dan bukan persentase ketepatan.</p>
        <div class="table-wrap" style="margin-bottom: 1.5rem;">
            <table>
                <thead>
                    <tr>
                        <th>Faktor yang Dinilai</th>
                        <th>Gain</th>
                        <th>Gain Ratio</th>
                        <th>Arti untuk Admin HES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($c45Data['attributes'] as $attr)
                        <tr>
                            <td><strong>{{ $attr['name'] }}</strong></td>
                            <td style="font-weight: 700; color: #2563eb;">{{ $attr['gain'] }}</td>
                            <td style="font-weight: 700; color: #7c3aed;">{{ $attr['gain_ratio'] }}</td>
                            <td>
                                @if($topGainRatio !== null && $topGainRatio > 0 && $attr['gain_ratio'] == $topGainRatio)
                                    <span class="badge badge-tinggi">Paling membantu membaca pola</span>
                                @else
                                    <span class="badge badge-rendah">Faktor pembanding</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="font-size: 0.95rem; font-weight: 700; color: #0f172a; margin-bottom: 0.875rem;">Apa hasil yang diberikan C4.5?</div>
        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.875rem; line-height: 1.6;">
            C4.5 menampilkan aturan pola yang dipelajari dari data berlabel. Saat observasi baru dibuat, sistem memakai pola tersebut untuk memberikan perkiraan risiko awal agar Admin HES punya bahan pertimbangan.
        </p>
        <div style="background: {{ $c45Data['model_ready'] ? '#f0fdf4' : '#fffbeb' }}; border:1px solid {{ $c45Data['model_ready'] ? '#bbf7d0' : '#fde68a' }}; border-radius:12px; padding:1rem; margin-bottom:1rem; color:#334155; font-size:0.875rem; line-height:1.6;">
            @if($c45Data['model_ready'])
                <strong>C4.5 sudah memiliki cukup data untuk membentuk pola.</strong> Ada {{ $c45Data['total'] }} observasi dengan lebih dari satu tingkat risiko. Aturan yang dipelajari tampil di bawah.
            @else
                <strong>Data belum cukup untuk membentuk pola C4.5.</strong> Sistem memerlukan sedikitnya 10 observasi berlabel dan dua tingkat risiko. Untuk sementara, perkiraan risiko baru memakai aturan dasar.
            @endif
        </div>
        @if($c45Data['model_ready'])
            <div style="font-size:0.95rem; font-weight:700; color:#0f172a; margin-bottom:0.5rem;">Pola keputusan yang ditemukan</div>
            <p style="font-size:0.85rem; color:#64748b; margin:0 0 0.75rem;">Baca aturan dari kiri ke kanan: jika kondisi di sebelah kiri sesuai dengan observasi, bagian kanan menunjukkan perkiraan risiko yang dipelajari dari data. Gunakan sebagai bahan pertimbangan Admin HES.</p>
            <div class="tree-box">
                @foreach($c45Data['decision_rules'] as $rule)
                    <div>{{ $rule }}</div>
                @endforeach
            </div>
        @else
            <div style="font-size:0.95rem; font-weight:700; color:#0f172a; margin-bottom:0.5rem;">Aturan Cadangan Sementara</div>
            <div class="tree-box">
                <div>Jika perilaku <span class="tree-node">Safe</span>, hasilnya <span class="tree-leaf-rendah">RISIKO RENDAH</span>.</div>
                <div>Jika perilaku <span class="tree-node">At-Risk</span>, gunakan kategori temuan sebagai panduan sementara:</div>
                <div style="padding-left: 1.5rem;">• Kategori LOTO, isolasi, prosedur, listrik, atau tanggap darurat → <span class="tree-leaf-tinggi">RISIKO TINGGI</span>.</div>
                <div style="padding-left: 1.5rem;">• Kategori APD, posisi badan, housekeeping, atau mengemudi → <span class="tree-leaf-sedang">RISIKO SEDANG</span>.</div>
                <div style="padding-left: 1.5rem;">• Kategori lainnya → <span class="tree-leaf-sedang">RISIKO SEDANG</span>.</div>
            </div>
        @endif
    </div>

    <div class="card" style="margin-bottom:1.25rem;">
        <div class="card-header">
            <span class="card-title">✅ Keputusan Final Admin HES</span>
        </div>
        <p style="font-size:0.875rem; color:#64748b; line-height:1.6; margin:0 0 1rem;">
            Siklus aktif berisi observasi yang belum masuk keputusan final. Setelah admin menyimpan keputusan, semua data aktif ditandai selesai dan tidak lagi masuk hitungan siklus aktif. Data lama tetap tersimpan sebagai riwayat dan bahan pelatihan C4.5.
        </p>
        <div style="display:flex; flex-wrap:wrap; gap:0.6rem; margin-bottom:1rem;">
            <span class="badge badge-rendah">Risiko rendah: {{ $cycleSummary['class_counts']['rendah'] }}</span>
            <span class="badge badge-sedang">Risiko sedang: {{ $cycleSummary['class_counts']['sedang'] }}</span>
            <span class="badge badge-tinggi">Risiko tinggi: {{ $cycleSummary['class_counts']['tinggi'] }}</span>
            <span style="display:inline-flex; align-items:center; padding:0.25rem 0.6rem; border-radius:999px; background:#eff6ff; color:#1d4ed8; font-size:0.75rem; font-weight:700;">Data siklus aktif: {{ $cycleSummary['total'] }}</span>
        </div>

        @if($cycleSummary['total'] === 0)
            <div style="background:#f8fafc;border:1px solid #cbd5e1;color:#475569;border-radius:10px;padding:.85rem;margin-bottom:1rem;font-size:.85rem;">
                Tidak ada data pada siklus aktif. Keputusan sebelumnya tetap tercatat di riwayat; data baru akan memulai siklus berikutnya.
            </div>
        @elseif($systemRecommendation)
            <div style="background:#eff6ff;border:1px solid #bfdbfe;color:#1e3a8a;border-radius:10px;padding:1rem;margin-bottom:1rem;">
                <strong>Saran tindakan sistem</strong>
                <div style="font-size:1rem;font-weight:700;margin-top:.35rem;">{{ $systemRecommendation['label'] }}</div>
                <div style="font-size:.85rem;line-height:1.55;margin-top:.35rem;">{{ $systemRecommendation['reason'] }}</div>
            </div>
        @elseif($cycleSummary['total'] > 0)
            <div style="background:#fffbeb;border:1px solid #fde68a;color:#92400e;border-radius:10px;padding:.85rem;margin-bottom:1rem;font-size:.85rem;">
                Data berlabel belum cukup untuk membuat saran tindakan berbasis pola C4.5. Admin tetap dapat mencatat keputusan sendiri berdasarkan pemeriksaan.
            </div>
        @endif

        @if($cycleSummary['total'] > 0)
        <form action="{{ route('admin.c45.keputusan') }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom:0.85rem;">
                <label for="keputusan" class="form-label">Keputusan final Admin HES</label>
                <select id="keputusan" name="keputusan" class="form-control" required>
                    <option value="">Pilih keputusan</option>
                    @foreach($decisionLabels as $value => $label)
                        <option value="{{ $value }}" {{ old('keputusan', $systemRecommendation['value'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}{{ $systemRecommendation && $systemRecommendation['value'] === $value ? ' — Saran sistem' : '' }}</option>
                    @endforeach
                </select>
                @error('keputusan')<small style="color:#b91c1c;">{{ $message }}</small>@enderror
            </div>
            <div id="keputusanCustomGroup" class="form-group" style="margin-bottom:0.85rem;display:{{ old('keputusan') === 'lainnya' ? 'block' : 'none' }};">
                <label for="keputusan-custom" class="form-label">Tuliskan keputusan Admin HES</label>
                <input id="keputusan-custom" type="text" name="keputusan_custom" class="form-control" maxlength="255" value="{{ old('keputusan_custom') }}" placeholder="Contoh: lakukan inspeksi bersama pengawas area">
                @error('keputusan_custom')<small style="color:#b91c1c;">{{ $message }}</small>@enderror
            </div>

            {{-- Catatan & Alasan Admin HES --}}
            <div class="form-group" style="margin-bottom:1rem;">
                <label for="catatan-keputusan" class="form-label" style="font-size:0.875rem; font-weight:700; color:#0f172a;">
                    📝 Catatan &amp; Alasan Admin HES
                    <span style="font-weight:400; color:#64748b; font-size:0.8rem;"> — Wajib diisi untuk dokumentasi evaluasi PHR</span>
                </label>
                <textarea
                    id="catatan-keputusan"
                    name="catatan"
                    class="form-control"
                    rows="4"
                    maxlength="3000"
                    placeholder="Tulis alasan keputusan, temuan utama yang mendasari keputusan, tindak lanjut yang perlu dilakukan, atau catatan untuk evaluasi bulan berikutnya.&#10;&#10;Contoh: Terdapat 3 observasi risiko tinggi pada area WP-4 terkait penggunaan LOTO. Keputusan: prioritaskan pembinaan kru di area tersebut dan lakukan inspeksi bersama pengawas."
                    style="min-height:100px; resize:vertical;"
                >{{ old('catatan') }}</textarea>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:0.3rem;">
                    <small style="color:#64748b;">Catatan ini akan tersimpan bersama keputusan dan dapat dilihat kembali di Riwayat Evaluasi per Bulan.</small>
                    <small id="catatanCounter" style="color:#94a3b8;">0 / 3000</small>
                </div>
                @error('catatan')<small style="color:#b91c1c;">{{ $message }}</small>@enderror
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;">
                💾 &nbsp;Simpan Keputusan Final Admin HES
            </button>
        </form>

        <script>
            document.getElementById('keputusan')?.addEventListener('change', function () {
                const isCustom = this.value === 'lainnya';
                const group = document.getElementById('keputusanCustomGroup');
                const input = document.getElementById('keputusan-custom');
                group.style.display = isCustom ? 'block' : 'none';
                input.required = isCustom;
            });
            document.getElementById('keputusan')?.dispatchEvent(new Event('change'));

            // Counter karakter catatan
            const catatanEl = document.getElementById('catatan-keputusan');
            const counterEl = document.getElementById('catatanCounter');
            function updateCounter() {
                const len = catatanEl?.value?.length ?? 0;
                if (counterEl) {
                    counterEl.textContent = len + ' / 3000';
                    counterEl.style.color = len > 2800 ? '#dc2626' : '#94a3b8';
                }
            }
            catatanEl?.addEventListener('input', updateCounter);
            updateCounter();
        </script>
        @endif

        {{-- Riwayat terbaru (5 keputusan terakhir) --}}
        @if($decisionHistory->isNotEmpty())
            <div style="border-top:1px solid #e2e8f0; margin-top:1.5rem; padding-top:1rem;">
                <div style="font-weight:700; color:#0f172a; margin-bottom:0.75rem;">🕐 5 Keputusan Terbaru</div>
                <div style="display:grid; gap:0.65rem;">
                    @foreach($decisionHistory as $decision)
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:0.8rem 0.9rem;">
                            <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:flex-start; gap:0.4rem;">
                                <strong style="color:#0f172a;">{{ $decision->keputusan === 'lainnya' && $decision->keputusan_custom ? $decision->keputusan_custom : ($decisionLabels[$decision->keputusan] ?? $decision->keputusan) }}</strong>
                                <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
                                    <span style="color:#64748b; font-size:0.8rem;">{{ $decision->created_at->format('d M Y H:i') }}</span>
                                    <a
                                        href="{{ route('admin.c45.keputusan.export', $decision->id) }}"
                                        style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.3rem 0.75rem;background:linear-gradient(135deg,#0d9488,#0284c7);color:#fff;border-radius:8px;font-size:0.75rem;font-weight:700;text-decoration:none;white-space:nowrap;"
                                        title="Download laporan Excel untuk presentasi PHR"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                        Export Excel
                                    </a>
                                </div>
                            </div>
                            <div style="color:#64748b; font-size:0.8rem; margin-top:0.25rem;">
                                Diputuskan oleh <strong>{{ $decision->admin_name }}</strong> · {{ $decision->total_observasi }} data
                                <span class="badge badge-rendah" style="font-size:0.7rem;">Rendah: {{ $decision->jumlah_rendah }}</span>
                                <span class="badge badge-sedang" style="font-size:0.7rem;">Sedang: {{ $decision->jumlah_sedang }}</span>
                                <span class="badge badge-tinggi" style="font-size:0.7rem;">Tinggi: {{ $decision->jumlah_tinggi }}</span>
                            </div>
                            <div style="font-size:.8rem;margin-top:.3rem;color:{{ $decision->mengikuti_rekomendasi ? '#15803d' : '#475569' }};">
                                {{ $decision->mengikuti_rekomendasi ? '✅ Mengikuti saran sistem' : '✏️ Keputusan mandiri Admin HES' }}
                                @if($decision->rekomendasi_sistem)
                                    · Saran sistem: {{ $decisionLabels[$decision->rekomendasi_sistem] ?? $decision->rekomendasi_sistem }}
                                @endif
                            </div>
                            @if($decision->catatan)
                                <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:0.55rem 0.7rem; margin-top:0.5rem; font-size:0.83rem; color:#78350f; line-height:1.55;">
                                    <strong>📝 Catatan Admin:</strong> {{ $decision->catatan }}
                                </div>
                            @else
                                <div style="color:#94a3b8; font-size:0.78rem; margin-top:0.35rem; font-style:italic;">Tidak ada catatan.</div>
                            @endif
                        </div>
                    @endforeach
                </div>
                @if($availableMonths->count() > 0)
                    <div style="margin-top:0.75rem; text-align:right;">
                        <a href="#evaluasi-bulanan" style="font-size:0.82rem; color:#0284c7; font-weight:600; text-decoration:none;">
                            Lihat semua riwayat evaluasi per bulan ↓
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- ============================================================ -->
    <!-- RIWAYAT EVALUASI PER BULAN -->
    <!-- ============================================================ -->
    @if($availableMonths->count() > 0)
    <div class="card" id="evaluasi-bulanan" style="margin-bottom:1.25rem;">
        <div class="card-header" style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:0.75rem;">
            <span class="card-title">📅 Riwayat Evaluasi Keputusan per Bulan</span>
            <form method="GET" action="{{ route('admin.c45.index') }}#evaluasi-bulanan" style="display:flex; align-items:center; gap:0.5rem;">
                <label style="font-size:0.8rem; font-weight:600; color:#475569;">Filter Bulan:</label>
                <select name="bulan" onchange="this.form.submit()" style="padding:0.35rem 0.65rem; border:1.5px solid #cbd5e1; border-radius:8px; font-size:0.85rem; color:#0f172a; background:#fff; cursor:pointer;">
                    <option value="">— Semua Bulan —</option>
                    @foreach($availableMonths as $ym)
                        @php
                            [$thn, $bln] = explode('-', $ym);
                            $namaBulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                        @endphp
                        <option value="{{ $ym }}" {{ $filterBulan === $ym ? 'selected' : '' }}>
                            {{ ($namaBulan[$bln] ?? $bln) . ' ' . $thn }}
                        </option>
                    @endforeach
                </select>
                @if($filterBulan)
                    <a href="{{ route('admin.c45.index') }}#evaluasi-bulanan" style="font-size:0.8rem; color:#64748b; text-decoration:none; padding:0.35rem 0.5rem;">✕ Reset</a>
                @endif
            </form>
        </div>

        @php
            $tampilBulan = $filterBulan
                ? $decisionByMonth->filter(fn($v, $k) => $k === $filterBulan)
                : $decisionByMonth;
            $namaBulanMap = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
        @endphp

        @foreach($tampilBulan->sortKeysDesc() as $ym => $decisions)
            @php
                [$thn, $bln] = explode('-', $ym);
                $labelBulan = ($namaBulanMap[$bln] ?? $bln) . ' ' . $thn;
                $totalObs = $decisions->sum('total_observasi');
                $totalTinggi = $decisions->sum('jumlah_tinggi');
                $totalSedang = $decisions->sum('jumlah_sedang');
                $totalRendah = $decisions->sum('jumlah_rendah');
            @endphp

            <div style="margin-bottom:1.5rem;">
                {{-- Header bulan --}}
                <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; background:linear-gradient(135deg,#0d9488,#0284c7); color:#fff; border-radius:12px; padding:0.75rem 1rem; margin-bottom:0.75rem; gap:0.5rem;">
                    <div>
                        <div style="font-size:1rem; font-weight:800;">📆 {{ $labelBulan }}</div>
                        <div style="font-size:0.78rem; opacity:0.88; margin-top:0.15rem;">{{ $decisions->count() }} keputusan · {{ $totalObs }} total observasi diproses</div>
                    </div>
                    <div style="display:flex; gap:0.4rem; flex-wrap:wrap;">
                        <span style="background:rgba(255,255,255,0.18); border-radius:999px; padding:0.2rem 0.65rem; font-size:0.75rem; font-weight:700;">🟢 Rendah: {{ $totalRendah }}</span>
                        <span style="background:rgba(255,255,255,0.18); border-radius:999px; padding:0.2rem 0.65rem; font-size:0.75rem; font-weight:700;">🟡 Sedang: {{ $totalSedang }}</span>
                        <span style="background:rgba(255,255,255,0.18); border-radius:999px; padding:0.2rem 0.65rem; font-size:0.75rem; font-weight:700;">🔴 Tinggi: {{ $totalTinggi }}</span>
                    </div>
                </div>

                {{-- Daftar keputusan dalam bulan ini --}}
                <div style="display:grid; gap:0.6rem; padding-left:0.5rem; border-left:3px solid #e2e8f0;">
                    @foreach($decisions->sortByDesc('created_at') as $decision)
                        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:0.9rem 1rem; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
                            {{-- Baris atas: keputusan + tombol export --}}
                            <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:flex-start; gap:0.5rem;">
                                <div>
                                    <div style="font-weight:700; color:#0f172a; font-size:0.92rem;">
                                        {{ $decision->keputusan === 'lainnya' && $decision->keputusan_custom
                                            ? $decision->keputusan_custom
                                            : ($decisionLabels[$decision->keputusan] ?? $decision->keputusan) }}
                                    </div>
                                    <div style="font-size:0.78rem; color:#64748b; margin-top:0.15rem;">
                                        {{ $decision->created_at->format('d M Y, H:i') }} · oleh <strong>{{ $decision->admin_name }}</strong>
                                    </div>
                                </div>
                                <a
                                    href="{{ route('admin.c45.keputusan.export', $decision->id) }}"
                                    style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.35rem 0.8rem;background:linear-gradient(135deg,#0d9488,#0284c7);color:#fff;border-radius:8px;font-size:0.75rem;font-weight:700;text-decoration:none;white-space:nowrap;flex-shrink:0;"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    Export Excel
                                </a>
                            </div>

                            {{-- Badge risiko --}}
                            <div style="display:flex; flex-wrap:wrap; gap:0.35rem; margin-top:0.5rem;">
                                <span class="badge badge-rendah" style="font-size:0.72rem;">Rendah: {{ $decision->jumlah_rendah }}</span>
                                <span class="badge badge-sedang" style="font-size:0.72rem;">Sedang: {{ $decision->jumlah_sedang }}</span>
                                <span class="badge badge-tinggi" style="font-size:0.72rem;">Tinggi: {{ $decision->jumlah_tinggi }}</span>
                                <span style="display:inline-flex;align-items:center;padding:0.18rem 0.55rem;border-radius:999px;background:#eff6ff;color:#1d4ed8;font-size:0.72rem;font-weight:700;">
                                    Total: {{ $decision->total_observasi }} obs
                                </span>
                                @if($decision->mengikuti_rekomendasi)
                                    <span style="display:inline-flex;align-items:center;padding:0.18rem 0.55rem;border-radius:999px;background:#f0fdf4;color:#15803d;font-size:0.72rem;font-weight:700;">✅ Ikut saran sistem</span>
                                @else
                                    <span style="display:inline-flex;align-items:center;padding:0.18rem 0.55rem;border-radius:999px;background:#f8fafc;color:#475569;font-size:0.72rem;font-weight:700;">✏️ Keputusan mandiri</span>
                                @endif
                            </div>

                            {{-- Rekomendasi sistem --}}
                            @if($decision->rekomendasi_sistem)
                                <div style="font-size:0.78rem; color:#64748b; margin-top:0.35rem;">
                                    Saran sistem C4.5: <em>{{ $decisionLabels[$decision->rekomendasi_sistem] ?? $decision->rekomendasi_sistem }}</em>
                                </div>
                            @endif

                            {{-- Catatan Admin --}}
                            @if($decision->catatan)
                                <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:0.6rem 0.75rem; margin-top:0.6rem; font-size:0.83rem; color:#78350f; line-height:1.6;">
                                    <strong>📝 Catatan &amp; Alasan Admin HES:</strong><br>
                                    {{ $decision->catatan }}
                                </div>
                            @else
                                <div style="color:#94a3b8; font-size:0.78rem; margin-top:0.4rem; font-style:italic;">Tidak ada catatan yang ditambahkan.</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        @if($tampilBulan->isEmpty())
            <div style="text-align:center; color:#64748b; padding:1.5rem; font-size:0.875rem;">
                Tidak ada keputusan pada bulan yang dipilih.
            </div>
        @endif
    </div>
    @endif

    <!-- Training Dataset Table -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">📋 Data Pelatihan C4.5 ({{ count($trainingList) }})</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Observer</th>
                        <th>Jenis Perilaku</th>
                        <th>Kategori BBS</th>
                        <th>Lokasi Area</th>
                        <th>Prediksi Awal</th>
                        <th>Label Final HES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trainingList as $index => $train)
                        <tr>
                            <td>{{ date('d M Y', strtotime($train->tanggal)) }}</td>
                            <td>{{ $train->user->name ?? '-' }}</td>
                            <td>{{ strtoupper($train->jenis_perilaku) }}</td>
                            <td>{{ Str::limit($train->kategori->nama_kategori ?? '-', 25) }}</td>
                            <td>{{ Str::limit($train->lokasi->nama_lokasi ?? '-', 22) }}</td>
                            <td><span class="badge badge-{{ $train->tingkat_risiko }}">{{ strtoupper($train->tingkat_risiko) }}</span></td>
                            <td><span class="badge badge-{{ $train->risiko_hes }}">{{ strtoupper($train->risiko_hes) }}</span></td>
                        </tr>
                    @endforeach
                    @if(count($trainingList) === 0)
                        <tr><td colspan="7" style="text-align:center; color:#64748b; padding:1.5rem;">Belum ada observasi berlabel. Mulai dari tombol Tinjau Observasi.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
