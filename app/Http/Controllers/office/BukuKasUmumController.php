<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BKUPo;
use App\Models\BKUKasKecil;
use App\Models\BKUArsip; 
use Barryvdh\DomPDF\Facade\Pdf;

class BukuKasUmumController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->start_date;
        $end   = $request->end_date;

        // Data PO
        $poQuery = BKUPo::with(['bahan.masterBahan']);
        if ($start && $end) {
            $poQuery->whereDate('tanggal_approve', '>=', $start)
                    ->whereDate('tanggal_approve', '<=', $end);
        }
        $poData = $poQuery->get()->map(function($item){
            $item->source = 'po';
            $item->tanggal_for_sort = $item->tanggal_approve;
            return $item;
        });

        // Data Kas Kecil
        $kasQuery = BKUKasKecil::query();
        if ($start && $end) {
            $kasQuery->whereDate('tanggal', '>=', $start)
                     ->whereDate('tanggal', '<=', $end);
        }
        $kasData = $kasQuery->get()->map(function($item){
            $item->source = 'kas';
            $item->tanggal_for_sort = $item->tanggal; // sesuaikan dengan kolom DB
            return $item;
        });

        // Gabungkan & urutkan
        $data = $poData->merge($kasData)
                       ->sortBy('tanggal_for_sort')
                       ->values();

        return view('office.bukukasumum.index', compact('data', 'start', 'end'));
    }

    public function cetak(Request $request)
    {
        $start = $request->start_date;
        $end   = $request->end_date;

        // Sama seperti index
        $poQuery = BKUPo::with(['bahan.masterBahan']);
        if ($start && $end) {
            $poQuery->whereDate('tanggal_approve', '>=', $start)
                    ->whereDate('tanggal_approve', '<=', $end);
        }
        $poData = $poQuery->get()->map(function($item){
            $item->source = 'po';
            $item->tanggal_for_sort = $item->tanggal_approve;
            return $item;
        });

        $kasQuery = BKUKasKecil::query();
        if ($start && $end) {
            $kasQuery->whereDate('tanggal', '>=', $start)
                     ->whereDate('tanggal', '<=', $end);
        }
        $kasData = $kasQuery->get()->map(function($item){
            $item->source = 'kas';
            $item->tanggal_for_sort = $item->tanggal;
            return $item;
        });

        $data = $poData->merge($kasData)
                       ->sortBy('tanggal_for_sort')
                       ->values();

        // --- Simpan otomatis ke tabel arsip ---//
        $saldo = 0;
        foreach($data as $item){
            if($item->source == 'po'){
                $debet = 0;
                $kredit = (float) $item->bahan->sum('jumlah_po');
                $uraian = $item->bahan->map(function($b){
                    return ($b->masterBahan->bahan ?? '')." ($b->jumlah_bahan $b->satuan)";
                })->implode(', ');
                $no_bukti = $item->nomor_po;
            } else {
                if(strtolower($item->jenis_transaksi) == 'masuk'){
                    $debet = $item->jumlah;
                    $kredit = 0;
                } else {
                    $debet = 0;
                    $kredit = $item->jumlah;
                }
                $uraian = $item->deskripsi;
                $no_bukti = $item->nomor_transaksi;
            }

            $saldo += ($debet - $kredit);

            BKUArsip::create([
                'tanggal'     => $item->tanggal_for_sort,
                'no_bukti'    => $no_bukti,
                'uraian'      => $uraian,
                'debet'       => $debet,
                'kredit'      => $kredit,
                'saldo'       => $saldo,
            ]);
        }

        $pdf = Pdf::loadView('office.bukukasumum.pdf', compact('data', 'start', 'end'))
                  ->setPaper('a4', 'landscape');

        return $pdf->stream('buku_kas_umum.pdf');
    }
}
