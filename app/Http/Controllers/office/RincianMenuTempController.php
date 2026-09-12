<?php

// Namespace untuk menempatkan controller di folder App\Http\Controllers\office
namespace App\Http\Controllers\office;

// Import base controller Laravel
use App\Http\Controllers\Controller;
use App\Models\BoxBahanBaku;
use App\Models\Buffer;
use App\Models\Menu;
// Import Request class untuk menangani request HTTP
use Illuminate\Http\Request;

// Import model TbRincianMenuTemp
use App\Models\TbRincianMenuTemp;

// Import model rincian_menu_harian
use App\Models\rincian_menu_harian;

// Import model MenuBahan
use App\Models\MenuBahan;
use App\Models\PerhitunganBumbu;
use App\Models\Resep;
use App\Models\TbMasterBahan;
use App\Models\TbRincianKontrak;
use App\Models\TbKontrak;
use App\Models\PaketMenu;
// Import model satuan
use App\Models\TbSatuan;

// Import class Carbon untuk manipulasi tanggal
use Illuminate\Support\Carbon;

// *Catatan:* Baris ini tampaknya tidak diperlukan (Mockery\Undefined)
use Mockery\Undefined;

// Import DB Facade untuk query builder
use Illuminate\Support\Facades\DB;
use ReflectionFunctionAbstract;
// Import DataTables package (Yajra) untuk integrasi DataTables di server side
use Yajra\DataTables\DataTables;

class RincianMenuTempController extends Controller
{
    private function resolveRincianKontrakId(int $idBahan, $harga = null): int
    {
        if (is_object($harga) && isset($harga->id) && $harga->id) {
            return $harga->id;
        }

        $rincian = TbRincianKontrak::where('id_bahan', $idBahan)
            ->where('status', 1)
            ->orderByDesc('id')
            ->first();

        if ($rincian) {
            return $rincian->id;
        }

        $kontrakId = TbKontrak::where('status', 1)
            ->orderByDesc('id')
            ->value('id') ?? 13;

        $bahan = TbMasterBahan::find($idBahan);

        $newRincian = TbRincianKontrak::create([
            'id_kontrak'  => $kontrakId,
            'id_bahan'    => $idBahan,
            'merek_bahan' => $bahan->bahan ?? '-',
            'harga_bahan' => is_object($harga) ? ($harga->harga_bahan ?? 0) : (float) ($harga ?? 0),
            'jumlah_bahan'=> 1,
            'satuan_bahan'=> $bahan->satuan_bahan ?? 0,
            'status'      => 1,
            'kemasan'     => '-',
        ]);

        return $newRincian->id;
    }

    public function storeKarbohidrat(Request $request)
    {
        // 1?? Validasi input request (metode validateRequest adalah custom)
        $request->validate([
            'id_menu' => 'required',
            'id_karbohidrat' => 'required|integer',

        ]);

        // 2?? Update kolom 'karbohidrat' pada tabel tb_menu sesuai ID menu
        DB::table('tb_menu')
            ->where('id', $request->id_menu)
            ->update([
                'karbohidrat' => $request->id_karbohidrat,
            ]);

        // 3?? Hitung jumlah penerima kategori A dan B dari histori menu
        $jumlah_siswa_a = DB::table('rincian_sekolah')
            ->where('id_menu_harian', $request->id_menu)
            ->sum('jumlah_penerima_a');

        $jumlah_siswa_b = DB::table('rincian_sekolah')
            ->where('id_menu_harian', $request->id_menu)
            ->sum('jumlah_penerima_b');
        $jumlah_total_siswa = $jumlah_siswa_a+ $jumlah_siswa_b;
        // 4?? Ambil input porsi A dan B
        $karbo_porsi_a = $request->porsi_a;
        $karbo_porsi_b = $request->porsi_b;

        // 5?? Hitung kebutuhan beras kategori A dan B (dibulatkan ke atas)
        //$karbo_kebutuhan_beras_a = ceil(($jumlah_siswa_a * $karbo_porsi_a * 3 / 4 / 2) / 1000);
        //$karbo_kebutuhan_beras_b = ceil(($jumlah_siswa_b * $karbo_porsi_b * 3 / 4 / 2) / 1000);
        $karbo_kebutuhan_beras_a = ceil($karbo_porsi_a / 1000 * $jumlah_siswa_a);
        $karbo_kebutuhan_beras_b = ceil($karbo_porsi_b / 1000 * $jumlah_siswa_b );
        $karbo_kebutuhan_beras_total = $karbo_kebutuhan_beras_a + $karbo_kebutuhan_beras_b;

        // 6?? Hitung kebutuhan tray, steamer, pintu steamer, air, produksi
        $karbo_kebutuhan_tray = ceil($karbo_kebutuhan_beras_total / 3);
        $karbo_kebutuhan_steamer = ceil($karbo_kebutuhan_tray / 24);
        $karbo_kebutuhan_pintu_steamer = ceil($karbo_kebutuhan_tray / 12);
        $karbo_kebutuhan_air = $karbo_kebutuhan_beras_total * 4 / 3;

        $karbo_hasil_produksi = $karbo_kebutuhan_beras_total * 4 / 3 * 2 * 1000;
        $karbo_hasil_produksi_kg = $karbo_kebutuhan_beras_total * 4 / 3 * 2;

        // 7?? Hitung berapa kali mencuci beras (setiap 72 kg 1 kali cuci)
        $karbo_hitungan_cuci_beras = ceil($karbo_kebutuhan_beras_total / 72);

        // 8?? Distribusi cuci ke slot cuci 1-5
        $jumlah_yang_dicuci = $karbo_kebutuhan_beras_total;
        $cuci_1 = 0;
        $cuci_2 = 0;
        $cuci_3 = 0;
        $cuci_4 = 0;
        $cuci_5 = 0;

        // Slot cuci 1
        if ($jumlah_yang_dicuci > 72) {
            $cuci_1 = 72;
            $jumlah_yang_dicuci -= 72;
        } else {
            $cuci_1 = $jumlah_yang_dicuci;
            $jumlah_yang_dicuci = 0;
        }

        // Slot cuci 2
        if ($jumlah_yang_dicuci > 0) {
            if ($jumlah_yang_dicuci > 72) {
                $cuci_2 = 72;
                $jumlah_yang_dicuci -= 72;
            } else {
                $cuci_2 = $jumlah_yang_dicuci;
                $jumlah_yang_dicuci = 0;
            }
        }

        // Slot cuci 3
        if ($jumlah_yang_dicuci > 0) {
            if ($jumlah_yang_dicuci > 72) {
                $cuci_3 = 72;
                $jumlah_yang_dicuci -= 72;
            } else {
                $cuci_3 = $jumlah_yang_dicuci;
                $jumlah_yang_dicuci = 0;
            }
        }

        // Slot cuci 4
        if ($jumlah_yang_dicuci > 0) {
            if ($jumlah_yang_dicuci > 72) {
                $cuci_4 = 72;
                $jumlah_yang_dicuci -= 72;
            } else {
                $cuci_4 = $jumlah_yang_dicuci;
                $jumlah_yang_dicuci = 0;
            }
        }

        // Slot cuci 5
        if ($jumlah_yang_dicuci > 0) {
            if ($jumlah_yang_dicuci > 72) {
                $cuci_5 = 72;
                $jumlah_yang_dicuci -= 72;
            } else {
                $cuci_5 = $jumlah_yang_dicuci;
                $jumlah_yang_dicuci = 0;
            }
        }

        DB::table('tb_rumus_perhitungan_karbo')->insert([
            'id_menu' => $request->id_menu,
            'karbo_porsi_a' => $karbo_porsi_a,
            'karbo_porsi_b' => $karbo_porsi_b,
            'karbo_kebutuhan_beras_a' => $karbo_kebutuhan_beras_a,
            'karbo_kebutuhan_beras_b' => $karbo_kebutuhan_beras_b,
            'karbo_kebutuhan_beras_total' => $karbo_kebutuhan_beras_total,
            'karbo_kebutuhan_tray' => $karbo_kebutuhan_tray,
            'karbo_kebutuhan_steamer' => $karbo_kebutuhan_steamer,
            'karbo_kebutuhan_pintu_steamer' => $karbo_kebutuhan_pintu_steamer,
            'karbo_kebutuhan_air' => $karbo_kebutuhan_air,
            'karbo_hasil_produksi' => $karbo_hasil_produksi,
            'karbo_hasil_produksi_kg' => $karbo_hasil_produksi_kg,
            'karbo_hitungan_cuci_beras' => $karbo_hitungan_cuci_beras,
        ]);
        
        $data_karbo = MenuBahan::where('menu_id', $request->id_karbohidrat)->get();
        foreach ($data_karbo as $row) {
            if ($row->status_bahan_baku == 1) {
                $harga = DB::table('tb_rincian_kontrak')->where('id_bahan', $row->bahan_id)
                    ->where('status',  1)->orderByDesc('id') // ambil yang terbaru berdasarkan id
                    ->first();
                $keterangan = DB::table('tb_spesifikasi_bahan')->where('id_bahan', $row->bahan_id)
                    ->first()->spesifikasi ?? '-';
                $nama_bahan = TbMasterBahan::find($row->bahan_id);
                if ($nama_bahan->id == 6) {
                    $totalBerasKg = $karbo_kebutuhan_beras_total ?? 0;
                    $pack_utama = $totalBerasKg / 72;
                    $pack25 = floor($pack_utama) * 2;
                    $pack5 = floor($pack_utama) * 4;
                    $pack1 = floor($pack_utama) * 2;
                    $sisa = $totalBerasKg - floor($pack_utama) * 72;
                    $pack25sisa = floor($sisa / 25);

                    $sisa = $sisa -  $pack25sisa * 25;
                    $pack25 = $pack25sisa + $pack25;
                    $pack5 = floor($sisa / 5) + $pack5;
                    $pack1 = ($sisa % 5) + $pack1;
                    if ($row->bahan_id == 6) {
                        $keterangan = '25kg : ' . $pack25 . 'pack | 5kg : ' . $pack5 . 'pack | 1kg : ' . $pack1 . 'pack';
                    }
                    if ($harga) {
                        DB::table('tb_rincian_menu_temp')->insert([
                            'id_menu'       => $request->id_menu,
                            'id_resep'      => $request->id_karbohidrat,
                            'id_bahan'      => $row->bahan_id,
                            'jumlah'        => $karbo_kebutuhan_beras_total,
                            'bumbu'         => 0,
                            'harga'         => $harga->harga_bahan,
                            'total_harga'   => $harga->harga_bahan * $karbo_kebutuhan_beras_total,
                            'id_satuan'     => 25,
                            'jumlah_box'     => 1,
                            'id_kontrak'     => $this->resolveRincianKontrakId($row->bahan_id, $harga),
                            'keterangan'     => $keterangan ?? '-',
                        ]);
                    }
                }else{
                    if($row->id_satuan == 1)
                    {
                        $jumlah = $karbo_kebutuhan_beras_total;
                        $satuan = 25;
                    }else{
                        $jumlah = $jumlah_total_siswa;
                        $satuan = $row->id_satuan;
                    }
                    if ($harga) {
                        DB::table('tb_rincian_menu_temp')->insert([
                            'id_menu'       => $request->id_menu,
                            'id_resep'      => $request->id_karbohidrat,
                            'id_bahan'      => $row->bahan_id,
                            'jumlah'        => $jumlah,
                            'bumbu'         => 0,
                            'harga'         => $harga->harga_bahan,
                            'total_harga'   => $harga->harga_bahan * $jumlah_total_siswa,
                            'id_satuan'     => $satuan,
                            'jumlah_box'     => 1,
                            'id_kontrak'     => $this->resolveRincianKontrakId($row->bahan_id, $harga),
                            'keterangan'     => $keterangan ?? '-',
                        ]);
                    }
                }  
                
            } else {
                $harga = DB::table('tb_rincian_kontrak')->where('id_bahan', $row->bahan_id)
                    ->where('status',  1)->orderByDesc('id') // ambil yang terbaru berdasarkan id
                    ->first();
                $keterangan = DB::table('tb_spesifikasi_bahan')->where('id_bahan', $row->bahan_id)
                    ->first();
                $keterangan = $keterangan->spesifikasi ?? '-';
                $data_perhitungan_bumbu = PerhitunganBumbu::where('id_menu_bahan', $row->id)->first();
                $pengali = $data_perhitungan_bumbu->pengali ?? 10;
                $pembagi = $data_perhitungan_bumbu->pembagi ?? 115;
                $jumlah_kebutuhan = $karbo_kebutuhan_beras_total * 1000;
                $jumlah_kebutuhan = $jumlah_kebutuhan / $pembagi;
                $jumlah_kebutuhan = $jumlah_kebutuhan * $pengali;
                $bahan = TbMasterBahan::find($row->bahan_id);
                
                if ($harga) {
                     
                    DB::table('tb_rincian_menu_temp')->insert([
                        'id_menu'       => $request->id_menu,
                        'id_resep'      => $request->id_karbohidrat,
                        'id_bahan'      => $row->bahan_id,
                        'jumlah'        => $jumlah_kebutuhan,
                        'bumbu'         => 0,
                        'harga'         => $harga->harga_bahan,
                        'total_harga'   =>  $harga->harga_bahan * $jumlah_kebutuhan / 1000,
                        'id_satuan'     => $bahan->satuan_bahan,
                        'jumlah_box'     => 1,
                        'id_kontrak'     => $this->resolveRincianKontrakId($row->bahan_id, $harga),
                        'keterangan'     => $keterangan ?? '-',
                    ]);
                }
            }
        }
        $menu = Menu::find($request->id_menu);
        $realisasiAkg = DB::table('tb_resep_realisasi_akg')
                ->where('id_resep',  $request->id_karbohidrat)
                ->get();

        $energi = 0;
        $protein = 0;
        $lemak = 0;
        $karbohidrat = 0;
        $serat = 0;
        $natrium = 0;
        foreach ($realisasiAkg as $row) {
             DB::table('tb_menu_akg')
                ->insert([
                    'id_menu' => $request->id_menu,
                    'komponen_sehat' => 'karbohidrat',
                    'id_nutrisi' => $request->id_karbohidrat,
                    'energi' => $row->energi_kcal,
                    'protein' => $row->protein_g,
                    'lemak' => $row->lemak_g,
                    'karbo' => $row->karbohidrat_g,
                    'serat' => $row->serat_g,
                    'natrium' => $row->natrium_mg,
                ]);
          
        }


       
        // 11?? Redirect kembali dengan pesan sukses
        return redirect()->route('rincian_bahan', ['idmenu' => $request->id_menu])
            ->with('success', 'Karbohidrat berhasil disimpan.');
    }


