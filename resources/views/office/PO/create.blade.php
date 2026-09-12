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
    </style>
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
                            <form action="{{ route('pengajuan_po.store') }}" method="POST" enctype="multipart/form-data">
                        
                                @csrf
                                
                                
                                    <input type="hidden" class="form-control @error('id_menu') is-invalid @enderror" name="id_menu" id="id_menu" value="{{ $id }}" placeholder="---">

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Nomor PO</label>
                                    <input type="text" class="form-control @error('nomor_po') is-invalid @enderror" name="nomor_po" value="{{ $nomor_PO }}" placeholder="---">
                                
                                    <!-- error message untuk name -->
                                    @error('nomor_po')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Supplier</label>
                                            <select class="form-control select2" id="supplier" name="supplier" style="width: 100%">
                                                 @foreach ($kontrak as $data)
                                                 
                                                 <option value={{ $data->id }} >{{ $data->nama_supplier }} </option>
                                                 
                                                 @endforeach
                                                  <option value=13 >Koperasi Seribu Impian </option>
                                            </select>
                                    <!-- error message untuk name -->
                                    @error('Supplier')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Tanggal Pengajuan</label>
                                    <input type="date" class="form-control @error('tanggal_pengajuan') is-invalid @enderror"  value="{{ old('tanggal_pengajuan', date('Y-m-d')) }}" name="tanggal_pengajuan">
                                    @error('tanggal_pengajuan')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>


                                <div class="form-group mb-3" hidden>
                                    <label class="font-weight-bold">Yang Mengajukan</label>
                                            <select class="form-control select2" id="id_karyawan" name="id_karyawan" style="width: 100%">
                                                 @foreach ($karyawan as $data)
                                                 
                                                 <option value={{ $data->id }} >{{ $data->nama }} </option>
                                                 
                                                 @endforeach
                                            </select>
                                    <!-- error message untuk name -->
                                    @error('Supplier')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-md btn-primary me-3">Lanjutkan</button>
                                <a href="{{ route('rincian_menu_po',$id) }}" class="btn btn-md btn-warning" id="btn-edit-post">Kembali</a>
                        

                            </form> 
                        </div>
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                       >
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
    <script>
        $(document).ready(function() {
            $('#supplier').select2({
                placeholder: "Pilih Bahan",
                allowClear: true
            });
        });
    </script>
    
    <!-- jQuery -->
</body>
</html>
