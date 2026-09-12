<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\MasterBahanNutrisi;

class ImportMasterBahanNutrisi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:master-bahan-nutrisi {--file=dokumen/data_gizi.xlsx : Path to XLSX file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import master bahan nutrisi dari data_gizi.xlsx ke tb_master_bahan_nutrisi';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = $this->option('file');
        $path = base_path($file);

        if (!file_exists($path)) {
            $this->error("File not found: {$path}");
            return 1;
        }

        $this->info("Membaca file: {$path}");

        try {
            $reader = IOFactory::createReaderForFile($path);
            $spreadsheet = $reader->load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (count($rows) < 5) {
                $this->error('File tidak berisi data yang cukup. Minimal 5 baris (header + sub-header + units + data).');
                return 1;
            }

            // Excel struktur:
            // Row 0: No., Kode, Nama Bahan Makanan, "Komposisi Zat Gizi...", NULL, NULL...
            // Row 1: NULL, Baru, ⇲, Air, Energi, Protein, Lemak, Karbohidrat, Serat, Abu...
            // Row 2: NULL, ⇲, NULL, ⇲, ⇲, ⇲, ⇲, ⇲, ⇲, ⇲...
            // Row 3: NULL, NULL, NULL, (g), (Kal), (g), (g), (g), (g), (g)...
            // Row 4+: Data

            // Tentukan kolom index
            $headerRow = $rows[1]; // Sub-header row untuk nama kolom nutrisi
            $columnMap = $this->mapColumns($headerRow);

            $count = 0;
            $skipped = 0;

            // Mulai dari row 4 (index 4 dalam 0-based)
            for ($i = 4; $i < count($rows); $i++) {
                $row = $rows[$i];

                // Skip jika baris kosong atau kolom pertama kosong
                if (empty($row[0]) && empty($row[1]) && empty($row[2])) {
                    continue;
                }

                $payload = [
                    'no' => isset($row[0]) ? (string) $row[0] : null,
                    'kode' => isset($row[1]) ? (string) $row[1] : null,
                    'nama_bahan' => isset($row[2]) ? (string) $row[2] : null,
                    'kelompok' => isset($row[26]) ? (string) $row[26] : null, // Kolom 26 untuk kelompok
                    'mentah_olahan' => isset($row[25]) ? (string) $row[25] : null, // Kolom 25 untuk mentah/olahan
                    'sumber' => isset($row[27]) ? (string) $row[27] : null, // Kolom 27 untuk sumber
                    'bdd' => null,
                ];

                // Map nutrisi columns
                foreach ($columnMap as $field => $colIndex) {
                    if (isset($row[$colIndex])) {
                        $value = $row[$colIndex];
                        // Konversi koma ke titik untuk desimal
                        if (is_string($value) && !empty($value)) {
                            $value = str_replace(',', '.', $value);
                            $payload[$field] = (float) $value;
                        }
                    }
                }

                // Skip jika kode kosong (kode adalah identifier unik)
                if (empty($payload['kode'])) {
                    $skipped++;
                    continue;
                }

                try {
                    // Upsert: update jika kode sudah ada, insert jika baru
                    MasterBahanNutrisi::updateOrCreate(
                        ['kode' => $payload['kode']],
                        $payload
                    );
                    $count++;
                } catch (\Exception $e) {
                    $this->warn("Error pada baris " . ($i + 1) . ": " . $e->getMessage());
                    $skipped++;
                }
            }

            $this->info("✓ Selesai. Baris berhasil diproses: {$count}, Baris dilewati: {$skipped}");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Map column names dari header row ke field names
     */
    private function mapColumns($headerRow)
    {
        // Direct column mapping berdasarkan struktur Excel yang sudah diverifikasi
        return [
            'air' => 3,           // Column index 3: "Air"
            'energi' => 4,        // Column index 4: "Energi"
            'protein' => 5,       // Column index 5: "Protein"
            'lemak' => 6,         // Column index 6: "Lemak"
            'karbohidrat' => 7,   // Column index 7: "Karbohidrat"
            'serat' => 8,         // Column index 8: "Serat"
            'abu' => 9,           // Column index 9: "Abu"
            'kalsium' => 10,      // Column index 10: "Kalsium"
            'fosfor' => 11,       // Column index 11: "Fosfor"
            'besi' => 12,         // Column index 12: "Besi"
            'natrium' => 13,      // Column index 13: "Natrium"
            'kalium' => 14,       // Column index 14: "Kalium"
            'seng' => 16,         // Column index 16: "Seng"
        ];
    }
}
