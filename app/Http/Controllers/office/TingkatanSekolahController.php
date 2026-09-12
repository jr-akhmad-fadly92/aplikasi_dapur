<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\TingkatanSekolah;
use DataTables;

class TingkatanSekolahController extends Controller
{
    public function index()
    {
        $header = "Master Tingkatan Sekolah";
        if (request()->ajax()) {
            $tingkatansekolah = TingkatanSekolah::select('id','jenjang_sekolah','gramasi_nasi','gramasi_sayur','gramasi_protein','gramasi_buah','gramasi_susu')->get();


            return DataTables::of($tingkatansekolah)
                ->addIndexColumn()

                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('tingkatansekolah.edit', $row['id']) . '" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>
                            <a href="' . route('tingkatansekolah.delete', $row->id) . '"
                            class="edit btn btn-danger btn-sm delete-button" 
                            data-id="' . $row->id . '">Delete</a>';
                
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('office/tingkatansekolah.index',compact('header'));
    }

    public function create()
    {
        return view('office/tingkatansekolah.create', ['header' => 'Tambah Tingkatan Sekolah']);
    }

    /**
     * Menyimpan data tingkatan sekolah ke database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'jenjang_sekolah' => 'required|string|max:255',
            'gramasi_nasi' => 'required|numeric',
            'gramasi_sayur' => 'required|numeric',
            'gramasi_protein' => 'required|numeric',
            'gramasi_buah' => 'required|numeric',
            'gramasi_susu' => 'required|numeric',
        ]);
        $golongan = 0;
        if ($validated['jenjang_sekolah'] == "tk-sd_kelas_3") {
            $golongan = 1;
        } else {
            $golongan = 2;
        }
        // Menyimpan data ke database
        TingkatanSekolah::create([
            'jenjang_sekolah'   => $validated['jenjang_sekolah'],
            'golongan'          =>  $golongan,
            'gramasi_nasi'      => $validated['gramasi_nasi'],
            'gramasi_sayur'     => $validated['gramasi_sayur'],
            'gramasi_protein'   => $validated['gramasi_protein'],
            'gramasi_buah'      => $validated['gramasi_buah'],
            'gramasi_susu'      => $validated['gramasi_susu'],
        ]);

        // Redirect ke halaman lain setelah sukses
        return redirect()->route('tingkatansekolah.index')->with('success', 'Data tingkatan sekolah berhasil ditambahkan');
    }

    public function edit($id)
    {
        $tingkatansekolah = TingkatanSekolah::findOrFail($id);
        return view('office/tingkatansekolah.edit', ['header' => 'Edit Data'], compact('tingkatansekolah'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jenjang_sekolah' => 'required|string|max:255',
            'gramasi_nasi' => 'required|numeric',
            'gramasi_sayur' => 'required|numeric',
            'gramasi_protein' => 'required|numeric',
            'gramasi_buah' => 'required|numeric',
            'gramasi_susu' => 'required|numeric',
        ]);
        $golongan = 0;
        if ($validated['jenjang_sekolah'] == "tk-sd_kelas_3") {
            $golongan = 1;
        } else {
            $golongan = 2;
        }
        $tingkatansekolah = TingkatanSekolah::findOrFail($id);
        $tingkatansekolah->update([
            'jenjang_sekolah' => $request->jenjang_sekolah,
            'golongan'          =>  $golongan,
            'gramasi_nasi' => $request->gramasi_nasi,
            'gramasi_sayur' => $request->gramasi_sayur,
            'gramasi_protein' => $request->gramasi_protein,
            'gramasi_buah' => $request->gramasi_buah,
            'gramasi_susu' => $request->gramasi_susu,
        ]);

        return redirect()->route('tingkatansekolah.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $tingkatansekolah = TingkatanSekolah::findOrFail($id);
        $tingkatansekolah->delete();

        return redirect()->route('tingkatansekolah.index')->with('success', 'Data berhasil dihapus!');
    }
}
