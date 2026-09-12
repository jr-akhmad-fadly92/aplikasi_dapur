<?php

namespace App\Exports;

use App\Models\DataDapur;
use App\Models\TbPoBahan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class FormPenerimaan1Export implements FromView, ShouldAutoSize, WithStyles
{
    protected $tanggal;

    public function __construct($tanggal = null)
    {
        $this->tanggal = $tanggal ?? date('Y-m-d');
    }

    public function view(): View
    {
        $dapur = DataDapur::first();

        return view('penerimaan.pdf.penerimaan_1', [
            'dapur' => $dapur,
            'tanggal' => $this->tanggal
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            7 => ['font' => ['bold' => true]],
        ];
    }
}
