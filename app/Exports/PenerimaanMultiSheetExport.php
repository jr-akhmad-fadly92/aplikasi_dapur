<?php

namespace App\Exports;

use App\Models\DataDapur;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenerimaanMultiSheetExport implements WithMultipleSheets
{
    protected $tanggal;

    public function __construct($tanggal = null)
    {
        $this->tanggal = $tanggal ?? date('Y-m-d');
    }

    public function sheets(): array
    {
        $sheets = [];
        
        for ($i = 1; $i <= 6; $i++) {
            $sheets[] = new PenerimaanSheet($i, $this->tanggal);
        }
        
        return $sheets;
    }
}

class PenerimaanSheet implements FromView, WithTitle, ShouldAutoSize, WithStyles
{
    protected $sheetNumber;
    protected $tanggal;

    public function __construct($sheetNumber, $tanggal)
    {
        $this->sheetNumber = $sheetNumber;
        $this->tanggal = $tanggal;
    }

    /**
     * Calculate rice packing breakdown: 25kg, 5kg, 1kg units
     */
    private function calculateRicePacking($totalKg)
    {
        $packing = ['25kg' => 0, '5kg' => 0, '1kg' => 0];
        $remaining = (int) $totalKg;

        // Per 72kg batch: 25kg x2, 5kg x4, 1kg x2
        $fullBatches = intdiv($remaining, 72);
        $packing['25kg'] += $fullBatches * 2;
        $packing['5kg'] += $fullBatches * 4;
        $packing['1kg'] += $fullBatches * 2;
        $remaining = $remaining % 72;

        // Handle remainder
        $packing['25kg'] += intdiv($remaining, 25);
        $remaining = $remaining % 25;
        $packing['5kg'] += intdiv($remaining, 5);
        $remaining = $remaining % 5;
        $packing['1kg'] += $remaining;

        return $packing;
    }

    public function view(): View
    {
        $dapur = DataDapur::first();

        $items = collect();

        // Map sheet to jenis (kategori bahan)
        $jenisMap = [
            1 => 1, // Karbohidrat
            2 => 2, // Lauk
            3 => 3, // Sayur
            4 => 4, // Buah
            5 => 5, // Penunjang
            6 => 6, // Bumbu dan lainnya
        ];

        $jenis = $jenisMap[$this->sheetNumber] ?? null;

        if ($jenis) {
            $items = DB::table('tb_po_bahan')
                ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
                ->join('tb_po', 'tb_po_bahan.id_po', '=', 'tb_po.id')
                ->join('tb_satuan', 'tb_po_bahan.satuan', '=', 'tb_satuan.id')
                ->where('tb_master_bahan.jenis', $jenis)
                ->when($this->tanggal, function ($query, $tanggal) {
                    return $query->whereDate('tb_po_bahan.tanggal_kedatangan', $tanggal);
                })
                ->orderBy('tb_po.nomor_po')
                ->orderBy('tb_po_bahan.tanggal_kedatangan')
                ->select([
                    'tb_po_bahan.tanggal_kedatangan',
                    'tb_master_bahan.bahan',
                    'tb_po.nomor_po',
                    'tb_po_bahan.jumlah_bahan',
                    'tb_satuan.satuan as nama_satuan',
                ])
                ->get();

            // Expand rice items into packing rows
            $expandedItems = collect();
            foreach ($items as $item) {
                if (preg_match('/\bberas\b/i', $item->bahan)) {
                    $packing = $this->calculateRicePacking($item->jumlah_bahan);
                    $jamFormat = \Carbon\Carbon::parse($item->tanggal_kedatangan)->format('H:i');

                    for ($i = 0; $i < $packing['25kg']; $i++) {
                        $expandedItems->push((object) [
                            'tanggal_kedatangan' => $jamFormat,
                            'bahan' => $item->bahan,
                            'nomor_po' => $item->nomor_po,
                            'jumlah_bahan' => 25,
                            'nama_satuan' => 'kg',
                        ]);
                    }
                    for ($i = 0; $i < $packing['5kg']; $i++) {
                        $expandedItems->push((object) [
                            'tanggal_kedatangan' => $jamFormat,
                            'bahan' => $item->bahan,
                            'nomor_po' => $item->nomor_po,
                            'jumlah_bahan' => 5,
                            'nama_satuan' => 'kg',
                        ]);
                    }
                    for ($i = 0; $i < $packing['1kg']; $i++) {
                        $expandedItems->push((object) [
                            'tanggal_kedatangan' => $jamFormat,
                            'bahan' => $item->bahan,
                            'nomor_po' => $item->nomor_po,
                            'jumlah_bahan' => 1,
                            'nama_satuan' => 'kg',
                        ]);
                    }
                } else {
                    $expandedItems->push($item);
                }
            }
            $items = $expandedItems;
        }

        return view('penerimaan.pdf.penerimaan_' . $this->sheetNumber, [
            'tanggal' => $this->tanggal,
            'dapur' => $dapur,
            'items' => $items,
        ]);
    }

    public function title(): string
    {
        $titles = [
            1 => 'Beras (Karbohidrat)',
            2 => 'Ayam (Lauk)',
            3 => 'Sayur',
            4 => 'Buah',
            5 => 'Penunjang',
            6 => 'Bumbu dan Lainnya'
        ];
        
        return $titles[$this->sheetNumber] ?? 'Sheet ' . $this->sheetNumber;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
