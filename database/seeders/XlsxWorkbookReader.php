<?php

namespace Database\Seeders;

use DOMDocument;
use DOMXPath;
use RuntimeException;
use ZipArchive;

class XlsxWorkbookReader
{
    private ZipArchive $archive;
    private array $sharedStrings = [];

    public function __construct(string $path)
    {
        $this->archive = new ZipArchive();

        if ($this->archive->open($path) !== true) {
            throw new RuntimeException("Tidak dapat membuka file Excel: {$path}");
        }

        if ($this->archive->locateName('xl/sharedStrings.xml') !== false) {
            $xml = $this->loadXml('xl/sharedStrings.xml');
            $xpath = new DOMXPath($xml);

            foreach ($xpath->query('//*[local-name()="si"]') as $item) {
                $text = '';
                foreach ($xpath->query('.//*[local-name()="t"]', $item) as $part) {
                    $text .= $part->textContent;
                }
                $this->sharedStrings[] = $text;
            }
        }
    }

    public function rows(string $sheetName): array
    {
        $workbook = $this->loadXml('xl/workbook.xml');
        $workbookXPath = new DOMXPath($workbook);
        $relationships = $this->loadXml('xl/_rels/workbook.xml.rels');
        $relationshipXPath = new DOMXPath($relationships);
        $targets = [];

        foreach ($relationshipXPath->query('//*[local-name()="Relationship"]') as $relationship) {
            $targets[$relationship->getAttribute('Id')] = $relationship->getAttribute('Target');
        }

        $sheetPath = null;
        foreach ($workbookXPath->query('//*[local-name()="sheet"]') as $sheet) {
            if ($sheet->getAttribute('name') !== $sheetName) {
                continue;
            }

            $relationshipId = $sheet->getAttributeNS(
                'http://schemas.openxmlformats.org/officeDocument/2006/relationships',
                'id'
            );
            $sheetPath = $this->normalizePath($targets[$relationshipId] ?? '');
            break;
        }

        if (!$sheetPath) {
            throw new RuntimeException("Sheet Excel tidak ditemukan: {$sheetName}");
        }

        $sheetXml = $this->loadXml($sheetPath);
        $sheetXPath = new DOMXPath($sheetXml);
        $rows = [];

        foreach ($sheetXPath->query('//*[local-name()="sheetData"]/*[local-name()="row"]') as $rowNode) {
            $values = [];
            $maxColumn = -1;

            foreach ($sheetXPath->query('./*[local-name()="c"]', $rowNode) as $cell) {
                $column = $this->columnIndex($cell->getAttribute('r'));
                $maxColumn = max($maxColumn, $column);
                $type = $cell->getAttribute('t');
                $valueNode = $sheetXPath->query('./*[local-name()="v"]', $cell)->item(0);
                $value = $valueNode?->textContent ?? '';

                if ($type === 's' && $value !== '') {
                    $value = $this->sharedStrings[(int) $value] ?? '';
                } elseif ($type === 'inlineStr') {
                    $value = '';
                    foreach ($sheetXPath->query('.//*[local-name()="t"]', $cell) as $part) {
                        $value .= $part->textContent;
                    }
                }

                $values[$column] = trim((string) $value);
            }

            if ($maxColumn < 0) {
                continue;
            }

            $rows[] = array_replace(array_fill(0, $maxColumn + 1, ''), $values);
        }

        if (count($rows) < 2) {
            return [];
        }

        $headers = array_map(fn ($header) => trim((string) $header), array_shift($rows));
        $records = [];

        foreach ($rows as $row) {
            $record = [];
            foreach ($headers as $index => $header) {
                if ($header !== '') {
                    $record[$header] = trim((string) ($row[$index] ?? ''));
                }
            }
            if (array_filter($record, fn ($value) => $value !== '') !== []) {
                $records[] = $record;
            }
        }

        return $records;
    }

    public function close(): void
    {
        $this->archive->close();
    }

    private function loadXml(string $path): DOMDocument
    {
        $contents = $this->archive->getFromName($path);
        if ($contents === false) {
            throw new RuntimeException("Bagian workbook tidak ditemukan: {$path}");
        }

        $document = new DOMDocument();
        if (!$document->loadXML($contents, LIBXML_NONET | LIBXML_NOBLANKS)) {
            throw new RuntimeException("XML workbook tidak valid: {$path}");
        }

        return $document;
    }

    private function normalizePath(string $target): string
    {
        $parts = [];
        foreach (explode('/', str_replace('\\', '/', ltrim($target, '/'))) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }
            if ($part === '..') {
                array_pop($parts);
                continue;
            }
            $parts[] = $part;
        }

        $path = implode('/', $parts);
        return str_starts_with($path, 'xl/') ? $path : 'xl/'.$path;
    }

    private function columnIndex(string $cellReference): int
    {
        preg_match('/^[A-Z]+/', strtoupper($cellReference), $matches);
        $index = 0;
        foreach (str_split($matches[0] ?? '') as $letter) {
            $index = ($index * 26) + (ord($letter) - ord('A') + 1);
        }

        return $index - 1;
    }
}
