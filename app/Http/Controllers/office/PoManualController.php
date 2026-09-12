<?php


namespace App\Http\Controllers\Office;


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
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\TbPo;
use App\Models\TbPoBahan;
use App\Models\Supplier;
use App\Models\TbKontrak;
use App\Models\Menu;
use App\Models\rincian_menu_harian;
use App\Models\TbMasterBahan;
use App\Models\TbSatuan;
use App\Models\DataDapur;
use App\Models\tb_karyawan;
use App\Models\TbBantuBahanPo;
use App\Models\TbRincianKontrak;

class PoManualController extends Controller
{
    private function getActiveRincianKontrak(int $idBahan, ?int $idKontrak = null): ?TbRincianKontrak
    {
        return TbRincianKontrak::where('id_bahan', $idBahan)
            ->where('status', 1)
            ->when($idKontrak, function ($query) use ($idKontrak) {
                return $query->where('id_kontrak', $idKontrak);
            })
            ->orderByDesc('id')
            ->first();
    }

    private function upsertActiveRincianKontrak(int $idBahan, int $idKontrak, float $hargaBahan, ?int $satuanBahan = null): TbRincianKontrak
    {
        $rincian = $this->getActiveRincianKontrak($idBahan, $idKontrak);

        if ($rincian) {
            $rincian->harga_bahan = $hargaBahan;
            if (!empty($satuanBahan)) {
                $rincian->satuan_bahan = $satuanBahan;
            }
            $rincian->status = 1;
            $rincian->save();

            return $rincian;
        }

        return TbRincianKontrak::create([
            'id_kontrak' => $idKontrak,
            'id_bahan' => $idBahan,
            'merek_bahan' => '-',
            'harga_bahan' => $hargaBahan,
            'jumlah_bahan' => 1,
            'satuan_bahan' => $satuanBahan ?: 0,
            'status' => 1,
            'kemasan' => '-',
        ]);
    }

    public function buat_po_manual()
    {
        $header = "Buat PO Manual";
        $karyawan = tb_karyawan::all();
        $jumlah_kontrak = TbPo::count();
        $jumlah_kontrak = TbPo::count();
        if ($jumlah_kontrak < 9) {
            $jumlah_kontrak = '00' . ($jumlah_kontrak + 1);
        } else if ($jumlah_kontrak < 99) {
            $jumlah_kontrak = '0' . ($jumlah_kontrak + 1);
        }
	$dapur = DataDapur::first();
        $bulan_PO   = Carbon::now()->format('Ym'); // Format YYYY-MM untuk filter
        $nomor_PO   = 'PO-Y0'. $dapur->nomor_dapur.'-NP' . $bulan_PO . $jumlah_kontrak;
        $Supplier   = Supplier::select('id', 'nama_supplier')->get();
        $kontrak    = Supplier::join('tb_kontrak', 'tb_supplier.id', 'tb_kontrak.id_supplier')
            
            ->select('tb_kontrak.id', 'tb_supplier.nama_supplier', 'tb_kontrak.id_supplier')
            ->distinct()
            ->get();
        return view('office/PO.create_manual', compact('header', 'nomor_PO','kontrak','karyawan'));
    }

    public function draft_po_manual(Request $request)
    {
        $request->validate([
            'nomor_po' => 'required|string|max:255',
            'supplier' => 'required',
            'tanggal_pengajuan' => 'required|date',
            'id_karyawan' => 'required',
        ]);
        
        TbPo::create([
            'nomor_po'          => $request->nomor_po,
            'tanggal_pengajuan' => $request->tanggal_pengajuan,
            'tanggal_po'        => $request->tanggal_pengajuan,
            'id_karyawan'       => $request->id_karyawan,
            'status_po'         => 'pengajuan',
            'id_kontrak'        => $request->supplier,
            'manual'            => 1
        ]);
        $data = TbPo::where('nomor_po', $request->nomor_po)->first();
       
        return redirect()->route('rincian_po_manual', ['id' => $data->id])->with('success', 'Draft PO berhasil dibuat.');
    }

