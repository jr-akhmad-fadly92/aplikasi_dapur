<?php

namespace App\Http\Controllers;

use App\Models\Buffer;
use App\Models\MenuBahan;
use Illuminate\Http\Request;
use App\Models\PaketMenu;
use App\Models\PerhitunganBumbu;
use App\Models\Resep;
use App\Models\TbMasterBahan;
use App\Models\TbSatuan;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;


class PaketMenuController extends Controller
{
    private function bulatkanKebutuhanNonUtama($jumlahKebutuhan, $satuanBahan)
    {
        $jumlahKebutuhan = (float) $jumlahKebutuhan;
        $satuanBahan = (int) $satuanBahan;

        if (in_array($satuanBahan, [1, 27])) {
            $batas = [
                25, 50, 100, 200, 300, 400, 500, 600, 700, 800, 900,
                1000, 1200, 1400, 1500, 1800, 2000, 2250, 2500, 2750,
                3000, 3250, 3500, 3750, 4000, 4250, 4500, 4750, 5000,
                5500, 6000, 6500, 7000, 7500, 8000, 8500, 9000, 9500,
                10000, 11000, 12000
            ];

            foreach ($batas as $nilai) {
                if ($jumlahKebutuhan < $nilai) {
                    $jumlahKebutuhan = $nilai;
                    break;
                }
            }

            if ($jumlahKebutuhan > 12000) {
                $jumlahKebutuhan = ceil($jumlahKebutuhan / 1000) * 1000;
            }

            return $jumlahKebutuhan;
        }

        return ceil($jumlahKebutuhan);
    }

    public function index()
    {
        $header = 'Paket Menu';

        $karbohidrat = Resep::where('id_komponen_sehat', 1)->get();
        $lauk        = Resep::where('id_komponen_sehat', 2)->get();
        $sayur       = Resep::where('id_komponen_sehat', 3)->get();
        $buah        = Resep::where('id_komponen_sehat', 4)->get();
        $suplemen    = Resep::where('id_komponen_sehat', 5)->get();

        $data = PaketMenu::all();

        return view('office.paket_menu.index', compact(
            'data',
            'header',
            'karbohidrat',
            'lauk',
            'sayur',
            'buah',
            'suplemen'
        ));
    }


