<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                            <h1 class="m-0 text-dark" id="currentTime">{{ $header }}</h1>
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
                                    <h1>{{ $header }}</h1>
                                    <h3 class="card-title d-inline-block me-3">
                                        @if (auth()->check() && in_array(auth()->user()->level, ["backoffice", "admin","ahli_akuntan"]))
                                        <a href="{{ route('pilih_menu_po') }}" class="btn btn-primary btn-sm">Buat PO Harian</a>
                                        <a href="{{ route('buat_po_manual') }}" class="btn btn-primary btn-sm">Buat PO Manual</a>
                                        @endif
                                    </h3>

                                    <div class="row mt-3 w-100">
                                        <div class="col-md-3">
                                            <div class="form-group mb-2">
                                                <label for="search_nomor_po">Cari Nomor PO</label>
                                                <input type="text" id="search_nomor_po" class="form-control form-control-sm" placeholder="Nomor PO...">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group mb-2">
                                                <label for="tanggal_awal">Tanggal Awal</label>
                                                <input type="date" id="tanggal_awal" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group mb-2">
                                                <label for="tanggal_akhir">Tanggal Akhir</label>
                                                <input type="date" id="tanggal_akhir" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group mb-2">
                                                <label>&nbsp;</label>
                                                <div class="d-flex">
                                                    <button type="button" id="btn-search" class="btn btn-info btn-sm mr-2">Cari</button>
                                                    <button type="button" id="btn-reset" class="btn btn-secondary btn-sm">Reset</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                 
                                <!-- Card Body -->
                                <div class="card-body">
                                    
                                    
                                    <!-- Tabel PO -->
                                    <table id="tbl_list_po" class="table table-bordered table-hover" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nomor PO</th>
                                                <th>Menu</th>
                                                <th>Tanggal Pengajuan</th>
                                                <th>Tanggal Approve</th>
                                                <th>Supplier</th>
                                                <th>Status PO</th>
                                                <th>Action</th>
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
        <!-- Modal Konfirmasi -->
        <div class="modal fade" id="modalKonfirmasi" tabindex="-1" role="dialog" aria-labelledby="modalKonfirmasiLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Update Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                Apakah Anda yakin ingin mengubah status menjadi <strong>ACC</strong>?
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <a href="#" class="btn btn-success" id="btnKonfirmasiAcc">Ya, ACC</a>
                </div>
            </div>
            </div>
        </div>
        <!-- Modal Konfirmasi -->
        <div class="modal fade" id="modalKonfirmasiclose" tabindex="-1" role="dialog" aria-labelledby="modalKonfirmasicloseLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Update Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                Apakah Anda yakin ingin mengubah status menjadi <strong>Close</strong>?
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <a href="#" class="btn btn-success" id="btnKonfirmasiClose">Ya, Close</a>
                </div>
            </div>
            </div>
        </div>
        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>
        
        <!-- Footer -->
        @include('Template.footer')
    </div>
    
    <!-- REQUIRED SCRIPTS -->
    @include('Template.script')
    
    <!-- DataTable Initialization -->
    <script type="text/javascript">
        $(document).ready(function () {
            const table = $('#tbl_list_po').DataTable({
                ajax: {
                    url: '{{ url()->current() }}',
                    data: function (d) {
                        d.search_nomor_po = $('#search_nomor_po').val();
                        d.tanggal_awal = $('#tanggal_awal').val();
                        d.tanggal_akhir = $('#tanggal_akhir').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nomor_po', name: 'nomor_po' },
                    { data: 'keterangan_menu', name: 'keterangan_menu' },
                    { data: 'tanggal_po_dibuat', name: 'tanggal_po_dibuat' },
                    { data: 'tanggal_acc', name: 'tanggal_acc' },
                    { data: 'nama_supplier', name: 'nama_supplier' },
                    { data: 'status_po', name: 'status_po' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

            // Search button
            $('#btn-search').on('click', function () {
                table.ajax.reload();
            });

            // Reset button clears inputs and reloads
            $('#btn-reset').on('click', function () {
                $('#search_nomor_po').val('');
                $('#tanggal_awal').val('');
                $('#tanggal_akhir').val('');
                table.ajax.reload();
            });

            // Enter key triggers search
            $('#search_nomor_po, #tanggal_awal, #tanggal_akhir').on('keypress', function (e) {
                if (e.which === 13) {
                    e.preventDefault();
                    table.ajax.reload();
                }
            });
        });
    </script>
    
    <!-- SweetAlert Notification -->
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
    
    <!-- Update PO Event -->
    
    <script>
        let selectedId = null;

        $(document).on('click', '.update-po', function () {
            selectedId = $(this).data('id');
            $('#modalKonfirmasi').modal('show');
        });

        $('#btnKonfirmasiAcc').on('click', function () {
            if (selectedId) {
                $.ajax({
                    url: '{{ route("update-manual-po") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: selectedId
                    },
                    success: function (response) {
                        $('#modalKonfirmasi').modal('hide');
                        Swal.fire('Berhasil', response.message, 'success');
                        // Reload DataTables kalau perlu:
                        $('#tbl_list_po').DataTable().ajax.reload(null, false); // false = tetap di halaman yang sama
                    },
                    error: function (xhr) {
                        Swal.fire('Error', 'Terjadi kesalahan saat mengupdate!', 'error');
                    }
                });
            }
        });

    </script>

    <script>
        //let selectedId = null;

        $(document).on('click', '.update-close', function () {
            selectedId = $(this).data('id');
            $('#modalKonfirmasiclose').modal('show');
        });

        $('#btnKonfirmasiClose').on('click', function () {
            if (selectedId) {
                $.ajax({
                    url: '{{ route("update-manual-close") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: selectedId
                    },
                    success: function (response) {
                        $('#modalKonfirmasiclose').modal('hide');
                        Swal.fire('Berhasil', response.message, 'success');
                        // Reload DataTables kalau perlu:
                        $('#tbl_list_po').DataTable().ajax.reload(null, false); // false = tetap di halaman yang sama
                    },
                    error: function (xhr) {
                        Swal.fire('Error', 'Terjadi kesalahan saat mengupdate!', 'error');
                    }
                });
            }
        });

    </script>
</body>
</html>
