<?php

namespace App\Services;

/**
 * Parser de planillas CSV/XLSX sin dependencias externas.
 * Devuelve un array de filas (cada fila es un array de celdas como string).
 * La primera fila se asume como encabezado.
 */
class SpreadsheetParser
{
    /** Detecta el formato por extensión y parsea. Devuelve [] si no es soportado. */
    public static function parse(string $path, string $extension): array
    {
        return match (strtolower($extension)) {
            'xlsx'        => self::parseXlsx($path),
            'csv', 'txt'  => self::parseCsv($path),
            default       => [],
        };
    }

    /** Parsea un archivo CSV y devuelve array de filas. */
    public static function parseCsv(string $path): array
    {
        $rows   = [];
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return [];
        }

        // Descartar BOM UTF-8 si existe
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $rows[] = array_map('strval', $row);
        }
        fclose($handle);

        return $rows;
    }

    /** Parsea un archivo XLSX usando ZipArchive + SimpleXML. */
    public static function parseXlsx(string $path): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            return [];
        }

        // Cadenas compartidas (celdas de tipo texto)
        $sharedStrings = [];
        $ssContent     = $zip->getFromName('xl/sharedStrings.xml');
        if ($ssContent !== false) {
            $ss = simplexml_load_string($ssContent);
            foreach ($ss->si as $si) {
                if (isset($si->t)) {
                    $sharedStrings[] = (string) $si->t;
                } else {
                    $text = '';
                    foreach ($si->r ?? [] as $r) {
                        $text .= (string) ($r->t ?? '');
                    }
                    $sharedStrings[] = $text;
                }
            }
        }

        $sheetContent = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if ($sheetContent === false) {
            return [];
        }

        $rows = [];
        $xml  = simplexml_load_string($sheetContent);

        foreach ($xml->sheetData->row as $row) {
            $cells   = [];
            $lastCol = 0;

            foreach ($row->c as $cell) {
                preg_match('/^([A-Z]+)/', (string) $cell['r'], $m);
                $colIdx = self::colLetterToIndex($m[1] ?? 'A');

                while ($lastCol < $colIdx - 1) {
                    $cells[] = '';
                    $lastCol++;
                }

                $value = '';
                if (isset($cell->v)) {
                    $value = (string) $cell['t'] === 's'
                        ? ($sharedStrings[(int) $cell->v] ?? '')
                        : (string) $cell->v;
                }
                $cells[] = $value;
                $lastCol = $colIdx;
            }
            $rows[] = $cells;
        }

        return $rows;
    }

    /** Convierte letras de columna Excel (A, B, AA…) a índice 1-based. */
    public static function colLetterToIndex(string $col): int
    {
        $idx = 0;
        foreach (str_split(strtoupper($col)) as $c) {
            $idx = $idx * 26 + (ord($c) - 64);
        }
        return $idx;
    }
}
