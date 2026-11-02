<?php

namespace App\Exports;

use App\Models\KeputusanHes;
use App\Models\ObservasiBbs;
use Illuminate\Support\Collection;
use RuntimeException;
use ZipArchive;

class KeputusanHesExport
{
    /**
     * Buat file Excel laporan keputusan final HES berisi 2 sheet:
     * - Sheet 1: Ringkasan Keputusan Final
     * - Sheet 2: Detail Observasi yang masuk dalam siklus tersebut
     */
    public function create(KeputusanHes $keputusan): string
    {
        // Ambil observasi yang terkait dengan keputusan ini
        $observasiList = ObservasiBbs::with(['user', 'lokasi', 'kategori'])
            ->where('keputusan_hes_id', $keputusan->id)
            ->latest()
            ->get();

        $labelKeputusan = $this->labelKeputusan($keputusan->keputusan, $keputusan->keputusan_custom);
        $labelRekomendasi = $keputusan->rekomendasi_sistem
            ? $this->labelKeputusan($keputusan->rekomendasi_sistem)
            : 'Tidak ada rekomendasi sistem';

        // Sheet 1 – Ringkasan
        $sheet1Rows = $this->buildRingkasanRows($keputusan, $labelKeputusan, $labelRekomendasi);
        // Sheet 2 – Detail Observasi
        $sheet2Rows = $this->buildDetailObservasiRows($observasiList);

        $sheet1Xml = $this->sheetXml($sheet1Rows, $this->columnName(count($sheet1Rows[0])), count($sheet1Rows), true);
        $sheet2Xml = $this->sheetXml($sheet2Rows, $this->columnName(count($sheet2Rows[0])), count($sheet2Rows), true);

        $path = tempnam(sys_get_temp_dir(), 'keputusan-hes-');
        if ($path === false) {
            throw new RuntimeException('Tidak dapat menyiapkan file Excel sementara.');
        }

        $zip = new ZipArchive();
        $opened = $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($opened !== true) {
            @unlink($path);
            throw new RuntimeException('Tidak dapat membuat file Excel.');
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->rootRelationshipsXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelationshipsXml());
        $zip->addFromString('xl/styles.xml', $this->stylesXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheet1Xml);
        $zip->addFromString('xl/worksheets/sheet2.xml', $sheet2Xml);
        $zip->close();

        return $path;
    }

    // -----------------------------------------------------------------------
    // Builder Sheet 1 – Ringkasan Keputusan Final
    // -----------------------------------------------------------------------
    private function buildRingkasanRows(KeputusanHes $k, string $labelKeputusan, string $labelRekomendasi): array
    {
        $tanggal = $k->created_at ? $k->created_at->format('d/m/Y H:i') : '-';
        $mengikuti = $k->mengikuti_rekomendasi ? 'Ya' : 'Tidak';

        return [
            // Judul
            ['LAPORAN KEPUTUSAN FINAL ADMIN HES', '', '', ''],
            ['Sistem Observasi Behavior-Based Safety (BBS)', '', '', ''],
            ['PT Pertamina Hulu Rokan (PHR)', '', '', ''],
            ['', '', '', ''],
            // Info keputusan
            ['INFORMASI KEPUTUSAN', '', '', ''],
            ['Tanggal Keputusan', $tanggal, '', ''],
            ['Admin HES Pembuat Keputusan', $k->admin_name ?? '-', '', ''],
            ['', '', '', ''],
            // Keputusan
            ['KEPUTUSAN FINAL', '', '', ''],
            ['Keputusan yang Dipilih', $labelKeputusan, '', ''],
            ['Rekomendasi Sistem C4.5', $labelRekomendasi, '', ''],
            ['Mengikuti Rekomendasi Sistem', $mengikuti, '', ''],
            ['Catatan / Keterangan', $k->catatan ?? '-', '', ''],
            ['', '', '', ''],
            // Ringkasan data
            ['RINGKASAN DATA OBSERVASI SIKLUS INI', '', '', ''],
            ['Total Observasi Diproses', (string) $k->total_observasi, '', ''],
            ['Jumlah Risiko Rendah', (string) $k->jumlah_rendah, '', ''],
            ['Jumlah Risiko Sedang', (string) $k->jumlah_sedang, '', ''],
            ['Jumlah Risiko Tinggi', (string) $k->jumlah_tinggi, '', ''],
            ['', '', '', ''],
            ['Dokumen ini dibuat otomatis oleh Sistem BBS pada ' . now()->format('d/m/Y H:i'), '', '', ''],
        ];
    }

