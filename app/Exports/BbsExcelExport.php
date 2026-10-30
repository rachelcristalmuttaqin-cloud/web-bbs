<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use RuntimeException;
use ZipArchive;

class BbsExcelExport
{
    private array $headers = [
        'No',
        'Tanggal',
        'Nama Kru',
        'No. Badge',
        'Unit / Departemen',
        'Lokasi',
        'Kategori BBS',
        'Jenis Perilaku',
        'Prediksi Risiko Awal',
        'Label Risiko HES',
        'Poin Checklist',
        'Temuan Observasi',
        'Keterangan Lainnya',
        'Ketika',
        'Sebab',
        'Saran / Tindakan',
        'Status Tindak Lanjut',
        'Catatan Tindak Lanjut',
    ];

    public function create(Collection $observations): string
    {
        $rows = [$this->headers];

        foreach ($observations as $index => $observation) {
            $checkedItems = collect($observation->item_states ?? [])
                ->filter(fn ($state) => in_array($state, ['safe', 'at_risk'], true))
                ->map(fn ($state, $code) => $code.': '.strtoupper($state))
                ->values()
                ->implode('; ');

            $rows[] = [
                (string) ($index + 1),
                $observation->tanggal ? date('d/m/Y', strtotime($observation->tanggal)) : '',
                $observation->user?->name ?? '',
                $observation->user?->no_batch ?? '',
                $observation->unit_departemen ?? '',
                $observation->lokasi?->nama_lokasi ?? '',
                $observation->kategori?->nama_kategori ?? '',
                strtoupper($observation->jenis_perilaku ?? ''),
                strtoupper($observation->tingkat_risiko ?? ''),
                strtoupper($observation->risiko_hes ?? ''),
                $checkedItems,
                $observation->ditemukan ?? '',
                $observation->observasi_lainnya ?? '',
                $observation->ketika ?? '',
                $observation->sebab ?? '',
                $observation->saran ?? '',
                strtoupper($observation->status_tindak_lanjut ?? ''),
                $observation->catatan_tindak_lanjut ?? '',
            ];
        }

        $lastRow = count($rows);
        $lastColumn = $this->columnName(count($this->headers));
        $sheetXml = $this->sheetXml($rows, $lastColumn, $lastRow);

        $path = tempnam(sys_get_temp_dir(), 'bbs-report-');
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
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->close();

        return $path;
    }

    private function sheetXml(array $rows, string $lastColumn, int $lastRow): string
    {
        $xmlRows = [];
        foreach ($rows as $rowIndex => $values) {
            $excelRow = $rowIndex + 1;
            $cells = [];
            foreach ($values as $columnIndex => $value) {
                $reference = $this->columnName($columnIndex + 1).$excelRow;
                $style = $rowIndex === 0 ? ' s="1"' : '';
                $text = $this->xmlEscape((string) $value);
                $cells[] = '<c r="'.$reference.'" t="inlineStr"'.$style.'><is><t xml:space="preserve">'.$text.'</t></is></c>';
            }
            $xmlRows[] = '<row r="'.$excelRow.'">'.implode('', $cells).'</row>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<dimension ref="A1:'.$lastColumn.$lastRow.'"/>'
            .'<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
            .'<sheetFormatPr defaultRowHeight="18"/><sheetData>'.implode('', $xmlRows).'</sheetData>'
            .'<autoFilter ref="A1:'.$lastColumn.$lastRow.'"/>'
            .'</worksheet>';
    }

    private function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'</Types>';
    }

    private function rootRelationshipsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
    }

    private function workbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets><sheet name="Laporan BBS" sheetId="1" r:id="rId1"/></sheets>'
            .'</workbook>';
    }

    private function workbookRelationshipsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>';
    }

    private function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><color rgb="FFFFFFFF"/><sz val="11"/><name val="Calibri"/></font></fonts>'
            .'<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF1F4E78"/><bgColor indexed="64"/></patternFill></fill></fills>'
            .'<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/></cellXfs>'
            .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            .'</styleSheet>';
    }

    private function columnName(int $number): string
    {
        $name = '';
        while ($number > 0) {
            $number--;
            $name = chr(65 + ($number % 26)).$name;
            $number = intdiv($number, 26);
        }

        return $name;
    }

    private function xmlEscape(string $value): string
    {
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $value) ?? $value;

        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
