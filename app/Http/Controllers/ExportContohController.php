<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExportContohController extends Controller
{
    /**
     * Download the example-based Excel file.
     */
    public function download()
    {
        $templatePath = base_path('dokumen/contoh.xlsx');

        if (!file_exists($templatePath)) {
            abort(404, 'Template not found at dokumen/contoh.xlsx');
        }

        // Return the original file directly so the downloaded file is identical
        $filename = 'export_contoh_' . date('Ymd_His') . '.xlsx';

        return response()->download($templatePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]);
    }
}
