<?php


namespace App\Http\Controllers\pdf;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan Anda sudah menginstal barryvdh/laravel-dompdf

class RekapPendampingController extends Controller
{
    public function generateRekapPendampingAllImages()
    {
        // 1. Data Dummy untuk "Pilihan Nama Pendamping" (mirip master data)
        $pendampingMasters = [
            (object)['no' => 1, 'nama_pendamping' => 'Susu', 'berat_bahan_baku_per_box' => null, 'satuan' => 'Kardus', 'ket' => null, 'hasil_matang_setelah_penyusutan_kg' => null, 'persentase_penyusutan' => null],
            (object)['no' => 2, 'nama_pendamping' => 'Telur Puyuh', 'berat_bahan_baku_per_box' => 600, 'satuan' => 'Pcs', 'ket' => 'Box plastik', 'hasil_matang_setelah_penyusutan_kg' => null, 'persentase_penyusutan' => null],
            (object)['no' => 3, 'nama_pendamping' => 'Telur Ayam Rebus', 'berat_bahan_baku_per_box' => 200, 'satuan' => 'Pcs', 'ket' => 'Gastronom', 'hasil_matang_setelah_penyusutan_kg' => null, 'persentase_penyusutan' => null],
            (object)['no' => 4, 'nama_pendamping' => 'Tahu Pong Goreng', 'berat_bahan_baku_per_box' => 400, 'satuan' => 'Pcs', 'ket' => 'Gastronom', 'hasil_matang_setelah_penyusutan_kg' => null, 'persentase_penyusutan' => null],
            (object)['no' => 5, 'nama_pendamping' => 'Tempe', 'berat_bahan_baku_per_box' => 200, 'satuan' => 'Pcs', 'ket' => 'Box plastik', 'hasil_matang_setelah_penyusutan_kg' => null, 'persentase_penyusutan' => null],
        ];

        // 2. Data Dummy untuk "Isi manual jumlah pax & pemorsian" dan "Isian Pendamping"
        $dataKalkulasi = [
            'jumlah_pax_buffer' => 2000, // Dari gambar
            'pemorsian_gram' => null, // Tidak ada di gambar untuk pendamping
            'pemorsian_unit' => 'pcs', // Diasumsikan dari gambar tempe 2000 pcs

            // Data Isian Pendamping dari gambar
            'isian_pendamping_tempe_nama' => 'Tempe',
            'isian_pendamping_tempe_jumlah_mentah' => 2000,
            'isian_pendamping_tempe_satuan_mentah' => 'Pcs',
            'isian_pendamping_tempe_jumlah_matang' => 2000, // Asumsi sama jika tidak ada penyusutan eksplisit
            'isian_pendamping_tempe_satuan_matang' => 'Pcs',
            'isian_pendamping_tempe_jenis' => null, // Tidak ada di gambar

            // Jika ada pendamping lain, bisa ditambahkan di sini
            'isian_pendamping_total_jumlah_mentah' => 2000, // Total dari semua pendamping
            'isian_pendamping_total_jumlah_matang' => 2000, // Total dari semua pendamping (asumsi)

            'kebutuhan_bahan_baku_total' => 2000, // Angka 2000 di gambar
            'kebutuhan_bahan_baku_matang_kali_masak' => 100, // Angka 100 di gambar
            'kebutuhan_titino_persen' => null, // Tidak ada di gambar
            'disamakan_maksimal_selisih_text' => null,
            'disamakan_maksimal_selisih_value' => null,
        ];

        // 3. Data Dummy untuk "Resep & Penerimaan"
        $resepPenerimaan = [
            (object)['no_resep' => 1, 'pendamping' => 'Tempe', 'jumlah' => 2000, 'satuan_resep' => 'Pcs', 'jml_box' => 10, 'total_box' => 10, 'isi_box_unit' => 200, 'isi_box_satuan' => 'Pcs'],
        ];

        $data = [
            'pendampingMasters' => $pendampingMasters,
            'dataKalkulasi' => $dataKalkulasi,
            'resepPenerimaan' => $resepPenerimaan,
        ];

        $pdf = Pdf::loadView('pdf.rekappendamping', $data);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('rekap.pendamping_' . date('Ymd_His') . '.pdf');
    }
}