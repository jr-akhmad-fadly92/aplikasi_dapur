<?php

namespace App\Http\Controllers\office;

use App\Exports\LaporanBiayaBahanPanganExport;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanBiayaBahanBakuController extends Controller
{
    public function index(Request $request)
    {
        $header = 'Laporan Biaya Bahan Baku';
        $query = $this->baseBahanBakuQuery();

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            [$tanggalMulai, $tanggalSelesai] = $this->parseTanggalRange(
                $request->tanggal_mulai,
                $request->tanggal_selesai
            );

            $query->whereBetween(DB::raw('DATE(COALESCE(pc.tanggal_tutup_po, tkk.tanggal))'), [
                $tanggalMulai,
                $tanggalSelesai,
            ]);
        }

        $data = (clone $query)->get();
        $total = (clone $query)->sum('tkk.jumlah');

        return view('office.laporanakuntan.lbbb', compact('data', 'total', 'header'));
    }

    public function cetak(Request $request)
    {
        $start = $request->tanggal_mulai;
        $end = $request->tanggal_selesai;
        $query = $this->baseBahanBakuQuery();

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            [$tanggalMulai, $tanggalSelesai] = $this->parseTanggalRange(
                $request->tanggal_mulai,
                $request->tanggal_selesai,
                true
            );

            $query->whereBetween(DB::raw('COALESCE(pc.tanggal_tutup_po, tkk.tanggal)'), [
                $tanggalMulai,
                $tanggalSelesai,
            ]);
        }

        $data = (clone $query)->get();
        $total = (clone $query)->sum('tkk.jumlah');

        $pdf = Pdf::loadView('office.laporanakuntan.lbbbpdf', compact('data', 'start', 'end', 'total'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('laporan_biaya_bahan_baku_' . date('Y-m-d_H-i-s') . '.pdf');
    }

    public function create()
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function Lap_biaya_bahan_baku_export(Request $request)
    {
        $start = Carbon::createFromFormat('d-m-Y', $request->tanggal_mulai)->format('Y-m-d');
        $end = Carbon::createFromFormat('d-m-Y', $request->tanggal_selesai)->format('Y-m-d');

        $data = DB::table('tb_po_bahan as po')
            ->join('tb_master_bahan as b', 'po.id_bahan', '=', 'b.id')
            ->join('rincian_menu_harian as r', 'po.id_rincian_bahan', '=', 'r.id')
            ->join('tb_menu as m', 'r.id_menu_harian', '=', 'm.id')
            ->join('tb_satuan as s', 'po.satuan', '=', 's.id')
            ->join('tb_po as tp', 'po.id_po', '=', 'tp.id')
            ->select(
                DB::raw('ROW_NUMBER() OVER (ORDER BY m.tanggal_kirim, m.menu, b.bahan) as no_urut'),
                'm.menu',
                'b.bahan',
                DB::raw('SUM(po.jumlah_bahan) as total_jumlah_bahan'),
                DB::raw('SUM(po.jumlah_po) as total_jumlah_po'),
                'm.tanggal_kirim',
                's.satuan'
            )
            ->whereBetween('m.tanggal_kirim', [$start, $end])
            ->where('tp.status_po', '=', 'close')
            ->groupBy('m.menu', 'b.bahan', 'm.tanggal_kirim', 's.satuan')
            ->orderBy('m.tanggal_kirim')
            ->orderBy('m.menu')
            ->orderBy('b.bahan')
            ->get();

        return Excel::download(
            new LaporanBiayaBahanPanganExport($data, $start, $end),
            'laporan_bahan_pangan' . date('d-m-Y') . '.xlsx'
        );
    }

    protected function baseBahanBakuQuery()
    {
        return DB::table('tb_kas_kecil_transaksi as tkk')
            ->leftJoin('tb_po as po', 'tkk.nomor_po', '=', 'po.nomor_po')
            ->leftJoin('tb_po_close as pc', 'po.id', '=', 'pc.id_po')
            ->where('tkk.status', 1)
            ->whereNotNull('tkk.nomor_po')
            ->where('tkk.nomor_po', 'like', 'PO-%')
            ->select(
                'tkk.jumlah',
                'tkk.deskripsi',
                'tkk.status',
                'tkk.nomor_po',
                DB::raw('COALESCE(pc.tanggal_tutup_po, DATE(tkk.tanggal)) as tanggal_tutup_po')
            )
            ->orderByRaw('COALESCE(pc.tanggal_tutup_po, DATE(tkk.tanggal)) asc');
    }

    protected function parseTanggalRange($tanggalMulai, $tanggalSelesai, $withTime = false)
    {
        $mulai = Carbon::createFromFormat('d-m-Y', $tanggalMulai)->format('Y-m-d');
        $selesai = Carbon::createFromFormat('d-m-Y', $tanggalSelesai)->format('Y-m-d');

        if ($withTime) {
            return [
                $mulai . ' 00:00:00',
                $selesai . ' 23:59:59',
            ];
        }

        return [$mulai, $selesai];
    }
}
