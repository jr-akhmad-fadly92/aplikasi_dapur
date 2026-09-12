<?php


namespace App\Http\Controllers\pdf;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RekapSayurController extends Controller
{
    /**
     * Menghasilkan laporan PDF Rekap Sayur dengan data hardcoded dari semua gambar.
     *
     * @return \Illuminate\Http\Response
     */
    public function generateRekapSayurAllImages()
    {
        // Data Hardcoded untuk Tabel Master Sayuran
        $sayurMasters = [
            (object)['no' => 3, 'nama_sayuran' => 'Labu Siam Parut', 'berat_bahan_baku_per_box' => 5.00, 'satuan' => 'kg', 'jenis' => 'Buah', 'hasil_matang_setelah_penyusutan_kg' => 4.5, 'persentase_penyusutan' => 10],
            (object)['no' => 4, 'nama_sayuran' => 'Sawi Putih', 'berat_bahan_baku_per_box' => 3.00, 'satuan' => 'kg', 'jenis' => 'Daun', 'hasil_matang_setelah_penyusutan_kg' => 2.4, 'persentase_penyusutan' => 20],
            (object)['no' => 5, 'nama_sayuran' => 'Wortel', 'berat_bahan_baku_per_box' => 5.00, 'satuan' => 'kg', 'jenis' => 'Umbi', 'hasil_matang_setelah_penyusutan_kg' => 4.75, 'persentase_penyusutan' => 5],
            (object)['no' => 6, 'nama_sayuran' => 'Buncis', 'berat_bahan_baku_per_box' => 5.00, 'satuan' => 'kg', 'jenis' => 'Batang', 'hasil_matang_setelah_penyusutan_kg' => 4.5, 'persentase_penyusutan' => 10],
            (object)['no' => 7, 'nama_sayuran' => 'Labu Siam Kotak', 'berat_bahan_baku_per_box' => 4.00, 'satuan' => 'kg', 'jenis' => 'Buah', 'hasil_matang_setelah_penyusutan_kg' => 3.8, 'persentase_penyusutan' => 5],
            (object)['no' => 8, 'nama_sayuran' => 'Kacang Panjang', 'berat_bahan_baku_per_box' => 5.00, 'satuan' => 'kg', 'jenis' => 'Polong', 'hasil_matang_setelah_penyusutan_kg' => 4.75, 'persentase_penyusutan' => 5],
            (object)['no' => 9, 'nama_sayuran' => 'Pare/Jagung Muda', 'berat_bahan_baku_per_box' => 5.00, 'satuan' => 'kg', 'jenis' => 'Buah', 'hasil_matang_setelah_penyusutan_kg' => 4.75, 'persentase_penyusutan' => 5],
            (object)['no' => 10, 'nama_sayuran' => 'Jagung Polii', 'berat_bahan_baku_per_box' => 5.00, 'satuan' => 'kg', 'jenis' => 'Buah', 'hasil_matang_setelah_penyusutan_kg' => 4.75, 'persentase_penyusutan' => 5],
            (object)['no' => 11, 'nama_sayuran' => 'Pakcoy', 'berat_bahan_baku_per_box' => 3.00, 'satuan' => 'kg', 'jenis' => 'Daun', 'hasil_matang_setelah_penyusutan_kg' => 2.4, 'persentase_penyusutan' => 20],
            (object)['no' => 12, 'nama_sayuran' => 'Kol / Kubis', 'berat_bahan_baku_per_box' => 3.00, 'satuan' => 'kg', 'jenis' => 'Daun', 'hasil_matang_setelah_penyusutan_kg' => 2.4, 'persentase_penyusutan' => 20],
            (object)['no' => 13, 'nama_sayuran' => 'Sawi Hijau', 'berat_bahan_baku_per_box' => 3.00, 'satuan' => 'kg', 'jenis' => 'Daun', 'hasil_matang_setelah_penyusutan_kg' => 2.4, 'persentase_penyusutan' => 20],
            (object)['no' => 14, 'nama_sayuran' => 'Bunga Kol', 'berat_bahan_baku_per_box' => 4.00, 'satuan' => 'kg', 'jenis' => 'Bunga', 'hasil_matang_setelah_penyusutan_kg' => 3.8, 'persentase_penyusutan' => 5],
        ];

        // Data Resep & Penerimaan
        $resepPenerimaan = [
            (object)['no_resep' => 18, 'sayuran' => 'Labu Siam Kotak', 'jumlah' => 2.00, 'satuan_resep' => 'kg', 'jml_box' => 1, 'total' => 5, 'isi_box_kg' => 4.00, 'persentase_isi_box' => 20],
            (object)['no_resep' => 19, 'sayuran' => 'Wortel', 'jumlah' => 5.00, 'satuan_resep' => 'kg', 'jml_box' => 1, 'total' => 1, 'isi_box_kg' => 5.00, 'persentase_isi_box' => 5],
            (object)['no_resep' => 20, 'sayuran' => '', 'jumlah' => 0.00, 'satuan_resep' => 'kg', 'jml_box' => null, 'total' => null, 'isi_box_kg' => null, 'persentase_isi_box' => '#VALUE!'],
            (object)['no_resep' => 21, 'sayuran' => '', 'jumlah' => 0.00, 'satuan_resep' => 'kg', 'jml_box' => null, 'total' => null, 'isi_box_kg' => null, 'persentase_isi_box' => '#VALUE!'],
        ];

        // Data Kalkulasi
        $dataKalkulasi = [
            'jumlah_pax_buffer' => 2300,
            'label_jumlah_pax' => 'Jumlah Pax (Dan Buffer 1%)',
            'pemorsian_gram' => 80,
            'isian_sayur_labu_siam_nama' => 'Labu Siam Kotak',
            'isian_sayur_labu_siam_kg_mentah' => 20,
            'isian_sayur_labu_siam_kg_matang' => 19,
            'isian_sayur_labu_siam_persen' => 19,
            'isian_sayur_labu_siam_jenis' => 'Buah',
            'isian_sayur_wortel_nama' => 'Wortel',
            'isian_sayur_wortel_kg_mentah' => 5,
            'isian_sayur_wortel_kg_matang' => 4.75,
            'isian_sayur_wortel_persen' => 4.75,
            'isian_sayur_wortel_jenis' => 'Umbi',
            'isian_sayur_total_kg_mentah' => 25,
            'isian_sayur_total_kg_matang' => 25.75,
            'isian_sayur_total_persen' => 25.75,
            'kebutuhan_bahan_baku_kg' => 184,
            'kebutuhan_bahan_baku_matang_kali_masak' => 1,
            'kebutuhan_sitno_persen' => 87.99,
            'disamakan_maksimal_selisih_text' => 'disamakan maksimal selisih -0.25% sd 0',
            'disamakan_maksimal_selisih_value' => 25.0,
        ];

        // Tambahkan informasi pembuat dan penyetuju
        $dibuat_oleh = 'Asisten Dapur';
        $disetujui_oleh = 'Kepala Dapur';

        // Generate PDF
        $pdf = Pdf::loadView('pdf.rekapsayur', [
            'sayurMasters' => $sayurMasters,
            'resepPenerimaan' => $resepPenerimaan,
            'dataKalkulasi' => $dataKalkulasi,
            'dibuat_oleh' => $dibuat_oleh,
            'disetujui_oleh' => $disetujui_oleh,
        ]);

        $pdf->setPaper('a4', 'landscape');

            return $pdf->download('rekap.sayur_' . date('Ymd_His') . '.pdf');
    }
}
