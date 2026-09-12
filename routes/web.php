<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\PackagingController;
use App\Http\Controllers\InspektoController;
use App\Http\Controllers\PengadaanController;
use App\Http\Controllers\GiziController;

use App\Http\Controllers\office\DataSekolahController;


use App\Http\Controllers\ManagementController;
use App\Http\Controllers\ApiLogsController;
use App\Http\Controllers\checklistKerjaController;
use App\Http\Controllers\InventoriController;
use App\Http\Controllers\TransaksiInventoriController;
use App\Http\Controllers\omprengController;

use App\Http\Controllers\testController;
use App\Http\Controllers\suratJalanController;

//penerimaan 
use App\Http\Controllers\penerimaan\TbPenerimaanController;

// khusus wadah 
use App\Http\Controllers\penerimaan\TbWadahController;
// khusus wadah 
use App\Http\Controllers\penerimaan\TransaksiWadahController;


//office
use App\Http\Controllers\office\MasterBahanController;
use App\Http\Controllers\office\MasterBahanNutrisiController;
use App\Http\Controllers\office\TbMasterMenuController;
use App\Http\Controllers\office\MenuYayasanController;
use App\Http\Controllers\office\TbResepController;
use App\Http\Controllers\office\PoController;
use App\Http\Controllers\office\PoManualController;
use App\Http\Controllers\office\TbGramasiMenuController;
use App\Http\Controllers\office\PerhitunganBumbuController;
use App\Http\Controllers\office\SpesifikasiBahanController;
use App\Http\Controllers\office\LaporanController;
use App\Http\Controllers\office\BoxBahanBakuController;
use App\Http\Controllers\office\BufferController;
use App\Http\Controllers\office\RekapPOController;
use App\Http\Controllers\office\TbPoCloseController;
use App\Http\Controllers\office\KasKecilTransaksiController;
use App\Http\Controllers\office\RincianMenuTempController;
use App\Http\Controllers\UploadDataController;

use App\Http\Controllers\OmprengManagementController; // anak magang 
use App\Http\Controllers\office\LaporanMenuController; // anak magang 

use App\Http\Controllers\RincianKemasanBahanController;



use App\Http\Controllers\golonganController;

use App\Http\Controllers\office\MasterKandunganGiziController;
use App\Http\Controllers\office\RoleKandunganGiziController;
use App\Http\Controllers\office\KepalaDapurDashboardController;


//use App\Http\Controllers\TbSatuanController;
use App\Http\Controllers\office\TbSatuanController;
use App\Http\Controllers\office\TbSupplierController;
use App\Http\Controllers\office\TingkatanSekolahController;
use App\Http\Controllers\office\CaraMasakController;
use App\Http\Controllers\office\ResepTahapMasakController;
use App\Http\Controllers\office\TbRincianKontrakController;
use App\Http\Controllers\office\TbHargaHetController;
use App\Http\Controllers\office\TbKontrakController;

//kitchen
use App\Http\Controllers\kitchen\KitchenController;
use App\Http\Controllers\warehouseController;
use App\Models\suratJalan;
use App\Models\warehouseTransaksi;
use App\Http\Controllers\kitchen\GramasiController;
use App\Http\Controllers\kitchen\HasilMasakController;
use App\Http\Controllers\kitchen\HistoriMenuController;


// excel 
use App\Exports\KebutuhanDapurExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\ExportContohController;
use App\Http\Controllers\FormChecklistHarianExportController;
use App\Http\Controllers\LaporanHarianDapurExportController;
use App\Http\Controllers\LaporanPoController;

// log penggunaan - masih belum dipake //
Route::resource('/apilogs', \App\Http\Controllers\ApiLogsController::class);
//====================================//



// ==================== LOGIN ================ //
Route::get('/', function () {
    return redirect('/login');
});

route::post('/simpanregistrasi',[LoginController::class,'simpanregistrasi'])->name('simpanregistrasi');
route::get('/login',[LoginController::class,'halamanlogin'])->name('login');
route::post('/postlogin',[LoginController::class,'postlogin'])->name('postlogin');
route::get('/logout',[LoginController::class,'logout'])->name('logout');


Route::group(['middleware' => ['auth', 'ceklevel:backoffice,admin']], function () {
    Route::get('/home', function () {
        return redirect()->route('dashboard_akuntan');
    })->name('home');
});


// ==================================== user ========================================== //
Route::group(['middleware' => ['auth', 'ceklevel:admin']], function () {
    route::get('/registrasi', [LoginController::class, 'registrasi'])->name('registrasi');
    
    Route::resource('/users_crud', \App\Http\Controllers\AdminController::class);
});
// ==================================== user ========================================== //



Route::get('dashboard_office', [HomeController::class, 'v_office'])->name('dashboard_office');
Route::get('dashboard_office/ajax_ompreng_per_line', [HomeController::class, 'ajax_ompreng_per_line'])->name('dashboard_office.ajax_ompreng_per_line');
Route::get('/master_bahan_nutrisi/template', [MasterBahanNutrisiController::class, 'downloadTemplate'])->name('master_bahan_nutrisi.template');

