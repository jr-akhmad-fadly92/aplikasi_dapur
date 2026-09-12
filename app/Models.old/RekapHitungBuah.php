<?php

namespace App\Models;

class RekapHitungBuah
{
    public int $pax;
    public float $gram_per_pax;
    public float $total_kg;
    public int $tray;
    public string $prepared_by;

    public function __construct(int $pax, float $gram_per_pax, string $prepared_by = 'Joko Priyo')
    {
        $this->pax          = $pax;
        $this->gram_per_pax = $gram_per_pax;
        $this->prepared_by  = $prepared_by;

        $buffer      = 1.01;
        $totalGram   = $this->pax * $this->gram_per_pax * $buffer;
        $this->total_kg = $totalGram / 1000;
        $this->tray     = (int) ceil($this->total_kg / 2);
    }
}