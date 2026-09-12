<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use App\Models\sekolahDapodik;
use Illuminate\Support\Facades\DB;
use App\Exports\laporanPersiapanExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Svg\Tag\Rect;

class testController extends Controller
{
    public function nutritionAiStatus(Request $request)
    {
        $url = (string) config('services.nutrition_ai.url');
        $apiKey = (string) config('services.nutrition_ai.api_key');
        $model = (string) config('services.nutrition_ai.model');
        $timeout = (int) config('services.nutrition_ai.timeout', 60);
        $host = (string) parse_url($url, PHP_URL_HOST);
        $requiresApiKey = !in_array($host, ['127.0.0.1', 'localhost'], true);

        $status = [
            'configured' => $url !== '' && $model !== '' && (!$requiresApiKey || $apiKey !== ''),
            'url' => $url,
            'model' => $model,
            'timeout' => $timeout,
            'requires_api_key' => $requiresApiKey,
            'api_key_present' => $apiKey !== '',
            'api_key_masked' => $apiKey !== '' ? substr($apiKey, 0, 7) . '...' . substr($apiKey, -4) : null,
        ];

        if (!$request->boolean('check', true)) {
            return response()->json([
                'success' => true,
                'status' => $status,
            ]);
        }

        if (!$status['configured']) {
            return response()->json([
                'success' => false,
                'status' => $status,
                'provider_status' => 'not_configured',
                'message' => 'Konfigurasi AI nutrisi belum lengkap.',
            ], 422);
        }

        try {
            $requestBuilder = Http::timeout($timeout)->acceptJson();
            if ($requiresApiKey) {
                $requestBuilder = $requestBuilder->withToken($apiKey);
            }

            $response = $requestBuilder->post($url, [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'Reply with short JSON only.'],
                    ['role' => 'user', 'content' => '{"healthcheck":true}'],
                ],
            ]);

            if ($response->failed()) {
                $errorCode = (string) data_get($response->json(), 'error.code', '');
                $errorMessage = (string) data_get($response->json(), 'error.message', '');

                return response()->json([
                    'success' => false,
                    'status' => $status,
                    'provider_status' => $errorCode !== '' ? $errorCode : 'http_error',
                    'message' => $errorMessage !== '' ? $errorMessage : 'Provider AI mengembalikan error.',
                    'http_status' => $response->status(),
                ], 422);
            }

