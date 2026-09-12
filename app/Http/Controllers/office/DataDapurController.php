<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

use Illuminate\Http\Request;
use App\Models\DataDapur;
use App\Models\KabKota;
use App\Models\Provinsi;
use DataTables;
use Illuminate\Http\RedirectResponse;

class DataDapurController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DataDapur::where('id',1)->get();

            return DataTables::of($data)
                ->addIndexColumn()
               
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('datadapur.edit', $row['id']) . '" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>
                            ';
                    
                 
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('office/datadapur.index', ['header' => 'Master Dapur']);
    }

    public function create()
    {

        return view('office/datadapur.create', ['header' => 'Tambah Dapur']);
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_dapur' => 'required|string|max:255',
            'alamat_dapur' => 'required|string',
            'nomor_dapur' => 'required',
            'ip_dapur' => 'nullable|string|max:100',
           // 'kota' => 'required|exists:tb_kabkota,id',
           // 'provinsi' => 'required|exists:tb_provinsi,id',
        ]);

        // Simpan ke database
        DataDapur::create([
            'nama_dapur' => $request->nama_dapur,
            'alamat_dapur' => $request->alamat_dapur,
            'nomor_dapur' => $request->nomor_dapur,
            'ip_dapur' => $request->ip_dapur,
            'kota' => $request->kota,
            'provinsi' => $request->provinsi,
            'kecamatan' => $request->kecamatan,
            'kelurahan' => $request->kelurahan,
            'no_telp' => $request->no_telp,
            'email' => $request->email,
        ]);

        return redirect()->route('datadapur.index')->with('success', 'Data dapur berhasil ditambahkan');
    }

    public function edit(string $id): View
    {
        $header = "Edit Dapur";
        //get product by ID
        $dapur = DataDapur::findOrFail(1);

        //render view with product
        return view('office/datadapur.edit', compact('dapur', 'header'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        // Validasi input yang dikirim oleh user
        $request->validate([
            'pemilik'        => 'required|min:1',
            'nama_dapur'     => 'required|min:1',
            'alamat_dapur'   => 'required|min:1',
            'nomor_dapur'    => 'required|min:1',
            'ip_dapur'       => 'nullable|string|max:100',
            'kota'           => 'required|min:1',
            'provinsi'       => 'required|min:1',
            'kelurahan'      => 'required|min:1',
            'no_telp'        => 'required|min:1',
            'kepala_dapur'   => 'required|min:1',
            'admin_dapur'    => 'required|min:1',
        ]);

        // Mencari data dapur berdasarkan ID yang diberikan
        $dapur = DataDapur::findOrFail($id);

        // Melakukan update data dapur tanpa mengubah gambar (jika ada)
        $dapur->update([
            'pemilik'       => $request->pemilik,
            'nama_dapur'    => $request->nama_dapur,
            'alamat_dapur'  => $request->alamat_dapur,
            'nomor_dapur'   => $request->nomor_dapur,
            'ip_dapur'      => $request->ip_dapur,
            'kota'          => $request->kota,
            'provinsi'      => $request->provinsi,
            'kecamatan'     => $request->kecamatan,
            'kelurahan'     => $request->kelurahan,
            'no_telp'       => $request->no_telp,
            'email'         => $request->email,
            'kepala_dapur'  => $request->kepala_dapur,
            'admin_dapur'   => $request->admin_dapur,
            'ahli_gizi'     => $request->ahli_gizi,
            'ahli_akuntan'  => $request->ahli_akuntan,
        ]);

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('datadapur.index')->with(['success' => 'Data Berhasil Diubah!']);
    }
}
