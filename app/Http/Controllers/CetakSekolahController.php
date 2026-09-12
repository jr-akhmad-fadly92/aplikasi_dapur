<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\DB; // Menggunakan DB Facade

class CetakSekolahController extends Controller
{
    public function cetakSekolah()
    {
        // Mengambil data langsung dari tabel menggunakan DB Facade
        $dataSekolah = DB::table('tb_data_sekolah')->get();

        // Mengirim data ke view
        $data = [
            'dataSekolah' => $dataSekolah
        ];

        // Memuat view 'pdf.laporan_sekolah' dan mengirimkan datanya
        $pdf = Pdf::loadView('pdf.cetaksekolah', $data);

        // Mengirimkan file PDF untuk diunduh
        return $pdf->download('laporan_data_sekolah.pdf');
    }
}