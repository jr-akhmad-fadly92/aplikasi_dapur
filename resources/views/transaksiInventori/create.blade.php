<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tambah Transaksi</title>
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
                            <h1 class="m-0 text-dark">Tambah Data Transaksi Inventori</h1>
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
                                    <form action="{{ route('transaksiinventori.store') }}" method="POST">
                                        @csrf

                                        <div class="form-group">
                                            <label>Kode Barang</label>
                                            <input type="text" class="form-control" name="kode_barang" value="{{ $kode_barang }}" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label>Kondisi</label>
                                            <input type="text" class="form-control" name="kondisi" required placeholder="Kondisi">
                                        </div>

                                        <div class="form-group">
                                            <label>PIC</label>
                                            <input type="text" class="form-control" name="pic" placeholder="---" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Jumlah</label>
                                            <input type="number" class="form-control" name="jumlah" placeholder="---" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Status</label>
                                            <input type="text" class="form-control" name="status" placeholder="Status" required>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <button type="reset" class="btn btn-md btn-warning">Reset</button>
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
