<?php

namespace App\Services;

use App\Models\ObservasiBbs;
use App\Models\KategoriPerilaku;
use App\Models\Lokasi;

class C45Service
{
    /**
     * Menghitung Entropy dari kumpulan data berdasarkan target kelas (tingkat_risiko).
     */
    public function calculateEntropy(array $counts, int $total): float
    {
        if ($total <= 0) return 0.0;

        $entropy = 0.0;
        foreach ($counts as $count) {
            if ($count > 0) {
                $p = $count / $total;
                $entropy -= $p * (log($p, 2));
            }
        }
        return round($entropy, 4);
    }

    /**
     * Mengambil ringkasan perhitungan Entropy & Gain untuk setiap atribut C4.5.
     */
    public function getAnalyticsSummary(): array
    {
        $dataset = ObservasiBbs::query()->whereNotNull('risiko_hes')->get();
        $totalDataset = $dataset->count();

        if ($totalDataset === 0) {
            return [
                'total' => 0,
                'total_entropy' => 0,
                'attributes' => [],
                'class_counts' => ['rendah' => 0, 'sedang' => 0, 'tinggi' => 0],
                'model_ready' => false,
            ];
        }

        $classCounts = [
            'rendah' => $dataset->where('risiko_hes', 'rendah')->count(),
            'sedang' => $dataset->where('risiko_hes', 'sedang')->count(),
            'tinggi' => $dataset->where('risiko_hes', 'tinggi')->count(),
        ];

        $totalEntropy = $this->calculateEntropy($classCounts, $totalDataset);

        // Calculate Information Gain for Attributes
        $attributes = [];

        // Atribut: Jenis Perilaku
        $attributes['jenis_perilaku'] = $this->calculateAttributeGain($dataset, 'jenis_perilaku', $totalDataset, $totalEntropy);
        
        // Atribut: Kategori Perilaku
        $attributes['kategori'] = $this->calculateAttributeGainRelation($dataset, 'kategori_id', KategoriPerilaku::class, 'nama_kategori', $totalDataset, $totalEntropy);

        // Atribut: Lokasi Area
        $attributes['lokasi'] = $this->calculateAttributeGainRelation($dataset, 'lokasi_id', Lokasi::class, 'nama_lokasi', $totalDataset, $totalEntropy);

        $modelReady = $totalDataset >= 10 && count(array_filter($classCounts)) >= 2;
        $decisionRules = [];
        if ($modelReady) {
            $tree = $this->buildTree($dataset, ['jenis_perilaku', 'kategori_id', 'lokasi_id']);
            $this->flattenRules($tree, [], $decisionRules);
        }

        return [
            'total' => $totalDataset,
            'total_entropy' => $totalEntropy,
            'class_counts' => $classCounts,
            'attributes' => $attributes,
            'model_ready' => $modelReady,
            'decision_rules' => $decisionRules,
        ];
    }

    private function calculateAttributeGain($dataset, string $column, int $totalDataset, float $totalEntropy): array
    {
        $values = $dataset->pluck($column)->unique();
        $subEntropies = [];
        $gainSum = 0.0;
        $splitInfo = 0.0;

        foreach ($values as $val) {
            $subset = $dataset->where($column, $val);
            $subTotal = $subset->count();
            $counts = [
                'rendah' => $subset->where('risiko_hes', 'rendah')->count(),
                'sedang' => $subset->where('risiko_hes', 'sedang')->count(),
                'tinggi' => $subset->where('risiko_hes', 'tinggi')->count(),
            ];
            $entropy = $this->calculateEntropy($counts, $subTotal);
            $weight = $subTotal / $totalDataset;
            $gainSum += $weight * $entropy;

            if ($weight > 0) {
                $splitInfo -= $weight * (log($weight, 2));
            }

            $subEntropies[] = [
                'value' => ucfirst($val),
                'total' => $subTotal,
                'counts' => $counts,
                'entropy' => $entropy,
            ];
        }

        $gain = round($totalEntropy - $gainSum, 4);
        $gainRatio = $splitInfo > 0 ? round($gain / $splitInfo, 4) : 0.0;

        return [
            'name' => 'Jenis Perilaku (Safe vs At-Risk)',
            'gain' => $gain,
            'gain_ratio' => $gainRatio,
            'sub_items' => $subEntropies,
        ];
    }

