<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        @include('Template.navbar')
        @include('Template.left-sidebar')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark" id="currentTime">Starter Page</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">{{ $header }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Bahan Masuk</h3>
                                </div>
                                <div class="card-body">
                                    <table id="tbl_list_tb_penerimaan" class="table table-bordered table-hover" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Bahan</th>
                                                <th>tanggal_kedatangan</th>
                                                <th>Jumlah Sudah Datang</th>
                                                
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Modal Konfirmasi Hapus -->
        <div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="modalDeleteLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalDeleteLabel">Konfirmasi Hapus</h5>
                </div>
                <div class="modal-body">
                Apakah Anda yakin ingin menghapus data ini?
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
                </div>
            </div>
            </div>
        </div>
  
        @include('Template.footer')
    </div>

    @include('Template.script')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#tbl_list_tb_penerimaan').DataTable({
                ajax: '{{ url()->current() }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'bahan_dan_id', name: 'bahan_dan_id' },
                    { data: 'waktu_datang', name: 'waktu_datang' },
                    { data: 'jumlah_yang_datang', name: 'jumlah_yang_datang' },
                    
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

    <script>
        let deleteId;

        // Saat tombol Delete diklik
        $(document).on('click', '.delete', function () {
            deleteId = $(this).data('id');
            $('#modalDelete').modal('show');
        });

        // Saat tombol "Hapus" di modal diklik
        $('#confirmDelete').click(function () {
            $.ajax({
                url: '/destroy-bahan/' + deleteId, // pastikan route sesuai
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    $('#modalDelete').modal('hide');
                    $('#tbl_list_tb_penerimaan').DataTable().ajax.reload(); // ganti ID sesuai tabel datamu
                    Swal.fire({
                    icon: "success",
                    title: "Data berhasil dihapus.",
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 2000
                });
                },
                error: function () {
                    $('#modalDelete').modal('hide');
                    alert('Terjadi kesalahan saat menghapus data.');
                }
            });
        });
    </script>

</body>
</html>