            return response()->json([
                'success' => true,
                'status' => $status,
                'provider_status' => 'ok',
                'message' => 'Koneksi AI nutrisi berhasil.',
                'reply_preview' => (string) data_get($response->json(), 'choices.0.message.content', ''),
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'status' => $status,
                'provider_status' => 'exception',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    //
    public function convert(Request $request)
    {
        //$amount = $request->input('amount');
        //$unit = $request->input('unit');
        //$ingredient = $request->input('ingredient');
        $amount = 1;
        $unit = 'liter';
        $ingredient = 'rice';

        // Data konversi lokal jika tidak ada internet
        $conversion_factors = [
            // Bahan Pokok
            'rice' => ['grams' => 1, 'cooked' => 2.2],
            'flour' => ['grams' => 1, 'cooked' => 1.0],
            'pasta' => ['grams' => 1, 'cooked' => 2.5],
            'potato' => ['grams' => 1, 'cooked' => 1.3],
            'corn' => ['grams' => 1, 'cooked' => 1.2],

            // Sayuran
            'carrot' => ['grams' => 1, 'cooked' => 0.9],
            'broccoli' => ['grams' => 1, 'cooked' => 0.85],
            'spinach' => ['grams' => 1, 'cooked' => 0.75],
            'cabbage' => ['grams' => 1, 'cooked' => 0.8],

            // Daging & Protein
            'chicken' => ['grams' => 1, 'cooked' => 0.75],
            'beef' => ['grams' => 1, 'cooked' => 0.7],
            'fish' => ['grams' => 1, 'cooked' => 0.8],
            'tofu' => ['grams' => 1, 'cooked' => 0.95],
            'tempeh' => ['grams' => 1, 'cooked' => 0.95],

            // Bumbu Dapur
            'onion' => ['grams' => 1, 'cooked' => 0.9],
            'garlic' => ['grams' => 1, 'cooked' => 0.95],
            'ginger' => ['grams' => 1, 'cooked' => 0.95],
            'chili' => ['grams' => 1, 'cooked' => 0.9],

            // Satuan Umum
            'tbsp' => ['grams' => 15], // 1 sdm ke gram
            'tsp' => ['grams' => 5], // 1 sdt ke gram
            'cup' => ['grams' => 240], // 1 cup ke gram (rata-rata)
            'ml' => ['grams' => 1], // 1 ml air setara 1 gram
        ];

        if (array_key_exists(strtolower($ingredient), $conversion_factors)) {
            $grams = $amount * $conversion_factors[strtolower($ingredient)]['grams'];
            $cooked = $grams * $conversion_factors[strtolower($ingredient)]['cooked'] ?? $grams;
            $result = "$amount $unit of $ingredient is approximately $grams grams and $cooked grams when cooked.";
        } elseif (array_key_exists(strtolower($unit), $conversion_factors)) {
            $grams = $amount * $conversion_factors[strtolower($unit)]['grams'];
            $result = "$amount $unit is approximately $grams grams.";
        } else {
            try {
                $response = Http::withToken((string) config('services.nutrition_ai.api_key'))
                    ->acceptJson()
                    ->post((string) config('services.nutrition_ai.url'), [
                    'model' => (string) config('services.nutrition_ai.model', 'gpt-4'),
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a food weight conversion assistant.'],
                        ['role' => 'user', 'content' => "Convert $amount $unit of $ingredient to grams and also to cooked weight."]
                    ]
                ]);

                $result = $response->json()['choices'][0]['message']['content'] ?? 'Conversion failed.';
            } catch (\Exception $e) {
                $result = 'Conversion failed due to no internet connection, and the ingredient is not in local database.';
            }
        }

        return $result;
    }

    public function handle()
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);
        $page = 1;
        $perPage = 4000; // Bisa kamu ubah sesuai kebutuhan
        $totalInserted = 0;
        $totalSkipped = 0;
        $logs = [];