    private function calculateAttributeGainRelation($dataset, string $fkColumn, string $modelClass, string $nameAttr, int $totalDataset, float $totalEntropy): array
    {
        $records = $modelClass::all();
        $subEntropies = [];
        $gainSum = 0.0;
        $splitInfo = 0.0;

        foreach ($records as $record) {
            $subset = $dataset->where($fkColumn, $record->id);
            $subTotal = $subset->count();
            if ($subTotal === 0) continue;

            $counts = [
                'rendah' => $subset->where('risiko_hes', 'rendah')->count(),
                'sedang' => $subset->where('risiko_hes', 'sedang')->count(),
                'tinggi' => $subset->where('risiko_hes', 'tinggi')->count(),
            ];
            $entropy = $this->calculateEntropy($counts, $subTotal);
            $weight = $subTotal / $totalDataset;
            $gainSum += $weight * $entropy;

            if ($weight > 0) {
                $splitInfo -= $weight * (log($weight, 2));
            }

            $subEntropies[] = [
                'value' => $record->$nameAttr,
                'total' => $subTotal,
                'counts' => $counts,
                'entropy' => $entropy,
            ];
        }

        $gain = round($totalEntropy - $gainSum, 4);
        $gainRatio = $splitInfo > 0 ? round($gain / $splitInfo, 4) : 0.0;

        $attrName = ($fkColumn === 'kategori_id') ? 'Kategori Perilaku BBS' : 'Lokasi Area Kerja';

        return [
            'name' => $attrName,
            'gain' => $gain,
            'gain_ratio' => $gainRatio,
            'sub_items' => $subEntropies,
        ];
    }

    /**
     * Mengklasifikasikan tingkat risiko observasi baru menggunakan pohon keputusan C4.5.
     */
    public function predictRisk(string $jenisPerilaku, int $kategoriId, int $lokasiId): string
    {
        $verifiedData = ObservasiBbs::query()->whereNotNull('risiko_hes')->get();
        $counts = [
            'rendah' => $verifiedData->where('risiko_hes', 'rendah')->count(),
            'sedang' => $verifiedData->where('risiko_hes', 'sedang')->count(),
            'tinggi' => $verifiedData->where('risiko_hes', 'tinggi')->count(),
        ];

        if ($verifiedData->count() >= 10 && count(array_filter($counts)) >= 2) {
            $tree = $this->buildTree($verifiedData, ['jenis_perilaku', 'kategori_id', 'lokasi_id']);
            $features = [
                'jenis_perilaku' => strtolower($jenisPerilaku),
                'kategori_id' => (string) $kategoriId,
                'lokasi_id' => (string) $lokasiId,
            ];

            while (!($tree['leaf'] ?? false)) {
                $attribute = $tree['attribute'];
                $value = $features[$attribute];
                if (!isset($tree['branches'][$value])) {
                    return $tree['class'];
                }
                $tree = $tree['branches'][$value];
            }

            return $tree['class'];
        }

        // Rule 1: Jika perilaku Safe -> Risiko Rendah
        if (strtolower($jenisPerilaku) === 'safe') {
            return 'rendah';
        }

        // Aturan cadangan dipakai sebelum cukup label risiko yang tervalidasi HES.
        $kategori = KategoriPerilaku::find($kategoriId);
        $namaKat = strtolower($kategori->nama_kategori ?? '');

        // Kategori Risiko Tinggi: LOTO, Prosedur, APAR/Tanggap Darurat, Listrik
        if (str_contains($namaKat, 'loto') || str_contains($namaKat, 'isolasi') || 
            str_contains($namaKat, 'prosedur') || str_contains($namaKat, 'darurat') ||
            str_contains($namaKat, 'listrik')) {
            return 'tinggi';
        }

        // Kategori Risiko Sedang: APD, Posisi Badan, Kebersihan/Housekeeping, Mengemudi
        if (str_contains($namaKat, 'apd') || str_contains($namaKat, 'posisi') || 
            str_contains($namaKat, 'housekeeping') || str_contains($namaKat, 'mengemudi')) {
            return 'sedang';
        }

        return 'sedang';
    }

