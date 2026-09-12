<?php

namespace App\Http\Controllers;

use App\Models\DetailKbm;
use App\Models\HariLibur;
use App\Models\KbmSekolah;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Support\Facades\DB; // Tambahkan ini


use Illuminate\Http\Request;

class KbmController extends Controller
{
    public function index()
    {
        $header = "KBM Sekolah";
        $kbm = KbmSekolah::first();
        return view('yayasan.kbm.index', compact('header','kbm'));
    }

    public function data(Request $request)
    {
        $kbm = KbmSekolah::select(['id', 'tahun_ajaran', 'semester', 'tanggal_mulai_kbm', 'tanggal_selesai_kbm']);

        return DataTables::of($kbm)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('kbm.edit', $row->id) . '" class="btn btn-sm btn-warning">Edit</a>
                    <button data-id="' . $row->id . '" class="btn btn-sm btn-danger btnDelete">Hapus</button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string',
            'semester' => 'required|in:ganjil,genap',
            'tanggal_mulai_kbm' => 'required|date',
            'tanggal_selesai_kbm' => 'required|date',
        ]);

        try {
            DB::table('tb_detail_kbm')->delete();
            DB::table('tb_kbm_sekolah')->delete();
          
            $kbm = KbmSekolah::create($request->only(['tahun_ajaran', 'semester', 'tanggal_mulai_kbm', 'tanggal_selesai_kbm']));

            $data = KbmSekolah::first();
            $start = Carbon::parse($data->tanggal_mulai_kbm);
            $end   = Carbon::parse($data->tanggal_selesai_kbm);
            $period = \Carbon\CarbonPeriod::create($data->tanggal_mulai_kbm, $data->tanggal_selesai_kbm);
            $start = \Carbon\Carbon::parse($data->tanggal_mulai_kbm);
            $end   = \Carbon\Carbon::parse($data->tanggal_selesai_kbm);

            $days = $end->diffInDays($start);

            $current = Carbon::parse($data->tanggal_mulai_kbm);

            // Tentukan Senin pertama KBM
            $firstMonday = $start->copy();
            if ($firstMonday->dayOfWeek !== Carbon::MONDAY) {
                $firstMonday->next(Carbon::MONDAY);
            }


            $po = 0;
            $po_sebelum = 0;
            $pengiriman = 0;
            $pengiriman_sebelumnya = 0;
            $pembayaran = 0;
            $pembayaran_sebelumnya = 0;
            $foundFirstKbm = false;

            while ($current->lte($end)) {
                $status = 'masuk';
                $keterangan = 'Aktif';
                $hari = $current->locale('id')->dayName;
                // ✅ $current adalah Carbon, jadi bisa isWeekend()
                if ($hari == 'Sabtu' || $hari == 'Minggu') {
                    $status = 'libur';
                    $keterangan = 'Libur Akhir Pekan';
                }

                $besok = $current->copy()->addDay()->toDateString();
                $hariLiburBesok = HariLibur::whereDate('tanggal', $besok)->first();

                if ($hariLiburBesok) {
                    // Besok libur
                    $statusBesok = 'libur';
                    $ketBesok = $hariLiburBesok->keterangan;
                } else {
                    // Besok masuk
                    $statusBesok = 'masuk';
                    $ketBesok = 'Aktif';
                }

                // cek libur nasional
                $hariLibur = HariLibur::whereDate('tanggal', $current->toDateString())->first();
                if ($hariLibur) {
                    $status = 'libur';
                    $keterangan = $hariLibur->keterangan;
                }


                // Hitung minggu
                if ($current->lt($firstMonday)) {
                    $minggu = 1; // semua sebelum Senin pertama dianggap minggu 1
                } else {
                    $hariKe = $firstMonday->diffInDays($current);
                    $minggu = (int)floor($hariKe / 7) + 1;
                }
                // === Hitung PO ===
                if ($current->dayOfWeek == 1) {
                    $po = $po_sebelum;
                    $po = $po + 1;
                    $po_sebelum = $po;
                }else{
                    $po = 0;
                }
                // Pengiriman dimulai Senin sebelum KBM
                /*if ($po_sebelum != 1 && $status == 'masuk') {
                    
                        $pengiriman = $pengiriman_sebelumnya;
                        $pengiriman = $pengiriman + 1;
                        $pengiriman_sebelumnya = $pengiriman;

                        $pembayaran = $pembayaran_sebelumnya;
                        $pembayaran = $pembayaran + 1;
                        $pembayaran_sebelumnya = $pembayaran;
                    
                } else {
                    $pengiriman = 0;
                    $pembayaran = 0;
                }*/
                if ($current->dayOfWeek == 0 || $current->dayOfWeek == 1 || $current->dayOfWeek == 2 || $current->dayOfWeek == 3 || $current->dayOfWeek == 4 || $current->dayOfWeek == 5) {

                   
                    if($po_sebelum > 1 && $status == 'masuk')
                    {
                        $pembayaran = $pembayaran_sebelumnya;
                        $pembayaran = $pembayaran + 1;
                        $pembayaran_sebelumnya = $pembayaran;
                    }
                    if($status == 'libur')
                    {

                    }
                } else {
                    if($current->dayOfWeek == 0 )
                    {
                        $pengiriman = 1;
                        $pembayaran = 0;    
                    }
                }   

                if($current->dayOfWeek == 0 || $current->dayOfWeek == 1 || $current->dayOfWeek == 2 || $current->dayOfWeek == 3 || $current->dayOfWeek == 4 )
                {
                   
                    if($po_sebelum > 1 && $status == 'masuk' && $current->dayOfWeek != 5)
                    {
                        
                        $pengiriman = $pengiriman_sebelumnya;
                        $pengiriman = $pengiriman + 1;
                        $pengiriman_sebelumnya = $pengiriman;
                    }

                    if ($current->dayOfWeek == 0) {

                        $pengiriman = $pengiriman_sebelumnya;
                        $pengiriman = $pengiriman + 1;
                        $pengiriman_sebelumnya = $pengiriman;
                    }

                   
                    
                }
                if ($current->dayOfWeek == 5 || $current->dayOfWeek == 6) {
                    $pengiriman = 0;
                }
                if($status == 'libur')
                {
                    $pembayaran = 0;
                }
                if ($current->dayOfWeek == 1 || $current->dayOfWeek == 2 || $current->dayOfWeek == 3 || $current->dayOfWeek == 4 || $current->dayOfWeek == 5 ) { 
                        if ($status == 'libur') {
                            $pengiriman_sebelumnya = $pengiriman_sebelumnya - 1;
                        }
                    }
                    DetailKbm::updateOrCreate(
                    [
                        'id_kbm' => $data->id,
                        'tanggal' => $current->toDateString()
                    ],
                    [
                        'keterangan' => $current->dayOfWeek,
                        'status' => $status,
                        'minggu' => $minggu,
                        'po' => $po,
                        'pengiriman' => $pengiriman,
                        'pembayaran' => $pembayaran
                    ]
                );

                $current->addDay(); // maju ke hari berikutnya
            }

            /*$days = $end->diffInDays($start);

            for ($i = 0; $i <= $days; $i++) {
                $date = $start->copy()->addDays($i);
               
                $status = 1;
                $keterangan = 'Masuk';
                
                DetailKbm::create([
                    'id_kbm' => $kbm->id,
                    'tanggal' => $date->format('Y-m-d'),
                    'keterangan' => $keterangan,
                    'status' => $status
                ]);
            }*/
            return response()->json(['message' => 'KBM berhasil dibuat', 'data' => $kbm]);
        } catch (\Exception $e) {
           // \Log::error('KBM store error: ' . $e->getMessage() . ' -- ' . $e->getTraceAsString());
            return response()->json(['message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }


    public function dt_tanggal_kbm()
    {
        $header = "Dashboard";
        return view('Home', compact('header'));
    }

    public function dt_detail_kbm()
    {
       
        $table = DB::table('tb_detail_kbm')->get();

        return DataTables::of($table)
            ->addIndexColumn()
            ->addColumn('minggu_ke', function ($row) {
               return  'minggu ke '.$row->minggu;
            })
            ->addColumn('Hari_libur', function ($row) {
                $data = DB::table('tb_hari_libur')->where('tanggal',$row->tanggal)->first();
                if($data)
                {
                    return $data->keterangan;
                }else{
                    return $row->status;
                }
            })
            ->addColumn('tanggal_kbm', function ($row) {
                
                Carbon::setLocale('id'); // untuk bahasa Indonesia
                return \Carbon\Carbon::parse($row->tanggal)->translatedFormat('l, d F Y');
            })
            ->rawColumns(['jumlah_satuan'])
            ->make(true);
    
    }

    public function detail_kbm()
    {
        $header = "Dashboard";
        $kbm = KbmSekolah::first();
        $query = "
            WITH minggu_fix AS (
                SELECT
                    DATE_SUB(tanggal, INTERVAL WEEKDAY(tanggal) DAY) AS senin_minggu,
                    DATE_ADD(DATE_SUB(tanggal, INTERVAL WEEKDAY(tanggal) DAY), INTERVAL 6 DAY) AS minggu_minggu,
                    MIN(minggu) AS minggu_ke,
                    SUM(CASE WHEN status = 'kbm' THEN 1 ELSE 0 END) AS total_kbm,
                    SUM(CASE WHEN status = 'libur' THEN 1 ELSE 0 END) AS total_libur,
                    SUM(
                        CASE 
                            WHEN status = 'libur' AND DAYOFWEEK(tanggal) NOT IN (1,7) THEN 1 
                            ELSE 0 
                        END
                    ) AS total_libur_non_weekend,
                    SUM(CASE WHEN status = 'cuti' THEN 1 ELSE 0 END) AS total_cuti,
                    SUM(CASE WHEN status = 'semester' THEN 1 ELSE 0 END) AS total_semester,
                    COUNT(CASE WHEN po != 0 THEN 1 END) AS count_po,
                    COUNT(DISTINCT CASE WHEN pengiriman != 0 THEN pengiriman END) AS count_pengiriman,
                    COUNT(CASE WHEN pembayaran != 0 THEN 1 END) AS count_pembayaran
                FROM tb_detail_kbm
                
                GROUP BY 
                    DATE_SUB(tanggal, INTERVAL WEEKDAY(tanggal) DAY),
                    DATE_ADD(DATE_SUB(tanggal, INTERVAL WEEKDAY(tanggal) DAY), INTERVAL 6 DAY)
            )
            SELECT
                DATE_FORMAT(senin_minggu, '%Y-%m') AS kelompok_bulan,
                MIN(minggu_ke) AS minggu_awal,
                MAX(minggu_ke) AS minggu_akhir,
                MIN(senin_minggu) AS tanggal_awal,
                MAX(minggu_minggu) AS tanggal_akhir,
                SUM(total_kbm) AS total_kbm,
                SUM(total_libur) AS total_libur,
                SUM(total_libur_non_weekend) AS total_libur_non_weekend,
                SUM(total_cuti) AS total_cuti,
                SUM(total_semester) AS total_semester,
                SUM(count_po) AS total_count_po,
                SUM(count_pengiriman) AS total_count_pengiriman,
                SUM(count_pembayaran) AS total_count_pembayaran
            FROM minggu_fix
            GROUP BY DATE_FORMAT(senin_minggu, '%Y-%m')
            ORDER BY kelompok_bulan

        ";

        $data = DB::select($query);
        return view('yayasan.kbm.detail_kbm', compact('header', 'kbm','data'));
    }

    public function index_libur()
    {
        $header = "Master Libur";
        $libur = HariLibur::all();
        return view('yayasan.kbm.master_libur', compact('header', 'libur'));
    }

    public function dt_master_libur()
    {

        $table = DB::table('tb_hari_libur')->get();

        return DataTables::of($table)
            ->addIndexColumn()
            ->addColumn('keterangan_libur', function ($row) {
               return $row->keterangan;
            })
            ->addColumn('tanggal_libur', function ($row) {

                Carbon::setLocale('id'); // untuk bahasa Indonesia
                return \Carbon\Carbon::parse($row->tanggal)->translatedFormat('l, d F Y');
            })
            ->addColumn('action', function ($row) {
                return '
                    
                    <button data-id="' . $row->id . '" class="btn btn-sm btn-danger btnDelete">Hapus</button>
                ';
            })
            ->rawColumns(['tanggal_libur', 'keterangan_libur', 'action'])
            ->make(true);
    }


    public function store_master_libur(Request $request)
    {
        $request->validate([
            'keterangan'    => 'required|string',
            'tanggal'       => 'required|date',
            'jenis'         => 'required|string',
        ]);

        try {
            $cek = HariLibur::where('tanggal',$request->tanggal)->count();
            if($cek == 0)
            {
                HariLibur::insert([
                    'tanggal'       => $request->tanggal,
                    'keterangan'    => $request->keterangan,
                    'jenis'         => $request->jenis,
                    'created_at'    => Carbon::now('Asia/Jakarta'),
                ]);
                $data = HariLibur::all();
                return response()->json(['message' => 'Data Libur berhasil dibuat', 'data' => $data]);
            }else{
                return response()->json(['message' => 'Data Sudah ada']);
            }
            
           
            
        } catch (\Exception $e) {
            // \Log::error('KBM store error: ' . $e->getMessage() . ' -- ' . $e->getTraceAsString());
            return response()->json(['message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy_libur($id)
    {
        $data = HariLibur::findOrFail($id);
        $data->delete();

        return response()->json(['success' => 'Data berhasil dihapus']);
    }

    

    public function pdf_cetakKalender()
    {
        $html = view('yayasan.kbm.pdf_kalender_penerimaan', [])->render();
        $pdf = Pdf::loadHTML($html);
        return $pdf->download('kalender_april_2025.pdf');
    }
}
