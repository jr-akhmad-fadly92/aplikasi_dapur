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
                                    <h1>{{ $header }}</h1>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">

                                    <form action="{{ route('prosestambahbahan') }}" method="post">
                                        {{ csrf_field() }}


                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Bahan</label>
                                            <input hidden type="text" class="form-control" name="menu_id" value="{{ $menu_id }}" placeholder="Nama">

                                            <select class="form-control select2" id="bahan_id" name="bahan_id">
                                                @foreach ($masterbahan as $data)

                                                <option value={{ $data->id }}>{{ $data->bahan }} ({{$data->nama_satuan }})</option>

                                                @endforeach
                                            </select>
                                            <!-- error message untuk name -->
                                            @error('bahan_id')
                                            <div class="alert alert-danger mt-2">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Jumlah</label>
                                            <input type="number" class="form-control @error('jumlah') is-invalid @enderror" name="jumlah" value="" placeholder="---">

                                            <!-- error message untuk name -->
                                            @error('jumlah')
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

    <!-- jQuery -->
</body>

</html>