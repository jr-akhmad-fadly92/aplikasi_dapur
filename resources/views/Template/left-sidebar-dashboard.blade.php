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
                @elseif(auth()->check() && is_null(auth()->user()->level))
                    <p class="d-block">Menu Dashboard</p>
                @endif

            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                
                
                

                
                @if (auth()->check() && in_array(auth()->user()->level, ["pengadaan", "admin", "backoffice", "kepala_dapur"]))
 <li class="nav-item has-treeview {{ Request::is('resep*') || Request::is('mastermenu*') || Request::is('master_satuan*') || Request::is('master_bahan*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link ">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dapur
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @if (auth()->user()->level == "backoffice" || "admin")
                        <li class="nav-item">
                            <a href="{{ route('master_satuan.index') }}" class="nav-link {{ Request::is('master_satuan*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Master Satuan</p>
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->level == "backoffice" ||auth()->user()->level == "admin")
                        <li class="nav-item">
                            <a href="{{ route('master_bahan.index') }}" class="nav-link {{ Request::is('master_bahan*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Master Bahan</p>
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->level == "backoffice" || "admin")
                        <li class="nav-item">
                            <a href="{{ route('resep.index') }}" class="nav-link {{ Request::is('resep*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Master Resep</p>
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->level == "pengadaan" || "admin")
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
                        @endif
                        
                        
                    </ul>
                </li>
                @endif
                @if (auth()->check() && in_array(auth()->user()->level, ["backoffice", "admin", "kepala_dapur","penerimaan"]))
         
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
                                <p>Dashboard Warehouse</p>
                            </a>
                        </li>
                        

                        
                    </ul>
                </li>
                @if (auth()->check() && in_array(auth()->user()->level, ["penerimaan", "kepala_dapur"]))
                    <li class="nav-item has-treeview">
                    <a href="#" class="nav-link ">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Penerimaan
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        
                        <li class="nav-item">
                            <a href="{{ route('master_wadah.index') }}" class="nav-link ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Master Wadah</p>
                            </a>
                        </li>
                       
                        <li class="nav-item">
                            <a href="{{ route('penerimaan_bahan.index') }}" class="nav-link ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Penerimaan Bahan Baku</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('transaksi_wadah.index') }}" class="nav-link ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Transaksi Kontainer</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('ompreng.formOmprengMasuk') }}" class="nav-link ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ompreng Masuk</p>
                            </a>
                        </li>

                    </ul>
                </li>
                @endif
               @if (auth()->check() && auth()->user()->level == "admin")
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
                @if (auth()->check() && in_array(auth()->user()->level, ["kitchen", "kepala_dapur","penerimaan"]))
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link ">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Menu
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
                       
                    </ul>
                </li>
                @endif
                @if (auth()->check() && in_array(auth()->user()->level, ["admin", "backoffice", "kepala_dapur"]))
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link ">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Data
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        
                        <li class="nav-item">
                            <a href="{{ route('mastersupplier.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Supplier</p>
                            </a>
                        </li>
                       <!--li class="nav-item">
                            <a href="{{ route('tingkatansekolah.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tingkatan Sekolah</p>
                            </a>
                        </!--li-->

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
                            <a href="{{ route('pengajuan_po.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pengajuan PO</p>
                            </a>
                        </li>
                        
                         <li class="nav-item">
                            <a href="{{ route('kontrak.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Kontrak Supplier</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/dashboard_warehouse" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Warehouse</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @if (auth()->check() && in_array(auth()->user()->level, ["backoffice", "admin", "kepala_dapur"]))
         
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link ">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Administrasi
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                       
                       
                      

                        
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
