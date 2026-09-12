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
                                        <!-- Tombol untuk buka modal -->
                                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalBox">
                                            Tambah Box
                                        </button>
                                    </h3>
                                </div>

                                <div class="card-body">
                                    <table id="tb_list_box" class="table table-bordered table-hover" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Bahan</th>
                                                <th>Isi Box</th>
                                                <th>Hasil Matang</th>
                                                <th>Penyusutan</th>
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
        
        <!-- Modal Input -->
        <div class="modal fade" id="modalBox" tabindex="-1" role="dialog" aria-labelledby="modalBoxLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formBox">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Box Bahan Baku</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="bahan_id">Bahan</label>
                        <select name="bahan_id" id="bahan_id" class="form-control select2" required style="width: 100%">
                            <option value="">-- Pilih Bahan --</option>
                            @foreach($bahanlist as $bahan)
                                <option value="{{ $bahan->id }}">{{ $bahan->bahan }} ({{ $bahan->satuan }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="isi_box">Isi Box</label>
                        <input type="number" name="isi_box" id="isi_box" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="penyusutan">Penyusutan ( % )</label>
                        <input type="number" name="penyusutan" id="penyusutan" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
                </form>
            </div>
            </div>
        </div>

        <!--modal update-->

        <div class="modal fade" id="modalEditBox" tabindex="-1">
            <div class="modal-dialog">
                <form id="formEditBox">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Edit Box</h5>
                </div>
                        <div class="modal-body">
                            <input type="hidden" id="edit_id">

                            <div class="form-group">
                                <label for="edit_isi_box">Isi Per Box</label>
                                <input type="number" id="edit_isi_box" name="edit_isi_box" class="form-control" required>
                            </div>
                           
                            <div class="form-group">
                                <label for="edit_penyusutan_persen">Penyusutan ( % )</label>
                                <input type="number" id="edit_penyusutan_persen" name="edit_penyusutan_persen" class="form-control" required>
                            </div>
                        </div>
                    <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
                </form>
            </div>
        </div>

        <!-- Modal Konfirmasi Delete -->
        <div class="modal fade" id="modalDeleteBox" tabindex="-1" aria-labelledby="deleteBoxLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteBoxLabel">Konfirmasi Hapus</h5>
          
                </div>
                <div class="modal-body">
                Apakah Anda yakin ingin menghapus data ini?
                </div>
                <div class="modal-footer">
                <button type="button" id="btn-confirm-delete" class="btn btn-danger">Ya, Hapus</button>
                </div>
            </div>
            </div>
        </div>

        <!-- Footer -->
        @include('Template.footer')

    </div>

    @include('Template.script')

    <script type="text/javascript">
        $(document).ready(function () {
            $('#tb_list_box').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('box-bahan-baku.index') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'bahan', name: 'bahan' },
                    { data: 'isi_box', name: 'isi_box' },
                    { data: 'isi_hasil_matang', name: 'isi_hasil_matang' },
                    { data: 'penyusutan_persen', name: 'penyusutan_persen' },
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
        document.getElementById('formBox').addEventListener('submit', function(e) {
            e.preventDefault();
        
            const formData = {
                bahan_id: document.getElementById('bahan_id').value,
                isi_box: document.getElementById('isi_box').value,
                penyusutan : document.getElementById('penyusutan').value,
                _token: '{{ csrf_token() }}'
            };
        
            fetch("{{ route('box-bahan-baku.store') }}", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': formData._token
                },
                body: JSON.stringify(formData)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                    }).then(() => {
                        $('#tb_list_box').DataTable().ajax.reload(null, false); // reload datatable tanpa reset halaman
                        $('#modalBox').modal('hide'); // tutup modal jika perlu
                        document.getElementById('formBox').reset(); // reset form
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Terjadi kesalahan.'
                    });
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#bahan_id').select2({
                placeholder: "Pilih Bahan",
                allowClear: true
            });
           

        });
    </script>
    <script>
        // Menampilkan modal dan isi data saat klik tombol edit
        $(document).on('click', '.btn-edit-box', function () {
            const id = $(this).data('id');
            const isiBox = $(this).data('isi');
            const penyusutanpersen = $(this).data('penyusutan');
    
            $('#edit_id').val(id);
            $('#edit_isi_box').val(isiBox);
            $('#edit_penyusutan_persen').val(penyusutanpersen);
            $('#modalEditBox').modal('show');
        });
    
        // Submit form edit
        document.getElementById('formEditBox').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('edit_id').value;
            const isi_box = document.getElementById('edit_isi_box').value;
            const edit_penyusutan = document.getElementById('edit_penyusutan_persen').value;
    
            fetch(`/box-bahan-baku/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ isi_box: isi_box,penyusutan :edit_penyusutan })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#tb_list_box').DataTable().ajax.reload(null, false);
                        $('#modalEditBox').modal('hide');
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message
                    });
                }
            });
        });
    </script>
    <script>
        let boxIdToDelete = null;
    
        // Saat tombol hapus ditekan, simpan id dan tampilkan modal
        $(document).on('click', '.btn-delete-box', function () {
            boxIdToDelete = $(this).data('id');
            $('#modalDeleteBox').modal('show');
        });
    
        // Ketika tombol konfirmasi hapus ditekan
        $('#btn-confirm-delete').on('click', function () {
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
    
            fetch(`/box-bahan-baku/${boxIdToDelete}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                $('#modalDeleteBox').modal('hide');
    
                if (data.success) {
                    // Refresh DataTables tanpa reload halaman
                    $('#tb_list_box').DataTable().ajax.reload(null, false);
    
                    // Optional: notifikasi
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.fire('Terjadi kesalahan', 'Gagal menghapus data.', 'error');
                $('#modalDeleteBox').modal('hide');
            });
        });
    </script>
    
        
        
    
</body>
</html>
