<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SiswaSekolahTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            [
                '20328501', // npsn (8 digit)
                '1234567890', // nisn (10 digit)
                'Contoh Nama Siswa', // nama
                '7A', // kelas
                'L', // jenis_kelamin (L/P)
                'Nama Orang Tua', // nama_orangtua (opsional)
                'Keterangan jika ada', // keterangan (opsional)
                '45', // berat_badan (kg)
                '150', // tinggi_badan (cm)
                '2010-01-15', // tanggal_lahir (YYYY-MM-DD)
                'Jakarta', // tempat_lahir
                'Tidak ada', // riwayat_penyakit_bawaan
                'Tidak ada', // riwayat_penyakit_menular
                'Tidak ada', // alergi
                '081234567890', // nomor_telp_emergency (12 digit)
                'A', // golongan_darah (A/B/AB/O)
                'A' // golongan_penerimaan (A/B)
            ],
            [
                '20328501', // npsn (8 digit)
                '1234567891', // nisn (10 digit)
                'Siti Aisyah', // nama
                '7A', // kelas
                'P', // jenis_kelamin (L/P)
                'Budi Santoso', // nama_orangtua (opsional)
                'Aktif', // keterangan (opsional)
                '42', // berat_badan (kg)
                '148', // tinggi_badan (cm)
                '2010-03-20', // tanggal_lahir (YYYY-MM-DD)
                'Bandung', // tempat_lahir
                'Asma', // riwayat_penyakit_bawaan
                'Tidak ada', // riwayat_penyakit_menular
                'Kacang tanah', // alergi
                '082345678901', // nomor_telp_emergency (12 digit)
                'B', // golongan_darah (A/B/AB/O)
                'B' // golongan_penerimaan (A/B)
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'npsn',
            'nisn',
            'nama',
            'kelas',
            'jenis_kelamin',
            'nama_orangtua',
            'keterangan',
            'berat_badan',
            'tinggi_badan',
            'tanggal_lahir',
            'tempat_lahir',
            'riwayat_penyakit_bawaan',
            'riwayat_penyakit_menular',
            'alergi',
            'nomor_telp_emergency',
            'golongan_darah',
            'golongan_penerimaan',
            'alamat',
            'pekerjaan_orang_tua'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ]
            ],
            // Style untuk baris contoh
            2 => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E7E6E6']
                ]
            ]
        ];
    }
}
