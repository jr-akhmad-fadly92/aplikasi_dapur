<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InspektoController extends Controller
{
    //
    public function index()
    {
        $header = "Dashboard Inspektor";
        return view('inspektor.dashboard_inspektor', compact('header'));
    }

    public function riwayat()
    {
        $header = "Riwayat Inspektor";
        return view('inspektor.riwayat_inspektor', compact('header'));
    }
}
