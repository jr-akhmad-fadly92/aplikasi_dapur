<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DetailPengajuanMenu2Export implements FromView
{
    protected $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function view(): View
    {
        // Kirim data ke view jika diperlukan
        return view('exports.detail_pengajuan_menu_2', $this->data);
    }
}
