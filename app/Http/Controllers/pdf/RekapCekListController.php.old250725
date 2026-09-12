<?php


namespace App\Http\Controllers\pdf;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan Anda sudah menginstal barryvdh/laravel-dompdf

// Jika Anda memiliki model untuk Resep dan Bahan di aplikasi Anda,
// pastikan untuk mengimpornya di sini. Contoh:
// use App\Models\Resep;
// use App\Models\Bahan;

class RekapCekListController extends Controller
{
    /**
     * Menampilkan 4 slide resep yang berbeda dalam format PDF.
     * Data resep bisa berasal dari dummy data atau dari database.
     *
     * @return \Illuminate\Http\Response
     */
   public function generateRekapCekListAllImages()
    {
        // --- Bagian PENTING: Menyiapkan Data Resep yang Berbeda untuk Setiap Slide ---

        // Pilihan 1: Menggunakan Data Dummy (Untuk Uji Coba/Pengembangan Cepat)
        // Ini adalah cara paling mudah untuk memastikan tampilan bekerja.
        // Dalam aplikasi nyata, Anda akan mengganti ini dengan data dari database.
        $dataResepUntukTampilan = [
            [
                'tanggal' => '16-07-2025', // Resep 1
                'menu' => 'Nasi Goreng Spesial',
                'porsi' => '10 Porsi',
                'jumlah_kali_masak' => '1 Kali',
                'bahan' => [
                    ['nama' => 'Nasi', 'jumlah' => '1 kg', 'satuan' => 'kg'],
                    ['nama' => 'Telur', 'jumlah' => '5 butir', 'satuan' => 'butir'],
                    ['nama' => 'Ayam Suwir', 'jumlah' => '200 gr', 'satuan' => 'gr'],
                    ['nama' => 'Bawang Merah', 'jumlah' => '50 gr', 'satuan' => 'gr'],
                    ['nama' => 'Kecap Manis', 'jumlah' => '3 sdm', 'satuan' => 'sdm'],
                ]
            ],
            [
                'tanggal' => '16-07-2025', // Resep 2
                'menu' => 'Sop Ayam Bening',
                'porsi' => '8 Porsi',
                'jumlah_kali_masak' => '1 Kali',
                'bahan' => [
                    ['nama' => 'Ayam', 'jumlah' => '500 gr', 'satuan' => 'gr'],
                    ['nama' => 'Wortel', 'jumlah' => '200 gr', 'satuan' => 'gr'],
                    ['nama' => 'Kentang', 'jumlah' => '150 gr', 'satuan' => 'gr'],
                    ['nama' => 'Bawang Putih', 'jumlah' => '30 gr', 'satuan' => 'gr'],
                    ['nama' => 'Garam', 'jumlah' => '1 sdt', 'satuan' => 'sdt'],
                ]
            ],
            [
                'tanggal' => '16-07-2025', // Resep 3
                'menu' => 'Mie Ayam Bakso',
                'porsi' => '12 Porsi',
                'jumlah_kali_masak' => '1 Kali',
                'bahan' => [
                    ['nama' => 'Mie Telur', 'jumlah' => '1 kg', 'satuan' => 'kg'],
                    ['nama' => 'Daging Ayam Cincang', 'jumlah' => '300 gr', 'satuan' => 'gr'],
                    ['nama' => 'Bakso Sapi', 'jumlah' => '20 butir', 'satuan' => 'butir'],
                    ['nama' => 'Sawi Hijau', 'jumlah' => '1 ikat', 'satuan' => 'ikat'],
                    ['nama' => 'Saos Sambal', 'jumlah' => 'secukupnya', 'satuan' => ''],
                ]
            ],
            [
                'tanggal' => '16-07-2025', // Resep 4
                'menu' => 'Gado-Gado',
                'porsi' => '7 Porsi',
                'jumlah_kali_masak' => '1 Kali',
                'bahan' => [
                    ['nama' => 'Lontong', 'jumlah' => '3 buah', 'satuan' => 'buah'],
                    ['nama' => 'Tahu', 'jumlah' => '4 potong', 'satuan' => 'potong'],
                    ['nama' => 'Tempe', 'jumlah' => '4 potong', 'satuan' => 'potong'],
                    ['nama' => 'Kacang Panjang', 'jumlah' => '100 gr', 'satuan' => 'gr'],
                    ['nama' => 'Saus Kacang', 'jumlah' => '200 gr', 'satuan' => 'gr'],
                ]
            ],
        ];

        // --- Meneruskan Data ke View dan Mengubahnya ke PDF ---
        // Load view 'resep_pdf' dengan data
        $pdf = Pdf::loadView('pdf.rekapceklist', ['data' => $dataResepUntukTampilan]);

        // Opsional: Atur ukuran kertas dan orientasi jika perlu
        // $pdf->setPaper('A4', 'portrait');

        // Mengunduh PDF dengan nama file tertentu
        return $pdf->download('rekap.ceklist_' . date('YmdHis') . '.pdf');

        // Atau, untuk menampilkan di browser tanpa mengunduh:
        // return $pdf->stream('resep-menu-' . date('YmdHis') . '.pdf');
    }
}