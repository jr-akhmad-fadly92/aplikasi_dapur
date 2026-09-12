<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class KebutuhanDapurExport implements FromView
{
    public function view(): View
    {
        return view('exports.kebutuhan', [
            'data' => [
                [
                    'masakan' => 'Nasi Putih',
                    'qty' => 1877,
                    'satuan' => 'pax',
                    'produksi_qty' => 33.7,
                    'produksi_satuan' => 'kg',
                    'produksi_jumlah' => '21',
                    'produksi_keterangan' => '180 gr / pax',
                    'realisasi_qty' => 1877,
                    'realisasi_satuan' => 'pax',
                    'realisasi_keterangan' => '',
                ],
                [
                    'masakan' => 'Ayam Kecap',
                    'qty' => 1583,
                    'satuan' => 'ptg',
                    'produksi_qty' => 116.7,
                    'produksi_satuan' => 'kg',
                    'produksi_jumlah' => '2',
                    'produksi_keterangan' => '',
                    'realisasi_qty' => 1563,
                    'realisasi_satuan' => 'pax',
                    'realisasi_keterangan' => 'kerusakan masak',
                ],
                [
                    'masakan' => 'Tumis Sayuran',
                    'qty' => 1393,
                    'satuan' => 'pax',
                    'produksi_qty' => 139.3,
                    'produksi_satuan' => 'kg',
                    'produksi_jumlah' => '6',
                    'produksi_keterangan' => '100 gr / pax',
                    'realisasi_qty' => 1323,
                    'realisasi_satuan' => 'pax',
                    'realisasi_keterangan' => 'ada yang tidak terinput',
                ],
                [
                    'masakan' => 'Telur Puyuh Rebus',
                    'qty' => 4691,
                    'satuan' => 'butir',
                    'produksi_qty' => 46.2,
                    'produksi_satuan' => 'kg',
                    'produksi_jumlah' => '6',
                    'produksi_keterangan' => '30 gr/pax (3 butir)',
                    'realisasi_qty' => 1563,
                    'realisasi_satuan' => 'pax',
                    'realisasi_keterangan' => '',
                ],
                [
                    'masakan' => 'Pisang',
                    'qty' => 1343,
                    'satuan' => 'buah',
                    'produksi_qty' => 134.3,
                    'produksi_satuan' => 'kg',
                    'produksi_jumlah' => '',
                    'produksi_keterangan' => '',
                    'realisasi_qty' => 1563,
                    'realisasi_satuan' => 'pax',
                    'realisasi_keterangan' => 'diambil dari stok untuk selasa',
                ],
            ]
        ]);
    }
}
