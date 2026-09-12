<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
</head>
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
                    <div class="row">
                    <div class="col-12">
                        <div class="card">
                        <div class="card-header">
                           <h1 >{{ $header }}</h1>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            @if($menu->status_pengajuan == "pending" || $menu->status_pengajuan == "approved" || $menu->status_pengajuan == "kirim po")
                                <h3 class="card-title">
                                @if($totalKosong == 0 ) 
                                   <a href="{{ route('pengajuan_po_buat_po', $menu->id)  }}" class="edit btn btn-primary btn-sm " id="btn-edit-post">Buat PO</a> 
                                   
                                    <a href="{{ route('simpan_rincian_menu_po', $menu->id)  }}" class="edit btn btn-primary btn-sm " id="btn-edit-post">Selesai</a>
                                
                                @else
                                    Mohon lengkapi terlebih dahulu hpp di rincian kontrak    
                                    <a href="{{ route('dashboard-rincian-kontrak', 1) }}" 
                                    class="btn btn-primary btn-sm" 
                                    id="btn-edit-post" 
                                    target="_blank" 
                                    rel="noopener noreferrer">
                                    Cek Koperasi
                                    </a> 
                                @endif     
                               
                                <a href="javascript:location.reload();" class="btn btn-primary btn-sm" id="btn-reload">
                                    Muat Ulang
                                </a>
                                <a href="{{ route('pilih_menu_po') }}"  class="btn btn-primary btn-sm " id="btn-edit-post">Kembali</a>
 
                                </h3>
                                    
                            @else
                                    <a href="{{ route('pilih_menu_po') }}"  class="btn btn-primary flex-fill" id="btn-edit-post">Kembali</a>

                            @endif
                            
                            <table id="tbl_list_master_menu" class="table table-bordered table-hover" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Resep</th>
                                    <th>Bahan</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Total Harga</th>
                                    <th>Supplier</th>
                                    <th>Nomor PO</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                        
                        
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

        <!-- Main Footer -->
        @include('Template.footer')
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
  
    @include('Template.script')
   
    <script type="text/javascript">
    $(document).ready(function () {
    $('#tbl_list_master_menu').DataTable({
            
            ajax: '{{ url()->current() }}',
            columns: [
                 { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama_resep', name: 'nama_resep' },
                { data: 'bahan', name: 'bahan' },
                { data: 'total_berat', name: 'total_berat' },
                { data: 'satuan', name: 'satuan' },
                { data: 'total_po', name: 'total_po' },
                {data: 'action', name: 'action', orderable: false, searchable: false}, // Aksi (tombol)
                { data: 'no_po', name: 'no_po' },


            ]
        });
    });
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
    
    <!-- jQuery -->
</body>
</html>
