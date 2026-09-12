<?php


namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
//import return type View
use Illuminate\View\View;
//import return type redirectResponse
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Supplier;
use App\Models\TbKontrak;
use App\Models\TbMasterBahan;
use App\Models\TbRincianKontrak;
use App\Models\TbSatuan;
use App\Exports\RincianKontrakHargaTemplateExport;
use App\Imports\RincianKontrakHargaImport;
use Maatwebsite\Excel\Facades\Excel;


class TbRincianKontrakController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {   
        $header         = 'Rincian-Kontrak';
        $kontrak        = TbKontrak::findOrFail($id);
        $supplier       = Supplier::where('id',$kontrak->id_supplier)->first();
        $bahan          = TbMasterBahan::all();
        $satuan         = TbSatuan::all();

        if (request()->ajax()) {
            //$users = User::query();
            $rincian_kontrak = TbRincianKontrak::where('id_kontrak',$id)->where('status',1)->get();


            return DataTables::of($rincian_kontrak)
                ->addIndexColumn() // Menambah index

                ->addColumn('nama_bahan', function ($row) {
                    $bahan_detail      = TbMasterBahan::find($row->id_bahan);

                    if(!$bahan_detail)
                    {
                        return " | " . $row->merek_bahan;    
                    }
                    return $bahan_detail->bahan ." | ".$row->merek_bahan;
                   
                })
                ->addColumn('nama_satuan', function ($row) {
                    $satuan_detail      = TbSatuan::findOrFail($row->satuan_bahan);

                    return number_format($row->harga_bahan, 0, ',', '.') . ' / ' . $satuan_detail->satuan;
                })
                ->addColumn('action', function ($row) {
                   
                    $btn = '
                    <button class="btn btn-sm btn-warning btn-edit-box"
                        data-id="' . $row->id . '"
                        data-idkontrak=13
                        data-idbahan="' . $row->id_bahan  . '"
                        data-hargabahan="' . $row->harga_bahan   . '"
                        data-jumlahbahan=1
                        data-satuanbahan="' . $row->satuan_bahan  . '"
                        data-merekbahan="' . $row->merek_bahan   . '"
                        data-status="' . $row->status   . '"
                        data-kemasan="' . $row->kemasan  . '"
                        >
                        Edit
                    </button>
                    <a href="' . route('rincian-kontrak.delete', $row->id) . '" 
                            class="edit btn btn-danger btn-sm delete-button" 
                            data-id="' . $row->id . '">Deleted</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('office/kontrak_supplier.index_rincian_kontrak', compact('header','supplier','kontrak','id', 'bahan','satuan'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_bahan'      => 'required',
            'merek_bahan'   => 'required',
            'harga_bahan'   => 'required',
            'jumlah_bahan'  => 'required',
            'satuan_bahan'  => 'required',
            'kemasan'       => 'required',
        ]);
        
        $cek    = TbRincianKontrak::where('id_bahan', $request->id_bahan)
                ->where('id_kontrak', $request->id_kontrak)
                ->where('merek_bahan', 'like', '%' . $request->merek_bahan . '%')
                ->where('status', 1)

            ->count();
        if($cek > 0 )
        {
            return response()->json(['error' => 'Bahan sudah ada']);
        }
        $bahan = TbMasterBahan::find($request->id_bahan);
        TbRincianKontrak::create([
            'id_kontrak'    => $request->id_kontrak,
            'id_bahan'      => $request->id_bahan,
            'merek_bahan'   => $bahan->bahan,
            'harga_bahan'   => $request->harga_bahan,
            'jumlah_bahan'  => $request->jumlah_bahan,
            'satuan_bahan'  => $request->satuan_bahan,
            'status'        => 1,
            'kemasan'       => $request->kemasan,
        ]);

        return response()->json(['message' => 'Bahan berhasil ditambahkan']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'harga_bahan'      => 'required|integer|min:1',      // wajib, integer >= 1
        ]);

        // Ambil data box berdasarkan id, jika tidak ditemukan akan error 404
        $bahan = TbMasterBahan::find($request->id_bahan);
        TbRincianKontrak::where('id', $id)->update([
           
            'harga_bahan'   => $request->harga_bahan,
           
        ]);

        // Kembalikan response JSON sukses
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui.'
        ]);
    }

    public function downloadTemplateHarga($idKontrak)
    {
        $kontrak = TbKontrak::findOrFail($idKontrak);

        $filename = 'template_update_harga_kontrak_' . $kontrak->id . '.xlsx';

        return Excel::download(new RincianKontrakHargaTemplateExport((int) $idKontrak), $filename);
    }

    public function importTemplateHarga(Request $request, $idKontrak)
    {
        TbKontrak::findOrFail($idKontrak);

        $request->validate([
            'file_harga' => 'required|mimes:xlsx,xls,csv|max:4096',
        ]);

        try {
            $import = new RincianKontrakHargaImport((int) $idKontrak);
            Excel::import($import, $request->file('file_harga'));

            $success = $import->getSuccessCount();
            $failed = $import->getFailedCount();
            $errors = $import->getErrors();

            $message = 'Import selesai. Berhasil: ' . $success . ', gagal: ' . $failed . '.';
            if (!empty($errors)) {
                $message .= ' Contoh error: ' . $errors[0];
            }

            return redirect()
                ->route('dashboard-rincian-kontrak', $idKontrak)
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->route('dashboard-rincian-kontrak', $idKontrak)
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //get product by ID
        $kontrak = TbRincianKontrak::findOrFail($id);
        $kontrak->status = 0;
        $kontrak->save();

        // delete product
        // $kontrak->delete();

        //redirect to index
        return redirect()->route('dashboard-rincian-kontrak',$kontrak->id_kontrak)->with(['success' => 'Data Berhasil Dihapus!']);
    }
}
