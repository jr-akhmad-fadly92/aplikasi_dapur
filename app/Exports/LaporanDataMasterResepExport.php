<?php

namespace App\Exports;

use App\Models\DataDapur;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanDataMasterResepExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data_resep;
    protected $dapur;
    protected    $satuan;
    protected    $jumlah_karbohidrat;
    protected    $jumlah_protein;
    protected    $jumlah_sayur;
    protected    $jumlah_buah;
    protected    $jumlah_tambahan;
    protected    $karbohidrat;
    protected    $protein;
    protected    $sayur;
    protected    $buah;
    protected    $tambahan;
    protected $start;
    protected $end;

    public function __construct(
        $data_resep,
        $dapur,
        $satuan,
        $jumlah_karbohidrat,
        $jumlah_protein,
        $jumlah_sayur,
        $jumlah_buah,
        $jumlah_tambahan,
        $karbohidrat,
        $protein,
        $sayur,
        $buah,
        $tambahan,
        $start,
        $end
        )
    {
        $this->data_resep           = $data_resep;
        $this->dapur                = $dapur;
        $this->satuan               = $satuan;
        $this->jumlah_karbohidrat   = $jumlah_karbohidrat;
        $this->jumlah_protein       = $jumlah_protein;
        $this->jumlah_sayur         = $jumlah_sayur;
        $this->jumlah_buah          = $jumlah_buah;
        $this->jumlah_tambahan      = $jumlah_tambahan;
        $this->karbohidrat          = $karbohidrat;
        $this->protein              = $protein;
        $this->sayur                = $sayur;
        $this->buah                 = $buah;
        $this->tambahan             = $tambahan;

        $this->start = $start;
        $this->end   = $end;
    }

    public function view(): View
    {
        $dapur = DataDapur::first();
        return view('exports.laporan_data_resep', [

            'data_resep'            => $this->data_resep,
            'dapur'                 => $this->dapur,
            'satuan'                => $this->satuan,
            'jumlah_karbohidrat'    => $this->jumlah_karbohidrat,
            'jumlah_protein'        => $this->jumlah_protein,
            'jumlah_sayur'          => $this->jumlah_sayur,
            'jumlah_buah'           => $this->jumlah_buah,
            'karbohidrat'           => $this->karbohidrat,
            'protein'               => $this->protein,
            'sayur'                 => $this->sayur,
            'buah'                  => $this->buah,
            'tambahan'              => $this->tambahan,
            'start'                 => $this->start,
            'end'                   => $this->end
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Hitung jumlah baris

        $karbohidrat    = count($this->karbohidrat) + 13; // 6 = header + judul
        $protein        = count($this->protein); // 6 = header + judul
        $sayur          = count($this->sayur); // 6 = header + judul
        $buah           = count($this->buah); // 6 = header + judul
        $tambahan       = count($this->tambahan); // 6 = header + judul

        $jumlah_row = $karbohidrat+ $protein+ $sayur+ $buah+ $tambahan;

        return [
            // Header tebal
            4    => ['font' => ['bold' => true]],

            // Border semua tabel
            "A4:C{$jumlah_row}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color'       => ['argb' => '000000'],
                    ],
                ],
            ],
        ];
    }
}



