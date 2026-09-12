<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpParser\Node\Expr\FuncCall;
use DataTables;
use App\Models\tbOmprengTransaksi;
use App\Models\Menu;
use App\Models\rincian_sekolah;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\TbOmpreng;

use function PHPSTORM_META\type;

class PackagingController extends Controller
{
    //
    public function index_dashboard()
    {
        $header = "Dashboard Packaging";
        return view('packaging.index_dashboard', compact('header'));
    }
    
    public function index_packing()
    {
        $header = "Dashboard Packaging";
        return view('packaging.index_packing', compact('header'));
    }

    public function index_riwayat_packaging()
    {
        $header = "Dashboard Packaging";
        return view('packaging.index_riwayat_packaging', compact('header'));
    }
//--------------------------------------------------------------------------------
    public function v_formPacking(Request $request)
    {

        if(strtoupper($request->porsi) == 'A' || strtoupper($request->porsi) == 'B'){
            $header = "Scan Ompreng Keluar";

            $porsi = $request->porsi;
            $menu = Menu::where('tanggal_kirim', date('Y-m-d'))->first();
            
            if($menu){
                $total_ompreng_keluar = $this->hitungOmprengKeluar($menu->id);
                $jumlah_kirim = rincian_sekolah::where('id_menu_harian', $menu->id)
                ->first();
            }else{
                return view('packaging.formPacking', compact('header', 'menu', 'porsi'));
            }
            //return $menu;
            
            
            return view('packaging.formPacking', compact('header', 'menu', 'porsi', 'total_ompreng_keluar', 'jumlah_kirim'));
        }
        //return redirect()->back()->with('error', 'Porsi tidak valid');
        
    }

