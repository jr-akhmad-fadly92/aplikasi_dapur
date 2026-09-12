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
                            <h1 class="m-0 text-dark">Tambah Data Inventori</h1>
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
                                    <h3>Tambah Data</h3>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('inventori.store') }}" method="POST">
                                        @csrf

                                        <!-- Kode Barang -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Kode Barang</label>
                                            <input type="text" class="form-control @error('kode_barang') is-invalid @enderror" name="kode_barang" step="any" placeholder="Kode Barang">
                                            @error('kode_barang')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Nama Barang -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Nama Barang</label>
                                            <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" name="nama_barang" step="any" placeholder="Nama Barang">
                                            @error('nama_barang')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Jenis Barang -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Jenis Barang</label>
                                            <select class="form-control @error('jenis_barang') is-invalid @enderror" name="jenis_barang">
                                                <option value="Ompreng">Ompreng</option>
                                                <option value="Rantang">Rantang</option>
                                                <option value="Alat Masak">Alat Masak</option>
                                                <option value="Lain Lain">Lain lain</option>
                                            </select>
                                            @error('jenis_barang')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Jumlah Stok -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Jumlah Stok</label>
                                            <input type="number" class="form-control @error('jumlah_stok') is-invalid @enderror" name="jumlah_stok" step="any" placeholder="---">
                                            @error('jumlah_stok')
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
