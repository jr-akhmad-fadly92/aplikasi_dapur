<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\office\PoController;
use App\Http\Controllers\office\TbMasterMenuController;
use App\Http\Controllers\office\RincianMenuTempController;
use App\Http\Controllers\office\MasterBahanNutrisiController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ExportIngestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// kirim po untuk di acc (hanya untuk pengguna terautentikasi via token)
Route::middleware(['auth:api', 'throttle:30,1'])->post('/api-pengajuan-po', [PoController::class, 'api_pengajuan_po'])->name('api_pengajuan_po.store');
//=========================
use App\Http\Controllers\ApiTestController;
use App\Http\Controllers\TestKitchenController;
Route::get('/test-send-menu-hari-ini', [TestKitchenController::class, 'sendMenuHariIni']);

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('auth.login');

    Route::middleware(['auth:api', 'throttle:60,1'])->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    });

    Route::get('/health', function () {
        return response()->json([
            'success' => true,
            'message' => 'API v1 is ready.',
            'data' => [
                'version' => 'v1',
                'timestamp' => now()->toDateTimeString(),
            ],
        ]);
    })->middleware('api.cache:300')->name('health');

    Route::get('/export-ingest', [ExportIngestController::class, 'index'])
        ->middleware('throttle:30,1')
        ->name('export-ingest');

    Route::post('/export-ingest/ack', [ExportIngestController::class, 'acknowledge'])
        ->middleware('throttle:30,1')
        ->name('export-ingest.ack');

    // Rekap sekolah dan ringkasan menu harian
    Route::get('/menu/{id_menu}/rekap-sekolah', [TbMasterMenuController::class, 'apiRekapSekolah'])
        ->middleware('api.cache:600')
        ->name('menu.rekap-sekolah');
    
    Route::get('/menu/{id_menu}/total-pax', [TbMasterMenuController::class, 'ajaxTotalPax'])
        ->middleware('api.cache:300')
        ->name('menu.total-pax');
    
    Route::get('/menu/{id_menu}/rincian-bahan', [TbMasterMenuController::class, 'apiRincianBahan'])
        ->middleware('api.cache:600')
        ->name('menu.rincian-bahan');

    // Operasi sekolah di menu
    Route::get('/sekolah/aktif', [TbMasterMenuController::class, 'ajaxSekolahAktifList'])
        ->middleware('api.cache:3600')
        ->name('sekolah.aktif');
    
    Route::middleware(['auth:api', 'throttle:60,1'])->group(function () {
        Route::post('/menu/sekolah/tambah', [TbMasterMenuController::class, 'tambahSekolahManual'])->name('menu.sekolah.tambah');
        Route::post('/menu/sekolah/update-pax', [TbMasterMenuController::class, 'updateJumlahSekolah'])->name('menu.sekolah.update-pax');
        Route::post('/menu/sekolah/hapus', [TbMasterMenuController::class, 'hapusRincianSekolah'])->name('menu.sekolah.hapus');

        // Operasi rincian bahan
        Route::post('/menu/rincian-bahan/update-jumlah', [RincianMenuTempController::class, 'update_jumlah_rincian_bahan'])->name('menu.rincian-bahan.update-jumlah');
        Route::post('/menu/rincian-bahan/update-jumlah-box', [RincianMenuTempController::class, 'update_jumlah_box'])->name('menu.rincian-bahan.update-jumlah-box');
        Route::post('/menu/rincian-bahan/update-keterangan', [RincianMenuTempController::class, 'update_keterangan'])->name('menu.rincian-bahan.update-keterangan');
        Route::post('/menu/rincian-bahan/update-harga', [RincianMenuTempController::class, 'update_harga'])->name('menu.rincian-bahan.update-harga');
        Route::post('/menu/rincian-bahan/update-sekolah', [RincianMenuTempController::class, 'update_sekolahb'])->name('menu.rincian-bahan.update-sekolah');

        // Kalkulasi nutrisi dari master bahan
        Route::post('/master-bahan-nutrisi/hitung', [MasterBahanNutrisiController::class, 'apiHitungNutrisi'])->name('master-bahan-nutrisi.hitung');
        Route::post('/master-bahan-nutrisi/detail', [MasterBahanNutrisiController::class, 'apiGetBahanDetail'])->name('master-bahan-nutrisi.detail');
        Route::get('/master-bahan-nutrisi/list', [MasterBahanNutrisiController::class, 'apiListBahan'])->name('master-bahan-nutrisi.list');
    });
});
