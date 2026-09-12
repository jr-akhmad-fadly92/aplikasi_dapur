<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use App\Models\DataDapur;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class LaporanPoSayurVersi1Export implements FromView, ShouldAutoSize, WithStyles
{
    protected $tanggal_awal;
    protected $tanggal_akhir;

    public function __construct($tanggal_awal, $tanggal_akhir)
    {
        $this->tanggal_awal = $tanggal_awal;
        $this->tanggal_akhir = $tanggal_akhir;
    }

    public function view(): View
    {
        $tanggal_awal = Carbon::parse($this->tanggal_awal)->format('Y-m-d');
        $tanggal_akhir = Carbon::parse($this->tanggal_akhir)->format('Y-m-d');

        $dapur = DataDapur::first();
        // Query untuk mendapatkan records dengan filter id_komponen_sehat = 3 (Sayur)
        $records = DB::table('rincian_menu_harian as rmh')
            ->join('tb_resep as tr', 'tr.id', '=', 'rmh.id_resep')
            ->join('tb_master_bahan as mb', 'mb.id', '=', 'rmh.id_bahan')
            ->leftJoin('tb_menu_bahan as tmb', function ($join) {
                $join->on('tmb.menu_id', '=', 'rmh.id_resep')
                    ->on('tmb.bahan_id', '=', 'rmh.id_bahan');
            })
            ->join('tb_po_bahan as pob', 'pob.id_rincian_bahan', '=', 'rmh.id')
            ->join('tb_po as po', 'po.id', '=', 'pob.id_po')
            ->leftJoin('rincian_sekolah as rs', 'rs.id_menu_harian', '=', 'rmh.id_menu_harian')
            ->where('tr.id_komponen_sehat', 3)
            ->whereBetween('pob.tanggal_digunakan', [$tanggal_awal, $tanggal_akhir])
            ->whereIn('tmb.status_bahan_baku', [1, 2, 4, 5])
            ->groupBy(
                'rmh.id',
                'rmh.id_bahan',
                'mb.bahan',
                'rmh.jumlah',
                'tr.nama_resep',
                'tmb.status_bahan_baku',
                'pob.tanggal_digunakan',
                'po.nomor_po'
            )
            ->orderBy('pob.tanggal_digunakan')
            ->orderBy('mb.bahan')
            ->select(
                'rmh.id as rincian_id',
                'rmh.id_bahan',
                'tr.nama_resep',
                'mb.bahan',
                'rmh.jumlah',
                'tmb.status_bahan_baku',
                'pob.tanggal_digunakan',
                'po.nomor_po',
                DB::raw('SUM(rs.jumlah_penerima_a) as total_penerima_a'),
                DB::raw('SUM(rs.jumlah_penerima_b) as total_penerima_b')
            )
            ->get()
            ->map(function ($row) {
                $row->jenis_golongan = ($row->total_penerima_a ?? 0) == 0 ? 'B' : 'A';
                return $row;
            });

        // Hitung summary: count berapa kali bahan digunakan per golongan
        $summary = [];
        foreach ($records as $row) {
            $key = $row->bahan;
            
            if (!isset($summary[$key])) {
                // Tentukan golongan berdasarkan penerima mana yang > 0
                $golongan = ($row->total_penerima_a > 0) ? 'A' : 'B';
                
                $summary[$key] = [
                    'bahan' => $row->bahan,
                    'golongan' => $golongan,
                    'count_pax_a' => 0,
                    'count_pax_b' => 0,
                ];
            }
            
            // Count occurrences
            if ($row->total_penerima_a > 0) {
                $summary[$key]['count_pax_a']++;
            } else {
                $summary[$key]['count_pax_b']++;
            }
        }

        // Convert to indexed array
        $summary = array_values($summary);

        // Sort: Golongan A first, then B, alphabetically by bahan
        usort($summary, function($a, $b) {
            // First sort by golongan (A before B)
            if ($a['golongan'] !== $b['golongan']) {
                return $a['golongan'] === 'A' ? -1 : 1;
            }
            // Then sort alphabetically by bahan
            return strcmp($a['bahan'], $b['bahan']);
        });

        // Get dapur info

        return view('exports.laporan_po_sayur_versi_1', [
            'records' => $records,
            'summary' => $summary,
            'dapur' => $dapur,
            'tanggal_awal' => $this->tanggal_awal,
            'tanggal_akhir' => $this->tanggal_akhir,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
