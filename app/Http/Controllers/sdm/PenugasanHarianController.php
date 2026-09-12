<?php

namespace App\Http\Controllers\sdm;

use App\Http\Controllers\Controller;
use App\Models\RolePenugasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PenugasanHarianController extends Controller
{
    // Generate & simpan penugasan otomatis
    public function generate()
    {
        $tanggal = date('Y-m-d');

        // cek kalau sudah ada penugasan hari ini, hapus dulu biar fresh
        DB::table('tb_penugasan_harian')->where('tanggal', $tanggal)->delete();

        // ambil semua karyawan beserta jam kerja
        $karyawan = DB::table('tb_karyawan_dapur')
            ->join('tb_waktu_kerja', 'tb_karyawan_dapur.id_karyawan', '=', 'tb_waktu_kerja.id_karyawan')
            ->select('tb_karyawan_dapur.*', 'tb_waktu_kerja.jam_kerja')
            ->get();

        $tugas = DB::table('tb_tugas')->get();

        foreach ($karyawan as $k) {
            $totalJam = $k->jam_kerja;
            $durasiPerTugas = $totalJam / 4; // 4 tugas per hari

            // ambil 4 tugas random
            $tugasKaryawan = $tugas->random(4);

            $mulai = strtotime($k->masuk_kerja);

            foreach ($tugasKaryawan as $index => $t) {
                $start = date("H:i:s", $mulai + ($index * $durasiPerTugas * 3600));
                $end   = date("H:i:s", $mulai + (($index + 1) * $durasiPerTugas * 3600));

                // simpan ke tabel
                DB::table('tb_penugasan_harian')->insert([
                    'id_karyawan' => $k->id_karyawan,
                    'id_tugas' => $t->id_tugas,
                    'waktu_mulai' => $start,
                    'waktu_selesai' => $end,
                    'tanggal' => $tanggal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('penugasan.harian')->with('success', 'Penugasan harian berhasil digenerate!');
    }

    // Menampilkan penugasan harian
    public function index()
    {
        $tanggal = date('Y-m-d');

        $penugasan = DB::table('tb_penugasan_harian')
            ->join('tb_karyawan_dapur', 'tb_penugasan_harian.id_karyawan', '=', 'tb_karyawan_dapur.id_karyawan')
            ->join('tb_tugas', 'tb_penugasan_harian.id_tugas', '=', 'tb_tugas.id_tugas')
            ->where('tb_penugasan_harian.tanggal', $tanggal)
            ->select(
                'tb_penugasan_harian.*',
                'tb_karyawan_dapur.nama_karyawan',
                'tb_karyawan_dapur.masuk_kerja',
                'tb_karyawan_dapur.keluar_kerja',
                'tb_tugas.kegiatan'
            )
            ->orderBy('tb_penugasan_harian.id_karyawan')
            ->orderBy('tb_penugasan_harian.waktu_mulai')
            ->get();

        // return view('office.sdm.penugasan.harian', compact('penugasan', 'tanggal'));
        return view('office.sdm.penugasan.penugasan', compact('penugasan', 'tanggal'));
    }

    public function hasil()
    {
        $tanggal = date('Y-m-d');

        $penugasan = DB::table('tb_penugasan_harian')
                ->join('tb_karyawan_dapur', 'tb_penugasan_harian.id_karyawan', '=', 'tb_karyawan_dapur.id_karyawan')
                ->join('tb_tugas', 'tb_penugasan_harian.id_tugas', '=', 'tb_tugas.id_tugas')
                ->where('tb_penugasan_harian.tanggal', $tanggal)
                ->select(
                        'tb_penugasan_harian.*',
                'tb_karyawan_dapur.nama_karyawan',
            'tb_karyawan_dapur.masuk_kerja',
            'tb_karyawan_dapur.keluar_kerja',
            'tb_tugas.kegiatan'
        )
        ->orderBy('tb_penugasan_harian.id_karyawan')
        ->orderBy('tb_penugasan_harian.waktu_mulai')
        ->get();

    return view('office.sdm.penugasan.hasil', compact('penugasan', 'tanggal'));
}

    public function dt_role_penugasan(Request $request)
    {
        if ($request->ajax()) {
            
            $data = RolePenugasan::select(['id', 'id_penugasan', 'tanggal', 'tanggal_selesai']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-primary">Edit</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

}

