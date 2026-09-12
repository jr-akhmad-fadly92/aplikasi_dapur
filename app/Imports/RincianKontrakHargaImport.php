<?php

namespace App\Imports;

use App\Models\TbRincianKontrak;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RincianKontrakHargaImport implements ToCollection, WithHeadingRow
{
    protected $idKontrak;
    protected $successCount = 0;
    protected $failedCount = 0;
    protected $errors = [];

    public function __construct(int $idKontrak)
    {
        $this->idKontrak = $idKontrak;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                $idRincian = isset($row['id_rincian_kontrak']) ? (int) $row['id_rincian_kontrak'] : 0;
                $hargaRaw = $row['harga_baru'] ?? null;
                $hargaBaru = (int) preg_replace('/[^0-9]/', '', (string) $hargaRaw);

                if ($idRincian <= 0 || $hargaBaru <= 0) {
                    $this->failedCount++;
                    $this->errors[] = 'Baris ' . ($index + 2) . ': id_rincian_kontrak/harga_baru tidak valid.';
                    continue;
                }

                $item = TbRincianKontrak::where('id', $idRincian)
                    ->where('id_kontrak', $this->idKontrak)
                    ->where('status', 1)
                    ->first();

                if (!$item) {
                    $this->failedCount++;
                    $this->errors[] = 'Baris ' . ($index + 2) . ': data rincian kontrak tidak ditemukan.';
                    continue;
                }

                $item->harga_bahan = $hargaBaru;
                $item->save();
                $this->successCount++;
            } catch (\Exception $e) {
                $this->failedCount++;
                $this->errors[] = 'Baris ' . ($index + 2) . ': ' . $e->getMessage();
            }
        }
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getFailedCount(): int
    {
        return $this->failedCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