    public function dt_formPacking(Request $request)
    {
        /*$table = tbOmprengTransaksi::select('kode_rantang', tbOmprengTransaksi::raw('COUNT(kode_ompreng) as jumlah_ompreng'))
            ->where('tb_menu_id', $request->menu_id)
            ->groupBy('kode_rantang')
            ->get();*/
        $table = tbOmprengTransaksi::where('tb_menu_id', $request->menu_id)
            ->where('porsi', $request->jenis_porsi)
            ->orderBy('tanggal_keluar', 'desc')
            ->get();
        return DataTables::of($table)
            ->addIndexColumn()
            ->addColumn('action', function ($table) {
                return '<a href="#" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    private function hitungRantang($kode_ompreng, $tb_menu_id)
    {
        $jumlah_ompreng = tbOmprengTransaksi::where('kode_rantang', $kode_ompreng)
                ->where('tb_menu_id', $tb_menu_id)
                ->count();
        $total_ompreng = tbOmprengTransaksi::where('tb_menu_id', $tb_menu_id)
        ->count();
        return [
            'jumlah_ompreng' => $jumlah_ompreng,
            'total_ompreng' => $total_ompreng
        ];
    }

    public function ajax_scanQR(Request $request)
    {
        //return $request;
        $kodeQR = $request->kodeQR;
        //return $kodeQR;
        if (strpos($kodeQR, 'OP_') === 0) {
            //$kode_rantang = $request->kode_rantang;
            $jenis_porsi = $request->jenis_porsi;
            $kode_ompreng = $request->kodeQR;
            $tb_menu_id = $request->tb_menu_id;
            $user_id = $request->user_id;
            //$user_id = 0;//diganti operator//Auth::user()->id;
            //return $request;
            
            /*if($jumlah_ompreng['jumlah_ompreng'] >= 10){
                return response()->json([
                    'success' => false,
                    'message' => 'Rantang sudah penuh',
                    'jenis' => 'RT',
                    'jumlah_ompreng' => $jumlah_ompreng['jumlah_ompreng'],
                    'total_ompreng' => $jumlah_ompreng['total_ompreng'],
                    'kode_rantang' => $kodeQR,
                    'type' => 'warning'
                ]);
            }*/
            $existingOmpreng = tbOmprengTransaksi::where('kode_ompreng', $kode_ompreng)
                ->where('tb_menu_id', $tb_menu_id)
                ->count();

            $statusOmpreng = TbOmpreng::where('kode_ompreng', $kode_ompreng)
                //->where('status', 'Diluar')
                ->count();
                //return $statusOmpreng;

            if ($statusOmpreng<1) {
                //apakah ompreng sudah terdaftar
                TbOmpreng::updateOrCreate(
                    [
                        'kode_ompreng' => $kode_ompreng,
                        
                    ],
                    [
                        'jenis' => "Ompreng",
                        'status' => "Diluar",
                        'keterangan' => '',
                    ]
                );
                /*return response()->json([
                    'success' => false,
                    'message' => 'Kode ompreng belum didaftarkan',
                    'type' => 'error'
                ]);*/
            }
            /*if ($existingOmpreng>0) {
                
                return response()->json([
                    'success' => false,
                    'message' => 'Ompreng masih diluar/ sudah discan',
                    'type' => 'error'
                ]);
            }*/

            tbOmprengTransaksi::updateOrCreate(
                [

                'kode_ompreng' => $kode_ompreng,
                ],[
                'porsi' => $jenis_porsi,
                'tb_menu_id' => $tb_menu_id,
                'user_id' => $user_id,
                'tanggal_keluar' => date('Y-m-d H:i:s'),
                'status' => 0
            ]);

            TbOmpreng::where('kode_ompreng', $kode_ompreng)->update([
                'status' => 'Diluar'
            ]);
            $jumlah_ompreng = $this->hitungOmprengKeluar($tb_menu_id);
            
                return response()->json([
                    'success' => true,
                    'jenis' => 'RT',
                    //'jumlah_ompreng' => $jumlah_ompreng['jumlah_ompreng']+1,
                    'total_ompreng_keluar' => $jumlah_ompreng['total_ompreng_keluar'],
                    'jumlah_ompreng_porsi_a' => $jumlah_ompreng['jumlah_ompreng_porsi_a'],
                    'jumlah_ompreng_porsi_b' => $jumlah_ompreng['jumlah_ompreng_porsi_b'],
                    //'kode_rantang' => $kode_rantang,
                    'type' => 'success',
                    'message' => 'Ompreng '.$kode_ompreng.' berhasil discan'
                ]);
        } 
        else if(strpos($kodeQR, 'porsi:') === 0){
            $porsi = explode(':', $kodeQR);
            if(strtoupper($porsi[1]) == 'A'){
                $url = route('packing.create', ['porsi' => 'A']);
            }
            else if(strtoupper($porsi[1]) == 'B'){
                $url = route('packing.create', ['porsi' => 'B']);
            }
            else{
                return response()->json([
                    'success' => false,
                    'message' => 'Porsi tidak valid',
                    'type' => 'error'
                ]);
            }
            return response()->json([
                'success' => true,
                'message' => 'redirect',
                'url' => $url,
                'type' => 'success'
            ]);
        }
        else if(strpos($kodeQR, 'Petugas:') === 0){
            $petugas = explode(':', $kodeQR);
            if($petugas[1]){
                return response()->json([
                    'success' => true,
                    'message' => 'update_petugas',
                    'user_id' => $petugas[1],
                    'type' => 'success'
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Petugas tidak ditemukan',
                    'type' => 'error'
                ]);
            }
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'Kode ompreng salah / belum terdaftar',
                'type'  => 'error'
            ]);
        }
    }

    private function hitungOmprengKeluar($id)
    {
        $total_ompreng_keluar = tbOmprengTransaksi::where('tb_menu_id', $id)
            ->count();
        $jumlah_ompreng_porsi_A = tbOmprengTransaksi::where('tb_menu_id', $id)
            ->where('porsi', 'A')
            ->count();

        $jumlah_ompreng_porsi_B = tbOmprengTransaksi::where('tb_menu_id', $id)
            ->where('porsi', 'B')
            ->count();

        return [
            'jumlah_ompreng_porsi_a' => $jumlah_ompreng_porsi_A,
            'jumlah_ompreng_porsi_b' => $jumlah_ompreng_porsi_B,
            'total_ompreng_keluar' => $total_ompreng_keluar
        ];
    }
}
