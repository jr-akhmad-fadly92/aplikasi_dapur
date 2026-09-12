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
                            <h1 class="m-0 text-dark" id="currentTime">Starter Page</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">

                            <!-- Card Info -->
                            <div class="card">
                                <div class="card-header">
                                    <div class="row invoice-info">
                                        <!-- Informasi QR Code dan Tanggal Digunakan -->
                                        <div class="col-sm-6">
                                            <ul class="list-group list-group-unbordered mb-3">
                                                <li class="list-group-item">
                                                    <b>QR Code Wadah</b> <a class="float-right">{{ $wadah->qr_code }}</a>
                                                </li>
                                                <li class="list-group-item">
                                                    <b>Tanggal Digunakan</b> 
                                                    <a class="float-right">
                                                        {{ \Carbon\Carbon::parse($data_wadah->created_at)->translatedFormat('l, d-m-Y') }}
                                                    </a>
                                                </li>
                                            </ul>
                                            <h3 class="card-title">
                                                <a href="{{route('transaksi_wadah.index')}}" class="btn btn-primary">Kembali</a>
                                                @if($count_null_berat > 0 )
                                                <a href="{{route('transaksi_wadah.simpan_gudang',$id)}}" class="btn btn-primary">Simpan</a>
                                                
                                                @endif
                                                
                                            </h3>
                                        </div>
                                        <!-- Informasi Tambahan -->
                                        <div class="col-sm-6">
                                            <ul class="list-group list-group-unbordered mb-3">
                                                <li class="list-group-item">
                                                    <b>Tanggal Digunakan</b> <a class="float-right">{{ \Carbon\Carbon::parse($tanggal_digunakan->tanggal_digunakan)->translatedFormat('l, d-m-Y') }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Table Card -->
                            <div class="card">
                                <div class="card-header">
                                    <h1>{{ $header }}</h1>
                                </div>
                                <div class="card-body">
                                    <table id="tbl_list_transaksi_wadah" class="table table-bordered table-hover" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Bahan</th>
                                                <th>Jumlah Berat Sebelum</th>
                                                <th>Jumlah Berat Sesudah</th>
                                                <th>Selisih Berat</th>
                                                <th>Waktu Sebelum</th>
                                                <th>Waktu Sesudah</th>
                                                <th>Selisih Waktu</th>
                                                <th>Update</th>
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

        <!-- Modal Update -->
        <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Input Penerimaan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="updateForm">
                        <div class="modal-body">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" id="id" name="id">
                            
                            <!-- Input Jumlah Bahan -->
                            <div class="form-group">
                                <label>Jumlah Bahan Yang Sesudah</label>
                                <input type="text" class="form-control" id="jumlah_berat_sesudah" name="jumlah_berat_sesudah">
                            </div>
                            
                            <!-- Pilihan Lokasi Penyimpanan -->
                            <div class="form-group">
                                <label>Lokasi</label>
                                <select class="form-control" id="lokasi" name="lokasi" required>
                                    <option value="0">Gudang Kering</option>
                                    <option value="1">Chiller</option>
                                    <option value="2">Freezer</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
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

    
    <!-- Scripts -->
    @include('Template.script')
    
    <!-- Script untuk inisialisasi DataTables -->
<script type="text/javascript">
    $(document).ready(function () {
        $('#tbl_list_transaksi_wadah').DataTable({
            ajax: '{{ url()->current() }}', // Mengambil data dari URL saat ini
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false }, // Nomor urut otomatis
                { data: 'bahan', name: 'bahan' }, // Nama bahan
                { data: 'jumlah_berat_sebelum', name: 'jumlah_berat_sebelum' }, // Berat sebelum
                { data: 'jumlah_berat_sesudah', name: 'jumlah_berat_sesudah' }, // Berat sesudah
                { data: 'selisih_berat', name: 'selisih_berat' }, // Selisih berat
                { data: 'tanggal_dan_waktu_masuk', name: 'tanggal_dan_waktu_masuk' }, // Waktu masuk
                { data: 'tanggal_dan_waktu_sesudah', name: 'tanggal_dan_waktu_sesudah' }, // Waktu setelahnya
                { data: 'selisih_waktu', name: 'selisih_waktu' }, // Selisih waktu
                { data: 'action', name: 'action', orderable: false, searchable: false } // Tombol aksi
            ]
        });
    });
</script>

<!-- SweetAlert2 untuk menampilkan notifikasi -->
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

    <!-- Script AJAX untuk update data transaksi -->
    <script>
        $(document).ready(function () {
            // Menyertakan token CSRF untuk setiap request AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Event saat tombol edit diklik, membuka modal update
            $(document).on('click', '.openModalBtn', function () {
                var id = $(this).data('id');
                $('#id').val(id);
                $('#updateModal').modal('show');
            });

            // Form submit untuk update transaksi
            $('#updateForm').submit(function (e) {
                e.preventDefault();

                let formData = $(this).serialize();
                console.log(formData); // Debug: Melihat data sebelum dikirim

                $.ajax({
                    url: '/simpan_transaksi_wadah_sesudah', // URL API untuk menyimpan data
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        Swal.fire('Berhasil!', response.message, 'success'); // Notifikasi sukses
                        $('#updateModal').modal('hide'); // Menutup modal setelah update
                        $('#updateModal form')[0].reset(); // Reset form setelah submit
                        $('#tbl_list_transaksi_wadah').DataTable().ajax.reload(); // Reload tabel
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText); // Debug: Menampilkan error di console
                        Swal.fire('Gagal!', 'Terjadi kesalahan: ' + xhr.responseJSON.message, 'error'); // Notifikasi error
                    }
                });
            });
        }); 
    </script>

</body>
</html>
