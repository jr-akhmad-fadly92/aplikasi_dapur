<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DetailPengajuanMenuExport implements WithMultipleSheets
{
    protected $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new DetailPengajuanMenuUtamaSheet($this->data),
            new DetailPengajuanMenuBahanBakuSheet($this->data),
            new DetailPengajuanMenuRekapPoSheet($this->data),
        ];
    }
}
