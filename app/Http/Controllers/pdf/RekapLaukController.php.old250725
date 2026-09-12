<?php


namespace App\Http\Controllers\pdf;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RekapLaukController extends Controller
{
    /**
     * Menghasilkan laporan PDF untuk Lauk menggunakan data hardcoded.
     *
     * @return \Illuminate\Http\Response
     */
    public function generateRekapLaukAllImages()
    {
        // Data Hardcoded untuk Tabel Master Lauk (DIPERBAIKI DI SINI)
        $laukMasters = [
            (object)['no' => 2, 'nama_lauk' => 'Ayam Bone', 'berat_bahan_baku_per_box' => 100.00, 'satuan' => 'Potong', 'jenis' => 'Unggas', 'hasil_matang_setelah_penyusutan_kg' => 90.00, 'persentase_penyusutan' => 10],
            (object)['no' => 3, 'nama_lauk' => 'Ayam Fillet Boneless', 'berat_bahan_baku_per_box' => 200.00, 'satuan' => 'Potong', 'jenis' => 'Unggas', 'hasil_matang_setelah_penyusutan_kg' => 180.00, 'persentase_penyusutan' => 10],
            (object)['no' => 4, 'nama_lauk' => 'Ayam Giling', 'berat_bahan_baku_per_box' => 10.00, 'satuan' => 'kg', 'jenis' => 'Unggas', 'hasil_matang_setelah_penyusutan_kg' => 9.00, 'persentase_penyusutan' => 10],
            (object)['no' => 5, 'nama_lauk' => 'Ikan Patin', 'berat_bahan_baku_per_box' => 100.00, 'satuan' => 'Potong', 'jenis' => 'Ikan', 'hasil_matang_setelah_penyusutan_kg' => 95.00, 'persentase_penyusutan' => 5],
            (object)['no' => 6, 'nama_lauk' => 'Ikan Lele', 'berat_bahan_baku_per_box' => 100.00, 'satuan' => 'Ekor', 'jenis' => 'Ikan', 'hasil_matang_setelah_penyusutan_kg' => 95.00, 'persentase_penyusutan' => 5],
            (object)['no' => 7, 'nama_lauk' => 'Telur', 'berat_bahan_baku_per_box' => 100.00, 'satuan' => 'Ekor', 'jenis' => 'Telur', 'hasil_matang_setelah_penyusutan_kg' => 98.00, 'persentase_penyusutan' => 2],
            // Pastikan baris kosong ini juga memiliki semua properti yang didefinisikan!
            (object)['no' => 8, 'nama_lauk' => '', 'berat_bahan_baku_per_box' => null, 'satuan' => '', 'jenis' => '', 'hasil_matang_setelah_penyusutan_kg' => null, 'persentase_penyusutan' => null],
            (object)['no' => 9, 'nama_lauk' => '', 'berat_bahan_baku_per_box' => null, 'satuan' => '', 'jenis' => '', 'hasil_matang_setelah_penyusutan_kg' => null, 'persentase_penyusutan' => null],
            (object)['no' => 10, 'nama_lauk' => '', 'berat_bahan_baku_per_box' => null, 'satuan' => '', 'jenis' => '', 'hasil_matang_setelah_penyusutan_kg' => null, 'persentase_penyusutan' => null],
            (object)['no' => 11, 'nama_lauk' => '', 'berat_bahan_baku_per_box' => null, 'satuan' => '', 'jenis' => '', 'hasil_matang_setelah_penyusutan_kg' => null, 'persentase_penyusutan' => null],
        ];

        // ... (sisanya sama, tidak ada perubahan pada $resepPenerimaan dan $dataKalkulasi) ...
        $resepPenerimaan = [
            (object)['no_resep' => 17, 'lauk' => 'Ayam Bone', 'jumlah' => 16.25, 'satuan_resep' => 'kg', 'jml_box' => 1, 'total_box' => 1, 'isi_box_kg' => 16.25],
            (object)['no_resep' => 18, 'lauk' => 'Ayam Giling', 'jumlah' => 5.00, 'satuan_resep' => 'kg', 'jml_box' => 1, 'total_box' => 4, 'isi_box_kg' => 5.00],
        ];

        $dataKalkulasi = [
            'jumlah_pax_buffer' => 4000,
            'pemorsian_gram' => 45,
            'isian_lauk_ayam_bone_nama' => 'Ayam bone',
            'isian_lauk_ayam_bone_kg_mentah' => null,
            'isian_lauk_ayam_bone_kg_matang' => 65,
            'isian_lauk_ayam_giling_nama' => 'Ayam giling',
            'isian_lauk_ayam_giling_kg_mentah' => null,
            'isian_lauk_ayam_giling_kg_matang' => 20,
            'isian_lauk_total_kg_mentah' => 85,
            'isian_lauk_total_kg_matang' => 85,
            'kebutuhan_bahan_baku_kg' => 160,
            'kebutuhan_bahan_baku_matang_kali_masak' => 1,
            'kebutuhan_titino_persen' => null,
            'disamakan_maksimal_selisih_text' => '',
            'disamakan_maksimal_selisih_value' => null,
        ];

        $pdf = Pdf::loadView('pdf.rekaplauk', compact(
            'laukMasters',
            'resepPenerimaan',
            'dataKalkulasi'
        ));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('rekap.lauk_' . date('Ymd_His') . '.pdf');
    }
}