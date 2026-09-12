<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenerimaanChecklist2Export implements WithMultipleSheets
{
    protected $tanggal;
    protected $jamPelayanan;

    public function __construct($tanggal = null, $jamPelayanan = null)
    {
        // Default tanggal jika tidak diisi: 2025-12-19
        $this->tanggal = $tanggal ?? '2025-12-19';
        $this->jamPelayanan = ($jamPelayanan === 'semua') ? null : $jamPelayanan; // null = tanpa filter jam
    }

    public function sheets(): array
    {
        $sheets = [];

        for ($i = 1; $i <= 6; $i++) {
            $sheets[] = new PenerimaanChecklist2Sheet($i, $this->tanggal, $this->jamPelayanan);
        }

        return $sheets;
    }
}

class PenerimaanChecklist2Sheet implements FromView, WithTitle, ShouldAutoSize, WithStyles
{
    protected $sheetNumber;
    protected $tanggal;
    protected $jamPelayanan;

    public function __construct($sheetNumber, $tanggal, $jamPelayanan = null)
    {
        $this->sheetNumber = $sheetNumber;
        $this->tanggal = $tanggal;
        $this->jamPelayanan = ($jamPelayanan === 'semua') ? null : $jamPelayanan;
    }

    /**
     * Calculate rice packing breakdown: 25kg, 5kg, 1kg units
     * Returns array: ['25kg' => count, '5kg' => count, '1kg' => count]
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
        // First: use 25kg
        $packing['25kg'] += intdiv($remaining, 25);
        $remaining = $remaining % 25;

        // Then: use 5kg
        $packing['5kg'] += intdiv($remaining, 5);
        $remaining = $remaining % 5;

        // Finally: use 1kg
        $packing['1kg'] += $remaining;

        return $packing;
    }

    public function view(): View
    {
        $dapur = DB::table('tb_data_dapur')->first();

        $items = collect();

        // Map sheet ke jenis (kategori bahan)
        $jenisMap = [
            1 => 1, // Karbohidrat
            2 => 2, // Lauk
            3 => 3, // Sayur
            4 => 4, // Buah
            5 => 5, // Pendamping
            6 => 6, // Bumbu
        ];

        $jenis = $jenisMap[$this->sheetNumber] ?? null;

        if ($jenis) {
            $query = DB::table('tb_po_bahan as p')
        ->join('tb_master_bahan as b', 'p.id_bahan', '=', 'b.id')
        ->join('tb_po', 'p.id_po', '=', 'tb_po.id')
        ->leftJoin('tb_satuan as s', 'p.satuan', '=', 's.id')
        ->join('rincian_menu_harian as r', 'p.id_rincian_bahan', '=', 'r.id')
        ->join('tb_menu as m', 'r.id_menu_harian', '=', 'm.id')
        ->where('b.jenis', $jenis)
        ->whereDate('p.tanggal_kedatangan', $this->tanggal)
        ->orderBy('tb_po.nomor_po')
        ->orderBy('p.tanggal_kedatangan')
        ->select([
            'p.tanggal_kedatangan',
            'b.bahan',
            'tb_po.nomor_po',
            'p.jumlah_bahan',
            's.satuan as nama_satuan',
            'm.tanggal_kirim',
            DB::raw("DATE_FORMAT(m.jam_pelayanan, '%H:%i') as jam_pelayanan")
        ])
        ;

           

            // Filter jam jika diisi (format HH:MM)
            if (!empty($this->jamPelayanan)) {
                $query->where(DB::raw('DATE_FORMAT(m.jam_pelayanan, "%H:%i")'), $this->jamPelayanan);
            }

            $items = $query->get();

            // Expand rice items into packing rows
            $expandedItems = collect();
            foreach ($items as $item) {
                // Check if this is a rice item (Beras)
                if (preg_match('/\bberas\b/i', $item->bahan)) {
                    // Apply packing logic
                    $packing = $this->calculateRicePacking($item->jumlah_bahan);
                    $jamFormat = \Carbon\Carbon::parse($item->tanggal_kedatangan)->format('H:i');

                    // Add 25kg rows
                    for ($i = 0; $i < $packing['25kg']; $i++) {
                        $expandedItems->push((object) [
                            'tanggal_kedatangan' => $jamFormat,
                            'bahan' => $item->bahan,
                            'nomor_po' => $item->nomor_po,
                            'jumlah_bahan' => 25,
                            'nama_satuan' => 'kg',
                            'tanggal_kirim' => $item->tanggal_kirim,
                            'jam_pelayanan' => $item->jam_pelayanan,
                        ]);
                    }

                    // Add 5kg rows
                    for ($i = 0; $i < $packing['5kg']; $i++) {
                        $expandedItems->push((object) [
                            'tanggal_kedatangan' => $jamFormat,
                            'bahan' => $item->bahan,
                            'nomor_po' => $item->nomor_po,
                            'jumlah_bahan' => 5,
                            'nama_satuan' => 'kg',
                            'tanggal_kirim' => $item->tanggal_kirim,
                            'jam_pelayanan' => $item->jam_pelayanan,
                        ]);
                    }

                    // Add 1kg rows
                    for ($i = 0; $i < $packing['1kg']; $i++) {
                        $expandedItems->push((object) [
                            'tanggal_kedatangan' => $jamFormat,
                            'bahan' => $item->bahan,
                            'nomor_po' => $item->nomor_po,
                            'jumlah_bahan' => 1,
                            'nama_satuan' => 'kg',
                            'tanggal_kirim' => $item->tanggal_kirim,
                            'jam_pelayanan' => $item->jam_pelayanan,
                        ]);
                    }
                } else {
                    // Non-rice items: keep as single row
                    $expandedItems->push($item);
                }
            }
            $items = $expandedItems;
        }

        return view('penerimaan.pdf.penerimaan_checklist_2_' . $this->sheetNumber, [
            'tanggal' => $this->tanggal,
            'dapur' => $dapur,
            'items' => $items,
            'sheetNumber' => $this->sheetNumber,
        ]);
    }

    public function title(): string
    {
        $titles = [
            1 => 'Karbohidrat',
            2 => 'Lauk',
            3 => 'Sayur',
            4 => 'Buah',
            5 => 'Pendamping',
            6 => 'Bumbu',
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