    // Data untuk DataTables
    public function dt_paket()
    {
        $data = PaketMenu::orderBy('id', 'desc')->get();
        return DataTables::of($data)
            ->addIndexColumn() // Menambah index
            ->addColumn('action', function ($row) {
                $action = '<button class="btn btn-sm btn-warning btn-edit-box" 
                        data-id="' . $row->id . '"
                        data-paket="' . $row->paket . '"
                        data-resepkarbo="' . $row->resep_karbo . '"
                        data-reseplauk="' . $row->resep_protein . '"
                        data-resepsayur="' . $row->resep_sayur . '"
                        data-resepbuah="' . $row->resep_buah . '"
                        data-resepsuplemen="' . $row->resep_suplemen . '"
                        data-beratmentahkarboa="' . $row->berat_mentah_karbo_a . '"
                        data-beratmentahkarbob="' . $row->berat_mentah_karbo_b . '"
                        data-beratmentahproteina="' . $row->berat_mentah_protein_a . '"
                        data-beratmentahproteinb="' . $row->berat_mentah_protein_b . '"
                        data-beratmentahsayur1a="' . $row->berat_mentah_sayur1_a . '"
                        data-beratmentahsayur2a="' . $row->berat_mentah_sayur2_a . '"
                        data-beratmentahsayur3a="' . $row->berat_mentah_sayur3_a . '"
                        data-beratmentahsayur4a="' . $row->berat_mentah_sayur4_a . '"
                        data-beratmentahsayur1b="' . $row->berat_mentah_sayur1_b . '"
                        data-beratmentahsayur2b="' . $row->berat_mentah_sayur2_b . '"
                        data-beratmentahsayur3b="' . $row->berat_mentah_sayur3_b . '"
                        data-beratmentahsayur4b="' . $row->berat_mentah_sayur4_b . '"
                        data-beratmentahbuaha="' . $row->berat_mentah_buah_a . '"
                        data-beratmentahbuahb="' . $row->berat_mentah_buah_b . '"
                        data-beratmentahsuplemena="' . $row->berat_mentah_suplemen_a . '"
                        data-beratmentahsuplemenb="' . $row->berat_mentah_suplemen_b . '"
                      
                        
                        "> 
                        Edit
                    </button>
                     <button class="btn btn-sm btn-danger btn-delete-box"
                        data-id="' . $row->id . '">
                        Hapus
                    </button>
                    ';
                return $action;
            })
            ->addColumn('nama_karbo', function ($row) {
                
                $nama = Resep::find($row->resep_karbo);
                return $nama->nama_resep;
            })
            ->addColumn('nama_lauk', function ($row) {
                $nama = Resep::find($row->resep_protein);
                return $nama->nama_resep;
            })
            ->addColumn('nama_sayur', function ($row) {
                $nama = Resep::find($row->resep_sayur);
                return $nama->nama_resep;
            })
            ->addColumn('nama_buah', function ($row) {
                $nama = Resep::find($row->resep_buah);
                return $nama->nama_resep;
            })
            ->addColumn('nama_suplemen', function ($row) {
                $nama = Resep::find($row->resep_suplemen);
                return $nama->nama_resep;
            })
            ->rawColumns([
                'action', 
                'nama_suplemen',
                'nama_buah',
                'nama_sayur',
                'nama_lauk',
                'nama_karbo'])
            ->make(true);
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'paket' => 'required'
        ]);

        //PaketMenu::create($request->all());
        PaketMenu::create([
            'paket'                 => $request->paket,       // relasi bahan baku
            'resep_karbo'           => $request->id_karbo,        // jumlah isi per box
            'resep_protein'         => $request->id_lauk ,   // jumlah hasil matang
            'resep_sayur'           => $request->id_sayur,     // jumlah penyusutan
            'resep_buah'            => $request->id_buah,     // jumlah penyusutan
            'resep_suplemen'        => $request->id_suplemen,     // jumlah penyusutan
            'berat_mentah_karbo_a'          => $request->berat_mentah_karbo_a,     // jumlah penyusutan
            'berat_mentah_karbo_b'          => $request->berat_mentah_karbo_b,     // jumlah penyusutan
            'berat_mentah_protein_a'        => $request->berat_mentah_protein_a,     // jumlah penyusutan
            'berat_mentah_protein_b'        => $request->berat_mentah_protein_b,     // jumlah penyusutan
            'berat_mentah_sayur1_a'         => $request->berat_mentah_sayur1_a,     // jumlah penyusutan
            'berat_mentah_sayur2_a'         => $request->berat_mentah_sayur2_a,     // jumlah penyusutan
            'berat_mentah_sayur3_a'         => $request->berat_mentah_sayur3_a,     // jumlah penyusutan
            'berat_mentah_sayur4_a'         => $request->berat_mentah_sayur4_a,     // jumlah penyusutan
            'berat_mentah_sayur1_b'         => $request->berat_mentah_sayur1_b,     // jumlah penyusutan
            'berat_mentah_sayur2_b'         => $request->berat_mentah_sayur2_b,     // jumlah penyusutan
            'berat_mentah_sayur3_b'         => $request->berat_mentah_sayur3_b,     // jumlah penyusutan
            'berat_mentah_sayur4_b'         => $request->berat_mentah_sayur4_b,     // jumlah penyusutan
            'berat_mentah_buah_a'           => $request->berat_mentah_buah_a,     // jumlah penyusutan
            'berat_mentah_buah_b'           => $request->berat_mentah_buah_b,     // jumlah penyusutan
            'berat_mentah_suplemen_a'       => $request->berat_mentah_suplemen_a,     // jumlah penyusutan
            'berat_mentah_suplemen_b'       => $request->berat_mentah_suplemen_b,     // jumlah penyusutan


        ]);
        return response()->json([
            'success' => true,
            'message' => 'Data Paket berhasil ditambahkan.'
        ]);
        //return redirect()->back()->with('success', 'Data paket berhasil ditambahkan!');
    }

    // Update data
    public function update(Request $request, $id)
    {
        //$paket = PaketMenu::findOrFail($id);
       
        $request->validate([
            'paket' => 'required'
        ]);

        //PaketMenu::create($request->all());
        PaketMenu::where('id',$id)->update([
            'paket'                 => $request->paket,       // relasi bahan baku
            'resep_karbo'           => $request->id_karbo,        // jumlah isi per box
            'resep_protein'         => $request->id_lauk,   // jumlah hasil matang
            'resep_sayur'           => $request->id_sayur,     // jumlah penyusutan
            'resep_buah'            => $request->id_buah,     // jumlah penyusutan
            'resep_suplemen'        => $request->id_suplemen,     // jumlah penyusutan
            'berat_mentah_karbo_a'          => $request->berat_mentah_karbo_a,     // jumlah penyusutan
            'berat_mentah_karbo_b'          => $request->berat_mentah_karbo_b,     // jumlah penyusutan
            'berat_mentah_protein_a'        => $request->berat_mentah_protein_a,     // jumlah penyusutan
            'berat_mentah_protein_b'        => $request->berat_mentah_protein_b,     // jumlah penyusutan
            'berat_mentah_sayur1_a'         => $request->berat_mentah_sayur1_a,     // jumlah penyusutan
            'berat_mentah_sayur2_a'         => $request->berat_mentah_sayur2_a,     // jumlah penyusutan
            'berat_mentah_sayur3_a'         => $request->berat_mentah_sayur3_a,     // jumlah penyusutan
            'berat_mentah_sayur4_a'         => $request->berat_mentah_sayur4_a,     // jumlah penyusutan
            'berat_mentah_sayur1_b'         => $request->berat_mentah_sayur1_b,     // jumlah penyusutan
            'berat_mentah_sayur2_b'         => $request->berat_mentah_sayur2_b,     // jumlah penyusutan
            'berat_mentah_sayur3_b'         => $request->berat_mentah_sayur3_b,     // jumlah penyusutan
            'berat_mentah_sayur4_b'         => $request->berat_mentah_sayur4_b,     // jumlah penyusutan
            'berat_mentah_buah_a'           => $request->berat_mentah_buah_a,     // jumlah penyusutan
            'berat_mentah_buah_b'           => $request->berat_mentah_buah_b,     // jumlah penyusutan
            'berat_mentah_suplemen_a'       => $request->berat_mentah_suplemen_a,     // jumlah penyusutan
            'berat_mentah_suplemen_b'       => $request->berat_mentah_suplemen_b,     // jumlah penyusutan


        ]);
        return response()->json([
            'success' => true,
            'message' => 'Data Paket berhasil diupdate.'
        ]);
        //return redirect()->back()->with('success', 'Data paket berhasil diperbarui!');
    }

    // Hapus data
    public function destroy($id)
    {
        $paket = PaketMenu::find($id);
        if (!$paket) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }
        $paket->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus.'
        ]);
    }

    public function pilihmenu(Request $request)
    {
        $validated = $request->validate([
            'id_menu' => 'required',
            'pilihan' => 'required',
        ]);


        $paket = PaketMenu::find($request->pilihan);
        DB::table('tb_menu')
            ->where('id', $request->id_menu)
            ->update([
            'karbohidrat' => $paket->resep_karbo,
            'protein' => $paket->resep_protein,
            'sayur' => $paket->resep_sayur,
            'buah' => $paket->resep_buah,
            'susu' => $paket->resep_suplemen,
            ]);
        DB::table('tb_rincian_menu_temp')->where('id_menu', $request->id_menu)->delete();
        $jumlah_siswa_a = DB::table('rincian_sekolah')
            ->where('id_menu_harian', $request->id_menu)
            ->sum('jumlah_penerima_a');

        $jumlah_siswa_b = DB::table('rincian_sekolah')
            ->where('id_menu_harian', $request->id_menu)
            ->sum('jumlah_penerima_b');
        $jumlah_total_siswa = $jumlah_siswa_a + $jumlah_siswa_b;

        // buat menu karbo
        // 4?? Ambil input porsi A dan B
        $karbo_porsi_a = $paket->berat_mentah_karbo_a;
        $karbo_porsi_b = $paket->berat_mentah_karbo_b;
        // 5?? Hitung kebutuhan beras kategori A dan B (dibulatkan ke atas)
        $karbo_kebutuhan_beras_a = ceil(($jumlah_siswa_a * $karbo_porsi_a * 3 / 4 / 2) / 1000);
        $karbo_kebutuhan_beras_b = ceil(($jumlah_siswa_b * $karbo_porsi_b * 3 / 4 / 2) / 1000);
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
        $cek_rumus = DB::table('tb_rumus_perhitungan_karbo')->where('id_menu', $request->id_menu)->first();
        if($cek_rumus)
        {
            DB::table('tb_rumus_perhitungan_karbo')->where('id_menu', $request->id_menu)->update([
                //'id_menu' => $request->id_menu,
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
        }else{
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
        }
        

        $data_karbo = MenuBahan::where('menu_id', $paket->resep_karbo)->get();
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
                    } else {
                        $keterangan = DB::table('tb_spesifikasi_bahan')->where('id_bahan', $row->bahan_id)
                            ->first();
                        $keterangan = $keterangan->spesifikasi ?? '-';
                    }
                    if ($harga) {
                        DB::table('tb_rincian_menu_temp')->insert([
                            'id_menu'       => $request->id_menu,
                            'id_resep'      => $paket->resep_karbo,
                            'id_bahan'      => $row->bahan_id,
                            'jumlah'        => $karbo_kebutuhan_beras_total,
                            'bumbu'         => 0,
                            'harga'         => $harga->harga_bahan,
                            'total_harga'   => $harga->harga_bahan * $karbo_kebutuhan_beras_total,
                            'id_satuan'     => 25,
                            'jumlah_box'     => 1,
                            'id_kontrak'     => $harga->id ?? 0,
                            'keterangan'     => $keterangan ?? '-',
                        ]);
                    }
                } else {

                    if ($harga) {
                        DB::table('tb_rincian_menu_temp')->insert([
                            'id_menu'       => $request->id_menu,
                            'id_resep'      => $paket->resep_karbo,
                            'id_bahan'      => $row->bahan_id,
                            'jumlah'        => $jumlah_total_siswa,
                            'bumbu'         => 0,
                            'harga'         => $harga->harga_bahan,
                            'total_harga'   => $harga->harga_bahan * $jumlah_total_siswa,
                            'id_satuan'     => $row->id_satuan,
                            'jumlah_box'     => 1,
                            'id_kontrak'     => $harga->id ?? 0,
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
                $satuan_bahan = $bahan->satuan_bahan ?? 1;
                $jumlah_kebutuhan = $this->bulatkanKebutuhanNonUtama($jumlah_kebutuhan, $satuan_bahan);

                if ($harga) {
                    DB::table('tb_rincian_menu_temp')->insert([
                        'id_menu'       => $request->id_menu,
                        'id_resep'      => $paket->resep_karbo,
                        'id_bahan'      => $row->bahan_id,
                        'jumlah'        => $jumlah_kebutuhan,
                        'bumbu'         => 0,
                        'harga'         => $harga->harga_bahan,
                        'total_harga'   =>  $harga->harga_bahan * $jumlah_kebutuhan / 1000,
                        'id_satuan'     => $bahan->satuan_bahan,
                        'jumlah_box'     => 1,
                        'id_kontrak'     => $harga->id ?? 0,
                        'keterangan'     => $keterangan ?? '-',
                    ]);
                }
            }
        }

        // buat menu Lauk
        // 4?? Ambil input porsi A dan B
        $protein_porsi_a = $paket->berat_mentah_protein_a;
        $protein_porsi_b = $paket->berat_mentah_protein_b;

        $data_resep = MenuBahan::where('menu_id', $paket->resep_protein)->where('status_bahan_baku', 1)->first();
        $data_resep_2 = MenuBahan::where('menu_id', $paket->resep_protein)->where('status_bahan_baku', 2)->first();
        $data_bahan = TbMasterBahan::find($data_resep->bahan_id);

        $kebutuhan_total_matang = $jumlah_siswa_b * $protein_porsi_b / 1000 + $jumlah_siswa_a * $protein_porsi_a / 1000;
        
        if ($data_bahan->satuan_bahan == 36 || $data_bahan->satuan_bahan == 40 ||  $data_bahan->satuan_bahan == 32) {
            $kebutuhan_total_matang = $jumlah_siswa_b  + $jumlah_siswa_a ;

           // $kebutuhan_total_matang = $kebutuhan_total_matang * 10;
        } else {
        }

        $buffer = Buffer::first();
        $buffer = $buffer->buffer_menu;
        $cek_rumus = DB::table('tb_rumus_perhitungan_protein')->where('id_menu', $request->id_menu)->first();
        if($cek_rumus)
        {
            DB::table('tb_rumus_perhitungan_protein')->where('id_menu', $request->id_menu)->update([
               // 'id_menu' => $request->id_menu,
                'protein_porsi_a' => $protein_porsi_a,
                'protein_porsi_b' => $protein_porsi_b,
                'kebutuhan_total_matang' => $kebutuhan_total_matang + $kebutuhan_total_matang * $buffer / 100,
                'status' => 1,

            ]);
        }else{
            DB::table('tb_rumus_perhitungan_protein')->insert([
                'id_menu' => $request->id_menu,
                'protein_porsi_a' => $protein_porsi_a,
                'protein_porsi_b' => $protein_porsi_b,
                'kebutuhan_total_matang' => $kebutuhan_total_matang + $kebutuhan_total_matang * $buffer / 100,
                'status' => 1,

            ]);
        }
            
        $kebutuhan_protein_a = 0;
        $kebutuhan_protein_b = 0;
            
        if($data_resep)
        {
            if ($data_resep->id_satuan == 1) {
                $kebutuhan_protein_a = $jumlah_siswa_a * $protein_porsi_a  / 1000 + $jumlah_siswa_b * $protein_porsi_b  / 1000;
            } else {
                $kebutuhan_protein_a = $jumlah_siswa_a + $jumlah_siswa_b;
            }
        }

        if ($data_resep_2) {
            if ($data_resep_2->id_satuan == 1) {
                $kebutuhan_protein_b = $jumlah_siswa_a * $protein_porsi_a  / 1000 + $jumlah_siswa_b * $protein_porsi_b  / 1000;
            } else {
                $kebutuhan_protein_b = $jumlah_siswa_a + $jumlah_siswa_b;
            }
        }



        $total_kebutuhan_protein = $kebutuhan_total_matang + $kebutuhan_total_matang * $buffer / 100;
        $penyusutan_protein_a = 0;
        $penyusutan_protein_b = 0;
        $kebutuhan_matang_a = $kebutuhan_protein_a - $penyusutan_protein_a;
        $kebutuhan_matang_b = $kebutuhan_protein_b - $penyusutan_protein_b;



        $data_bahan = TbMasterBahan::find($data_resep->bahan_id);
        if ($data_bahan->satuan_bahan == 1) {
            $tilting = 20;
            $jumlah_tilting = $total_kebutuhan_protein / $tilting;
        } else {
            $tilting = 20000;
            $jumlah_tilting = $total_kebutuhan_protein * 100 / $tilting;
        }

 
        DB::table('tb_rumus_perhitungan_protein')->where('id_menu', $request->id_menu)->update([
            'kebutuhan_protein_a' => $kebutuhan_protein_a,
            'kebutuhan_protein_b' => $kebutuhan_protein_b,
            'penyusutan_protein_a' => $penyusutan_protein_a,
            'penyusutan_protein_b' => $penyusutan_protein_b,
            'kebutuhan_matang_a' => $kebutuhan_matang_a,
            'kebutuhan_matang_b' => $kebutuhan_matang_b,
            'kebutuhan_matang_realisasi' => ($kebutuhan_matang_a + $kebutuhan_matang_b),
            'kebutuhan_total_mentah' => $total_kebutuhan_protein,
            'kapasitas_tilting' => 20,
            'jumlah_masak' => $jumlah_tilting,

            'status' => 2,

        ]);
        $jumlah_box_rencana = $jumlah_tilting;
        if ($jumlah_box_rencana == 0) {
            $jumlah_box_rencana = 0;
        }

        $data_rumus_protein = DB::table('tb_rumus_perhitungan_protein')->where('id_menu', $request->id_menu)->first();

        $data_protein = MenuBahan::where('menu_id', $paket->resep_protein)->orderByRaw("FIELD(status_bahan_baku, 1, 2, 4, 5, 3)")
            ->get();
        $data_bahan_utama = MenuBahan::where('menu_id', $paket->resep_protein)->where('status_bahan_baku', 1)->first();
        $data_bahan_utama = TbMasterBahan::find($data_bahan_utama->bahan_id);
        $utama = 1;

        foreach ($data_protein as $row) {

            $harga = DB::table('tb_rincian_kontrak')->where('id_bahan', $row->bahan_id)
                ->where('status',  1)->orderByDesc('id') // ambil yang terbaru berdasarkan id
                ->first() ?? 0;
            $data_bahan = TbMasterBahan::find($row->bahan_id);
            //$data_bahan = MenuBahan::where('menu_id', $data_menu->protein)->where('bahan_id', $row->bahan_id)->first();
            $satuan = 1;
            $kebutuhan_mentah = $total_kebutuhan_protein;

            $jumlah_box = 1;
            $keterangan = DB::table('tb_spesifikasi_bahan')->where('id_bahan', $row->bahan_id)
                ->first();
            $keterangan = $keterangan->spesifikasi ?? '-';
            if ($row->status_bahan_baku == 1) {
                if ($data_bahan->satuan_bahan == 36 || $data_bahan_utama->satuan_bahan == 40) {
                    $satuan = 36;
                    $kebutuhan_protein_a    = $data_rumus_protein->kebutuhan_protein_a;
                    $kebutuhan_protein_b    = $data_rumus_protein->kebutuhan_protein_b;
                    $utama = 36;
                } else if ($data_bahan_utama->satuan_bahan == 32) {
                    $satuan = 32;
                    $utama = 32;
                    $kebutuhan_protein_a    = $data_rumus_protein->kebutuhan_protein_a;
                    $kebutuhan_protein_b    = $data_rumus_protein->kebutuhan_protein_b;
                } else if ($data_bahan_utama->satuan_bahan == 22) {
                    $satuan = 22;
                    $utama = 22;
                    $kebutuhan_protein_a    = $data_rumus_protein->kebutuhan_protein_a;
                    $kebutuhan_protein_b    = $data_rumus_protein->kebutuhan_protein_b;
                } else {
                    $utama = 25;
                    $satuan = 25;
                }
                $jumlah_box = ($kebutuhan_protein_a + $kebutuhan_protein_a * $buffer / 100) / $jumlah_tilting;
                DB::table('tb_rincian_menu_temp')->insert([
                    'id_menu' => $request->id_menu,
                    'id_resep'      => $paket->resep_protein,
                    'id_bahan'      => $row->bahan_id,
                    'jumlah'        => ($kebutuhan_protein_a + $kebutuhan_protein_a * $buffer / 100),
                    'bumbu'         => 0,
                    'harga'         => $harga->harga_bahan ?? 0,
                    'total_harga'   => ($harga->harga_bahan ?? 0) * ($kebutuhan_protein_a + $kebutuhan_protein_a * $buffer / 100),
                    'jumlah_box'    => $jumlah_box_rencana,
                    'id_satuan'     => $satuan,
                    'id_kontrak'     => ($harga->id ?? 0),
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
                    'id_resep'      => $paket->resep_protein,
                    'id_bahan'      => $row->bahan_id,
                    'jumlah'        => ($kebutuhan_protein_b + $kebutuhan_protein_b * $buffer / 100),
                    'bumbu'         => 0,
                    'harga'         => ($harga->harga_bahan ?? 0),
                    'total_harga'   => ($harga->harga_bahan ?? 0) * ($kebutuhan_protein_b + $kebutuhan_protein_b * $buffer / 100),
                    'jumlah_box'    => $jumlah_box_rencana,
                    'id_satuan'     => $satuan,
                    'id_kontrak'     => $harga->id ?? 0,
                    'keterangan'    => $keterangan

                ]);
            } else {
                $data_perhitungan_bumbu = PerhitunganBumbu::where('id_menu_bahan', $row->id)->first();
                $pengali = $data_perhitungan_bumbu->pengali ?? 10;
                $pembagi = $data_perhitungan_bumbu->pembagi ?? 115;
                $kebutuhan_mentah       = $total_kebutuhan_protein;
                $jumlah_kebutuhan = $kebutuhan_mentah ;
                if($data_bahan_utama->satuan_bahan == 36 || $data_bahan_utama->satuan_bahan == 40)
                {
                    $jumlah_kebutuhan = $kebutuhan_mentah / $pembagi * $pengali;
                } elseif ($data_bahan_utama->satuan_bahan == 1 )
                {
                    $jumlah_kebutuhan = $kebutuhan_mentah * 1000 / $pembagi * $pengali;
                }else{
                    $jumlah_kebutuhan = $kebutuhan_mentah  / $pembagi * $pengali;
                }
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
                $jumlah_kebutuhan = $this->bulatkanKebutuhanNonUtama($jumlah_kebutuhan, $satuan_bahan);
                

                $nama_satuan = TbSatuan::find($satuan_bahan);
                if ($satuan_bahan == 1 || $satuan_bahan == 27) {
                    DB::table('tb_rincian_menu_temp')->insert([
                        'id_menu' => $request->id_menu,
                        'id_resep'      => $paket->resep_protein,
                        'id_bahan'      => $row->bahan_id,
                        'jumlah'        => $jumlah_kebutuhan,
                        'bumbu'         => 0,
                        'harga'         => ($harga->harga_bahan ?? 0),
                        'total_harga'   => ($harga->harga_bahan ?? 0) / 1000 * $jumlah_kebutuhan,
                        'jumlah_box'    => $jumlah_box,
                        'id_satuan'     => $satuan_bahan ?? 0,
                        //'id_satuan'     => $data_bahan->id_satuan ?? 0,
                        'id_kontrak'     => $harga->id ?? 0,
                        'keterangan'    => $keterangan

                    ]);
                } else {
                    DB::table('tb_rincian_menu_temp')->insert([
                        'id_menu' => $request->id_menu,
                        'id_resep'      => $paket->resep_protein,
                        'id_bahan'      => $row->bahan_id,
                        'jumlah'        => $jumlah_kebutuhan * 10,
                        'bumbu'         => 0,
                        'harga'         => ($harga->harga_bahan ?? 0),
                        'total_harga'   => ($harga->harga_bahan ?? 0)  * $jumlah_kebutuhan * 10,
                        'jumlah_box'    => $jumlah_box,
                        'id_satuan'     => $satuan_bahan ?? 0,
                        //'id_satuan'     => $data_bahan->id_satuan ?? 0,
                        'id_kontrak'     => $harga->id ?? 0,
                        'keterangan'    => $keterangan

                    ]);
                }
            }
        }

        // sayur ==========================================
        $cek_bahan = DB::table('tb_menu_bahan')->where('menu_id', $paket->resep_sayur)->count();
        if ($cek_bahan == 0) {
            return redirect()->route('rincian_bahan', ['idmenu' => $request->id_menu])
                ->with('success', 'Sayur berhasil disimpan.');
        }
        if($jumlah_siswa_a == 0 )
        {
            $sayur_porsi_a = 0;
        }else{
            $sayur_porsi_a = $paket->berat_mentah_sayur1_a + $paket->berat_mentah_sayur2_a + $paket->berat_mentah_sayur3_a + $paket->berat_mentah_sayur4_a;
        }

        if ($jumlah_siswa_b == 0) {

            $sayur_porsi_b = 0;
        }else{
            $sayur_porsi_b = $paket->berat_mentah_sayur1_b + $paket->berat_mentah_sayur2_b + $paket->berat_mentah_sayur3_b + $paket->berat_mentah_sayur4_b;
        }

        $kebutuhan_total_matang = $jumlah_siswa_b * $sayur_porsi_b / 1000 + $jumlah_siswa_a * $sayur_porsi_a / 1000;
        $kebutuhan_total_matang = $kebutuhan_total_matang + $kebutuhan_total_matang * $buffer / 100;
        DB::table('tb_rumus_perhitungan_sayur')->insert([
            'id_menu' => $request->id_menu,
            'sayur_porsi_a' => $sayur_porsi_a,
            'sayur_porsi_b' => $sayur_porsi_b,
            'kebutuhan_total_matang' => $kebutuhan_total_matang,
            'status' => 1,

        ]);
        $kebutuhan_sayur_a = 0;
        $kebutuhan_sayur_b = 0;
        $kebutuhan_sayur_c = 0;
        $kebutuhan_sayur_d = 0;

        if ($paket->paket == 'A') {
            $kebutuhan_sayur_a = $paket->berat_mentah_sayur1_a * $jumlah_total_siswa / 1000;
            $kebutuhan_sayur_b = $paket->berat_mentah_sayur2_a * $jumlah_total_siswa / 1000;
            $kebutuhan_sayur_c = $paket->berat_mentah_sayur3_a * $jumlah_total_siswa / 1000;
            $kebutuhan_sayur_d = $paket->berat_mentah_sayur4_a * $jumlah_total_siswa / 1000;
        } else {
            $kebutuhan_sayur_a = $paket->berat_mentah_sayur1_b * $jumlah_total_siswa / 1000;
            $kebutuhan_sayur_b = $paket->berat_mentah_sayur2_b * $jumlah_total_siswa / 1000;
            $kebutuhan_sayur_c = $paket->berat_mentah_sayur3_b * $jumlah_total_siswa / 1000;
            $kebutuhan_sayur_d = $paket->berat_mentah_sayur4_b * $jumlah_total_siswa / 1000;
        }
        $penyusutan_sayur_a = 0;
        $penyusutan_sayur_b = 0;
        $penyusutan_sayur_c = 0;
        $penyusutan_sayur_d = 0;
        $kebutuhan_matang_a = $kebutuhan_sayur_a;
        $kebutuhan_matang_b = $kebutuhan_sayur_b;
        $kebutuhan_matang_c = $kebutuhan_sayur_c;
        $kebutuhan_matang_d = $kebutuhan_sayur_d;
        $sayur1 = MenuBahan::where('menu_id', $paket->resep_sayur)->where('status_bahan_baku', 1)->first();
        $sayur2 = MenuBahan::where('menu_id', $paket->resep_sayur)->where('status_bahan_baku', 2)->first();
        $sayur3 = MenuBahan::where('menu_id', $paket->resep_sayur)->where('status_bahan_baku', 4)->first();
        $sayur4 = MenuBahan::where('menu_id', $paket->resep_sayur)->where('status_bahan_baku', 5)->first();
        if (!$sayur1) {
            $kebutuhan_sayur_a = 0;
            $penyusutan_sayur_a = 0;
            $kebutuhan_matang_a = 0;
        }
        if (!$sayur2) {
            $kebutuhan_sayur_b = 0;
            $penyusutan_sayur_b = 0;
            $kebutuhan_matang_b = 0;
        }
        if (!$sayur3) {
            $kebutuhan_sayur_c = 0;
            $penyusutan_sayur_c = 0;
            $kebutuhan_matang_c = 0;
        }
        if (!$sayur4) {
            $kebutuhan_sayur_d = 0;
            $penyusutan_sayur_d = 0;
            $kebutuhan_matang_d = 0;
        }

        $total_matang_sayur = $kebutuhan_matang_a + $kebutuhan_matang_b + $kebutuhan_matang_c + $kebutuhan_matang_d;
        $total_matang_sayur = $total_matang_sayur + $total_matang_sayur * $buffer / 100;
        DB::table('tb_rumus_perhitungan_sayur')->where('id_menu', $request->id_menu)->update([
            'kebutuhan_sayur_a' => $kebutuhan_sayur_a,
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
            'kebutuhan_matang_realisasi' => $total_matang_sayur,
            'kebutuhan_total_mentah' => $total_matang_sayur,
            'kapasitas_tilting' => 20,
            'jumlah_masak' => ceil($total_matang_sayur / 20),

            'status' => 2,

        ]);

        $jumlah_box_sayur_rencana =  $total_matang_sayur / 20;

        $jumlah_tilting = $total_matang_sayur / 20;
        $data_rumus_sayur = DB::table('tb_rumus_perhitungan_sayur')->where('id_menu', $request->id_menu)->first();
        $data_sayur = MenuBahan::where('menu_id', $paket->resep_sayur)->orderByRaw("FIELD(status_bahan_baku, 1, 2, 4, 5, 3)")
            ->get();
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
                    $box = floor($total_matang_sayur / 20);

                    // jika hasil floor = 0, maka dibuat 1
                    if ($box == 0) {
                        $box = 1;
                    }

                    $jumlah_box = $kebutuhan_sayur_a / $box;
                    $jumlah_masak = $box;
                    $jumlah_box = ceil(ceil($jumlah_box / ($data_box->isi_per_box / 1000)) * $jumlah_masak);
                } else {
                    $jumlah_box = ($kebutuhan_sayur_a + $kebutuhan_sayur_a * $buffer / 100) / $jumlah_tilting;
                }


                if ($jumlah_box == 0 && !empty($row->bahan_id) && $kebutuhan_sayur_a > 0) {
                    $jumlah_box = 1;
                }
                DB::table('tb_rincian_menu_temp')->insert([
                    'id_menu' => $request->id_menu,
                    'id_resep'      => $paket->resep_sayur,
                    'id_bahan'      => $row->bahan_id,
                    'jumlah'        => ($kebutuhan_sayur_a + $kebutuhan_sayur_a * $buffer / 100),
                    'bumbu'         => 0,
                    'harga'         => ($harga->harga_bahan ?? 0),
                    'total_harga'   => ($harga->harga_bahan ?? 0) * ($kebutuhan_sayur_a + $kebutuhan_sayur_a * $buffer / 100),
                    'jumlah_box'    => $jumlah_box,
                    'id_satuan'     => 25,
                    //'jumlah_box'     => 1,
                    'id_kontrak'     => $harga->id ?? 0,
                    'keterangan'     => $keterangan ?? '-'
                ]);
            } else if ($row->status_bahan_baku == 2) {
                $data_box = DB::table('tb_box_bahan_baku')->where('id_bahan', $row->bahan_id)->select('isi_per_box')->first();
                if ($data_box) {
                    $box = floor($total_matang_sayur / 20);

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
                    'id_resep'      => $paket->resep_sayur,
                    'id_bahan'      => $row->bahan_id,
                    'jumlah'        => ($kebutuhan_sayur_b + $kebutuhan_sayur_b * $buffer / 100),
                    'bumbu'         => 0,
                    'harga'         => ($harga->harga_bahan ?? 0),
                    'total_harga'   => ($harga->harga_bahan ?? 0) * ($kebutuhan_sayur_b + $kebutuhan_sayur_b * $buffer / 100),
                    'jumlah_box'    => $jumlah_box,
                    'id_satuan'     => 25,
                    'id_kontrak'     => $harga->id ?? 0,
                    'keterangan'     => $keterangan ?? '-'
                ]);
            } else if ($row->status_bahan_baku == 4) {
                $data_box = DB::table('tb_box_bahan_baku')->where('id_bahan', $row->bahan_id)->select('isi_per_box')->first();
                if ($data_box) {
                    $box = floor($total_matang_sayur / 20);

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
                    'id_resep'      => $paket->resep_sayur,
                    'id_bahan'      => $row->bahan_id,
                    'jumlah'        => ($kebutuhan_sayur_c + $kebutuhan_sayur_c * $buffer / 100),
                    'bumbu'         => 0,
                    'harga'         => ($harga->harga_bahan ?? 0),
                    'total_harga'   => ($harga->harga_bahan ?? 0) * ($kebutuhan_sayur_c + $kebutuhan_sayur_c * $buffer / 100),
                    'jumlah_box'    => $jumlah_box,
                    'id_satuan'     => 25,
                    'id_kontrak'     => ($harga->id ?? 0),
                    'keterangan'     => $keterangan ?? '-'
                ]);
            } else if ($row->status_bahan_baku == 5) {
                $data_box = DB::table('tb_box_bahan_baku')->where('id_bahan', $row->bahan_id)->select('isi_per_box')->first();
                if ($data_box) {
                    $box = floor($total_matang_sayur / 20);

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
                    'id_resep'      => $paket->resep_sayur,
                    'id_bahan'      => $row->bahan_id,
                    'jumlah'        => ($kebutuhan_sayur_d + $kebutuhan_sayur_d * $buffer / 100),
                    'bumbu'         => 0,
                    'harga'         => ($harga->harga_bahan ?? 0),
                    'total_harga'   => ($harga->harga_bahan ?? 0) * ($kebutuhan_sayur_d + $kebutuhan_sayur_d * $buffer / 100),
                    'jumlah_box'    => $jumlah_box,
                    'id_satuan'     => 25,
                    'id_kontrak'     => ($harga->id ?? 0),
                    'keterangan'     => $keterangan ?? '-'

                ]);
            } else {
                $data_perhitungan_bumbu = PerhitunganBumbu::where('id_menu_bahan', $row->id)->first();
                $pengali = $data_perhitungan_bumbu->pengali ?? 10;
                $pembagi = $data_perhitungan_bumbu->pembagi ?? 115;
                $jumlah_kebutuhan = $total_matang_sayur * 1000;
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
                $jumlah_kebutuhan = $this->bulatkanKebutuhanNonUtama($jumlah_kebutuhan, $satuan_bahan);
                $nama_satuan = TbSatuan::find($satuan_bahan);
                if ($jumlah_box == 0 && !empty($row->bahan_id) && $jumlah_kebutuhan > 0) {
                    $jumlah_box = 1;
                }
                if ($satuan_bahan == 1 || $satuan_bahan == 27) {
                    DB::table('tb_rincian_menu_temp')->insert([
                        'id_menu' => $request->id_menu,
                        'id_resep'      => $paket->resep_sayur,
                        'id_bahan'      => $row->bahan_id,
                        'jumlah'        => $jumlah_kebutuhan ?? 0,
                        'bumbu'         => 0,
                        'harga'         => ($harga->harga_bahan ?? 0),
                        'total_harga'   => ($harga->harga_bahan ?? 0) / 1000 * $jumlah_kebutuhan,
                        'jumlah_box'    => $jumlah_box,
                        'id_satuan'     => ($satuan_bahan ?? 2),
                        'id_kontrak'     => ($harga->id ?? 0),
                        'keterangan'     => $keterangan ?? '-'
                    ]);
                } else {
                    DB::table('tb_rincian_menu_temp')->insert([
                        'id_menu' => $request->id_menu,
                        'id_resep'      => $paket->resep_sayur,
                        'id_bahan'      => $row->bahan_id,
                        'jumlah'        => $jumlah_kebutuhan ?? 0,
                        'bumbu'         => 0,
                        'harga'         => ($harga->harga_bahan ?? 0),
                        'total_harga'   => ($harga->harga_bahan ?? 0)  * $jumlah_kebutuhan,
                        'jumlah_box'    => $jumlah_box,
                        'id_satuan'     => ($satuan_bahan ?? 2),
                        'id_kontrak'     => ($harga->id ?? 0),
                        'keterangan'     => $keterangan ?? '-'
                    ]);
                }
            }
        }


        // Buah =================================
        // 4?? Ambil input porsi A dan B
        $buah_porsi_a = $paket->berat_mentah_buah_a;
        $buah_porsi_b = $paket->berat_mentah_buah_b;
        $kebutuhan_total_matang = $jumlah_siswa_b  + $jumlah_siswa_a ;
        $kebutuhan_total_matang = $kebutuhan_total_matang + $kebutuhan_total_matang * $buffer / 100;

        DB::table('tb_rumus_perhitungan_buah')->insert([
            'id_menu' => $request->id_menu,
            'buah_porsi_a' => $buah_porsi_a,
            'buah_porsi_b' => $buah_porsi_b,
            'kebutuhan_total_matang' => $kebutuhan_total_matang,
            'kebutuhan_buah_a'      => $kebutuhan_total_matang,
            'kebutuhan_buah_b'      => $kebutuhan_total_matang,
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
        $data_buah = MenuBahan::where('menu_id', $paket->resep_buah)->get();

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
            DB::table('tb_rincian_menu_temp')->insert([
                'id_menu' => $request->id_menu,
                'id_resep'      => $paket->resep_buah,
                'id_bahan'      => $row->bahan_id,
                'jumlah'        => $kebutuhan_total_matang,
                'bumbu'         => 0,
                'harga'         => ($harga->harga_bahan ?? 0),
                'jumlah_box'    => $jumlah_box,
                'total_harga'   => ($harga->harga_bahan ?? 0) * $kebutuhan_total_matang,
                'id_satuan'     => 22,
                'id_kontrak'     => $harga->id ?? 0,
                'keterangan'     => $keterangan
            ]);
        }

        // suplemen =======================================
        $suplemen_porsi_a = $paket->berat_mentah_suplemen_a;
        $suplemen_porsi_b = $paket->berat_mentah_suplemen_b;
        $bahan = MenuBahan::where('menu_id', $paket->resep_suplemen)->where('status_bahan_baku', 1)->first();
        if($bahan->id_satuan == 1 )
        {
            $kebutuhan_total_matang = $jumlah_siswa_b * $suplemen_porsi_b + $jumlah_siswa_a * $suplemen_porsi_a;
            $kebutuhan_suplemen_a = $jumlah_siswa_a * $suplemen_porsi_a;
            $kebutuhan_suplemen_b = $jumlah_siswa_b * $suplemen_porsi_b;
        }else{
            $kebutuhan_total_matang = $jumlah_siswa_b + $jumlah_siswa_a ;
            $kebutuhan_suplemen_a = $jumlah_siswa_a ;
            $kebutuhan_suplemen_b = $jumlah_siswa_b ;
        }
       
        $kebutuhan_total_matang = $kebutuhan_total_matang + $kebutuhan_total_matang * $buffer / 100;
        
        DB::table('tb_rumus_perhitungan_suplemen')->insert([
            'id_menu' => $request->id_menu,
            'suplemen_porsi_a' => $suplemen_porsi_a,
            'suplemen_porsi_b' => $suplemen_porsi_b,
            'kebutuhan_total_matang' => $kebutuhan_total_matang,
            'kebutuhan_suplemen_a'  => $kebutuhan_suplemen_a,
            'kebutuhan_suplemen_b'      => $kebutuhan_suplemen_b,
            'penyusutan_suplemen_a'     => 0,
            'penyusutan_suplemen_b'     => 0,
            'kebutuhan_matang_a'    => $kebutuhan_suplemen_a,
            'kebutuhan_matang_b'    => $kebutuhan_suplemen_b,
            'kebutuhan_matang_realisasi'    => $kebutuhan_total_matang,
            'kebutuhan_total_mentah'    => $kebutuhan_total_matang,
            'kapasitas_tilting'    => 0,
            'jumlah_masak'    => 1,

            'status' => 1,

        ]);
        $data_rumus_suplemen = DB::table('tb_rumus_perhitungan_suplemen')->where('id_menu', $request->id_menu)->first();
        $data_suplemen = MenuBahan::where('menu_id', $paket->resep_suplemen)->orderByRaw("FIELD(status_bahan_baku, 1, 2, 4, 5, 3)")
            ->get();

        foreach ($data_suplemen as $row) {
            $bahan = TbMasterBahan::find($row->bahan_id);
            $harga = DB::table('tb_rincian_kontrak')->where('id_bahan', $row->bahan_id)
                ->where('status',  1)->orderByDesc('id') // ambil yang terbaru berdasarkan id
                ->first() ?? 0;
            $keterangan = DB::table('tb_spesifikasi_bahan')->where('id_bahan', $row->bahan_id)
                ->first();

            $keterangan = $keterangan->spesifikasi ?? '-';
            if ($row->status_bahan_baku == 1) {
                $data_box = DB::table('tb_box_bahan_baku')->where('id_bahan', $row->bahan_id)->select('isi_per_box')->first();
                if ($data_box) {
                    $jumlah_box = ceil($kebutuhan_total_matang / $data_box->isi_per_box);
                } else {
                    $jumlah_box = $kebutuhan_total_matang / 250;
                }
                if ($bahan->satuan_bahan == 1) {
                    DB::table('tb_rincian_menu_temp')->insert([
                        'id_menu' => $request->id_menu,
                        'id_resep'      => $paket->resep_suplemen,
                        'id_bahan'      => $row->bahan_id,
                        'jumlah'        => $kebutuhan_total_matang / 1000,
                        'bumbu'         => 0,
                        'harga'         => ($harga->harga_bahan ?? 0),
                        'total_harga'   => ($harga->harga_bahan ?? 0) * $kebutuhan_total_matang  / 1000,
                        'jumlah_box'    => $jumlah_box,
                        'id_satuan'     => 25,
                        'id_kontrak'     => $harga->id ?? 0,
                        'keterangan'     => $keterangan

                    ]);
                } else {
                    DB::table('tb_rincian_menu_temp')->insert([
                        'id_menu' => $request->id_menu,
                        'id_resep'      => $paket->resep_suplemen,
                        'id_bahan'      => $row->bahan_id,
                        'jumlah'        => $kebutuhan_total_matang,
                        'bumbu'         => 0,
                        'harga'         => ($harga->harga_bahan ?? 0),
                        'total_harga'   => ($harga->harga_bahan ?? 0) * $kebutuhan_total_matang,
                        'jumlah_box'    => $jumlah_box,
                        'id_satuan'     => $bahan->satuan_bahan,
                        'id_kontrak'     => $harga->id ?? 0,
                        'keterangan'     => $keterangan

                    ]);
                }
            } else {
                $data_perhitungan_bumbu = PerhitunganBumbu::where('id_menu_bahan', $row->id)->first();
                $pengali = $data_perhitungan_bumbu->pengali ?? 10;
                $pembagi = $data_perhitungan_bumbu->pembagi ?? 115;
                $jumlah_kebutuhan = $kebutuhan_total_matang ;
                $jumlah_kebutuhan = $jumlah_kebutuhan / $pembagi;
                $jumlah_kebutuhan = $jumlah_kebutuhan * $pengali;
                $satuan_bahan = $bahan->satuan_bahan ?? 1;
                $jumlah_kebutuhan = $this->bulatkanKebutuhanNonUtama($jumlah_kebutuhan, $satuan_bahan);
                DB::table('tb_rincian_menu_temp')->insert([
                    'id_menu' => $request->id_menu,
                    'id_resep'      => $paket->resep_suplemen,
                    'id_bahan'      => $row->bahan_id,
                    'jumlah'        => $jumlah_kebutuhan ,
                    'bumbu'         => 0,
                    'harga'         => ($harga->harga_bahan ?? 0),
                    'total_harga'   => ($harga->harga_bahan ?? 0) * $jumlah_kebutuhan / 1000,
                    'jumlah_box'    => 1,
                    'id_satuan'     => $bahan->satuan_bahan,
                    'id_kontrak'     => $harga->id ?? 0,
                    'keterangan'     => $keterangan

                ]);
            }
        }
        return redirect()->route('rincian_bahan', ['idmenu' => $request->id_menu])
            ->with('success', 'Lauk berhasil disimpan.');
    }
}