    public function rincian_po_manual($id)
    {
        $header = "Rincian PO Manual";
        $po     = TbPo::where('id', $id)->first();
        $bahan  = TbMasterBahan::all();
        $satuan  = TbSatuan::all();
        if (request()->ajax()) {
        $data = TbPoBahan::join('tb_master_bahan','tb_po_bahan.id_bahan','tb_master_bahan.id')
                ->where('tb_po_bahan.id_po', $id)        
                ->select('tb_master_bahan.bahan', 'tb_po_bahan.jumlah_bahan', 'tb_po_bahan.id', 'tb_po_bahan.jumlah_po', 'tb_po_bahan.satuan')
                ->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('bahan', function ($row) {
                return $row->bahan ?? '-';
            })
            ->addColumn('jumlah_yang_dipesan', function ($row) {
                $satuan = TbSatuan::where('id',$row->satuan)->first();
                return number_format($row->jumlah_bahan, 0, ',', '.') .' '. $satuan->satuan ?? '-';
            })
            ->addColumn('jumlah_yang_dibayar', function ($row) {
                return 'Rp. '.number_format($row->jumlah_po, 0, ',', '.') ?? '-';
            })
            ->addColumn('action', function ($row) {
                $url = route('hapus_barang_po_manual', $row['id']);
                return '<button class="btn btn-danger btn-sm btn-delete" data-id="' . $row['id'] . '" data-url="' . $url . '">Delete</button>';
            })

            ->rawColumns(['action', 'jumlah_yang_dibayar', 'jumlah_yang_dipesan'])
            ->make(true);
        }
        return view('office/PO.index_rincian_po_manual', compact('header', 'po', 'bahan', 'satuan'));
    }

    public function tambah_barang_po_manual(Request $request)
    {
        $request->validate([
            'id_po'              => 'required|integer',
            'id_bahan'           => 'required|integer',
            'jumlah_bahan'       => 'required|numeric',
            'satuan'             => 'required',
            'jumlah_po'          => 'required|numeric',
            'id_kontrak'         => 'required|integer',
            'tanggal_kedatangan' => 'required|date',
        ]);

        $tanggal_digunakan = date('Y-m-d', strtotime($request->tanggal_kedatangan));
        $rincianKontrak = $this->upsertActiveRincianKontrak(
            (int) $request->id_bahan,
            (int) $request->id_kontrak,
            (float) $request->jumlah_po,
            (int) $request->satuan
        );

        $hargaSatuan = (float) $rincianKontrak->harga_bahan;

        TbPoBahan::create([
            'id_po'              => $request->id_po,
            'id_bahan'           => $request->id_bahan,
            'jumlah_bahan'       => $request->jumlah_bahan,
            'satuan'             => $rincianKontrak->satuan_bahan ?: $request->satuan,
            'jumlah_po'          => $hargaSatuan * $request->jumlah_bahan,
            'id_kontrak'         => $request->id_kontrak,
            'id_rincian_bahan'   => 0,
            'id_rincian_kontrak' => $rincianKontrak->id,
            'tanggal_kedatangan' => $request->tanggal_kedatangan,
            'tanggal_digunakan'  => $tanggal_digunakan,
        ]);

        return response()->json(['success' => 'Data berhasil ditambahkan']);
    }

   
    public function get_harga_bahan(Request $request)
    {
        $idBahan   = $request->get('id_bahan');
        $idKontrak = $request->get('id_kontrak');

        $rincian = $this->getActiveRincianKontrak((int) $idBahan, $idKontrak ? (int) $idKontrak : null);

        return response()->json([
            'harga_bahan' => $rincian ? (float) $rincian->harga_bahan : 0,
            'satuan_bahan' => $rincian ? (int) $rincian->satuan_bahan : null,
            'id_rincian_kontrak' => $rincian ? (int) $rincian->id : null,
        ]);
    }

    public function update_harga_bahan(Request $request)
    {
        $idBahan   = $request->get('id_bahan');
        $idKontrak = $request->get('id_kontrak');
        $harga     = $request->get('harga_bahan');
        $satuan    = $request->get('satuan_bahan');

        if (!$idBahan || !$idKontrak || $harga === null || $harga === '') {
            return response()->json(['success' => false, 'message' => 'Data harga belum lengkap'], 422);
        }

        $rincian = $this->upsertActiveRincianKontrak(
            (int) $idBahan,
            (int) $idKontrak,
            (float) $harga,
            $satuan ? (int) $satuan : null
        );

        return response()->json([
            'success' => true,
            'harga_bahan' => (float) $rincian->harga_bahan,
            'satuan_bahan' => (int) $rincian->satuan_bahan,
            'id_rincian_kontrak' => (int) $rincian->id,
        ]);
    }

    public function hapus_barang_po_manual($id)
    {
        $barang = TbPoBahan::findOrFail($id);
        $barang->delete();
        return response()->json(['success' => true, 'message' => 'Bahan PO berhasil dihapus.']);
    }
}
