<!DOCTYPE html>
<html lang="en">
<head>
    <title>Detail Inventori</title>
    @include('Template.head')
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        @include('Template.navbar')

        <!-- Main Sidebar Container -->
        @include('Template.left-sidebar')

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Inventori</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Container untuk Detail Barang -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>Kode Barang: {{ $inventori->kode_barang }}</h5>
                            <h5>Nama Barang: {{ $inventori->nama_barang }}</h5>
                            <h5>Jenis Barang: {{ $inventori->jenis_barang }}</h5>
                            <h5>Jumlah Stok: {{ $inventori->jumlah_stok }}</h5>
                        </div>
                    </div>

                    <!-- Tabel Transaksi Inventaris -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Riwayat Transaksi</h3>
                        </div>
                        <div class="card-body">
                        <h3 class="card-title">
                            <a href="{{ route('transaksiinventori.create', ['kode_barang' => $inventori->kode_barang]) }}" class="btn btn-primary btn-sm">
                                Tambah Data
                            </a>
                        </h3>
                        <br><br>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Barang</th>
                                        <th>Kondisi</th>
                                        <th>PIC</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($transaksi as $index => $tr)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $tr->kode_barang }}</td>
                                        <td>{{ $tr->kondisi }}</td>
                                        <td>{{ $tr->pic }}</td>
                                        <td>{{ $tr->jumlah }}</td>
                                        <td>{{ $tr->status }}</td>
                                        <td>
                                            <a href="{{ route('transaksiinventori.edit', $tr->id) }}" class="btn btn-warning">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($transaksi->isEmpty())
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada transaksi untuk barang ini</td>
                                    </tr>
                                @endif
                            </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tombol Kembali -->
                    <a href="{{ route('inventori.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Footer -->
        @include('Template.footer')

    </div>

    @include('Template.script')

    <script src="{{ asset('AdminLte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
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

</body>
</html>

