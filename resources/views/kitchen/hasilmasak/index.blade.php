<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head') <!-- Menyertakan file head -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        @include('Template.navbar') <!-- Navbar -->
        @include('Template.left-sidebar') <!-- Sidebar -->

        <div class="content-wrapper">
            <!-- Header Konten -->
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

            <!-- Konten Utama -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <!-- Menu Hari Ini -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h2>Menu Hari Ini</h2>
                                    <div class="row row-cols-2 row-cols-md-5">
                                        @foreach ([
                                            ['img' => 'nasi.png', 'title' => 'Karbohidrat', 'desc' => $menus->nama_karbohidrat ?? '-', 'kode_a' => $histori_masak_a->hasil_porsi_karbohidrat ?? 0, 'kode_b' => $histori_masak_b->hasil_porsi_karbohidrat ?? 0],
                                            ['img' => 'protein.png', 'title' => 'Protein', 'desc' => $menus->nama_protein ?? '-', 'kode_a' => $histori_masak_a->hasil_porsi_protein ?? 0, 'kode_b' => $histori_masak_b->hasil_porsi_protein ?? 0],
                                            ['img' => 'sayur.png', 'title' => 'Sayur', 'desc' => $menus->nama_sayur ?? '-', 'kode_a' => $histori_masak_a->hasil_porsi_sayur ?? 0, 'kode_b' => $histori_masak_b->hasil_porsi_sayur ?? 0],
                                            ['img' => 'buah.png', 'title' => 'Buah', 'desc' => $menus->nama_buah ?? '-', 'kode_a' => $histori_masak_a->hasil_porsi_buah ?? 0, 'kode_b' => $histori_masak_b->hasil_porsi_buah ?? 0],
                                            ['img' => 'susu.png', 'title' => 'Pelengkap', 'desc' => $menus->nama_susu ?? '-', 'kode_a' => $histori_masak_a->hasil_porsi_susu ?? 0, 'kode_b' => $histori_masak_b->hasil_porsi_susu ?? 0]
                                        ] as $item)
                                            <div class="col">
                                                <div class="card bg-soft-primary h-100 text-center">
                                                    <div class="card-body">
                                                        <img width="100px" src="{{ asset('image/' . $item['img']) }}" alt="">
                                                        <h4>{{ $item['desc'] }}</h4>
                                                        <h6>A : {{ $item['kode_a'] }} Pack</h6>
                                                        <h6>B : {{ $item['kode_b'] }} Pack</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Histori Input -->
                        <div class="col-12 col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Histori Input</h3>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('laporan-masak-harian') }}" method="GET" target="_blank" class="form-inline mb-3">
                                        <label for="tanggal_laporan" class="mr-2 mb-2">Tanggal Laporan:</label>
                                        <input
                                            type="date"
                                            id="tanggal_laporan"
                                            name="tanggal"
                                            class="form-control form-control-sm mr-2 mb-2"
                                            value="{{ request('tanggal', \Carbon\Carbon::now('Asia/Jakarta')->toDateString()) }}"
                                        >
                                        <button type="submit" class="btn btn-sm btn-danger mb-2">
                                            <i class="fas fa-file-pdf"></i> Download Laporan
                                        </button>
                                    </form>
                                    <div class="table-responsive">
                                        <table id="tbl_list_tb_hasil" class="table table-bordered table-hover" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Resep</th>
                                                    <th>Jumlah</th>
                                                    <th>Waktu Jadi</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Input Data Masakan -->
                        <div class="col-12 col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Masukkan Data Masakan</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="menu_id" class="form-label">Paket Menu (Hari Ini)</label>
                                        <select id="menu_id" class="form-control form-control-sm">
                                            @forelse($menuOptions as $opt)
                                                <option value="{{ $opt->id }}" {{ ($selectedMenuId == $opt->id) ? 'selected' : '' }}>{{ $opt->menu }}</option>
                                            @empty
                                                <option value="">Tidak ada menu untuk hari ini</option>
                                            @endforelse
                                        </select>
                                    </div>
                                    <form id="formHasilMasak" action="{{ route('hasil-masak.store') }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label for="waktu_mulai_masak">Jam Mulai Masak Hari Ini</label>
                                            <input type="datetime-local" class="form-control" id="waktu_mulai_masak" name="waktu_mulai_masak" value="{{ $waktu_mulai }}" required>
                                        </div>
                                        <input type="hidden" name="id_menu" id="hidden_id_menu" value="{{ $menus->id_menu ?? 0 }}">
                                        <div class="form-group">
                                            <label for="resep_id">Pilih Resep</label>
                                            <select class="form-control" id="resep_id" name="resep_id">
                                                <option value="">-- Pilih Resep --</option>
                                                <option value="1">{{ $menus->nama_karbohidrat ?? '--'}}</option>
                                                <option value="2">{{ $menus->nama_protein ?? '--'}}</option>
                                                <option value="3">{{ $menus->nama_sayur ?? '--'}}</option>
                                                <option value="4">{{ $menus->nama_buah ?? '--' }}</option>
                                                <option value="5">{{ $menus->nama_susu ?? '--' }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="berat" id="label_berat">Berat (gram/ml/pcs)</label>
                                            <input type="number" class="form-control" id="berat" name="berat" required>
                                        </div>
                                        @php
                                            $tanggalHariIni = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
                                        @endphp
                                        <div class="form-group" hidden>
                                            <label>Jam Mulai Masak</label>
                                            <input type="datetime-local" class="form-control" name="created_at" value="{{ $tanggalHariIni }}" required>
                                        </div>

                                        
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        
        @include('Template.footer') <!-- Footer -->
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Hasil Masak</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="editId">
                        <div class="form-group">
                            <label for="editJumlah" id="editJumlahLabel">Jumlah (gram/ml/pcs)</label>
                            <input type="number" class="form-control" id="editJumlah" name="jumlah" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('Template.script')
    <script type="text/javascript">
        $(document).ready(function () {
            const satuanPerKomponen = @json($satuanPerKomponen);

            function syncLabelBerat() {
                const komponen = String($('#resep_id').val() || '');
                const satuan = satuanPerKomponen[komponen] || 'gram/ml/pcs';
                $('#label_berat').text('Berat ( ' + satuan + ' )');
            }

            const table = $('#tbl_list_tb_hasil').DataTable({
                responsive: true,
                ajax: {
                    url: '{{ url()->current() }}',
                    data: function(d) {
                        d.menu_id = $('#menu_id').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nama_resep_input', name: 'nama_resep_input' },
                    { data: 'jumlah_jadi', name: 'jumlah_jadi' },
                    { data: 'waktu_jadi', name: 'waktu_jadi' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $('#resep_id').on('change', syncLabelBerat);
            syncLabelBerat();

            // On change paket menu, reload page to refresh all data (menu cards, recipe dropdown, histori)
            $('#menu_id').on('change', function() {
                const id = $(this).val();
                window.location.href = '{{ url()->current() }}' + '?menu_id=' + id;
            });

            // Handle Edit Button
            $(document).on('click', '.edit-btn', function() {
                const id = $(this).data('id');
                $.ajax({
                    url: '/hasil-masak/' + id + '/get-edit',
                    type: 'GET',
                    success: function(data) {
                        $('#editId').val(data.id);
                        $('#editJumlah').val(data.jumlah);
                        const komponen = String(data.id_komponen_sehat || '');
                        const satuan = satuanPerKomponen[komponen] || 'gram/ml/pcs';
                        $('#editJumlahLabel').text('Jumlah ( ' + satuan + ' )');
                        $('#editModal').modal('show');
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal mengambil data'
                        });
                    }
                });
            });

            // Handle Edit Form Submit
            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                const id = $('#editId').val();
                const formData = {
                    jumlah: $('#editJumlah').val(),
                    _method: 'PUT',
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.ajax({
                    url: '/hasil-masak/' + id,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#editModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            });
                            $('#tbl_list_tb_hasil').DataTable().ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menyimpan data'
                        });
                    }
                });
            });

            // Handle Submit Form Input Data Masakan (tanpa reload page)
            $('#formHasilMasak').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            });
                            $('#tbl_list_tb_hasil').DataTable().ajax.reload(null, false);
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menyimpan data'
                        });
                    }
                });
            });

            // delete-link dibiarkan direct <a href> tanpa konfirmasi
            $(document).on('click', '.delete-link', function(e) {
                e.preventDefault();
                const deleteUrl = $(this).attr('href');

                $.ajax({
                    url: deleteUrl,
                    type: 'GET',
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: (response && response.message) ? response.message : 'Data berhasil dihapus',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        $('#tbl_list_tb_hasil').DataTable().ajax.reload(null, false);
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menghapus data'
                        });
                    }
                });
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
    $(document).ready(function() {
        
        $("#masakanForm").submit(function(e) {
            
            e.preventDefault(); // Mencegah reload halaman
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('hasil-masak.store') }}", 
                method: "POST",
                data: $(this).serialize(), 
                success: function(response) {
                    if (response.success) {
                        $("#responseMessage").html('<div class="alert alert-success">' + response.message + '</div>');
                        $("#masakanForm")[0].reset();
                    } else {
                        $("#responseMessage").html('<div class="alert alert-danger">Terjadi kesalahan</div>');
                    }
                },
                error: function(xhr) {
                    $("#responseMessage").html('<div class="alert alert-danger">Terjadi kesalahan, silakan coba lagi.</div>');
                }
            });
        });
    });
    </script>
</body>
</html>
