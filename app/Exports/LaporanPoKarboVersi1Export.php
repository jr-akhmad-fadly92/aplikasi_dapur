<?php

namespace App\Exports;

use App\Models\DataDapur;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanPoKarboVersi1Export implements FromView, ShouldAutoSize, WithStyles
{
    protected string $tanggal_awal;
    protected string $tanggal_akhir;

    public function __construct(?string $tanggal_awal = null, ?string $tanggal_akhir = null)
    {
        // Default per permintaan: 1 - 16 Desember 2025
        $this->tanggal_awal = $tanggal_awal ?? '2025-12-01';
        $this->tanggal_akhir = $tanggal_akhir ?? '2025-12-16';
    }

    public function view(): View
    {
        $tanggal_awal = Carbon::parse($this->tanggal_awal)->format('Y-m-d');
        $tanggal_akhir = Carbon::parse($this->tanggal_akhir)->format('Y-m-d');

        $dapur = DataDapur::first();

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
            ->where('tr.id_komponen_sehat', 1)
            ->whereBetween('pob.tanggal_digunakan', [$tanggal_awal, $tanggal_akhir])
            ->where(function ($q) {
                $q->whereNull('tmb.status_bahan_baku')
                    ->orWhere('tmb.status_bahan_baku', '!=', 3);
            })
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

        // Hitung count per bahan per golongan
        $summary = [];
        foreach ($records as $row) {
            $key = $row->bahan . '|' . $row->jenis_golongan;
            if (!isset($summary[$key])) {
                $summary[$key] = [
                    'bahan' => $row->bahan,
                    'golongan' => $row->jenis_golongan,
                    'count_pax_a' => 0,
                    'count_pax_b' => 0,
                ];
            }
            if ($row->jenis_golongan === 'A') {
                $summary[$key]['count_pax_a']++;
            } else {
                $summary[$key]['count_pax_b']++;
            }
        }
        usort($summary, function ($a, $b) {
            // Sort by golongan first (A before B), then by bahan
            if ($a['golongan'] !== $b['golongan']) {
                return $a['golongan'] === 'A' ? -1 : 1;
            }
            return strcmp($a['bahan'], $b['bahan']);
        });

        return view('exports.laporan_po_karbo_versi_1', [
            'dapur' => $dapur,
            'records' => $records,
            'summary' => $summary,
            'tanggal_awal' => $this->tanggal_awal,
            'tanggal_akhir' => $this->tanggal_akhir,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }
}
