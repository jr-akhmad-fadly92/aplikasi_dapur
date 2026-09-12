<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FormChecklistHarianExport;

class FormChecklistHarianExportController extends Controller
{
    /**
     * Export checklist harian dokumen ke Excel.
     */
    public function download(Request $request)
    {
        $filename = 'Form_checklist_harian_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new FormChecklistHarianExport(), $filename);
    }
}
