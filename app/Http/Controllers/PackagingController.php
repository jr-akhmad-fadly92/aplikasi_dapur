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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\TbOmpreng;
use Carbon\Carbon;
use function PHPSTORM_META\type;

class PackagingController extends Controller
{
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
        $ip = request()->ip();
        $segments = explode('.', $ip);
        $id_ip = end($segments);
        $header = "Scan Ompreng Keluar";
        if(isset($request->line)){
            $id_ip = $request->line;
        }
        
        if(strtoupper($request->porsi) == 'A' || strtoupper($request->porsi) == 'B'){

            $porsi = strtoupper($request->porsi);
            
        }
        else{
            $porsi = 'B';
        }
       // $menu = Menu::where('tanggal_kirim', date('Y-m-d'))->first();
        $menu = Menu::where('tanggal_kirim', Carbon::now('Asia/Jakarta')->toDateString())->first();     
        if($menu){
            $total_ompreng_keluar = $this->hitungOmprengKeluar($menu->id, $id_ip);
            $jumlah_kirim = rincian_sekolah::where('id_menu_harian', $menu->id)
            ->sum('jumlah_penerima_total');
            
        }else{
            //$total_ompreng_keluar = $this->hitungOmprengKeluar(0, $id_ip);

            return view('packaging.formPacking', compact('header', 'menu', 'porsi', 'id_ip'));
        }
        //return $menu;
        
        
        return view('packaging.formPacking', compact('header', 'menu', 'porsi', 'total_ompreng_keluar', 'jumlah_kirim', 'id_ip'));
        //return redirect()->back()->with('error', 'Porsi tidak valid');
        
    }

    public function dt_formPacking(Request $request)
    {
        // Optimasi: gunakan join langsung instead of eager loading untuk speed
        $table = tbOmprengTransaksi::select(
                'tb_ompreng_transaksi.kode_ompreng',
                'tb_ompreng_transaksi.porsi',
                'tb_ompreng.nomor as nomor'
            )
            ->join('tb_ompreng', 'tb_ompreng_transaksi.kode_ompreng', '=', 'tb_ompreng.kode_ompreng')
            ->where('tb_ompreng_transaksi.tb_menu_id', $request->menu_id)
            ->where('tb_ompreng_transaksi.porsi', $request->jenis_porsi)
            ->orderBy('tb_ompreng_transaksi.tanggal_keluar', 'desc')
            ->limit(10)
            ->get();
            
        return DataTables::of($table)
            ->addIndexColumn()
            ->editColumn('nomor', function ($row) {
                return $row->nomor ?? '-';
            })
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
        $kodeQR = $request->kodeQR;
        $debugMode = filter_var($request->debug, FILTER_VALIDATE_BOOLEAN);
        $loopCount = max(1, (int) $request->input('loop', 1));
        $lineAlias = $request->input('line', $request->user_id);

        if ($request->isMethod('get') && !$debugMode && empty($kodeQR)) {
            return response()->json([
                'success' => false,
                'message' => 'Endpoint scan ini digunakan untuk POST dari proses scan. Untuk tes browser, tambahkan parameter debug=1 dan kodeQR.',
                'type' => 'error',
                'hint' => 'Contoh: /formPacking/ajax_scanQR?debug=1&loop=100&kodeQR=OP_01_12345&tb_menu_id=425&jenis_porsi=B&line=4',
            ], 200);
        }

        if ($debugMode) {
            $debugBase = [
                'received_kodeQR' => $kodeQR,
                'tb_menu_id' => $request->tb_menu_id,
                'jenis_porsi' => $request->jenis_porsi,
                'user_id' => $request->user_id,
                'line' => $lineAlias,
                'loop' => $loopCount,
                'expected_patterns' => [
                    'OP_(?:01_)?\\d+',
                    'porsi:A or porsi:B',
                    'Petugas:<id>',
                ],
            ];
        }

        if ($debugMode && $loopCount > 1) {
            if (!preg_match('/^OP_(?:01_)?\d+$/', $kodeQR)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mode loop hanya untuk barcode OP',
                    'type' => 'error',
                    'debug' => $debugBase,
                ]);
            }

            $kodeList = $this->generateSequentialOmprengCodes($kodeQR, $loopCount);
            $results = [];
            $lastSuccessPayload = null;

            foreach ($kodeList as $index => $generatedKode) {
                $persistResult = $this->persistOmprengScan(
                    $generatedKode,
                    $request->tb_menu_id,
                    $request->jenis_porsi,
                    $lineAlias
                );

                if ($persistResult['success']) {
                    $lastSuccessPayload = $persistResult;
                }

                $results[] = [
                    'iteration' => $index + 1,
                    'kode_ompreng' => $generatedKode,
                    'success' => $persistResult['success'],
                    'message' => $persistResult['success']
                        ? ('Ompreng ' . $generatedKode . ' berhasil discan')
                        : ($persistResult['message'] ?? 'Gagal scan'),
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Debug loop selesai',
                'type' => 'success',
                'debug' => $debugBase,
                'results' => $results,
                'total_ompreng_keluar' => $lastSuccessPayload['total_ompreng_keluar'] ?? null,
                'jumlah_ompreng_porsi_a' => $lastSuccessPayload['jumlah_ompreng_porsi_a'] ?? null,
                'jumlah_ompreng_porsi_b' => $lastSuccessPayload['jumlah_ompreng_porsi_b'] ?? null,
                'total_ompreng_keluar_id' => $lastSuccessPayload['total_ompreng_keluar_id'] ?? null,
                'row_data' => $lastSuccessPayload['row_data'] ?? null,
            ]);
        }

        if (preg_match('/^OP_(?:01_)?\d+$/', $kodeQR)) {
            $persistResult = $this->persistOmprengScan(
                $kodeQR,
                $request->tb_menu_id,
                $request->jenis_porsi,
                $lineAlias
            );

            if (!$persistResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $persistResult['message'] ?? 'Terjadi kesalahan saat menyimpan data',
                    'type' => 'error',
                ]);
            }

            return response()->json([
                'success' => true,
                'jenis' => 'OP',
                'total_ompreng_keluar' => $persistResult['total_ompreng_keluar'],
                'jumlah_ompreng_porsi_a' => $persistResult['jumlah_ompreng_porsi_a'],
                'jumlah_ompreng_porsi_b' => $persistResult['jumlah_ompreng_porsi_b'],
                'total_ompreng_keluar_id' => $persistResult['total_ompreng_keluar_id'],
                'row_data' => $persistResult['row_data'],
                'type' => 'success',
                'message' => 'Ompreng '.$kodeQR.' berhasil discan',
                'debug' => $debugMode ? array_merge($debugBase, [
                    'matched_rule' => 'OP barcode',
                    'matched_code' => $kodeQR,
                ]) : null,
            ]);
        }  
        else if(strpos($kodeQR, 'porsi:') === 0){
            $porsi = explode(':', $kodeQR);
            if(strtoupper($porsi[1]) == 'A'){
                $url = route('packing.create', ['porsi' => 'A', 'line' => $lineAlias]);
            }
            else if(strtoupper($porsi[1]) == 'B'){
                $url = route('packing.create', ['porsi' => 'B', 'line' => $lineAlias]);
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
                'type' => 'success',
                'debug' => $debugMode ? array_merge($debugBase, [
                    'matched_rule' => 'porsi redirect',
                    'redirect_url' => $url,
                ]) : null,
            ]);
        }
        else if(strpos($kodeQR, 'Petugas:') === 0){
            $petugas = explode(':', $kodeQR);
            if($petugas[1]){
                return response()->json([
                    'success' => true,
                    'message' => 'update_petugas',
                    'user_id' => $petugas[1],
                    'type' => 'success',
                    'debug' => $debugMode ? array_merge($debugBase, [
                        'matched_rule' => 'Petugas update',
                        'petugas_id' => $petugas[1],
                    ]) : null,
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Petugas tidak ditemukan',
                    'type' => 'error',
                    'debug' => $debugMode ? array_merge($debugBase, [
                        'matched_rule' => 'Petugas update',
                        'failure_reason' => 'petugas id kosong',
                    ]) : null,
                ]);
            }
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'Kode ompreng salah / belum terdaftar',
                'type'  => 'error',
                'debug' => $debugMode ? array_merge($debugBase, [
                    'matched_rule' => null,
                    'failure_reason' => 'pattern tidak cocok',
                ]) : null,
            ]);
        }
    }

    private function generateSequentialOmprengCodes(string $baseKode, int $loopCount): array
    {
        if (!preg_match('/^(OP_(?:01_)?)(\d+)$/', $baseKode, $matches)) {
            return [$baseKode];
        }

        $prefix = $matches[1];
        $numberRaw = $matches[2];
        $numberLength = strlen($numberRaw);
        $startNumber = (int) $numberRaw;
        $codes = [];

        for ($i = 1; $i <= $loopCount; $i++) {
            $nextNumber = $startNumber + $i;
            $codes[] = $prefix . str_pad((string) $nextNumber, $numberLength, '0', STR_PAD_LEFT);
        }

        return $codes;
    }

    private function persistOmprengScan(string $kodeOmpreng, $tbMenuId, $jenisPorsi, $userId): array
    {
        $delta = null;

        DB::beginTransaction();
        try {
            $transaksi = tbOmprengTransaksi::where('kode_ompreng', $kodeOmpreng)
                ->where('tb_menu_id', $tbMenuId)
                ->lockForUpdate()
                ->first();

            $isCreated = false;
            $oldPorsi = null;
            $oldUserId = null;

            if (!$transaksi) {
                $transaksi = new tbOmprengTransaksi([
                    'kode_ompreng' => $kodeOmpreng,
                    'tb_menu_id' => $tbMenuId,
                ]);
                $isCreated = true;
            } else {
                $oldPorsi = $transaksi->porsi;
                $oldUserId = (string)$transaksi->user_id;
            }

            $transaksi->porsi = $jenisPorsi;
            $transaksi->user_id = $userId;
            $transaksi->tanggal_keluar = date('Y-m-d H:i:s');
            $transaksi->status = 0;
            $transaksi->save();

            $delta = [
                'is_created' => $isCreated,
                'old_porsi' => $oldPorsi,
                'old_user_id' => $oldUserId,
                'new_porsi' => $jenisPorsi,
                'new_user_id' => (string)$userId,
            ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data',
            ];
        }

        $jumlahOmpreng = $this->hitungOmprengKeluar($tbMenuId, $userId, $delta);

        $rowData = tbOmprengTransaksi::join('tb_ompreng', 'tb_ompreng_transaksi.kode_ompreng', '=', 'tb_ompreng.kode_ompreng')
            ->where('tb_ompreng_transaksi.kode_ompreng', $kodeOmpreng)
            ->where('tb_ompreng_transaksi.tb_menu_id', $tbMenuId)
            ->select(
                'tb_ompreng_transaksi.kode_ompreng',
                'tb_ompreng_transaksi.porsi',
                'tb_ompreng.nomor as nomor'
            )
            ->first();

        return [
            'success' => true,
            'total_ompreng_keluar' => $jumlahOmpreng['total_ompreng_keluar'],
            'jumlah_ompreng_porsi_a' => $jumlahOmpreng['jumlah_ompreng_porsi_a'],
            'jumlah_ompreng_porsi_b' => $jumlahOmpreng['jumlah_ompreng_porsi_b'],
            'total_ompreng_keluar_id' => $jumlahOmpreng['total_ompreng_keluar_id'],
            'row_data' => $rowData,
        ];
    }

    private function hitungOmprengKeluar($id, $user_id, $delta = null)
    {
        $menuKey = 'packaging:counts:menu:' . $id;
        $userKey = 'packaging:counts:menu:' . $id . ':user:' . $user_id;

        if ($delta) {
            $cachedMenuCounts = Cache::get($menuKey);
            if ($cachedMenuCounts) {
                if ($delta['is_created']) {
                    $cachedMenuCounts['total_ompreng_keluar']++;
                }

                if ($delta['is_created']) {
                    if ($delta['new_porsi'] === 'A') {
                        $cachedMenuCounts['jumlah_ompreng_porsi_a']++;
                    } elseif ($delta['new_porsi'] === 'B') {
                        $cachedMenuCounts['jumlah_ompreng_porsi_b']++;
                    }
                } elseif ($delta['old_porsi'] !== $delta['new_porsi']) {
                    if ($delta['old_porsi'] === 'A') {
                        $cachedMenuCounts['jumlah_ompreng_porsi_a']--;
                    } elseif ($delta['old_porsi'] === 'B') {
                        $cachedMenuCounts['jumlah_ompreng_porsi_b']--;
                    }

                    if ($delta['new_porsi'] === 'A') {
                        $cachedMenuCounts['jumlah_ompreng_porsi_a']++;
                    } elseif ($delta['new_porsi'] === 'B') {
                        $cachedMenuCounts['jumlah_ompreng_porsi_b']++;
                    }
                }

                Cache::put($menuKey, $cachedMenuCounts, now()->addMinutes(5));
            }

            if ($delta['is_created']) {
                if (Cache::has($userKey)) {
                    Cache::increment($userKey);
                }
            } elseif ($delta['old_user_id'] !== $delta['new_user_id']) {
                $oldUserKey = 'packaging:counts:menu:' . $id . ':user:' . $delta['old_user_id'];
                $newUserKey = 'packaging:counts:menu:' . $id . ':user:' . $delta['new_user_id'];

                if (Cache::has($oldUserKey)) {
                    $currentOld = (int) Cache::get($oldUserKey, 0);
                    Cache::put($oldUserKey, max(0, $currentOld - 1), now()->addMinutes(5));
                }

                if (Cache::has($newUserKey)) {
                    Cache::increment($newUserKey);
                }
            }
        }

        $counts = Cache::remember($menuKey, now()->addMinutes(5), function () use ($id) {
            $row = tbOmprengTransaksi::where('tb_menu_id', $id)
                ->selectRaw('COUNT(*) as total_ompreng_keluar')
                ->selectRaw('SUM(CASE WHEN porsi = "A" THEN 1 ELSE 0 END) as jumlah_ompreng_porsi_a')
                ->selectRaw('SUM(CASE WHEN porsi = "B" THEN 1 ELSE 0 END) as jumlah_ompreng_porsi_b')
                ->first();

            return [
                'jumlah_ompreng_porsi_a' => (int)$row->jumlah_ompreng_porsi_a,
                'jumlah_ompreng_porsi_b' => (int)$row->jumlah_ompreng_porsi_b,
                'total_ompreng_keluar' => (int)$row->total_ompreng_keluar,
            ];
        });

        $totalOmprengKeluarId = (int) Cache::remember($userKey, now()->addMinutes(5), function () use ($id, $user_id) {
            return tbOmprengTransaksi::where('tb_menu_id', $id)
                ->where('user_id', $user_id)
                ->count();
        });

        return [
            'jumlah_ompreng_porsi_a' => (int)$counts['jumlah_ompreng_porsi_a'],
            'jumlah_ompreng_porsi_b' => (int)$counts['jumlah_ompreng_porsi_b'],
            'total_ompreng_keluar' => (int)$counts['total_ompreng_keluar'],
            'total_ompreng_keluar_id' => $totalOmprengKeluarId
        ];
    }

    public function ajax_keepAlive()
    {
        return response()->json([
            'message' => 'Keep Alive',
        ]);
    }
}
