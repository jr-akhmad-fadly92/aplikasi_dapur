<?php

namespace App\Http\Controllers\office;

use App\Exports\HargaHetTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\HargaHetImport;
use App\Models\TbHargaHet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class TbHargaHetController extends Controller
{
    public function index(Request $request)
    {
        $header = 'Harga HET';

        if ($request->ajax()) {
            $rows = DB::table('tb_master_bahan as b')
                ->leftJoin('tb_harga_het as h', 'b.id', '=', 'h.id_bahan')
                ->select(
                    'b.id as id_bahan',
                    'b.bahan as nama_bahan',
                    'h.tanggal_update',
                    'h.harga_het'
                )
                ->orderBy('b.bahan', 'asc')
                ->get();

            return DataTables::of($rows)
                ->addIndexColumn()
                ->addColumn('tanggal_update_format', function ($row) {
                    if (empty($row->tanggal_update)) {
                        return '-';
                    }

                    return date('d-m-Y', strtotime($row->tanggal_update));
                })
                ->addColumn('harga_het_format', function ($row) {
                    $harga = (int) ($row->harga_het ?? 0);

                    return 'Rp ' . number_format($harga, 0, ',', '.');
                })
                ->addColumn('action', function ($row) {
                    $tanggal = !empty($row->tanggal_update) ? $row->tanggal_update : date('Y-m-d');
                    $harga = (int) ($row->harga_het ?? 0);

                    return '<button class="btn btn-warning btn-sm btn-edit-harga-het" data-id_bahan="' . $row->id_bahan . '" data-nama_bahan="' . e($row->nama_bahan) . '" data-tanggal_update="' . $tanggal . '" data-harga_het="' . $harga . '">Update Manual</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('office.harga_het.index', compact('header'));
    }

    public function upsert(Request $request)
    {
        $request->validate([
            'id_bahan' => 'required|exists:tb_master_bahan,id',
            'tanggal_update' => 'required|date',
            'harga_het' => 'required|integer|min:0',
        ]);

        TbHargaHet::updateOrCreate(
            ['id_bahan' => $request->id_bahan],
            [
                'tanggal_update' => $request->tanggal_update,
                'harga_het' => $request->harga_het,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Harga HET berhasil diperbarui.',
        ]);
    }

    public function downloadTemplate()
    {
        return Excel::download(new HargaHetTemplateExport(), 'template_harga_het.xlsx');
    }

    public function importTemplate(Request $request)
    {
        $request->validate([
            'file_het' => 'required|mimes:xlsx,xls,csv|max:4096',
        ]);

        try {
            $import = new HargaHetImport();
            Excel::import($import, $request->file('file_het'));

            $message = 'Import selesai. Berhasil: ' . $import->getSuccessCount() . ', gagal: ' . $import->getFailedCount() . '.';

            $errors = $import->getErrors();
            if (!empty($errors)) {
                $message .= ' Contoh error: ' . $errors[0];
            }

            return redirect()->route('harga-het.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('harga-het.index')->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }
}
