<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CounterPaxExport implements FromView, ShouldAutoSize
{
    protected $tanggal;
    protected $data;
    protected $totals;
    protected $dapur;

    public function __construct(string $tanggal, $data, array $totals, $dapur)
    {
        $this->tanggal = $tanggal;
        $this->data = $data;
        $this->totals = $totals;
        $this->dapur = $dapur;
    }

    public function view(): View
    {
        return view('exports.counter_pax_excel', [
            'tanggal' => $this->tanggal,
            'data' => $this->data,
            'totals' => $this->totals,
            'dapur' => $this->dapur,
        ]);
    }
}