    public function dt_karbohidrat_temp($id_menu)
    {
        // Ambil data Menu berdasarkan id_menu
        $data = Menu::where('id', $id_menu)->first();

        // Query rincian bahan resep karbohidrat
        /*if ($data->status_pengajuan == 'pending')
        {
            $table = DB::table('tb_rincian_menu_temp')
                ->join('tb_master_bahan', 'tb_rincian_menu_temp.id_bahan', '=', 'tb_master_bahan.id')
                ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
                ->select(
                    'tb_master_bahan.bahan',

                    'tb_rincian_menu_temp.id',
                    'tb_rincian_menu_temp.jumlah',
                    'tb_rincian_menu_temp.harga',
                    'tb_rincian_menu_temp.total_harga',
                    'tb_satuan.satuan',
                    'tb_rincian_menu_temp.id_satuan'
                )
                ->where('tb_rincian_menu_temp.id_resep', $data->karbohidrat)
                ->where('tb_rincian_menu_temp.id_menu', $id_menu)
                ->get();
        }else{
            $table = DB::table('tb_rincian_menu_temp')
                ->join('tb_master_bahan', 'tb_rincian_menu_temp.id_bahan', '=', 'tb_master_bahan.id')
                ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
                ->select(
                    'tb_master_bahan.bahan',

                    'tb_rincian_menu_temp.id',
                    'tb_rincian_menu_temp.jumlah',
                    'tb_rincian_menu_temp.harga',
                    'tb_rincian_menu_temp.total_harga',
                    'tb_satuan.satuan',
                    'tb_rincian_menu_temp.id_satuan'
                )
                ->where('tb_rincian_menu_temp.id_resep', $data->karbohidrat)
                ->where('tb_rincian_menu_temp.id_menu', $id_menu)
                ->get();
        }*/

        $table = DB::table('tb_rincian_menu_temp')
            ->join('tb_master_bahan', 'tb_rincian_menu_temp.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'tb_rincian_menu_temp.id_satuan', '=', 'tb_satuan.id')
            ->select(
                'tb_master_bahan.bahan',

                'tb_rincian_menu_temp.id',
                'tb_rincian_menu_temp.jumlah',
                'tb_rincian_menu_temp.harga',
                'tb_rincian_menu_temp.total_harga',
                'tb_rincian_menu_temp.keterangan',
                'tb_rincian_menu_temp.jumlah_box',
                'tb_satuan.satuan',
                'tb_rincian_menu_temp.id_satuan'
            )
            ->where('tb_rincian_menu_temp.id_resep', $data->karbohidrat)
            ->where('tb_rincian_menu_temp.id_menu', $id_menu)
            ->get();
        // Return data untuk DataTables
        return DataTables::of($table)
            ->addIndexColumn()

            // Kolom jumlah bahan dengan format satuan
            ->editColumn('jumlah_bahan', function ($row) {
                $data_satuan = TbSatuan::find($row->id_satuan);
                $satuan = $data_satuan->satuan;
                return '<input type="number" class="form-control jumlah" data-id="' . $row->id . '" value="' . $row->jumlah . '"> <button class="btn btn-success btn-sm update-jumlah" data-id="' . $row->id . '">Update</button>';
                if ($row->bahan == 'beras') {
                    return number_format($row->jumlah, 0, '', '.') . ' kg';
                } else {
                    return number_format($row->jumlah, 0, '', '.') . ' ' .  $satuan;
                }
            })

            // Kolom harga satuan
            ->addColumn('harga_satuan', function ($row) {
                return 'Rp. ' . number_format($row->harga, 0, '', '.');
            })

            // Kolom total harga dibayar
            ->addColumn('total_dibayar', function ($row) {
                return 'Rp. ' . number_format($row->total_harga, 0, '', '.');
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('rincian_menu_temp.delete', $row->id) . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Yakin hapus bahan ini?\')">Delete</a>';
            })
            ->addColumn('input_harga', function ($row) {
                return '<input type="number" class="form-control jumlah_harga" data-id="' . $row->id . '" value="' . $row->harga . '"> <button class="btn btn-success btn-sm update-jumlah-harga" data-id="' . $row->id . '">Update</button>';

                //return number_format($row->jumlah, 0, ',', '.'); // Format: 1.234,56
            })
            ->addColumn('input_jumlah_box', function ($row) {
                return '<input type="number" class="form-control jumlah_box" data-id="' . $row->id . '" value="' . $row->jumlah_box . '"> <button class="btn btn-success btn-sm update-jumlah-box" data-id="' . $row->id . '">Update</button>';

                //return number_format($row->jumlah, 0, ',', '.'); // Format: 1.234,56
            })
            ->addColumn('input_keterangan', function ($row) {
                return '<input type="text" class="form-control jumlah_keterangan" data-id="' . $row->id . '" value="' . $row->keterangan . '"> <button class="btn btn-success btn-sm update-jumlah-keterangan" data-id="' . $row->id . '">Update</button>';

                //return number_format($row->jumlah, 0, ',', '.'); // Format: 1.234,56
            })
            ->rawColumns(['jumlah_bahan', 'action', 'harga_satuan', 'total_dibayar', 'input_keterangan', 'input_jumlah_box', 'input_harga'])
            ->make(true);
    }


    public function dt_karbohidrat($id_menu)
    {
        $table = rincian_menu_harian::where('status', 0)
            ->where('jumlah', '>', 0)
            ->where('jenis', 1) //jenis bahan masak
            ->orderBy('tanggal_masuk', 'asc')
            ->get();

        return DataTables::of($table)
            ->addIndexColumn()

            ->editColumn('status', function ($row) {
                return $row->status == 0 ? "in Stock" : "out of stock";
            })
            ->addColumn('lama_penyimpanan', function ($row) {
                return now()->diffInDays(\Carbon\Carbon::parse($row->tanggal_masuk)) . " hari";
            })
            ->addColumn('nama_satuan', function ($table) {
                $satuan = TbSatuan::where('id', $table->id_satuan)->first();
                return $satuan->satuan;
            })
            ->rawColumns(['status', 'nama_satuan'])
            ->make(true);
    }

    public function update_jumlah_rincian_bahan(Request $request)
    {
        // ? Validasi input request (pastikan id dan jumlah ada, jumlah harus numerik)
        $request->validate([
            'id'     => 'required',
            'jumlah' => 'required|numeric',
        ]);

        // ? cek bahan utama ]
        $cek1 = DB::table('tb_rincian_menu_temp')->find($request->id);
        $bahan_utama_1 = MenuBahan::where('menu_id', $cek1->id_resep)->where('status_bahan_baku', 1)->first();
        $bahan_utama_2 = MenuBahan::where('menu_id', $cek1->id_resep)->where('status_bahan_baku', 2)->first() ?? 0;
        $bahan_utama_3 = MenuBahan::where('menu_id', $cek1->id_resep)->where('status_bahan_baku', 4)->first() ?? 0;
        $bahan_utama_4 = MenuBahan::where('menu_id', $cek1->id_resep)->where('status_bahan_baku', 5)->first() ?? 0;
        $data_bahan_utama = DB::table('tb_rincian_menu_temp')
                ->where('id_menu', $cek1->id_menu)
                ->where('id_resep', $cek1->id_resep)
                ->where('id_bahan', $bahan_utama_1->bahan_id)
                ->first();
        $jumlah_bahan_utama = 0;
        $satuan_bahan_utama = 'kg';
        if($bahan_utama_1)
        {
            $jumlah_bahan_utama += DB::table('tb_rincian_menu_temp')
                ->where('id_menu', $cek1->id_menu)
                ->where('id_resep', $cek1->id_resep)
                ->where('id_bahan', $bahan_utama_1->bahan_id)
                ->sum('jumlah');
           
            
        }
        if($bahan_utama_2)
        {
            $jumlah_bahan_utama += DB::table('tb_rincian_menu_temp')
                ->where('id_menu', $cek1->id_menu)
                ->where('id_resep', $cek1->id_resep)
                ->where('id_bahan', $bahan_utama_2->bahan_id)
                ->sum('jumlah');
        }
        if($bahan_utama_3)
        {
            $jumlah_bahan_utama += DB::table('tb_rincian_menu_temp')
                ->where('id_menu', $cek1->id_menu)
                ->where('id_resep', $cek1->id_resep)
                ->where('id_bahan', $bahan_utama_3->bahan_id)
                ->sum('jumlah');
        }
        if($bahan_utama_4)
        {
            $jumlah_bahan_utama += DB::table('tb_rincian_menu_temp')
                ->where('id_menu', $cek1->id_menu)
                ->where('id_resep', $cek1->id_resep)
                ->where('id_bahan', $bahan_utama_4->bahan_id)
                ->sum('jumlah');
        }  
        if( $data_bahan_utama && $data_bahan_utama->id_satuan == 25)
            {
                $satuan_bahan_utama = 'kg';
                $jumlah_bahan_utama = $jumlah_bahan_utama * 1000;
            
            }else{
                $satuan_bahan_utama = 'gram';
            }

        // ? Ambil data rincian menu sementara berdasarkan ID
        $rincian = DB::table('tb_rincian_menu_temp')->find($request->id);

        // ? Ambil data bahan dari tabel MenuBahan untuk cek status bahan baku
        $data = MenuBahan::where('menu_id', $rincian->id_resep)
            ->where('bahan_id', $rincian->id_bahan)
            ->first();

        // ? Update tabel rincian_menu_harian jika data ditemukan
        if ($rincian) {

            // update bahan baku resep
            $cek_status_bahan_baku = $data->status_bahan_baku ;
            if($cek_status_bahan_baku == 3)
                {
                    DB::table('tb_perhitungan_bumbu')
                    ->where('id_menu_bahan', $data->id)
                    ->update([
                        'pembagi' => $jumlah_bahan_utama,
                        'pengali' => $request->jumlah,
                    ]);
                    MenuBahan::where('menu_id', $rincian->id_resep)
                    ->where('bahan_id', $rincian->id_bahan)
                    ->update([
                        'jumlah' => $request->jumlah,
                    ]);
                    
                }
            // Hitung total harga default (berbasis per 1000 gram/ml)
            $total_harga = $rincian->harga * $request->jumlah / 1000;
            $keterangan  = $rincian->keterangan;

            // Jika status bahan baku bukan 3 ? hitung dengan metode berbeda
            if ($data->status_bahan_baku != 3) {
                // Total harga dihitung langsung dari jumlah * harga
                $total_harga = $rincian->harga * $request->jumlah;

                // Konversi jumlah ke kilogram (anggap jumlah adalah gram)
                $totalBerasKg = $request->jumlah ?? 0;

                // Hitung jumlah pack utama (36 kg = 1 paket khusus)
                $pack_utama = $totalBerasKg / 72;

                // Hitung pembagian pack awal
                $pack25 = floor($pack_utama) * 2;
                $pack5  = floor($pack_utama) * 4;
                $pack1  = floor($pack_utama) * 2;

                // Hitung sisa setelah diambil pack utama
                $sisa = $totalBerasKg - floor($pack_utama) * 72;
                $pack25sisa = floor($sisa / 25);
                $sisa = $sisa - $pack25sisa*25;
                $pack5sisa = floor($sisa / 5);
                $pack1sisa = $sisa % 5;
                $pack25 =  $pack25 + $pack25sisa;
                $pack5 =  $pack5 + $pack5sisa;
                $pack1 =  $pack1 + $pack1sisa;


                // Jika sisa lebih dari 25 ? ambil 1 pack 25kg
                /*if ($sisa >= 25) {
                    $pack25 += 1;
                    $sisa   -= 25;
                }
               
                // Hitung tambahan pack 5kg dan 1kg dari sisa
                $pack5 += floor($sisa / 5);
                $pack1 += ($sisa % 5);
                */
                // Jika bahan dengan ID = 6 (contoh: Beras), buat keterangan pembagian pack
                if ($rincian->id_bahan == 6) {
                    $keterangan = '25kg : ' . $pack25 . ' pack | 5kg : ' . $pack5 . ' pack | 1kg : ' . $pack1 . ' pack';
                }
            }
            $satuan = TbSatuan::find($rincian->id_satuan);
            if($satuan->satuan == 'buah' )
            {
                // ? Update data ke tabel tb_rincian_menu_temp
                DB::table('tb_rincian_menu_temp')
                    ->where('id', $request->id)
                    ->update([
                        'jumlah'      => $request->jumlah,
                        'harga'       => $rincian->harga,
                        'total_harga' => $total_harga*1000,
                        'keterangan'  => $keterangan
                    ]);
            }else{
                // ? Update data ke tabel tb_rincian_menu_temp
                DB::table('tb_rincian_menu_temp')
                    ->where('id', $request->id)
                    ->update([
                        'jumlah'      => $request->jumlah,
                        'harga'       => $rincian->harga,
                        'total_harga' => $total_harga,
                        'keterangan'  => $keterangan
                    ]);
            }
            
        }

        // ? Return respon JSON sukses
        return response()->json([
            'success' => true,
            'message' => 'Jumlah bahan berhasil diperbarui.',
            'data' => null,
            'meta' => [
                'timestamp' => now()->toIso8601String(),
                'code' => 200,
            ],
        ], 200);
    }


    public function update_jumlah_box(Request $request)
    {
        try {
            // ? Validasi input request
            $request->validate([
                'id' => 'required',
                'jumlah' => 'required|numeric',
            ]);
            $rincian    = DB::table('tb_rincian_menu_temp')->find($request->id);
            $data       = MenuBahan::where('menu_id', $rincian->id_resep)->where('bahan_id', $rincian->id_bahan)->first();

            // ? Update tabel rincian_menu_harian

            if ($rincian) {

                DB::table('tb_rincian_menu_temp')->where('id', $request->id)->update([
                    'jumlah_box' => $request->jumlah,
                ]);
            }


            // ? Return respon JSON
            return response()->json([
                'success' => true,
                'message' => 'Jumlah box berhasil diperbarui.',
                'data' => null,
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 200,
                ],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi data gagal.',
                'errors' => $e->errors(),
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 422,
                ],
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui jumlah box.',
                'errors' => ['exception' => $e->getMessage()],
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 500,
                ],
            ], 500);
        }
    }

    public function update_keterangan(Request $request)
    {
        try {
            // ? Validasi input request
            $request->validate([
                'id' => 'required',
                'keterangan' => 'required',
            ]);
            $rincian    = DB::table('tb_rincian_menu_temp')->find($request->id);
            $data       = MenuBahan::where('menu_id', $rincian->id_resep)->where('bahan_id', $rincian->id_bahan)->first();

            // ? Update tabel rincian_menu_harian

            if ($rincian) {
                $keterangan = DB::table('tb_spesifikasi_bahan')->where('id_bahan', $rincian->id_bahan)->first();

                DB::table('tb_rincian_menu_temp')->where('id', $request->id)->update([
                    'keterangan' => $request->keterangan,
                ]);
            }


            // ? Return respon JSON
            return response()->json([
                'success' => true,
                'message' => 'Keterangan berhasil diperbarui.',
                'data' => null,
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 200,
                ],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi data gagal.',
                'errors' => $e->errors(),
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 422,
                ],
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui keterangan.',
                'errors' => ['exception' => $e->getMessage()],
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 500,
                ],
            ], 500);
        }
    }

    public function update_harga(Request $request)
    {
        try {
            // ? Validasi input request
            $request->validate([
                'id' => 'required',
                'harga' => 'required',
            ]);
            $rincian    = DB::table('tb_rincian_menu_temp')->find($request->id);
            $data       = MenuBahan::where('menu_id', $rincian->id_resep)->where('bahan_id', $rincian->id_bahan)->first();


            //update harga bahan baku resep
            db::table('tb_rincian_kontrak')
                ->where('id', $rincian->id_kontrak)
                ->update(['harga_bahan' => $request->harga]);
            // ? Update tabel rincian_menu_harian

            if ($rincian) {
                $total_harga = $request->harga * $rincian->jumlah / 1000;
                if ($data->status_bahan_baku != 3) {
                    $total_harga = $request->harga * $rincian->jumlah;
                }
                DB::table('tb_rincian_menu_temp')->where('id', $request->id)->update([
                    'harga'         => $request->harga,
                    'total_harga'   => $total_harga

                ]);
            }


            // ? Return respon JSON
            return response()->json([
                'success' => true,
                'message' => 'Harga berhasil diperbarui.',
                'data' => null,
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 200,
                ],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi data gagal.',
                'errors' => $e->errors(),
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 422,
                ],
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui harga.',
                'errors' => ['exception' => $e->getMessage()],
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 500,
                ],
            ], 500);
        }
    }

    public function update_sekolahb(Request $request)
    {
        try {
            // ? Validasi input request
            $request->validate([
                'id' => 'required',
            ]);
            $rincian = DB::table('rincian_sekolah')->find($request->id);

            $updateData = [];
            
            // Update golongan A jika ada
            if ($request->has('jumlah_a')) {
                $updateData['jumlah_penerima_a'] = $request->jumlah_a;
                $updateData['jumlah_penerima_total'] = $request->jumlah_a + $rincian->jumlah_penerima_b;
            }
            
            // Update golongan B jika ada
            if ($request->has('jumlah_b')) {
                $updateData['jumlah_penerima_b'] = $request->jumlah_b;
                $updateData['jumlah_penerima_total'] = $request->jumlah_b + $rincian->jumlah_penerima_a;
            }
            
            // Update kedua golongan jika keduanya dikirim
            if ($request->has('jumlah_a') && $request->has('jumlah_b')) {
                $updateData['jumlah_penerima_total'] = $request->jumlah_a + $request->jumlah_b;
            }

            // ? Update tabel rincian_sekolah
            DB::table('rincian_sekolah')->where('id', $request->id)->update($updateData);

            // ? Return respon JSON
            return response()->json([
                'success' => true,
                'message' => 'Sekolah berhasil diperbarui.',
                'data' => null,
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 200,
                ],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi data gagal.',
                'errors' => $e->errors(),
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 422,
                ],
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data sekolah.',
                'errors' => ['exception' => $e->getMessage()],
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 500,
                ],
            ], 500);
        }
    }

    public function delete_karbohidrat_temp($id)
    {
        // 1?? Ambil data menu berdasarkan ID
        $data = Menu::find($id);

        // 2?? Hapus data dari tabel tb_rincian_menu_temp
        // yang id_menu-nya = $id dan id_reset = nilai karbohidrat pada tb_menu
        DB::table('tb_rincian_menu_temp')
            ->where('id_menu', $id)
            ->where('id_resep', $data->karbohidrat)
            ->delete();

        // 3?? Hapus data dari tabel tb_rumus_perhitungan_karbo
        // yang id_menu-nya = $id
        DB::table('tb_rumus_perhitungan_karbo')
            ->where('id_menu', $id)
            ->delete();

        DB::table('tb_menu_akg')
            ->where('id_menu', $id)
            ->where('komponen_sehat', 'karbohidrat')   
            ->delete();



        // 4?? Update kolom karbohidrat di tb_menu menjadi NULL
        DB::table('tb_menu')
            ->where('id', $id)
            ->update(['karbohidrat' => null]);
        
        // 5?? Redirect kembali ke halaman rincian bahan dengan pesan sukses
        return redirect()
            ->route('rincian_bahan', ['idmenu' => $id])
            ->with('success', 'Karbohidrat berhasil dihapus.');
    }

    public function delete_rincian_menu_temp($id)
    {
        $row = DB::table('tb_rincian_menu_temp')->where('id', $id)->first();

        if (!$row) {
            return redirect()->back()->with('error', 'Data temp tidak ditemukan.');
        }

        DB::table('tb_rincian_menu_temp')->where('id', $id)->delete();

        return redirect()
            ->route('rincian_bahan', ['idmenu' => $row->id_menu])
            ->with('success', 'Rincian menu temp berhasil dihapus.');
    }


    public function storeSayur(Request $request)
    {
        // 1?? Validasi input request (metode validateRequest adalah custom)
        $request->validate([
            'id_menu' => 'required',
            'id_sayur' => 'required|integer',

        ]);

        

        
        // 2?? Update kolom 'karbohidrat' pada tabel tb_menu sesuai ID menu
        DB::table('tb_menu')
            ->where('id', $request->id_menu)
            ->update([
                'sayur' => $request->id_sayur,
            ]);
        $cek_bahan = DB::table('tb_menu_bahan')->where('menu_id', $request->id_sayur,)->count();
        if ($cek_bahan == 0) {
            return redirect()->route('rincian_bahan', ['idmenu' => $request->id_menu])
                ->with('success', 'Sayur berhasil disimpan.');
        }
        // 3?? Hitung jumlah penerima kategori A dan B dari histori menu
        $jumlah_siswa_a = DB::table('rincian_sekolah')
            ->where('id_menu_harian', $request->id_menu)
            ->sum('jumlah_penerima_a');

        $jumlah_siswa_b = DB::table('rincian_sekolah')
            ->where('id_menu_harian', $request->id_menu)
            ->sum('jumlah_penerima_b');

        // 4?? Ambil input porsi A dan B
        $sayur_porsi_a = $request->porsi_a;
        $sayur_porsi_b = $request->porsi_b;
        $kebutuhan_total_matang = $jumlah_siswa_b * $sayur_porsi_b / 1000 + $jumlah_siswa_a * $sayur_porsi_a / 1000;


        $buffer = Buffer::first();
        $buffer = $buffer->buffer_menu;
        DB::table('tb_rumus_perhitungan_sayur')->insert([
            'id_menu' => $request->id_menu,
            'sayur_porsi_a' => $sayur_porsi_a,
            'sayur_porsi_b' => $sayur_porsi_b,
            'kebutuhan_total_matang' => $kebutuhan_total_matang + $kebutuhan_total_matang * $buffer /100,
            'status' => 1,

        ]);

        // 11?? Redirect kembali dengan pesan sukses
        return redirect()->route('rincian_bahan', ['idmenu' => $request->id_menu])
            ->with('success', 'Sayur berhasil disimpan.');
    }

    public function delete_sayur_temp($id)
    {
        // 1?? Ambil data menu berdasarkan ID
        $data = Menu::find($id);


        // 3?? Hapus data dari tabel tb_rumus_perhitungan_karbo
        // yang id_menu-nya = $id
        DB::table('tb_rumus_perhitungan_sayur')
            ->where('id_menu', $id)
            ->delete();

        DB::table('tb_rincian_menu_temp')->where('id_menu', $id)->where('id_resep', $data->sayur)
            ->delete();

        // 4?? Update kolom karbohidrat di tb_menu menjadi NULL
        DB::table('tb_menu')
            ->where('id', $id)
            ->update(['sayur' => null]);

        // 5?? Redirect kembali ke halaman rincian bahan dengan pesan sukses
        return redirect()
            ->route('rincian_bahan', ['idmenu' => $id])
            ->with('success', 'Sayur berhasil dihapus.');
    }

    public function storeSayur_simpan(Request $request)
    {
        // 1?? Validasi input request (metode validateRequest adalah custom)
        $request->validate([
            'id_menu' => 'required',
            //'id_sayur' => 'required|integer',

        ]);
        $kebutuhan_sayur_a = 0;
        $kebutuhan_sayur_b = 0;
        $kebutuhan_sayur_c = 0;
        $kebutuhan_sayur_d = 0;
        $penyusutan_sayur_a = 0;
        $penyusutan_sayur_b = 0;
        $penyusutan_sayur_c = 0;
        $penyusutan_sayur_d = 0;
        $kebutuhan_matang_a = 0;
        $kebutuhan_matang_b = 0;
        $kebutuhan_matang_c = 0;
        $kebutuhan_matang_d = 0;

        $buffer = Buffer::first();
        $buffer = $buffer->buffer_menu;
        $buffer = 0;
        if ($request->id_bahan_1_sayur != 0) {
            $kebutuhan_sayur_a  = $request->jumlah_bahan_1_sayur + $request->jumlah_bahan_1_sayur * $buffer / 100;
            $penyusutan_sayur_a = $request->penyusutan_1_sayur;
            $kebutuhan_matang_a = $request->jumlah_bahan_1_sayur - $request->penyusutan_1_sayur * $request->jumlah_bahan_1_sayur / 100;
        }
        if ($request->id_bahan_2_sayur != 0) {
            $kebutuhan_sayur_b  = $request->jumlah_bahan_2_sayur + $request->jumlah_bahan_2_sayur * $buffer / 100;
            $penyusutan_sayur_b = $request->penyusutan_2_sayur;
            $kebutuhan_matang_b = $request->jumlah_bahan_2_sayur - $request->penyusutan_2_sayur * $request->jumlah_bahan_2_sayur / 100;
        }
        if ($request->id_bahan_3_sayur != 0) {
            $kebutuhan_sayur_c  = $request->jumlah_bahan_3_sayur + $request->jumlah_bahan_3_sayur * $buffer / 100;
            $penyusutan_sayur_c = $request->penyusutan_3_sayur;
            $kebutuhan_matang_c = $request->jumlah_bahan_3_sayur - $request->penyusutan_3_sayur * $request->jumlah_bahan_3_sayur / 100;
        }
        if ($request->id_bahan_4_sayur != 0) {
            $kebutuhan_sayur_d  = $request->jumlah_bahan_4_sayur + $request->jumlah_bahan_4_sayur * $buffer / 100;
            $penyusutan_sayur_d = $request->penyusutan_4_sayur;
            $kebutuhan_matang_d = $request->jumlah_bahan_4_sayur - $request->penyusutan_4_sayur * $request->jumlah_bahan_4_sayur / 100;
        }
        

        DB::table('tb_rumus_perhitungan_sayur')->where('id_menu', $request->id_menu)->update([
            'kebutuhan_sayur_a' => $kebutuhan_sayur_a ,
            'kebutuhan_sayur_b' => $kebutuhan_sayur_b,
            'kebutuhan_sayur_c' => $kebutuhan_sayur_c,
            'kebutuhan_sayur_d' => $kebutuhan_sayur_d,
            'penyusutan_sayur_a' => $penyusutan_sayur_a,
            'penyusutan_sayur_b' => $penyusutan_sayur_b,
            'penyusutan_sayur_c' => $penyusutan_sayur_c,
            'penyusutan_sayur_d' => $penyusutan_sayur_d,
            'kebutuhan_matang_a' => $kebutuhan_matang_a,
            'kebutuhan_matang_b' => $kebutuhan_matang_b,
            'kebutuhan_matang_c' => $kebutuhan_matang_c,
            'kebutuhan_matang_d' => $kebutuhan_matang_d,
            'kebutuhan_matang_realisasi' => $request->kebutuhan_matang_realisasi_input_sayur,
            'kebutuhan_total_mentah' => $request->kebutuhan_mentah_input_sayur,
            'kapasitas_tilting' => 20,
            'jumlah_masak' => ceil($request->kebutuhan_mentah_input_sayur / 20),

            'status' => 2,

        ]);

        $jumlah_box_sayur_rencana =  $request->kebutuhan_mentah_input_sayur / 20;
        if($request->jumlah_box_sayur_rencana)
        {
           // $jumlah_box_sayur_rencana = $request->jumlah_box_sayur_rencana;
        }
        $jumlah_tilting = $request->kebutuhan_mentah_input_sayur / 20;
        $data_rumus_sayur = DB::table('tb_rumus_perhitungan_sayur')->where('id_menu', $request->id_menu)->first();
        $data_menu = Menu::where('id', $request->id_menu)->first();
        $data_sayur = MenuBahan::where('menu_id', $data_menu->sayur)->get();
        foreach ($data_sayur as $row) {
            
            
            $jumlah_box = 1;
            $harga = DB::table('tb_rincian_kontrak')->where('id_bahan', $row->bahan_id)
                ->where('status',  1)->orderByDesc('id') // ambil yang terbaru berdasarkan id
                ->first() ?? 0;
            $keterangan = DB::table('tb_spesifikasi_bahan')->where('id_bahan', $row->bahan_id)
                ->first();
            $keterangan = $keterangan->spesifikasi ?? '-';
            
            if ($row->status_bahan_baku == 1) {
                $data_box = DB::table('tb_box_bahan_baku')->where('id_bahan', $row->bahan_id)->select('isi_per_box')->first();
                if ($data_box) {
                    $box = floor($request->kebutuhan_mentah_input_sayur / 20);

                    // jika hasil floor = 0, maka dibuat 1
                    if ($box == 0) {
                        $box = 1;
                    }

                    $jumlah_box = $kebutuhan_sayur_a / $box;
                    $jumlah_masak = $box;
                    $jumlah_box = ceil(ceil($jumlah_box / ($data_box->isi_per_box/1000)) * $jumlah_masak);
                } else {
                    $jumlah_box = ($kebutuhan_sayur_a + $kebutuhan_sayur_a * $buffer / 100) / $jumlah_tilting;
                }


                if ($jumlah_box == 0 && !empty($row->bahan_id) && $kebutuhan_sayur_a > 0) {
                    $jumlah_box = 1;
                }
                DB::table('tb_rincian_menu_temp')->insert([
                    'id_menu' => $request->id_menu,
                    'id_resep'      => $data_menu->sayur,
                    'id_bahan'      => $row->bahan_id,
                    'jumlah'        => ($kebutuhan_sayur_a + $kebutuhan_sayur_a * $buffer / 100),
                    'bumbu'         => 0,
                    'harga'         => ($harga->harga_bahan ?? 0),
                    'total_harga'   => ($harga->harga_bahan ?? 0) * ($kebutuhan_sayur_a + $kebutuhan_sayur_a * $buffer / 100),
                    'jumlah_box'    => $jumlah_box,
                    'id_satuan'     => 25,
                    //'jumlah_box'     => 1,
                    'id_kontrak'     => $this->resolveRincianKontrakId($row->bahan_id, $harga),
                    'keterangan'     => $keterangan ?? '-'
                ]);
            } else if ($row->status_bahan_baku == 2) {
                $data_box = DB::table('tb_box_bahan_baku')->where('id_bahan', $row->bahan_id)->select('isi_per_box')->first();
                if ($data_box) {
                    $box = floor($request->kebutuhan_mentah_input_sayur / 20);

                    // jika hasil floor = 0, maka dibuat 1
                    if ($box == 0) {
                        $box = 1;
                    }

                    $jumlah_box = $kebutuhan_sayur_b / $box;
                    $jumlah_masak = $box;
                    $jumlah_box = ceil(ceil($jumlah_box / ($data_box->isi_per_box / 1000)) * $jumlah_masak);
                } else {
                    $jumlah_box = ($kebutuhan_sayur_b + $kebutuhan_sayur_b * $buffer / 100) / $jumlah_tilting;
                }

                if ($jumlah_box == 0 && !empty($row->bahan_id) && $kebutuhan_sayur_b > 0) {
                    $jumlah_box = 1;
                }
                DB::table('tb_rincian_menu_temp')->insert([
                    'id_menu' => $request->id_menu,
                    'id_resep'      => $data_menu->sayur,
                    'id_bahan'      => $row->bahan_id,
                    'jumlah'        => ($kebutuhan_sayur_b + $kebutuhan_sayur_b * $buffer / 100),
                    'bumbu'         => 0,
                    'harga'         => ($harga->harga_bahan ?? 0),
                    'total_harga'   => ($harga->harga_bahan ?? 0) * ($kebutuhan_sayur_b + $kebutuhan_sayur_b * $buffer / 100),
                    'jumlah_box'    => $jumlah_box,
                    'id_satuan'     => 25,
                    'id_kontrak'     => $this->resolveRincianKontrakId($row->bahan_id, $harga),
                    'keterangan'     => $keterangan ?? '-'
                ]);
            } else if ($row->status_bahan_baku == 4) {
                $data_box = DB::table('tb_box_bahan_baku')->where('id_bahan', $row->bahan_id)->select('isi_per_box')->first();
                if ($data_box) {
                    $box = floor($request->kebutuhan_mentah_input_sayur / 20);

                    // jika hasil floor = 0, maka dibuat 1
                    if ($box == 0) {
                        $box = 1;
                    }

                    $jumlah_box = $kebutuhan_sayur_c / $box;
                    $jumlah_masak = $box;
                    $jumlah_box = ceil(ceil($jumlah_box / ($data_box->isi_per_box / 1000)) * $jumlah_masak);
                } else {
                    $jumlah_box = ($kebutuhan_sayur_a + $kebutuhan_sayur_c * $buffer / 100) / $jumlah_tilting;
                }

                if ($jumlah_box == 0 && !empty($row->bahan_id) && $kebutuhan_sayur_c > 0) {
                    $jumlah_box = 1;
                }
                DB::table('tb_rincian_menu_temp')->insert([
                    'id_menu' => $request->id_menu,
                    'id_resep'      => $data_menu->sayur,
                    'id_bahan'      => $row->bahan_id,
                    'jumlah'        => ($kebutuhan_sayur_c + $kebutuhan_sayur_c * $buffer / 100),
                    'bumbu'         => 0,
                    'harga'         => ($harga->harga_bahan ?? 0),
                    'total_harga'   => ($harga->harga_bahan ?? 0) * ($kebutuhan_sayur_c + $kebutuhan_sayur_c * $buffer / 100),
                    'jumlah_box'    => $jumlah_box,
                    'id_satuan'     => 25,
                    'id_kontrak'     => $this->resolveRincianKontrakId($row->bahan_id, $harga),
                    'keterangan'     => $keterangan ?? '-'
                ]);
            } else if ($row->status_bahan_baku == 5) {
                $data_box = DB::table('tb_box_bahan_baku')->where('id_bahan', $row->bahan_id)->select('isi_per_box')->first();
                if ($data_box) {
                    $box = floor($request->kebutuhan_mentah_input_sayur / 20);

                    // jika hasil floor = 0, maka dibuat 1
                    if ($box == 0) {
                        $box = 1;
                    }

                    $jumlah_box = $kebutuhan_sayur_d / $box;
                    $jumlah_masak = $box;
                    $jumlah_box = ceil(ceil($jumlah_box / ($data_box->isi_per_box / 1000)) * $jumlah_masak);
                } else {
                    $jumlah_box = ($kebutuhan_sayur_d + $kebutuhan_sayur_a * $buffer / 100) / $jumlah_tilting;
                }

                if ($jumlah_box == 0 && !empty($row->bahan_id) && $kebutuhan_sayur_d > 0) {
                    $jumlah_box = 1;
                }
                DB::table('tb_rincian_menu_temp')->insert([
                    'id_menu' => $request->id_menu,
                    'id_resep'      => $data_menu->sayur,
                    'id_bahan'      => $row->bahan_id,
                    'jumlah'        => ($kebutuhan_sayur_d + $kebutuhan_sayur_d * $buffer / 100 ),
                    'bumbu'         => 0,
                    'harga'         => ($harga->harga_bahan ?? 0),
                    'total_harga'   => ($harga->harga_bahan ?? 0) * ($kebutuhan_sayur_d + $kebutuhan_sayur_d * $buffer / 100),
                    'jumlah_box'    => $jumlah_box,
                    'id_satuan'     => 25,
                    'id_kontrak'     => $this->resolveRincianKontrakId($row->bahan_id, $harga),
                    'keterangan'     => $keterangan ?? '-'

                ]);
            } else {
                $data_perhitungan_bumbu = PerhitunganBumbu::where('id_menu_bahan', $row->id)->first();
                $pengali = $data_perhitungan_bumbu->pengali ?? 10;
                $pembagi = $data_perhitungan_bumbu->pembagi ?? 115;
                $jumlah_kebutuhan = $request->kebutuhan_mentah_input_sayur * 1000;
                $jumlah_kebutuhan = $jumlah_kebutuhan / $pembagi;
                $jumlah_kebutuhan = $jumlah_kebutuhan * $pengali;

                $bahan = TbMasterBahan::find($row->bahan_id);
                if (!$bahan) {
                    $jumlah_kebutuhan = 9999;

                    $satuan_bahan = 1;
                } else {
                    $jumlah_kebutuhan = $jumlah_kebutuhan;
                    if ($bahan->satuan_bahan == 1) {
                        $satuan_bahan = 1;
                    } else if ($bahan->satuan_bahan == 27) {
                        $satuan_bahan = 27;
                    } else if ($bahan->satuan_bahan == 23) {
                        $satuan_bahan = 23;
                    } else if ($bahan->satuan_bahan == 32) {
                        $satuan_bahan = 32;
                    } else {
                        $satuan_bahan = 1;
                    }
                }
                $nama_satuan = TbSatuan::find($satuan_bahan);
                if ($jumlah_box == 0 && !empty($row->bahan_id) && $jumlah_kebutuhan > 0) {
                    $jumlah_box = 1;
                }
                if ($satuan_bahan == 1 || $satuan_bahan == 27) 
                {
                    if (in_array($satuan_bahan , [1, 27])) {

                        $batas = [
                            25, 50, 100, 200, 300, 400, 500, 600, 700, 800, 900,
                            1000, 1200, 1400, 1500, 1800, 2000, 2250, 2500, 2750,
                            3000, 3250, 3500, 3750, 4000, 4250, 4500, 4750, 5000,
                            5500, 6000, 6500, 7000, 7500, 8000, 8500, 9000, 9500,
                            10000, 11000, 12000
                        ];

                        foreach ($batas as $nilai) {
                            if ($jumlah_kebutuhan < $nilai) {
                                $jumlah_kebutuhan = $nilai;
                                break;
                            }
                        }

                        // Jika lebih dari 12000
                        if ($jumlah_kebutuhan > 12000) {
                            $jumlah_kebutuhan = ceil($jumlah_kebutuhan / 1000) * 1000;
                        }

                    } else {
                        // Selain satuan 1 / 27 → bulatkan ke atas tanpa koma
                        $jumlah_kebutuhan = ceil($jumlah);
                    }
                    DB::table('tb_rincian_menu_temp')->insert([
                        'id_menu' => $request->id_menu,
                        'id_resep'      => $data_menu->sayur,
                        'id_bahan'      => $row->bahan_id,
                        'jumlah'        => $jumlah_kebutuhan ?? 0,
                        'bumbu'         => 0,
                        'harga'         => ($harga->harga_bahan ?? 0),
                        'total_harga'   => ($harga->harga_bahan ?? 0) / 1000 * $jumlah_kebutuhan,
                        'jumlah_box'    => $jumlah_box,
                        'id_satuan'     => ($satuan_bahan ?? 2),
                        'id_kontrak'     => $this->resolveRincianKontrakId($row->bahan_id, $harga),
                        'keterangan'     => $keterangan ?? '-'
                    ]);
                }else{
                    DB::table('tb_rincian_menu_temp')->insert([
                        'id_menu' => $request->id_menu,
                        'id_resep'      => $data_menu->sayur,
                        'id_bahan'      => $row->bahan_id,
                        'jumlah'        => $jumlah_kebutuhan ?? 0,
                        'bumbu'         => 0,
                        'harga'         => ($harga->harga_bahan ?? 0),
                        'total_harga'   => ($harga->harga_bahan ?? 0)  * $jumlah_kebutuhan,
                        'jumlah_box'    => $jumlah_box,
                        'id_satuan'     => ($satuan_bahan ?? 2),
                        'id_kontrak'     => $this->resolveRincianKontrakId($row->bahan_id, $harga),
                        'keterangan'     => $keterangan ?? '-'
                    ]);
                }

                
            }
            
        }
        $menu = Menu::find($request->id_menu);
            $realisasiAkg = DB::table('tb_resep_realisasi_akg')
                    ->where('id_resep',  $data_menu->sayur)
                    ->get();

            $energi = 0;
            $protein = 0;
            $lemak = 0;
            $karbohidrat = 0;
            $serat = 0;
            $natrium = 0;
            foreach ($realisasiAkg as $row) {
                DB::table('tb_menu_akg')
                    ->insert([
                        'id_menu' => $request->id_menu,
                        'komponen_sehat' => 'Sayur',
                        'id_nutrisi' => $data_menu->sayur,
                        'energi' => $row->energi_kcal,
                        'protein' => $row->protein_g,
                        'lemak' => $row->lemak_g,
                        'karbo' => $row->karbohidrat_g,
                        'serat' => $row->serat_g,
                        'natrium' => $row->natrium_mg,
                    ]);
            
            }

        // 11?? Redirect kembali dengan pesan sukses
        return redirect()->route('rincian_bahan', ['idmenu' => $request->id_menu])
            ->with('success', 'Sayur berhasil disimpan.');
    }

    public function dt_sayur_temp($id_menu)
    {
        // Ambil data Menu berdasarkan id_menu
        $data = Menu::where('id', $id_menu)->first();

        // Query rincian bahan resep karbohidrat
        /*if ($data->status_pengajuan == 'pending') {
            $table = DB::table('tb_rincian_menu_temp')
                ->join('tb_master_bahan', 'tb_rincian_menu_temp.id_bahan', '=', 'tb_master_bahan.id')
                ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
                ->select(
                    'tb_master_bahan.bahan',

                    'tb_rincian_menu_temp.id',
                    'tb_rincian_menu_temp.jumlah',
                    'tb_rincian_menu_temp.harga',
                    'tb_rincian_menu_temp.total_harga',
                    'tb_satuan.satuan',
                    'tb_rincian_menu_temp.id_satuan'
                )
                ->where('tb_rincian_menu_temp.id_resep', $data->sayur)
                ->where('tb_rincian_menu_temp.id_menu', $id_menu)
                ->get();
        } else {
            $table = DB::table('tb_rincian_menu_temp')
                ->join('tb_master_bahan', 'tb_rincian_menu_temp.id_bahan', '=', 'tb_master_bahan.id')
                ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
                ->select(
                    'tb_master_bahan.bahan',

                    'tb_rincian_menu_temp.id',
                    'tb_rincian_menu_temp.jumlah',
                    'tb_rincian_menu_temp.harga',
                    'tb_rincian_menu_temp.total_harga',
                    'tb_satuan.satuan',
                    'tb_rincian_menu_temp.id_satuan'
                )
                ->where('tb_rincian_menu_temp.id_resep', $data->sayur)
                ->where('tb_rincian_menu_temp.id_menu', $id_menu)
                ->get();
        }*/

        $table = DB::table('tb_rincian_menu_temp')
            ->join('tb_master_bahan', 'tb_rincian_menu_temp.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'tb_rincian_menu_temp.id_satuan', '=', 'tb_satuan.id')
            ->select(
                'tb_master_bahan.bahan',

                'tb_rincian_menu_temp.id',
                'tb_rincian_menu_temp.jumlah',
                'tb_rincian_menu_temp.harga',
                'tb_rincian_menu_temp.total_harga',
                'tb_rincian_menu_temp.keterangan',
                'tb_rincian_menu_temp.jumlah_box',
                'tb_satuan.satuan',
                'tb_rincian_menu_temp.id_satuan'
            )
            ->where('tb_rincian_menu_temp.id_resep', $data->sayur)
            ->where('tb_rincian_menu_temp.id_menu', $id_menu)
            ->get();
        // Return data untuk DataTables
        return DataTables::of($table)
            ->addIndexColumn()

            // Kolom jumlah bahan dengan format satuan
            ->editColumn('jumlah_bahan', function ($row) {
                $data_satuan = TbSatuan::find($row->id_satuan);
                $satuan = $data_satuan->satuan;
                return '<input type="number" class="form-control jumlah" data-id="' . $row->id . '" value="' . $row->jumlah . '"> <button class="btn btn-success btn-sm update-jumlah" data-id="' . $row->id . '">Update</button>';
                if ($row->bahan == 'beras') {
                    return number_format($row->jumlah, 0, '', '.') . ' kg';
                } else {
                    return number_format($row->jumlah, 0, '', '.') . ' ' .  $satuan;
                }
            })

            // Kolom harga satuan
            ->addColumn('harga_satuan', function ($row) {
                return 'Rp. ' . number_format($row->harga, 0, '', '.');
            })

            // Kolom total harga dibayar
            ->addColumn('total_dibayar', function ($row) {
                return 'Rp. ' . number_format($row->total_harga, 0, '', '.');
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('rincian_menu_temp.delete', $row->id) . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Yakin hapus bahan ini?\')">Delete</a>';
            })
            ->addColumn('input_harga', function ($row) {
                return '<input type="number" class="form-control jumlah_harga" data-id="' . $row->id . '" value="' . $row->harga . '"> <button class="btn btn-success btn-sm update-jumlah-harga" data-id="' . $row->id . '">Update</button>';

                //return number_format($row->jumlah, 0, ',', '.'); // Format: 1.234,56
            })
            ->addColumn('input_jumlah_box', function ($row) {
                return '<input type="number" class="form-control jumlah_box" data-id="' . $row->id . '" value="' . $row->jumlah_box . '"> <button class="btn btn-success btn-sm update-jumlah-box" data-id="' . $row->id . '">Update</button>';

                //return number_format($row->jumlah, 0, ',', '.'); // Format: 1.234,56
            })
            ->addColumn('input_keterangan', function ($row) {
                return '<input type="text" class="form-control jumlah_keterangan" data-id="' . $row->id . '" value="' . $row->keterangan . '"> <button class="btn btn-success btn-sm update-jumlah-keterangan" data-id="' . $row->id . '">Update</button>';

                //return number_format($row->jumlah, 0, ',', '.'); // Format: 1.234,56
            })
            ->rawColumns(['jumlah_bahan', 'action', 'harga_satuan', 'total_dibayar', 'input_keterangan', 'input_jumlah_box', 'input_harga'])
            ->make(true);
    }

    public function storeProtein(Request $request)
    {
        // 1?? Validasi input request (metode validateRequest adalah custom)
        $request->validate([
            'id_menu' => 'required',
            'id_protein' => 'required|integer',

        ]);

        // 2?? Update kolom 'Protein' pada tabel tb_menu sesuai ID menu
        DB::table('tb_menu')
            ->where('id', $request->id_menu)
            ->update([
                'protein' => $request->id_protein,
            ]);
        $cek_bahan = DB::table('tb_menu_bahan')->where('menu_id', $request->id_protein,)->count();
        if ($cek_bahan == 0) {
            return redirect()->route('rincian_bahan', ['idmenu' => $request->id_menu])
                ->with('success', 'Sayur berhasil disimpan.');
        }
        // 3?? Hitung jumlah penerima kategori A dan B dari histori menu
        $jumlah_siswa_a = DB::table('rincian_sekolah')
            ->where('id_menu_harian', $request->id_menu)
            ->sum('jumlah_penerima_a');

        $jumlah_siswa_b = DB::table('rincian_sekolah')
            ->where('id_menu_harian', $request->id_menu)
            ->sum('jumlah_penerima_b');

        // 4?? Ambil input porsi A dan B
        $protein_porsi_a = $request->porsi_a;
        $protein_porsi_b = $request->porsi_b;
        $data_menu = Menu::find($request->id_menu);
        $data_resep = MenuBahan::where('menu_id', $data_menu->protein)->where('status_bahan_baku', 1)->first();
        $data_bahan = TbMasterBahan::find($data_resep->bahan_id);

        $kebutuhan_total_matang = $jumlah_siswa_b * $protein_porsi_b / 1000 + $jumlah_siswa_a * $protein_porsi_a / 1000;
        if ($data_bahan->satuan_bahan == 36 || $data_bahan->satuan_bahan == 40 ||  $data_bahan->satuan_bahan == 32) {
            $kebutuhan_total_matang = $kebutuhan_total_matang * 10;
        } else {
        }

        $buffer = Buffer::first();
        $buffer = $buffer->buffer_menu;
        DB::table('tb_rumus_perhitungan_protein')->insert([
            'id_menu' => $request->id_menu,
            'protein_porsi_a' => $protein_porsi_a,
            'protein_porsi_b' => $protein_porsi_b,
            'kebutuhan_total_matang' => $kebutuhan_total_matang + $kebutuhan_total_matang * $buffer/ 100 ,
            'status' => 1,

        ]);

        // 11?? Redirect kembali dengan pesan sukses
        return redirect()->route('rincian_bahan', ['idmenu' => $request->id_menu])
            ->with('success', 'Lauk berhasil disimpan.');
    }

    public function delete_protein_temp($id)
    {
        // 1?? Ambil data menu berdasarkan ID
        $data = Menu::find($id);


        // 3?? Hapus data dari tabel tb_rumus_perhitungan_karbo
        // yang id_menu-nya = $id
        DB::table('tb_rumus_perhitungan_protein')
            ->where('id_menu', $id)
            ->delete();

        DB::table('tb_rincian_menu_temp')->where('id_menu', $id)->where('id_resep', $data->protein)
            ->delete();

        // 4?? Update kolom karbohidrat di tb_menu menjadi NULL
        DB::table('tb_menu')
            ->where('id', $id)
            ->update(['protein' => null]);

        // 5?? Redirect kembali ke halaman rincian bahan dengan pesan sukses
        return redirect()
            ->route('rincian_bahan', ['idmenu' => $id])
            ->with('success', 'Lauk berhasil dihapus.');
    }

    public function storeProtein_simpan(Request $request)
    {
        // 1?? Validasi input request (metode validateRequest adalah custom)
        $request->validate([
            'id_menu' => 'required',
            //'id_sayur' => 'required|integer',

        ]);
        $kebutuhan_protein_a = 0;
        $kebutuhan_protein_b = 0;
        $penyusutan_protein_a = 0;
        $penyusutan_protein_b = 0;
        $kebutuhan_matang_a = 0;
        $kebutuhan_matang_b = 0;

        $buffer = Buffer::first();
        $buffer = $buffer->buffer_menu;
        $buffer = 0;
        if ($request->id_bahan_1 != 0) {
            $kebutuhan_protein_a  = $request->jumlah_bahan_1 + $request->jumlah_bahan_1 * $buffer / 100;
            $penyusutan_protein_a = $request->penyusutan_1;
            $kebutuhan_matang_a = $kebutuhan_protein_a - $request->penyusutan_1 * $kebutuhan_protein_a / 100;
        }
        if ($request->id_bahan_2 != 0) {
            $kebutuhan_protein_b  = $request->jumlah_bahan_2 + $request->jumlah_bahan_2 * $buffer / 100;
            $penyusutan_protein_b = $request->penyusutan_2;
            $kebutuhan_matang_b = $kebutuhan_protein_b - $request->penyusutan_2 * $kebutuhan_protein_b / 100;
        }
        $data_bahan = TbMasterBahan::find($request->id_bahan_1);
        if ($data_bahan->satuan_bahan == 1) {
            $tilting = 20;
            $jumlah_tilting = $request->kebutuhan_mentah_input / $tilting;
        } else {
            $tilting = 20000;
            $jumlah_tilting = $request->kebutuhan_mentah_input * 100 / $tilting;
        }

        DB::table('tb_rumus_perhitungan_protein')->where('id_menu', $request->id_menu)->update([
            'kebutuhan_protein_a' => $kebutuhan_protein_a,
            'kebutuhan_protein_b' => $kebutuhan_protein_b,
            'penyusutan_protein_a' => $penyusutan_protein_a,
            'penyusutan_protein_b' => $penyusutan_protein_b,
            'kebutuhan_matang_a' => $kebutuhan_matang_a,
            'kebutuhan_matang_b' => $kebutuhan_matang_b,
            'kebutuhan_matang_realisasi' => $request->kebutuhan_matang_realisasi_input,
            'kebutuhan_total_mentah' => $request->kebutuhan_mentah_input + $request->kebutuhan_mentah_input * $buffer /100,
            'kapasitas_tilting' => 20,
            'jumlah_masak' => $jumlah_tilting,

            'status' => 2,

        ]);
        $jumlah_box_rencana = $jumlah_tilting;
        if($request->jumlah_box_rencana)
        {
            $jumlah_box_rencana = $request->jumlah_box_rencana;
        }

        $data_rumus_protein = DB::table('tb_rumus_perhitungan_protein')->where('id_menu', $request->id_menu)->first();
        $data_menu = Menu::where('id', $request->id_menu)->first();
        $data_protein = MenuBahan::where('menu_id', $data_menu->protein)->orderByRaw("FIELD(status_bahan_baku, 1, 2, 4, 5, 3)")
            ->get();
        $data_bahan_utama = MenuBahan::where('menu_id', $data_menu->protein)->where('status_bahan_baku', 1)->first();
        $data_bahan_utama = TbMasterBahan::find($data_bahan_utama->bahan_id);
        $utama = 1;
        foreach ($data_protein as $row) {

            $harga = DB::table('tb_rincian_kontrak')->where('id_bahan', $row->bahan_id)
                ->where('status',  1)->orderByDesc('id') // ambil yang terbaru berdasarkan id
                ->first() ?? 0;
            $data_bahan = TbMasterBahan::find($row->bahan_id);
            //$data_bahan = MenuBahan::where('menu_id', $data_menu->protein)->where('bahan_id', $row->bahan_id)->first();
            $satuan = 1;
            $kebutuhan_mentah = $request->kebutuhan_mentah_input;

            $jumlah_box = 1;
            $keterangan = DB::table('tb_spesifikasi_bahan')->where('id_bahan', $row->bahan_id)
                ->first();
            $keterangan = $keterangan->spesifikasi ?? '-';
            if ($row->status_bahan_baku == 1) {
                if ($data_bahan->satuan_bahan == 36 || $data_bahan_utama->satuan_bahan == 40 ) {
                    $satuan = 36;
                    $kebutuhan_protein_a    = $data_rumus_protein->kebutuhan_protein_a;
                    $kebutuhan_protein_b    = $data_rumus_protein->kebutuhan_protein_b;
                    $utama = 36;
                }
                else if ( $data_bahan_utama->satuan_bahan == 32) {
                    $satuan = 32;
                    $utama = 32;
                    $kebutuhan_protein_a    = $data_rumus_protein->kebutuhan_protein_a;
                    $kebutuhan_protein_b    = $data_rumus_protein->kebutuhan_protein_b;
                } else if ($data_bahan_utama->satuan_bahan == 22) {
                    $satuan = 22;
                    $utama = 22;
                    $kebutuhan_protein_a    = $data_rumus_protein->kebutuhan_protein_a;
                    $kebutuhan_protein_b    = $data_rumus_protein->kebutuhan_protein_b;
                }
                 elseif($data_bahan_utama->satuan_bahan == 1) {
                     $satuan = 25;
                     $utama = 25;
                } else {    
                    $utama = $data_bahan_utama->satuan_bahan;
                    $satuan = $data_bahan_utama->satuan_bahan;
                    $kebutuhan_protein_a    = $data_rumus_protein->kebutuhan_protein_a;
                    $kebutuhan_protein_b    = $data_rumus_protein->kebutuhan_protein_b;
                }
                $jumlah_box = ($kebutuhan_protein_a + $kebutuhan_protein_a* $buffer / 100) / $jumlah_tilting;
                DB::table('tb_rincian_menu_temp')->insert([
                    'id_menu' => $request->id_menu,
                    'id_resep'      => $data_menu->protein,
                    'id_bahan'      => $row->bahan_id,
                    'jumlah'        => ($kebutuhan_protein_a + $kebutuhan_protein_a * $buffer / 100),
                    'bumbu'         => 0,
                    'harga'         => $harga->harga_bahan ?? 0,
                    'total_harga'   => ($harga->harga_bahan ?? 0) * ($kebutuhan_protein_a + $kebutuhan_protein_a * $buffer / 100),
                    'jumlah_box'    => $jumlah_box_rencana,
                    'id_satuan'     => $satuan,
                    'id_kontrak'     => $this->resolveRincianKontrakId($row->bahan_id, $harga),
                    'keterangan'    => $keterangan

                ]);
            } else if ($row->status_bahan_baku == 2) {
                if ($data_bahan->satuan_bahan == 36 || $data_bahan_utama->satuan_bahan == 40) {
                    $satuan = 36;
                    $kebutuhan_protein_a    = $data_rumus_protein->kebutuhan_protein_a;
                    $kebutuhan_protein_b    = $data_rumus_protein->kebutuhan_protein_b;
                } else {
                    $satuan = 25;
                }
                $jumlah_box = ($kebutuhan_protein_a + $kebutuhan_protein_a * $buffer / 100) / $jumlah_tilting;

                DB::table('tb_rincian_menu_temp')->insert([
                    'id_menu' => $request->id_menu,
                    'id_resep'      => $data_menu->protein,
                    'id_bahan'      => $row->bahan_id,
                    'jumlah'        => ($kebutuhan_protein_b + $kebutuhan_protein_b * $buffer / 100),
                    'bumbu'         => 0,
                    'harga'         => ($harga->harga_bahan ?? 0),
                    'total_harga'   => ($harga->harga_bahan ?? 0) * ($kebutuhan_protein_b + $kebutuhan_protein_b * $buffer / 100),
                    'jumlah_box'    => $jumlah_box_rencana,
                    'id_satuan'     => $satuan,
                    'id_kontrak'     => $this->resolveRincianKontrakId($row->bahan_id, $harga),
                    'keterangan'    => $keterangan

                ]);
            } else {
                $data_perhitungan_bumbu = PerhitunganBumbu::where('id_menu_bahan', $row->id)->first();
                $pengali = $data_perhitungan_bumbu->pengali ?? 10;
                $pembagi = $data_perhitungan_bumbu->pembagi ?? 115;
                $kebutuhan_mentah       = $kebutuhan_mentah * 100;
                $jumlah_kebutuhan = $kebutuhan_mentah / $pembagi * $pengali;
                if (!$data_bahan) {
                    $jumlah_kebutuhan = 9999;

                    $satuan_bahan = 1;
                } else {
                    $jumlah_kebutuhan = $jumlah_kebutuhan;
                    if ($data_bahan->satuan_bahan == 1) {
                        $satuan_bahan = 1;
                    } else if ($data_bahan->satuan_bahan == 27) {
                        $satuan_bahan = 27;
                    } else if ($data_bahan->satuan_bahan == 23) {
                        $satuan_bahan = 23;
                    } else if ($data_bahan->satuan_bahan == 32) {
                        $satuan_bahan = 32;
                    } else {
                        $satuan_bahan = $data_bahan->satuan_bahan;
                    }
                }
                if($utama == 25 || $utama == 1)
                {
                    $jumlah_kebutuhan = $jumlah_kebutuhan * 10;
                }else {
                    $jumlah_kebutuhan = $jumlah_kebutuhan / 100;
                }
                $nama_satuan = TbSatuan::find($satuan_bahan);
                if ($satuan_bahan == 1 || $satuan_bahan == 27) 
                    {
                        if (in_array($satuan_bahan , [1, 27])) {

                        $batas = [
                            25, 50, 100, 200, 300, 400, 500, 600, 700, 800, 900,
                            1000, 1200, 1400, 1500, 1800, 2000, 2250, 2500, 2750,
                            3000, 3250, 3500, 3750, 4000, 4250, 4500, 4750, 5000,
                            5500, 6000, 6500, 7000, 7500, 8000, 8500, 9000, 9500,
                            10000, 11000, 12000
                        ];

                        foreach ($batas as $nilai) {
                            if ($jumlah_kebutuhan < $nilai) {
                                $jumlah_kebutuhan = $nilai;
                                break;
                            }
                        }

                        // Jika lebih dari 12000
                        if ($jumlah_kebutuhan > 12000) {
                            $jumlah_kebutuhan = ceil($jumlah_kebutuhan / 1000) * 1000;
                        }

                    } else {
                        // Selain satuan 1 / 27 → bulatkan ke atas tanpa koma
                        $jumlah_kebutuhan = ceil($jumlah);
                    }
                    DB::table('tb_rincian_menu_temp')->insert([
                        'id_menu' => $request->id_menu,
                        'id_resep'      => $data_menu->protein,
                        'id_bahan'      => $row->bahan_id,
                        'jumlah'        => $jumlah_kebutuhan ,
                        'bumbu'         => 0,
                        'harga'         => ($harga->harga_bahan ?? 0),
                        'total_harga'   => ($harga->harga_bahan ?? 0) / 1000 * $jumlah_kebutuhan ,
                        'jumlah_box'    => $jumlah_box,
                        'id_satuan'     => $satuan_bahan ?? 0,
                        //'id_satuan'     => $data_bahan->id_satuan ?? 0,
                        'id_kontrak'     => $this->resolveRincianKontrakId($row->bahan_id, $harga),
                        'keterangan'    => $keterangan

                    ]);
                }else{
                    DB::table('tb_rincian_menu_temp')->insert([
                        'id_menu' => $request->id_menu,
                        'id_resep'      => $data_menu->protein,
                        'id_bahan'      => $row->bahan_id,
                        'jumlah'        => $jumlah_kebutuhan * 10,
                        'bumbu'         => 0,
                        'harga'         => ($harga->harga_bahan ?? 0),
                        'total_harga'   => ($harga->harga_bahan ?? 0)  * $jumlah_kebutuhan * 10,
                        'jumlah_box'    => $jumlah_box,
                        'id_satuan'     => $satuan_bahan ?? 0,
                        //'id_satuan'     => $data_bahan->id_satuan ?? 0,
                        'id_kontrak'     => $this->resolveRincianKontrakId($row->bahan_id, $harga),
                        'keterangan'    => $keterangan

                    ]);
                }
                
            }
        }
        $menu = Menu::find($request->id_menu);
            $realisasiAkg = DB::table('tb_resep_realisasi_akg')
                    ->where('id_resep',  $data_menu->protein)
                    ->get();

            $energi = 0;
            $protein = 0;
            $lemak = 0;
            $karbohidrat = 0;
            $serat = 0;
            $natrium = 0;
            foreach ($realisasiAkg as $row) {
                DB::table('tb_menu_akg')
                    ->insert([
                        'id_menu' => $request->id_menu,
                        'komponen_sehat' => 'Protein',
                        'id_nutrisi' => $data_menu->protein,
                        'energi' => $row->energi_kcal,
                        'protein' => $row->protein_g,
                        'lemak' => $row->lemak_g,
                        'karbo' => $row->karbohidrat_g,
                        'serat' => $row->serat_g,
                        'natrium' => $row->natrium_mg,
                    ]);
            
            }
        // 11?? Redirect kembali dengan pesan sukses
        return redirect()->route('rincian_bahan', ['idmenu' => $request->id_menu])
            ->with('success', 'Lauk berhasil disimpan.');
    }

    public function dt_protein_temp($id_menu)
    {
        // Ambil data Menu berdasarkan id_menu
        $data = Menu::where('id', $id_menu)->first();

        // Query rincian bahan resep karbohidrat
        /*if ($data->status_pengajuan == 'pending') {
            $table = DB::table('tb_rincian_menu_temp')
                ->join('tb_master_bahan', 'tb_rincian_menu_temp.id_bahan', '=', 'tb_master_bahan.id')
                ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
                ->select(
                    'tb_master_bahan.bahan',

                    'tb_rincian_menu_temp.id',
                    'tb_rincian_menu_temp.jumlah',
                    'tb_rincian_menu_temp.harga',
                    'tb_rincian_menu_temp.total_harga',
                    'tb_satuan.satuan',
                    'tb_rincian_menu_temp.id_satuan'
                )
                ->where('tb_rincian_menu_temp.id_resep', $data->protein)
                ->where('tb_rincian_menu_temp.id_menu', $id_menu)
                ->get();
        } else {
            $table = DB::table('rincian_menu_harian')
                ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
                ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
                ->select(
                    'tb_master_bahan.bahan',

                    'rincian_menu_harian.id',
                    'rincian_menu_harian.jumlah',
                    'rincian_menu_harian.harga',
                    'rincian_menu_harian.total_harga',
                    'tb_satuan.satuan',
                    'rincian_menu_harian.id_satuan'
                )
                ->where('rincian_menu_harian.id_resep', $data->protein)
                ->where('rincian_menu_harian.id_menu_harian', $id_menu)
                ->get();
        }*/
        $table = DB::table('tb_rincian_menu_temp')
            ->join('tb_master_bahan', 'tb_rincian_menu_temp.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'tb_rincian_menu_temp.id_satuan', '=', 'tb_satuan.id')
            ->select(
                'tb_master_bahan.bahan',

                'tb_rincian_menu_temp.id',
                'tb_rincian_menu_temp.jumlah',
                'tb_rincian_menu_temp.harga',
                'tb_rincian_menu_temp.total_harga',
                'tb_rincian_menu_temp.keterangan',
                'tb_rincian_menu_temp.jumlah_box',
                'tb_satuan.satuan',
                'tb_rincian_menu_temp.id_satuan'
            )
            ->where('tb_rincian_menu_temp.id_resep', $data->protein)
            ->where('tb_rincian_menu_temp.id_menu', $id_menu)
            ->get();

        // Return data untuk DataTables
        return DataTables::of($table)
            ->addIndexColumn()

            // Kolom jumlah bahan dengan format satuan
            ->editColumn('jumlah_bahan', function ($row) {
                $data_satuan = TbSatuan::find($row->id_satuan);
                $satuan = $data_satuan->satuan;
                return '<input type="number" class="form-control jumlah" data-id="' . $row->id . '" value="' . $row->jumlah . '" step="0.01"> <button class="btn btn-success btn-sm update-jumlah" data-id="' . $row->id . '">Update</button>';
                if ($row->bahan == 'beras') {
                    return number_format($row->jumlah, 0, '', '.') . ' kg';
                } else {
                    return number_format($row->jumlah, 0, '', '.') . ' ' .  $satuan;
                }
            })

            // Kolom harga satuan
            ->addColumn('harga_satuan', function ($row) {
                return 'Rp. ' . number_format($row->harga, 0, '', '.');
            })

            // Kolom total harga dibayar
            ->addColumn('total_dibayar', function ($row) {
                return 'Rp. ' . number_format($row->total_harga, 0, '', '.');
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('rincian_menu_temp.delete', $row->id) . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Yakin hapus bahan ini?\')">Delete</a>';
            })
            ->addColumn('input_harga', function ($row) {
                return '<input type="number" class="form-control jumlah_harga" data-id="' . $row->id . '" value="' . $row->harga . '"> <button class="btn btn-success btn-sm update-jumlah-harga" data-id="' . $row->id . '">Update</button>';

                //return number_format($row->jumlah, 0, ',', '.'); // Format: 1.234,56
            })
            ->addColumn('input_jumlah_box', function ($row) {
                return '<input type="number" class="form-control jumlah_box" data-id="' . $row->id . '" value="' . $row->jumlah_box . '"> <button class="btn btn-success btn-sm update-jumlah-box" data-id="' . $row->id . '">Update</button>';

                //return number_format($row->jumlah, 0, ',', '.'); // Format: 1.234,56
            })
            ->addColumn('input_keterangan', function ($row) {
                return '<input type="text" class="form-control jumlah_keterangan" data-id="' . $row->id . '" value="' . $row->keterangan . '"> <button class="btn btn-success btn-sm update-jumlah-keterangan" data-id="' . $row->id . '">Update</button>';

                //return number_format($row->jumlah, 0, ',', '.'); // Format: 1.234,56
            })
            ->rawColumns(['jumlah_bahan', 'action', 'harga_satuan', 'total_dibayar', 'input_keterangan', 'input_jumlah_box', 'input_harga'])
            ->make(true);
    }

    public function storeBuah(Request $request)
    {
        // 1?? Validasi input request (metode validateRequest adalah custom)
        $request->validate([
            'id_menu' => 'required',
            'id_buah' => 'required|integer',

        ]);

        // 2?? Update kolom 'karbohidrat' pada tabel tb_menu sesuai ID menu
        DB::table('tb_menu')
            ->where('id', $request->id_menu)
            ->update([
                'buah' => $request->id_buah,
            ]);

        // 3?? Hitung jumlah penerima kategori A dan B dari histori menu
        $jumlah_siswa_a = DB::table('rincian_sekolah')
            ->where('id_menu_harian', $request->id_menu)
            ->sum('jumlah_penerima_a');

        $jumlah_siswa_b = DB::table('rincian_sekolah')
            ->where('id_menu_harian', $request->id_menu)
            ->sum('jumlah_penerima_b');
        $buffer = Buffer::first();
        $buffer = $buffer->buffer_menu;
        // 4?? Ambil input porsi A dan B
        $buah_porsi_a = $request->porsi_a;
        $buah_porsi_b = $request->porsi_b;
        $kebutuhan_total_matang = $jumlah_siswa_b * $buah_porsi_b + $jumlah_siswa_a * $buah_porsi_a;
        $kebutuhan_total_matang = $kebutuhan_total_matang + $kebutuhan_total_matang * $buffer / 100;
        


        DB::table('tb_rumus_perhitungan_buah')->insert([
            'id_menu' => $request->id_menu,
            'buah_porsi_a' => $buah_porsi_a,
            'buah_porsi_b' => $buah_porsi_b,
            'kebutuhan_total_matang' => $kebutuhan_total_matang,
            'kebutuhan_buah_a'  => $kebutuhan_total_matang,
            'kebutuhan_buah_b'      => 0,
            'penyusutan_buah_a'     => 0,
            'penyusutan_buah_b'     => 0,
            'kebutuhan_matang_a'    => $kebutuhan_total_matang,
            'kebutuhan_matang_b'    => $kebutuhan_total_matang,
            'kebutuhan_matang_realisasi'    => $kebutuhan_total_matang,
            'kebutuhan_total_mentah'    => $kebutuhan_total_matang,
            'kapasitas_tilting'    => 0,
            'jumlah_masak'    => 1,

            'status' => 1,

        ]);
        $data_rumus_buah = DB::table('tb_rumus_perhitungan_buah')->where('id_menu', $request->id_menu)->first();
        $data_menu = Menu::where('id', $request->id_menu)->first();
        $data_buah = MenuBahan::where('menu_id', $data_menu->buah)->get();
        
        foreach ($data_buah as $row) {
            $data_box = DB::table('tb_box_bahan_baku')->where('id_bahan', $row->bahan_id)->select('isi_per_box')->first();
            if ($data_box) {
                $jumlah_box = ceil($kebutuhan_total_matang / $data_box->isi_per_box);
            } else {
                $jumlah_box = $kebutuhan_total_matang / 250;
            }
            $harga = DB::table('tb_rincian_kontrak')->where('id_bahan', $row->bahan_id)
                ->where('status',  1)->orderByDesc('id') // ambil yang terbaru berdasarkan id
                ->first() ?? 0;
            $keterangan = DB::table('tb_spesifikasi_bahan')->where('id_bahan', $row->bahan_id)
                ->first();
            $keterangan = $keterangan->spesifikasi ?? '-';

            if($row->status_bahan_baku != 3 && $row->id_satuan == 1) 
                {
                    $kebutuhan_total_matang = $jumlah_siswa_b * $buah_porsi_b + $jumlah_siswa_a * $buah_porsi_a;
                    $kebutuhan_total_matang = $kebutuhan_total_matang / 1000 ;
                    $satuan = 25;
                }else{
                    $kebutuhan_total_matang = $kebutuhan_total_matang;
                    $satuan = 22;
                }

            DB::table('tb_rincian_menu_temp')->insert([
                'id_menu' => $request->id_menu,
                'id_resep'      => $data_menu->buah,
                'id_bahan'      => $row->bahan_id,
                'jumlah'        => $kebutuhan_total_matang ,
                'bumbu'         => 0,
                'harga'         => ($harga->harga_bahan ?? 0),
                'jumlah_box'    => $jumlah_box,
                'total_harga'   => ($harga->harga_bahan ?? 0) * $kebutuhan_total_matang,
                'id_satuan'     => $satuan,
                'id_kontrak'     => $this->resolveRincianKontrakId($row->bahan_id, $harga),
                'keterangan'     => $keterangan
            ]);
        }
        $menu = Menu::find($request->id_menu);
            $realisasiAkg = DB::table('tb_resep_realisasi_akg')
                    ->where('id_resep',  $data_menu->buah)
                    ->get();

            $energi = 0;
            $protein = 0;
            $lemak = 0;
            $karbohidrat = 0;
            $serat = 0;
            $natrium = 0;
            foreach ($realisasiAkg as $row) {
                DB::table('tb_menu_akg')
                    ->insert([
                        'id_menu' => $request->id_menu,
                        'komponen_sehat' => 'buah',
                        'id_nutrisi' => $data_menu->buah,
                        'energi' => $row->energi_kcal,
                        'protein' => $row->protein_g,
                        'lemak' => $row->lemak_g,
                        'karbo' => $row->karbohidrat_g,
                        'serat' => $row->serat_g,
                        'natrium' => $row->natrium_mg,
                    ]);
            
            }
        // 11?? Redirect kembali dengan pesan sukses
        return redirect()->route('rincian_bahan', ['idmenu' => $request->id_menu])
            ->with('success', 'Buah berhasil disimpan.');
    }

    public function dt_buah_temp($id_menu)
    {
        // Ambil data Menu berdasarkan id_menu
        $data = Menu::where('id', $id_menu)->first();

        // Query rincian bahan resep karbohidrat
        /*if ($data->status_pengajuan == 'pending') {
            $table = DB::table('tb_rincian_menu_temp')
                ->join('tb_master_bahan', 'tb_rincian_menu_temp.id_bahan', '=', 'tb_master_bahan.id')
                ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
                ->select(
                    'tb_master_bahan.bahan',

                    'tb_rincian_menu_temp.id',
                    'tb_rincian_menu_temp.jumlah',
                    'tb_rincian_menu_temp.harga',
                    'tb_rincian_menu_temp.total_harga',
                    'tb_satuan.satuan'
                )
                ->where('tb_rincian_menu_temp.id_resep', $data->buah)
                ->where('tb_rincian_menu_temp.id_menu', $id_menu)
                ->get();
        } else {
            $table = DB::table('rincian_menu_harian')
                ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
                ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
                ->select(
                    'tb_master_bahan.bahan',

                    'rincian_menu_harian.id',
                    'rincian_menu_harian.jumlah',
                    'rincian_menu_harian.harga',
                    'rincian_menu_harian.total_harga',
                    'tb_satuan.satuan'
                )
                ->where('rincian_menu_harian.id_resep', $data->buah)
                ->where('rincian_menu_harian.id_menu_harian', $id_menu)
                ->get();
        }*/
        $table = DB::table('tb_rincian_menu_temp')
            ->join('tb_master_bahan', 'tb_rincian_menu_temp.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'tb_rincian_menu_temp.id_satuan', '=', 'tb_satuan.id')
            ->select(
                'tb_master_bahan.bahan',

                'tb_rincian_menu_temp.id',
                'tb_rincian_menu_temp.jumlah',
                'tb_rincian_menu_temp.harga',
                'tb_rincian_menu_temp.total_harga',
                'tb_rincian_menu_temp.keterangan',
                'tb_rincian_menu_temp.jumlah_box',
                'tb_satuan.satuan',
                'tb_rincian_menu_temp.id_satuan'
            )
            ->where('tb_rincian_menu_temp.id_resep', $data->buah)
            ->where('tb_rincian_menu_temp.id_menu', $id_menu)
            ->get();

        // Return data untuk DataTables
        return DataTables::of($table)
            ->addIndexColumn()

            // Kolom jumlah bahan dengan format satuan
            ->editColumn('jumlah_bahan', function ($row) {
                $data_satuan = TbSatuan::find($row->id_satuan);
                $satuan = $data_satuan->satuan;
                return '<input type="number" class="form-control jumlah" data-id="' . $row->id . '" value="' . $row->jumlah . '"> <button class="btn btn-success btn-sm update-jumlah" data-id="' . $row->id . '">Update</button>';
                if ($row->bahan == 'beras') {
                    return number_format($row->jumlah, 0, '', '.') . ' kg';
                } else {
                    return number_format($row->jumlah, 0, '', '.') . ' ' .  $satuan;
                }
            })

            // Kolom harga satuan
            ->addColumn('harga_satuan', function ($row) {
                return 'Rp. ' . number_format($row->harga, 0, '', '.');
            })

            // Kolom total harga dibayar
            ->addColumn('total_dibayar', function ($row) {
                return 'Rp. ' . number_format($row->total_harga, 0, '', '.');
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('rincian_menu_temp.delete', $row->id) . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Yakin hapus bahan ini?\')">Delete</a>';
            })
            ->addColumn('input_harga', function ($row) {
                return '<input type="number" class="form-control jumlah_harga" data-id="' . $row->id . '" value="' . $row->harga . '"> <button class="btn btn-success btn-sm update-jumlah-harga" data-id="' . $row->id . '">Update</button>';

                //return number_format($row->jumlah, 0, ',', '.'); // Format: 1.234,56
            })
            ->addColumn('input_jumlah_box', function ($row) {
                return '<input type="number" class="form-control jumlah_box" data-id="' . $row->id . '" value="' . $row->jumlah_box . '"> <button class="btn btn-success btn-sm update-jumlah-box" data-id="' . $row->id . '">Update</button>';

                //return number_format($row->jumlah, 0, ',', '.'); // Format: 1.234,56
            })
            ->addColumn('input_keterangan', function ($row) {
                return '<input type="text" class="form-control jumlah_keterangan" data-id="' . $row->id . '" value="' . $row->keterangan . '"> <button class="btn btn-success btn-sm update-jumlah-keterangan" data-id="' . $row->id . '">Update</button>';

                //return number_format($row->jumlah, 0, ',', '.'); // Format: 1.234,56
            })
            ->rawColumns(['jumlah_bahan', 'action', 'harga_satuan', 'total_dibayar', 'input_keterangan', 'input_jumlah_box', 'input_harga'])
            ->make(true);
    }

    public function delete_buah_temp($id)
    {
        // 1?? Ambil data menu berdasarkan ID
        $data = Menu::find($id);


        // 3?? Hapus data dari tabel tb_rumus_perhitungan_karbo
        // yang id_menu-nya = $id
        DB::table('tb_rumus_perhitungan_buah')
            ->where('id_menu', $id)
            ->delete();

        DB::table('tb_rincian_menu_temp')->where('id_menu', $id)->where('id_resep', $data->buah)
            ->delete();

        // 4?? Update kolom karbohidrat di tb_menu menjadi NULL
        DB::table('tb_menu')
            ->where('id', $id)
            ->update(['buah' => null]);

        // 5?? Redirect kembali ke halaman rincian bahan dengan pesan sukses
        return redirect()
            ->route('rincian_bahan', ['idmenu' => $id])
            ->with('success', 'Buah berhasil dihapus.');
    }



    public function storeSuplemen(Request $request)
    {
        // 1?? Validasi input request (metode validateRequest adalah custom)
        $request->validate([
            'id_menu' => 'required',
            'id_suplemen' => 'required|integer',

        ]);

        // 2?? Update kolom 'karbohidrat' pada tabel tb_menu sesuai ID menu
        DB::table('tb_menu')
            ->where('id', $request->id_menu)
            ->update([
                'susu' => $request->id_suplemen,
            ]);

        // 3?? Hitung jumlah penerima kategori A dan B dari histori menu
        $jumlah_siswa_a = DB::table('rincian_sekolah')
            ->where('id_menu_harian', $request->id_menu)
            ->sum('jumlah_penerima_a');

        $jumlah_siswa_b = DB::table('rincian_sekolah')
            ->where('id_menu_harian', $request->id_menu)
            ->sum('jumlah_penerima_b');

        // 4?? Ambil input porsi A dan B
        $buffer = Buffer::first();
        $buffer = $buffer->buffer_menu;

        $suplemen_porsi_a = $request->porsi_a;
        $suplemen_porsi_b = $request->porsi_b;
        $kebutuhan_total_matang = $jumlah_siswa_b * $suplemen_porsi_b + $jumlah_siswa_a * $suplemen_porsi_a;
        $kebutuhan_total_matang = $kebutuhan_total_matang + $kebutuhan_total_matang * $buffer / 100;

        DB::table('tb_rumus_perhitungan_suplemen')->insert([
            'id_menu' => $request->id_menu,
            'suplemen_porsi_a' => $suplemen_porsi_a,
            'suplemen_porsi_b' => $suplemen_porsi_b,
            'kebutuhan_total_matang' => $kebutuhan_total_matang,
            'kebutuhan_suplemen_a'  => $kebutuhan_total_matang,
            'kebutuhan_suplemen_b'      => 0,
            'penyusutan_suplemen_a'     => 0,
            'penyusutan_suplemen_b'     => 0,
            'kebutuhan_matang_a'    => $kebutuhan_total_matang,
            'kebutuhan_matang_b'    => $kebutuhan_total_matang,
            'kebutuhan_matang_realisasi'    => $kebutuhan_total_matang,
            'kebutuhan_total_mentah'    => $kebutuhan_total_matang,
            'kapasitas_tilting'    => 0,
            'jumlah_masak'    => 1,

            'status' => 1,

        ]);
        $data_rumus_suplemen = DB::table('tb_rumus_perhitungan_suplemen')->where('id_menu', $request->id_menu)->first();
        $data_menu = Menu::where('id', $request->id_menu)->first();
        $data_suplemen = MenuBahan::where('menu_id', $data_menu->susu)->orderByRaw("FIELD(status_bahan_baku, 1, 2, 4, 5, 3)")
            ->get();
        
        foreach ($data_suplemen as $row) {
            $bahan = TbMasterBahan::find($row->bahan_id);
            $harga = DB::table('tb_rincian_kontrak')->where('id_bahan', $row->bahan_id)
                ->where('status',  1)->orderByDesc('id') // ambil yang terbaru berdasarkan id
                ->first() ?? 0;
            $keterangan = DB::table('tb_spesifikasi_bahan')->where('id_bahan', $row->bahan_id)
                ->first();

            $keterangan = $keterangan->spesifikasi ?? '-';
            if ($row->status_bahan_baku == 1) 
                {
                $data_box = DB::table('tb_box_bahan_baku')->where('id_bahan', $row->bahan_id)->select('isi_per_box')->first();
                if ($data_box) {
                    $jumlah_box = ceil($kebutuhan_total_matang / $data_box->isi_per_box);
                } else {
                    $jumlah_box = $kebutuhan_total_matang / 250;
                }
                if($bahan->satuan_bahan == 1 )   
                {
                    DB::table('tb_rincian_menu_temp')->insert([
                        'id_menu' => $request->id_menu,
                        'id_resep'      => $data_menu->susu,
                        'id_bahan'      => $row->bahan_id,
                        'jumlah'        => $kebutuhan_total_matang / 1000,
                        'bumbu'         => 0,
                        'harga'         => ($harga->harga_bahan ?? 0),
                        'total_harga'   => ($harga->harga_bahan ?? 0) * $kebutuhan_total_matang  / 1000,
                        'jumlah_box'    => $jumlah_box,
                        'id_satuan'     => 25,
                        'id_kontrak'     => $this->resolveRincianKontrakId($row->bahan_id, $harga),
                        'keterangan'     => $keterangan

                    ]);
                }else{
                    $satuan_nama = TbSatuan::find($bahan->satuan_bahan)->satuan ?? '';
                    if($satuan_nama == 'papan' )
                        {
                            $kebutuhan_total_matang = $jumlah_siswa_b * $suplemen_porsi_b + $jumlah_siswa_a * $suplemen_porsi_a;
                            $kebutuhan_total_matang = $kebutuhan_total_matang / 500;
                            $jumlah_box = 1 ;

                        }

                    DB::table('tb_rincian_menu_temp')->insert([
                        'id_menu' => $request->id_menu,
                        'id_resep'      => $data_menu->susu,
                        'id_bahan'      => $row->bahan_id,
                        'jumlah'        => $kebutuhan_total_matang,
                        'bumbu'         => 0,
                        'harga'         => ($harga->harga_bahan ?? 0),
                        'total_harga'   => ($harga->harga_bahan ?? 0) * $kebutuhan_total_matang,
                        'jumlah_box'    => $jumlah_box,
                        'id_satuan'     => $bahan->satuan_bahan,
                        'id_kontrak'     => $this->resolveRincianKontrakId($row->bahan_id, $harga),
                        'keterangan'     => $keterangan

                    ]);
                }
                
                }else{
                $data_perhitungan_bumbu = PerhitunganBumbu::where('id_menu_bahan', $row->id)->first();
                $pengali = $data_perhitungan_bumbu->pengali ?? 10;
                $pembagi = $data_perhitungan_bumbu->pembagi ?? 115;
                $jumlah_kebutuhan = $kebutuhan_total_matang;
                $jumlah_kebutuhan = $jumlah_kebutuhan / $pembagi;
                $jumlah_kebutuhan = $jumlah_kebutuhan * $pengali;
                if (in_array($bahan->satuan_bahan , [1, 27])) {

                        $batas = [
                            25, 50, 100, 200, 300, 400, 500, 600, 700, 800, 900,
                            1000, 1200, 1400, 1500, 1800, 2000, 2250, 2500, 2750,
                            3000, 3250, 3500, 3750, 4000, 4250, 4500, 4750, 5000,
                            5500, 6000, 6500, 7000, 7500, 8000, 8500, 9000, 9500,
                            10000, 11000, 12000
                        ];

                        foreach ($batas as $nilai) {
                            if ($jumlah_kebutuhan < $nilai) {
                                $jumlah_kebutuhan = $nilai;
                                break;
                            }
                        }

                        // Jika lebih dari 12000
                        if ($jumlah_kebutuhan > 12000) {
                            $jumlah_kebutuhan = ceil($jumlah_kebutuhan / 1000) * 1000;
                        }

                    } else {
                        // Selain satuan 1 / 27 → bulatkan ke atas tanpa koma
                        $jumlah_kebutuhan = ceil($jumlah_kebutuhan);
                    }
                DB::table('tb_rincian_menu_temp')->insert([
                    'id_menu' => $request->id_menu,
                    'id_resep'      => $data_menu->susu,
                    'id_bahan'      => $row->bahan_id,
                    'jumlah'        => $jumlah_kebutuhan,
                    'bumbu'         => 0,
                    'harga'         => ($harga->harga_bahan ?? 0),
                    'total_harga'   => ($harga->harga_bahan ?? 0) * $jumlah_kebutuhan / 1000,
                    'jumlah_box'    => 1,
                    'id_satuan'     => $bahan->satuan_bahan,
                    'id_kontrak'     => $this->resolveRincianKontrakId($row->bahan_id, $harga),
                    'keterangan'     => $keterangan

                ]);
                }
            
        }

        $menu = Menu::find($request->id_menu);
            $realisasiAkg = DB::table('tb_resep_realisasi_akg')
                    ->where('id_resep',  $data_menu->susu)
                    ->get();

            $energi = 0;
            $protein = 0;
            $lemak = 0;
            $karbohidrat = 0;
            $serat = 0;
            $natrium = 0;
            foreach ($realisasiAkg as $row) {
                DB::table('tb_menu_akg')
                    ->insert([
                        'id_menu' => $request->id_menu,
                        'komponen_sehat' => 'pendamping',
                        'id_nutrisi' => $data_menu->susu,
                        'energi' => $row->energi_kcal,
                        'protein' => $row->protein_g,
                        'lemak' => $row->lemak_g,
                        'karbo' => $row->karbohidrat_g,
                        'serat' => $row->serat_g,
                        'natrium' => $row->natrium_mg,
                    ]);
            
            }

        // 11?? Redirect kembali dengan pesan sukses
        return redirect()->route('rincian_bahan', ['idmenu' => $request->id_menu])
            ->with('success', 'Suplemen berhasil disimpan.');
    }

    public function dt_suplemen_temp($id_menu)
    {
        // Ambil data Menu berdasarkan id_menu
        $data = Menu::where('id', $id_menu)->first();

        // Query rincian bahan resep karbohidrat
        /*if ($data->status_pengajuan == 'pending') {
            $table = DB::table('tb_rincian_menu_temp')
                ->join('tb_master_bahan', 'tb_rincian_menu_temp.id_bahan', '=', 'tb_master_bahan.id')
                ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
                ->select(
                    'tb_master_bahan.bahan',

                    'tb_rincian_menu_temp.id',
                    'tb_rincian_menu_temp.jumlah',
                    'tb_rincian_menu_temp.harga',
                    'tb_rincian_menu_temp.total_harga',
                    'tb_satuan.satuan'
                )
                ->where('tb_rincian_menu_temp.id_resep', $data->susu)
                ->where('tb_rincian_menu_temp.id_menu', $id_menu)
                ->get();
        } else {
            $table = DB::table('rincian_menu_harian')
                ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
                ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
                ->select(
                    'tb_master_bahan.bahan',

                    'rincian_menu_harian.id',
                    'rincian_menu_harian.jumlah',
                    'rincian_menu_harian.harga',
                    'rincian_menu_harian.total_harga',
                    'tb_satuan.satuan'
                )
                ->where('rincian_menu_harian.id_resep', $data->susu)
                ->where('rincian_menu_harian.id_menu_harian', $id_menu)
                ->get();
        }*/
        $table = DB::table('tb_rincian_menu_temp')
            ->join('tb_master_bahan', 'tb_rincian_menu_temp.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'tb_rincian_menu_temp.id_satuan', '=', 'tb_satuan.id')
            ->select(
                'tb_master_bahan.bahan',

                'tb_rincian_menu_temp.id',
                'tb_rincian_menu_temp.jumlah',
                'tb_rincian_menu_temp.harga',
                'tb_rincian_menu_temp.total_harga',
                'tb_rincian_menu_temp.keterangan',
                'tb_rincian_menu_temp.jumlah_box',
                'tb_satuan.satuan',
                'tb_rincian_menu_temp.id_satuan'
            )
            ->where('tb_rincian_menu_temp.id_resep', $data->susu)
            ->where('tb_rincian_menu_temp.id_menu', $id_menu)
            ->get();

        // Return data untuk DataTables
        return DataTables::of($table)
            ->addIndexColumn()

            // Kolom jumlah bahan dengan format satuan
            ->editColumn('jumlah_bahan', function ($row) {
                $data_satuan = TbSatuan::find($row->id_satuan);
                $satuan = $data_satuan->satuan;
                return '<input type="number" class="form-control jumlah" data-id="' . $row->id . '" value="' . $row->jumlah . '"> <button class="btn btn-success btn-sm update-jumlah" data-id="' . $row->id . '">Update</button>';
                if ($row->bahan == 'beras') {
                    return number_format($row->jumlah, 0, '', '.') . ' kg';
                } else {
                    return number_format($row->jumlah, 0, '', '.') . ' ' .  $satuan;
                }
            })

            // Kolom harga satuan
            ->addColumn('harga_satuan', function ($row) {
                return 'Rp. ' . number_format($row->harga, 0, '', '.');
            })

            // Kolom total harga dibayar
            ->addColumn('total_dibayar', function ($row) {
                return 'Rp. ' . number_format($row->total_harga, 0, '', '.');
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('rincian_menu_temp.delete', $row->id) . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Yakin hapus bahan ini?\')">Delete</a>';
            })
            ->addColumn('input_harga', function ($row) {
                return '<input type="number" class="form-control jumlah_harga" data-id="' . $row->id . '" value="' . $row->harga . '"> <button class="btn btn-success btn-sm update-jumlah-harga" data-id="' . $row->id . '">Update</button>';

                //return number_format($row->jumlah, 0, ',', '.'); // Format: 1.234,56
            })
            ->addColumn('input_jumlah_box', function ($row) {
                return '<input type="number" class="form-control jumlah_box" data-id="' . $row->id . '" value="' . $row->jumlah_box . '"> <button class="btn btn-success btn-sm update-jumlah-box" data-id="' . $row->id . '">Update</button>';

                //return number_format($row->jumlah, 0, ',', '.'); // Format: 1.234,56
            })
            ->addColumn('input_keterangan', function ($row) {
                return '<input type="text" class="form-control jumlah_keterangan" data-id="' . $row->id . '" value="' . $row->keterangan . '"> <button class="btn btn-success btn-sm update-jumlah-keterangan" data-id="' . $row->id . '">Update</button>';

                //return number_format($row->jumlah, 0, ',', '.'); // Format: 1.234,56
            })
            ->rawColumns(['jumlah_bahan', 'action', 'harga_satuan', 'total_dibayar', 'input_keterangan', 'input_jumlah_box', 'input_harga'])
            ->make(true);
    }

    public function delete_suplemen_temp($id)
    {
        // 1?? Ambil data menu berdasarkan ID
        $data = Menu::find($id);


        // 3?? Hapus data dari tabel tb_rumus_perhitungan_karbo
        // yang id_menu-nya = $id
        DB::table('tb_rumus_perhitungan_suplemen')
            ->where('id_menu', $id)
            ->delete();

        DB::table('tb_rincian_menu_temp')->where('id_menu', $id)->where('id_resep', $data->susu)
            ->delete();

        // 4?? Update kolom karbohidrat di tb_menu menjadi NULL
        DB::table('tb_menu')
            ->where('id', $id)
            ->update(['susu' => null]);

        // 5?? Redirect kembali ke halaman rincian bahan dengan pesan sukses
        return redirect()
            ->route('rincian_bahan', ['idmenu' => $id])
            ->with('success', 'Suplemen berhasil dihapus.');
    }

    public function publish_rincian_menu($id)
    {
        $id_menu_harian = $id;

        DB::transaction(function () use ($id, $id_menu_harian) {
            // Ambil semua data dari temp lalu sinkronkan ke tabel final tanpa mengganti ID yang masih dipakai.
            $dataTemp = DB::table('tb_rincian_menu_temp')
                ->where('id_menu', $id)
                ->orderBy('id')
                ->get();

            $existingRows = rincian_menu_harian::where('id_menu_harian', $id_menu_harian)
                ->orderBy('id')
                ->get()
                ->groupBy(function ($row) {
                    return implode('|', [
                        (string) $row->id_resep,
                        (string) $row->id_bahan,
                        (string) ($row->bumbu ?? ''),
                    ]);
                })
                ->map(function ($rows) {
                    return $rows->values();
                });

            $matchedIds = [];

            foreach ($dataTemp as $item) {
                $rowKey = implode('|', [
                    (string) $item->id_resep,
                    (string) $item->id_bahan,
                    (string) ($item->bumbu ?? ''),
                ]);

                $attributes = [
                    'id_menu_harian' => $id_menu_harian,
                    'id_resep' => $item->id_resep,
                    'id_bahan' => $item->id_bahan,
                    'jumlah' => $item->jumlah,
                    'bumbu' => $item->bumbu,
                    'harga' => $item->harga,
                    'total_harga' => $item->total_harga,
                    'id_kontrak' => $item->id_kontrak,
                    'id_satuan' => $item->id_satuan,
                    'jumlah_box' => $item->jumlah_box,
                    'keterangan' => $item->keterangan,
                ];

                $matchedRow = null;
                if ($existingRows->has($rowKey) && $existingRows[$rowKey]->isNotEmpty()) {
                    $matchedRow = $existingRows[$rowKey]->shift();
                }

                if ($matchedRow) {
                    $matchedRow->update($attributes);
                    $matchedIds[] = $matchedRow->id;
                } else {
                    $newRow = rincian_menu_harian::create($attributes);
                    $matchedIds[] = $newRow->id;
                }

                /*
                if($nama_bahan->bahan == 'Beras Medium' || $nama_bahan->bahan == 'Beras Premium')
                {
                    $jumlah_total = $item->jumlah;
                    $paket36kg = floor($jumlah_total / 36);
                    $paket25kg = 0;
                    $paket5kg = 0;
                    $paket1kg = 0;
                    if($paket36kg>0)
                    {
                        $paket25kg = $paket36kg * 1;
                        $paket5kg = $paket36kg * 2;
                        $paket1kg = $paket36kg *1 ;
                        $jumlah_total = $jumlah_total - $paket36kg * 36;

                        $paket25kg_sisa = floor($jumlah_total / 25);
                        $jumlah_total = $jumlah_total - $paket25kg_sisa * 25;
                        $paket5kg_sisa =  floor($jumlah_total / 5);
                        $paket1kg_sisa = $jumlah_total - $paket5kg_sisa * 5;
                        
                        $paket25kg = $paket36kg * 1 + $paket25kg_sisa;
                        $paket5kg = $paket36kg * 2 + $paket5kg_sisa;
                        $paket1kg = $paket36kg * 1 + $paket1kg_sisa;
                    }else{
                        $paket25kg = floor($jumlah_total / 25);
                        $jumlah_total = $jumlah_total - $paket25kg * 25;
                        $paket5kg =  floor($jumlah_total / 5);
                        $paket1kg = $jumlah_total - $paket5kg*5;

                    }

                    // jumlah yang 25 kg
                    for ($i = 0; $i < $paket25kg; $i++) {
                        rincian_menu_harian::create([
                            'id_menu_harian' => $id_menu_harian,
                            'id_resep'       => $item->id_resep,
                            'id_bahan'       => $item->id_bahan,
                            'jumlah'         => 25,
                            'bumbu'          => $item->bumbu,
                            'harga'          => $item->harga,
                            'total_harga'    => $item->total_harga / 25,
                            'id_kontrak'     => $item->id_kontrak,
                            'id_satuan'      => $item->id_satuan,
                            'jumlah_box'     => 1, // per loop jadi 1 box
                            'keterangan'     => $item->keterangan,
                        ]);
                    }
                    // jumlah yang 5 kg
                    for ($i = 0; $i < $paket5kg; $i++) {
                        rincian_menu_harian::create([
                            'id_menu_harian' => $id_menu_harian,
                            'id_resep'       => $item->id_resep,
                            'id_bahan'       => $item->id_bahan,
                            'jumlah'         => 5,
                            'bumbu'          => $item->bumbu,
                            'harga'          => $item->harga,
                            'total_harga'    => $item->total_harga / 5,
                            'id_kontrak'     => $item->id_kontrak,
                            'id_satuan'      => $item->id_satuan,
                            'jumlah_box'     => 1, // per loop jadi 1 box
                            'keterangan'     => $item->keterangan,
                        ]);
                    }
                    // jumlah yang 1 kg
                    for ($i = 0; $i < $paket1kg; $i++) {
                        rincian_menu_harian::create([
                            'id_menu_harian' => $id_menu_harian,
                            'id_resep'       => $item->id_resep,
                            'id_bahan'       => $item->id_bahan,
                            'jumlah'         => 1,
                            'bumbu'          => $item->bumbu,
                            'harga'          => $item->harga,
                            'total_harga'    => $item->total_harga / 1,
                            'id_kontrak'     => $item->id_kontrak,
                            'id_satuan'      => $item->id_satuan,
                            'jumlah_box'     => 1, // per loop jadi 1 box
                            'keterangan'     => $item->keterangan,
                        ]);
                    }

                    
                }else{
                    
                    for ($i = 0; $i < $item->jumlah_box; $i++) {
                        rincian_menu_harian::create([
                            'id_menu_harian' => $id_menu_harian,
                            'id_resep'       => $item->id_resep,
                            'id_bahan'       => $item->id_bahan,
                            'jumlah'         => $item->jumlah / $item->jumlah_box,
                            'bumbu'          => $item->bumbu,
                            'harga'          => $item->harga,
                            'total_harga'    => $item->total_harga / $item->jumlah_box,
                            'id_kontrak'     => $item->id_kontrak,
                            'id_satuan'      => $item->id_satuan,
                            'jumlah_box'     => 1, // per loop jadi 1 box
                            'keterangan'     => $item->keterangan,
                        ]);
                    }
                }*/
            }

            $deleteQuery = rincian_menu_harian::where('id_menu_harian', $id_menu_harian);
            if (!empty($matchedIds)) {
                $deleteQuery->whereNotIn('id', $matchedIds);
            }
            $deleteQuery->delete();

            Menu::where('id', $id_menu_harian)->update([
                'status_pengajuan' => 'pending'
            ]);
        });

        $menu = Menu::find($id_menu_harian);
        if ($menu) {
            $rumusKarbo = DB::table('tb_rumus_perhitungan_karbo')
                ->where('id_menu', $id_menu_harian)
                ->orderByDesc('id')
                ->first();

            $rumusProtein = DB::table('tb_rumus_perhitungan_protein')
                ->where('id_menu', $id_menu_harian)
                ->orderByDesc('id')
                ->first();

            $rumusSayur = DB::table('tb_rumus_perhitungan_sayur')
                ->where('id_menu', $id_menu_harian)
                ->orderByDesc('id')
                ->first();

            $rumusBuah = DB::table('tb_rumus_perhitungan_buah')
                ->where('id_menu', $id_menu_harian)
                ->orderByDesc('id')
                ->first();

            $rumusSuplemen = DB::table('tb_rumus_perhitungan_suplemen')
                ->where('id_menu', $id_menu_harian)
                ->orderByDesc('id')
                ->first();

            $jumlahPenerimaA = DB::table('rincian_sekolah')
                ->where('id_menu_harian', $id_menu_harian)
                ->sum('jumlah_penerima_a');

            $paketNama = ((int) $jumlahPenerimaA === 0) ? 'B' : 'A';

            $mainSayurCount = 0;
            if (!empty($menu->sayur)) {
                $mainSayurCount = DB::table('tb_menu_bahan')
                    ->where('menu_id', $menu->sayur)
                    ->whereIn('status_bahan_baku', [1, 2, 4, 5])
                    ->count();
            }

            $slotSayur = max(1, min(4, (int) $mainSayurCount));
            $sayurDistribusiA = [0, 0, 0, 0];
            $sayurDistribusiB = [0, 0, 0, 0];

            if ($rumusSayur) {
                $totalSayurA = (int) ($rumusSayur->sayur_porsi_a ?? 0);
                $totalSayurB = (int) ($rumusSayur->sayur_porsi_b ?? 0);

                $baseA = intdiv($totalSayurA, $slotSayur);
                $sisaA = $totalSayurA % $slotSayur;
                for ($i = 0; $i < $slotSayur; $i++) {
                    $sayurDistribusiA[$i] = $baseA + ($i < $sisaA ? 1 : 0);
                }

                $baseB = intdiv($totalSayurB, $slotSayur);
                $sisaB = $totalSayurB % $slotSayur;
                for ($i = 0; $i < $slotSayur; $i++) {
                    $sayurDistribusiB[$i] = $baseB + ($i < $sisaB ? 1 : 0);
                }
            }

            $paketPayload = [
                'paket' => $paketNama,
                'resep_karbo' => $menu->karbohidrat,
                'resep_protein' => $menu->protein,
                'resep_sayur' => $menu->sayur,
                'resep_buah' => $menu->buah,
                'resep_suplemen' => $menu->susu,

                'berat_mentah_karbo_a' => $rumusKarbo ? $rumusKarbo->karbo_porsi_a : null,
                'berat_mentah_karbo_b' => $rumusKarbo ? $rumusKarbo->karbo_porsi_b : null,

                'berat_mentah_protein_a' => $rumusProtein ? $rumusProtein->protein_porsi_a : null,
                'berat_mentah_protein_b' => $rumusProtein ? $rumusProtein->protein_porsi_b : null,

                'berat_mentah_sayur1_a' => $sayurDistribusiA[0],
                'berat_mentah_sayur2_a' => $sayurDistribusiA[1],
                'berat_mentah_sayur3_a' => $sayurDistribusiA[2],
                'berat_mentah_sayur4_a' => $sayurDistribusiA[3],
                'berat_mentah_sayur1_b' => $sayurDistribusiB[0],
                'berat_mentah_sayur2_b' => $sayurDistribusiB[1],
                'berat_mentah_sayur3_b' => $sayurDistribusiB[2],
                'berat_mentah_sayur4_b' => $sayurDistribusiB[3],

                'berat_mentah_buah_a' => $rumusBuah ? $rumusBuah->buah_porsi_a : null,
                'berat_mentah_buah_b' => $rumusBuah ? $rumusBuah->buah_porsi_b : null,

                'berat_mentah_suplemen_a' => $rumusSuplemen ? $rumusSuplemen->suplemen_porsi_a : null,
                'berat_mentah_suplemen_b' => $rumusSuplemen ? $rumusSuplemen->suplemen_porsi_b : null,
            ];

            $paket = PaketMenu::query()
                ->where('paket', $paketNama)
                ->where('resep_karbo', $menu->karbohidrat)
                ->where('resep_protein', $menu->protein)
                ->where('resep_sayur', $menu->sayur)
                ->where('resep_buah', $menu->buah)
                ->where('resep_suplemen', $menu->susu)
                ->first();

            if ($paket) {
                $paket->update($paketPayload);
            } else {
                $paket = PaketMenu::create($paketPayload);
            }

            $giziHarian = DB::table('tb_menu_gizi_harian')
                ->where('id_menu', $id_menu_harian)
                ->orderByDesc('id')
                ->first();

            DB::table('tb_gizi_menu')->updateOrInsert(
                ['id_paket' => $paket->id],
                [
                    'energi' => $giziHarian ? $giziHarian->energi : 0,
                    'protein' => $giziHarian ? $giziHarian->protein : 0,
                    'lemak' => $giziHarian ? $giziHarian->lemak : 0,
                    'karbohidrat' => $giziHarian ? $giziHarian->karbohidrat : 0,
                    'serat' => $giziHarian ? $giziHarian->serat : 0,
                    'natrium' => $giziHarian ? $giziHarian->natrium : 0,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        return redirect()->route('rincian_bahan', ['idmenu' => $id])->with('success', 'Data berhasil disalin ke menu harian.');
    }

    public function dt_bumbu_temp($id_menu)
    {
        // Ambil data Menu berdasarkan id_menu
        $data = Menu::where('id', $id_menu)->first();

        // Query rincian bahan resep karbohidrat
        if ($data->status_pengajuan == 'pending') {
            $table = DB::table('rincian_menu_harian')
                ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
                ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
                ->select(
                    'tb_master_bahan.bahan',

                    'rincian_menu_harian.id',
                    'rincian_menu_harian.jumlah',
                    'rincian_menu_harian.harga',
                    'rincian_menu_harian.bumbu',
                    'rincian_menu_harian.total_harga',
                    'tb_satuan.satuan'
                )
                //->where('rincian_menu_harian.id_resep', $data->susu)
                ->where('rincian_menu_harian.bumbu', 1)
                ->where('rincian_menu_harian.id_menu_harian', $id_menu)
                ->get();
        } else {
            $table = DB::table('rincian_menu_harian')
                ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
                ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
                ->select(
                    'tb_master_bahan.bahan',

                    'rincian_menu_harian.id',
                    'rincian_menu_harian.jumlah',
                    'rincian_menu_harian.harga',
                    'rincian_menu_harian.bumbu',
                    'rincian_menu_harian.total_harga',
                    'tb_satuan.satuan'
                )
                //->where('rincian_menu_harian.id_resep', $data->susu)
                ->where('rincian_menu_harian.bumbu', 1)
                ->where('rincian_menu_harian.id_menu_harian', $id_menu)
                ->get();
        }


        // Return data untuk DataTables
        return DataTables::of($table)
            ->addIndexColumn()

            // Kolom jumlah bahan dengan format satuan
            ->editColumn('jumlah_bahan', function ($row) {
                return number_format($row->jumlah, 0, '', '.') . ' ' . $row->satuan;
            })

            // Kolom harga satuan
            ->addColumn('harga_satuan', function ($row) {
                return 'Rp. ' . number_format($row->harga, 0, '', '.');
            })

            // Kolom total harga dibayar
            ->addColumn('total_dibayar', function ($row) {
                return 'Rp. ' . number_format($row->total_harga, 0, '', '.');
            })
            ->addColumn('action', function ($row) {
                return '<input type="number" class="form-control jumlah" data-id="' . $row->id . '" value="' . $row->jumlah . '"> <button class="btn btn-success btn-sm update-jumlah" data-id="' . $row->id . '">Update</button>';

                //return number_format($row->jumlah, 0, ',', '.'); // Format: 1.234,56
            })

            ->rawColumns(['jumlah_bahan', 'action', 'harga_satuan', 'total_dibayar'])
            ->make(true);
    }
}
