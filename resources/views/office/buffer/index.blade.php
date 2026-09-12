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
                                        
                                    </h3>
                                </div>

                                <div class="card-body">
                                    <table id="tb_list_buffer" class="table table-bordered table-hover" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Buffer pada Menu</th>
                                                <th>Buffer pada PO</th>
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
                    <h5 class="modal-title">Edit Buffer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="buffer_menu">buffer menu</label>
                        <input type="number" name="buffer_menu" id="buffer_menu" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="buffer_po">buffer PO</label>
                        <input type="number" name="buffer_po" id="buffer_po" class="form-control" required>
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
                                <label for="edit_buffer_menu">buffer menu</label>
                                <input type="number" name="edit_buffer_menu" id="edit_buffer_menu" class="form-control" required required min="0" max="2">
                            </div>
                            <div class="form-group">
                                <label for="edit_buffer_po">buffer PO</label>
                                <input type="number" name="edit_buffer_po" id="edit_buffer_po" class="form-control" required required min="0" max="2">  
                            </div>
                            
                        </div>
                    <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
                </form>
            </div>
        </div>


        <!-- Footer -->
        @include('Template.footer')

    </div>

    @include('Template.script')

    <script type="text/javascript">
        $(document).ready(function () {
            $('#tb_list_buffer').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('buffer.index') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'menu', name: 'menu' },
                    { data: 'po', name: 'po' },
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
                bahan_id: document.getElementById('buffer_menu').value,
                isi_box: document.getElementById('buffer_po').value,
                _token: '{{ csrf_token() }}'
            };
        
            fetch("{{ route('buffer.store') }}", {
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
                        $('#tb_list_buffer').DataTable().ajax.reload(null, false); // reload datatable tanpa reset halaman
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
        // Menampilkan modal dan isi data saat klik tombol edit
        $(document).on('click', '.btn-edit-buffer', function () {
            const id = $(this).data('id');
            const menu = $(this).data('menu');
            const po = $(this).data('po');
    
            $('#edit_id').val(id);
            $('#edit_buffer_menu').val(menu);
            $('#edit_buffer_po').val(po);
            $('#modalEditBox').modal('show');
        });
    
        // Submit form edit
        document.getElementById('formEditBox').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('edit_id').value;
            const menu = document.getElementById('edit_buffer_menu').value;
            const po = document.getElementById('edit_buffer_po').value;
    
            fetch(`/buffer/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ menu: menu, po: po })
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
                        $('#tb_list_buffer').DataTable().ajax.reload(null, false);
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
    
</body>
</html>
