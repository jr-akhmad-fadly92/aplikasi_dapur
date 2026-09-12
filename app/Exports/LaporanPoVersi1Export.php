<?php

namespace App\Exports;

use App\Models\DataDapur;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanPoVersi1Export implements FromView, ShouldAutoSize, WithStyles
{
    protected $tanggal_awal;
    protected $tanggal_akhir;

    public function __construct($tanggal_awal = null, $tanggal_akhir = null)
    {
        // Default: 1-21 Desember 2025
        $this->tanggal_awal = $tanggal_awal ?? '2025-12-01';
        $this->tanggal_akhir = $tanggal_akhir ?? '2025-12-21';
    }

    public function view(): View
    {
        $dapur = DataDapur::first();
        
        // Generate tanggal pelayanan (1-21 desember)
        $tanggal_list = [];
        $start = Carbon::parse($this->tanggal_awal);
        $end = Carbon::parse($this->tanggal_akhir);
        
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $tanggal_list[] = $date->format('Y-m-d');
        }
        
        // Ambil data PO dengan sample bahan
        $po_data = DB::table('tb_po')
            //->where('status', '!=', 0) // Non-deleted POs
            ->orderBy('tanggal_pengajuan', 'asc')
            ->get()
            ->map(function ($po) {
                // Ambil 1 sample bahan dari setiap PO
                $sample_bahan = DB::table('tb_po_bahan')
                    ->where('id_po', $po->id)
                    ->first();
                
                $po->tanggal_digunakan = $sample_bahan ? $sample_bahan->tanggal_digunakan : null;
                return $po;
            });
        
        // Susun data untuk ditampilkan per tanggal
        $po_per_tanggal = [];
        foreach ($tanggal_list as $tanggal) {
            $po_per_tanggal[$tanggal] = $po_data
                ->filter(function ($po) use ($tanggal) {
                    return $po->tanggal_digunakan && 
                           Carbon::parse($po->tanggal_digunakan)->format('Y-m-d') === $tanggal;
                })
                ->first();
        }

        return view('exports.laporan_po_versi_1', [
            'dapur' => $dapur,
            'tanggal_list' => $tanggal_list,
            'po_per_tanggal' => $po_per_tanggal,
            'tanggal_awal' => $this->tanggal_awal,
            'tanggal_akhir' => $this->tanggal_akhir
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }
}