    private function buildTree($dataset, array $attributes): array
    {
        $classCounts = [
            'rendah' => $dataset->where('risiko_hes', 'rendah')->count(),
            'sedang' => $dataset->where('risiko_hes', 'sedang')->count(),
            'tinggi' => $dataset->where('risiko_hes', 'tinggi')->count(),
        ];
        arsort($classCounts);
        $majorityClass = array_key_first($classCounts);
        $presentClasses = count(array_filter($classCounts));

        if ($presentClasses <= 1 || $attributes === []) {
            return ['leaf' => true, 'class' => $majorityClass];
        }

        $totalEntropy = $this->calculateEntropy($classCounts, $dataset->count());
        $bestAttribute = null;
        $bestRatio = 0.0;
        $candidateScores = [];

        foreach ($attributes as $attribute) {
            $groups = $dataset->groupBy($attribute);
            $weightedEntropy = 0.0;
            $splitInfo = 0.0;
            foreach ($groups as $group) {
                $weight = $group->count() / $dataset->count();
                $groupCounts = [
                    'rendah' => $group->where('risiko_hes', 'rendah')->count(),
                    'sedang' => $group->where('risiko_hes', 'sedang')->count(),
                    'tinggi' => $group->where('risiko_hes', 'tinggi')->count(),
                ];
                $weightedEntropy += $weight * $this->calculateEntropy($groupCounts, $group->count());
                if ($weight > 0) {
                    $splitInfo -= $weight * log($weight, 2);
                }
            }

            $gain = $totalEntropy - $weightedEntropy;
            $ratio = $splitInfo > 0 ? $gain / $splitInfo : 0.0;
            if ($gain > 0) {
                $candidateScores[$attribute] = ['gain' => $gain, 'ratio' => $ratio];
            }
        }

        if ($candidateScores !== []) {
            $averageGain = array_sum(array_column($candidateScores, 'gain')) / count($candidateScores);
            foreach ($candidateScores as $attribute => $score) {
                if ($score['gain'] >= $averageGain && $score['ratio'] > $bestRatio) {
                    $bestRatio = $score['ratio'];
                    $bestAttribute = $attribute;
                }
            }
        }

        if ($bestAttribute === null) {
            return ['leaf' => true, 'class' => $majorityClass];
        }

        $remainingAttributes = array_values(array_diff($attributes, [$bestAttribute]));
        $branches = [];
        foreach ($dataset->groupBy($bestAttribute) as $value => $group) {
            $branches[(string) $value] = $this->buildTree($group, $remainingAttributes);
        }

        return [
            'leaf' => false,
            'class' => $majorityClass,
            'attribute' => $bestAttribute,
            'branches' => $branches,
        ];
    }

    private function flattenRules(array $node, array $conditions, array &$rules): void
    {
        if ($node['leaf'] ?? false) {
            $conditionText = $conditions === []
                ? 'Tanpa kondisi khusus'
                : implode(' DAN ', $conditions);
            $rules[] = $conditionText.' → Risiko '.strtoupper($node['class']);
            return;
        }

        $attributeNames = [
            'jenis_perilaku' => 'Perilaku',
            'kategori_id' => 'Kategori',
            'lokasi_id' => 'Lokasi',
        ];

        foreach ($node['branches'] as $value => $branch) {
            $displayValue = match ($node['attribute']) {
                'jenis_perilaku' => strtoupper($value),
                'kategori_id' => KategoriPerilaku::find($value)?->nama_kategori ?? $value,
                'lokasi_id' => Lokasi::find($value)?->nama_lokasi ?? $value,
                default => $value,
            };
            $nextConditions = [...$conditions, ($attributeNames[$node['attribute']] ?? $node['attribute']).': '.$displayValue];
            $this->flattenRules($branch, $nextConditions, $rules);
        }
    }
}
