<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use App\Models\MasterBahanNutrisi;
use App\Services\NutrisiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yajra\DataTables\Facades\DataTables;

class MasterBahanNutrisiController extends Controller
{
    protected $nutrisiService;

    public function __construct()
    {
        $this->nutrisiService = new NutrisiService();
    }

    public function index(Request $request)
    {
        $header = 'Daftar Master Bahan Nutrisi';
        $sourceList = MasterBahanNutrisi::query()
            ->whereNotNull('sumber')
            ->where('sumber', '!=', '')
            ->distinct()
            ->orderBy('sumber')
            ->pluck('sumber');
        $kelompokList = MasterBahanNutrisi::query()
            ->whereNotNull('kelompok')
            ->where('kelompok', '!=', '')
            ->distinct()
            ->orderBy('kelompok')
            ->pluck('kelompok');
        $mentahOlahanList = MasterBahanNutrisi::query()
            ->whereNotNull('mentah_olahan')
            ->where('mentah_olahan', '!=', '')
            ->distinct()
            ->orderBy('mentah_olahan')
            ->pluck('mentah_olahan');

        if ($request->ajax()) {
            $query = MasterBahanNutrisi::query();

            if ($request->filled('kelompok')) {
                $query->where('kelompok', $request->kelompok);
            }

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('energi', function ($row) {
                    return $this->formatNumber($row->energi);
                })
                ->editColumn('protein', function ($row) {
                    return $this->formatNumber($row->protein);
                })
                ->editColumn('lemak', function ($row) {
                    return $this->formatNumber($row->lemak);
                })
                ->editColumn('karbohidrat', function ($row) {
                    return $this->formatNumber($row->karbohidrat);
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('master_bahan_nutrisi.show', $row->id);
                    $deleteUrl = route('master_bahan_nutrisi.destroy', $row->id);

                    return '<div class="btn-group btn-group-sm" role="group">'
                        . '<a href="' . $editUrl . '" class="btn btn-info">Edit</a>'
                        . '<form action="' . $deleteUrl . '" method="POST" onsubmit="return confirm(\'Hapus master bahan nutrisi ini?\')" style="display:inline-block;margin-left:4px;">'
                        . csrf_field()
                        . method_field('DELETE')
                        . '<button type="submit" class="btn btn-danger">Delete</button>'
                        . '</form>'
                        . '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('office.master_bahan_nutrisi.index', compact('header', 'kelompokList', 'sourceList', 'mentahOlahanList'));
    }

    public function show(MasterBahanNutrisi $masterBahanNutrisi): View
    {
        $header = 'Detail Bahan Nutrisi';

        return view('office.master_bahan_nutrisi.show', compact('header', 'masterBahanNutrisi'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no' => 'nullable|string|max:255',
            'kode' => 'required|string|max:255|unique:data_gizi,kode',
            'nama_bahan' => 'required|string|max:255',
            'kelompok' => 'nullable|string|max:255',
            'mentah_olahan' => 'nullable|string|max:255',
            'sumber' => 'nullable|string|max:255',
            'bdd' => 'nullable|numeric|min:0|max:100',
            'air' => 'nullable|numeric',
            'energi' => 'nullable|numeric',
            'protein' => 'nullable|numeric',
            'lemak' => 'nullable|numeric',
            'karbohidrat' => 'nullable|numeric',
            'serat' => 'nullable|numeric',
            'abu' => 'nullable|numeric',
            'natrium' => 'nullable|numeric',
            'kalium' => 'nullable|numeric',
            'kalsium' => 'nullable|numeric',
            'magnesium' => 'nullable|numeric',
            'fosfor' => 'nullable|numeric',
            'besi' => 'nullable|numeric',
            'seng' => 'nullable|numeric',
        ]);

        DB::table('data_gizi')->insert([
            'no' => $validated['no'] ?? null,
            'kode' => $validated['kode'],
            'nama_bahan_makanan' => $validated['nama_bahan'],
            'kelompok_makanan' => $validated['kelompok'] ?? null,
            'mentah_olahan' => $validated['mentah_olahan'] ?? null,
            'sumber_tkpi' => $validated['sumber'] ?? null,
            'bdd_pct' => $validated['bdd'] ?? null,
            'air_g' => $validated['air'] ?? null,
            'energi_kal' => $validated['energi'] ?? null,
            'protein_g' => $validated['protein'] ?? null,
            'lemak_g' => $validated['lemak'] ?? null,
            'karbohidrat_g' => $validated['karbohidrat'] ?? null,
            'serat_g' => $validated['serat'] ?? null,
            'abu_g' => $validated['abu'] ?? null,
            'natrium_na_mg' => $validated['natrium'] ?? null,
            'kalium_ka_mg' => $validated['kalium'] ?? null,
            'kalsium_ca_mg' => $validated['kalsium'] ?? null,
            'fosfor_p_mg' => $validated['fosfor'] ?? null,
            'besi_fe_mg' => $validated['besi'] ?? null,
            'seng_zn_mg' => $validated['seng'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('master_bahan_nutrisi.index')
            ->with('success', 'Master bahan nutrisi berhasil ditambahkan.');
    }

    public function destroy(MasterBahanNutrisi $masterBahanNutrisi)
    {
        DB::table('tb_resep_realisasi_akg')
            ->where('id_master_bahan_nutrisi', $masterBahanNutrisi->id)
            ->delete();

        DB::table('data_gizi')
            ->where('id', $masterBahanNutrisi->id)
            ->delete();

        return redirect()
            ->route('master_bahan_nutrisi.index')
            ->with('success', 'Master bahan nutrisi berhasil dihapus.');
    }

    public function importExcel(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:xls,xlsx,csv|max:10240',
        ]);

        try {
            $path = $validated['file']->getRealPath();
            $reader = IOFactory::createReaderForFile($path);
            $spreadsheet = $reader->load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $count = 0;
            $skipped = 0;

            for ($i = 4; $i < count($rows); $i++) {
                $row = $rows[$i];

                if (empty($row[0]) && empty($row[1]) && empty($row[2])) {
                    continue;
                }

                $payload = [
                    'no' => isset($row[0]) ? (string) $row[0] : null,
                    'kode' => isset($row[1]) ? trim((string) $row[1]) : null,
                    'nama_bahan' => isset($row[2]) ? (string) $row[2] : null,
                    'kelompok' => isset($row[26]) ? (string) $row[26] : null,
                    'mentah_olahan' => isset($row[25]) ? (string) $row[25] : null,
                    'sumber' => isset($row[27]) ? (string) $row[27] : null,
                    'bdd' => null,
                ];

                if (empty($payload['kode'])) {
                    $skipped++;
                    continue;
                }

                foreach ($this->excelFieldMap() as $field => $index) {
                    if (isset($row[$index]) && $row[$index] !== '') {
                        $value = is_string($row[$index]) ? str_replace(',', '.', $row[$index]) : $row[$index];
                        $payload[$field] = is_numeric($value) ? (float) $value : null;
                    }
                }

                MasterBahanNutrisi::updateOrCreate(
                    ['kode' => $payload['kode']],
                    $payload
                );

                $count++;
            }

            return redirect()
                ->route('master_bahan_nutrisi.index')
                ->with('success', "Import selesai. {$count} baris berhasil diproses, {$skipped} baris dilewati.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal import file: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('template_master_nutrisi');

        // Header baris 1-4 mengikuti struktur file data_gizi.xlsx
        $sheet->setCellValue('A1', 'No.');
        $sheet->setCellValue('B1', 'Kode');
        $sheet->setCellValue('C1', 'Nama Bahan Makanan');
        $sheet->setCellValue('D1', 'Komposisi Zat Gizi Makanan per 100 gram BDD (Bagian yang Dapat Dimakan) - Sumber: TKPI 2019');
        $sheet->setCellValue('Z1', 'Mentah /');
        $sheet->setCellValue('AA1', 'Kelompok');
        $sheet->setCellValue('AB1', 'Sumber');

        $sheet->setCellValue('B2', 'Baru');
        $sheet->setCellValue('C2', '⇲');
        $sheet->setCellValue('D2', 'Air');
        $sheet->setCellValue('E2', 'Energi');
        $sheet->setCellValue('F2', 'Protein');
        $sheet->setCellValue('G2', 'Lemak');
        $sheet->setCellValue('H2', 'Karbohidrat');
        $sheet->setCellValue('I2', 'Serat');
        $sheet->setCellValue('J2', 'Abu');
        $sheet->setCellValue('K2', 'Kalsium');
        $sheet->setCellValue('L2', 'Fosfor');
        $sheet->setCellValue('M2', 'Besi');
        $sheet->setCellValue('N2', 'Natrium');
        $sheet->setCellValue('O2', 'Kalium');
        $sheet->setCellValue('P2', 'Tembaga');
        $sheet->setCellValue('Q2', 'Seng');
        $sheet->setCellValue('R2', 'Retinol');
        $sheet->setCellValue('S2', 'β-karoten');
        $sheet->setCellValue('T2', 'Karoten');
        $sheet->setCellValue('U2', 'Thiamin');
        $sheet->setCellValue('V2', 'Riboflavin');
        $sheet->setCellValue('W2', 'Niasin');
        $sheet->setCellValue('X2', 'Vitamin C');
        $sheet->setCellValue('Y2', 'BDD');
        $sheet->setCellValue('Z2', 'Olahan');
        $sheet->setCellValue('AA2', 'Makanan');
        $sheet->setCellValue('AB2', 'TKPI 2019');

        $sheet->setCellValue('B3', '⇲');
        $sheet->setCellValue('D3', '⇲');
        $sheet->setCellValue('E3', '⇲');
        $sheet->setCellValue('F3', '⇲');
        $sheet->setCellValue('G3', '⇲');
        $sheet->setCellValue('H3', '⇲');
        $sheet->setCellValue('I3', '⇲');
        $sheet->setCellValue('J3', '⇲');
        $sheet->setCellValue('K3', '(Ca) ⇲');
        $sheet->setCellValue('L3', '(P) ⇲');
        $sheet->setCellValue('M3', '(Fe) ⇲');
        $sheet->setCellValue('N3', '(Na) ⇲');
        $sheet->setCellValue('O3', '(Ka) ⇲');
        $sheet->setCellValue('P3', '(Cu) ⇲');
        $sheet->setCellValue('Q3', '(Zn) ⇲');
        $sheet->setCellValue('R3', '(vit. A) ⇲');
        $sheet->setCellValue('S3', '⇲');
        $sheet->setCellValue('T3', 'total ⇲');
        $sheet->setCellValue('U3', '(vit. B1) ⇲');
        $sheet->setCellValue('V3', '(vit. B2) ⇲');
        $sheet->setCellValue('W3', '⇲');
        $sheet->setCellValue('X3', '⇲');
        $sheet->setCellValue('Y3', '⇲');
        $sheet->setCellValue('Z3', '⇲');
        $sheet->setCellValue('AA3', '⇲');
        $sheet->setCellValue('AB3', '⇲');

        $sheet->setCellValue('D4', '(g)');
        $sheet->setCellValue('E4', '(Kal)');
        $sheet->setCellValue('F4', '(g)');
        $sheet->setCellValue('G4', '(g)');
        $sheet->setCellValue('H4', '(g)');
        $sheet->setCellValue('I4', '(g)');
        $sheet->setCellValue('J4', '(g)');
        $sheet->setCellValue('K4', '(mg)');
        $sheet->setCellValue('L4', '(mg)');
        $sheet->setCellValue('M4', '(mg)');
        $sheet->setCellValue('N4', '(mg)');
        $sheet->setCellValue('O4', '(mg)');
        $sheet->setCellValue('P4', '(mg)');
        $sheet->setCellValue('Q4', '(mg)');
        $sheet->setCellValue('R4', '(mcg)');
        $sheet->setCellValue('S4', '(mcg)');
        $sheet->setCellValue('T4', '(mcg)');
        $sheet->setCellValue('U4', '(mg)');
        $sheet->setCellValue('V4', '(mg)');
        $sheet->setCellValue('W4', '(mg)');
        $sheet->setCellValue('X4', '(mg)');
        $sheet->setCellValue('Y4', '(%)');

        // Struktur visual agar mirip dengan sumber
        $sheet->mergeCells('D1:Y1');
        $sheet->mergeCells('Z1:Z1');
        $sheet->mergeCells('AA1:AA1');
        $sheet->mergeCells('AB1:AB1');

        $sheet->getStyle('A1:AB4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:AB4')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:AB4')->getAlignment()->setWrapText(true);

        $sheet->getStyle('A1:AB4')->getFont()->setBold(true);
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(24);
        $sheet->getRowDimension(3)->setRowHeight(24);
        $sheet->getRowDimension(4)->setRowHeight(24);

        foreach (range('A', 'AB') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->freezePane('D5');

        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_master_bahan_nutrisi.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function update(Request $request, MasterBahanNutrisi $masterBahanNutrisi)
    {
        $validated = $request->validate([
            'no' => 'nullable|string|max:255',
            'kode' => 'required|string|max:255|unique:tb_master_bahan_nutrisi,kode,' . $masterBahanNutrisi->id,
            'nama_bahan' => 'required|string|max:255',
            'kelompok' => 'nullable|string|max:255',
            'mentah_olahan' => 'nullable|string|max:255',
            'sumber' => 'nullable|string|max:255',
            'bdd' => 'nullable|numeric|min:0|max:100',
            'air' => 'nullable|numeric',
            'energi' => 'nullable|numeric',
            'protein' => 'nullable|numeric',
            'lemak' => 'nullable|numeric',
            'karbohidrat' => 'nullable|numeric',
            'serat' => 'nullable|numeric',
            'abu' => 'nullable|numeric',
            'natrium' => 'nullable|numeric',
            'kalium' => 'nullable|numeric',
            'kalsium' => 'nullable|numeric',
            'magnesium' => 'nullable|numeric',
            'fosfor' => 'nullable|numeric',
            'besi' => 'nullable|numeric',
            'seng' => 'nullable|numeric',
        ]);

        $masterBahanNutrisi->update($validated);

        return redirect()
            ->route('master_bahan_nutrisi.show', $masterBahanNutrisi->id)
            ->with('success', 'Data master bahan nutrisi berhasil diperbarui.');
    }

    /**
     * API: Hitung nutrisi berdasarkan bahan_id, jumlah gram, dan BDD
     * 
     * Request:
     * POST /api/master-bahan-nutrisi/hitung
     * {
     *   "bahan_id": 1,
     *   "jumlah_gram": 150,
     *   "bdd": 80
     * }
     */
    public function apiHitungNutrisi(Request $request)
    {
        try {
            $request->validate([
                'bahan_id' => 'required|integer|exists:tb_master_bahan_nutrisi,id',
                'jumlah_gram' => 'required|numeric|min:0.01',
                'bdd' => 'nullable|numeric|min:0|max:100',
            ]);

            $nutrisi = $this->nutrisiService->hitungNutrisi(
                $request->bahan_id,
                $request->jumlah_gram,
                $request->bdd
            );

            return response()->json([
                'success' => true,
                'data' => $nutrisi,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Get detail bahan dengan nutrisi reference
     * Gunakan untuk populate form dengan default BDD dan nutrisi dasar
     */
    public function apiGetBahanDetail(Request $request)
    {
        try {
            $request->validate([
                'bahan_id' => 'required|integer|exists:tb_master_bahan_nutrisi,id',
            ]);

            $bahan = MasterBahanNutrisi::find($request->bahan_id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $bahan->id,
                    'nama_bahan' => $bahan->nama_bahan,
                    'kode' => $bahan->kode,
                    'kelompok' => $bahan->kelompok,
                    'bdd' => $bahan->bdd ?? 100,
                    'nutrisi_per_100g' => [
                        'energi' => $bahan->energi,
                        'protein' => $bahan->protein,
                        'lemak' => $bahan->lemak,
                        'karbohidrat' => $bahan->karbohidrat,
                        'serat' => $bahan->serat,
                        'natrium' => $bahan->natrium,
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Get list bahan untuk select2 search
     * GET /api/master-bahan-nutrisi/list?q=search_term
     */
    public function apiListBahan(Request $request)
    {
        try {
            $search = $request->get('q', '');
            $limit = 20;

            $query = MasterBahanNutrisi::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_bahan', 'like', '%' . $search . '%')
                        ->orWhere('kode', 'like', '%' . $search . '%')
                        ->orWhere('kelompok', 'like', '%' . $search . '%');
                });
            }

            $items = $query->select('id', 'nama_bahan', 'kode', 'kelompok')
                ->orderBy('nama_bahan')
                ->limit($limit)
                ->get();

            return response()->json($items, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function formatNumber($value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
    }

    private function excelFieldMap(): array
    {
        return [
            'air' => 3,
            'energi' => 4,
            'protein' => 5,
            'lemak' => 6,
            'karbohidrat' => 7,
            'serat' => 8,
            'abu' => 9,
            'kalsium' => 10,
            'fosfor' => 11,
            'besi' => 12,
            'natrium' => 13,
            'kalium' => 14,
            'magnesium' => 15,
            'seng' => 16,
        ];
    }
}
