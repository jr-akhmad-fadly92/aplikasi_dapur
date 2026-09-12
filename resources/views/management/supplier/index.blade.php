<!DOCTYPE html>
<html lang="en">
<head>
    <title>Supplier Management</title>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        @include('management.navbar')
        @include('management.sidebar')
        @include('management.header')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">Supplier Management</h1>
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
                                    <h3 class="card-title">
                                        <a href="#" class="btn btn-primary btn-sm">
                                            Tambah Supplier
                                        </a>
                                    </h3>
                                </div>

                                <div class="card-body">
                                    <table id="tbl_list_supplier" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Supplier</th>
                                                <th>Jenis Supplier</th>
                                                <th>No Telepon</th>
                                                <th>Alamat</th>
                                                <th>Nama PIC</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>PT Maju Sejahtera</td>
                                                <td>Bahan Baku</td>
                                                <td>08123456789</td>
                                                <td>Jakarta, Indonesia</td>
                                                <td>Budi Santoso</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm">Edit</button>
                                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>CV Mandiri Jaya</td>
                                                <td>Peralatan</td>
                                                <td>082233445566</td>
                                                <td>Bandung, Indonesia</td>
                                                <td>Rina Widodo</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm">Edit</button>
                                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>UD Sentosa</td>
                                                <td>Bahan Tambahan</td>
                                                <td>08199887766</td>
                                                <td>Surabaya, Indonesia</td>
                                                <td>Andi Saputra</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm">Edit</button>
                                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Footer -->
        @include('management.footer')

    </div>

    @include('management.script')

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
