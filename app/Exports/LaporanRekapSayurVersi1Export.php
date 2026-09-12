<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanRekapSayurVersi1Export implements FromView, ShouldAutoSize, WithStyles
{
    protected $tanggal_awal;
    protected $tanggal_akhir;

    public function __construct($tanggal_awal = null, $tanggal_akhir = null)
    {
        $this->tanggal_awal = $tanggal_awal ?? '2025-12-01';
        $this->tanggal_akhir = $tanggal_akhir ?? '2025-12-21';
    }

    public function view(): View
    {
        $records = DB::table('tb_menu_bahan as tmb')
            ->join('tb_master_bahan as mb', 'tmb.bahan_id', '=', 'mb.id')
            ->join('tb_resep as tr', 'tmb.menu_id', '=', 'tr.id')
            ->join('golongan_bahan as gb', 'gb.bahan_id', '=', 'mb.id')
            ->join('golongan as g', 'g.id', '=', 'gb.golongan_id')
            ->join('rincian_menu_harian as rmh', function ($join) {
                $join->on('rmh.id_bahan', '=', 'mb.id')
                     ->on('rmh.id_resep', '=', 'tr.id');
            })
            ->join('tb_menu as tm', 'rmh.id_menu_harian', '=', 'tm.id')
            ->where('g.parent_id', 4)
            ->where('tmb.status_bahan_baku', 1)
            ->whereBetween('tm.tanggal_kirim', [$this->tanggal_awal, $this->tanggal_akhir])
            ->select(
                'mb.bahan as bahan',
                'tr.nama_resep as resep',
                'gb.golongan_id',
                'g.golongan as golongan',
                'g.parent_id',
                'tm.tanggal_kirim'
            )
            ->groupBy(
                'tm.tanggal_kirim',
                'rmh.id_menu_harian',
                'rmh.id_resep',
                'rmh.id_bahan',
                'mb.bahan',
                'tr.nama_resep',
                'gb.golongan_id',
                'g.golongan',
                'g.parent_id'
            )
            ->orderBy('tm.tanggal_kirim')
            ->orderBy('mb.bahan')
            ->orderBy('tr.nama_resep')
            ->get();

        $grouped = [];
        $index = 1;

        foreach ($records as $row) {
            if (!isset($grouped[$row->golongan])) {
                $grouped[$row->golongan] = [
                    'reseps' => [],
                    'rowIndex' => $index,
                ];
                $index++;
            }
            if (!in_array($row->resep, $grouped[$row->golongan]['reseps'], true)) {
                $grouped[$row->golongan]['reseps'][] = $row->resep;
            }
        }

        $dapur = DB::table('tb_data_dapur')->first();

        return view('exports.laporan_rekap_karbo_versi_1', [
            'grouped' => $grouped,
            'dapur' => $dapur,
            'tanggal_awal' => $this->tanggal_awal,
            'tanggal_akhir' => $this->tanggal_akhir,
            'judul' => 'Sayur',
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
