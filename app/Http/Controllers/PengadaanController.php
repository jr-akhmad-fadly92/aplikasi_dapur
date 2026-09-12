<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PengadaanController extends Controller
{
    //
    public function index()
    {
        $header = "Dashboard procurement";
        return view('pengadaan.dashboard_procurement', compact('header'));
    }

    public function po()
    {
        $header = "PO procurement";
        return view('pengadaan.po_procurement', compact('header'));
    }

    public function riwayat()
    {
        $header = "Riwayat procurement";
        return view('pengadaan.riwayat_procurement', compact('header'));
    }
}
