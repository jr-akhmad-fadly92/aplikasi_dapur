<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class RincianSekolahQueryHelper
{
    /**
     * Get rincian sekolah with remaining quantities (sisa jumlah)
     * Fixes SQL injection vulnerability by using proper parameter binding
     * 
     * @param array $menuIds
     * @param string $referensi
     * @return \Illuminate\Database\Query\Builder
     */
    public static function getWithSisaJumlah($menuIds, $referensi)
    {
        if (empty($menuIds)) {
            return DB::table('rincian_sekolah')
                ->whereRaw('0 = 1'); // Return empty result set safely
        }

        return DB::table('rincian_sekolah')
            ->leftJoin('tb_data_sekolah', 'rincian_sekolah.id_sekolah', '=', 'tb_data_sekolah.id')
            ->join('tb_menu', 'rincian_sekolah.id_menu_harian', '=', 'tb_menu.id')
            ->leftJoin('surat_jalan_item', function ($join) use ($referensi) {
                $join->on('rincian_sekolah.id', '=', 'surat_jalan_item.rincian_sekolah_id')
                    ->where(function ($q) use ($referensi) {
                        // Using parameter binding - SAFE from SQL injection
                        $q->where('surat_jalan_item.surat_jalan_referensi', $referensi)
                          ->orWhere('surat_jalan_item.status', 1);
                    });
            })
            ->select(
                DB::raw('MIN(rincian_sekolah.id) as id'),
                'tb_data_sekolah.nama_sekolah',
                'tb_data_sekolah.alamat_sekolah',
                'tb_data_sekolah.jumlah_a',
                'tb_data_sekolah.jumlah_b',
                DB::raw('SUM(rincian_sekolah.jumlah_penerima_a - COALESCE(
                    CASE 
                        WHEN surat_jalan_item.surat_jalan_referensi = ? OR surat_jalan_item.status = 1 
                        THEN surat_jalan_item.jumlah_a ELSE 0 END, 0)) AS sisa_jumlah_penerima_a'),
                DB::raw('SUM(rincian_sekolah.jumlah_penerima_b - COALESCE(
                    CASE 
                        WHEN surat_jalan_item.surat_jalan_referensi = ? OR surat_jalan_item.status = 1 
                        THEN surat_jalan_item.jumlah_b ELSE 0 END, 0)) AS sisa_jumlah_penerima_b')
            )
            ->whereIn('rincian_sekolah.id_menu_harian', $menuIds)
            ->groupBy(
                'tb_data_sekolah.id',
                'tb_data_sekolah.nama_sekolah',
                'tb_data_sekolah.alamat_sekolah',
                'tb_data_sekolah.jumlah_a',
                'tb_data_sekolah.jumlah_b'
            )
            ->setBindings([$referensi, $referensi]);
    }

    /**
     * Get rincian sekolah for form submission (with specific IDs)
     * 
     * @param array $rincianIds
     * @param string $referensi
     * @return \Illuminate\Database\Query\Builder
     */
    public static function getByIdsWithSisa($rincianIds, $referensi)
    {
        if (empty($rincianIds)) {
            return DB::table('rincian_sekolah')
                ->whereRaw('0 = 1');
        }

        return DB::table('rincian_sekolah')
            ->leftJoin('tb_data_sekolah', 'rincian_sekolah.id_sekolah', '=', 'tb_data_sekolah.id')
            ->leftJoin('surat_jalan_item', function ($join) use ($referensi) {
                $join->on('rincian_sekolah.id', '=', 'surat_jalan_item.rincian_sekolah_id')
                    ->where(function ($q) use ($referensi) {
                        $q->where('surat_jalan_item.surat_jalan_referensi', $referensi)
                          ->orWhere('surat_jalan_item.status', 1);
                    });
            })
            ->select(
                DB::raw('MIN(rincian_sekolah.id) as id'),
                'tb_data_sekolah.nama_sekolah',
                'tb_data_sekolah.alamat_sekolah',
                'tb_data_sekolah.jumlah_a',
                'tb_data_sekolah.jumlah_b',
                DB::raw('SUM(rincian_sekolah.jumlah_penerima_a - COALESCE(
                    CASE 
                        WHEN surat_jalan_item.surat_jalan_referensi = ? OR surat_jalan_item.status = 1 
                        THEN surat_jalan_item.jumlah_a ELSE 0 END, 0)) AS sisa_jumlah_penerima_a'),
                DB::raw('SUM(rincian_sekolah.jumlah_penerima_b - COALESCE(
                    CASE 
                        WHEN surat_jalan_item.surat_jalan_referensi = ? OR surat_jalan_item.status = 1 
                        THEN surat_jalan_item.jumlah_b ELSE 0 END, 0)) AS sisa_jumlah_penerima_b')
            )
            ->whereIn('rincian_sekolah.id', $rincianIds)
            ->groupBy(
                'tb_data_sekolah.id',
                'tb_data_sekolah.nama_sekolah',
                'tb_data_sekolah.alamat_sekolah',
                'tb_data_sekolah.jumlah_a',
                'tb_data_sekolah.jumlah_b'
            )
            ->setBindings([$referensi, $referensi]);
    }
}
