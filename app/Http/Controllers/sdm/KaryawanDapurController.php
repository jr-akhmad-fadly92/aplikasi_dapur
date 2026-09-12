<?php

namespace App\Http\Controllers\sdm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KaryawanDapurController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan daftar semua data karyawan dalam bentuk tabel.
     */
    public function index()
    {
        $karyawan = DB::table('tb_karyawan_dapur')->get();

        return view('office.sdm.karyawandapur', compact('karyawan'));
    }

    public function create()
    {
        return view('office.sdm.karyawancreate');
    }

    /**
     * Menyimpan data karyawan yang baru dibuat ke database.
     */
    public function store(Request $request)
    {
        // Validasi data
        $validatedData = $request->validate([
            'nik' => 'required|string|max:255|unique:tb_karyawan_dapur,nik',
            'nama_karyawan' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
            'status_karyawan' => 'nullable|string|max:50',
            'masuk_kerja' => 'required|date',
        ]);
        
        // Logika pembuatan ID karyawan otomatis
        $masuk_kerja = Carbon::parse($request->masuk_kerja);
        $tahun = $masuk_kerja->format('Y');
        $bulan = $masuk_kerja->format('m');

        // 1. Ambil nomor dapur dari tabel tb_data_dapur
        $dataDapur = DB::table('tb_data_dapur')->first();
        $nomorDapur = '';
        if ($dataDapur) {
            $nomorDapur = $dataDapur->nomor_dapur;
        }

        // 2. Hitung jumlah karyawan untuk bulan dan tahun ini  
        $jumlahKaryawanBulanIni = DB::table('tb_karyawan_dapur')
                               ->whereYear('masuk_kerja', $tahun)
                               ->whereMonth('masuk_kerja', $bulan)
                               ->count();

        // 3. Buat nomor urut
        $nomorUrut = str_pad($jumlahKaryawanBulanIni + 1, 3, '0', STR_PAD_LEFT);

        // 4. Gabungkan semua komponen untuk membuat ID karyawan
        $idKaryawan = 'KY' . $nomorDapur . $tahun . $bulan . $nomorUrut;
        
        // Simpan data ke database
        DB::table('tb_karyawan_dapur')->insert([
            'nik' => $validatedData['nik'],
            'nama_karyawan' => $validatedData['nama_karyawan'],
            'alamat' => $validatedData['alamat'],
            'no_hp' => $validatedData['no_hp'],
            'status_karyawan' => $request->input('status_karyawan'),
            'masuk_kerja' => $validatedData['masuk_kerja'],
            'id_karyawan' => $idKaryawan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('karyawan-dapur.index')->with('success', 'Data karyawan berhasil ditambahkan.');
    }
    
    // --- TAMBAHAN BARU ---
    
    /**
     * Menampilkan formulir untuk mengedit data karyawan.
     */
    public function edit($id)
    {
        // Mengambil data karyawan berdasarkan id_karyawan
        $karyawan = DB::table('tb_karyawan_dapur')->where('id_karyawan', $id)->first();
        
        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        return view('office.sdm.karyawanedit', compact('karyawan'));
    }

    /**
     * Menyimpan data yang sudah diperbarui ke database.
     */
    public function update(Request $request, $id)
    {
        // Validasi input tanggal keluar
        $request->validate([
            'keluar_kerja' => 'nullable|date',
        ]);
        
        // Lakukan update data di database
        DB::table('tb_karyawan_dapur')
            ->where('id_karyawan', $id)
            ->update([
                'keluar_kerja' => $request->keluar_kerja,
                'updated_at' => now(),
            ]);

        return redirect()->route('karyawan-dapur.index')->with('success', 'Tanggal keluar karyawan berhasil diperbarui.');
    }
}