<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use App\Models\DataDapur;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FormChecklistHarianExport implements FromView, ShouldAutoSize
{
    protected $payload;

    public function __construct(array $payload = [])
    {
        $this->payload = $payload;
    }

    public function view(): View
    {
        $dapur = DataDapur::first();
        return view('exports.Form_checklist_harian', $this->payload,compact('dapur'));
    }
}
