<?php

namespace App\Imports;

use App\Models\TbMasterBahan;
use App\Models\TbHargaHet;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class HargaHetImport implements ToCollection, WithHeadingRow
{
    protected $successCount = 0;
    protected $failedCount = 0;
    protected $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                $idBahan = isset($row['id_bahan']) ? (int) $row['id_bahan'] : 0;
                $hargaRaw = $row['harga_het'] ?? null;
                $tanggalRaw = $row['tanggal_update'] ?? null;

                $hargaProvided = $this->isFilled($hargaRaw);
                $tanggalProvided = $this->isFilled($tanggalRaw);

                $bahan = TbMasterBahan::find($idBahan);
                if (!$bahan) {
                    $this->failedCount++;
                    $this->errors[] = 'Baris ' . ($index + 2) . ': id_bahan tidak ditemukan di tb_master_bahan.';
                    continue;
                }

                $existing = TbHargaHet::where('id_bahan', $idBahan)->first();

                // Jika tidak ada input update sama sekali, pertahankan data lama.
                if (!$hargaProvided && !$tanggalProvided) {
                    $this->successCount++;
                    continue;
                }

                $hargaHet = $existing ? (int) $existing->harga_het : 0;
                if ($hargaProvided) {
                    $normalized = preg_replace('/[^0-9]/', '', (string) $hargaRaw);
                    $hargaHet = $normalized === '' ? 0 : (int) $normalized;
                }

                // Jika data lama ada dan harga tidak berubah, jangan update tanggal/data.
                if ($existing && $hargaHet === (int) $existing->harga_het) {
                    $this->successCount++;
                    continue;
                }

                $tanggalUpdate = $existing ? $existing->tanggal_update : date('Y-m-d');
                if ($tanggalProvided) {
                    $tanggalUpdate = $this->parseDate($tanggalRaw);
                    if (empty($tanggalUpdate)) {
                        $this->failedCount++;
                        $this->errors[] = 'Baris ' . ($index + 2) . ': tanggal_update tidak valid.';
                        continue;
                    }
                }

                if (!$tanggalProvided) {
                    $tanggalUpdate = date('Y-m-d');
                }

                // Untuk data yang belum pernah ada, nilai default tetap 0 jika tidak diubah.
                if (!$existing && $hargaHet === 0 && !$tanggalProvided) {
                    $this->successCount++;
                    continue;
                }

                // Untuk data yang belum pernah ada dan masih 0, tetap default 0 (tidak membuat record).
                if (!$existing && $hargaHet === 0) {
                    $this->successCount++;
                    continue;
                }

                TbHargaHet::updateOrCreate(
                    ['id_bahan' => $idBahan],
                    [
                        'tanggal_update' => $tanggalUpdate,
                        'harga_het' => $hargaHet,
                    ]
                );

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

    private function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function isFilled($value): bool
    {
        return !is_null($value) && trim((string) $value) !== '';
    }
}
