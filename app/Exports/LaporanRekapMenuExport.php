<?php

namespace App\Exports;

use App\Models\DataDapur;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanRekapMenuExport implements WithMultipleSheets
{
    protected $start;
    protected $end;
    protected $menuReportsByDate;
    protected $bumbuTotalsByDate;

    public function __construct($start, $end, Collection $menuReportsByDate, Collection $bumbuTotalsByDate)
    {
        $this->start = $start;
        $this->end = $end;
        $this->menuReportsByDate = $menuReportsByDate;
        $this->bumbuTotalsByDate = $bumbuTotalsByDate;
    }

    public function sheets(): array
    {
        $tanggalSheets = $this->menuReportsByDate->keys()
            ->merge($this->bumbuTotalsByDate->keys())
            ->unique()
            ->sort()
            ->values();

        if ($tanggalSheets->isEmpty()) {
            $tanggalSheets = collect([$this->start]);
        }

        return $tanggalSheets->map(function ($tanggal) {
            return new LaporanRekapMenuPerTanggalSheet(
                $tanggal,
                $this->menuReportsByDate->get($tanggal, collect()),
                $this->bumbuTotalsByDate->get($tanggal, collect())
            );
        })->all();
    }

}

class LaporanRekapMenuPerTanggalSheet implements FromView, ShouldAutoSize, WithTitle
{
    protected $tanggal;
    protected $menuReports;
    protected $bumbuTotal;

    public function __construct(string $tanggal, Collection $menuReports, Collection $bumbuTotal)
    {
        $this->tanggal = $tanggal;
        $this->menuReports = $menuReports;
        $this->bumbuTotal = $bumbuTotal;
    }

    public function view(): View
    {
        $dapur = DataDapur::first();

        $totalPorsi = $this->menuReports->sum(function ($item) {
            return (int) ($item['total_porsi'] ?? 0);
        });

        return view('exports.laporan_rekap_menu', [
            'menuReports' => $this->menuReports,
            'bumbu_total' => $this->bumbuTotal,
            'totalPorsi' => $totalPorsi,
            'start' => $this->tanggal,
            'end' => $this->tanggal,
            'dapur' => $dapur,
        ]);
    }

    public function title(): string
    {
        return Carbon::parse($this->tanggal)->format('d-m-Y');
    }

}
