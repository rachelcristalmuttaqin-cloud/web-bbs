<?php

namespace App\Http\Controllers;

use App\Exports\BbsExcelExport;
use App\Models\User;
use App\Models\ObservasiBbs;
use App\Models\KategoriPerilaku;
use App\Models\Lokasi;
use App\Models\KeputusanHes;
use App\Services\C45Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminBbsController extends Controller
{
    protected $c45Service;

    public function __construct(C45Service $c45Service)
    {
        $this->c45Service = $c45Service;
    }

    /**
     * Dashboard Admin / HES
     */
    public function index()
    {
        $totalObservasi = ObservasiBbs::count();
        $safeCount = ObservasiBbs::where('jenis_perilaku', 'safe')->count();
        $atRiskCount = ObservasiBbs::where('jenis_perilaku', 'at_risk')->count();

        $risikoRendah = ObservasiBbs::where('tingkat_risiko', 'rendah')->count();
        $risikoSedang = ObservasiBbs::where('tingkat_risiko', 'sedang')->count();
        $risikoTinggi = ObservasiBbs::where('tingkat_risiko', 'tinggi')->count();
        $highRiskCount = $risikoTinggi;

        $pendingUsers = User::where('status', 'pending')->count();
        $pendingUserCount = $pendingUsers;
        $tindakLanjutOpen = ObservasiBbs::where('status_tindak_lanjut', 'open')->count();

        $recentObservasi = ObservasiBbs::with(['user', 'lokasi', 'kategori'])
            ->latest()
            ->take(6)
            ->get();

        $riskDistribution = [
            'rendah' => $risikoRendah,
            'sedang' => $risikoSedang,
            'tinggi' => $risikoTinggi,
        ];

        return view('admin.index', compact(
            'totalObservasi',
            'safeCount',
            'atRiskCount',
            'risikoRendah',
            'risikoSedang',
            'risikoTinggi',
            'highRiskCount',
            'pendingUsers',
            'pendingUserCount',
            'tindakLanjutOpen',
            'recentObservasi',
            'riskDistribution'
        ));
    }

    /**
     * Data Seluruh Karyawan PT & Status Keaktifannya
     */
    public function daftarKaryawan(Request $request)
    {
        $query = User::where('role', 'karyawan');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%")
                  ->orWhere('unit_departemen', 'like', "%{$search}%")
                  ->orWhere('no_batch', 'like', "%{$search}%");
            });
        }

        $karyawanList = $query->latest()->paginate(15)->withQueryString();

        $totalKaryawan = User::where('role', 'karyawan')->count();
        $totalAktif = User::where('role', 'karyawan')->where('status', 'active')->count();
        $totalPending = User::where('role', 'karyawan')->where('status', 'pending')->count();
        $totalRejected = User::where('role', 'karyawan')->where('status', 'rejected')->count();

        return view('admin.karyawan', compact(
            'karyawanList',
            'totalKaryawan',
            'totalAktif',
            'totalPending',
            'totalRejected'
        ));
    }

    /**
     * Verifikasi Pendaftaran Akun Karyawan
     */
    public function verifikasiUser()
    {
        $pendingUsers = User::where('status', 'pending')->latest()->get();
        $allUsers = User::where('role', 'karyawan')->latest()->get();

        return view('admin.verifikasi_user', compact('pendingUsers', 'allUsers'));
    }

    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'active']);
        $accountType = $user->role === 'admin' ? 'Admin HES' : 'karyawan';

        return back()->with('success', 'Akun ' . $accountType . ' "' . $user->name . '" telah berhasil disetujui & diaktifkan.');
    }

    public function rejectUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'rejected']);
        $accountType = $user->role === 'admin' ? 'Admin HES' : 'karyawan';

        return back()->with('success', 'Pendaftaran akun ' . $accountType . ' "' . $user->name . '" telah ditolak.');
    }

    /**
     * Kelola Data Observasi BBS
     */
    public function kelolaObservasi(Request $request)
    {
        $query = ObservasiBbs::with(['user', 'lokasi', 'kategori']);

        if ($request->filled('validasi_risiko')) {
            if ($request->validasi_risiko === 'pending') {
                $query->whereNull('risiko_hes');
            } elseif ($request->validasi_risiko === 'validated') {
                $query->whereNotNull('risiko_hes');
            }
        }

        if ($request->filled('lokasi_id')) {
            $query->where('lokasi_id', $request->lokasi_id);
        }
        if ($request->filled('jenis_perilaku')) {
            $query->where('jenis_perilaku', $request->jenis_perilaku);
        }
        if ($request->filled('tingkat_risiko')) {
            $query->where('tingkat_risiko', $request->tingkat_risiko);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $observasiList = $query->latest()->paginate(15)->withQueryString();
        $lokasiList = Lokasi::all();
        $kategoriList = KategoriPerilaku::all();

        return view('admin.observasi.index', compact('observasiList', 'lokasiList', 'kategoriList'));
    }

    /**
     * Detail Observasi & Tindak Lanjut
     */
    public function detailObservasi($id)
    {
        $observasi = ObservasiBbs::with(['user', 'lokasi', 'kategori', 'validatorRisikoHes'])->findOrFail($id);

        return view('admin.observasi.detail', compact('observasi'));
    }

    public function updateTindakLanjut(Request $request, $id)
    {
        $request->validate([
            'status_tindak_lanjut' => ['required', 'in:open,in_progress,closed'],
            'sebab' => ['nullable', 'string', 'max:5000'],
            'catatan_tindak_lanjut' => ['nullable', 'string'],
        ]);

        $observasi = ObservasiBbs::findOrFail($id);
        $observasi->update([
            'status_tindak_lanjut' => $request->status_tindak_lanjut,
            'sebab' => $request->sebab,
            'catatan_tindak_lanjut' => $request->catatan_tindak_lanjut,
        ]);

        return back()->with('success', 'Status tindak lanjut Steering Committee berhasil diperbarui.');
    }

    public function validasiRisikoHes(Request $request, $id)
    {
        $request->validate([
            'risiko_hes' => ['required', 'in:rendah,sedang,tinggi'],
            'jenis_perilaku' => ['nullable', 'in:safe,at_risk'],
        ]);

        $observasi = ObservasiBbs::findOrFail($id);
        $observasi->update([
            'jenis_perilaku' => $request->input('jenis_perilaku', $observasi->jenis_perilaku),
            'risiko_hes' => $request->risiko_hes,
            'risiko_hes_oleh' => $request->user()->id,
            'risiko_hes_pada' => now(),
        ]);

        return back()->with('success', 'Klasifikasi perilaku dan tingkat risiko final HES berhasil disimpan. Label risiko akan digunakan sebagai data pelatihan C4.5.');
    }

    /**
     * Modul Analytics & Pohon Keputusan C4.5
     */
    public function modulC45()
    {
        $c45Data = $this->c45Service->getAnalyticsSummary();
        $trainingList = ObservasiBbs::with(['user', 'kategori', 'lokasi', 'validatorRisikoHes'])
            ->whereNotNull('risiko_hes')
            ->latest('risiko_hes_pada')
            ->get();
        $pendingReviewCount = ObservasiBbs::whereNull('keputusan_hes_id')->whereNull('risiko_hes')->count();
        $decisionHistory = KeputusanHes::query()->latest()->take(10)->get();
        $cycleSummary = $this->getActiveDecisionCycleSummary();
        $systemRecommendation = $this->buildSystemDecisionRecommendation($c45Data, $cycleSummary);

        return view('admin.c45.index', compact('c45Data', 'trainingList', 'pendingReviewCount', 'decisionHistory', 'systemRecommendation', 'cycleSummary'));
    }

    public function storeKeputusanHes(Request $request)
    {
        $validated = $request->validate([
            'keputusan' => ['required', 'in:prioritaskan_tinggi,pembinaan_at_risk,perbaikan_kondisi_kerja,pemantauan,apresiasi_safe,lainnya'],
            'keputusan_custom' => ['nullable', 'required_if:keputusan,lainnya', 'string', 'max:255'],
            'catatan' => ['nullable', 'string', 'max:3000'],
        ]);

        $decision = DB::transaction(function () use ($request, $validated) {
            $activeObservations = ObservasiBbs::query()
                ->whereNull('keputusan_hes_id')
                ->lockForUpdate()
                ->get();

            if ($activeObservations->isEmpty()) {
                return null;
            }

            $cycleSummary = $this->summarizeDecisionCycle($activeObservations);
            $modelSummary = $this->c45Service->getAnalyticsSummary();
            $recommendation = $this->buildSystemDecisionRecommendation($modelSummary, $cycleSummary);
            $followingRecommendation = $recommendation !== null
                && $validated['keputusan'] === $recommendation['value'];

            $decision = KeputusanHes::query()->create([
                'user_id' => $request->user()->id,
                'admin_name' => $request->user()->name,
                'keputusan' => $validated['keputusan'],
                'rekomendasi_sistem' => $recommendation['value'] ?? null,
                'mengikuti_rekomendasi' => $followingRecommendation,
                'keputusan_custom' => $validated['keputusan_custom'] ?? null,
                'catatan' => $validated['catatan'] ?? null,
                'total_observasi' => $cycleSummary['total'],
                'jumlah_rendah' => $cycleSummary['class_counts']['rendah'],
                'jumlah_sedang' => $cycleSummary['class_counts']['sedang'],
                'jumlah_tinggi' => $cycleSummary['class_counts']['tinggi'],
            ]);

            ObservasiBbs::query()
                ->whereIn('id', $activeObservations->modelKeys())
                ->update(['keputusan_hes_id' => $decision->id]);

            return $decision;
        });

        if (!$decision) {
            return back()->with('error', 'Belum ada observasi aktif untuk dimasukkan ke keputusan final.');
        }

        return redirect()->route('admin.c45.index')->with('success', 'Keputusan final disimpan untuk '.$decision->total_observasi.' observasi. Data siklus ini sudah ditandai selesai dan tetap tersimpan sebagai data pelatihan/riwayat.');
    }

    private function getActiveDecisionCycleSummary(): array
    {
        $observations = ObservasiBbs::query()->whereNull('keputusan_hes_id')->get();

        return $this->summarizeDecisionCycle($observations);
    }

    private function summarizeDecisionCycle($observations): array
    {
        $counts = ['rendah' => 0, 'sedang' => 0, 'tinggi' => 0];
        foreach ($observations as $observation) {
            $risk = $observation->risiko_hes ?? $observation->tingkat_risiko;
            if (isset($counts[$risk])) {
                $counts[$risk]++;
            }
        }

        return ['total' => $observations->count(), 'class_counts' => $counts];
    }

    private function buildSystemDecisionRecommendation(array $modelSummary, array $cycleSummary): ?array
    {
        if (!($modelSummary['model_ready'] ?? false) || $cycleSummary['total'] === 0) {
            return null;
        }

        $counts = $cycleSummary['class_counts'];
        if ($counts['tinggi'] > 0) {
            return [
                'value' => 'prioritaskan_tinggi',
                'label' => 'Prioritaskan tindak lanjut pada risiko tinggi',
                'reason' => 'Terdapat '.$counts['tinggi'].' observasi berlabel risiko tinggi. Tinjau dan prioritaskan tindak lanjut untuk temuan tersebut.',
            ];
        }

        if ($counts['sedang'] > 0) {
            return [
                'value' => 'perbaikan_kondisi_kerja',
                'label' => 'Periksa atau perbaiki prosedur, alat, atau kondisi lokasi kerja',
                'reason' => 'Belum ada label risiko tinggi, tetapi terdapat '.$counts['sedang'].' observasi risiko sedang yang perlu diperiksa atau dipantau.',
            ];
        }

        return [
            'value' => 'pemantauan',
            'label' => 'Lanjutkan pemantauan dan evaluasi pada observasi berikutnya',
            'reason' => 'Data berlabel saat ini berada pada tingkat risiko rendah. Pertahankan pemantauan pada observasi berikutnya.',
        ];
    }

    /**
     * Master Data (Kategori & Lokasi)
     */
    public function masterData()
    {
        $kategoriList = KategoriPerilaku::withCount('observasi')->get();
        $lokasiList = Lokasi::withCount('observasi')->get();

        return view('admin.master.index', compact('kategoriList', 'lokasiList'));
    }

    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        KategoriPerilaku::create($request->only('nama_kategori', 'deskripsi'));

        return back()->with('success', 'Kategori Perilaku BBS berhasil ditambahkan.');
    }

    public function deleteKategori($id)
    {
        KategoriPerilaku::findOrFail($id)->delete();
        return back()->with('success', 'Kategori Perilaku berhasil dihapus.');
    }

    public function storeLokasi(Request $request)
    {
        $request->validate([
            'nama_lokasi' => ['required', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:255'],
        ]);

        Lokasi::create($request->only('nama_lokasi', 'area'));

        return back()->with('success', 'Lokasi Area Kerja berhasil ditambahkan.');
    }

    public function deleteLokasi($id)
    {
        Lokasi::findOrFail($id)->delete();
        return back()->with('success', 'Lokasi Area Kerja berhasil dihapus.');
    }

    /**
     * Export Laporan Rekapitulasi Observasi
     */
    public function laporan(Request $request)
    {
        $observasiList = $this->laporanQuery($request)->get();

        return view('admin.laporan', compact('observasiList'));
    }

    public function exportLaporanExcel(Request $request, BbsExcelExport $export)
    {
        $observasiList = $this->laporanQuery($request)->get();
        $filePath = $export->create($observasiList);
        $filename = 'Laporan_BBS_'.now()->format('Ymd_His').'.xlsx';

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function laporanQuery(Request $request)
    {
        $query = ObservasiBbs::with(['user', 'lokasi', 'kategori']);

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        return $query->latest();
    }
}