        do {
            $url = "https://api-sekolah-indonesia.vercel.app/sekolah?page={$page}&perPage={$perPage}";
            $response = Http::get($url);

            if (!$response->successful()) {
                $logs[] = "Gagal fetch halaman {$page}";
                break;
            }

            $json = $response->json();
            $data = $json['dataSekolah'] ?? [];

            if (empty($data)) {
                $logs[] = "Tidak ada data di halaman {$page}, proses dihentikan.";
                break;
            }

            $batch = [];
            $skipped = 0;

            foreach ($data as $item) {
                if (!isset($item['npsn'])) {
                    $skipped++;
                    continue;
                }

                $batch[] = [
                    'npsn' => $item['npsn'],
                    'kode_prop' => $item['kode_prop'],
                    'propinsi' => $item['propinsi'],
                    'kode_kab_kota' => $item['kode_kab_kota'],
                    'kabupaten_kota' => $item['kabupaten_kota'],
                    'kode_kec' => $item['kode_kec'],
                    'kecamatan' => $item['kecamatan'],
                    'sekolah' => $item['sekolah'],
                    'bentuk' => $item['bentuk'],
                    'status' => $item['status'],
                    'alamat_jalan' => $item['alamat_jalan'] ?? null,
                    'lintang' => $item['lintang'] ?? null,
                    'bujur' => $item['bujur'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            sekolahDapodik::upsert($batch, ['npsn'], [
                'kode_prop',
                'propinsi',
                'kode_kab_kota',
                'kabupaten_kota',
                'kode_kec',
                'kecamatan',
                'sekolah',
                'bentuk',
                'status',
                'alamat_jalan',
                'lintang',
                'bujur',
                'updated_at'
            ]);

            $inserted = count($batch);
            $logs[] = "📄 Page {$page}: {$inserted} data disimpan, {$skipped} data dilewati.";
            $totalInserted += $inserted;
            $totalSkipped += $skipped;
            $page++;
        } while (true);

        return response()->json([
            'success' => true,
            'message' => '🎉 Proses selesai.',
            'total_data_disimpan' => $totalInserted,
            'total_data_dilewati' => $totalSkipped,
            'log' => $logs
        ]);
    }

    public function testlaporan(Request $request)
    {
        $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');

        $menu = Menu::with([
            'rincianMenuKarbohidrat.tbPoBahan.tbPenerimaan.warehouseTransaksi' => function ($query) {
                $query->select(
                    DB::raw('SUM(jumlah) as jumlah_masuk'),
                    DB::raw('count(jumlah) as box_masuk'),
                    DB::raw('SUM(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as jumlah_keluar'),
                    DB::raw('COUNT(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as box_keluar'),
                    'id_penerimaan',
                    'warehouse_transaksi.id_satuan', // Mengambil id_satuan untuk LEFT JOIN dengan tabel satuan
                    'satuan.satuan' // Mengambil nama_satuan dari tabel satuan
                )
                    ->leftJoin('tb_satuan as satuan', 'warehouse_transaksi.id_satuan', '=', 'satuan.id') // LEFT JOIN dengan tabel satuan
                    ->groupBy('id_penerimaan', 'warehouse_transaksi.id_satuan', 'satuan.satuan'); // Kelompokkan berdasarkan id_penerimaan dan id_satuan
            },
            'rincianMenuProtein.tbPoBahan.tbPenerimaan.warehouseTransaksi' => function ($query) {
                $query->select(
                    DB::raw('SUM(jumlah) as jumlah_masuk'),
                    DB::raw('count(jumlah) as box_masuk'),
                    DB::raw('SUM(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as jumlah_keluar'),
                    DB::raw('COUNT(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as box_keluar'),
                    'id_penerimaan',
                    'warehouse_transaksi.id_satuan', // Mengambil id_satuan untuk LEFT JOIN dengan tabel satuan
                    'satuan.satuan' // Mengambil nama_satuan dari tabel satuan
                )
                    ->leftJoin('tb_satuan as satuan', 'warehouse_transaksi.id_satuan', '=', 'satuan.id') // LEFT JOIN dengan tabel satuan
                    ->groupBy('id_penerimaan', 'warehouse_transaksi.id_satuan', 'satuan.satuan'); // Kelompokkan berdasarkan id_penerimaan dan id_satuan
            },
            'rincianMenuSayur.tbPoBahan.tbPenerimaan.warehouseTransaksi' => function ($query) {
                $query->select(
                    DB::raw('SUM(jumlah) as jumlah_masuk'),
                    DB::raw('count(jumlah) as box_masuk'),
                    DB::raw('SUM(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as jumlah_keluar'),
                    DB::raw('COUNT(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as box_keluar'),
                    'id_penerimaan',
                    'warehouse_transaksi.id_satuan', // Mengambil id_satuan untuk LEFT JOIN dengan tabel satuan
                    'satuan.satuan' // Mengambil nama_satuan dari tabel satuan
                )
                    ->leftJoin('tb_satuan as satuan', 'warehouse_transaksi.id_satuan', '=', 'satuan.id') // LEFT JOIN dengan tabel satuan
                    ->groupBy('id_penerimaan', 'warehouse_transaksi.id_satuan', 'satuan.satuan'); // Kelompokkan berdasarkan id_penerimaan dan id_satuan
            },
            'rincianMenuSusu.tbPoBahan.tbPenerimaan.warehouseTransaksi' => function ($query) {
                $query->select(
                    DB::raw('SUM(jumlah) as jumlah_masuk'),
                    DB::raw('count(jumlah) as box_masuk'),
                    DB::raw('SUM(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as jumlah_keluar'),
                    DB::raw('COUNT(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as box_keluar'),
                    'id_penerimaan',
                    'warehouse_transaksi.id_satuan', // Mengambil id_satuan untuk LEFT JOIN dengan tabel satuan
                    'satuan.satuan' // Mengambil nama_satuan dari tabel satuan
                )
                    ->leftJoin('tb_satuan as satuan', 'warehouse_transaksi.id_satuan', '=', 'satuan.id') // LEFT JOIN dengan tabel satuan
                    ->groupBy('id_penerimaan', 'warehouse_transaksi.id_satuan', 'satuan.satuan'); // Kelompokkan berdasarkan id_penerimaan dan id_satuan
            },
            'rincianMenuBuah.tbPoBahan.tbPenerimaan.warehouseTransaksi' => function ($query) {
                $query->select(
                    DB::raw('SUM(jumlah) as jumlah_masuk'),
                    DB::raw('count(jumlah) as box_masuk'),
                    DB::raw('SUM(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as jumlah_keluar'),
                    DB::raw('COUNT(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as box_keluar'),
                    'id_penerimaan',
                    'warehouse_transaksi.id_satuan', // Mengambil id_satuan untuk LEFT JOIN dengan tabel satuan
                    'satuan.satuan' // Mengambil nama_satuan dari tabel satuan
                )
                    ->leftJoin('tb_satuan as satuan', 'warehouse_transaksi.id_satuan', '=', 'satuan.id') // LEFT JOIN dengan tabel satuan
                    ->groupBy('id_penerimaan', 'warehouse_transaksi.id_satuan', 'satuan.satuan'); // Kelompokkan berdasarkan id_penerimaan dan id_satuan
            },
        ])
            ->where('tanggal_kirim', $tanggal)
            ->get();
        //return $menu;
        //return view('test.testLaporan', compact('menu'));
        //return $menu;


        // Fetch data from the database as before

        return Excel::download(new laporanPersiapanExport($tanggal), 'menu_report.xlsx');
    }

    public function v_laporanPersiapan()
    {
        $header = "Laporan Persiapan Menu";
        return view('office.laporan.laporanPersiapan', compact('header'));
    }

    public function ajax_updateLaporanPersiapan(Request $request)
    {
        // Ambil tanggal dari request
        $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
        //return $tanggal;
        $menu = Menu::with([
            'rincianMenuKarbohidrat.tbPoBahan.tbPenerimaan.warehouseTransaksi' => function ($query) {
                $query->select(
                    DB::raw('SUM(jumlah) as jumlah_masuk'),
                    DB::raw('count(jumlah) as box_masuk'),
                    DB::raw('SUM(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as jumlah_keluar'),
                    DB::raw('COUNT(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as box_keluar'),
                    'id_penerimaan',
                    'warehouse_transaksi.id_satuan', // Mengambil id_satuan untuk LEFT JOIN dengan tabel satuan
                    'satuan.satuan' // Mengambil nama_satuan dari tabel satuan
                )
                    ->leftJoin('tb_satuan as satuan', 'warehouse_transaksi.id_satuan', '=', 'satuan.id') // LEFT JOIN dengan tabel satuan
                    ->groupBy('id_penerimaan', 'warehouse_transaksi.id_satuan', 'satuan.satuan'); // Kelompokkan berdasarkan id_penerimaan dan id_satuan
            },
            'rincianMenuProtein.tbPoBahan.tbPenerimaan.warehouseTransaksi' => function ($query) {
                $query->select(
                    DB::raw('SUM(jumlah) as jumlah_masuk'),
                    DB::raw('count(jumlah) as box_masuk'),
                    DB::raw('SUM(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as jumlah_keluar'),
                    DB::raw('COUNT(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as box_keluar'),
                    'id_penerimaan',
                    'warehouse_transaksi.id_satuan', // Mengambil id_satuan untuk LEFT JOIN dengan tabel satuan
                    'satuan.satuan' // Mengambil nama_satuan dari tabel satuan
                )
                    ->leftJoin('tb_satuan as satuan', 'warehouse_transaksi.id_satuan', '=', 'satuan.id') // LEFT JOIN dengan tabel satuan
                    ->groupBy('id_penerimaan', 'warehouse_transaksi.id_satuan', 'satuan.satuan'); // Kelompokkan berdasarkan id_penerimaan dan id_satuan
            },
            'rincianMenuSayur.tbPoBahan.tbPenerimaan.warehouseTransaksi' => function ($query) {
                $query->select(
                    DB::raw('SUM(jumlah) as jumlah_masuk'),
                    DB::raw('count(jumlah) as box_masuk'),
                    DB::raw('SUM(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as jumlah_keluar'),
                    DB::raw('COUNT(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as box_keluar'),
                    'id_penerimaan',
                    'warehouse_transaksi.id_satuan', // Mengambil id_satuan untuk LEFT JOIN dengan tabel satuan
                    'satuan.satuan' // Mengambil nama_satuan dari tabel satuan
                )
                    ->leftJoin('tb_satuan as satuan', 'warehouse_transaksi.id_satuan', '=', 'satuan.id') // LEFT JOIN dengan tabel satuan
                    ->groupBy('id_penerimaan', 'warehouse_transaksi.id_satuan', 'satuan.satuan'); // Kelompokkan berdasarkan id_penerimaan dan id_satuan
            },
            'rincianMenuSusu.tbPoBahan.tbPenerimaan.warehouseTransaksi' => function ($query) {
                $query->select(
                    DB::raw('SUM(jumlah) as jumlah_masuk'),
                    DB::raw('count(jumlah) as box_masuk'),
                    DB::raw('SUM(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as jumlah_keluar'),
                    DB::raw('COUNT(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as box_keluar'),
                    'id_penerimaan',
                    'warehouse_transaksi.id_satuan', // Mengambil id_satuan untuk LEFT JOIN dengan tabel satuan
                    'satuan.satuan' // Mengambil nama_satuan dari tabel satuan
                )
                    ->leftJoin('tb_satuan as satuan', 'warehouse_transaksi.id_satuan', '=', 'satuan.id') // LEFT JOIN dengan tabel satuan
                    ->groupBy('id_penerimaan', 'warehouse_transaksi.id_satuan', 'satuan.satuan'); // Kelompokkan berdasarkan id_penerimaan dan id_satuan
            },
            'rincianMenuBuah.tbPoBahan.tbPenerimaan.warehouseTransaksi' => function ($query) {
                $query->select(
                    DB::raw('SUM(jumlah) as jumlah_masuk'),
                    DB::raw('count(jumlah) as box_masuk'),
                    DB::raw('SUM(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as jumlah_keluar'),
                    DB::raw('COUNT(CASE WHEN status = 1 THEN jumlah ELSE 0 END) as box_keluar'),
                    'id_penerimaan',
                    'warehouse_transaksi.id_satuan', // Mengambil id_satuan untuk LEFT JOIN dengan tabel satuan
                    'satuan.satuan' // Mengambil nama_satuan dari tabel satuan
                )
                    ->leftJoin('tb_satuan as satuan', 'warehouse_transaksi.id_satuan', '=', 'satuan.id') // LEFT JOIN dengan tabel satuan
                    ->groupBy('id_penerimaan', 'warehouse_transaksi.id_satuan', 'satuan.satuan'); // Kelompokkan berdasarkan id_penerimaan dan id_satuan
            },
        ])
            ->where('tanggal_kirim', $tanggal)
            ->get();
        //return $menu;

        // Kembalikan data laporan dalam format HTML atau JSON
        return view('test.testLaporan', compact('menu'));
    }
}
