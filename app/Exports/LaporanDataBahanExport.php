<?php

namespace App\Exports;

use App\Models\DataDapur;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanDataBahanExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data_resep;
    protected $dapur;
    protected    $satuan;
    protected    $jumlah_karbohidrat;
    protected    $jumlah_protein;
    protected    $jumlah_sayur;
    protected    $jumlah_buah;
    protected    $jumlah_tambahan;
    protected    $jumlah_bumbu;
    protected    $jumlah_penunjang;
    protected    $karbohidrat;
    protected    $protein;
    protected    $sayur;
    protected    $buah;
    protected    $tambahan;
    protected    $master_karbo;
    protected    $master_protein;
    protected    $master_sayur;
    protected    $master_buah;
    protected    $master_tambahan;
    protected    $master_bumbu;
    protected    $master_penunjang;
    protected $start;
    protected $end;

    public function __construct(
        $master_karbo,
        $master_protein,
        $master_sayur,
        $master_buah,
        $master_tambahan,
        $master_bumbu,
        $master_penunjang,
        $data_resep,
        $dapur,
        $satuan,
        $jumlah_karbohidrat,
        $jumlah_protein,
        $jumlah_sayur,
        $jumlah_buah,
        $jumlah_tambahan,
        $jumlah_bumbu,
        $jumlah_penunjang,
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
        $this->jumlah_bumbu         = $jumlah_bumbu;
        $this->jumlah_penunjang     = $jumlah_penunjang;
        $this->karbohidrat          = $karbohidrat;
        $this->protein              = $protein;
        $this->sayur                = $sayur;
        $this->buah                 = $buah;
        $this->tambahan             = $tambahan;

        $this->master_karbo         = $master_karbo;
        $this->master_protein       = $master_protein;
        $this->master_sayur         = $master_sayur;
        $this->master_buah          = $master_buah;
        $this->master_tambahan      = $master_tambahan;
        $this->master_bumbu         = $master_bumbu;
        $this->master_penunjang     = $master_penunjang;

        $this->start = $start;
        $this->end   = $end;
    }

    public function view(): View
    {
        $dapur = DataDapur::first();
        return view('exports.laporan_data_bahan', [

            'master_karbo'          => $this->master_karbo,
            'master_protein'        => $this->master_protein,
            'master_sayur'          => $this->master_sayur,
            'master_buah'           => $this->master_buah,
            'master_tambahan'       => $this->master_tambahan,
            'master_bumbu'          => $this->master_bumbu,
            'master_penunjang'      => $this->master_penunjang,

            'data_resep'            => $this->data_resep,
            'dapur'                 => $this->dapur,
            'satuan'                => $this->satuan,
            'jumlah_karbohidrat'    => $this->jumlah_karbohidrat,
            'jumlah_protein'        => $this->jumlah_protein,
            'jumlah_sayur'          => $this->jumlah_sayur,
            'jumlah_buah'           => $this->jumlah_buah,
            'jumlah_tambahan'       => $this->jumlah_tambahan,
            'jumlah_bumbu'          => $this->jumlah_bumbu,
            'jumlah_penunjang'      => $this->jumlah_penunjang,
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
        // Tabel detail mulai dari row 6 dengan 7 section.
        // Setiap section memiliki 2 row header (judul section + header kolom)
        // lalu diikuti baris data.
        $totalDataRows =
            count($this->master_karbo) +
            count($this->master_protein) +
            count($this->master_sayur) +
            count($this->master_buah) +
            count($this->master_tambahan) +
            count($this->master_bumbu) +
            count($this->master_penunjang);

        $sectionCount = 7;
        $tableStartRow = 6;
        $tableEndRow = $tableStartRow + ($sectionCount * 2) + $totalDataRows - 1;

        return [
            // Header tebal
            4    => ['font' => ['bold' => true]],

            // Border semua tabel
            "A{$tableStartRow}:C{$tableEndRow}" => [
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

