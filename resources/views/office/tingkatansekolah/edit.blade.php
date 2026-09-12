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
                            <form action="{{ route('tingkatansekolah.update', $tingkatansekolah->id) }}" method="POST" enctype="multipart/form-data">
                        
                                @csrf
                                @method('PUT')

                                

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Jenjang</label>
                                    <select name="jenjang_sekolah" class="form-control" required>
                                        <option value="tk-sd_kelas_3" {{ old('jenjang_sekolah', $tingkatansekolah->jenjang_sekolah) == 'tk-sd_kelas_3' ? 'selected' : '' }}>TK - SD Kelas 3</option>
                                        <option value="sd_kelas_4-6" {{ old('jenjang_sekolah', $tingkatansekolah->jenjang_sekolah) == 'sd_kelas_4-6' ? 'selected' : '' }}>SD Kelas 4-6</option>
                                        <option value="smp" {{ old('jenjang_sekolah', $tingkatansekolah->jenjang_sekolah) == 'smp' ? 'selected' : '' }}>SMP</option>
                                        <option value="sma" {{ old('jenjang_sekolah', $tingkatansekolah->jenjang_sekolah) == 'sma' ? 'selected' : '' }}>SMA</option>
                                        <option value="ibu_hamil" {{ old('jenjang_sekolah', $tingkatansekolah->jenjang_sekolah) == 'ibu_hamil' ? 'selected' : '' }}>Ibu Hamil</option>
                                    </select>

                                    @error('jenjang_sekolah')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                

                                <div class="form-group mb-3">
                                    <label>Gramasi Nasi</label>
                                    <input type="number" name="gramasi_nasi" class="form-control" value="{{ $tingkatansekolah->gramasi_nasi }}" required>
                                    
                                    @error('gramasi_nasi')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Gramasi Sayur</label>
                                    <input name="gramasi_sayur" class="form-control" value="{{ $tingkatansekolah->gramasi_sayur }}" required>

                                    @error('gramasi_sayur')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Gramasi Protein</label>
                                    <input type="number" name="gramasi_protein" class="form-control" value="{{ $tingkatansekolah->gramasi_protein }}" required>

                                    @error('gramasi_protein')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Gramasi Buah</label>
                                    <input name="gramasi_buah" class="form-control" value="{{ $tingkatansekolah->gramasi_buah }}" required>

                                    @error('gramasi_buah')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Gramasi Susu</label>
                                    <input type="number" name="gramasi_susu" class="form-control" value="{{ $tingkatansekolah->gramasi_susu }}" required>

                                    @error('gramasi_susu')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                
                                        
                                

                                <button type="submit" class="btn btn-md btn-primary me-3">UPDATE</button>
                                <button type="reset" class="btn btn-md btn-warning">RESET</button>

                            </form> 
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
    
    <!-- jQuery -->
</body>
</html>
