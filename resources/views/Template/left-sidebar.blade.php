<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="{{ asset('image/logo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Aplikasi Dapur</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <i class="fa fa-solid fa-user img-circle elevation-2 fa-2x"></i>
                
            </div>
            <div class="info">
               @if(auth()->check() && auth()->user()->level == "penerimaan")
                    <a href="{{ route('dashboard_penerimaan') }}" class="d-block">{{ strtoupper(auth()->user()->name) }}</a>
                @elseif(auth()->check() && auth()->user()->level == "admin")
                    <a href="{{ route('home') }}" class="d-block">{{ strtoupper(auth()->user()->name) }}</a>
                @elseif(auth()->check() && auth()->user()->level == "kitchen")
                    <a href="{{ route('dashboard_kitchen') }}" class="d-block">{{ strtoupper(auth()->user()->name) }}</a>
                @elseif(auth()->check() && auth()->user()->level == "backoffice")
                    <a href="{{ route('dashboard_office') }}" class="d-block">{{ strtoupper(auth()->user()->name) }}</a>
                @elseif(auth()->check() && auth()->user()->level == "warehouse")
                    <a href="{{ route('dashboard_warehouse') }}" class="d-block">{{ strtoupper(auth()->user()->name) }}</a>
                @elseif(auth()->check() && auth()->user()->level == "kepala_dapur")
                    <a href="{{ route('dashboard_kepala_dapur') }}" class="d-block">{{ strtoupper(auth()->user()->name) }}</a>
                @elseif(auth()->check() && auth()->user()->level == "ahli_akuntan")
                    <a href="{{ route('dashboard_office') }}" class="d-block">{{ strtoupper(auth()->user()->name) }}</a>
                @elseif(auth()->check() && auth()->user()->level == "ahli_akuntan")
                    <a href="{{ route('dashboard_office') }}" class="d-block">{{ strtoupper(auth()->user()->name) }}</a>
                @elseif(auth()->check() && is_null(auth()->user()->level))
                    <a href="{{ route('login') }}" class="d-block">Silakan login kembali</a>
                @endif

            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                @if (auth()->check() && in_array(auth()->user()->level, ["pengadaan", "admin", "backoffice", "kepala_dapur","ahli_akuntan"]))
           
                <li class="nav-item has-treeview {{ Request::is('resep*')  || Request::is('master_satuan*') || Request::is('master_bahan*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link ">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Master Data
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @if (auth()->user()->level == "backoffice" || auth()->user()->level == "admin" ||auth()->user()->level == "kepala_dapur")
                  
                        <li class="nav-item">
                            <a href="{{ route('master_satuan.index') }}" class="nav-link {{ Request::is('master_satuan*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Master Satuan</p>
                            </a>
                        </li>
               
                        
               
                        <li class="nav-item">
                            <a href="{{ route('resep.index') }}" class="nav-link {{ Request::is('resep*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Master Resep</p>
                            </a>
                        </li>
                 
                        <li class="nav-item">
                            <a href="{{ route('golongan') }}" class="nav-link {{ Request::is('golongan*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Golongan</p>
                            </a>
                        </li>
                
                        <li class="nav-item">
                            <a href="{{ route('box-bahan-baku.index') }}" class="nav-link {{ Request::is('box-bahan-baku*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Box</p>
                            </a>
                        </li>
                
                        <li class="nav-item">
                            <a href="{{ route('buffer.index') }}" class="nav-link {{ Request::is('buffer*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Buffer</p>
                            </a>
                        </li>
                 
                        
                        <li class="nav-item">
                            <a href="{{ route('mastersupplier.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Supplier</p>
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->level == "backoffice" || auth()->user()->level == "admin" ||auth()->user()->level == "kepala_dapur"||auth()->user()->level == "ahli_akuntan")

                        <li class="nav-item">
                                        <a href="{{ route('master_bahan.index') }}" class="nav-link {{ Request::is('master_bahan*') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Master Bahan</p>
                                        </a>
                                    </li>
                        <li class="nav-item">
                            <a href="{{ route('master_bahan_nutrisi.index') }}" class="nav-link {{ Request::is('master_bahan_nutrisi*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Master Bahan Nutrisi</p>
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->level == "ahli_akuntan" || auth()->user()->level == "admin" ||auth()->user()->level == "kepala_dapur")
                  
                        <li class="nav-item">
                            <a href="/dashboard-rincian-kontrak/13" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Master Harga</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('harga-het.index') }}" class="nav-link {{ request()->routeIs('harga-het.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Harga HET</p>
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->level == "backoffice" || auth()->user()->level == "admin" ||auth()->user()->level == "kepala_dapur")
                  
                        <li class="nav-item">
                            <a href="{{ route('datasekolah.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Sekolah</p>
                            </a>
                        </li>
                        
                        
                        <li class="nav-item">
                            <a href="{{ route('datadapur.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Dapur</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('paketmenu.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Paket Menu</p>
                            </a>
                        </li>
                        
                        @endif
                    </ul>
                </li>
                @endif

                @if (auth()->check() && in_array(auth()->user()->level, ["admin", "backoffice", "kepala_dapur","ahli_akuntan"]))
            
                <li class="nav-item has-treeview" hidden>
                    <a href="#" class="nav-link ">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Yayasan
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                       <li class="nav-item">
                            <a href="/master_libur" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Daftar Libur</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/master-bantuan" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Bantuan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/detail_kbm" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data KBM</p>
                            </a>
                        </li>
                      
                    </ul>
                </li>
                @endif

                @if (auth()->check() && in_array(auth()->user()->level, ["admin", "backoffice", "kepala_dapur","ahli_akuntan"]))
            
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link ">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dapur
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @if (auth()->check() && in_array(auth()->user()->level, ["admin", "backoffice", "kepala_dapur", "ahli_akuntan"]))
                        <li class="nav-item">
                            <a href="{{ route('dashboard_kepala_dapur') }}" class="nav-link {{ request()->routeIs('dashboard_kepala_dapur') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Dashboard Kepala Dapur</p>
                            </a>
                        </li>
                        @endif

                        <li class="nav-item">
                            <a href="{{ route('mastermenu.index') }}" class="nav-link {{ request()->routeIs('mastermenu.*') && !request()->routeIs('mastermenu.print-center') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pengajuan menu</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('mastermenu.print-center') }}" class="nav-link {{ request()->routeIs('mastermenu.print-center') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pusat Cetak</p>
                            </a>
                        </li>
                         @if (auth()->check() && in_array(auth()->user()->level, ["admin", "kepala_dapur","ahli_akuntan","backoffice"]))
            
                        <li class="nav-item">
                            <a href="{{ route('pengajuan_po.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pengajuan PO</p>
                            </a>
                        </li>
                        
                        
                        <li class="nav-item" hidden>
                            <a href="{{ route('Rekap_po.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Rekap PO</p>
                            </a>
                        </li>
                        @endif
                        @if (auth()->check() && in_array(auth()->user()->level, ["admin", "backoffice"]))
                        <li class="nav-item" hidden>
                            <a href="{{ route('data_penerimaan_bahan') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Koreksi Data Penerimaan</p>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a href="{{ route('suratJalan') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Surat jalan</p>
                            </a>
                        </li>
                      
                    </ul>
                </li>
                @endif
                @if (auth()->check() && in_array(auth()->user()->level, ["admin", "backoffice", "kepala_dapur","ahli_akuntan"]))
            
                <li class="nav-item has-treeview" hidden>
                    <a href="#" class="nav-link ">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            SDM
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                       
                        <li class="nav-item">
                            <a href="/karyawan-dapur" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Karyawan</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="/bagian" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Bagian</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="/tugas" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Tugas</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/waktu-kerja" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Sif</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @if (auth()->check() && in_array(auth()->user()->level, ["admin", "backoffice", "ahli_akuntan","kepala_dapur"]))
            
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link ">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Laporan
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                         @if (auth()->check() && in_array(auth()->user()->level, ["admin", "backoffice", "kepala_dapur"]))                        
                        <li class="nav-item" hidden>
                            <a href="{{ route('laporan_hasil_masak') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Laporan Hasil Masak</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('menu-laporan-masak-harian') }}" class="nav-link {{ Request::is('menu-laporan-masak-harian*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Laporan Masak Harian</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ route('laporanPersiapan') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Laporan Persiapan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('laporan-bahan-baku.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Laporan Bahan Baku</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('laporan-bahan.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Laporan Pembelian</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('counter-pax.index') }}" class="nav-link {{ Request::is('counter-pax*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Counter Pax</p>
                            </a>
                        </li>
                        @endif
                        @if (auth()->check() && in_array(auth()->user()->level, ["admin", "ahli_akuntan", "kepala_dapur"]))
                        <li class="nav-item" hidden>
                            <a href="{{ route('laporan_harian') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Harian</p>
                            </a>
                        </li>

                        <li class="nav-item" hidden>
                            <a href="{{ route('laporanPersiapan') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>bulanan</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/laporan-biaya-bahan-baku" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Biaya Bahan Baku</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/laporan-biaya-operasional" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Biaya Operasional</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/laporan-biaya-sewa" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Biaya Infra dan peralatan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('laporan.keuangan.index')}}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Realisasi Anggaran </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Penggunaan Dana </p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if (auth()->check() && in_array(auth()->user()->level, ["admin", "backoffice", "kepala_dapur"]))
            
                <li  class="nav-item has-treeview {{ Request::is('checklist*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link ">
                        <i class="nav-icon fas fa-calendar-check"></i>
                        <p>
                            Checklist Pekerjaan
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a hidden href="{{ route('checklistGudang') }}" class="nav-link {{ Request::is('checklistGudang*') ? 'active' : '' }}">
                                <i class="far fa-check-square nav-icon"></i>
                                <p>Checklist Gudang Keluar</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('checklistPenerimaan') }}" class="nav-link {{ Request::is('checklistPenerimaan*') ? 'active' : '' }}">
                            <i class="far fa-check-square nav-icon"></i>
                                <p>Checklist Penerimaan</p>
                                <br>
                                <p>& Gudang</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a  href="{{ route('upload-data.index') }}" class="nav-link {{ Request::is('upload-data*') ? 'active' : '' }}">
                            <i class="far fa-check-square nav-icon"></i>
                                <p>Upload Data</p>
                            </a>
                        </li>
                        
                    </ul>
                </li>
                @endif
                
                
                @if (auth()->check() && in_array(auth()->user()->level, ["penerimaan", "admin", "backoffice"]))
            
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link ">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Penerimaan
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        
                        <li class="nav-item" hidden>
                            <a href="{{ route('master_wadah.index') }}" class="nav-link ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Master Wadah</p>
                            </a>
                        </li>
                       
                        <li class="nav-item" hidden>
                            <a href="{{ route('penerimaan_bahan.index') }}" class="nav-link ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Penerimaan Bahan Baku</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('laporan_penerimaan') }}" class="nav-link ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Laporan Penerimaan</p>
                            </a>
                        </li>

                        <li class="nav-item" hidden>
                            <a href="{{ route('transaksi_wadah.index') }}" class="nav-link ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Transaksi Kontainer</p>
                            </a>
                        </li>

                        <li class="nav-item" hidden>
                            <a href="{{ route('ompreng.formOmprengMasuk') }}" class="nav-link ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ompreng Masuk</p>
                            </a>
                        </li>

                    </ul>
                </li>
                @endif
                @if (auth()->check() && in_array(auth()->user()->level, [ "admin"]))
            
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link ">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Setting Admin
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        
                        <li class="nav-item">
                            <a href="{{ route('users_crud.index') }}" class="nav-link ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Users</p>
                            </a>
                        </li>
                       
                       
                    </ul>
                </li>
                @endif
                @if (auth()->check() && in_array(auth()->user()->level, ["warehouse","penerimaan","admin", "kepala_dapur"]))
            
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link ">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Warehouse
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    
                    <ul class="nav nav-treeview">
                       
                        <li class="nav-item">
                            <a href="/dashboard_warehouse" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/warehouse/form_warehouse?type=out" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Barang Keluar</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/warehouse/form_warehouse?type=in" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Barang Masuk</p>
                            </a>
                        </li>
                        
                    </ul>
                </li>
                @endif
                @if (auth()->check() && in_array(auth()->user()->level, ["ahli_akuntan", "admin"]))
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link ">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Akutansi
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        
                        <li class="nav-item">
                            <a href="{{ route('Rekap_po.index') }}" class="nav-link ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Rekap PO</p>
                            </a>
                        </li>
                       
                       
                    </ul>
             
                    
                    <ul class="nav nav-treeview">
                       
                        <li class="nav-item">
                            <a href="{{ route('kas-kecil.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Kas Kecil</p>
                            </a>
                        </li>
                      
                    </ul>
           
                </li>
                @endif
                @if (auth()->check() && in_array(auth()->user()->level, ["kitchen", "admin","penerimaan"]))
            
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link ">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Serving
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        
                        <li class="nav-item">
                            <a href="{{ route('gramasi.index') }}" class="nav-link ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Gramasi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('hasil-masak.index') }}" class="nav-link ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Hasil Masak</p>
                            </a>
                        </li>
                        @if (auth()->check() && in_array(auth()->user()->level, ["kitchen", "penerimaan"]))
                        <li class="nav-item">
                            <a href="{{ route('menu-laporan-masak-harian') }}" class="nav-link {{ Request::is('menu-laporan-masak-harian*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Laporan Masak Harian</p>
                            </a>
                        </li>
                        @endif
                       
                    </ul>
                </li>
                @endif
                <li class="nav-item">
                    <a href="{{ route('logout') }}" class="nav-link">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Logout
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
