<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GiziController extends Controller
{
    //
    public function index()
    {
        $header = "Dashboard Gizi";
        return view('gizi.masakan', compact('header'));
    }

    public function gizi()
    {
        $header = "Dashboard Gizi";
        return view('gizi.masakan', compact('header'));
    }

    public function bahan_baku()
    {
        $header = "Dashboard bahan baku gizi";
        return view('gizi.bahan_baku', compact('header'));
    }

    public function riwayat_gizi()
    {
        $header = "Dashboard Gizi";
        return view('gizi.riwayat', compact('header'));
    }

}
