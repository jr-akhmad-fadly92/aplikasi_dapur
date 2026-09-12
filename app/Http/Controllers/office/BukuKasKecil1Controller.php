<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BKKPo1;
use Barryvdh\DomPDF\Facade\Pdf;

class BukuKasKecil1Controller extends Controller
{
    public function index(Request $request)
    {
        $start = $request->start_date;
        $end   = $request->end_date;

        $query = BKKPo1::with(['bahan.masterBahan']);

        if ($start && $end) {
            $query->whereDate('tanggal_approve', '>=', $start)
                  ->whereDate('tanggal_approve', '<=', $end);
        }

        $data = $query->orderBy('tanggal_approve')->get();

        return view('office.bukukaskecil.index1', compact('data','start','end'));

    }

    public function cetak(Request $request)
    {
        $start = $request->start_date;
        $end   = $request->end_date;

        $query = BKKPo1::with(['bahan.masterBahan']);

        if ($start && $end) {
            $query->whereDate('tanggal_approve', '>=', $start)
                  ->whereDate('tanggal_approve', '<=', $end);
        }

        $data = $query->orderBy('tanggal_approve')->get();

        return Pdf::loadView('office.bukukaskecil.pdf1', compact('data','start','end'))
          ->setPaper('a4','landscape')
          ->stream('BKK_PO.pdf');

    }
}
