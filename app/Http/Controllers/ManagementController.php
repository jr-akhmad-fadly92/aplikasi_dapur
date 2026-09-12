<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManagementController extends Controller
{
    public function index()
    {
        return view('management.dashboard');
    }

    public function indexKitchen()
    {
        return view('management.kitchenm.index');
    }

    public function indexSupplier()
    {
        return view('management.supplier.index');
    }
}
