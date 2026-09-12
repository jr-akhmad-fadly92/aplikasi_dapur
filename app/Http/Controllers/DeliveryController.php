<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    //
    public function index()
    {
        $header = "Dashboard Delivery";
        return view('delivery.dashboard', compact('header'));
    }
}
