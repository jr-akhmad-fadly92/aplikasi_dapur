<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
    <style>
        .form-control {
            height: 40px; /* Sesuaikan tinggi */
            width: 100%; /* Pastikan width full */
        }

        .select2-container .select2-selection--single {
            height: 40px !important; /* Samakan dengan input lainnya */
            padding: 5px;
            display: flex;
            align-items: center;
        }
        
        .select2-selection__rendered {
            line-height: 30px !important;
        }

        #tbl_list_karbo th,
        #tbl_list_protein th,
        #tbl_list_sayur th,
        #tbl_list_buah th,
        #tbl_list_suplemen th,
        #tbl_list_bumbu th,
        #tbl_list_master_menu th,
        #tbl_list_sekolah th {
            white-space: nowrap;
            vertical-align: middle;
        }

        #tbl_list_karbo td,
        #tbl_list_protein td,
        #tbl_list_sayur td,
        #tbl_list_buah td,
        #tbl_list_suplemen td,
        #tbl_list_bumbu td,
        #tbl_list_master_menu td,
        #tbl_list_sekolah td {
            vertical-align: middle;
        }

        .dataTables_scrollHeadInner,
        .dataTables_scrollHeadInner table,
        .dataTables_scrollBody table {
            width: 100% !important;
        }

        .dataTables_wrapper .form-control {
            min-width: 90px;
        }
       
        
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        @include('Template.navbar')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('Template.left-sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark" id="currentTime">Starter Page</h1>
                        </div><!-- /.col -->
                        
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="col-12">
                        <div class="card">
                        <div class="card-header">
                             
                          <div class="row invoice-info">
                                <div class="col-sm-6 text-left">
                                <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <b>Menu</b> <span>{{ $data_menu_harian->menu}}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center" hidden>
                                    <b>Tanggal pengajuan</b> <span>{{ \Carbon\Carbon::parse($data_menu_harian->tanggal_pengajuan)->translatedFormat('l, d-m-Y') }}</span>
                                @if (auth()->check() && in_array(auth()->user()->level, ["admin", "ahli_akuntan","backoffice","kepala_dapur"]))
                                <li class="list-group-item">
                                    <b>Extimasi Biaya</b> <a class="float-right">Rp. {{ number_format(($extimasi_biaya ?? 0),0,0) }}</a>
                                </li>
                                @endif
                                </ul>
                                <div class="d-flex justify-content-start gap-3 flex-wrap">
                                    <a href="{{ route('mastermenu.index') }}" class="btn btn-primary me-2" id="btn-edit-post">Selesai</a>&nbsp;
                                    <button type="button" class="btn btn-success me-2" data-toggle="modal" data-target="#autoPoModal">
                                        Auto Generate PO
                                    </button>
                                    <button type="button" class="btn btn-primary me-2" data-toggle="modal" data-target="#modalTambah">
                                        Tambah Rincian Menu
                                    </button>&nbsp;
                                    <a href="{{ route('publish_rincian_menu',$data_menu_harian->id) }}" class="btn btn-primary">Publish</a>                       
                                </div>
                                
                                </div>
                                <!-- /.col -->

                                <!-- Auto Generate PO Modal -->
                                <div class="modal fade" id="autoPoModal" tabindex="-1" role="dialog" aria-labelledby="autoPoModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="autoPoModalLabel">Auto Generate PO</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('pengajuan_po.auto_generate_po') }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <input type="hidden" name="id_menu" value="{{ $data_menu_harian->id }}">
                                                    <input type="hidden" name="id_kontrak" value="13">
                                                    <input type="hidden" name="tanggal_po" value="{{ date('Y-m-d') }}">
                                                    <input type="hidden" name="tanggal_pengajuan" value="{{ date('Y-m-d') }}">
                                                    <input type="hidden" name="tanggal_digunakan" value="{{ $data_menu_harian->tanggal_kirim ?? date('Y-m-d') }}">

                                                    <div class="form-group">
                                                        <label for="nomor_po">Nomor PO</label>
                                                        <input type="text" class="form-control" id="nomor_po" name="nomor_po" value="{{ $nomor_PO ?? '' }}" required>
                                                        <small class="form-text text-muted">Tanggal PO dan tanggal pengajuan otomatis diisi hari ini. Kontrak default: 13.</small>
                                                    </div>

                                                    <div class="form-group mt-3">
                                                        <label for="tanggal_kedatangan">Tanggal Kedatangan</label>
                                                        <input type="datetime-local" class="form-control" id="tanggal_kedatangan" name="tanggal_kedatangan" value="{{ \Carbon\Carbon::parse($data_menu_harian->tanggal_kirim ?? now())->subDay()->setTime(10, 0)->format('Y-m-d\TH:i') }}" required>
                                                        <small class="form-text text-muted">Default H-1 jam 10:00, bisa diubah.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success">Buat PO</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 invoice-col text-right">
                                <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <b>Jumlah</b> <span id="jumlah-pax-text">{{ number_format($jumlah, 0, ',', '.') }} Pax</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <b>Tanggal Pelayanan</b> <span>{{ \Carbon\Carbon::parse($data_menu_harian->tanggal_kirim)->translatedFormat('l, d-m-Y') }}</span>
                                </li>
                                @if (auth()->check() && in_array(auth()->user()->level, ["admin", "ahli_akuntan","backoffice","kepala_dapur"]))
                                
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <b>Budget Biaya</b> <span id="budget-biaya-text">Rp. {{ number_format(($budget_harga ?? 0),0,0) }}</span>
                                </li>
                                @endif
                                
                                <li class="list-group-item">
                                     <form action="{{ route('paketmenu.pilih') }}" method="POST">
                                        @csrf
                                        <!-- Input tersembunyi -->
                                        <input type="hidden" name="id_menu" value="{{ $data_menu_harian->id }}">
                                        <div class="form-group mb-0">
                                            <label class="font-weight-bold mb-2">Pilih Paket Menu</label> 
                                            <div class="d-flex align-items-center gap-2">
                                                <select class="form-control select2 flex-grow-1" id="pilihan" name="pilihan" style="width: 100%" required>
                                                    <option value="" selected>
                                                        Karbo | Lauk | sayur | buah | Suplemen
                                                    </option>
                                                    @foreach($paketmenu as $data)
                                                    <option value="{{ $data->id }}">
                                                        {{ $data->nama_karbo }} | {{ $data->nama_protein }}| {{ $data->nama_sayur }} | {{ $data->nama_buah }} | {{ $data->nama_suplemen }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </div>
                                     </form>
                                    
                                </li>
                                </ul>
                                
                                </div>
                                <!-- /.col -->
                                
                                <!-- /.col -->
                            </div>
                        </div>
                        <!-- /.card-header -->
                       
                        <!-- /.card -->
                        <div class="card">
                            <div class="card-header">
                                <h1>{{ $header }}</h1>
                            </div>
                            <div class="card-header p-2">
                                <ul class="nav nav-pills" style=" width: 100%;flex: 1 1 auto;text-align: center;">
                                    <li class="nav-item" style="width: 15%;flex: 1 1 auto;text-align: center;">
                                        <a class="nav-link active" href="#Karbohidrat" data-toggle="tab">Karbohidrat : {{ $nama_karbo->nama_resep ?? '-'}} @if (auth()->check() && in_array(auth()->user()->level, ["admin", "ahli_akuntan","backoffice","kepala_dapur"])) <b>Extimasi Biaya</b> <a class="float-center">Rp. {{ number_format($extimasi_biaya_karbo,0,0) }}</a> @endif </a>
                                    </li>
                                    <li class="nav-item" style="width: 15%;flex: 1 1 auto;text-align: center;">
                                        <a class="nav-link" href="#Lauk" data-toggle="tab">Lauk : {{ $nama_lauk->nama_resep ?? '-'}} @if (auth()->check() && in_array(auth()->user()->level, ["admin", "ahli_akuntan","backoffice","kepala_dapur"])) <b>Extimasi Biaya</b> <a class="float-center">Rp. {{ number_format($extimasi_biaya_lauk,0,0) }}</a> @endif</a>
                                    </li>
                                    <li class="nav-item" style="width: 15%;flex: 1 1 auto;text-align: center;">
                                        <a class="nav-link" href="#Sayur" data-toggle="tab">Sayur : {{ $nama_sayur->nama_resep ?? '-'}} @if (auth()->check() && in_array(auth()->user()->level, ["admin", "ahli_akuntan","backoffice","kepala_dapur"])) <b>Extimasi Biaya</b> <a class="float-center">Rp. {{ number_format($extimasi_biaya_sayur,0,0) }}</a> @endif </a>
                                    </li>
                                    <li class="nav-item" style="width: 15%;flex: 1 1 auto;text-align: center;">
                                        <a class="nav-link" href="#Buah" data-toggle="tab">Buah : {{ $nama_buah->nama_resep ?? '-'}} @if (auth()->check() && in_array(auth()->user()->level, ["admin", "ahli_akuntan","backoffice","kepala_dapur"])) <b>Extimasi Biaya</b> <a class="float-center">Rp. {{ number_format($extimasi_biaya_buah,0,0) }}</a> @endif </a>
                                    </li>
                                    <li class="nav-item" style="width: 15%;flex: 1 1 auto;text-align: center;">
                                        <a class="nav-link" href="#Suplemen" data-toggle="tab">Suplemen : {{ $nama_suplemen->nama_resep ?? '-'}} @if (auth()->check() && in_array(auth()->user()->level, ["admin", "ahli_akuntan","backoffice","kepala_dapur"])) <b>Extimasi Biaya</b> <a class="float-center">Rp. {{ number_format($extimasi_biaya_suplemen,0,0) }}</a> @endif </a>
                                    </li>
                                    <li class="nav-item" style="flex: 1 1 auto;text-align: center;" hidden>
                                        <a class="nav-link" href="#Tambahan" data-toggle="tab">Tambahan</a>
                                    </li>
                                    <li class="nav-item" style="flex: 1 1 auto;text-align: center;">
                                        <a class="nav-link" href="#rekapan" data-toggle="tab">Rekapan</a>
                                    </li>
                                    <li class="nav-item" style="flex: 1 1 auto;text-align: center;">
                                        <a class="nav-link" href="#RekapSekolah" data-toggle="tab">Rekapan Sekolah</a>
                                    </li>
                                    <li class="nav-item" style="flex: 1 1 auto;text-align: center;">
                                        <a class="nav-link" href="#MenuGizi" data-toggle="tab">Data Nutrisi</a>
                                    </li>
                                </ul>
                            </div>
                            <!-- /.card-header -->
                        
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="active tab-pane" id="Karbohidrat">
                                        <!-- Konten Karbohidrat -->
                                        @if(!$data_menu_harian->karbohidrat)
                                        <form action="{{ route('rincian-menu-temp.storeKarbohidrat') }}" method="POST">
                                            @csrf
                                            <div class="form-group " >
                                                <input type="hidden" class="form-control "  id="id_menu" name="id_menu" value="{{ $data_menu_harian->id }}">
                                                <label for="id_karbohidrat">Pilih Resep:</label>
                                                <select name="id_karbohidrat" id="id_karbohidrat" class="form-control select2" required>
                                                    <option value="">-- Pilih Resep --</option>
                                                    @foreach($karbohidrat as $resep)
                                                        <option value="{{ $resep->id }}">{{ $resep->nama_resep ?? 'Resep #' . $resep->id }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            
                                            <div class="form-group " >
                                                <label class="font-weight-bold">Porsi A ( gram )</label>
                                                <input type="number" class="form-control" name="porsi_a" id="porsi_a" value="38">
                                            </div>
                                            <div class="form-group " >
                                                <label class="font-weight-bold">Porsi B ( gram )</label>
                                                <input type="number" class="form-control" name="porsi_b" id="porsi_b" value="56">
                                            </div>

                                            <button type="submit"  id="submitBtn" class="btn btn-md btn-primary me-3">Simpan</button>
                                        </form>
                                        @else
                                        <a href="{{ route('karbohidrat.temp.delete', $data_menu_harian->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                            Hapus
                                        </a>
                                        <a href="{{ route('cetak.rekap.beras', $data_menu_harian->id) }}"  class="btn btn-success btn-sm" >
                                            Cetak Karbo
                                        </a>
                                        
                                        <table class="table table-bordered table-striped" style="width: 100%">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 30%">ID Menu</td>
                                                    <td style="width: 70%">{{ $data_menu_harian->id }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Porsi A</td>
                                                    <td>{{ $detail_karbo->karbo_porsi_a ?? 0}} gram</td>
                                                </tr>
                                                <tr>
                                                    <td>Porsi B</td>
                                                    <td>{{ $detail_karbo->karbo_porsi_b ?? 0 }} gram</td>
                                                </tr>
                                                <tr>
                                                    <td>Kebutuhan Beras A (kg)</td>
                                                    <td>{{ number_format(($detail_karbo->karbo_kebutuhan_beras_a ?? 0),0,0) ?? 0}} kg</td>
                                                </tr>
                                                <tr>
                                                    <td>Kebutuhan Beras B (kg)</td>
                                                    <td>{{ number_format(($detail_karbo->karbo_kebutuhan_beras_b ?? 0),0,0) ?? 0}} kg</td>
                                                </tr>
                                                <tr>
                                                    <td>Total Kebutuhan Beras (kg)</td>
                                                    <td>{{  number_format(($detail_karbo->karbo_kebutuhan_beras_total ?? 0),0,0)  ?? 0}} kg</td>
                                                </tr>
                                                <tr>
                                                    <td>Kebutuhan Tray</td>
                                                    <td>{{ number_format(($detail_karbo->karbo_kebutuhan_tray ?? 0),0,0) ?? 0}} tray</td>
                                                </tr>
                                                <tr>
                                                    <td>Kebutuhan Steamer</td>
                                                    <td>{{ number_format(($detail_karbo->karbo_kebutuhan_steamer ?? 0),0,0)  ?? 0}} steamer</td>
                                                </tr>
                                                <tr>
                                                    <td>Kebutuhan Pintu Steamer</td>
                                                    <td>{{ number_format(($detail_karbo->karbo_kebutuhan_pintu_steamer ?? 0),0,0)  ?? 0}} pintu</td>
                                                </tr>
                                                <tr>
                                                    <td>Kebutuhan Air (liter)</td>
                                                    <td>{{ number_format(($detail_karbo->karbo_kebutuhan_air ?? 0),0,0) ?? 0}} liter</td>
                                                </tr>
                                                <tr>
                                                    <td>Hasil Produksi (porsi)</td>
                                                    <td>{{ number_format(($detail_karbo->karbo_hasil_produksi ?? 0),0,0) ?? 0}} gram</td>
                                                </tr>
                                                <tr>
                                                    <td>Hasil Produksi (kg)</td>
                                                    <td>{{ number_format(($detail_karbo->karbo_hasil_produksi_kg ?? 0),0,0) ?? 0}} kg</td>
                                                </tr>
                                                <tr>
                                                    <td>Hitungan Cuci Beras</td>
                                                    <td>{{ $detail_karbo->karbo_hitungan_cuci_beras ?? 0}} x</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <h4>Bahan Baku</h4>
                                        <table id="tbl_list_karbo" class="table table-bordered table-striped" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>no</th>
                                                    <th>Bahan</th>
                                                    <th>Jumlah</th>
                                                    <th>satuan</th>
                                                    <th>Harga</th>
                                                    <th>Total Harga</th>
                                                    <th>Total box</th>
                                                    <th>Keterangan</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            
                                        </table>

                                        @endif
                                        <!-- end Konten Karbohidrat -->
                                        
                                    </div>
                                    <!-- /.tab-pane -->
                        
                                    <div class="tab-pane" id="Lauk">
                                        <a href="{{ route('protein.temp.delete', $data_menu_harian->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                            Hapus
                                        </a>
                                        <a href="{{ route('cetak.rekap.lauk', $data_menu_harian->id) }}"  class="btn btn-success btn-sm" >
                                            Download
                                        </a>
                                        @if(!$data_menu_harian->protein)
                                        <form action="{{ route('rincian-menu-temp.storeProtein') }}" method="POST">
                                            @csrf
                                            <!--div class="form-group " >
                                                <label class="font-weight-bold">Jenis Resep</label>
                                                <select name="jenis_resep" id="jenis_resep" class="form-control" style="width: 100%" required>
                                                    <option value="0">-- Langsung masak --</option>
                                                    <option value="1">-- Olahan --</option>
                                                   
                                                </select>
                                            </div-->
                                            <div class="form-group " >
                                                <input type="hidden" class="form-control "  id="id_menu" name="id_menu" value="{{ $data_menu_harian->id }}">
                                                <label for="id_protein">Pilih Resep:</label>
                                                <select name="id_protein" id="id_protein" class="form-control select2" style="width: 100%" required>
                                                    <option value="">-- Pilih Resep --</option>
                                                    @foreach($protein as $resep)
                                                        <option value="{{ $resep->id }}">{{ $resep->nama_resep ?? 'Resep #' . $resep->id }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group " >
                                                <label class="font-weight-bold">Porsi A ( gram )</label>
                                                <input type="number" class="form-control" name="porsi_a" id="porsi_a" value="80">
                                            </div>
                                            <div class="form-group " >
                                                <label class="font-weight-bold">Porsi B ( gram )</label>
                                                <input type="number" class="form-control" name="porsi_b" id="porsi_b" value="100">
                                            </div>
                                            
                                            <button type="submit"  id="submitBtn" class="btn btn-md btn-primary me-3">Simpan</button>
                                        </form>
                                        @else
                                        
                                        
                                        @endif
                                        @if(!$rumus_protein)

                                        @elseif($rumus_protein->status == 2  )
                                        <table class="table table-bordered" style="width: 100%">
                                            <thead>
                                                
                                                <tr>
                                                    <th>Label</th>
                                                    <th>Nama Bahan</th>
                                                    <th>Jumlah Bahan</th>
                                                    <th>Satuan</th>
                                                    <th>Penyusutan</th>
                                                    <th>Hasil Matang</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- Baris 1 --}}
                                                <tr>
                                                    <td>Bahan 1</td>
                                                    <td>
                                                        <input type="text" name="nama_bahan_1" value="{{ $protein_1->bahan ?? '-'}}" class="form-control" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="jumlah_bahan_1" id="jumlah_bahan_1" value="{{ $rumus_protein->kebutuhan_protein_a ?? 0 }}" class="form-control" required>
                                                    </td>
                                                    <td>
                                                        
                                                        <input type="text" name="satuan_bahan_1" class="form-control" value="kg / Ekor / Potong" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="penyusutan_1" id="penyusutan_1" value="{{ $protein_1->penyusutan ?? 0}}" class="form-control" readonly>%
                                                    </td>
                                                    <td>
                                                        <input type="number" name="jumlah_bahan_matang_1" id="jumlah_bahan_matang_1" value="{{ $rumus_protein->kebutuhan_matang_a ?? 0 }}" class="form-control" required>
                                                    </td>
                                                </tr>
                                    
                                                {{-- Baris 2 --}}
                                                <tr>
                                                    <td>Bahan 2</td>
                                                    <td>
                                                        <input type="text" name="nama_bahan_2" value="{{ $protein_2->bahan ?? '-'}}" class="form-control" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="jumlah_bahan_2" id="jumlah_bahan_2" value="{{ $rumus_protein->kebutuhan_protein_b ?? 0 }}" class="form-control" required>
                                                    </td>
                                                    <td>
                                                        
                                                        <input type="text" name="satuan_bahan_2" class="form-control" value="kg / Ekor / Potong" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="penyusutan_2" id="penyusutan_2" value="{{ $protein_2->penyusutan ?? 0}}" class="form-control" readonly>%
                                                    </td>
                                                    <td>
                                                        <input type="number" name="jumlah_bahan_matang_2" id="jumlah_bahan_matang_2" value="{{ $rumus_protein->kebutuhan_matang_b ?? 0 }}" class="form-control" required>
                                                    </td>
                                                </tr>

                                               
                                                {{-- Tambah baris sesuai kebutuhan --}}
                                            </tbody>
                                        </table>
                                        <table class="table table-bordered" style="width: 100%">
                                            <tr>
                                                <th>Kebutuhan Bahan Baku Mentah</th>
                                                <th style="text-align: center"><input 
                                                    type="text" 
                                                    id="kebutuhan_mentah" 
                                                    name="kebutuhan_mentah" 
                                                    value="{{ $rumus_protein->kebutuhan_total_mentah ?? 0}} {{ $protein_1->satuan == 'Gram' ? 'Kg' : $protein_1->satuan }}"
                                                    style="width: 100%; text-align:center" 
                                                    class="form-control" 
                                                    readonly
                                                >
                                                </th>
                                                <th>Kebutuhan Bahan Baku Matang</th>
                                                <th style="text-align: center"><input 
                                                    type="text" 
                                                    id="kebutuhan_total_matang" 
                                                    name="kebutuhan_total_matang" 
                                                    value="{{ $rumus_protein->kebutuhan_total_matang ?? 0 }} {{ $protein_1->satuan == 'Gram' ? 'Kg' : $protein_1->satuan }}"
                                                    style="width: 100%; text-align:center" 
                                                    class="form-control" 
                                                    readonly
                                                ></th>
                                                <th>Akumulasi Jumlah Berat Matang</th>
                                                <th colspan=2  style="text-align: center;">    
                                                    <input 
                                                    type="text" 
                                                    value="{{ round($rumus_protein->kebutuhan_matang_realisasi ?? 0) }} {{ $protein_1->satuan == 'Gram' ? 'Kg' : $protein_1->satuan }}"
                                                    style="width: 100%; text-align:center ; background-color :chartreuse" 
                                                    class="form-control" 
                                                    readonly
                                                    >
                                                </th>
                                                
                                            </tr>
                                            <tr>
                                                <th>Jumlah Masak</th>
                                                <th style="text-align: center">
                                                    <div class="input-group">
                                                        <input 
                                                            type="number" 
                                                            id="jumlah_masak_protein"
                                                            value="{{ $rumus_protein->jumlah_masak ?? 0 }}"
                                                            style="text-align:center" 
                                                            class="form-control"
                                                            min="0" step="1"
                                                        >
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">x</span>
                                                        </div>
                                                        <button type="button" class="btn btn-success btn-sm ml-1" id="btn_save_jumlah_masak_protein"
                                                            data-id-menu="{{ $data_menu_harian->id }}"
                                                            data-tipe="protein">
                                                            Simpan
                                                        </button>
                                                    </div>
                                                </th>
                                               
                                                
                                            </tr>
                                            <tr>
                                                <td></td>
                                            </tr>
                                        </table>
                                            <h4>Bahan Baku</h4>
                                            <table id="tbl_list_protein" class="table table-bordered table-striped" style="width: 100%">
                                                <thead>
                                                    <tr>
                                                        <th>no</th>
                                                        <th>Bahan</th>
                                                        <th>Jumlah</th>
                                                        <th>satuan</th>
                                                        <th>Harga</th>
                                                        <th>Total Harga</th>
                                                        <th>Total box</th>
                                                        <th>Keterangan</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        @else
                                        <form action="{{ route('rincian-menu-temp.storeProtein_simpan') }}" method="POST">
                                            @csrf
                                            <input type="hidden" class="form-control "  id="id_menu" name="id_menu" value="{{ $data_menu_harian->id }}">
                                            <table class="table table-bordered" style="width: 100%">
                                                <thead>
                                                    
                                                    <tr>
                                                        <th>Label</th>
                                                        <th>Nama Bahan</th>
                                                        <th>Jumlah Bahan</th>
                                                        <th>Satuan</th>
                                                        <th>Penyusutan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {{-- Baris 1 --}}
                                                    <tr>
                                                        <td>Bahan 1</td>
                                                        <td>
                                                            <input type="number" name="id_bahan_1" value="{{ $protein_1->id ?? 0}}" class="form-control" required>
                                                            <input type="text" name="nama_bahan_1" value="{{ $protein_1->bahan ?? '-'}}" class="form-control" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="jumlah_bahan_1" id="jumlah_bahan_1"  class="form-control" required step="0.01">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="satuan_bahan_1" class="form-control" value="@if($protein_1->satuan == 'Gram') Kg @else {{$protein_1->satuan}} @endif" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="penyusutan_1" id="penyusutan_1" value="{{ $protein_1->penyusutan ?? 0}}" class="form-control" readonly>%
                                                        </td>
                                                    </tr>
                                        
                                                    {{-- Baris 2 --}}
                                                    <tr>
                                                        <td>Bahan 2</td>
                                                        <td>
                                                            <input type="number" name="id_bahan_2" value="{{ $protein_2->id ?? 0}}" class="form-control" required>
                                                            <input type="text" name="nama_bahan_2" value="{{ $protein_2->bahan ?? '-'}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="jumlah_bahan_2" id="jumlah_bahan_2" class="form-control" step="0.01">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="satuan_bahan_2" class="form-control" value="@if($protein_1->satuan == 'Gram') Kg @else {{$protein_1->satuan}} @endif" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="penyusutan_2" id="penyusutan_2" value="{{ $protein_2->penyusutan ?? 0}}" class="form-control" readonly>%
                                                        </td>
                                                    </tr>

                                                   
                                                    {{-- Tambah baris sesuai kebutuhan --}}
                                                </tbody>
                                            </table>
                                            <table class="table table-bordered" style="width: 100%">
                                                <tr>
                                                    <th>Kebutuhan Bahan Baku Mentah</th>
                                                    <th style="text-align: center"><input 
                                                        type="text" 
                                                        id="kebutuhan_mentah" 
                                                        name="kebutuhan_mentah" 
                                                        value="0 kg/potong"
                                                        style="width: 100%; text-align:center" 
                                                        class="form-control" 
                                                        readonly
                                                    >
                                                    <input hidden
                                                        type="number" 
                                                        id="kebutuhan_mentah_input" 
                                                        name="kebutuhan_mentah_input" 
                                                        value="0 kg/potong"
                                                        style="width: 100%; text-align:center" 
                                                        class="form-control" 
                                                        readonly 
                                                    >
                                                    </th>
                                                    <th>Kebutuhan Bahan Baku Matang</th>
                                                    <th style="text-align: center"><input 
                                                        type="text" 
                                                        id="kebutuhan_total_matang" 
                                                        name="kebutuhan_total_matang" 
                                                        value="{{ $rumus_protein->kebutuhan_total_matang ?? 0}} kg/potong"
                                                        style="width: 100%; text-align:center" 
                                                        class="form-control" 
                                                        readonly
                                                    ></th>
                                                    <th>Akumulasi Jumlah Berat Matang</th>
                                                    <th colspan=2  style="text-align: center;">    
                                                        <input 
                                                        type="text" 
                                                        id="kebutuhan_matang_realisasi" 
                                                        name="kebutuhan_matang_realisasi" 
                                                        value="0 kg/potong"
                                                        style="width: 100%; text-align:center" 
                                                        class="form-control" 
                                                        readonly
                                                        >
                                                        <input hidden
                                                        type="number" 
                                                        id="kebutuhan_matang_realisasi_input" 
                                                        name="kebutuhan_matang_realisasi_input" 
                                                        value="0 kg/potong"
                                                        style="width: 100%; text-align:center" 
                                                        class="form-control" 
                                                        readonly
                                                    ></th>
                                                    
                                                </tr>
                                                <tr>
                                                    <th>Jumlah Box</th>
                                                    <th style="text-align: center"><input 
                                                        type="number" 
                                                        id="jumlah_box_rencana" 
                                                        name="jumlah_box_rencana" 
                                                        value="0"
                                                        style="width: 100%; text-align:center" 
                                                        class="form-control" 
                                                        
                                                    >
                                                    
                                                    
                                                    </th>
                                                    
                                                    
                                                </tr>
                                            </table>
                                            
                                            <button type="submit" id="submit_protein" class="btn btn-primary mt-3">Simpan</button>
                                        </form>
                                        
                                        @endif
                                        <!-- Konten Lauk -->2
                                    </div>
                                    <!-- /.tab-pane -->
                        
                                    <div class="tab-pane" id="Sayur">
                                        <a href="{{ route('sayur.temp.delete', $data_menu_harian->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                            Hapus
                                        </a>
                                        <a href="{{ url('/cetak-rekap-sayur/' . $data_menu_harian->id) }}" target="_blank" class="btn btn-success btn-sm">
                                            Download
                                        </a>
                                        @if(!$data_menu_harian->sayur)
                                        <form action="{{ route('rincian-menu-temp.storeSayur') }}" method="POST">
                                            @csrf
                                            <div class="form-group " >
                                                <input type="hidden" class="form-control "  id="id_menu" name="id_menu" value="{{ $data_menu_harian->id }}">
                                                <label for="id_sayur">Pilih Resep:</label>
                                                <select name="id_sayur" id="id_sayur" class="form-control select2" style="width: 100%" required>
                                                    <option value="">-- Pilih Resep --</option>
                                                    @foreach($sayur as $resep)
                                                        <option value="{{ $resep->id }}">{{ $resep->nama_resep ?? 'Resep #' . $resep->id }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group " >
                                                <label class="font-weight-bold">Porsi A ( gram )</label>
                                                <input type="number" class="form-control" name="porsi_a" id="porsi_a" value="40">
                                            </div>
                                            <div class="form-group " >
                                                <label class="font-weight-bold">Porsi B ( gram )</label>
                                                <input type="number" class="form-control" name="porsi_b" id="porsi_b" value="60">
                                            </div>
                                            
                                            <button type="submit"  id="submitBtn" class="btn btn-md btn-primary me-3">Simpan</button>
                                        </form>
                                        @else
                                        
                                        
                                        @endif
                                        
                                        @if(!$rumus_sayur)

                                        @elseif($rumus_sayur->status == 2  )
                                        <table class="table table-bordered" style="width: 100%">
                                            <thead>
                                                
                                                <tr>
                                                    <th>Label</th>
                                                    <th>Nama Bahan</th>
                                                    <th>Jumlah Bahan</th>
                                                    <th>Satuan</th>
                                                    <th>Penyusutan</th>
                                                    <th>Hasil Matang</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- Baris 1 --}}
                                                <tr>
                                                    <td>Bahan 1</td>
                                                    <td>
                                                        <input type="text" name="nama_bahan_1_sayur" value="{{ $sayur_1->bahan ?? '-'}}" class="form-control" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="jumlah_bahan_1_sayur" id="jumlah_bahan_1_sayur" value="{{ $rumus_sayur->kebutuhan_sayur_a ?? 0 }}" class="form-control" required>
                                                    </td>
                                                    <td>
                                                        
                                                        <input type="text" name="satuan_bahan_1_sayur" class="form-control" value="kg" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="penyusutan_1_sayur" id="penyusutan_1_sayur" value="{{ $sayur_1->penyusutan ?? 0}}" class="form-control" readonly>%
                                                    </td>
                                                    <td>
                                                        <input type="number" name="jumlah_bahan_matang_1_sayur" id="jumlah_bahan_matang_1_sayur" value="{{ $rumus_sayur->kebutuhan_matang_a ?? 0 }}" class="form-control" required>
                                                    </td>
                                                </tr>
                                    
                                                {{-- Baris 2 --}}
                                                <tr>
                                                    <td>Bahan 2</td>
                                                    <td>
                                                        <input type="text" name="nama_bahan_2_sayur" value="{{ $sayur_2->bahan ?? '-'}}" class="form-control" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="jumlah_bahan_2_sayur" id="jumlah_bahan_2_sayur" value="{{ $rumus_sayur->kebutuhan_sayur_b ?? 0 }}" class="form-control" required>
                                                    </td>
                                                    <td>
                                                        
                                                        <input type="text" name="satuan_bahan_2_sayur" class="form-control" value="kg" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="penyusutan_2_sayur" id="penyusutan_2_sayur" value="{{ $sayur_2->penyusutan ?? 0}}" class="form-control" readonly>%
                                                    </td>
                                                    <td>
                                                        <input type="number" name="jumlah_bahan_matang_2_sayur" id="jumlah_bahan_matang_2_sayur" value="{{ $rumus_sayur->kebutuhan_matang_b ?? 0 }}" class="form-control" required>
                                                    </td>
                                                </tr>

                                                {{-- Baris 3 --}}
                                                <tr>
                                                    <td>Bahan 3</td>
                                                    <td>
                                                        <input type="text" name="nama_bahan_3_sayur" value="{{ $sayur_3->bahan ?? '-'}}" class="form-control" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="jumlah_bahan_3_sayur" id="jumlah_bahan_3_sayur" value="{{ $rumus_sayur->kebutuhan_sayur_c ?? 0 }}" class="form-control" required>
                                                    </td>
                                                    <td>
                                                       
                                                        <input type="text" name="satuan_bahan_3_sayur" class="form-control" value="kg" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="penyusutan_3_sayur" id="penyusutan_3_sayur" value="{{ $sayur_3->penyusutan ?? 0}}" class="form-control" readonly>%
                                                    </td>
                                                    <td>
                                                        <input type="number" name="jumlah_bahan_matang_3_sayur" id="jumlah_bahan_matang_3_sayur" value="{{ $rumus_sayur->kebutuhan_matang_c ?? 0 }}" class="form-control" required>
                                                    </td>
                                                </tr>

                                                {{-- Baris 4 --}}
                                                <tr>
                                                    <td>Bahan 4</td>
                                                    <td>
                                                        <input type="text" name="nama_bahan_4_sayur" value="{{ $sayur_4->bahan ?? '-'}}" class="form-control" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="jumlah_bahan_4_sayur" id="jumlah_bahan_4_sayur" value="{{ $rumus_sayur->kebutuhan_sayur_d ?? 0 }}" class="form-control" required>
                                                    </td>
                                                    <td>
                                                        
                                                        <input type="text" name="satuan_bahan_4_sayur" class="form-control" value="kg" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="penyusutan_4_sayur" id="penyusutan_4_sayur" value="{{ $sayur_4->penyusutan ?? 0}}" class="form-control" readonly>%
                                                    </td>
                                                    <td>
                                                        <input type="number" name="jumlah_bahan_matang_4_sayur" id="jumlah_bahan_matang_4_sayur" value="{{ $rumus_sayur->kebutuhan_matang_d ?? 0 }}" class="form-control" required>
                                                    </td>
                                                </tr>
                                                {{-- Tambah baris sesuai kebutuhan --}}
                                            </tbody>
                                        </table>
                                        <table class="table table-bordered" style="width: 100%">
                                            <tr>
                                                <th>Kebutuhan Bahan Baku Mentah</th>
                                                <th style="text-align: center"><input 
                                                    type="text" 
                                                    id="kebutuhan_mentah_sayur" 
                                                    name="kebutuhan_mentah_sayur" 
                                                    value="{{ $rumus_sayur->kebutuhan_total_mentah ?? 0}} kg/potong"
                                                    style="width: 100%; text-align:center" 
                                                    class="form-control" 
                                                    readonly
                                                >
                                                </th>
                                                <th>Kebutuhan Bahan Baku Matang</th>
                                                <th style="text-align: center"><input 
                                                    type="text" 
                                                    id="kebutuhan_total_matang_sayur" 
                                                    name="kebutuhan_total_matang_sayur" 
                                                    value="{{ $rumus_sayur->kebutuhan_total_matang ?? 0}} kg/potong"
                                                    style="width: 100%; text-align:center" 
                                                    class="form-control" 
                                                    readonly
                                                ></th>
                                                <th>Akumulasi Jumlah Berat Matang</th>
                                                <th colspan=2  style="text-align: center;">    
                                                    <input 
                                                    type="text" 
                                                    value="{{ $rumus_sayur->kebutuhan_matang_realisasi ?? 0 }} kg/potong"
                                                    style="width: 100%; text-align:center ; background-color :chartreuse" 
                                                    class="form-control" 
                                                    readonly
                                                    >
                                                </th>
                                                
                                            </tr>
                                            <tr>
                                                <th colspan="3">Jumlah Masak</th>
                                                <th colspan="3" style="text-align: center">
                                                    <div class="input-group">
                                                        <input 
                                                            type="number" 
                                                            id="jumlah_masak_sayur"
                                                            value="{{ $rumus_sayur->jumlah_masak ?? 0 }}"
                                                            style="text-align:center" 
                                                            class="form-control"
                                                            min="0" step="1"
                                                        >
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">x</span>
                                                        </div>
                                                        <button type="button" class="btn btn-success btn-sm ml-1" id="btn_save_jumlah_masak_sayur"
                                                            data-id-menu="{{ $data_menu_harian->id }}"
                                                            data-tipe="sayur">
                                                            Simpan
                                                        </button>
                                                    </div>
                                                </th>
                                                
                                                
                                            </tr>
                                            <tr>
                                                <td></td>
                                            </tr>
                                        </table>
                                            <h4>Bahan Baku</h4>
                                            <table id="tbl_list_sayur" class="table table-bordered table-striped" style="width: 100%">
                                                <thead>
                                                    <tr>
                                                        <th>no</th>
                                                        <th>Bahan</th>
                                                        <th>Jumlah</th>
                                                        <th>satuan</th>
                                                        <th>Harga</th>
                                                        <th>Total Harga</th>
                                                        <th>Total box</th>
                                                        <th>Keterangan</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        @else
                                        <form action="{{ route('rincian-menu-temp.storeSayur_simpan') }}" method="POST">
                                            @csrf
                                            <input type="hidden" class="form-control "  id="id_menu" name="id_menu" value="{{ $data_menu_harian->id }}">
                                            <table class="table table-bordered" style="width: 100%">
                                                <thead>
                                                    
                                                    <tr>
                                                        <th>Label</th>
                                                        <th>Nama Bahan</th>
                                                        <th>Jumlah Bahan</th>
                                                        <th>Satuan</th>
                                                        <th>Penyusutan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {{-- Baris 1 --}}
                                                    <tr>
                                                        <td>Bahan 1</td>
                                                        <td>
                                                            <input type="number" name="id_bahan_1_sayur" value="{{ $sayur_1->id ?? 0}}" class="form-control" required>
                                                            <input type="text" name="nama_bahan_1_sayur" value="{{ $sayur_1->bahan ?? '-'}}" class="form-control" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="jumlah_bahan_1_sayur" id="jumlah_bahan_1_sayur"  class="form-control" required>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="satuan_bahan_1_sayur" class="form-control" value="kg" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="penyusutan_1_sayur" id="penyusutan_1_sayur" value="{{ $sayur_1->penyusutan ?? 0}}" class="form-control" readonly>%
                                                        </td>
                                                    </tr>
                                        
                                                    {{-- Baris 2 --}}
                                                    <tr>
                                                        <td>Bahan 2</td>
                                                        <td>
                                                            <input type="number" name="id_bahan_2_sayur" value="{{ $sayur_2->id ?? 0}}" class="form-control" required>
                                                            <input type="text" name="nama_bahan_2_sayur" value="{{ $sayur_2->bahan ?? '-'}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="jumlah_bahan_2_sayur" id="jumlah_bahan_2_sayur" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="satuan_bahan_2_sayur" class="form-control" value="kg" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="penyusutan_2_sayur" id="penyusutan_2_sayur" value="{{ $sayur_2->penyusutan ?? 0}}" class="form-control" readonly>%
                                                        </td>
                                                    </tr>

                                                    {{-- Baris 3 --}}
                                                    <tr>
                                                        <td>Bahan 3</td>
                                                        <td>
                                                            <input type="number" name="id_bahan_3_sayur" value="{{ $sayur_3->id ?? 0}}" class="form-control" required>
                                                            <input type="text" name="nama_bahan_3_sayur" value="{{ $sayur_3->bahan ?? '-'}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="jumlah_bahan_3_sayur" id="jumlah_bahan_3_sayur" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="satuan_bahan_3_sayur" class="form-control" value="kg" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="penyusutan_3_sayur" id="penyusutan_3_sayur" value="{{ $sayur_3->penyusutan ?? 0}}" class="form-control" readonly>%
                                                        </td>
                                                    </tr>

                                                    {{-- Baris 4 --}}
                                                    <tr>
                                                        <td>Bahan 4</td>
                                                        <td>
                                                            <input type="number" name="id_bahan_4_sayur" value="{{ $sayur_4->id ?? 0}}" class="form-control" required>
                                                            <input type="text" name="nama_bahan_4_sayur" value="{{ $sayur_4->bahan ?? '-'}}" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="jumlah_bahan_4_sayur" id="jumlah_bahan_4_sayur" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="satuan_bahan_4_sayur" class="form-control" value="kg" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="penyusutan_4_sayur" id="penyusutan_4_sayur" value="{{ $sayur_4->penyusutan ?? 0}}" class="form-control" readonly>%
                                                        </td>
                                                    </tr>
                                                    {{-- Tambah baris sesuai kebutuhan --}}
                                                </tbody>
                                            </table>
                                            <table class="table table-bordered" style="width: 100%">
                                                <tr>
                                                    <th>Kebutuhan Bahan Baku Mentah</th>
                                                    <th style="text-align: center"><input 
                                                        type="text" 
                                                        id="kebutuhan_mentah_sayur" 
                                                        name="kebutuhan_mentah_sayur" 
                                                        value="0 kg"
                                                        style="width: 100%; text-align:center" 
                                                        class="form-control" 
                                                        readonly
                                                    >
                                                    <input hidden
                                                        type="number" 
                                                        id="kebutuhan_mentah_input_sayur" 
                                                        name="kebutuhan_mentah_input_sayur" 
                                                        value="0 kg"
                                                        style="width: 100%; text-align:center" 
                                                        class="form-control" 
                                                        readonly 
                                                    >
                                                    </th>
                                                    <th>Kebutuhan Bahan Baku Matang</th>
                                                    <th style="text-align: center"><input 
                                                        type="text" 
                                                        id="kebutuhan_total_matang_sayur" 
                                                        name="kebutuhan_total_matang_sayur" 
                                                        value="{{ $rumus_sayur->kebutuhan_total_matang ?? 0}} kg"
                                                        style="width: 100%; text-align:center" 
                                                        class="form-control" 
                                                        readonly
                                                    ></th>
                                                    <th>Akumulasi Jumlah Berat Matang</th>
                                                    <th colspan=2  style="text-align: center;">    
                                                        <input 
                                                        type="text" 
                                                        id="kebutuhan_matang_realisasi_sayur" 
                                                        name="kebutuhan_matang_realisasi_sayur" 
                                                        value="0 kg"
                                                        style="width: 100%; text-align:center" 
                                                        class="form-control" 
                                                        readonly
                                                        >
                                                        <input 
                                                        type="number" hidden
                                                        id="kebutuhan_matang_realisasi_input_sayur" 
                                                        name="kebutuhan_matang_realisasi_input_sayur" 
                                                        value="0 kg"
                                                        style="width: 100%; text-align:center" 
                                                        class="form-control" 
                                                        readonly
                                                    ></th>
                                                    
                                                </tr>
                                                <tr>
                                                    <th>Jumlah Box</th>
                                                    <th style="text-align: center"><input 
                                                        type="number" 
                                                        id="jumlah_box_sayur_rencana" 
                                                        name="jumlah_box_sayur_rencana" 
                                                        value="0"
                                                        style="width: 100%; text-align:center" 
                                                        class="form-control" 
                                                        
                                                    >
                                                    
                                                    
                                                    </th>
                                                    
                                                    
                                                </tr>
                                            </table>
                                            
                                            <button type="submit" id="submit_sayur" class="btn btn-primary mt-3">Simpan</button>
                                        </form>
                                        
                                        @endif
                                        
                                        <!-- Konten Sayur -->3
                                    </div>
                                    <!-- /.tab-pane -->
                                    
                                    <div class="tab-pane" id="Buah">
                                        <a href="{{ route('buah.temp.delete', $data_menu_harian->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                            Hapus
                                        </a>
                                        <a hidden href="{{ url('/cetak-rekap-lauk/' . $data_menu_harian->id) }}" target="_blank" class="btn btn-success btn-sm">
                                            Download
                                        </a>
                                        @if(!$data_menu_harian->buah)
                                        <form action="{{ route('rincian-menu-temp.storeBuah') }}" method="POST">
                                            @csrf
                                            <div class="form-group " >
                                                <input type="hidden" class="form-control "  id="id_menu" name="id_menu" value="{{ $data_menu_harian->id }}">
                                                <label for="id_buah">Pilih Resep:</label>
                                                <select name="id_buah" id="id_buah" class="form-control select2" style="width: 100%" required>
                                                    <option value="">-- Pilih Resep --</option>
                                                    @foreach($buah as $resep)
                                                        <option value="{{ $resep->id }}">{{ $resep->nama_resep ?? 'Resep #' . $resep->id }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            
                                            <div class="form-group " >
                                                <label class="font-weight-bold">Porsi A ( gram / Pcs)</label>
                                                <input type="number" class="form-control" name="porsi_a" id="porsi_a" value="1">
                                            </div>
                                            <div class="form-group " >
                                                <label class="font-weight-bold">Porsi B ( gram / Pcs )</label>
                                                <input type="number" class="form-control" name="porsi_b" id="porsi_b" value="1">
                                            </div>

                                            <button type="submit"  id="submitBtn" class="btn btn-md btn-primary me-3">Simpan</button>
                                        </form>
                                        @else
                                        <h4>Bahan Baku</h4>
                                        <table id="tbl_list_buah" class="table table-bordered table-striped" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>no</th>
                                                    <th>Bahan</th>
                                                    <th>Jumlah</th>
                                                    <th>satuan</th>
                                                    <th>Harga</th>
                                                    <th>Total Harga</th>
                                                    <th>Total box</th>
                                                    <th>Keterangan</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            
                                        </table>
                                        @endif
                                        <!-- Konten Buah -->4
                                    </div>
                                    <!-- /.tab-pane -->
                        
                                    <div class="tab-pane" id="Suplemen">
                                        <a href="{{ route('suplemen.temp.delete', $data_menu_harian->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                            Hapus
                                        </a>
                                        <a hidden href="{{ url('/cetak-rekap-lauk/' . $data_menu_harian->id) }}" target="_blank" class="btn btn-success btn-sm">
                                            Download
                                        </a>
                                        @if(!$data_menu_harian->susu)
                                        <form action="{{ route('rincian-menu-temp.storeSuplemen') }}" method="POST">
                                            @csrf
                                            <div class="form-group " >
                                                <input type="hidden" class="form-control "  id="id_menu" name="id_menu" value="{{ $data_menu_harian->id }}">
                                                <label for="id_suplemen">Pilih Resep:</label>
                                                <select name="id_suplemen" id="id_suplemen" class="form-control select2"  style="width: 100%" required>
                                                    <option value="">-- Pilih Resep --</option>
                                                    @foreach($suplemen as $resep)
                                                        <option value="{{ $resep->id }}">{{ $resep->nama_resep ?? 'Resep #' . $resep->id }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            
                                            <div class="form-group " >
                                                <label class="font-weight-bold">Porsi A ( gram / Pcs)</label>
                                                <input type="number" class="form-control" name="porsi_a" id="porsi_a" value="1">
                                            </div>
                                            <div class="form-group " >
                                                <label class="font-weight-bold">Porsi B ( gram / Pcs )</label>
                                                <input type="number" class="form-control" name="porsi_b" id="porsi_b" value="1">
                                            </div>

                                            <button type="submit"  id="submitBtn" class="btn btn-md btn-primary me-3">Simpan</button>
                                        </form>
                                        @else
                                        <h4>Bahan Baku</h4>
                                        <table id="tbl_list_suplemen" class="table table-bordered table-striped" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>no</th>
                                                    <th>Bahan</th>
                                                    <th>Jumlah</th>
                                                    <th>satuan</th>
                                                    <th>Harga</th>
                                                    <th>Total Harga</th> 
                                                    <th>Total box</th>
                                                    <th>Keterangan</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            
                                        </table>
                                        @endif
                                        <!-- Konten Suplemen -->5
                                    </div>
                                    <div class="tab-pane" id="Tambahan">
                                        <table id="tbl_list_bumbu" class="table table-bordered table-hover " style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Resep</th>
                                                    <th>Bahan</th>
                                                    <th>Jumlah</th>
                                                    <th>Satuan</th>
                                                    <th>aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
            
                                            </tbody>
                                        </table>
                                        <!-- Konten Tambahan -->
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="rekapan">
                                        <table id="tbl_list_master_menu" class="table table-bordered table-hover " style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Resep</th>
                                                    <th>Bahan</th>
                                                    <th>Jumlah</th>
                                                    <th>Satuan</th>
                                                    <th>aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
            
                                            </tbody>
                                        </table>
                                        <!-- Konten Tambahan -->
                                    </div>
                                    <div class="tab-pane" id="MenuGizi">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <button type="button" class="btn btn-warning" id="btnAutoGiziAi">
                                                    <i class="fas fa-magic"></i> Hitung Otomatis Berdasarkan AI
                                                </button>
                                                <button type="button" class="btn btn-info" id="btnAutoGiziDb">
                                                    <i class="fas fa-database"></i> Hitung Otomatis Berdasarkan Database
                                                </button>
                                                <button type="button" class="btn btn-primary" id="btnAddGizi" data-toggle="modal" data-target="#modalGizi">
                                                    <i class="fas fa-plus"></i> Tambah/Edit Data Nutrisi
                                                </button>
                                                <a href="{{ route('export_gizi_harian', $idmenu) }}" class="btn btn-success" id="btnExportGizi">
                                                    <i class="fas fa-download"></i> Export Excel
                                                </a>
                                                <a href="{{ route('excelChecklistOrganoleptik', $idmenu) }}" class="btn btn-secondary">
                                                    <i class="fas fa-file-excel"></i> Export Checklist Organoleptik
                                                </a>
                                                <a href="{{ route('pdfChecklistOrganoleptik', $idmenu) }}" class="btn btn-danger" target="_blank">
                                                    <i class="fas fa-file-pdf"></i> PDF Organoleptik
                                                </a>
                                            </div>
                                        </div>

                                        {{-- Display Auto-Calculated Nutrition Summary --}}
                                        @if($menuGiziHarian)
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <div class="card card-info">
                                                    <div class="card-header with-border">
                                                        <h3 class="card-title">📊 Nutrisi Menu (Hasil Perhitungan Otomatis dari tb_resep_realisasi_akg)</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                <div class="info-box">
                                                                    <span class="info-box-text">Energi (kkal)</span>
                                                                    <span class="info-box-number" style="font-size: 18px;">{{ number_format($menuGiziHarian->energi, 2, ',', '.') }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="info-box">
                                                                    <span class="info-box-text">Protein (g)</span>
                                                                    <span class="info-box-number" style="font-size: 18px;">{{ number_format($menuGiziHarian->protein, 2, ',', '.') }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="info-box">
                                                                    <span class="info-box-text">Lemak (g)</span>
                                                                    <span class="info-box-number" style="font-size: 18px;">{{ number_format($menuGiziHarian->lemak, 2, ',', '.') }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="info-box">
                                                                    <span class="info-box-text">Karbo (g)</span>
                                                                    <span class="info-box-number" style="font-size: 18px;">{{ number_format($menuGiziHarian->karbohidrat, 2, ',', '.') }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="info-box">
                                                                    <span class="info-box-text">Serat (g)</span>
                                                                    <span class="info-box-number" style="font-size: 18px;">{{ number_format($menuGiziHarian->serat, 2, ',', '.') }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="info-box">
                                                                    <span class="info-box-text">Natrium (mg)</span>
                                                                    <span class="info-box-number" style="font-size: 18px;">{{ number_format($menuGiziHarian->natrium, 2, ',', '.') }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="table table-bordered table-striped">
                                                    <tbody id="giziTableBody">
                                                        <tr>
                                                            <td colspan="2" class="text-center text-muted">Loading...</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="RekapSekolah">
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalTambahSekolah">
                                                <i class="fas fa-plus"></i> Tambah Sekolah
                                            </button>
                                        </div>
                                        <table id="tbl_list_sekolah" class="table table-bordered table-hover " style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Sekolah</th>
                                                    <th>Jenjang</th>
                                                    <th>A</th>
                                                    <th>B</th>
                                                    <th>Total</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
            
                                            </tbody>
                                        </table>
                                        <!-- Konten Tambahan -->
                                    </div>
                                    <!-- /.tab-pane -->
                                </div>
                                <!-- /.tab-content -->
                            </div>
                            <!-- /.card-body -->
                        </div>

                        
                    </div>
                    <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
                </section>
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>
        <!-- /.control-sidebar -->
        <!-- Modal Tambah bumbu -->
        <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahLabel">Tambah Rincian Menu Harian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formTambah">
                <div class="form-group" hidden>
                    <label for="id_menu">id menu</label>
                    <input type="text" class="form-control" id="id_menu" name="id_menu" value="{{ $idmenu }}" required>
                </div>
                <div class="form-group">
                    <label for="id_resep">Resep</label>
                    <select class="form-control select2" id="id_resep" name="id_resep" style="width: 100%">
                        <option value={{ $nama_karbo->id ?? 0 }}  >{{ $nama_karbo->nama_resep ?? '-' }}</option>
                        <option value={{ $nama_lauk->id ?? 0 }}  >{{ $nama_lauk->nama_resep ?? '-' }}</option>
                        <option value={{ $nama_sayur->id ?? 0 }}  >{{ $nama_sayur->nama_resep ?? '-' }}</option>
                        <option value={{ $nama_buah->id ?? 0 }}  >{{ $nama_buah->nama_resep ?? '-' }}</option>
                        <option value={{ $nama_suplemen->id ?? 0 }}  >{{ $nama_suplemen->nama_resep ?? '-' }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="bahan_id">Bahan</label>
                    <select class="form-control select2" id="bahan_id" name="bahan_id" style="width: 100%">
                    @foreach ($bumbu as $data)
                        <option value={{ $data->id }}>{{ $data->bahan }} ({{$data->satuan }})</option>
                    @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="jumlah">Jumlah</label>
                    <input type="number" class="form-control" id="jumlah" name="jumlah" required>
                </div>
                <div class="form-group">
                    <label for="id_satuan">Satuan</label>
                    <select class="form-control select2" id="id_satuan" name="id_satuan" style="width: 100%">
                    @foreach ($satuan as $data)
                        <option value={{ $data->id }}>{{ $data->satuan }} </option>
                    @endforeach
                    </select>
                </div>
                <div class="form-group" hidden>
                    <label for="bumbu">bumbu</label>
                    <input type="number" class="form-control" id="bumbu" name="bumbu" value=0 >
                </div>
                <div class="form-group">
                    <label for="harga">Harga Satuan</label>
                    <input type="number" class="form-control" id="harga" name="harga" value=0 >
                </div>
                <div class="form-group">
                    <label for="jumlah_box">Jumlah Box</label>
                    <input type="number" class="form-control" id="jumlah_box" name="jumlah_box" value=0 >
                </div>
                
                <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
            </div>
        </div>
        </div>

        <!-- Modal Gizi Harian -->
        <div class="modal fade" id="modalGizi" tabindex="-1" aria-labelledby="modalGiziLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalGiziLabel">Data Nutrisi Menu</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formGizi">
                            @csrf
                            <input type="hidden" id="giziId" name="id">
                            <input type="hidden" id="giziIdMenu" name="id_menu" value="{{ $idmenu }}">
                            
                            <div class="form-group">
                                <label for="giziEnergi">Energi (kkal)</label>
                                <input type="number" step="0.01" class="form-control" id="giziEnergi" name="energi" min="0" required>
                            </div>
                            <div class="form-group">
                                <label for="giziProtein">Protein (g)</label>
                                <input type="number" step="0.01" class="form-control" id="giziProtein" name="protein" min="0" required>
                            </div>
                            <div class="form-group">
                                <label for="giziLemak">Lemak (g)</label>
                                <input type="number" step="0.01" class="form-control" id="giziLemak" name="lemak" min="0" required>
                            </div>
                            <div class="form-group">
                                <label for="giziKarbohidrat">Karbohidrat (g)</label>
                                <input type="number" step="0.01" class="form-control" id="giziKarbohidrat" name="karbohidrat" min="0" required>
                            </div>
                            <div class="form-group">
                                <label for="giziSerat">Serat (g)</label>
                                <input type="number" step="0.01" class="form-control" id="giziSerat" name="serat" min="0" required>
                            </div>
                            <div class="form-group">
                                <label for="giziNatrium">Natrium (mg)</label>
                                <input type="number" step="0.01" class="form-control" id="giziNatrium" name="natrium" min="0" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" id="btnDeleteGizi" style="display:none;">Hapus</button>
                        <button type="button" class="btn btn-primary" id="btnSaveGizi">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeleteLabel">Konfirmasi Hapus</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin menghapus data ini?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <form id="formDelete" method="POST" action="">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
          </form>
      </div>
    </div>
  </div>
</div>

        <!-- Main Footer -->
        @include('Template.footer')
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
  
    @include('Template.script')
   
    <script type="text/javascript">
    $(document).ready(function () {
    $('#tbl_list_master_menu').DataTable({
            scrollX: true,
            autoWidth: false,
            
            ajax: '{{ url()->current() }}',
            columns: [
                 { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'resep', name: 'resep' },
                { data: 'bahan', name: 'bahan' },
                { data: 'jumlah_bahan', name: 'jumlah_bahan' },
                { data: 'satuan', name: 'satuan' },
                { data: 'action', name: 'action' },
               

            ]
        });
        function refreshJumlahPax() {
            $.get('{{ route('ajax.total.pax', ['id_menu' => $idmenu]) }}', function(res) {
                $('#jumlah-pax-text').text(res.total.toLocaleString('id-ID') + ' Pax');
                if ($('#budget-biaya-text').length) {
                    $('#budget-biaya-text').text('Rp. ' + res.budget.toLocaleString('id-ID'));
                }
            });
        }

        // Event submit form untuk insert data
        $('#tbl_list_sekolah').DataTable({
            scrollX: true,
            autoWidth: false,
            ajax: '{{ route('sekolah.data', ['id_menu' => $idmenu])  }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama_sekolah', name: 'nama_sekolah' },
                { data: 'jenjang_sekolah', name: 'jenjang_sekolah' },
                { data: 'input_sekolah_a', name: 'input_sekolah_a' },
                { data: 'input_sekolah_b', name: 'input_sekolah_b' },
                { data: 'jumlah_penerima_total', name: 'jumlah_penerima_total' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            drawCallback: function() {
                refreshJumlahPax();
            }
        });

        // Hapus sekolah dari menu
        $(document).on('click', '.btn-hapus-sekolah', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var hapusId   = $(this).data('id');
            var hapusNama = $(this).data('nama');
            Swal.fire({
                title: 'Hapus Sekolah?',
                html: 'Yakin ingin menghapus <strong>' + hapusNama + '</strong> dari menu ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                var isConfirmed = (result && result.isConfirmed === true) || (result && result.value === true);
                if (isConfirmed) {
                    $.post('{{ route('rincian_sekolah.hapus') }}', {
                        _token: '{{ csrf_token() }}',
                        id: hapusId
                    })
                    .done((response) => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        });
                        $('#tbl_list_sekolah').DataTable().ajax.reload(null, false);
                        $('#tbl_list_karbo').DataTable().ajax.reload();
                        $('#tbl_list_protein').DataTable().ajax.reload();
                        $('#tbl_list_sayur').DataTable().ajax.reload();
                        $('#tbl_list_buah').DataTable().ajax.reload();
                        $('#tbl_list_suplemen').DataTable().ajax.reload();
                    })
                    .fail((xhr) => {
                        var msg = 'Terjadi kesalahan (status: ' + xhr.status + ')';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        Swal.fire({ icon: 'error', title: 'Error', text: msg });
                    });
                }
            });
        });
    });
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
        $('#tbl_list_karbo').DataTable({
            scrollX: true,
            autoWidth: false,
                
                ajax: '{{ route('karbohidrat.temp.data', ['id_menu' => $idmenu]) }}',
                columns: [
                     { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'bahan', name: 'bahan' },
                    { data: 'jumlah_bahan', name: 'jumlah_bahan' },
                    { data: 'satuan', name: 'satuan' },
                    { data: 'input_harga', name: 'input_harga' },
                    { data: 'total_dibayar', name: 'total_dibayar' },
                    { data: 'input_jumlah_box', name: 'input_jumlah_box' },
                    { data: 'input_keterangan', name: 'input_keterangan' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                   
    
                ]
            });
            // Event submit form untuk insert data
            
        });
        $(document).ready(function () {
        $('#tbl_list_sayur').DataTable({
            scrollX: true,
            autoWidth: false,
                
                ajax: '{{ route('sayur.temp.data', ['id_menu' => $idmenu]) }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'bahan', name: 'bahan' },
                    { data: 'jumlah_bahan', name: 'jumlah_bahan' },
                    { data: 'satuan', name: 'satuan' },
                    { data: 'input_harga', name: 'input_harga' },
                    { data: 'total_dibayar', name: 'total_dibayar' },
                    { data: 'input_jumlah_box', name: 'input_jumlah_box' },
                    { data: 'input_keterangan', name: 'input_keterangan' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                   
    
                ]
            });
            // Event submit form untuk insert data
            
        });
        $(document).ready(function () {
        $('#tbl_list_protein').DataTable({
            scrollX: true,
            autoWidth: false,
                
                ajax: '{{ route('protein.temp.data', ['id_menu' => $idmenu]) }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'bahan', name: 'bahan' },
                    { data: 'jumlah_bahan', name: 'jumlah_bahan' },
                    { data: 'satuan', name: 'satuan' },
                    { data: 'input_harga', name: 'input_harga' },
                    { data: 'total_dibayar', name: 'total_dibayar' },
                    { data: 'input_jumlah_box', name: 'input_jumlah_box' },
                    { data: 'input_keterangan', name: 'input_keterangan' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                   
    
                ]
            });
            // Event submit form untuk insert data
            
        });
        $(document).ready(function () {
        $('#tbl_list_buah').DataTable({
            scrollX: true,
            autoWidth: false,
                
                ajax: '{{ route('buah.temp.data', ['id_menu' => $idmenu]) }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'bahan', name: 'bahan' },
                    { data: 'jumlah_bahan', name: 'jumlah_bahan' },
                    { data: 'satuan', name: 'satuan' },
                    { data: 'input_harga', name: 'input_harga' },
                    { data: 'total_dibayar', name: 'total_dibayar' },
                    { data: 'input_jumlah_box', name: 'input_jumlah_box' },
                    { data: 'input_keterangan', name: 'input_keterangan' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                   
    
                ]
            });
            // Event submit form untuk insert data
            
        });
        $(document).ready(function () {
        $('#tbl_list_suplemen').DataTable({
            scrollX: true,
            autoWidth: false,
                
                ajax: '{{ route('suplemen.temp.data', ['id_menu' => $idmenu]) }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'bahan', name: 'bahan' },
                    { data: 'jumlah_bahan', name: 'jumlah_bahan' },
                    { data: 'satuan', name: 'satuan' },
                    { data: 'input_harga', name: 'input_harga' },
                    { data: 'total_dibayar', name: 'total_dibayar' },
                    { data: 'input_jumlah_box', name: 'input_jumlah_box' },
                    { data: 'input_keterangan', name: 'input_keterangan' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                   
    
                ]
            });
            // Event submit form untuk insert data
            
        });
        $(document).ready(function () {
        $('#tbl_list_bumbu').DataTable({
            scrollX: true,
            autoWidth: false,
                
                ajax: '{{ route('bumbu.temp.data', ['id_menu' => $idmenu]) }}',
                columns: [
                     { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'bahan', name: 'bahan' },
                    { data: 'jumlah_bahan', name: 'jumlah_bahan' },
                    { data: 'harga_satuan', name: 'harga_satuan' },
                    { data: 'total_dibayar', name: 'total_dibayar' },
                    { data: 'action', name: 'action' },
    
                ]
            });
            // Event submit form untuk insert data
            
        });

        function adjustAllDataTables() {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        }

        // Recalculate column width when switching tab to avoid header/body misalignment
        $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
            setTimeout(function () {
                adjustAllDataTables();
            }, 100);
        });

        $(window).on('resize', function () {
            adjustAllDataTables();
        });

        setTimeout(function () {
            adjustAllDataTables();
        }, 200);
        </script>
     <script src="{{ asset('AdminLte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
     <script>
        //message with sweetalert
        @if(session('success'))
            Swal.fire({
                icon: "success",
                title: "BERHASIL",
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @elseif(session('error'))
            Swal.fire({
                icon: "error",
                title: "GAGAL!",
                text: "{{ session('error') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @endif
            
    </script>
    
    <script>
        $(document).ready(function() {
            // Event saat tombol Update diklik
                $('#formTambah').submit(function (e) {
                e.preventDefault();
                var formData = {
                    id_menu : $('#id_menu').val(),
                    id_resep : $('#id_resep').val(),
                    bahan_id: $('#bahan_id').val(),
                    jumlah: $('#jumlah').val(),
                    id_satuan : $('#id_satuan').val(),
                    harga : $('#harga').val(),
                    jumlah_box : $('#jumlah_box').val(),
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url: '{{ route("tambahan_rincian_menu_harian.store") }}',
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if(response.success) {
                            $('#modalTambah').modal('hide');
                            $('#formTambah')[0].reset();
                            $('#tbl_list_master_menu').DataTable().ajax.reload(); // Refresh DataTable
                            Swal.fire({
                                icon: "success",
                                title: "BERHASIL",
                                text: "{{ session('success') }}",
                                showConfirmButton: false,
                                timer: 2000
                            });
                            
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function (xhr) {
                        alert('Terjadi kesalahan dalam menyimpan data');
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Event saat tombol Update diklik
            $(document).on('click', '.update-jumlah', function() {
                let id = $(this).data('id');
                let jumlah = $(this).closest('tr').find('.jumlah').val();
               
                $.ajax({
                    url: "{{ route('rincian_bahan.update_jumlah_temp') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        jumlah : jumlah
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: "success",
                            title: "BERHASIL",
                            text: "{{ session('success') }}",
                            showConfirmButton: false,
                            timer: 2000
                        });
                        $('#tbl_list_karbo').DataTable().ajax.reload();
                        $('#tbl_list_protein').DataTable().ajax.reload();
                        $('#tbl_list_sayur').DataTable().ajax.reload();
                        $('#tbl_list_buah').DataTable().ajax.reload();
                        $('#tbl_list_suplemen').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        alert(jumlah);
                    }
                });
            });

            // Simpan Jumlah Masak (protein & sayur)
            $('#btn_save_jumlah_masak_protein, #btn_save_jumlah_masak_sayur').on('click', function() {
                let btn     = $(this);
                let idMenu  = btn.data('id-menu');
                let tipe    = btn.data('tipe');
                let inputId = tipe === 'protein' ? '#jumlah_masak_protein' : '#jumlah_masak_sayur';
                let val     = $(inputId).val();

                if (val === '' || isNaN(val)) {
                    Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Masukkan nilai yang valid.' });
                    return;
                }

                $.ajax({
                    url: "{{ route('rincian_bahan.update_jumlah_masak') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id_menu: idMenu,
                        tipe: tipe,
                        jumlah_masak: val
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan saat menyimpan.' });
                    }
                });
            });
            
            $(document).on('click', '.update-jumlah-harga', function() {
                let id = $(this).data('id');
                let harga = $(this).closest('tr').find('.jumlah_harga').val();
               
                $.ajax({
                    url: "{{ route('rincian_bahan.update_harga') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        harga : harga
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: "success",
                            title: "BERHASIL",
                            text: "{{ session('success') }}",
                            showConfirmButton: false,
                            timer: 2000
                        });
                        $('#tbl_list_karbo').DataTable().ajax.reload();
                        $('#tbl_list_protein').DataTable().ajax.reload();
                        $('#tbl_list_sayur').DataTable().ajax.reload();
                        $('#tbl_list_buah').DataTable().ajax.reload();
                        $('#tbl_list_suplemen').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        alert(jumlah);
                    }
                });
            });

            $(document).on('click', '.update-jumlah-keterangan', function() {
                let id = $(this).data('id');
                let keterangan = $(this).closest('tr').find('.jumlah_keterangan').val();
               
                $.ajax({
                    url: "{{ route('rincian_bahan.update_keterangan') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        keterangan : keterangan
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: "success",
                            title: "BERHASIL",
                            text: "{{ session('success') }}",
                            showConfirmButton: false,
                            timer: 2000
                        });
                        $('#tbl_list_karbo').DataTable().ajax.reload();
                        $('#tbl_list_protein').DataTable().ajax.reload();
                        $('#tbl_list_sayur').DataTable().ajax.reload();
                        $('#tbl_list_buah').DataTable().ajax.reload();
                        $('#tbl_list_suplemen').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        alert(jumlah);
                    }
                });
            });

            $(document).on('click', '.update-jumlah-box', function() {
                let id = $(this).data('id');
                let jumlah = $(this).closest('tr').find('.jumlah_box').val();
               
                $.ajax({
                    url: "{{ route('rincian_bahan.update_jumlah_box') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        jumlah : jumlah
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: "success",
                            title: "BERHASIL",
                            text: "{{ session('success') }}",
                            showConfirmButton: false,
                            timer: 2000
                        });
                        $('#tbl_list_karbo').DataTable().ajax.reload();
                        $('#tbl_list_protein').DataTable().ajax.reload();
                        $('#tbl_list_sayur').DataTable().ajax.reload();
                        $('#tbl_list_buah').DataTable().ajax.reload();
                        $('#tbl_list_suplemen').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        alert(jumlah);
                    }
                });
            });  
            
            $(document).on('click', '.update-jumlah-sekolaha', function () {
    let id = $(this).data('id');
    let jumlah_a = $(this).siblings('.jumlah_a').val(); // Ambil nilai input golongan A

    $.ajax({
        url: "{{ route('rincian_bahan.update_sekolah') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            id: id,
            jumlah_a: jumlah_a
        },
        success: function (response) {
            Swal.fire({
                icon: "success",
                title: "BERHASIL",
                text: response.message ?? 'Berhasil update golongan A',
                showConfirmButton: false,
                timer: 2000
            });

            $('#tbl_list_karbo').DataTable().ajax.reload();
            $('#tbl_list_protein').DataTable().ajax.reload();
            $('#tbl_list_sayur').DataTable().ajax.reload();
            $('#tbl_list_buah').DataTable().ajax.reload();
            $('#tbl_list_suplemen').DataTable().ajax.reload();
            $('#tbl_list_sekolah').DataTable().ajax.reload();
        },
        error: function (xhr) {
            console.error(xhr.responseText);
        }
    });
});

            $(document).on('click', '.update-jumlah-sekolahb', function () {
    let id = $(this).data('id');
    let jumlah_b = $(this).siblings('.jumlah_b').val(); // Ambil nilai input yang berada di td yang sama

    $.ajax({
        url: "{{ route('rincian_bahan.update_sekolah') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            id: id,
            jumlah_b: jumlah_b
        },
        success: function (response) {
            Swal.fire({
                icon: "success",
                title: "BERHASIL",
                text: response.message ?? 'Berhasil update golongan B',
                showConfirmButton: false,
                timer: 2000
            });

            $('#tbl_list_karbo').DataTable().ajax.reload();
            $('#tbl_list_protein').DataTable().ajax.reload();
            $('#tbl_list_sayur').DataTable().ajax.reload();
            $('#tbl_list_buah').DataTable().ajax.reload();
            $('#tbl_list_suplemen').DataTable().ajax.reload();
            $('#tbl_list_sekolah').DataTable().ajax.reload();
        },
        error: function (xhr) {
            console.error(xhr.responseText);
        }
    });
});


        });
    </script>

    <script>
        $(document).ready(function() {
            const tabStorageKey = 'rincian_menu_active_tab_{{ $idmenu }}';

            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                const targetTab = $(e.target).attr('href');
                if (targetTab) {
                    localStorage.setItem(tabStorageKey, targetTab);
                }
            });

            const savedTab = localStorage.getItem(tabStorageKey);
            if (savedTab && $('a[data-toggle="tab"][href="' + savedTab + '"]').length) {
                $('a[data-toggle="tab"][href="' + savedTab + '"]').tab('show');
            }

            $('#bahan_id').select2({
                placeholder: "Pilih Bahan",
                allowClear: true
            });
            $('#id_satuan').select2({
                placeholder: "Pilih satuan",
                allowClear: true
            });
            $('#id_karbohidrat').select2({
                placeholder: "Pilih Karbo",
                allowClear: true
            });
            $('#id_sayur').select2({
                placeholder: "Pilih Sayur",
                allowClear: true
            });
            $('#id_protein').select2({
                placeholder: "Pilih Suplemen",
                allowClear: true
            });
            $('#id_buah').select2({
                placeholder: "Pilih Buah",
                allowClear: true
            });
            $('#id_suplemen').select2({
                placeholder: "Pilih Suplemen",
                allowClear: true
            });
            $('#pilihan').select2({
                placeholder: "Pilih menu",
                allowClear: true
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            function hitungRealisasi() {
                // Ambil nilai input
                let jumlah1 = parseFloat($('#jumlah_bahan_1').val()) || 0;
                let jumlah2 = parseFloat($('#jumlah_bahan_2').val()) || 0;
                let jumlah3 = parseFloat($('#jumlah_bahan_3').val()) || 0;
                let jumlah4 = parseFloat($('#jumlah_bahan_4').val()) || 0;
                let penyusutan1 = parseFloat($('#penyusutan_1').val()) || 0;
                let penyusutan2 = parseFloat($('#penyusutan_2').val()) || 0;
                let penyusutan3 = parseFloat($('#penyusutan_3').val()) || 0;
                let penyusutan4 = parseFloat($('#penyusutan_4').val()) || 0;

                // Rumus
                let total1 = jumlah1 - (jumlah1 * penyusutan1 / 100);
                let total2 = jumlah2 - (jumlah2 * penyusutan2 / 100);
                let total3 = jumlah3 - (jumlah3 * penyusutan3 / 100);
                let total4 = jumlah4 - (jumlah4 * penyusutan4 / 100);
                let hasil_mentah = jumlah1+jumlah2+jumlah3+jumlah4;
                let hasil = total1 + total2 + total3 + total4;

                // Tampilkan hasil ke input
                
                $('#kebutuhan_matang_realisasi').val(hasil.toFixed(2) + ' {{  'kg / potong / ekor' }}');
                $('#kebutuhan_mentah').val(hasil_mentah.toFixed(2) + ' {{  'kg / potong / ekor'}}');
                $('#kebutuhan_matang_realisasi_input').val(hasil);
                $('#kebutuhan_mentah_input').val(hasil_mentah);
                
            }

            // Panggil saat input berubah
            $('#jumlah_bahan_1, #jumlah_bahan_2').on('input', function() {
                hitungRealisasi();
            });
        });
        $(document).ready(function() {
            function cekSelisih() {
                // Ambil nilai kebutuhan_total_matang tanpa 'kg'
                let totalMatangStr = $('#kebutuhan_total_matang').val().replace('kg','').trim();
                let realisasiStr = $('#kebutuhan_matang_realisasi').val().replace('kg','').trim();
                
               // let totalMatangStr = $totalMatangEl.val()?.replace('kg', '').trim() || '0';
               // let realisasiStr = $realisasiEl.val()?.replace('kg', '').trim() || '0';
                let totalMatang = parseFloat(totalMatangStr) || 0;
                let realisasi = parseFloat(realisasiStr) || 0;

                if (totalMatang === 0) {
                // Tidak ada referensi, hapus warna
                $('#kebutuhan_matang_realisasi').css('background-color', '');
                return;
                }

                // Hitung selisih persen
                let selisihPersen = ((realisasi - totalMatang) / totalMatang) * 100;

                // Jika selisih >= -0.25% dan <=0% => hijau
                if (selisihPersen <= 0 && selisihPersen >= -0.5) {
                $('#kebutuhan_matang_realisasi').css('background-color', '#aaffaa'); // Hijau muda
                $('#submit_sayur').show();
                } else {
                $('#kebutuhan_matang_realisasi').css('background-color', '#ffaaaa'); // Merah muda
                $('#submit_sayur').hide();
                }
            }

            // Panggil fungsi cekSelisih() saat kebutuhan_matang_realisasi berubah
            $('#kebutuhan_matang_realisasi').on('input', cekSelisih);

            // Jika input ini diisi otomatis dari fungsi lain, panggil secara berkala
            setInterval(cekSelisih, 500);
        });
    </script>
     
    <script>
        $(document).ready(function() {
            function hitungRealisasi_sayur() {
                // Ambil nilai input
                let jumlah1 = parseFloat($('#jumlah_bahan_1_sayur').val()) || 0;
                let jumlah2 = parseFloat($('#jumlah_bahan_2_sayur').val()) || 0;
                let jumlah3 = parseFloat($('#jumlah_bahan_3_sayur').val()) || 0;
                let jumlah4 = parseFloat($('#jumlah_bahan_4_sayur').val()) || 0;
                let penyusutan1 = parseFloat($('#penyusutan_1_sayur').val()) || 0;
                let penyusutan2 = parseFloat($('#penyusutan_2_sayur').val()) || 0;
                let penyusutan3 = parseFloat($('#penyusutan_3_sayur').val()) || 0;
                let penyusutan4 = parseFloat($('#penyusutan_4_sayur').val()) || 0;

                // Rumus
                let total1 = jumlah1 - (jumlah1 * penyusutan1 / 100);
                let total2 = jumlah2 - (jumlah2 * penyusutan2 / 100);
                let total3 = jumlah3 - (jumlah3 * penyusutan3 / 100);
                let total4 = jumlah4 - (jumlah4 * penyusutan4 / 100);
                let hasil_mentah = jumlah1+jumlah2+jumlah3+jumlah4;
                let hasil = total1 + total2 + total3 + total4;

                // Tampilkan hasil ke input
                $('#kebutuhan_matang_realisasi_sayur').val(hasil.toFixed(2) + ' kg');
                $('#kebutuhan_mentah_sayur').val(hasil_mentah.toFixed(2) + ' kg');
                $('#kebutuhan_matang_realisasi_input_sayur').val(hasil);
                $('#kebutuhan_mentah_input_sayur').val(hasil_mentah);
                
            }

            // Panggil saat input berubah
            $('#jumlah_bahan_1_sayur, #jumlah_bahan_2_sayur,#jumlah_bahan_3_sayur,#jumlah_bahan_4_sayur').on('input', function() {
                hitungRealisasi_sayur();
            });
        });
        $(document).ready(function() {
            function cekSelisih_sayur() {
                // Ambil nilai kebutuhan_total_matang tanpa 'kg'
                let totalMatangStr = $('#kebutuhan_total_matang_sayur').val().replace('kg','').trim();
                let realisasiStr = $('#kebutuhan_matang_realisasi_sayur').val().replace('kg','').trim();
                
               // let totalMatangStr = $totalMatangEl.val()?.replace('kg', '').trim() || '0';
               // let realisasiStr = $realisasiEl.val()?.replace('kg', '').trim() || '0';
                let totalMatang = parseFloat(totalMatangStr) || 0;
                let realisasi = parseFloat(realisasiStr) || 0;

                if (totalMatang === 0) {
                // Tidak ada referensi, hapus warna
                $('#kebutuhan_matang_realisasi_sayur').css('background-color', '');
                return;
                }

                // Hitung selisih persen
                let selisihPersen = ((realisasi - totalMatang) / totalMatang) * 100;

                // Jika selisih >= -0.25% dan <=0% => hijau
                if (selisihPersen <= 0 && selisihPersen >= -0.5) {
                $('#kebutuhan_matang_realisasi_sayur').css('background-color', '#aaffaa'); // Hijau muda
                $('#submit_sayur').show();
                } else {
                $('#kebutuhan_matang_realisasi_sayur').css('background-color', '#ffaaaa'); // Merah muda
                $('#submit_sayur').hide();
                }
            }

            // Panggil fungsi cekSelisih() saat kebutuhan_matang_realisasi berubah
            $('#kebutuhan_matang_realisasi_sayur').on('input', cekSelisih_sayur);

            // Jika input ini diisi otomatis dari fungsi lain, panggil secara berkala
            setInterval(cekSelisih_sayur, 500);
        });
    </script>

    <!-- Menu Gizi Harian CRUD JavaScript -->
    <script>
        $(document).ready(function() {
            const idMenu = "{{ $idmenu }}";
            const autoGiziButtonConfig = {
                ai: {
                    selector: '#btnAutoGiziAi',
                    defaultHtml: '<i class="fas fa-magic"></i> Hitung Otomatis Berdasarkan AI',
                    loadingText: 'Sedang menghitung dengan AI...',
                    loadingTitle: 'Sedang menghitung AKG dengan AI',
                    loadingMessage: 'Mohon tunggu, AI sedang memproses nilai nutrisi menu.'
                },
                database: {
                    selector: '#btnAutoGiziDb',
                    defaultHtml: '<i class="fas fa-database"></i> Hitung Otomatis Berdasarkan Master Bahan Nutrisi',
                    loadingText: 'Sedang menghitung dari master bahan nutrisi...',
                    loadingTitle: 'Sedang menghitung nutrisi dari master bahan nutrisi',
                    loadingMessage: 'Mohon tunggu, sistem sedang menghitung berdasarkan data master bahan nutrisi.'
                }
            };

            // Load gizi data when page loads
            loadGiziData();

            // Load gizi data when MenuGizi tab is clicked
            $('a[href="#MenuGizi"]').on('click', function() {
                loadGiziData();
            });

            // Load gizi data
            function loadGiziData() {
                $.ajax({
                    url: `/menu-gizi-harian/${idMenu}`,
                    type: 'GET',
                    success: function(data) {
                        let html = '';
                        if (data && data.id) {
                            html = `
                                <tr>
                                    <td><strong>Energi (kkal)</strong></td>
                                    <td>${data.energi}</td>
                                </tr>
                                <tr>
                                    <td><strong>Protein (g)</strong></td>
                                    <td>${data.protein}</td>
                                </tr>
                                <tr>
                                    <td><strong>Lemak (g)</strong></td>
                                    <td>${data.lemak}</td>
                                </tr>
                                <tr>
                                    <td><strong>Karbohidrat (g)</strong></td>
                                    <td>${data.karbohidrat}</td>
                                </tr>
                                <tr>
                                    <td><strong>Serat (g)</strong></td>
                                    <td>${data.serat}</td>
                                </tr>
                                <tr>
                                    <td><strong>Natrium (mg)</strong></td>
                                    <td>${data.natrium}</td>
                                </tr>
                            `;
                            $('#giziId').val(data.id);
                            $('#btnDeleteGizi').show();
                            fillGiziForm(data);
                        } else {
                            html = `<tr><td colspan="2" class="text-center text-muted">Belum ada data nutrisi. Gunakan salah satu tombol hitung otomatis di atas.</td></tr>`;
                            $('#giziId').val('');
                            $('#btnDeleteGizi').hide();
                        }
                        $('#giziTableBody').html(html);
                    },
                    error: function() {
                        $('#giziTableBody').html(`<tr><td colspan="2" class="text-center text-danger">Error loading data</td></tr>`);
                    }
                });
            }

            function fillGiziForm(data) {
                $('#giziId').val(data.id || '');
                $('#giziEnergi').val(data.energi || 0);
                $('#giziProtein').val(data.protein || 0);
                $('#giziLemak').val(data.lemak || 0);
                $('#giziKarbohidrat').val(data.karbohidrat || 0);
                $('#giziSerat').val(data.serat || 0);
                $('#giziNatrium').val(data.natrium || 0);
            }

            let isCalculatingGizi = false;

            function setAutoGiziButtonLoading(mode, isLoading) {
                Object.keys(autoGiziButtonConfig).forEach(function(key) {
                    const config = autoGiziButtonConfig[key];
                    const $button = $(config.selector);

                    $button.prop('disabled', isLoading);

                    if (isLoading && key === mode) {
                        $button.html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> ' + config.loadingText);
                    } else {
                        $button.html(config.defaultHtml);
                    }
                });
            }

            function calculateGizi(mode = 'ai', showToast = true) {
                if (isCalculatingGizi) {
                    return;
                }

                const buttonConfig = autoGiziButtonConfig[mode] || autoGiziButtonConfig.ai;
                isCalculatingGizi = true;
                setAutoGiziButtonLoading(mode, true);

                if (showToast) {
                    Swal.fire({
                        title: buttonConfig.loadingTitle,
                        text: buttonConfig.loadingMessage,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                }

                $.ajax({
                    url: `/menu-gizi-harian/calculate/${idMenu}`,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        mode: mode
                    },
                    success: function(response) {
                        if (showToast && Swal.isVisible()) {
                            Swal.close();
                        }

                        if (response.data) {
                            fillGiziForm(response.data);
                        }

                        loadGiziData();

                        if (showToast) {
                            let message = response.message || 'Data nutrisi berhasil dihitung otomatis.';
                            if (response.missing_ingredients && response.missing_ingredients.length > 0) {
                                message += ' Beberapa bahan belum punya master gizi: ' + response.missing_ingredients.join(', ');
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: message.substring(0, 300),
                                allowOutsideClick: true
                            });
                        }
                    },
                    error: function(xhr) {
                        if (showToast && Swal.isVisible()) {
                            Swal.close();
                        }

                        if (!showToast) {
                            return;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Gagal menghitung data nutrisi otomatis.'
                        });
                    },
                    complete: function() {
                        isCalculatingGizi = false;
                        setAutoGiziButtonLoading(mode, false);
                    }
                });
            }

            $('#btnAutoGiziAi').on('click', function() {
                calculateGizi('ai', true);
            });

            $('#btnAutoGiziDb').on('click', function() {
                calculateGizi('database', true);
            });

            // Open modal for add/edit
            $('#btnAddGizi').on('click', function() {
                $.ajax({
                    url: `/menu-gizi-harian/${idMenu}`,
                    type: 'GET',
                    success: function(data) {
                        if (data && data.id) {
                            // Edit mode
                            fillGiziForm(data);
                            $('#btnDeleteGizi').show();
                            $('#modalGiziLabel').text('Edit Data Nutrisi Menu');
                        } else {
                            // Add mode
                            $('#formGizi')[0].reset();
                            $('#giziId').val('');
                            $('#giziIdMenu').val(idMenu);
                            $('#btnDeleteGizi').hide();
                            $('#modalGiziLabel').text('Tambah Data Nutrisi Menu');
                        }
                        $('#modalGizi').modal('show');
                    }
                });
            });

            // Save gizi data
            $('#btnSaveGizi').on('click', function() {
                const giziId = $('#giziId').val();
                const formData = {
                    _token: "{{ csrf_token() }}",
                    id_menu: idMenu,
                    energi: $('#giziEnergi').val(),
                    protein: $('#giziProtein').val(),
                    lemak: $('#giziLemak').val(),
                    karbohidrat: $('#giziKarbohidrat').val(),
                    serat: $('#giziSerat').val(),
                    natrium: $('#giziNatrium').val()
                };

                let url = '/menu-gizi-harian/store';
                let type = 'POST';

                if (giziId) {
                    url = `/menu-gizi-harian/update/${giziId}`;
                }

                $.ajax({
                    url: url,
                    type: type,
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message || 'Data nutrisi berhasil disimpan',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        $('#modalGizi').modal('hide');
                        loadGiziData();
                    },
                    error: function(xhr) {
                        console.log('Error Response:', xhr);
                        let errorMsg = 'Terjadi kesalahan!';
                        
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            // Validation errors
                            let errors = xhr.responseJSON.errors;
                            errorMsg = 'Validasi gagal:\n';
                            $.each(errors, function(key, value) {
                                errorMsg += '- ' + value[0] + '\n';
                            });
                        } else if (xhr.status === 404) {
                            errorMsg = 'Endpoint tidak ditemukan';
                        } else if (xhr.status === 500) {
                            errorMsg = 'Error server: ' + (xhr.responseJSON?.message || 'Silakan cek console');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg.substring(0, 300),
                            allowOutsideClick: true
                        });
                    }
                });
            });

            // Delete gizi data
            $('#btnDeleteGizi').on('click', function() {
                const giziId = $('#giziId').val();
                if (!giziId) return;

                Swal.fire({
                    title: 'Hapus Data',
                    text: 'Yakin ingin menghapus data nutrisi ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/menu-gizi-harian/delete/${giziId}`,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                                $('#modalGizi').modal('hide');
                                loadGiziData();
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Gagal menghapus data'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
    <!-- Modal Tambah Sekolah -->
    <div class="modal fade" id="modalTambahSekolah" tabindex="-1" aria-labelledby="modalTambahSekolahLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTambahSekolahLabel">Tambah Sekolah ke Menu</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="formTambahSekolah">
            @csrf
            <input type="hidden" name="id_menu" value="{{ $idmenu }}">
            <div class="form-group">
                <label>Sekolah <span class="text-danger">*</span></label>
                <select class="form-control select2" id="select_sekolah_tambah" name="id_sekolah" style="width:100%">
                    <option value="">-- Pilih Sekolah --</option>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jumlah Penerima A</label>
                        <input type="number" class="form-control" id="ts_jumlah_a" name="jumlah_a" min="0" value="0">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jumlah Penerima B</label>
                        <input type="number" class="form-control" id="ts_jumlah_b" name="jumlah_b" min="0" value="0">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block" id="btnSimpanSekolah">
                <i class="fas fa-save"></i> Simpan
            </button>
            </form>
        </div>
        </div>
    </div>
    </div>
    <script>
    $(document).ready(function() {
        // Load daftar sekolah ketika modal dibuka, lalu init Select2
        $('#modalTambahSekolah').on('show.bs.modal', function() {
            var select = $('#select_sekolah_tambah');

            // Destroy Select2 jika sudah ada agar tidak double
            if (select.hasClass('select2-hidden-accessible')) {
                select.select2('destroy');
            }

            $.get('{{ route('ajax.sekolah.list') }}', function(data) {
                var sekolahList = [];

                if (Array.isArray(data)) {
                    sekolahList = data;
                } else if (data && Array.isArray(data.data)) {
                    sekolahList = data.data;
                }

                select.empty().append('<option value="">-- Pilih Sekolah --</option>');
                $.each(sekolahList, function(i, s) {
                    select.append(
                        $('<option>', {
                            value: s.id,
                            'data-a': s.jumlah_a ?? 0,
                            'data-b': s.jumlah_b ?? 0,
                            text: s.nama_sekolah + ' (' + s.jenjang_sekolah + ')'
                        })
                    );
                });

                // Init Select2 setelah data terisi
                select.select2({
                    placeholder: '-- Pilih Sekolah --',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#modalTambahSekolah')
                });
            }).fail(function() {
                select.empty().append('<option value="">-- Gagal memuat sekolah --</option>');
            });
        });

        // Auto-fill jumlah saat sekolah dipilih (pakai event Select2)
        $(document).on('select2:select', '#select_sekolah_tambah', function() {
            var selected = $(this).find(':selected');
            $('#ts_jumlah_a').val(selected.data('a') || 0);
            $('#ts_jumlah_b').val(selected.data('b') || 0);
        });
        $(document).on('select2:unselect select2:clear', '#select_sekolah_tambah', function() {
            $('#ts_jumlah_a').val(0);
            $('#ts_jumlah_b').val(0);
        });

        // Destroy Select2 & reset form saat modal ditutup
        $('#modalTambahSekolah').on('hidden.bs.modal', function() {
            var select = $('#select_sekolah_tambah');
            if (select.hasClass('select2-hidden-accessible')) {
                select.select2('destroy');
            }
            $('#formTambahSekolah')[0].reset();
            select.empty().append('<option value="">-- Pilih Sekolah --</option>');
        });

        // Submit form tambah sekolah via AJAX
        $('#formTambahSekolah').on('submit', function(e) {
            e.preventDefault();
            var btn = $('#btnSimpanSekolah');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            $.ajax({
                url: '{{ route('rincian_sekolah.tambah_manual') }}',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#modalTambahSekolah').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 2000
                    });
                    $('#tbl_list_sekolah').DataTable().ajax.reload();
                    $('#tbl_list_karbo').DataTable().ajax.reload();
                    $('#tbl_list_protein').DataTable().ajax.reload();
                    $('#tbl_list_sayur').DataTable().ajax.reload();
                    $('#tbl_list_buah').DataTable().ajax.reload();
                    $('#tbl_list_suplemen').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    var msg = 'Terjadi kesalahan';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    Swal.fire({ icon: 'error', title: 'Error', text: msg });
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan');
                }
            });
        });
    });
    </script>
    <!-- jQuery -->
</body>
</html>
