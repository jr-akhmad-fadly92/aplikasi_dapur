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
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">{{ $header }}</h1>
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
                                       
                                    </h3>
                                </div>

                                <div class="card-body">
                                    <h3 class="card-title"><a href="{{ route('tingkatansekolah.create') }}" class="edit btn btn-primary btn-sm " id="btn-edit-post">Tambah</a></h3>

                                    <table id="tb_list_master_tingkatan_sekolah" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Jenjang</th>
                                                <th>Gramasi Nasi</th>
                                                <th>Gramasi Sayur</th>
                                                <th>Gramasi Protein</th>
                                                <th>Gramasi Buah</th>
                                                <th>Gramasi Susu</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data akan di-load oleh DataTables -->
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
        @include('Template.footer')

    </div>

    @include('Template.script')

    <script type="text/javascript">
        $(document).ready(function () {
            $('#tb_list_master_tingkatan_sekolah').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('tingkatansekolah.index') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'jenjang_sekolah', name: 'jenjang_sekolah' },
                    { data: 'gramasi_nasi', name: 'gramasi_nasi' },
                    { data: 'gramasi_sayur', name: 'gramasi_sayur' },
                    { data: 'gramasi_protein', name: 'gramasi_protein' },
                    { data: 'gramasi_buah', name: 'gramasi_buah' },
                    { data: 'gramasi_susu', name: 'gramasi_susu' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });
    </script>

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
