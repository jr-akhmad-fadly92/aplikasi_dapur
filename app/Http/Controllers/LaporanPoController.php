<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\LaporanPoVersi1Export;
use App\Exports\LaporanPoKarboVersi1Export;
use App\Exports\LaporanPoLaukVersi1Export;
use App\Exports\LaporanPoSayurVersi1Export;
use App\Exports\LaporanPoBuahVersi1Export;
use App\Exports\LaporanPoPendampingVersi1Export;
use App\Exports\LaporanRekapKarboVersi1Export;
use App\Exports\LaporanRekapKarboVersi2Export;
use App\Exports\LaporanRekapLaukVersi1Export;
use App\Exports\LaporanRekapSayurVersi1Export;
use App\Exports\LaporanRekapBuahVersi1Export;
use App\Exports\LaporanRekapPendampingVersi1Export;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPoController extends Controller
{
    /**
     * Export Laporan PO Versi 1
     * Menampilkan PO per tanggal pelayanan
     */
    public function exportPoVersi1(Request $request)
    {
        $tanggal_awal = $request->input('tanggal_awal', '2025-12-01');
        $tanggal_akhir = $request->input('tanggal_akhir', '2025-12-21');
        
        $fileName = 'Laporan_PO_' . $tanggal_awal . '_sd_' . $tanggal_akhir . '.xlsx';
        
        return Excel::download(
            new LaporanPoVersi1Export($tanggal_awal, $tanggal_akhir),
            $fileName
        );
    }

    /**
     * Export Laporan PO Karbo (id_komponen_sehat = 1)
     */
    public function exportPoKarboVersi1(Request $request)
    {
        $tanggal_awal = $request->input('tanggal_awal', '2025-12-01');
        $tanggal_akhir = $request->input('tanggal_akhir', '2025-12-16');

        $fileName = 'Laporan_PO_Karbo_' . $tanggal_awal . '_sd_' . $tanggal_akhir . '.xlsx';

        return Excel::download(
            new LaporanPoKarboVersi1Export($tanggal_awal, $tanggal_akhir),
            $fileName
        );
    }

    /**
     * Export Laporan PO Lauk (id_komponen_sehat = 2)
     */
    public function exportPoLaukVersi1(Request $request)
    {
        $tanggal_awal = $request->input('tanggal_awal', '2025-12-01');
        $tanggal_akhir = $request->input('tanggal_akhir', '2025-12-16');

        $fileName = 'Laporan_PO_Lauk_' . $tanggal_awal . '_sd_' . $tanggal_akhir . '.xlsx';

        return Excel::download(
            new LaporanPoLaukVersi1Export($tanggal_awal, $tanggal_akhir),
            $fileName
        );
    }

    /**
     * Export Laporan PO Sayur (id_komponen_sehat = 3)
     */
    public function exportPoSayurVersi1(Request $request)
    {
        $tanggal_awal = $request->input('tanggal_awal', '2025-12-01');
        $tanggal_akhir = $request->input('tanggal_akhir', '2025-12-16');

        $fileName = 'Laporan_PO_Sayur_' . $tanggal_awal . '_sd_' . $tanggal_akhir . '.xlsx';

        return Excel::download(
            new LaporanPoSayurVersi1Export($tanggal_awal, $tanggal_akhir),
            $fileName
        );
    }

    /**
     * Export Laporan PO Buah (id_komponen_sehat = 4)
     */
    public function exportPoBuahVersi1(Request $request)
    {
        $tanggal_awal = $request->input('tanggal_awal', '2025-12-01');
        $tanggal_akhir = $request->input('tanggal_akhir', '2025-12-16');

        $fileName = 'Laporan_PO_Buah_' . $tanggal_awal . '_sd_' . $tanggal_akhir . '.xlsx';

        return Excel::download(
            new LaporanPoBuahVersi1Export($tanggal_awal, $tanggal_akhir),
            $fileName
        );
    }

    /**
     * Export Laporan PO Pendamping (id_komponen_sehat = 5)
     */
    public function exportPoPendampingVersi1(Request $request)
    {
        $tanggal_awal = $request->input('tanggal_awal', '2025-12-01');
        $tanggal_akhir = $request->input('tanggal_akhir', '2025-12-16');

        $fileName = 'Laporan_PO_Pendamping_' . $tanggal_awal . '_sd_' . $tanggal_akhir . '.xlsx';

        return Excel::download(
            new LaporanPoPendampingVersi1Export($tanggal_awal, $tanggal_akhir),
            $fileName
        );
    }

    /**
     * Export Laporan Rekap Karbo (id_komponen_sehat = 1)
     */
    public function exportRekapKarboVersi1(Request $request)
    {
        $tanggal_awal = $request->input('tanggal_awal', '2025-12-01');
        $tanggal_akhir = $request->input('tanggal_akhir', '2025-12-21');

        $fileName = 'Laporan_Rekap_Karbo_' . $tanggal_awal . '_sd_' . $tanggal_akhir . '.xlsx';

        return Excel::download(
            new LaporanRekapKarboVersi1Export($tanggal_awal, $tanggal_akhir),
            $fileName
        );
    }

    /**
     * Export Laporan Rekap Karbo Versi 2 (dengan jumlah masak)
     */
    public function exportRekapKarboVersi2(Request $request)
    {
        $tanggal_awal = $request->input('tanggal_awal', '2025-12-01');
        $tanggal_akhir = $request->input('tanggal_akhir', '2025-12-21');

        $fileName = 'Laporan_Rekap_Karbo_V2_' . $tanggal_awal . '_sd_' . $tanggal_akhir . '.xlsx';

        return Excel::download(
            new LaporanRekapKarboVersi2Export($tanggal_awal, $tanggal_akhir),
            $fileName
        );
    }

    /**
     * Export Laporan Rekap Lauk (parent_id in 15,21,16,17,18,19)
     */
    public function exportRekapLaukVersi1(Request $request)
    {
        $tanggal_awal = $request->input('tanggal_awal', '2025-12-01');
        $tanggal_akhir = $request->input('tanggal_akhir', '2025-12-21');

        $fileName = 'Laporan_Rekap_Lauk_' . $tanggal_awal . '_sd_' . $tanggal_akhir . '.xlsx';

        return Excel::download(
            new LaporanRekapLaukVersi1Export($tanggal_awal, $tanggal_akhir),
            $fileName
        );
    }

    /**
     * Export Laporan Rekap Sayur (parent_id = 4)
     */
    public function exportRekapSayurVersi1(Request $request)
    {
        $tanggal_awal = $request->input('tanggal_awal', '2025-12-01');
        $tanggal_akhir = $request->input('tanggal_akhir', '2025-12-21');

        $fileName = 'Laporan_Rekap_Sayur_' . $tanggal_awal . '_sd_' . $tanggal_akhir . '.xlsx';

        return Excel::download(
            new LaporanRekapSayurVersi1Export($tanggal_awal, $tanggal_akhir),
            $fileName
        );
    }

    /**
     * Export Laporan Rekap Buah (parent_id = 20)
     */
    public function exportRekapBuahVersi1(Request $request)
    {
        $tanggal_awal = $request->input('tanggal_awal', '2025-12-01');
        $tanggal_akhir = $request->input('tanggal_akhir', '2025-12-21');

        $fileName = 'Laporan_Rekap_Buah_' . $tanggal_awal . '_sd_' . $tanggal_akhir . '.xlsx';

        return Excel::download(
            new LaporanRekapBuahVersi1Export($tanggal_awal, $tanggal_akhir),
            $fileName
        );
    }

    /**
     * Export Laporan Rekap Pendamping (parent_id = 21)
     */
    public function exportRekapPendampingVersi1(Request $request)
    {
        $tanggal_awal = $request->input('tanggal_awal', '2025-12-01');
        $tanggal_akhir = $request->input('tanggal_akhir', '2025-12-21');

        $fileName = 'Laporan_Rekap_Pendamping_' . $tanggal_awal . '_sd_' . $tanggal_akhir . '.xlsx';

        return Excel::download(
            new LaporanRekapPendampingVersi1Export($tanggal_awal, $tanggal_akhir),
            $fileName
        );
    }
}
