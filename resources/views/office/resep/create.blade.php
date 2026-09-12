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
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h1>{{ $header }}</h1>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <form action="{{ route('resep.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf

                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Nama Resep</label>
                                            <input type="text" class="form-control @error('nama_resep') is-invalid @enderror" name="nama_resep" value="" placeholder="---">
                                            @error('nama_resep')
                                                <div class="alert alert-danger mt-2">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Komponen Sehat</label>
                                            <select class="form-control" id="id_komponen_sehat" name="id_komponen_sehat">
                                                @foreach ($KomponenSehat as $data)
                                                    <option value="{{ $data->id }}">{{ $data->komponen }}</option>
                                                @endforeach
                                            </select>
                                            @error('id_komponen_sehat')
                                                <div class="alert alert-danger mt-2">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Jenis Resep</label>
                                            <select class="form-control" name="jenis_resep">
                                                <option value="tidak diolah" selected>Tidak Diolah (Default)</option>
                                                <option value="goreng">Goreng</option>
                                                <option value="rebus/kukus">Rebus/Kukus</option>
                                                <option value="tumis">Tumis</option>
                                                <option value="potong">Potong</option>
                                                <option value="utuh">Utuh</option>
                                                <option value="lain-lain">Lain-lain</option>
                                            </select>
                                            @error('jenis_resep')
                                                <div class="alert alert-danger mt-2">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3" hidden>
                                            <label class="font-weight-bold">Porsi Resep</label>
                                            <input type="number" class="form-control @error('porsi_resep') is-invalid @enderror" name="porsi_resep" value="1" placeholder="---">
                                            @error('porsi_resep')
                                                <div class="alert alert-danger mt-2">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3" hidden>
                                            <label class="font-weight-bold">Margin porsi ( % )</label>
                                            <input type="number" class="form-control @error('margin') is-invalid @enderror" name="margin" value="0" placeholder="---" min="0" max="100">
                                            @error('margin')
                                                <div class="alert alert-danger mt-2">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        
                                        
                                        <button type="submit" class="btn btn-md btn-primary me-3">Simpan</button>
                                        <a type="reset" href="{{ route('resep.index') }}" class="btn btn-md btn-warning">Kembali</a>
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
</body>
</html>
