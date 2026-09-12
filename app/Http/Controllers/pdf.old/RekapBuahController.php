<?php

namespace App\Http\Controllers\pdf;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RekapBuahController extends Controller
{
    public function cetak()
    {
        // data dummy (bisa diubah jadi dinamis)
        $data = [
            'nama_penyusun' => 'Joko Priyo',
            'tanggal_print' => now()->format('d-m-Y H:i:s'),
            'pilihan_lauk' => [
                [
                    'no'           => 1,
                    'nama_lauk'    => 'Apel',
                    'berat'        => 100.00,
                    'satuan'       => 'kg',
                    'jenis'        => 'Buah',
                    'hasil_matang' => 2631.00,
                    'persen'       => '0%',
                ],
                // baris kosong
                ['no'=>2,'nama_lauk'=>'-','berat'=>0,'satuan'=>'kg','jenis'=>'Buah','hasil_matang'=>0,'persen'=>'0%'],
                ['no'=>3,'nama_lauk'=>'-','berat'=>0,'satuan'=>'kg','jenis'=>'Buah','hasil_matang'=>0,'persen'=>'0%'],
                ['no'=>4,'nama_lauk'=>'-','berat'=>0,'satuan'=>'kg','jenis'=>'Buah','hasil_matang'=>0,'persen'=>'0%'],
                ['no'=>5,'nama_lauk'=>'-','berat'=>0,'satuan'=>'kg','jenis'=>'Buah','hasil_matang'=>0,'persen'=>'0%'],
            ],
            'resep' => [
                [
                    'no'     => 17,
                    'lauk'   => '-',
                    'jumlah' => 2631.00,
                    'satuan' => 'kg'
                ]
            ]
        ];

        $pdf = Pdf::loadView('pdf.rekapbuah', $data)->setPaper('a4', 'landscape');
        return $pdf->stream('rekap_buah.pdf');
    }
}