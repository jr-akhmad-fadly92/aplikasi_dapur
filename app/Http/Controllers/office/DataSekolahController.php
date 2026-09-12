<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\DataSekolah;
use App\Models\TingkatanSekolah;
use App\Models\DataDapur;
use App\Models\dataSiswa;
use Yajra\DataTables\Facades\DataTables;
use App\Models\sekolahAktif;
use App\Models\sekolahDapodik;

class DataSekolahController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();

        return view('office/datasekolah.index', ['header' => 'Master Sekolah']);
    } 

    public function v_formDataSekolah(Request $request)
    {
        if(isset($request->id)){
            $sekolah = DataSekolah::findOrFail($request->id);
            
            return view('office/datasekolah.create', ['header' => 'Edit Sekolah'], compact('sekolah'));
        }
        

        return view('office/datasekolah.create', ['header' => 'Tambah Sekolah']);
    }

    public function ajax_simpanDataSekolah(Request $request)
    {
        // Validasi input
        
        $rule = [
            'nama_sekolah' => 'required',
            'jenjang_sekolah' => 'required',
            'alamat_sekolah' => 'required',
        ];
        $message = [
            'nama_sekolah.required' => 'Nama sekolah wajib diisi',
            'jenjang_sekolah.required' => 'Jenjang sekolah wajib diisi',
            'alamat_sekolah.required' => 'Alamat sekolah wajib diisi',
        ];
        $this->validate($request, $rule, $message);
        
        // Simpan ke database
        if(isset($request->id)){
            if($request->id != ''){
                $dataSekolah = DataSekolah::findOrFail($request->id);
                $dataSekolah->update([
                    'nama_sekolah'      => $request->nama_sekolah,
                    'jenjang_sekolah'   => $request->jenjang_sekolah,
                    'alamat_sekolah'    => $request->alamat_sekolah,
                    'npsn'              => $request->npsn,
                ]);
                return response()->json(['status' => 'success', 'message' => 'Data sekolah berhasil diubah', 'id' => $request->id]);
            }
        }
        $dataSekolah = DataSekolah::create([
            'nama_sekolah'      => $request->nama_sekolah,
            'jenjang_sekolah'   => $request->jenjang_sekolah,
            'alamat_sekolah'    => nl2br($request->alamat_sekolah),
            'npsn'              => $request->npsn,
        ]);

        $id = $dataSekolah->id;

        return response()->json(['status' => 'success', 'message' => 'Data sekolah berhasil ditambahkan', 'id' => $id]);
    }

    public function dt_dataSekolah()
    {
        $data = DataSekolah::orderBy('jumlah_siswa', 'desc')->where('status_aktif', 1)->get();

        //return $data;
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                /*$btn = '<a href="' . route('datasekolah.edit', $row['id']) . '" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Det</a>
                        <a href=""
                        class="edit btn btn-danger btn-sm delete-button" 
                        data-id="' . $row->id . '">Delete</a>';
                        */
                $btn = '<a href="detailSekolah-' . $row['id'] . '" class="btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Detail</a>';

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    
    public function deleteDataSekolah(Request $request)
    {
        $dataSekolah = DataSekolah::findOrFail($request->id);
        $dataSekolah->delete();
        return response()->json(['status' => 'success', 'message' => 'Data sekolah berhasil dihapus']);
    }

    public function v_detailSekolah(Request $request)
    {
        $sekolah = DataSekolah::findOrFail($request->id);
        $hari_aktif = sekolahAktif::where('id_tb_data_sekolah', $request->id)
            ->where('status', 1)
            ->first();
        //return $sekolah;
        return view('office/datasekolah.detailSekolah', ['header' => $sekolah->nama_sekolah], compact('sekolah', 'hari_aktif'));
    }

    public function dt_dataSiswa(Request $request)
    {
        if(isset($request->id)){
            $table = dataSiswa::where('id_tb_data_sekolah', $request->id)
                ->orderBy('created_at', 'desc')
                ->get();
            return DataTables::of($table)
                ->addIndexColumn()
                ->make(true);
        }
        else{
        $table = dataSiswa::get();
            
        }
        return DataTables::of($table)
            ->addIndexColumn()
            
            ->make(true);
    }

    public function v_formDataSiswa(Request $request)
    {
        if(isset($request->id)){
            $sekolah = DataSekolah::findOrFail($request->id);
            
            return view('office/datasekolah.form.formDataSiswa', ['header' => 'Edit Sekolah'], compact('sekolah'));
        }
        

        return view('office/datasekolah.form.formDataSiswa', ['header' => 'Tambah Sekolah']);
    }

    public function ajax_simpanSiswa(Request $request)
    {
        //validasi input
        $rules = [
            'tahun_ajaran' => 'required',
            
            'semester' => 'required',
            'id_sekolah' => 'required',
        ];
        $message = [
            'tahun_ajaran.required' => 'Tahun ajaran wajib diisi',
            
            'semester.required' => 'Semester wajib diisi',
            'id_sekolah.required' => 'ID sekolah wajib diisi',
        ];
        $this->validate($request, $rules, $message);
        $dataSiswa = dataSiswa::create([
            'tahun_ajaran' => $request->tahun_ajaran,
            'jumlah_a' => $request->jumlah_a ?? 0,
            'jumlah_b' => $request->jumlah_b ?? 0,
            'semester' => $request->semester,
            'id_tb_data_sekolah' => $request->id_sekolah,
        ]);
        dataSiswa::where('id_tb_data_sekolah', $request->id_sekolah)
            ->where('id', '!=', $dataSiswa->id)
            ->update(['status' => 0]);
        $dataSekolah = DataSekolah::findOrFail($request->id_sekolah);
        $dataSekolah->update([
            'jumlah_siswa' => $request->jumlah_a + $request->jumlah_b,
            'jumlah_a' => $request->jumlah_a,
            'jumlah_b' => $request->jumlah_b,
        ]);
        return response()->json(['status' => 'success', 'message' => 'Data siswa berhasil ditambahkan']);
    }

    public function v_formHariAktifSekolah(Request $request)
    {   
        $sekolah_id = $request->id;
        if(isset($request->id)){
            $sekolah_aktif = sekolahAktif::where('id', $request->id)->first();
            
            return view('office.datasekolah.form.formHariAktifSekolah', compact('sekolah_aktif', 'sekolah_id'));
        }
        return view('office.datasekolah.form.formHariAktifSekolah', compact('sekolah_id'));
    }

    public function ajax_simpanHariAktifSekolah(Request $request)
    {
        //return $request;
        if(isset($request->id_sekolah)){
            $sekolah_aktif = sekolahAktif::updateOrCreate(
                [
                    'id_tb_data_sekolah' => $request->id_sekolah
                ],
                [
                    'senin' => $request->senin ?? 0,
                    'selasa' => $request->selasa ?? 0,
                    'rabu' => $request->rabu ?? 0,
                    'kamis' => $request->kamis ?? 0,
                    'jumat' => $request->jumat ?? 0,
                    'sabtu' => $request->sabtu ?? 0,
                    'minggu' => $request->minggu ?? 0,
                    'status' => 1
    
                ]
            );
            $hari =[
                'senin' => $this->masukLibur($request->senin ?? 0),
                'selasa' => $this->masukLibur($request->selasa ?? 0),
                'rabu' => $this->masukLibur($request->rabu ?? 0),
                'kamis' => $this->masukLibur($request->kamis ?? 0),
                'jumat' => $this->masukLibur($request->jumat ?? 0),
                'sabtu' => $this->masukLibur($request->sabtu ?? 0),
                'minggu' => $this->masukLibur($request->minggu ?? 0),
            ];
            return response()->json(['type' => 'success', 
                'message' => 'Data sekolah berhasil diupdate',
                'hari' => $hari
            ]);

        }
        
    }

    private function masukLibur($hari)
    {
        if($hari != 0){
            return "Masuk";
        }
        return "Libur";
    }

    public function autoComplete(Request $request)
    {
        $search = $request->get('query');

        $results = sekolahDapodik::where('npsn', 'LIKE', "%{$search}%")
            ->orWhere('sekolah', 'LIKE', "%{$search}%")
            ->select('npsn', 'sekolah')
            ->limit(10)
            ->get();

        return response()->json($results);
    }
   
}
