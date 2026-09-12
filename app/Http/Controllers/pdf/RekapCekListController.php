<?php

namespace App\Http\Controllers\pdf;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CekRincianMenuHarian;
use App\Models\CekResep;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Menu;
use App\Models\Resep;
use App\Models\rincian_menu_harian;
use App\Models\rincian_sekolah;
use App\Models\RumusPerhitunganProtein;
use App\Models\RumusPerhitunganSayur;
use App\Models\TbRumusPerhitunganKarbo;
use App\Models\DataDapur;
class RekapCekListController extends Controller
{
    public function tampilChecklist($id_menu_harian)
    {
        $rincian = CekRincianMenuHarian::with(['resep', 'bahan'])
            ->where('id_menu_harian', $id_menu_harian)
            ->get();

        if ($rincian->isEmpty()) {
            abort(404, 'Data tidak ditemukan.');
        }

        $resep = $rincian->first()->resep;
        $tanggal = now()->format('d-m-Y');
        $jumlah_masak = 1;

        $pdf = Pdf::loadView('pdf.rekapceklist', compact('rincian', 'resep', 'tanggal', 'jumlah_masak'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream("ceklist-resep.pdf");
    }

    public function generateRekapCekListAllImages($id_menu)
    {
        // --- Bagian PENTING: Menyiapkan Data Resep yang Berbeda untuk Setiap Slide ---

        // Pilihan 1: Menggunakan Data Dummy (Untuk Uji Coba/Pengembangan Cepat)
        // Ini adalah cara paling mudah untuk memastikan tampilan bekerja.
        // Dalam aplikasi nyata, Anda akan mengganti ini dengan data dari database.

        /*$dataResepUntukTampilan = [
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
        ];*/

        $dataResepUntukTampilan = [];
        $tanggal = Carbon::today()->format('Y-m-d');
        $menu   = Menu::find($id_menu);
        $jumlah_porsi = rincian_sekolah::where('id_menu_harian', $id_menu)->sum('jumlah_penerima_total');
        $jumlah_masak = TbRumusPerhitunganKarbo::where('id_menu', $id_menu)->first();
        $rumus_karbo = TbRumusPerhitunganKarbo::where('id_menu', $id_menu)->first();

        // resep karbohidrat
        $karbo  = Resep::find($menu->karbohidrat);

        $bahanBaku = DB::table('rincian_menu_harian')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
            ->select(
                'tb_master_bahan.bahan as nama_bahan',
                'rincian_menu_harian.jumlah_box',
                'rincian_menu_harian.jumlah',
                'tb_satuan.satuan'
            )
            ->where('rincian_menu_harian.id_menu_harian', $id_menu)
            ->where('rincian_menu_harian.id_resep', $menu->karbohidrat)
            ->get();

        $bahanList = $bahanBaku->map(function ($item) {
            if ($item->satuan == 'gram' || $item->satuan == 'Gram') {
                $satuan = 'kg';
                $jumlah = $item->jumlah / 1000;
                $jumlah = rtrim(rtrim(number_format($jumlah / 1000, 3, '.', ''), '0'), '.');
            } else if ($item->satuan == 'ml') {
                $satuan = 'Liter';
                $jumlah = $item->jumlah / 1000;
                $jumlah = rtrim(rtrim(number_format($jumlah / 1000, 3, '.', ''), '0'), '.');
            } else {
                $satuan = $item->satuan;
                $jumlah = $item->jumlah;
            }
            return [
                'nama'   => $item->nama_bahan,    // sesuaikan field di tabel
                'jumlah' => $item->jumlah,
                'satuan' => $item->satuan
            ];
        })->toArray();
        $dataResepUntukTampilan[] = [
            'tanggal' => Carbon::parse($menu->tanggal_kirim)->format('d-m-Y'),
            'menu' => $karbo->nama_resep,             // sesuaikan field di tabel
            'porsi' => $jumlah_porsi . ' Porsi',
            'jumlah_kali_masak' => $jumlah_masak->karbo_kebutuhan_pintu_steamer . ' Kali ( perhitungan 1 pintu )',
            'bahan' => $bahanList,
            'jumlah_masak' => $jumlah_masak->jumlah_masak
        ];


        // Lauk
        $lauk  = Resep::find($menu->protein);
        $jumlah_masak = RumusPerhitunganProtein::where('id_menu', $id_menu)->first();
        $jumlah_protein = RumusPerhitunganProtein::where('id_menu', $id_menu)->first();
        $bahanBaku = DB::table('rincian_menu_harian')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
            ->select(
                'tb_master_bahan.bahan as nama_bahan',
                'rincian_menu_harian.jumlah_box',
                'rincian_menu_harian.jumlah',
                'tb_satuan.satuan'
            )
            ->where('rincian_menu_harian.id_menu_harian', $id_menu)
            ->where('rincian_menu_harian.id_resep', $menu->protein)
            ->get();

        $bahanList = $bahanBaku->map(function ($item) use ($jumlah_masak) {
            if ($item->satuan == 'gram' || $item->satuan == 'Gram') {
                $satuan = 'kg';
                $jumlah = $item->jumlah / 1000;
                $jumlah = rtrim(rtrim(number_format($jumlah / 1000, 3, '.', ''), '0'), '.');
            } else if ($item->satuan == 'ml') {
                $satuan = 'Liter';
                $jumlah = $item->jumlah / 1000;
                $jumlah = rtrim(rtrim(number_format($jumlah / 1000, 3, '.', ''), '0'), '.');
            } else {
                $satuan = $item->satuan;
                $jumlah = $item->jumlah;
            }
            if ($item->jumlah_box > 1) {
                return [
                    'nama'   => $item->nama_bahan . ' | ' . ($item->jumlah_box / $jumlah_masak->jumlah_masak) . 'box',    // sesuaikan field di tabel
                    'jumlah' => $item->jumlah / $jumlah_masak->jumlah_masak,
                    'satuan' => $item->satuan
                ];
            } else {
                return [
                    'nama'   => $item->nama_bahan,    // sesuaikan field di tabel
                    'jumlah' => $item->jumlah / $jumlah_masak->jumlah_masak,
                    'satuan' => $item->satuan
                ];
            }
        })->toArray();
        $dataResepUntukTampilan[] = [
            'tanggal' => Carbon::parse($menu->tanggal_kirim)->format('d-m-Y'),
            'menu' => $lauk->nama_resep,             // sesuaikan field di tabel
            'porsi' => $jumlah_porsi . ' Porsi',
            'jumlah_kali_masak' => $jumlah_masak->jumlah_masak . ' Kali',
            'bahan' => $bahanList,
            'jumlah_masak' => $jumlah_masak->jumlah_masak
        ];

        // Sayur
        $sayur  = Resep::find($menu->sayur);
        $jumlah_masak = RumusPerhitunganSayur::where('id_menu', $id_menu)->first();
        $jumlah_sayur = RumusPerhitunganProtein::where('id_menu', $id_menu)->first();
        $bahanBaku = DB::table('rincian_menu_harian')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
            ->select(
                'tb_master_bahan.bahan as nama_bahan',
                'rincian_menu_harian.jumlah_box',
                'rincian_menu_harian.jumlah',
                'tb_satuan.satuan'
            )
            ->where('rincian_menu_harian.id_menu_harian', $id_menu)
            ->where('rincian_menu_harian.id_resep', $menu->sayur)
            ->get();

        $bahanList = $bahanBaku->map(function ($item) use ($jumlah_masak) {
            if ($item->satuan == 'gram' || $item->satuan == 'Gram') {
                $satuan = 'kg';
                $jumlah = $item->jumlah / 1000;
                $jumlah = rtrim(rtrim(number_format($jumlah / 1000, 3, '.', ''), '0'), '.');
            } else if ($item->satuan == 'ml') {
                $satuan = 'Liter';
                $jumlah = $item->jumlah / 1000;
                $jumlah = rtrim(rtrim(number_format($jumlah / 1000, 3, '.', ''), '0'), '.');
            } else {
                $satuan = $item->satuan;
                $jumlah = $item->jumlah;
            }
            if($item->jumlah_box > 1)
            {
                return [
                    'nama'   => $item->nama_bahan.' | '.ceil($item->jumlah_box / $jumlah_masak->jumlah_masak ).'box',    // sesuaikan field di tabel
                    'jumlah' => $item->jumlah / $jumlah_masak->jumlah_masak,
                    'satuan' => $item->satuan
                ];
            }else{
                return [
                    'nama'   => $item->nama_bahan,    // sesuaikan field di tabel
                    'jumlah' => $item->jumlah / $jumlah_masak->jumlah_masak,
                    'satuan' => $item->satuan
                ];
            }
            
        })->toArray();
        $dataResepUntukTampilan[] = [
            'tanggal' => Carbon::parse($menu->tanggal_kirim)
                ->locale('id')
                ->translatedFormat('l, d F Y'),
            'menu' => $sayur->nama_resep,             // sesuaikan field di tabel
            'porsi' => $jumlah_porsi . ' Porsi',
            'jumlah_kali_masak' => ($jumlah_masak->jumlah_masak ?? 1) . ' Kali',
            'bahan' => $bahanList,
            'jumlah_masak' => $jumlah_masak->jumlah_masak ?? 1
        ];

        // buah
        $buah  = Resep::find($menu->buah);
        $jumlah_buah = RumusPerhitunganProtein::where('id_menu', $id_menu)->first();

        $bahanBaku = DB::table('rincian_menu_harian')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
            ->select(
                'tb_master_bahan.bahan as nama_bahan',
                'rincian_menu_harian.jumlah',
                'tb_satuan.satuan'
            )
            ->where('rincian_menu_harian.id_menu_harian', $id_menu)
            ->where('rincian_menu_harian.id_resep', $menu->buah)
            ->get();

        $bahanList = $bahanBaku->map(function ($item) {
            if ($item->satuan == 'gram' || $item->satuan == 'Gram') {
                $satuan = 'kg';
                $jumlah = $item->jumlah / 1000;
                $jumlah = rtrim(rtrim(number_format($jumlah / 1000, 3, '.', ''), '0'), '.');
            } else if ($item->satuan == 'ml') {
                $satuan = 'Liter';
                $jumlah = $item->jumlah / 1000;
                $jumlah = rtrim(rtrim(number_format($jumlah / 1000, 3, '.', ''), '0'), '.');
            } else {
                $satuan = $item->satuan;
                $jumlah = $item->jumlah;
            }
            return [
                'nama'   => $item->nama_bahan,    // sesuaikan field di tabel
                'jumlah' => $item->jumlah,
                'satuan' => $item->satuan
            ];
        })->toArray();
        $dataResepUntukTampilan[] = [
            'tanggal' => Carbon::parse($menu->tanggal_kirim)->format('d-m-Y'),
            'menu' => $buah->nama_resep,             // sesuaikan field di tabel
            'porsi' => $jumlah_porsi . ' Porsi',
            'jumlah_kali_masak' =>  '1 Kali',
            'bahan' => $bahanList,
            'jumlah_masak' => $jumlah_masak->jumlah_masak ?? 1
        ];

        // pendamping
        $pendamping  = Resep::find($menu->susu);
        $jumlah_pendamping = RumusPerhitunganProtein::where('id_menu', $id_menu)->first();

        $bahanBaku = DB::table('rincian_menu_harian')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
            ->select(
                'tb_master_bahan.bahan as nama_bahan',
                'rincian_menu_harian.jumlah',
                'tb_satuan.satuan'
            )
            ->where('rincian_menu_harian.id_menu_harian', $id_menu)
            ->where('rincian_menu_harian.id_resep', $menu->susu)
            ->get();

        $bahanList = $bahanBaku->map(function ($item) {
            if ($item->satuan == 'gram' || $item->satuan == 'Gram') {
                $satuan = 'kg';
                $jumlah = $item->jumlah / 1000;
                $jumlah = rtrim(rtrim(number_format($jumlah / 1000, 3, '.', ''), '0'), '.');
            } else if ($item->satuan == 'ml') {
                $satuan = 'Liter';
                $jumlah = $item->jumlah / 1000;
                $jumlah = rtrim(rtrim(number_format($jumlah / 1000, 3, '.', ''), '0'), '.');
            } else {
                $satuan = $item->satuan;
                $jumlah = $item->jumlah;
            }
            return [
                'nama'   => $item->nama_bahan,    // sesuaikan field di tabel
                'jumlah' => $item->jumlah,
                'satuan' => $item->satuan
            ];
        })->toArray();
        $dataResepUntukTampilan[] = [
            'tanggal' => Carbon::parse($menu->tanggal_kirim)->format('d-m-Y'),
            'menu' => $pendamping->nama_resep,             // sesuaikan field di tabel
            'porsi' => $jumlah_porsi . ' Porsi',
            'jumlah_kali_masak' =>  '1 Kali',
            'bahan' => $bahanList,
            'jumlah_masak' => $jumlah_masak->jumlah_masak ?? 1
        ];
        
        $data_dapur = Datadapur::first();
        // --- Meneruskan Data ke View dan Mengubahnya ke PDF ---
        // Load view 'resep_pdf' dengan data
        $pdf = Pdf::loadView('pdf.rekapceklist', ['data' => $dataResepUntukTampilan, 'data_dapur' => $data_dapur]);

        // Opsional: Atur ukuran kertas dan orientasi jika perlu
        // $pdf->setPaper('A4', 'portrait');

        // Mengunduh PDF dengan nama file tertentu
        return $pdf->download('rekap.ceklist_' . date('YmdHis') . '.pdf');

        // Atau, untuk menampilkan di browser tanpa mengunduh:
        // return $pdf->stream('resep-menu-' . date('YmdHis') . '.pdf');
    }


    public function generateRekapCekListAllHasilMasak($id_menu)
    {
        // --- Bagian PENTING: Menyiapkan Data Resep yang Berbeda untuk Setiap Slide ---

        // Pilihan 1: Menggunakan Data Dummy (Untuk Uji Coba/Pengembangan Cepat)
        // Ini adalah cara paling mudah untuk memastikan tampilan bekerja.
        // Dalam aplikasi nyata, Anda akan mengganti ini dengan data dari database.

        /*$dataResepUntukTampilan = [
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
        ];*/

        $dataResepUntukTampilan = [];
        $tanggal = Carbon::today()->format('Y-m-d');
        $menu   = Menu::find($id_menu);
        $jumlah_porsi = rincian_sekolah::where('id_menu_harian', $id_menu)->sum('jumlah_penerima_total');
        $jumlah_masak = TbRumusPerhitunganKarbo::where('id_menu', $id_menu)->first();
        $rumus_karbo = TbRumusPerhitunganKarbo::where('id_menu', $id_menu)->first();
        // resep karbohidrat
        $karbo  = Resep::find($menu->karbohidrat);

        $bahanBaku = DB::table('rincian_menu_harian')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
            ->select(
                'tb_master_bahan.bahan as nama_bahan',
                'rincian_menu_harian.jumlah',
                'tb_satuan.satuan'
            )
            ->where('rincian_menu_harian.id_menu_harian', $id_menu)
            ->where('rincian_menu_harian.id_resep', $menu->karbohidrat)
            ->get();

        $bahanList = $bahanBaku->map(function ($item) {
            if ($item->satuan == 'gram' || $item->satuan == 'Gram') {
                $satuan = 'kg';
                $jumlah = $item->jumlah / 1000;
                $jumlah = rtrim(rtrim(number_format($jumlah / 1000, 3, '.', ''), '0'), '.');
            } else if ($item->satuan == 'ml') {
                $satuan = 'Liter';
                $jumlah = $item->jumlah / 1000;
                $jumlah = rtrim(rtrim(number_format($jumlah / 1000, 3, '.', ''), '0'), '.');
            } else {
                $satuan = $item->satuan;
                $jumlah = $item->jumlah;
            }
            return [
                'nama'   => $item->nama_bahan,    // sesuaikan field di tabel
                'jumlah' => $item->jumlah,
                'satuan' => $item->satuan
            ];
        })->toArray();
        $dataResepUntukTampilan[] = [
            'tanggal' => Carbon::parse($menu->tanggal_kirim)->format('d-m-Y'),
            'menu' => $karbo->nama_resep,             // sesuaikan field di tabel
            'porsi' => $jumlah_porsi . ' Porsi',
            'jumlah_kali_masak' => $jumlah_masak->karbo_kebutuhan_pintu_steamer . ' Kali ( perhitungan 1 pintu )',
            'bahan' => $bahanList,
            'jumlah_masak' => $jumlah_masak->jumlah_masak
        ];


        // Lauk
        $lauk  = Resep::find($menu->protein);
        $jumlah_masak = RumusPerhitunganProtein::where('id_menu', $id_menu)->first();
        $rumus_protein = RumusPerhitunganProtein::where('id_menu', $id_menu)->first();
        $bahanBaku = DB::table('rincian_menu_harian')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
            ->select(
                'tb_master_bahan.bahan as nama_bahan',
                'rincian_menu_harian.jumlah',
                'tb_satuan.satuan'
            )
            ->where('rincian_menu_harian.id_menu_harian', $id_menu)
            ->where('rincian_menu_harian.id_resep', $menu->protein)
            ->get();

        $bahanList = $bahanBaku->map(function ($item) use ($jumlah_masak) {
            if ($item->satuan == 'gram' || $item->satuan == 'Gram') {
                $satuan = 'kg';
                $jumlah = $item->jumlah / 1000;
                $jumlah = rtrim(rtrim(number_format($jumlah / 1000, 3, '.', ''), '0'), '.');
            } else if ($item->satuan == 'ml') {
                $satuan = 'Liter';
                $jumlah = $item->jumlah / 1000;
                $jumlah = rtrim(rtrim(number_format($jumlah / 1000, 3, '.', ''), '0'), '.');
            } else {
                $satuan = $item->satuan;
                $jumlah = $item->jumlah;
            }
            return [
                'nama'   => $item->nama_bahan,    // sesuaikan field di tabel
                'jumlah' => $item->jumlah / $jumlah_masak->jumlah_masak,
                'satuan' => $item->satuan
            ];
        })->toArray();
        $dataResepUntukTampilan[] = [
            'tanggal' => Carbon::parse($menu->tanggal_kirim)->format('d-m-Y'),
            'menu' => $lauk->nama_resep,             // sesuaikan field di tabel
            'porsi' => $jumlah_porsi . ' Porsi',
            'jumlah_kali_masak' => $jumlah_masak->jumlah_masak,
            'bahan' => $bahanList,
            'jumlah_masak' => $jumlah_masak->jumlah_masak

        ];

        // Sayur
        $sayur  = Resep::find($menu->sayur);
        $jumlah_masak = RumusPerhitunganSayur::where('id_menu', $id_menu)->first();
        $rumus_sayur = RumusPerhitunganSayur::where('id_menu', $id_menu)->first();
        $bahanBaku = DB::table('rincian_menu_harian')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
            ->select(
                'tb_master_bahan.bahan as nama_bahan',
                'rincian_menu_harian.jumlah',
                'tb_satuan.satuan'
            )
            ->where('rincian_menu_harian.id_menu_harian', $id_menu)
            ->where('rincian_menu_harian.id_resep', $menu->sayur)
            ->get();

        $bahanList = $bahanBaku->map(function ($item) use ($jumlah_masak) {
            if ($item->satuan == 'gram' || $item->satuan == 'Gram') {
                $satuan = 'kg';
                $jumlah = $item->jumlah / 1000;
                $jumlah = rtrim(rtrim(number_format($jumlah / 1000, 3, '.', ''), '0'), '.');
            } else if ($item->satuan == 'ml') {
                $satuan = 'Liter';
                $jumlah = $item->jumlah / 1000;
                $jumlah = rtrim(rtrim(number_format($jumlah / 1000, 3, '.', ''), '0'), '.');
            } else {
                $satuan = $item->satuan;
                $jumlah = $item->jumlah;
            }
            return [
                'nama'   => $item->nama_bahan,    // sesuaikan field di tabel
                'jumlah' => ($item->jumlah / $jumlah_masak->jumlah_masak),
                'satuan' => $item->satuan
            ];
        })->toArray();
        $dataResepUntukTampilan[] = [
            'tanggal' => Carbon::parse($menu->tanggal_kirim)->format('d-m-Y'),
            'menu' => $sayur->nama_resep,             // sesuaikan field di tabel
            'porsi' => $jumlah_porsi . ' Porsi',
            'jumlah_kali_masak' => $jumlah_masak->jumlah_masak ?? 1 ,
            'bahan' => $bahanList,
            'jumlah_masak' => $jumlah_masak->jumlah_masak ?? 1
        ];

        // buah
        $buah  = Resep::find($menu->buah);

        $bahanBaku = DB::table('rincian_menu_harian')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
            ->select(
                'tb_master_bahan.bahan as nama_bahan',
                'rincian_menu_harian.jumlah',
                'tb_satuan.satuan'
            )
            ->where('rincian_menu_harian.id_menu_harian', $id_menu)
            ->where('rincian_menu_harian.id_resep', $menu->buah)
            ->get();

        $bahanList = $bahanBaku->map(function ($item) {
            if ($item->satuan == 'gram' || $item->satuan == 'Gram') {
                $satuan = 'kg';
                $jumlah = $item->jumlah / 1000;
                $jumlah = rtrim(rtrim(number_format($jumlah / 1000, 3, '.', ''), '0'), '.');
            } else if ($item->satuan == 'ml') {
                $satuan = 'Liter';
                $jumlah = $item->jumlah / 1000;
                $jumlah = rtrim(rtrim(number_format($jumlah / 1000, 3, '.', ''), '0'), '.');
            } else {
                $satuan = $item->satuan;
                $jumlah = $item->jumlah;
            }
            return [
                'nama'   => $item->nama_bahan,    // sesuaikan field di tabel
                'jumlah' => $item->jumlah,
                'satuan' => $item->satuan
            ];
        })->toArray();
        $dataResepUntukTampilan[] = [
            'tanggal' => Carbon::parse($menu->tanggal_kirim)->format('d-m-Y'),
            'menu' => $buah->nama_resep,             // sesuaikan field di tabel
            'porsi' => $jumlah_porsi . ' Porsi',
            'jumlah_kali_masak' =>  1,
            'bahan' => $bahanList,
            'jumlah_masak' => 1
        ];

        // pendamping
        $pendamping  = Resep::find($menu->susu);

        $bahanBaku = DB::table('rincian_menu_harian')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
            ->select(
                'tb_master_bahan.bahan as nama_bahan',
                'rincian_menu_harian.jumlah',
                'tb_satuan.satuan'
            )
            ->where('rincian_menu_harian.id_menu_harian', $id_menu)
            ->where('rincian_menu_harian.id_resep', $menu->susu)
            ->get();

        $bahanList = $bahanBaku->map(function ($item) {
            if ($item->satuan == 'gram' || $item->satuan == 'Gram') {
                $satuan = 'kg';
                $jumlah = $item->jumlah / 1000;
                $jumlah = rtrim(rtrim(number_format($jumlah / 1000, 3, '.', ''), '0'), '.');
            } else if ($item->satuan == 'ml') {
                $satuan = 'Liter';
                $jumlah = $item->jumlah / 1000;
                $jumlah = rtrim(rtrim(number_format($jumlah / 1000, 3, '.', ''), '0'), '.');
            } else {
                $satuan = $item->satuan;
                $jumlah = $item->jumlah;
            }
            return [
                'nama'   => $item->nama_bahan,    // sesuaikan field di tabel
                'jumlah' => $item->jumlah,
                'satuan' => $item->satuan
            ];
        })->toArray();
        $dataResepUntukTampilan[] = [
            'tanggal' => Carbon::parse($menu->tanggal_kirim)->format('d-m-Y'),
            'menu' => $pendamping->nama_resep,             // sesuaikan field di tabel
            'porsi' => $jumlah_porsi . ' Porsi',
            'jumlah_kali_masak' =>  1,
            'bahan' => $bahanList,
            'jumlah_masak' => 1
        ];

        $data_dapur = Datadapur::first();
        // --- Meneruskan Data ke View dan Mengubahnya ke PDF ---
        // Load view 'resep_pdf' dengan data
        $pdf = Pdf::loadView('pdf.rekapceklist_hasil_masak', ['data' => $dataResepUntukTampilan, 'data_dapur' => $data_dapur]);

        // Opsional: Atur ukuran kertas dan orientasi jika perlu
        // $pdf->setPaper('A4', 'portrait');

        // Mengunduh PDF dengan nama file tertentu
        return $pdf->download('rekap.ceklist_Masak_' . date('YmdHis') . '.pdf');

        // Atau, untuk menampilkan di browser tanpa mengunduh:
        // return $pdf->stream('resep-menu-' . date('YmdHis') . '.pdf');
    }
}
