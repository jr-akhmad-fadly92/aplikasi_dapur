<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LaporanRekapMenuSimpleExport implements FromCollection, WithEvents, ShouldAutoSize
{
    protected $rows;
    protected $start;
    protected $end;
    protected $dapur;
    protected $mergeRanges = []; // Row ranges to merge per date group
    protected $lastDataRow = 8;

    public function __construct(Collection $rows, string $start, string $end, $dapur)
    {
        $this->rows  = $rows;
        $this->start = $start;
        $this->end   = $end;
        $this->dapur = $dapur;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Bold header info rows 1-5 and column heading row 8
                foreach ([1, 2, 3, 4, 5, 8] as $row) {
                    $sheet->getStyle('A' . $row . ':Q' . $row)->getFont()->setBold(true);
                }

                // Merge & center columns A (No), B (Tanggal), C (Hari) per date group
                foreach ($this->mergeRanges as $range) {
                    [$start, $end] = $range;
                    foreach (['A', 'B', 'C'] as $col) {
                        $cellRange = $col . $start . ':' . $col . $end;
                        $sheet->mergeCells($cellRange);
                        $sheet->getStyle($cellRange)
                              ->getAlignment()
                              ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                              ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    }
                }

                // Apply border from column No to Natrium (A:Q) until last data row
                $sheet->getStyle('A8:Q' . $this->lastDataRow)
                      ->getBorders()
                      ->getAllBorders()
                      ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }

    public function collection()
    {
        $startDate = Carbon::parse($this->start);
        $endDate   = Carbon::parse($this->end);
        $weekNo    = (int) ceil($startDate->day / 7);
        $monthName = $startDate->locale('id')->translatedFormat('F');
        $periode   = $this->start !== $this->end
            ? $startDate->format('j') . ' - ' . $endDate->locale('id')->translatedFormat('j F Y')
            : $startDate->locale('id')->translatedFormat('j F Y');

        $e = array_fill(0, 16, ''); // 17 columns total, filler for header rows

        $header = collect([
            array_merge(['Yayasan Bina bangsa Semarang'], $e),
            array_merge(['Unit Kegiatan MBG'],            $e),
            array_merge([$this->dapur->nama_dapur ?? '-'],$e),
            array_merge(['Daftar Menu ' . $monthName . ' Minggu ke ' . $weekNo], $e),
            array_merge(['Periode ' . $periode],          $e),
            $e,
            $e,
            [
                'No', 'Tanggal', 'Hari', 'No. Urut', 'Golongan',
                'Karbohidrat', 'Protein', 'Sayur', 'Buah', 'Pendamping',
                'Jumlah Porsi', 'Energi (kkal)', 'Protein (g)', 'Lemak (g)',
                'Karbohidrat (g)', 'Serat (g)', 'Natrium (mg)',
            ],
        ]);

        // Header occupies rows 1-8; data starts at Excel row 9
        $headerRowCount = 8;

        $currentDate      = null;
        $dateNo           = 0;
        $dayCounter       = 0;
        $groupStartIdx    = null; // 0-based index in $data
        $groups           = [];

        $sortedRows = $this->rows->sortBy(function ($row) {
            $tanggalObj = null;
            if (!empty($row->tanggal_kirim)) {
                $tanggalObj = $row->tanggal_kirim instanceof Carbon
                    ? $row->tanggal_kirim
                    : Carbon::parse($row->tanggal_kirim);
            }

            $tanggalKey = $tanggalObj ? $tanggalObj->format('Y-m-d') : '9999-12-31';
            $golonganKey = ((int) ($row->total_penerima_a ?? 0) > 0) ? 0 : 1; // A first, then B

            return $tanggalKey . '|' . $golonganKey;
        })->values();

        $data = $sortedRows->map(function ($row, $index) use (
            &$currentDate, &$dateNo, &$dayCounter, &$groupStartIdx, &$groups
        ) {
            $tanggalObj = null;
            if (!empty($row->tanggal_kirim)) {
                $tanggalObj = $row->tanggal_kirim instanceof Carbon
                    ? $row->tanggal_kirim
                    : Carbon::parse($row->tanggal_kirim);
            }

            $tanggal  = $tanggalObj ? $tanggalObj->format('Y-m-d') : '-';
            $hari     = $tanggalObj ? $tanggalObj->locale('id')->translatedFormat('l') : '-';
            $golongan = ((int) ($row->total_penerima_a ?? 0) > 0) ? 'A' : 'B';

            if ($tanggal !== $currentDate) {
                // Close previous group
                if ($groupStartIdx !== null) {
                    $groups[] = ['start' => $groupStartIdx, 'end' => $index - 1];
                }
                $currentDate   = $tanggal;
                $dateNo++;
                $dayCounter    = 1;
                $groupStartIdx = $index;
            } else {
                $dayCounter++;
            }

            return [
                $dateNo,   // same number for same date
                $tanggal,
                $hari,
                $dayCounter,
                $golongan,
                $row->nama_karbohidrat,
                $row->nama_protein,
                $row->nama_sayur,
                $row->nama_buah,
                $row->nama_susu,
                (int) ($row->total_penerima ?? 0),
                (float) ($row->energi ?? 0),
                (float) ($row->protein_gizi ?? 0),
                (float) ($row->lemak ?? 0),
                (float) ($row->karbohidrat_gizi ?? 0),
                (float) ($row->serat ?? 0),
                (float) ($row->natrium ?? 0),
            ];
        });

        // Close last group
        if ($groupStartIdx !== null) {
            $groups[] = ['start' => $groupStartIdx, 'end' => $data->count() - 1];
        }

        // Build merge ranges for groups that span more than 1 row
        $this->mergeRanges = [];
        foreach ($groups as $g) {
            if ($g['end'] > $g['start']) {
                $sheetStart = $g['start'] + $headerRowCount + 1; // 1-indexed
                $sheetEnd   = $g['end']   + $headerRowCount + 1;
                $this->mergeRanges[] = [$sheetStart, $sheetEnd];
            }
        }

        $this->lastDataRow = $headerRowCount + max($data->count(), 1);

        return $header->concat($data);
    }
}
