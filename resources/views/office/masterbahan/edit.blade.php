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
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Starter Page</li>
                            </ol>
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
                            <form action="{{ route('master_bahan.update', $bahan->id) }}" method="POST" enctype="multipart/form-data">
                        
                                @csrf
                                @method('PUT')

                                

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Bahan</label>
                                    <input type="text" class="form-control @error('bahan') is-invalid @enderror" name="bahan" value="{{ old('bahan', $bahan->bahan) }}" placeholder="Bahan">
                                
                                    <!-- error message untuk name -->
                                    @error('bahan')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" hidden>
                                    <label class="font-weight-bold">Gramasi</label>
                                    <input type="number" step="0.01" class="form-control @error('gramasi') is-invalid @enderror" name="gramasi" value="{{ old('gramasi', $bahan->gramasi) }}" placeholder="---">
                                
                                    <!-- error message untuk level -->
                                    @error('gramasi')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                
                                    
                                        <div class="form-group mb-3" hidden>
                                            <label class="font-weight-bold">satuan gudang </label>
                                             <select class="form-control" id="satuan_gudang" name="satuan_gudang">
                                                 @foreach ($satuan as $data)
                                                 
                                                 <option value={{ $data->id }} 
                                                    @if($bahan->satuan_gudang == $data->id ) selected @endif 
                                                    >{{ $data->satuan }}</option>
                                                  @endforeach
                                            </select>
                                            <!-- error message untuk price -->
                                            @error('satuan_gudang')
                                                <div class="alert alert-danger mt-2">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                         <div class="form-group mb-3">
                                            <label class="font-weight-bold">satuan bahan </label>
                                             <select class="form-control select2" id="satuan_bahan" name="satuan_bahan">
                                                 @foreach ($satuan as $data)
                                                 
                                                 <option value={{ $data->id }} 
                                                    @if($bahan->satuan_bahan == $data->id ) selected @endif 
                                                    >{{ $data->satuan }}</option>
                                                  @endforeach
                                            </select>
                                            <!-- error message untuk price -->
                                            @error('satuan_bahan')
                                                <div class="alert alert-danger mt-2">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Jenis</label>
                                             <select class="form-control" id="jenis" name="jenis">
                                                 <option value=1 @if($bahan->jenis==1) selected @endif >Karbo</option>
                                                 <option value=2 @if($bahan->jenis==2) selected @endif >lauk</option>
                                                 <option value=3 @if($bahan->jenis==3) selected @endif >sayur</option>
                                                 <option value=4 @if($bahan->jenis==4) selected @endif >buah</option>
                                                 <option value=5 @if($bahan->jenis==5) selected @endif >Suplemen</option>
                                                 <option value=6 @if($bahan->jenis==6) selected @endif >bumbu</option>
                                                 <option value=7 @if($bahan->jenis==7) selected @endif >Non Pangan (Penunjang)</option>
                                                 
                                             </select>
                                            <!-- error message untuk price -->
                                            @error('satuan_bahan')
                                                <div class="alert alert-danger mt-2">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Detail Golongan</label>
                                             <select class="form-control select2" id="golongan" name="golongan">
                                                @foreach($golongan as $data)
                                                    <option value={{ $data->id }} @if($golongan_bahan->golongan_id == $data->id) selected @endif>{{ $data->golongan }}</option>


                                                @endforeach
                                                 
                                             </select>
                                            <!-- error message untuk price -->
                                            @error('golongan')
                                                <div class="alert alert-danger mt-2">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                <button type="submit" class="btn btn-md btn-primary me-3">UPDATE</button>
                                <a type="reset" class="btn btn-md btn-warning" href="{{ route('master_bahan.index') }}">Kembali</a>


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
            $('#satuan_bahan').select2({
                placeholder: "Pilih Satuan",
                allowClear: true
            });
             $('#golongan').select2({
                placeholder: "Pilih golongan",
                allowClear: true
            });

        });
    </script>
    <!-- jQuery -->
</body>
</html>
