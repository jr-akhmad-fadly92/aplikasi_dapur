<?php

namespace App\Exports;

use App\Models\DataDapur;
use App\Models\Menu;
use App\Models\HasilMasak;
use App\Models\Resep;
use App\Models\TbPoBahan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class LaporanHasilMasakExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $start;
    protected $count;

    public function __construct( $data, $start, $count)
    {
        $this->data  = $data;
        $this->start = $start;
        $this->count  = $count;
    }

    public function view(): View
    {
        $dapur = DataDapur::first();
        $data_non_pangan  = DB::table('tb_po_bahan as pb')
            ->join('tb_master_bahan as mb', 'pb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as s', 'pb.satuan', '=', 's.id')
            ->select(
                'mb.bahan',
                'pb.jumlah_bahan as total_jumlah',
                's.satuan as nama_satuan'
            )
            ->whereBetween('pb.tanggal_kedatangan', [
            $this->start . ' 00:00:00',
            $this->start . ' 23:59:59'
            ])
            ->get();
        $menu  = Menu::where('tanggal_kirim', '2025-10-07')
            ->orderBy('id', 'asc') 
            ->first();
        $nama_karbo = Resep::where('id',$menu->karbohidrat)->first();
        $nama_lauk = Resep::where('id',$menu->protein)->first();
        $nama_sayur = Resep::where('id',$menu->sayur)->first();
        $nama_buah = Resep::where('id',$menu->buah)->first();
        $nama_suplemen = Resep::where('id',$menu->susu)->first();
        $menu2 = Menu::where('tanggal_kirim', '2025-10-07')
            ->orderBy('id', 'desc')
            ->first();

        $nama_karbo_2 = Resep::where('id', $menu2->karbohidrat)->first();
        $nama_lauk_2 = Resep::where('id', $menu2->protein)->first();
        $nama_sayur_2 = Resep::where('id', $menu2->sayur)->first();
        $nama_buah_2 = Resep::where('id', $menu2->buah)->first();
        $nama_suplemen_2 = Resep::where('id', $menu2->susu)->first();

        $data_karbo = HasilMasak::where('id_menu', $menu->id)->where('id_komponen_sehat', 1)->get();
        $data_lauk = HasilMasak::where('id_menu', $menu->id)->where('id_komponen_sehat', 2)->get();
        $data_sayur = HasilMasak::where('id_menu', $menu->id)->where('id_komponen_sehat', 3)->get();
        $data_buah = HasilMasak::where('id_menu', $menu->id)->where('id_komponen_sehat', 4)->get();
        $data_suplemen = HasilMasak::where('id_menu', $menu->id)->where('id_komponen_sehat', 5)->get();

        $data_karbo_2 = HasilMasak::where('id_menu', $menu2->id)->where('id_komponen_sehat', 1)->get();
        $data_lauk_2 = HasilMasak::where('id_menu', $menu2->id)->where('id_komponen_sehat', 2)->get();
        $data_sayur_2 = HasilMasak::where('id_menu', $menu2->id)->where('id_komponen_sehat', 3)->get();
        $data_buah_2 = HasilMasak::where('id_menu', $menu2->id)->where('id_komponen_sehat', 4)->get();
        $data_suplemen_2 = HasilMasak::where('id_menu', $menu2->id)->where('id_komponen_sehat', 5)->get();



        //$data_karbo = HasilMasak::where('id_menu', $menu->id)->where('id_komponen_sehat', 1)->get();
        return view('exports.Laporan_hasil_masak', [
            'karbo' => $data_karbo,
            'lauk' => $data_lauk,
            'sayur' => $data_sayur,
            'buah' => $data_buah,
            'suplemen' => $data_suplemen,
            'nama_karbo' => $nama_karbo,
            'nama_lauk' => $nama_lauk,
            'nama_sayur' => $nama_sayur,
            'nama_buah' => $nama_buah,
            'nama_suplemen' => $nama_suplemen,

            'nama_karbo_2' => $nama_karbo_2,
            'nama_lauk_2' => $nama_lauk_2,
            'nama_sayur_2' => $nama_sayur_2,
            'nama_buah_2' => $nama_buah_2,
            'nama_suplemen_2' => $nama_suplemen_2,
            'data_karbo_2' => $data_karbo_2,
            'data_lauk_2' => $data_lauk_2,
            'data_sayur_2' => $data_sayur_2,
            'data_buah_2' => $data_buah_2,
            'data_suplemen_2' => $data_suplemen_2,

            'data'  => $this->data,
            'data_non_pangan'  => $data_non_pangan,
            'count'  => $this->count,
            'start' => $this->start,
            'dapur' => $dapur
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Hitung jumlah baris
       
        $lastRow2 = count($this->data) + 5; // 6 = header + judul
        $jumlah_row =35;
        return [
            // Header tebal
            4    => ['font' => ['bold' => true]],

            // Border semua tabel
            "B5:M{$jumlah_row}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color'       => ['argb' => '000000'],
                    ],
                ],
            ],
        ];
    }
}


