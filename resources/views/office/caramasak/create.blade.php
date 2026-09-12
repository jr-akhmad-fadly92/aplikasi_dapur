<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        @include('Template.navbar')

        <!-- Main Sidebar Container -->
        @include('Template.left-sidebar')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3>Tambah Cara Masak</h3>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('caramasak.store') }}" method="POST">
                                        @csrf
                                        
                                        <!-- Menu -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Menu</label>
                                            <select class="form-control" name="id_menu">
                                                @foreach ($menu as $data)
                                                    <option value="{{ $data->id }}">{{ $data->nama_menu }}</option>
                                                @endforeach
                                            </select>
                                            @error('nama_menu')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- No Telepon -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Durasi</label>
                                            <input type="text" class="form-control @error('durasi') is-invalid @enderror" name="durasi" placeholder="Durasi">
                                            @error('durasi')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Alamat Supplier -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Keterangan Menu</label>
                                            <input type="text" class="form-control @error('keterangan_menu') is-invalid @enderror" name="keterangan_menu" placeholder="Keterangan Masak">
                                            @error('keterangan_menu')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <button type="reset" class="btn btn-warning">Reset</button>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Footer -->
        @include('Template.footer')

    </div>

    <!-- Required Scripts -->
    @include('Template.script')

</body>
</html>
