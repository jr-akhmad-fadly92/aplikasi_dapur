<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class DetailPengajuanMenuUtamaSheet implements FromView, WithTitle
{
    protected $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.detail_pengajuan_menu_utama', $this->data);
    }

    public function title(): string
    {
        return 'data menu utama';
    }
}
