<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExportIngestController extends Controller
{
    protected function authorizeRequest(Request $request)
    {
        $expectedToken = (string) config('services.export_ingest.token', '');
        $providedToken = (string) ($request->header('X-Export-Token') ?? $request->query('token', ''));

        if ($expectedToken === '') {
            return response()->json([
                'success' => false,
                'message' => 'EXPORT_INGEST_TOKEN belum dikonfigurasi di server client.',
                'data' => [],
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 503,
                ],
            ], 503);
        }

        if (!hash_equals($expectedToken, $providedToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized export ingest request.',
                'data' => [],
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 401,
                ],
            ], 401);
        }

        return null;
    }

    public function index(Request $request)
    {
        if ($response = $this->authorizeRequest($request)) {
            return $response;
        }

        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $limit = (int) ($validated['limit'] ?? 10);
        $periodStart = isset($validated['start_date'])
            ? Carbon::parse($validated['start_date'])->startOfDay()
            : now()->startOfDay();
        $periodEnd = isset($validated['end_date'])
            ? Carbon::parse($validated['end_date'])->endOfDay()
            : $periodStart->copy()->addDays(13)->endOfDay();

        if ($periodEnd->lt($periodStart)) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal akhir tidak boleh lebih kecil dari tanggal awal.',
                'data' => [],
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 422,
                ],
            ], 422);
        }

        if ($periodStart->diffInDays($periodEnd) > 13) {
            return response()->json([
                'success' => false,
                'message' => 'Rentang export-ingest maksimal 14 hari per request.',
                'data' => [],
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 422,
                ],
            ], 422);
        }

        $dapur = DB::table('tb_data_dapur')
            ->select('ip_dapur', 'nama_dapur')
            ->where('id', 1)
            ->first();

        if (!$dapur) {
            return response()->json([
                'success' => true,
                'message' => 'Data dapur belum tersedia.',
                'data' => [],
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 200,
                    'count' => 0,
                ],
            ], 200);
        }

        $hasGolonganColumn = Schema::hasColumn('tb_menu', 'golongan');
        $hasIdSatuan = Schema::hasColumn('rincian_menu_harian', 'id_satuan');
        $hasHarga = Schema::hasColumn('rincian_menu_harian', 'harga');
        $hasTotalHarga = Schema::hasColumn('rincian_menu_harian', 'total_harga');

        $menuSelect = [
            'm.id',
            'm.menu as nama_menu',
            'm.tanggal_kirim as tanggal_pelayanan',
        ];

        if ($hasGolonganColumn) {
            $menuSelect[] = 'm.golongan';
        }

        $menus = DB::table('tb_menu as m')
            ->leftJoin('api_export_ingest_logs as l', 'l.menu_id', '=', 'm.id')
            ->where('m.status_pengajuan', '!=', 'rejected')
            ->whereBetween('m.tanggal_kirim', [
                $periodStart->toDateString(),
                $periodEnd->toDateString(),
            ])
            ->where(function ($query) {
                $query->whereNull('l.menu_id')
                    ->orWhereNull('l.acknowledged_at');
            })
            ->orderBy('m.tanggal_kirim')
            ->orderBy('m.id')
            ->limit($limit)
            ->get(array_merge($menuSelect, [
                'l.export_ref',
                'l.pulled_at',
                'l.last_polled_at',
                'l.acknowledged_at',
            ]));

        $payload = [];
        $logRows = [];
        $now = now();

        foreach ($menus as $menu) {
            $exportRef = $menu->export_ref ?: (string) Str::uuid();
            $tanggalPelayanan = Carbon::parse($menu->tanggal_pelayanan);
            $tanggalKirimBahan = $tanggalPelayanan->toDateString();

            $jumlahPm = (int) DB::table('rincian_sekolah')
                ->where('id_menu_harian', $menu->id)
                ->sum('jumlah_penerima_total');

            $sumA = (int) DB::table('rincian_sekolah')
                ->where('id_menu_harian', $menu->id)
                ->sum('jumlah_penerima_a');

            $sumB = (int) DB::table('rincian_sekolah')
                ->where('id_menu_harian', $menu->id)
                ->sum('jumlah_penerima_b');

            $golongan = 'A';
            if ($hasGolonganColumn && !empty($menu->golongan)) {
                $golongan = (string) $menu->golongan;
            } elseif ($sumA <= 0 && $sumB > 0) {
                $golongan = 'B';
            } elseif ($sumA > 0 && $sumB > 0) {
                $golongan = 'A/B';
            }

            $bahanQuery = DB::table('rincian_menu_harian as rmh')
                ->join('tb_master_bahan as b', 'b.id', '=', 'rmh.id_bahan')
                ->leftJoin('tb_resep as r', 'r.id', '=', 'rmh.id_resep')
                ->where('rmh.id_menu_harian', $menu->id)
                ->select(
                    'r.nama_resep as resep',
                    'b.bahan as nama_bahan',
                    'rmh.jumlah as jumlah_bahan'
                );

            if ($hasIdSatuan) {
                $bahanQuery->leftJoin('tb_satuan as s', 's.id', '=', 'rmh.id_satuan')
                    ->addSelect(DB::raw("COALESCE(s.satuan, '-') as satuan_bahan"));
            } else {
                $bahanQuery->addSelect(DB::raw("'-' as satuan_bahan"));
            }

            if ($hasHarga) {
                $bahanQuery->addSelect('rmh.harga as harga_satuan');
            } else {
                $bahanQuery->addSelect(DB::raw('0 as harga_satuan'));
            }

            if ($hasTotalHarga) {
                $bahanQuery->addSelect('rmh.total_harga as total_harga_bahan');
            } else {
                $bahanQuery->addSelect(DB::raw('0 as total_harga_bahan'));
            }

            $bahanRows = $bahanQuery->orderBy('rmh.id')->get();

            foreach ($bahanRows as $row) {
                $payload[] = [
                    'ip_dapur' => (string) ($dapur->ip_dapur ?? ''),
                    'nama_dapur' => (string) ($dapur->nama_dapur ?? ''),
                    'nama_menu' => (string) $menu->nama_menu,
                    'tanggal_pelayanan' => $tanggalPelayanan->toDateString(),
                    'tanggal_kirim_bahan_baku' => $tanggalKirimBahan,
                    'jumlah_pm' => $jumlahPm,
                    'golongan' => $golongan,
                    'resep' => (string) ($row->resep ?? '-'),
                    'nama_bahan' => (string) $row->nama_bahan,
                    'jumlah_bahan' => (float) $row->jumlah_bahan,
                    'satuan_bahan' => (string) $row->satuan_bahan,
                    'harga_satuan' => (float) $row->harga_satuan,
                    'total_harga_bahan' => (float) $row->total_harga_bahan,
                    'tanggal_kirim' => $tanggalKirimBahan,
                    'menu_id' => (int) $menu->id,
                    'export_ref' => $exportRef,
                ];
            }

            $logRows[] = [
                'menu_id' => $menu->id,
                'export_ref' => $exportRef,
                'pulled_at' => $menu->pulled_at ?: $now,
                'last_polled_at' => $now,
                'exported_at' => $now,
                'updated_at' => $now,
                'created_at' => $now,
            ];
        }

        if (!empty($logRows)) {
            DB::table('api_export_ingest_logs')->upsert(
                $logRows,
                ['menu_id'],
                ['export_ref', 'pulled_at', 'last_polled_at', 'exported_at', 'updated_at']
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Data export ingest periode 2 minggu berhasil diambil.',
            'data' => $payload,
            'meta' => [
                'timestamp' => now()->toIso8601String(),
                'code' => 200,
                'count' => count($payload),
                'start_date' => $periodStart->toDateString(),
                'end_date' => $periodEnd->toDateString(),
                'period_days' => 14,
                'ack_endpoint' => url('/api/v1/export-ingest/ack'),
            ],
        ], 200);
    }

    public function acknowledge(Request $request)
    {
        if ($response = $this->authorizeRequest($request)) {
            return $response;
        }

        $payload = $request->validate([
            'export_refs' => ['nullable', 'array', 'min:1'],
            'export_refs.*' => ['string', 'max:100'],
            'menu_ids' => ['nullable', 'array', 'min:1'],
            'menu_ids.*' => ['integer'],
        ]);

        $exportRefs = $payload['export_refs'] ?? [];
        $menuIds = $payload['menu_ids'] ?? [];

        if (empty($exportRefs) && empty($menuIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Minimal kirim export_refs atau menu_ids untuk ack.',
                'data' => null,
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 422,
                ],
            ], 422);
        }

        $query = DB::table('api_export_ingest_logs')->whereNull('acknowledged_at');

        $query->where(function ($builder) use ($exportRefs, $menuIds) {
            if (!empty($exportRefs)) {
                $builder->whereIn('export_ref', $exportRefs);
            }

            if (!empty($menuIds)) {
                if (!empty($exportRefs)) {
                    $builder->orWhereIn('menu_id', $menuIds);
                } else {
                    $builder->whereIn('menu_id', $menuIds);
                }
            }
        });

        $affected = $query->update([
            'acknowledged_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ack export ingest berhasil disimpan.',
            'data' => [
                'acknowledged_count' => $affected,
            ],
            'meta' => [
                'timestamp' => now()->toIso8601String(),
                'code' => 200,
            ],
        ], 200);
    }
}