    // -----------------------------------------------------------------------
    // Builder Sheet 2 – Detail Observasi dalam siklus
    // -----------------------------------------------------------------------
    private function buildDetailObservasiRows(Collection $observasiList): array
    {
        $headers = [
            'No',
            'Tanggal',
            'Nama Kru',
            'No. Badge',
            'Unit / Departemen',
            'Lokasi',
            'Kategori BBS',
            'Jenis Perilaku',
            'Prediksi Risiko Awal (C4.5)',
            'Label Risiko HES Final',
            'Temuan Observasi',
            'Saran / Tindakan',
            'Status Tindak Lanjut',
            'Catatan Tindak Lanjut',
        ];

        $rows = [$headers];

        foreach ($observasiList as $i => $obs) {
            $rows[] = [
                (string) ($i + 1),
                $obs->tanggal ? date('d/m/Y', strtotime($obs->tanggal)) : '-',
                $obs->user?->name ?? '-',
                $obs->user?->no_batch ?? '-',
                $obs->unit_departemen ?? '-',
                $obs->lokasi?->nama_lokasi ?? '-',
                $obs->kategori?->nama_kategori ?? '-',
                strtoupper($obs->jenis_perilaku ?? '-'),
                strtoupper($obs->tingkat_risiko ?? '-'),
                strtoupper($obs->risiko_hes ?? '-'),
                $obs->ditemukan ?? '-',
                $obs->saran ?? '-',
                strtoupper($obs->status_tindak_lanjut ?? '-'),
                $obs->catatan_tindak_lanjut ?? '-',
            ];
        }

        return $rows;
    }

    // -----------------------------------------------------------------------
    // XML Builders
    // -----------------------------------------------------------------------
    private function sheetXml(array $rows, string $lastColumn, int $lastRow, bool $freezeFirstRow = false): string
    {
        $xmlRows = [];
        foreach ($rows as $rowIndex => $values) {
            $excelRow = $rowIndex + 1;
            $cells = [];
            foreach ($values as $columnIndex => $value) {
                $reference = $this->columnName($columnIndex + 1) . $excelRow;
                // Style 1 = header biru, style 2 = judul section (bold biru gelap), style 0 = normal
                $style = $rowIndex === 0 ? ' s="1"' : '';
                $text = $this->xmlEscape((string) $value);
                $cells[] = '<c r="' . $reference . '" t="inlineStr"' . $style . '><is><t xml:space="preserve">' . $text . '</t></is></c>';
            }
            $xmlRows[] = '<row r="' . $excelRow . '">' . implode('', $cells) . '</row>';
        }

        $freeze = $freezeFirstRow
            ? '<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
            : '';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<dimension ref="A1:' . $lastColumn . $lastRow . '"/>'
            . $freeze
            . '<sheetFormatPr defaultRowHeight="18"/><sheetData>' . implode('', $xmlRows) . '</sheetData>'
            . '<autoFilter ref="A1:' . $lastColumn . '1"/>'
            . '</worksheet>';
    }

    private function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';
    }

    private function rootRelationshipsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function workbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets>'
            . '<sheet name="Keputusan Final HES" sheetId="1" r:id="rId1"/>'
            . '<sheet name="Detail Observasi" sheetId="2" r:id="rId2"/>'
            . '</sheets>'
            . '</workbook>';
    }

    private function workbookRelationshipsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    private function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><color rgb="FFFFFFFF"/><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF1F4E78"/><bgColor indexed="64"/></patternFill></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="2">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/>'
            . '</cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';
    }

    private function columnName(int $number): string
    {
        $name = '';
        while ($number > 0) {
            $number--;
            $name = chr(65 + ($number % 26)) . $name;
            $number = intdiv($number, 26);
        }
        return $name;
    }

    private function xmlEscape(string $value): string
    {
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $value) ?? $value;
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function labelKeputusan(string $value, ?string $custom = null): string
    {
        $map = [
            'prioritaskan_tinggi'     => 'Prioritaskan Tindak Lanjut pada Risiko Tinggi',
            'pembinaan_at_risk'       => 'Lakukan Pembinaan pada Perilaku At-Risk',
            'perbaikan_kondisi_kerja' => 'Periksa / Perbaiki Prosedur, Alat, atau Kondisi Lokasi Kerja',
            'pemantauan'              => 'Lanjutkan Pemantauan dan Evaluasi pada Observasi Berikutnya',
            'apresiasi_safe'          => 'Berikan Apresiasi kepada Kru dengan Perilaku Safe',
            'lainnya'                 => $custom ?? 'Keputusan Lainnya',
        ];
        return $map[$value] ?? ucfirst(str_replace('_', ' ', $value));
    }
}