// ==================================== pengadaan ========================================== //
Route::group(['middleware' => ['auth', 'ceklevel:backoffice,kepala_dapur,ahli_akuntan']], function () {
    Route::get('/dashboard_kepala_dapur', [KepalaDapurDashboardController::class, 'index'])->name('dashboard_kepala_dapur');

    Route::resource('/master_bahan', \App\Http\Controllers\office\MasterBahanController::class);
    Route::get('/master_bahan/{id}/akg', [MasterBahanController::class, 'akgIndex'])->name('master_bahan.akg.index');
    Route::post('/master_bahan/{id}/akg', [MasterBahanController::class, 'akgStore'])->name('master_bahan.akg.store');
    Route::post('/master_bahan/{id}/akg/{akgId}', [MasterBahanController::class, 'akgUpdate'])->name('master_bahan.akg.update');
    Route::delete('/master_bahan/{id}/akg/{akgId}', [MasterBahanController::class, 'akgDestroy'])->name('master_bahan.akg.destroy');
    Route::get('/master_bahan/delete/{id}', [MasterBahanController::class, 'destroy'])->name('master_bahan.delete');
    Route::get('/pdf_laporan_master_bahan', [MasterBahanController::class, 'pdf_laporan_master_bahan'])->name('pdf_laporan_master_bahan');
    Route::resource('/master_bahan_nutrisi', MasterBahanNutrisiController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::post('/master_bahan_nutrisi/import', [MasterBahanNutrisiController::class, 'importExcel'])->name('master_bahan_nutrisi.import');
    Route::get('/master_bahan_nutrisi_calculator', function() {
        return view('office.master_bahan_nutrisi.calculator', ['header' => 'Kalkulator Nutrisi Bahan']);
    })->name('master_bahan_nutrisi.calculator');

    // Route::resource('/master_bahan', \App\Http\Controllers\MasterBahanController::class);
    Route::resource('/master_satuan', TbSatuanController::class);
    Route::get('/master_satuan/delete/{id}', [TbSatuanController::class, 'destroy'])->name('master_satuan.delete');

    // ==================================== menu ========================================== //
    Route::get('/mastermenu/print-center', [TbMasterMenuController::class, 'printCenter'])->name('mastermenu.print-center');
    Route::get('/mastermenu/get-checklist-po-id', [TbMasterMenuController::class, 'getChecklistPoId'])->name('mastermenu.get-checklist-po-id');
    Route::resource('/mastermenu', \App\Http\Controllers\office\TbMasterMenuController::class);
    // [2026-04-01] Added route: menu_yayasan resource (isolated from mastermenu)
    Route::resource('/menu_yayasan', MenuYayasanController::class);
    // [2026-04-01] Added route: menu_yayasan delete shortcut
    Route::get('/menu_yayasan/delete/{id}', [MenuYayasanController::class, 'destroy'])->name('menu_yayasan.delete');
    // [2026-04-01] Added route: menu_yayasan cancel confirm
    Route::post('/menu_yayasan/cancel-confirm/{id}', [MenuYayasanController::class, 'cancelConfirm'])->name('menu_yayasan.cancel-confirm');
    // [2026-04-01] Added route: menu_yayasan acc
    Route::post('/menu_yayasan/acc/{id}', [MenuYayasanController::class, 'acc'])->name('menu_yayasan.acc');
    Route::get('/mastermenu/delete/{id}', [TbMasterMenuController::class, 'destroy'])->name('mastermenu.delete');
    Route::get('/pdf_pengajuan_menu/{id}', [TbMasterMenuController::class, 'pdf_pengajuan_menu'])->name('pdf_pengajuan_menu');

    //cancel menu
    Route::get('/mastermenu/cancel/{id}', [TbMasterMenuController::class, 'cancel'])->name('mastermenu.cancel');
    Route::post('/mastermenu/cancel-confirm/{id}', [TbMasterMenuController::class, 'cancelConfirm'])->name('mastermenu.cancel-confirm');

    Route::post('/mastermenu/acc/{id}', [TbMasterMenuController::class, 'acc'])->name('mastermenu.acc');
    //Route::delete('/mastermenu/{id}', [TbMasterMenuController::class, 'destroy'])->name('mastermenu.destroy');


    //end menu

    // ==================================== supplier ========================================== //
    Route::resource('/mastersupplier', \App\Http\Controllers\office\TbSupplierController::class);
    Route::get('/mastersupplier', [TbSupplierController::class, 'index'])->name('mastersupplier.index');
    Route::get('/mastersupplier/delete/{id}', [TbSupplierController::class, 'destroy'])->name('mastersupplier.delete');
    // end supplier

    // ==================================== tingkat sekolah ========================================== //
    Route::resource('/tingkatansekolah', \App\Http\Controllers\office\TingkatanSekolahController::class);
    Route::get('/tingkatansekolah', [TingkatanSekolahController::class, 'index'])->name('tingkatansekolah.index');
    Route::get('/tingkatansekolah/delete/{id}', [TingkatanSekolahController::class, 'destroy'])->name('tingkatansekolah.delete');
    // end tingkatan

    // dashboard
    // ==================================== resep ========================================== //
    Route::resource('/resep', \App\Http\Controllers\office\TbResepController::class);
    Route::get('/resep/delete/{id}', [TbResepController::class, 'destroy'])->name('resep.delete');
    Route::get('/detailresep/{id}', [TbResepController::class, 'detailresep'])->name('detailresep.index');
    Route::get('/resep/{id}/realisasi-akg', [TbResepController::class, 'realisasiAkg'])->name('resep.realisasi-akg.index');
    Route::post('/resep/{id}/realisasi-akg', [TbResepController::class, 'storeRealisasiAkg'])->name('resep.realisasi-akg.store');
    Route::delete('/resep/{id}/realisasi-akg/{realisasiId}', [TbResepController::class, 'destroyRealisasiAkg'])->name('resep.realisasi-akg.destroy');
    Route::get('/tambah_detail_resep/{id}', [TbResepController::class, 'tambahbahan'])->name('detailresep.tambah_detail_resep');
    Route::get('/edit_detail_resep/{id}', [TbResepController::class, 'editbahan'])->name('detailresep.edit_detail_resep');
    Route::post('/prosestambahbahan', [TbResepController::class, 'storetambahbahan'])->name('prosestambahbahan');
    Route::post('/proseseditbahan', [TbResepController::class, 'edittambahbahan'])->name('proseseditbahan');
    Route::get('/prosesdeletebahan/{id}', [TbResepController::class, 'deletedetailbahan'])->name('prosesdeletebahan');
    Route::get('/resep/{id}/tahap-masak', [ResepTahapMasakController::class, 'index'])->name('resep.tahap-masak.index');
    Route::post('/resep/{id}/tahap-masak', [ResepTahapMasakController::class, 'store'])->name('resep.tahap-masak.store');
    Route::get('/resep/{id}/tahap-masak/{tahapMasak}/edit', [ResepTahapMasakController::class, 'edit'])->name('resep.tahap-masak.edit');
    Route::put('/resep/{id}/tahap-masak/{tahapMasak}', [ResepTahapMasakController::class, 'update'])->name('resep.tahap-masak.update');
    Route::delete('/resep/{id}/tahap-masak/{tahapMasak}', [ResepTahapMasakController::class, 'destroy'])->name('resep.tahap-masak.destroy');



    // ============================== cara masak ========================= // 
    Route::get('/caramasak', [CaraMasakController::class, 'index'])->name('caramasak.index');
    Route::get('/caramasak/create', [CaraMasakController::class, 'create'])->name('caramasak.create');
    Route::post('/caramasak', [CaraMasakController::class, 'store'])->name('caramasak.store');


    //data dapur
    Route::resource('/datadapur', \App\Http\Controllers\office\DataDapurController::class);
    //end data dapur

    //bahan masak
    Route::resource('/bahanmasak', \App\Http\Controllers\kitchen\BahanMasakController::class);
    //end bahan masak

    //stok gudang
    Route::resource('/stokgudang', \App\Http\Controllers\warehouse\StokGudangController::class);
    //end stok gudang

    //menu bahan
    route::resource('/menubahan', \App\Http\Controllers\MenuBahanCOntroller::class);
    //end menu bahan


    // rincian menu harian

    Route::get('/rincian_menu_harian/{idmenu}', [TbMasterMenuController::class, 'index_rincian_menu_harian']);
    //Route::get('/tambahan_rincian_menu_harian/{idmenu}', [TbMasterMenuController::class, 'tambahan_rincian_menu_harian']);
    Route::post('/rincian_menu_harian//{idmenu}', [TbMasterMenuController::class, 'store_rincian_menu_harian']);
    Route::put('/rincian_menu_harian/{idmenu}/{idrincian}', [TbMasterMenuController::class, 'update_rincian_menu_harian']);
    Route::delete('/rincian_menu_harian/{idmenu}/{idrincian}', [TbMasterMenuController::class, 'destroy_rincian_menu_harian']);
    Route::post('/tambahan_rincian_menu_harian/store', [TbMasterMenuController::class, 'tambahan_rincian_menu_harian'])->name('tambahan_rincian_menu_harian.store');

    // end menu harian

    // rincian sekolah harian
    Route::get('/rincian_sekolah_harian/{idmenu}', [TbMasterMenuController::class, 'index_rincian_sekolah_harian'])->name('rincian_sekolah_harian');
    Route::post('/rincian_sekolah/update-jumlah', [TbMasterMenuController::class, 'updateJumlahSekolah'])->name('rincian_sekolah.update_jumlah');
    Route::get('/update_rincian_sekolah_harian/{idmenu}', [TbMasterMenuController::class, 'updateRincianSekolah'])->name('update_rincian_sekolah_harian');
    //Route::post('/update_rincian_sekolah_harian/{idmenu}', [TbMasterMenuController::class, 'updateRincianSekolah'])->name('update_rincian_sekolah_harian');

    Route::get('/rincian_bahan/{idmenu}', [TbMasterMenuController::class, 'rincian_bahan'])->name('rincian_bahan');
    Route::post('/rincian_bahan/update-jumlah', [TbMasterMenuController::class, 'update_jumlah_rincian_bahan'])->name('rincian_bahan.update_jumlah');
    Route::post('/rincian_bahan/update-jumlah-masak', [TbMasterMenuController::class, 'update_jumlah_masak'])->name('rincian_bahan.update_jumlah_masak');
    Route::get('/nutrition-ai/status', [testController::class, 'nutritionAiStatus'])->name('nutrition_ai.status');

    // rincian sekolah harian



    //---------------------start sekolah-------------------------
    //Route::resource('/datasekolah', \App\Http\Controllers\office\DataSekolahController::class);
    Route::get('datasekolah', [DataSekolahController::class, 'index'])->name('datasekolah.index');
    Route::get('datasekolah/create', [DataSekolahController::class, 'v_formDataSekolah'])->name('datasekolah.create');
    Route::get('datasekolah/edit/{id}', [DataSekolahController::class, 'v_formDataSekolah'])->name('datasekolah.edit');
    Route::post('datasekolah/ajax_simpanDataSekolah', [DataSekolahController::class, 'ajax_simpanDataSekolah'])->name('datasekolah.ajax_simpanDataSekolah');
    Route::get('datasekolah/dt_dataSekolah', [DataSekolahController::class, 'dt_dataSekolah'])->name('datasekolah.dt_dataSekolah');
    Route::get('detailSekolah-{id}', [DataSekolahController::class, 'v_detailSekolah'])->name('detailSekolah.index');
    Route::get('detailSekolah/dt_dataSiswa', [DataSekolahController::class, 'dt_dataSiswa'])->name('detailSekolah.dt_dataSiswa');
    Route::get('detailSekolah/form_dataSiswa', [DataSekolahController::class, 'v_formDataSiswa'])->name('detailSekolah.form_dataSiswa');
    Route::post('detailSekolah/ajax_simpanSiswa', [DataSekolahController::class, 'ajax_simpanSiswa'])->name('detailSekolah.ajax_simpanDataSiswa');
    Route::get('detailSekolah/form_hariAktifSekolah', [DataSekolahController::class, 'v_formHariAktifSekolah'])->name('detailSekolah.v_formHariAktifSekolah');
    Route::post('detailSekolah/ajax_simpanHariAktifSekolah', [DataSekolahController::class, 'ajax_simpanHariAktifSekolah'])->name('detailSekolah/ajax_simpanHariAktifSekolah');
    //----------------------end sekolah--------------------------


    // =======================Kontrak Supplier========================================= //

    Route::resource('kontrak', TbKontrakController::class);
    Route::get('/kontrak/delete/{id}', [TbKontrakController::class, 'destroy'])->name('kontrak.delete');
    Route::resource('rincian-kontrak', TbRincianKontrakController::class);
    Route::get('/dashboard-rincian-kontrak/{idmenu}', [TbRincianKontrakController::class, 'index'])->name('dashboard-rincian-kontrak');
    Route::get('/rincian-kontrak/{idkontrak}/template-harga', [TbRincianKontrakController::class, 'downloadTemplateHarga'])->name('rincian-kontrak.template-harga');
    Route::post('/rincian-kontrak/{idkontrak}/import-harga', [TbRincianKontrakController::class, 'importTemplateHarga'])->name('rincian-kontrak.import-harga');
    Route::post('/rincian-kontraks/store', [TbRincianKontrakController::class, 'store'])->name('rincian-kontraks.store');
    Route::get('/rincian-kontrak/delete/{id}', [TbRincianKontrakController::class, 'destroy'])->name('rincian-kontrak.delete');

    Route::get('/harga-het', [TbHargaHetController::class, 'index'])->name('harga-het.index');
    Route::post('/harga-het/upsert', [TbHargaHetController::class, 'upsert'])->name('harga-het.upsert');
    Route::get('/harga-het/template', [TbHargaHetController::class, 'downloadTemplate'])->name('harga-het.template');
    Route::post('/harga-het/import', [TbHargaHetController::class, 'importTemplate'])->name('harga-het.import');

    // ==================================== pengajuan PO ====================================== //

    Route::resource('pengajuan_po', PoController::class);
    Route::get('/pengajuan_po_buat_po/{id}', [PoController::class, 'create_po'])
        ->name('pengajuan_po_buat_po');
    Route::post('/pengajuan_po/auto_generate', [PoController::class, 'auto_generate_po'])
        ->name('pengajuan_po.auto_generate_po');
    Route::get('/get-rincian-bahan/{idbahan}', [PoController::class, 'getRincian']);

    //Route::get('/pengajuan_po/rincian/{id}/{id_menu}', [PoController::class, 'rincian_pengajuan_po'])->name('rincian_pengajuan_po');
    //Route::get('/pengajuan_po/rincian/{id}/{id_menu}/{idkontrak}', [PoController::class, 'rincian_pengajuan_po'])->name('rincian_pengajuan_po');;
    Route::get('/pengajuan_po/rincian/{id}/{id_menu}/{id_kontrak}', [PoController::class, 'rincian_pengajuan_po'])->name('rincian_pengajuan_po');

    Route::post('/pengajuan_po/rincian/store', [PoController::class, 'simpan_rincian_pengajuan_po'])->name('simpan_rincian_pengajuan_po');
    Route::get('/pengajuan_po/bulk_pengiriman/{id}/{id_menu}/{id_kontrak}', [PoController::class, 'form_bulk_pengiriman'])->name('pengajuan_po.bulk_pengiriman');
    Route::post('/pengajuan_po/simpan_bulk_pengiriman', [PoController::class, 'simpan_bulk_pengiriman'])->name('pengajuan_po.simpan_bulk_pengiriman');
    Route::get('/pengajuan_po/hapus_po_bahan/{ids}', [PoController::class, 'hapus_po_bahan'])->name('delete_po_bahan');
    Route::get('/pengajuan_po/simpan_draft/{id}/{id_menu}', [PoController::class, 'simpan_draft_pengajuan_po'])->name('simpan_draft_pengajuan_po');
    Route::get('/pdf_pengajuan_po/{id}', [PoController::class, 'pdf_pengajuan_po'])->name('pdf_pengajuan_po');
    Route::get('/pdf_pengajuan_po_2/{id}', [PoController::class, 'pdf_pengajuan_po_tanpa_harga'])->name('pdf_pengajuan_po_tanpa_harga');
    Route::get('/pdf_pengajuan_po_2_lama/{id}', [PoController::class, 'pdf_pengajuan_po_tanpa_harga_lama'])->name('pdf_pengajuan_po_tanpa_harga_lama');
    Route::get('/pdf_pengajuan_po_manual/{id}', [PoController::class, 'pdf_pengajuan_po_manual'])->name('pdf_pengajuan_po_manual');
    Route::get('/docx_pengajuan_po_manual/{id}', [PoController::class, 'docx_pengajuan_po_manual'])->name('docx_pengajuan_po_manual');
    Route::get('/pengajuan_po/delete/{id}', [PoController::class, 'destroy'])->name('pengajuan_po.delete');


    Route::get('pilih_menu_po', [PoController::class, 'pilih_menu'])->name('pilih_menu_po');


    Route::get('rincian_menu_po/{id}', [PoController::class, 'rincian_bahan_menu'])->name('rincian_menu_po');
    Route::get('rincian_menu_po/pilih_supplier/{id}', [PoController::class, 'input_supplier'])->name('rincian_menu_po.pilih_supplier');
    Route::post('rincian_menu_po/simpan_supplier/{id}', [PoController::class, 'simpan_supplier'])->name('rincian_menu_po.simpan_supplier');

    Route::get('simpan_rincian_menu_po/{id}', [PoController::class, 'simpan_rincian_menu_po'])->name('simpan_rincian_menu_po');
    Route::get('rincian_menu_po/form_input_kontrak/{id}', [PoController::class, 'form_input_kontrak'])->name('rincian_menu_po.form_input_kontrak');
    Route::post('rincian_menu_po/simpan_kontrak/{id}', [PoController::class, 'simpan_kontrak_rincian'])->name('rincian_menu_po.simpan_kontrak');
    Route::get('rincian_menu_po/hapus/{id}', [PoController::class, 'hapus_rincian_menu'])->name('rincian_menu_po.hapus');
    Route::post('/update-manual-po', [POController::class, 'updatemanualpo'])->name('update-manual-po');

    Route::get('buat_po_manual', [PoManualController::class, 'buat_po_manual'])->name('buat_po_manual');
    Route::post('draft_po_manual', [PoManualController::class, 'draft_po_manual'])->name('draft_po_manual');
    Route::get('rincian_po_manual/{id}', [PoManualController::class, 'rincian_po_manual'])->name('rincian_po_manual');
    Route::post('tambah_barang_po_manual', [PoManualController::class, 'tambah_barang_po_manual'])->name('tambah_barang_po_manual');
    Route::get('hapus_barang_po_manual/delete/{id}', [PoManualController::class, 'hapus_barang_po_manual'])->name('hapus_barang_po_manual');
    Route::get('get_harga_bahan_po_manual', [PoManualController::class, 'get_harga_bahan'])->name('get_harga_bahan_po_manual');
    Route::post('update_harga_bahan_po_manual', [PoManualController::class, 'update_harga_bahan'])->name('update_harga_bahan_po_manual');


    Route::post('simpan_po_manual', [PoManualController::class, 'simpan_po_manual'])->name('simpan_po_manual');

    
    Route::get('/pengajuan_po/edit/{id}', [PoController::class, 'edit_po'])->name('pengajuan_po.edit_bahan_baku');
    Route::get('/pengajuan_po/edit/dt_bahan_po/{id}', [PoController::class, 'dt_bahan_edit_po']); 
    // Route untuk mengambil data edit
    Route::get('/pengajuan_po//edit/get_data/{id}', [PoController::class, 'get_EditData']);
    // Route untuk memperbarui data
    Route::post('/pengajuan_po/update_data', [PoController::class, 'update_Data_po']);
    // Route untuk menambah data bahan PO
    Route::post('/pengajuan_po/store_data', [PoController::class, 'store_Data_po']);
    // Route untuk menghapus data bahan PO hanya di tb_po_bahan
    Route::delete('/pengajuan_po/delete_data/{id}', [PoController::class, 'destroy_Data_po'])->name('pengajuan_po.delete_data');
    Route::post('/pengajuan_po/delete_data/{id}', [PoController::class, 'destroy_Data_po']);
    Route::get('/pengajuan_po/delete_data/{id}', [PoController::class, 'destroy_Data_po']);

    // ==================================== AI Suggestions ==================================== //
    Route::post('/api/po/ai/suggest-quantity', [\App\Http\Controllers\Office\PoAiController::class, 'suggestQuantity'])->name('po.ai.suggest-quantity');
    Route::post('/api/po/ai/recommend-supplier', [\App\Http\Controllers\Office\PoAiController::class, 'recommendSupplier'])->name('po.ai.recommend-supplier');
    Route::post('/api/po/ai/analyze-pricing', [\App\Http\Controllers\Office\PoAiController::class, 'analyzePricing'])->name('po.ai.analyze-pricing');
    Route::post('/api/po/ai/predict-eta', [\App\Http\Controllers\Office\PoAiController::class, 'predictDeliveryEta'])->name('po.ai.predict-eta');
    // ==================================== END AI Suggestions ==================================== //

    // Upload Data Management
    Route::resource('upload-data', 'App\Http\Controllers\UploadDataController');
    Route::get('/upload-data/{id}/download/{field}', [UploadDataController::class, 'download'])->name('upload-data.download');
    // Route untuk export detail pengajuan menu
    Route::get('/export/detail-pengajuan-menu', [App\Http\Controllers\exports\DetailPengajuanMenuController::class, 'index'])->name('export.detail_pengajuan_menu');
    Route::get('/export/detail-pengajuan-menu/excel/{menuId}', [App\Http\Controllers\exports\DetailPengajuanMenuController::class, 'exportExcel'])->name('export.detail_pengajuan_menu.excel');
    Route::get('/export/detail-pengajuan-menu-2', [App\Http\Controllers\exports\DetailPengajuanMenu2Controller::class, 'index'])->name('export.detail_pengajuan_menu_2');
    Route::get('/export/detail-pengajuan-menu-2/excel/{menuId}', [App\Http\Controllers\exports\DetailPengajuanMenu2Controller::class, 'exportExcel'])->name('export.detail_pengajuan_menu_2.excel');
    Route::get('/upload-data/ajax/get-menus-by-date', [UploadDataController::class, 'getMenusByDate'])->name('upload-data.get-menus-by-date');

   
    // ================================== end menu ======================================== //

});



//Delivery blm dipake
route::get('/dashboard_delivery', [DeliveryController::class, 'index'])->name('dashboard_delivery');
// end Delivery


//packaging 
route::get('/packing', [PackagingController::class, 'index_packing'])->name('packing');
route::get('/riwayat_packaging', [PackagingController::class, 'index_riwayat_packaging'])->name('riwayat_packaging');
//didik
route::get('/packing/formPacking-{porsi}', [PackagingController::class, 'v_formPacking'])->name('packing.create');
route::get('/packing/formPacking', [PackagingController::class, 'v_formPacking'])->name('packing.createDefault');
route::get('/formPacking/dt_formPacking', [PackagingController::class, 'dt_formPacking'])->name('packing.dt_formPacking');
route::match(['get', 'post'], '/formPacking/ajax_scanQR', [PackagingController::class, 'ajax_scanQR'])->name('packing.ajax_scanQR');
Route::get('formPacking/ajax_keepAlive', [PackagingController::class, 'ajax_keepAlive'])->name('packing.ajax_keepAlive');
// end packaging







//--Inventori --//
Route::resource('/inventori', \App\Http\Controllers\InventoriController::class);
Route::get('/download-pdf', [InventoriController::class, 'downloadPDF'])->name('download.pdf');
//-- end Inventori --//

//--Transaksi Inventori --//
Route::resource('/transaksiinventori', \App\Http\Controllers\TransaksiInventoriController::class);
//-- end Transaksi Inventori --//



//-------------------ompreng-------------------------------------
Route::group(['middleware' => ['auth', 'throttle:120,1']], function () {
    Route::get('ompreng', [omprengController::class, 'v_ompreng']);
    Route::get('ompreng/dt_ompreng', [omprengController::class, 'dt_ompreng']);
    Route::get('ompreng/dt_rantang', [omprengController::class, 'dt_rantang']);
    Route::get('ompreng/formOmprengMasuk', [omprengController::class, 'v_formOmprengMasuk'])->name('ompreng.formOmprengMasuk');
    Route::get('formOmprengMasuk/dt_omprengKeluar', [omprengController::class, 'dt_omprengKeluar']);
    Route::post('formOmprengMasuk/ajax_scanQR', [omprengController::class, 'ajax_scanQR']);
    Route::get('ompreng/form_registerOmpreng', [omprengController::class, 'v_formRegisterOmpreng'])->name('ompreng.formRegisterOmpreng');
    Route::get('ompreng/form_registerNomorOmpreng', [omprengController::class, 'v_formRegisterNomorOmpreng'])->name('ompreng.formRegisterNomorOmpreng');
    Route::post('ompreng/ajax_simpanRegisterOmpreng', [omprengController::class, 'ajax_simpanRegisterOmpreng'])->name('ompreng.ajax_simpanRegisterOmpreng');
    Route::get('registerOmpreng', [omprengController::class, 'v_formRegisterOmpreng2'])->name('ompreng.registerOmpreng2');
});
//------------------------end of ompreng-----------------------------

//---------------------start sekolah-------------------------
//Route::resource('/datasekolah', \App\Http\Controllers\office\DataSekolahController::class);
/*Route::get('datasekolah', [DataSekolahController::class, 'index'])->name('datasekolah.index');
Route::get('datasekolah/create', [DataSekolahController::class, 'v_formDataSekolah'])->name('datasekolah.create');
Route::get('datasekolah/edit/{id}', [DataSekolahController::class, 'v_formDataSekolah'])->name('datasekolah.edit');
Route::post('datasekolah/ajax_simpanDataSekolah', [DataSekolahController::class, 'ajax_simpanDataSekolah'])->name('datasekolah.ajax_simpanDataSekolah');
Route::get('datasekolah/dt_dataSekolah', [DataSekolahController::class, 'dt_dataSekolah'])->name('datasekolah.dt_dataSekolah');
Route::get('detailSekolah-{id}', [DataSekolahController::class, 'v_detailSekolah'])->name('detailSekolah.index');
Route::get('detailSekolah/dt_dataSiswa', [DataSekolahController::class, 'dt_dataSiswa'])->name('detailSekolah.dt_dataSiswa');
Route::get('detailSekolah/form_dataSiswa', [DataSekolahController::class, 'v_formDataSiswa'])->name('detailSekolah.form_dataSiswa');
Route::post('detailSekolah/ajax_simpanSiswa', [DataSekolahController::class, 'ajax_simpanSiswa'])->name('detailSekolah.ajax_simpanDataSiswa');
Route::get('detailSekolah/form_hariAktifSekolah', [DataSekolahController::class, 'v_formHariAktifSekolah'])->name('detailSekolah.v_formHariAktifSekolah');
Route::post('detailSekolah/ajax_simpanHariAktifSekolah', [DataSekolahController::class, 'ajax_simpanHariAktifSekolah'])->name('detailSekolah/ajax_simpanHariAktifSekolah');
Route::get('datasekolah/autoComplete', [DataSekolahController::class, 'autoComplete'])->name('datasekolah.autoComplete');
//----------------------end sekolah--------------------------*/

//----------------------start surat jalan--------------------------
Route::group(['middleware' => ['auth', 'throttle:60,1']], function () {
    Route::get('suratJalan/formSuratJalan-{id}', [suratJalanController::class, 'v_formSuratJalan'])->name('suratJalan.formEditSuratJalan');
    Route::get('suratJalan/formSuratJalan', [suratJalanController::class, 'v_formSuratJalan'])->name('suratJalan.formSuratJalan');
    Route::get('suratJalan/formSuratJalanb', [suratJalanController::class, 'v_formSuratJalanB'])->name('suratJalan.formSuratJalanb');
    Route::get('formSuratJalan/dt_dataRincianSekolah', [suratJalanController::class, 'dt_dataRincianSekolah'])->name('dt_dataRincianSekolah');
    Route::get('formSuratJalan/dt_suratJalanItem', [suratJalanController::class, 'dt_suratJalanItem'])->name('dt_suratJalanItem');
    Route::get('formSuratJalan/form_suratJalanItem', [suratJalanController::class, 'form_suratJalanItem'])->name('form_suratJalanItem');
    Route::post('formSuratJalan/ajax_simpanSuratJalanItem', [suratJalanController::class, 'ajax_simpanSuratJalanItem'])->name('ajax_simpanSuratJalanItem');
    Route::post('formSuratJalan/ajax_simpanSuratJalan', [suratJalanController::class, 'ajax_simpanSuratJalan'])->name('ajax_simpanSuratJalan');
    Route::post('formSuratJalan/ajax_pubSuratJalan', [suratJalanController::class, 'ajax_pubSuratJalan'])->name('ajax_pubSuratJalan');
    Route::get('suratJalan/dt_suratJalan', [suratJalanController::class, 'dt_suratJalan'])->name('dt_suratJalan');
    Route::get('suratJalan', [suratJalanController::class, 'v_suratJalan'])->name('suratJalan');
    Route::get('suratJalan/detailSuratJalan-{id}', [suratJalanController::class, 'v_detailSuratJalan'])->name('v_detailSuratJalan');
    Route::get('suratJalan/pdfSuratJalan', [suratJalanController::class, 'pdf_suratJalan'])->name('pdf_suratJalan');
    Route::get('suratJalan/pdfSuratJalanA4', [suratJalanController::class, 'pdf_suratJalanA4'])->name('pdf_suratJalanA4');
    Route::post('formSuratJalan/ajax_deleteSuratJalanItem', [suratJalanController::class, 'ajax_deleteSuratJalanItem'])->name('ajax_deleteSuratJalanItem');
});

//----------------------end surat jalan--------------------------

Route::get('/fetchSekolah', [testController::class, 'handle'])->name('fetchSekolah');
Route::get('/testlaporan', [testController::class, 'testlaporan'])->name('testlaporan');
Route::get('/laporanPersiapan', [testController::class, 'v_laporanPersiapan'])->name('laporanPersiapan');
Route::post('/laporanPersiapan/ajax_updateLaporanPersiapan', [testController::class, 'ajax_updateLaporanPersiapan'])->name('ajax_updateLaporanPersiapan');



//====================== dashboard =================================//
Route::group(['middleware' => ['auth', 'throttle:60,1']], function () {
    // penerimaan
   
    // warehouse
    Route::get('dashboard_warehouse', [HomeController::class, 'v_warehouse'])->name('dashboard_warehouse');
    // kitchen
    Route::get('dashboard_kitchen', [KitchenController::class, 'index'])->name('dashboard_kitchen');
    // Packaging
    Route::get('dashboard_packaging', [HomeController::class, 'v_packaging'])->name('dashboard_packaging');
    // Laboratrium
    Route::get('dashboard_laboratorium', [HomeController::class, 'v_laboratorium'])->name('dashboard_laboratorium');
});

// TV Dashboard (without login)
 Route::get('dashboard_penerimaan', [HomeController::class, 'v_penerimaan'])->name('dashboard_penerimaan');
Route::get('dashboard_tv_penerimaan', [HomeController::class, 'v_penerimaan_tv'])->name('dashboard_tv_penerimaan');
Route::get('dashboard_tv_warehouse', [HomeController::class, 'v_warehouse_tv'])->name('dashboard_tv_warehouse');
Route::get('dashboard_tv_kitchen', [KitchenController::class, 'index_tv'])->name('dashboard_tv_kitchen');
Route::get('dashboard_tv_packaging', [HomeController::class, 'v_packaging_tv'])->name('dashboard_tv_packaging');
Route::get('dashboardTvPackaging/ajax_getData', [HomeController::class, 'ajax_getData'])->name('dashboardTvPackaging.ajax_getData');

//==================================================================//


// ==================================== wadah ==========================================//
Route::group(['middleware' => ['auth', 'throttle:60,1']], function () {
    Route::resource('master_wadah', TbWadahController::class);
    Route::get('/master_wadah/delete/{id}', [TbWadahController::class, 'destroy'])->name('master_wadah.delete');
});

//=====================================================================================//
// ==================================== tb penerimaan ==========================================//
Route::group(['middleware' => ['auth', 'throttle:100,1']], function () {
    // [2026-04-01] Related route: penerimaan_bahan resource (context for laporan penerimaan enhancement)
    Route::resource('penerimaan_bahan', TbPenerimaanController::class);
    // [2026-04-01] Related route: simpan penerimaan bahan
    Route::post('simpan_penerimaan_bahan', [TbPenerimaanController::class, 'simpan_penerimaan_bahan'])->name('simpan_penerimaan_bahan');
    // [2026-04-01] Related route: koreksi penerimaan bahan
    Route::post('koreksi_penerimaan_bahan', [TbPenerimaanController::class, 'koreksi_penerimaan_bahan'])->name('koreksi_penerimaan_bahan');
    // [2026-04-01] Added route: laporan penerimaan update stok gudang
    Route::post('laporan_penerimaan/update-stok-gudang', [TbPenerimaanController::class, 'update_stok_gudang'])->name('laporan_penerimaan.update_stok_gudang');
    // [2026-04-01] Added route: laporan penerimaan update satuan gudang
    Route::post('laporan_penerimaan/update-satuan-gudang', [TbPenerimaanController::class, 'update_satuan_gudang'])->name('laporan_penerimaan.update_satuan_gudang');
    // [2026-04-01] Related route: halaman laporan penerimaan
    Route::get('laporan_penerimaan', [TbPenerimaanController::class, 'laporan_penerimaan'])->name('laporan_penerimaan');
    Route::get('/cheklist_penerimaan_bgn/{id}', [PoController::class, 'cheklist_penerimaan_bgn'])->name('cheklist_penerimaan_bgn');
    Route::get('stok_opnam_pdf', [TbPenerimaanController::class, 'stok_opnam_pdf'])->name('stok_opnam_pdf');
});

//=====================================================================================//
// ==================================== transaksi_wadah ==========================================//
Route::resource('/transaksi_wadah', TransaksiWadahController::class);
Route::get('/transaksi-wadah', [TransaksiWadahController::class, '__invoke'])->name('transaksi_wadah.index');
Route::get('transaksi_wadah', TransaksiWadahController::class);
Route::get('detail_wadah/{id}', [TransaksiWadahController::class, 'detail_wadah'])->name('detail_wadah');
Route::post('simpan_transaksi_wadah_sesudah', [TransaksiWadahController::class, 'simpan_transaksi_wadah_sesudah'])->name('simpan_transaksi_wadah_sesudah');
Route::get('transaksi_wadah/simpan_gudang/{id}', [TransaksiWadahController::class, 'simpan_gudang'])->name('transaksi_wadah.simpan_gudang');

//Route::post('simpan_penerimaan_bahan', [TbPenerimaanController::class, 'simpan_penerimaan_bahan'])->name('simpan_penerimaan_bahan');

//-----------------------------------warehouse-------------------------------------------
Route::group(['middleware' => ['auth', 'throttle:100,1']], function () {
    // [2026-04-01] Related route: halaman form transaksi warehouse
    Route::get('/warehouse/form_warehouse', [warehouseController::class, 'v_formTransaksiWarehouse'])->name('v_formWarehouse');
    // [2026-04-01] Related route: data transaksi warehouse
    Route::get('form_warehouse/dt_transaksiWarehouse', [warehouseController::class, 'dt_transaksiWarehouse'])->name('dt_transaksiWarehouse');
    // [2026-04-01] Related route: data stok gudang aktif
    Route::get('warehouse/dt_warehouseInStock', [warehouseController::class, 'dt_warehouseInStock'])->name('dt_warehouseInStock');
    // [2026-04-01] Related route: data stok wajib keluar
    Route::get('warehouse/dt_warehouseMustOut', [warehouseController::class, 'dt_warehouseMustOut'])->name('dt_warehouseMustOut');
    Route::get('/form_warehouse/form_barangTransaksiMasuk', [warehouseController::class, 'form_barangTransaksiMasuk'])->name('form_transaksiBarangMasuk');
    Route::post('form_warehouse/ajax_simpanBarangTransaksiMasuk', [warehouseController::class, 'ajax_simpanBarangTransaksiMasuk'])->name('ajax_simpanBarangTransaksiMasuk');
    Route::post('form_warehouse/ajax_simpanBarangTransaksiKeluar', [warehouseController::class, 'ajax_simpanBarangTransaksiKeluar'])->name('ajax_simpanBarangTransaksiKeluar');
    Route::post('form_warehouse/ajax_simpanBarangTransaksiKeluarSemua', [warehouseController::class, 'ajax_simpanBarangTransaksiKeluarSemua'])->name('ajax_simpanBarangTransaksiKeluarSemua');
    Route::post('form_warehouse/ajax_kembalikanBarangTransaksiKeluar', [warehouseController::class, 'ajax_kembalikanBarangTransaksiKeluar'])->name('ajax_kembalikanBarangTransaksiKeluar');
    Route::post('form_warehouse/ajax_koreksiJumlahKeluar', [warehouseController::class, 'ajax_koreksiJumlahKeluar'])->name('ajax_koreksiJumlahKeluar');
    // [2026-04-01] Added route: warehouse per-item unit update
    Route::post('form_warehouse/ajax_updateSatuanGudang', [warehouseController::class, 'ajax_updateSatuanGudang'])->name('ajax_updateSatuanGudang');
    Route::post('form_warehouse/ajax_hapusBarangTransaksiMasuk', [warehouseController::class, 'ajax_hapusBarangTransaksiMasuk'])->name('ajax_hapusBarangTransaksiMasuk');
    Route::get('form_warehouse/dt_warehouse', [warehouseController::class, 'dt_warehouse'])->name('dt_warehouse');
    Route::post('/get-bahan-by-po', [warehouseController::class, 'getBahanByPO'])->name('get-bahan-by-po');
    Route::get('warehouse/dt_list_bahan', [warehouseController::class, 'dt_list_bahan'])->name('dt_list_bahan');
});

//-----------------------------end of warehouse-------------------------------------------

//=====================================Gramasi=================================================//
Route::group(['middleware' => ['auth', 'throttle:60,1']], function () {
    Route::resource('gramasi', GramasiController::class);
});
//============================================================================================//



//=====================================Hasil Masak=================================================//
Route::group(['middleware' => ['auth', 'throttle:60,1']], function () {
    Route::resource('hasil-masak', HasilMasakController::class);
    Route::get('hasil-masak/{id}/get-edit', [HasilMasakController::class, 'getEdit'])->name('hasil-masak.getEdit');
    Route::get('hasil-masak/delete/{id}', [HasilMasakController::class, 'destroy'])->name('hasil-masak.delete');
    Route::get('laporan-masak-harian', [HasilMasakController::class, 'laporanMasakharian'])->name('laporan-masak-harian');
    Route::get('menu-laporan-masak-harian', [HasilMasakController::class, 'v_menu_laporan_masak_harian'])->name('menu-laporan-masak-harian');
    Route::post('menu-laporan-masak-harian/sisa-bahan-baku', [HasilMasakController::class, 'simpanSisaBahanBaku'])->name('menu-laporan-masak-harian.sisa.store');
});

//============================================================================================//


//---------------------golongan-------------------------------------
Route::get('golongan', [golonganController::class, 'v_golongan'])->name('golongan');
Route::get('golongan/dt_golongan', [golonganController::class, 'dt_golongan'])->name('dt_golongan');
Route::get('golongan/form_golongan', [golonganController::class, 'v_formGolongan'])->name('golongan.formGolongan');
Route::get('golongan/ajax_getGolongan', [golonganController::class, 'ajax_getGolongan'])->name('golongan.ajax_getGolongan');
Route::post('golongan/ajax_simpanGolongan', [golonganController::class, 'ajax_simpanGolongan'])->name('golongan.ajax_simpanGolongan');
Route::post('golongan/ajax_deleteGolongan', [golonganController::class, 'ajax_deleteGolongan'])->name('golongan.ajax_deleteGolongan');
//-------------------end of golongan-------------------------------------


//=====================================Kandungan Gizi=================================================//

Route::resource('master-gizi', MasterKandunganGiziController::class);
Route::resource('rincian-kandungan-gizi', RoleKandunganGiziController::class);
Route::get('kandungan-gizi/{id}', [RoleKandunganGiziController::class, 'index_dashboard'])->name('kandungan-gizi');



//============================================================================================//


//=====================================checklist kerja=================================================//
Route::get('checklistGudang', [checklistKerjaController::class, 'v_checklistGudang'])->name('checklistGudang');
Route::get('checklistKerja/pdfChecklistGudangKeluar', [checklistKerjaController::class, 'pdf_checklistKerjaGudangKeluar'])->name('pdfChecklistKerjaGudangKeluar');
// Route to show daily checklist view
Route::get('/checklist_harian', [checklistKerjaController::class, 'v_checklistHarian'])->name('checklist_harian');
Route::get('checklistKerja/test', [checklistKerjaController::class, 'dt_item'])->name('dt_item');
Route::get('checklistGudang/dt_checklistGudangKeluar', [checklistKerjaController::class, 'dt_checklistGudangKeluar'])->name('dt_checklistGudangKeluar');
Route::get('checklistPenerimaan', [checklistKerjaController::class, 'v_checklistPenerimaan'])->name('checklistPenerimaan');
Route::get('checklistKerja/pdfChecklistPenerimaan', [checklistKerjaController::class, 'pdf_checklistKerjaPenerimaan'])->name('pdfChecklistPenerimaan');
Route::get('checklistPenerimaan/dt_checklistPenerimaan', [checklistKerjaController::class, 'dt_checklistPenerimaan'])->name('dt_checklistPenerimaan');
//=====================================end of checklist kerja===========================================//


Route::get('/display_ompreng', function () {
    return redirect('/ompreng');
})->name('display_ompreng');


//=====================================tambahan gramasi menu 19 - 4 - 2025=================================================//
Route::resource('tb-gramasi-menu', TbGramasiMenuController::class);
//==============================end of tambahan gramasi menu 19 - 4 - 2025=================================================//

//=====================================tambahan perhitungan bumbu 20 - 4 - 2025=================================================//
Route::resource('perhitungan-bumbu', PerhitunganBumbuController::class);
//==============================end of tambahan perhitungan bumbu 20 - 4 - 2025=================================================//



//=====================================tambahan Spesifikasi Bahan baku 21 - 4 - 2025=================================================//
Route::resource('spesifikasi_bahan_baku', SpesifikasiBahanController::class);
Route::post('simpan_spesifikasi_bahan_baku', [SpesifikasiBahanController::class, 'simpan_spesifikasi_bahan_baku'])->name('simpan_spesifikasi_bahan_baku');
//==============================end of Spesifikasi Bahan baku 21 - 4 - 2025=================================================//


//=====================================Export laporan hasil masak 23 - 4 - 2025=================================================//
Route::get('export-hasil-masak/{id}', [HasilMasakController::class, 'excel_hasil_masak'])->name('export-hasil-masak');
Route::get('laporan-hasil-masak', [HasilMasakController::class, 'laporan_hasil_masak'])->name('laporan-hasil-masak');
Route::get('laporan_hasil_masak', [HasilMasakController::class, 'v_laporan_hasil_masak'])->name('laporan_hasil_masak');
Route::get('dt_global_hasil_masak', [HasilMasakController::class, 'dt_global_hasil_masak'])->name('dt_global_hasil_masak');
Route::get('dt_rincian_hasil_masak', [HasilMasakController::class, 'dt_rincian_hasil_masak'])->name('dt_rincian_hasil_masak');


//==============================end of Export laporan hasil masak  23 - 4 - 2025=================================================//


//=====================================dashboard laporan pertanggal 24 - 4 - 2025=================================================//
Route::get('Laporan', [LaporanController::class, 'v_laporan'])->name('Laporan');
Route::get('dt_Laporan', [HasilMasakController::class, 'dt_global_hasil_masak'])->name('dt_Laporan');

//==============================end of dashboard laporan pertanggal 24 - 4 - 2025=================================================//


//===================================== Box pertanggal 20 - 6 - 2025=================================================//
Route::resource('box-bahan-baku', BoxBahanBakuController::class);
Route::put('/box-bahan-baku/{id}', [BoxBahanBakuController::class, 'update'])->name('box-bahan-baku.updates');
Route::delete('/box-bahan-baku/{id}', [BoxBahanBakuController::class, 'destroy']);


//==============================end of Box pertanggal 20 - 6 - 2025=================================================//

//===================================== buffer pertanggal 21 - 6 - 2025=================================================//
Route::resource('buffer', BufferController::class);
Route::put('/buffer/{id}', [BufferController::class, 'update'])->name('buffer.updates');

//==============================end of buffer pertanggal 21 - 6 - 2025=================================================//

Route::get('/get-po-bahan/{index}', [App\Http\Controllers\penerimaan\TbPenerimaanController::class, 'getSingle']);


//===================================== Rekap PO pertanggal 26 - 6 - 2025=================================================//


Route::resource('Rekap_po', RekapPOController::class);
Route::get('rincian_rekap_po/{id}', [RekapPOController::class, 'v_rincian_rekapan'])->name('rincian_rekap_po');
Route::post('/update-jumlah-po', [RekapPOController::class, 'update_jumlah_po'])->name('update_jumlah_po');
Route::resource('po-close', TbPoCloseController::class);
Route::post('/Rekap_po/{id}/close', [RekapPOController::class, 'closePo'])->name('pengajuan_po.close');


//===================================== Rekap PO pertanggal 26 - 6 - 2025=================================================//


//===================================== Kas kecil pertanggal 1 - 7 - 2025=================================================//

Route::resource('kas-kecil', KasKecilTransaksiController::class);
Route::put('/kas-kecil/{id}', [KasKecilTransaksiController::class, 'update'])->name('kas-kecil.updates');
//Route::put('/kas-kecil/{id}', [KasKecilTransaksiController::class, 'update'])->name('kas-kecil.updates');
Route::put('/kas-kecil-delete/{id}', [KasKecilTransaksiController::class, 'destroy'])->name('kas-kecil.deletes');
Route::delete('/kas-kecil/{id}', [KasKecilTransaksiController::class, 'destroy']);
Route::get('/laporan_pdf_kas_kecil/{bulan}/{tahun}', [KasKecilTransaksiController::class, 'pdf_laporan'])->name('laporan.pdf');


//===================================== end Kas kecil pertanggal 1 - 7 - 2025=================================================//

//===================================== rincian menu pertanggal 10 - 7 - 2025=================================================//

Route::post('/rincian-menu-temp/store-karbohidrat', [RincianMenuTempController::class, 'storeKarbohidrat'])->name('rincian-menu-temp.storeKarbohidrat');
Route::post('/rincian-menu-temp/store-protein', [RincianMenuTempController::class, 'storeProtein'])->name('rincian-menu-temp.storeProtein');
Route::post('/rincian-menu-temp/store-sayur', [RincianMenuTempController::class, 'storeSayur'])->name('rincian-menu-temp.storeSayur');
Route::post('/rincian-menu-temp/store-buah', [RincianMenuTempController::class, 'storeBuah'])->name('rincian-menu-temp.storeBuah');
Route::post('/rincian-menu-temp/store-suplemen', [RincianMenuTempController::class, 'storeSuplemen'])->name('rincian-menu-temp.storeSuplemen');
Route::post('/rincian-menu-temp/store-tambahan', [RincianMenuTempController::class, 'storeTambahan'])->name('rincian-menu-temp.storeTambahan');

Route::get('/dt-karbohidrat-temp/{id_menu}', [RincianMenuTempController::class, 'dt_karbohidrat_temp'])->name('karbohidrat.temp.data');
Route::get('/dt-karbohidrat-temp/delete/{id}', [RincianMenuTempController::class, 'delete_karbohidrat_temp'])->name('karbohidrat.temp.delete');
Route::post('/rincian_bahan_temp/update-jumlah', [RincianMenuTempController::class, 'update_jumlah_rincian_bahan'])->name('rincian_bahan.update_jumlah_temp');
Route::get('/dt-rincian-menu-temp/delete/{id}', [RincianMenuTempController::class, 'delete_rincian_menu_temp'])->name('rincian_menu_temp.delete');


Route::get('/dt-sayur-temp/delete/{id}', [RincianMenuTempController::class, 'delete_sayur_temp'])->name('sayur.temp.delete');
Route::post('/rincian-menu-temp/store-sayur-simpan', [RincianMenuTempController::class, 'storeSayur_simpan'])->name('rincian-menu-temp.storeSayur_simpan');
Route::get('/dt-sayur-temp/{id_menu}', [RincianMenuTempController::class, 'dt_sayur_temp'])->name('sayur.temp.data');

Route::get('/dt-protein-temp/delete/{id}', [RincianMenuTempController::class, 'delete_protein_temp'])->name('protein.temp.delete');
Route::post('/rincian-menu-temp/store-protein-simpan', [RincianMenuTempController::class, 'storeProtein_simpan'])->name('rincian-menu-temp.storeProtein_simpan');
Route::get('/dt-protein-temp/{id_menu}', [RincianMenuTempController::class, 'dt_protein_temp'])->name('protein.temp.data');

Route::get('/dt-buah-temp/{id_menu}', [RincianMenuTempController::class, 'dt_buah_temp'])->name('buah.temp.data');
Route::get('/dt-buah-temp/delete/{id}', [RincianMenuTempController::class, 'delete_buah_temp'])->name('buah.temp.delete');

Route::get('/dt-suplemen-temp/{id_menu}', [RincianMenuTempController::class, 'dt_suplemen_temp'])->name('suplemen.temp.data');
Route::get('/dt-suplemen-temp/delete/{id}', [RincianMenuTempController::class, 'delete_suplemen_temp'])->name('suplemen.temp.delete');


Route::get('/Hapus_bahan_rincian/{id}', [TbMasterMenuController::class, 'hapus_bahan_rincian'])->name('Hapus_bahan_rincian');
Route::get('/publish_rincian_menu/{id}', [RincianMenuTempController::class, 'publish_rincian_menu'])->name('publish_rincian_menu');


Route::get('/dt-bumbu-temp/{id_menu}', [RincianMenuTempController::class, 'dt_bumbu_temp'])->name('bumbu.temp.data');
//Route::get('/dt-bumbu-temp/{id_menu}', [RincianMenuTempController::class, 'dt_suplemen_temp'])->name('suplemen.temp.data');

//===================================== end rincian menu pertanggal 10 - 7 - 2025=================================================//
//===================================== update asrun ===============================//
use App\Http\Controllers\pdf\RekapBerasController;
use App\Http\Controllers\pdf\RekapBuahController;
use App\Http\Controllers\pdf\RekapSayurController;
use App\Http\Controllers\pdf\RekapLaukController;
use App\Http\Controllers\pdf\RekapPendampingController;
use App\Http\Controllers\pdf\RekapCekListController;
use App\Http\Controllers\CetakSekolahController;
use App\Http\Controllers\office\BukuKasUmumController;
use App\Http\Controllers\office\BukuKasKecil1Controller;
use App\Http\Controllers\office\LaporanBiayaOprasionalController;
use App\Http\Controllers\office\LaporanBiayaSewaController;
use App\Http\Controllers\office\LaporanRealisasiAnggaranController;
use App\Http\Controllers\office\LaporanPenggunaanDanaController;
use App\Http\Controllers\office\LaporanBiayaBahanBakuController;

Route::get('/cetak-rekap-beras/{id_menu}', [RekapBerasController::class, 'cetak'])->name('cetak.rekap.beras');
Route::get('/cetak-rekap-sayur/{id_menu}', [RekapSayurController::class, 'generateRekapSayurAllImages'])->name('cetak.rekap.sayur');
Route::get('/cetak-rekap-lauk/{id_menu}', [RekapLaukController::class, 'generateRekapLaukAllImages'])->name('cetak.rekap.lauk');
Route::get('/cetak-rekap-pendamping/{id_menu}', [RekapPendampingController::class, 'generateRekapPendampingAllImages'])->name('cetak.rekap.pendamping');
Route::get('/cetak-rekap-ceklist/{id_menu}', [RekapCekListController::class, 'generateRekapCekListAllImages'])->name('cetak.rekap.ceklist');

//======================================== 28 && 29 - 07-2025 =================================================//
Route::get('/cetak-rekap-lauk/{id_menu}', [RekapLaukController::class, 'cetakRekapLauk'])->name('cetak.rekap.lauk');
Route::get('/cetak-rekap-buah/{id_menu}', [RekapBuahController::class, 'cetakRekapBuah'])->name('cetak.rekapbuah');
Route::get('/cetak-sekolah', [CetakSekolahController::class, 'cetakSekolah']);
//=================================== 16 - 8 - 2025 =================================//
Route::get('/buku-kas-umum', [BukuKasUmumController::class, 'index'])->name('buku_kas.index');
Route::get('/buku-kas-umum/cetak', [BukuKasUmumController::class, 'cetak'])->name('buku_kas.cetak');
//=================================== 18 - 8 2025 =====================================//
Route::get('/buku-kas-kecil-po', [BukuKasKecil1Controller::class, 'index'])->name('bkk1_po.index');
Route::get('/buku-kas-kecil-po/cetak', [BukuKasKecil1Controller::class, 'cetak'])->name('bkk1_po.cetak');
//=================================== 19 - 8 - 2025 =====================================//
Route::get('/laporan-biaya-operasional', [LaporanBiayaOprasionalController::class, 'index'])->name('lbo.index');
Route::get('/laporan-biaya-operasional/cetak', [LaporanBiayaOprasionalController::class, 'cetak'])->name('lbo.cetak');
Route::get('/laporan-biaya-operasional/export', [LaporanBiayaOprasionalController::class, 'Lap_biaya_non_pangan_export'])
    ->name('lbo.cetak2');
//==================================== 20 - 8 - 2025 ====================================//
Route::get('/laporan-biaya-sewa', [LaporanBiayaSewaController::class, 'index'])->name('lbs.index');;
Route::get('/laporan-biaya-sewa/pdf', [LaporanBiayaSewaController::class, 'cetak'])->name('lbs.cetak');
Route::get('/laporan-biaya-sewa/export', [LaporanBiayaSewaController::class, 'Lap_biaya_Sewa_export'])
    ->name('lbs.cetak2');

//Route::get('/laporan-realisasi-anggaran', [LaporanRealisasiAnggaranController::class, 'index'])->name('laporan.keuangan.index');
//Route::get('/laporan-realisasi-anggaran/cetak', [LaporanRealisasiAnggaranController::class, 'cetak'])->name('laporan.keuangan.cetak');
Route::get('/laporan-realisasi-anggaran/view_laporan', [LaporanRealisasiAnggaranController::class, 'v_laporan_biaya_realisasi'])->name('laporan.keuangan.index');
Route::get('/laporan-realisasi-anggaran/dt_laporan', [LaporanRealisasiAnggaranController::class, 'dt_laporan_biaya_realisasi'])->name('laporan.keuangan.dt_laporan');
Route::resource('laporan-realisasi-anggaran', LaporanRealisasiAnggaranController::class);
Route::put('/laporan-realisasi-anggaran/{id}', [LaporanRealisasiAnggaranController::class, 'update'])->name('laporan.keuangan.updates');
Route::get('/laporan-realisasi-anggaran/cetak/{id}', [LaporanRealisasiAnggaranController::class, 'Lap_Realisasi_Anggaran'])
    ->name('realisasi.cetak');


Route::get('/laporan-penggunaan-dana', [LaporanPenggunaanDanaController::class, 'index'])->name('lpd2m.index');
Route::get('/laporan-penggunaan-dana/cetak', [LaporanPenggunaanDanaController::class, 'cetak'])->name('lpd2m.cetak');

//===================================== end update asrun ===============================//

//===================================== tambahan penerimaan 29 07 2025 =================//

Route::get('data_penerimaan_bahan', [TbPenerimaanController::class, 'v_data_masuk'])->name('data_penerimaan_bahan');
//Route::get('/data_penerimaan_bahan', [TbPenerimaanController::class, 'destroy_bahan'])->name('delete.penerimaan_data');
Route::delete('/destroy-bahan/{id}', [TbPenerimaanController::class, 'destroy_bahan']);
Route::get('/pdf_laporan_master_satuan', [TbSatuanController::class, 'pdf_laporan_master_satuan'])->name('pdf_laporan_master_satuan');

//=================================== end tambahan penerimaan 29 07 2025 ===============//
//=================================== Sekolahan 30 07 2025 ===============//
Route::get('/dt-sekolah/{id_menu}', [TbMasterMenuController::class, 'dt_rekap_sekolah'])->name('sekolah.data');
Route::get('/ajax/sekolah-aktif-list', [TbMasterMenuController::class, 'ajaxSekolahAktifList'])->name('ajax.sekolah.list');
Route::get('/ajax/total-pax/{id_menu}', [TbMasterMenuController::class, 'ajaxTotalPax'])->name('ajax.total.pax');
Route::post('/rincian_sekolah/tambah-manual', [TbMasterMenuController::class, 'tambahSekolahManual'])->name('rincian_sekolah.tambah_manual');
Route::post('/rincian_sekolah/hapus', [TbMasterMenuController::class, 'hapusRincianSekolah'])->name('rincian_sekolah.hapus');
Route::get('/pdf_laporan_master_resep', [TbResepController::class, 'pdf_laporan_master_resep'])->name('pdf_laporan_master_resep');
Route::get('/api/po-notif', [HomeController::class, 'getPONotifAjax']);



//=================================== sekolahan 30 07 2025 ===============//

//======================================== 28 && 29 - 07-2025 =================================================//
Route::get('/cetak-rekap-lauk/{id_menu}', [RekapLaukController::class, 'cetakRekapLauk'])->name('cetak.rekap.lauk');
//Route::get('/cetak-rekap-buah', [RekapBuahController::class, 'cetak'])->name('cetak.rekapbuah');

//===================================== end update asrun ===============================//


//===================================== 31 - 07 - 2025 =================================================//
//===================================== Laporan Hasil Masak Reza =================================================//
Route::prefix('laporan/menu')->name('laporan.menu.')->group(function () {
    Route::get('/periode1', [LaporanMenuController::class, 'dataPeriodePertama'])->name('periode1');
    Route::get('/periode2', [LaporanMenuController::class, 'dataPeriodeKedua'])->name('periode2');
}); //==================================== end of Laporan Hasil Masak ======================================//


//===================================== 31 - 7 - 2025 ===========================//
//===================================== update Adip Ompreng===============================//
Route::get('ompreng-management', [OmprengManagementController::class, 'index'])->name('ompreng.management');
//==================================== end of ompreng ======================================//


//======================================= 1 08 2025 ===============================//

Route::get('/Laporan/rekap_po/download', [LaporanController::class, 'rekap_po_PDF'])->name('rekap_po_PDF');


Route::post('/rincian_bahan_temp/update-jumlah-box', [RincianMenuTempController::class, 'update_jumlah_box'])->name('rincian_bahan.update_jumlah_box');
Route::post('/rincian_bahan_temp/update-keterangan', [RincianMenuTempController::class, 'update_keterangan'])->name('rincian_bahan.update_keterangan');
Route::post('/rincian_bahan_temp/update-harga', [RincianMenuTempController::class, 'update_harga'])->name('rincian_bahan.update_harga');
Route::post('/rincian_bahan_temp/update-sekolah', [RincianMenuTempController::class, 'update_sekolahb'])->name('rincian_bahan.update_sekolah');

//=======================================end 1 08 2025 ===============================//
//======================================= 5 08 2025 ===============================//
Route::get('/Laporan/rekap_menu/download', [LaporanController::class, 'rekap_menu_PDF'])->name('rekap_menu_PDF');
Route::get('/Laporan/rekap_menu/download/excel', [LaporanController::class, 'rekap_menu_Excel'])->name('rekap_menu_excel');

//=======================================end 5 08 2025 ===============================//
//======================================== 5 08 2025 =================================//

Route::get('/cetak-rekap-ceklist-masak/{id_menu}', [RekapCekListController::class, 'generateRekapCekListAllHasilMasak'])->name('cetak.rekap.ceklist_hasil_masak');
//=======================================end 5 08 2025 ===============================//
//=================================== 11 08 2025 =====================================//
// dashboard
Route::get('dashboard_akuntan', [HomeController::class, 'v_akuntan'])->name('dashboard_akuntan');
//=================================== 11 08 2025 =====================================//


//=================================== 17 08 2025 =====================================//
Route::resource('kemasan-materials', RincianKemasanBahanController::class);
//Route::get('kemasan-material/{id}', [RincianKemasanBahanController::class, 'v_dashboard'])->name('kemasan-material');
Route::get('kemasan-material/{id}', [RincianKemasanBahanController::class, 'v_dashboard'])->name('kemasan-material');


Route::get('/pdf_pengajuan_menu_3/{id}', [TbMasterMenuController::class, 'pdf_pengajuan_menu_3'])->name('pdf_pengajuan_menu_3');

Route::get('checklistKerja/pdfChecklistPenerimaan_2', [checklistKerjaController::class, 'pdf_checklistKerjaPenerimaan_2'])->name('pdfChecklistPenerimaan_2');

//=================================== end 17 08 2025 =====================================//

//=================================== 22 08 2025 =====================================//
Route::get('/laporan_harian', [LaporanController::class, 'v_laporan_harian'])->name('laporan_harian');
//Route::post('/laporan_harian/ajax_laporan_harian', [LaporanController::class, 'ajax_laporan_harian'])->name('ajax_laporan_harian');
Route::post('/laporan_harian/ajax_laporan_harian', [LaporanController::class, 'ajax_laporan_harian'])->name('ajax_laporan_harian');
Route::get('/counter-pax', [LaporanController::class, 'counterPaxIndex'])->name('counter-pax.index');
Route::get('/counter-pax/export/excel', [LaporanController::class, 'counterPaxExportExcel'])->name('counter-pax.export.excel');
Route::get('/counter-pax/export/pdf', [LaporanController::class, 'counterPaxExportPdf'])->name('counter-pax.export.pdf');
//=================================== Laporan Biaya Bahan Baku =====================================//
Route::get('/laporan-biaya-bahan-baku', [LaporanBiayaBahanBakuController::class, 'index'])->name('lbbb.index');
Route::get('/laporan-biaya-bahan-baku/cetak', [LaporanBiayaBahanBakuController::class, 'cetak'])->name('lbbb.cetak');
Route::get('/laporan-biaya-bahan/export', [LaporanBiayaBahanBakuController::class, 'Lap_biaya_bahan_baku_export'])
    ->name('lbbb.cetak2');

//==================================== Laporan Biaya Bahan Baku ====================================//


//=================================== end 22 08 2025 =====================================//
//=================================== magang 24 08 2025 =====================================//

//========================================== Input Data Siswa ======================================//

use App\Http\Controllers\InputdatasiswaController;
//========================================== Input Data Siswa Reza 22-08-2025 ======================================//
Route::get('/inputdatasiswa', [InputdatasiswaController::class, 'index'])->name('inputdatasiswa.index');
Route::get('inputdatasiswa/dt_dataSiswa', [InputdatasiswaController::class, 'dt_dataSiswa'])->name('inputdatasiswa.dt_dataSiswa');
Route::get('inputdatasiswa/form_dataSiswa', [InputdatasiswaController::class, 'form_dataSiswa'])->name('inputdatasiswa.form_dataSiswa');
Route::get('inputdatasiswa/detail_siswa', [InputdatasiswaController::class, 'detail_siswa'])->name('inputdatasiswa.detail_siswa');
Route::post('inputdatasiswa/ajax_simpanSiswa', [InputdatasiswaController::class, 'ajax_simpanSiswa'])->name('inputdatasiswa.ajax_simpanSiswa');
Route::post('inputdatasiswa/ajax_deleteSiswa', [InputdatasiswaController::class, 'ajax_deleteSiswa'])->name('inputdatasiswa.ajax_deleteSiswa');
Route::get('inputdatasiswa/getSekolahList', [InputdatasiswaController::class, 'getSekolahList'])->name('inputdatasiswa.getSekolahList');
//========================================= End Input Data Reza ===================================================//

//========================================= Import/Export routes Adip 22-08-2025 =========================================//
Route::get('inputdatasiswa/form_import', [InputdatasiswaController::class, 'form_import'])->name('inputdatasiswa.form_import');
Route::get('inputdatasiswa/form_export', [InputdatasiswaController::class, 'form_export'])->name('inputdatasiswa.form_export');
Route::get('inputdatasiswa/downloadTemplate', [InputdatasiswaController::class, 'downloadTemplate'])->name('inputdatasiswa.downloadTemplate');
Route::post('inputdatasiswa/importSiswa', [InputdatasiswaController::class, 'importSiswa'])->name('inputdatasiswa.importSiswa');
Route::post('inputdatasiswa/exportSiswa', [InputdatasiswaController::class, 'exportSiswa'])->name('inputdatasiswa.exportSiswa');
Route::post('inputdatasiswa/previewExport', [InputdatasiswaController::class, 'previewExport'])->name('inputdatasiswa.previewExport');
//========================================== End Import/Export routes Adip 22-08-2025 =========================================//


Route::post('inputdatasiswa/previewExport_presensi', [InputdatasiswaController::class, 'previewExport_presensi'])->name('inputdatasiswa.previewExport_presensi');
Route::get('inputdatasiswa/form_export_presensi', [InputdatasiswaController::class, 'form_export_presensi'])->name('inputdatasiswa.form_export_presensi');
Route::post('inputdatasiswa/exportSiswa_presensi', [InputdatasiswaController::class, 'exportSiswa_presensi'])->name('inputdatasiswa.exportSiswa_presensi');


//===========================================laporan akuntansi=================================================//
Route::get('/akuntansi/dt_data_bahan_datang', [LaporanMenuController::class, 'dt_data_bahan_datang'])->name('dt_data_bahan_datang');
//===========================================laporan akuntansi=================================================//


// test api //
use App\Http\Controllers\TestKitchenController;

Route::get('/kirim-menu', [TestKitchenController::class, 'kirim']);


// ============================================================= sdm =================================


use App\Http\Controllers\sdm\KaryawanDapurController;
use App\Http\Controllers\sdm\BagianController;
use App\Http\Controllers\sdm\WaktuKerjaController;
use App\Http\Controllers\sdm\TugasController;
use App\Http\Controllers\sdm\PenugasanHarianController;

Route::get('/karyawan-dapur', [KaryawanDapurController::class, 'index'])->name('karyawan-dapur.index');
Route::get('/karyawan-dapur-create', [KaryawanDapurController::class, 'create'])->name('karyawan-dapur.create');
Route::post('/karyawan-dapur', [KaryawanDapurController::class, 'store'])->name('karyawan-dapur.store');
Route::get('/karyawan-dapur/edit/{id}', [KaryawanDapurController::class, 'edit'])->name('karyawan-dapur.edit');
Route::put('/karyawan-dapur/update/{id}', [KaryawanDapurController::class, 'update'])->name('karyawan-dapur.update');

Route::get('/bagian', [BagianController::class, 'index'])->name('bagian.index');
Route::get('/bagian-create', [BagianController::class, 'create'])->name('bagian.create');
Route::post('/bagian-store', [BagianController::class, 'store'])->name('bagian.store');

Route::get('/waktu-kerja', [WaktuKerjaController::class, 'index'])->name('waktu.index');
Route::get('/waktu-kerja-create', [WaktuKerjaController::class, 'create'])->name('waktu.create');
Route::post('/waktu-kerja', [WaktuKerjaController::class, 'store'])->name('waktu.store');

Route::get('/tugas', [TugasController::class, 'index'])->name('tugas.index');
Route::get('/tugas/create', [TugasController::class, 'create'])->name('tugas.create');
Route::post('/tugas', [TugasController::class, 'store'])->name('tugas.store');

Route::get('/penugasan-harian', [PenugasanHarianController::class, 'index'])->name('penugasan.harian');
Route::get('/penugasan-harian/generate', [PenugasanHarianController::class, 'generate'])->name('penugasan.generate');
Route::get('/role-penugasan/data', [PenugasanHarianController::class, 'dt_role_penugasan'])->name('dt_role_penugasan');


// ============================================================= sdm =================================

// ============================================= bantuan ============================================= //
use App\Http\Controllers\MasterBantuanController;
Route::resource('master-bantuan', MasterBantuanController::class);

// ============================================= bantuan ============================================= //
//============================================== KBM ================================================= //
use App\Http\Controllers\KbmController;

Route::get('/master_kbm', [KbmController::class, 'index'])->name('kbm.indexs');
Route::get('/kbm/data', [KbmController::class, 'data'])->name('kbm.data');
Route::resource('/kbm', KbmController::class);
Route::get('/detail_kbm', [KbmController::class, 'detail_kbm'])->name('detail_kbm');
Route::get('/detail_kbm/data', [KbmController::class, 'dt_detail_kbm'])->name('detail_kbm.data');
Route::get('/master_libur', [KbmController::class, 'index_libur'])->name('kbm.libur');
Route::get('/master_libur/data', [KbmController::class, 'dt_master_libur'])->name('master_libur.data');
Route::post('/master_libur/simpan', [KbmController::class, 'store_master_libur'])->name('master_libur.simpan');
Route::delete('/master_libur/{id}', [KbmController::class, 'destroy_libur'])->name('hari-libur.destroy');
//============================================== KBM ================================================= //

//============================================== excel =============================================== //
Route::get('/master-resep/export', [TbResepController::class, 'Lap_data_resep_export'])
    ->name('dataresep.cetak');


//============================================== excel =============================================== //


//============================================== 6 10 2025 =============================================== //

use App\Http\Controllers\office\LaporanBahanController;
use App\Http\Controllers\office\LaporanBahanBakuController;

//=====================================Laporan PO Pembelian=================================================//
Route::get('laporan-bahan', [LaporanBahanController::class, 'index'])->name('laporan-bahan.index');
Route::get('laporan-bahan/data', [LaporanBahanController::class, 'getData'])->name('laporan-bahan.getData');
Route::get('laporan-bahan/export-pdf', [LaporanBahanController::class, 'exportPdf'])->name('laporan-bahan.export-pdf');
Route::get('laporan-bahan/export-excel', [LaporanBahanController::class, 'exportExcel'])->name('laporan-bahan.export-excel');


Route::get('laporan-Rekap-Menu', [TbMasterMenuController::class, 'Lap_rekap_menu_export'])->name('laporan-rekap-menu.excel');
//==================================================================================================//

//=====================================Laporan Bahan Baku=================================================//
Route::get('laporan-bahan-baku', [LaporanBahanBakuController::class, 'index'])->name('laporan-bahan-baku.index');
Route::get('laporan-bahan-baku/data', [LaporanBahanBakuController::class, 'getData'])->name('laporan-bahan-baku.getData');
Route::get('laporan-bahan-baku/export-pdf', [LaporanBahanBakuController::class, 'exportPdf'])->name('laporan-bahan-baku.export-pdf');
Route::get('laporan-bahan-baku/export-excel', [LaporanBahanBakuController::class, 'exportExcel'])->name('laporan-bahan-baku.export-excel');
//==================================================================================================//


// ================================== paket menu ===================================//
use App\Http\Controllers\PaketMenuController;

Route::get('/paket-menu', [PaketMenuController::class, 'index'])->name('paketmenu.index');
Route::get('/paket-menu/dt_paket', [PaketMenuController::class, 'dt_paket'])->name('paketmenu.dt_paket');
Route::post('/paket-menu', [PaketMenuController::class, 'store'])->name('paketmenu.store');
Route::put('/paket-menu/update/{id}', [PaketMenuController::class, 'update'])->name('paketmenu.update');
Route::delete('/paket-menu/{id}', [PaketMenuController::class, 'destroy'])->name('paketmenu.destroy');
Route::post('/pilih-paket-menu', [PaketMenuController::class, 'pilihmenu'])->name('paketmenu.pilih');

Route::get('laporan-Rekap-Menu', [TbMasterMenuController::class, 'Lap_rekap_menu_export'])->name('laporan-rekap-menu.excel');
Route::get('laporan-Rekap-Menu/{idmenu}', [TbMasterMenuController::class, 'Lap_rekap_menu_export2'])->name('laporan-rekap-menu.excel2');

//================================================================================//

// ============================== penerimaan 27 10 2025 ============================//
Route::get('/penerimaan/dt_sudah_diterima', [TbPenerimaanController::class, 'dt_sudah_diterima'])->name('penerimaan.dt_sudah_diterima');
Route::get('/penerimaan/dt_gudang', [TbPenerimaanController::class, 'dt_gudang'])->name('penerimaan.dt_gudang');

Route::get('checklistKerja/excelChecklistPenerimaan', [checklistKerjaController::class, 'excel_checklistKerjaPenerimaan'])->name('excelChecklistPenerimaan');
Route::get('checklistKerja/excel_checklistKerja_Penerimaan_non_pangan', [checklistKerjaController::class, 'excel_checklistKerja_Penerimaan_non_pangan'])->name('excelChecklistPenerimaanNonPangan');
Route::get('checklistKerja/excelChecklistOrganoleptik/{idmenu}', [checklistKerjaController::class, 'excel_checklist_uji_organoleptik'])->name('excelChecklistOrganoleptik');
Route::get('checklistKerja/pdfChecklistOrganoleptik/{idmenu}', [checklistKerjaController::class, 'pdf_checklist_uji_organoleptik'])->name('pdfChecklistOrganoleptik');
Route::put('/rincian-kontraks/update/{id}', [TbRincianKontrakController::class, 'update'])->name('rincian-kontraks.updates');
// Route to download the example-based Excel (dokumen/contoh.xlsx)
Route::get('export/contoh', [ExportContohController::class, 'download'])->name('export.contoh');
// Route to download checklist harian dokumen
Route::get('form-checklist-harian/export', [FormChecklistHarianExportController::class, 'download'])->name('form-checklist-harian.export');
// Route to download laporan harian dapur (exact copy of dokumen/contoh.xlsx)
Route::get('laporan_harian_dapur/excel', [LaporanHarianDapurExportController::class, 'download'])->name('laporan_harian_dapur.excel');
// Route to download PO report (laporan_po_versi_1)
Route::get('laporan-po/export', [LaporanPoController::class, 'exportPoVersi1'])->name('laporan-po.export');
// Route to download PO Karbo report (laporan_po_karbo_versi_1)
Route::get('laporan-po/karbo/export', [LaporanPoController::class, 'exportPoKarboVersi1'])->name('laporan-po-karbo.export');
// Route to download PO Lauk report (laporan_po_lauk_versi_1)
Route::get('laporan-po/lauk/export', [LaporanPoController::class, 'exportPoLaukVersi1'])->name('laporan-po-lauk.export');
// Route to download PO Sayur report (laporan_po_sayur_versi_1)
Route::get('laporan-po/sayur/export', [LaporanPoController::class, 'exportPoSayurVersi1'])->name('laporan-po-sayur.export');
// Route to download PO Buah report (laporan_po_buah_versi_1)
Route::get('laporan-po/buah/export', [LaporanPoController::class, 'exportPoBuahVersi1'])->name('laporan-po-buah.export');
// Route to download PO Pendamping report (laporan_po_pendamping_versi_1)
Route::get('laporan-po/pendamping/export', [LaporanPoController::class, 'exportPoPendampingVersi1'])->name('laporan-po-pendamping.export');
// Route to download Rekap Karbo report (laporan_rekap_karbo_versi_1)
Route::get('laporan-rekap-karbo/export', [LaporanPoController::class, 'exportRekapKarboVersi1'])->name('laporan-rekap-karbo.export');
// Route to download Rekap Karbo report Versi 2 (dengan jumlah masak)
Route::get('laporan-rekap-karbo-v2/export', [LaporanPoController::class, 'exportRekapKarboVersi2'])->name('laporan-rekap-karbo-v2.export');
// Route to download Rekap Lauk report
Route::get('laporan-rekap-lauk/export', [LaporanPoController::class, 'exportRekapLaukVersi1'])->name('laporan-rekap-lauk.export');
// Route to download Rekap Sayur report
Route::get('laporan-rekap-sayur/export', [LaporanPoController::class, 'exportRekapSayurVersi1'])->name('laporan-rekap-sayur.export');
// Route to download Rekap Buah report
Route::get('laporan-rekap-buah/export', [LaporanPoController::class, 'exportRekapBuahVersi1'])->name('laporan-rekap-buah.export');
// Route to download Rekap Pendamping report
Route::get('laporan-rekap-pendamping/export', [LaporanPoController::class, 'exportRekapPendampingVersi1'])->name('laporan-rekap-pendamping.export');
// ============================== penerimaan 27 10 2025 =============================//

//=========================================gramasi resep ============================//
use App\Http\Controllers\GramasiResepController;
Route::resource('gramasi-resep', GramasiResepController::class);
Route::get('gramasi-resep/dt_gramasi_resep/{id_menu}', [GramasiResepController::class, 'dt_gramasi_resep'])->name('gramasi-resep.dt_gramasi_resep');
Route::put('/gramasi-resep/{id}', [GramasiResepController::class, 'update'])->name('gramasi-resep.updates');
//=========================================end gramasi resep ============================//

//=========================================cheklist 14 - 11 - 2025 ============================//
Route::get('Form_pengeluaran/excel_form_pengeluaran_non_pangan', [checklistKerjaController::class, 'excel_form_Pengeluaran_non_pangan'])->name('Excel_form_Pengeluaran_non_pangan');
Route::get('hasil_matang/excel_pangan', [checklistKerjaController::class, 'excel_hasil_matang'])->name('excel_hasil_matang');
Route::get('Stok_opnam/excel_Non_Pangan', [checklistKerjaController::class, 'excel_Stok_Opnam_Non_Pangan'])->name('excel_Stok_Opnam_Non_Pangan');
//=========================================end cheklist 14 - 11 - 2025 ============================//

//=========================================Excel Master Bahan 25 - 11 - 2025 ============================//
Route::get('/master-bahan/export', [MasterBahanController::class, 'Lap_data_bahan_export'])
    ->name('databahan.cetak');
//=========================================end Excel Master Bahan 25 - 11 - 2025 ============================//

//=========================================Excel Penerimaan 11 - 12 - 2025 ============================//
Route::get('Form_penerimaan/excel_form_penerimaan_1', [checklistKerjaController::class, 'excel_form_Penerimaan_1'])->name('Excel_form_Penerimaan_1');
Route::get('Form_penerimaan/excel_form_penerimaan_2', [checklistKerjaController::class, 'excel_form_Penerimaan_2'])->name('Excel_form_Penerimaan_2');
Route::get('Form_penerimaan/excel_form_penerimaan_3', [checklistKerjaController::class, 'excel_form_Penerimaan_3'])->name('Excel_form_Penerimaan_3');


//=========================================Excel Penerimaan 11 - 12 - 2025 ============================//

//========================================= tambahan dari kodiklat 2 januari 2026 ============================//
Route::get('Form_penerimaan/excel_penerimaan_all', [checklistKerjaController::class, 'excel_penerimaan_all'])->name('excel_penerimaan_all');
Route::get('Form_penerimaan/excel_penerimaan_checklist_1_1', [checklistKerjaController::class, 'excel_penerimaan_checklist_1_1'])->name('excel_penerimaan_checklist_1_1');

Route::get('Form_penerimaan/excel_penerimaan_checklist_2_1', [checklistKerjaController::class, 'excel_penerimaan_checklist_2_1'])->name('excel_penerimaan_checklist_2_1');
Route::get('Form_penerimaan/excel_penerimaan_checklist_2_2', [checklistKerjaController::class, 'excel_penerimaan_checklist_2_1'])->name('excel_penerimaan_checklist_2_2');
Route::get('Form_penerimaan/excel_penerimaan_checklist_2_3', [checklistKerjaController::class, 'excel_penerimaan_checklist_2_1'])->name('excel_penerimaan_checklist_2_3');
Route::get('Form_penerimaan/excel_penerimaan_checklist_2_4', [checklistKerjaController::class, 'excel_penerimaan_checklist_2_1'])->name('excel_penerimaan_checklist_2_4');
Route::get('Form_penerimaan/excel_penerimaan_checklist_2_5', [checklistKerjaController::class, 'excel_penerimaan_checklist_2_1'])->name('excel_penerimaan_checklist_2_5');
Route::get('Form_penerimaan/excel_penerimaan_checklist_2_6', [checklistKerjaController::class, 'excel_penerimaan_checklist_2_1'])->name('excel_penerimaan_checklist_2_6');
// Menu Gizi Harian CRUD
    Route::post('/menu-gizi-harian/store', [TbMasterMenuController::class, 'storeMenuGizi'])->name('menu_gizi_harian.store');
    Route::post('/menu-gizi-harian/update/{id}', [TbMasterMenuController::class, 'updateMenuGizi'])->name('menu_gizi_harian.update');
    Route::delete('/menu-gizi-harian/delete/{id}', [TbMasterMenuController::class, 'deleteMenuGizi'])->name('menu_gizi_harian.delete');
    // [2026-04-04] Added route: hitung otomatis data nutrisi menu harian
    Route::post('/menu-gizi-harian/calculate/{idmenu}', [TbMasterMenuController::class, 'calculateMenuGizi'])->name('menu_gizi_harian.calculate');
    Route::get('/menu-gizi-harian/{idmenu}', [TbMasterMenuController::class, 'getMenuGizi'])->name('menu_gizi_harian.get');
    Route::get('/export-gizi-harian/{idmenu}', [TbMasterMenuController::class, 'exportGiziHarian'])->name('export_gizi_harian');

Route::post('/update-manual-close', [POController::class, 'updatemanualclose'])->name('update-manual-close');


//========================================= tambahan dari kodiklat 2 januari 2026 ============================//