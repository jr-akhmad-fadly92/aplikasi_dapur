<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\MenuGiziHarian;
use App\Models\Menu;

class ImportDataGizi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:data-gizi {--file= : Path to XLSX file (relative to project root)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import nutrisi dari dokumen/data_gizi.xlsx ke tb_menu_gizi_harian';

    public function handle()
    {
        $path = $this->option('file') ?? base_path('dokumen/data_gizi.xlsx');

        if (!file_exists($path)) {
            $this->error("File not found: {$path}");
            return 1;
        }

        $this->info("Membaca file: {$path}");

        $reader = IOFactory::createReaderForFile($path);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (count($rows) < 2) {
            $this->error('File tidak berisi data. Pastikan ada header dan setidaknya satu baris data.');
            return 1;
        }

        $headerRow = array_shift($rows);
        $headers = [];
        foreach ($headerRow as $col => $value) {
            $key = strtolower(trim((string) $value));
            $headers[$col] = $key;
        }

        $count = 0;
        foreach ($rows as $r) {
            $data = [];
            foreach ($r as $col => $value) {
                $key = $headers[$col] ?? null;
                if (!$key) continue;
                $data[$key] = $value;
            }

            // Try to determine id_menu
            $idMenu = null;
            if (!empty($data['id_menu']) || !empty($data['menu_id'])) {
                $idMenu = (int) ($data['id_menu'] ?? ($data['menu_id'] ?? 0));
            } elseif (!empty($data['menu']) || !empty($data['nama_menu'])) {
                $menuName = $data['menu'] ?? $data['nama_menu'];
                $menu = Menu::where('menu', $menuName)->orWhere('menu', 'LIKE', "%{$menuName}%")->first();
                if ($menu) {
                    $idMenu = $menu->id;
                }
            }

            if (!$idMenu) {
                $this->warn('Baris dilewati karena tidak dapat menentukan id_menu (menu/id kosong): ' . json_encode($data));
                continue;
            }

            $payload = [
                'energi' => isset($data['energi']) ? (float) $data['energi'] : null,
                'protein' => isset($data['protein']) ? (float) $data['protein'] : null,
                'lemak' => isset($data['lemak']) ? (float) $data['lemak'] : null,
                'karbohidrat' => isset($data['karbohidrat']) ? (float) $data['karbohidrat'] : null,
                'serat' => isset($data['serat']) ? (float) $data['serat'] : null,
                'natrium' => isset($data['natrium']) ? (float) $data['natrium'] : null,
            ];

            // Upsert
            MenuGiziHarian::updateOrCreate([
                'id_menu' => $idMenu,
            ], array_merge(['id_menu' => $idMenu], $payload));

            $count++;
        }

        $this->info("Selesai. Baris diproses: {$count}");
        return 0;
    }
}
