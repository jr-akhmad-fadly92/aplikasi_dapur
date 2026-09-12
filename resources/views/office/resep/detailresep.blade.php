<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        @include('Template.navbar')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('Template.left-sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark" id="currentTime">Starter Page</h1>
                        </div><!-- /.col -->
                       
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                    <div class="col-12">
                        <div class="card">
                        <div class="card-header">
                            <h1 >{{ $header }}</h1>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <h3 class="card-title"><a href="{{ route('detailresep.tambah_detail_resep',$menu_id) }}" class="edit btn btn-primary btn-sm " id="btn-edit-post">Tambah</a>
                            <a href="{{ route('resep.realisasi-akg.index', $menu_id) }}" class="edit btn btn-warning btn-sm " id="btn-edit-post">AKG Resep</a>
                            <a href="{{ route('resep.index') }}" class="edit btn btn-info btn-sm " id="btn-edit-post">Kembali</a></h3>
                            
                           <table id="tbl_list_bahan_masak" class="table table-bordered table-hover" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Bahan</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Status Bahan</th>
                                    <th>Perhitungan</th>
                                    <th>Gramasi</th>
                                    
                                    <th>action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                        
                    </div>
                    <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                    <!--modal update-->

                    <div class="modal fade" id="modalEditBox" tabindex="-1">
                        <div class="modal-dialog">
                            <form id="formEditBox">
                            <div class="modal-content">
                                <div class="modal-header">
                                <h5 class="modal-title">Update Gramasi</h5>
                            </div>
                                    <div class="modal-body">
                                        <input type="hidden" id="id_resep" >
                                        <input type="hidden" id="status_bahan_baku">

                                        <div class="form-group">
                                            <label for="gramasi_a">Gramasi A</label>
                                            <input type="number" id="gramasi_a" name="gramasi_a" class="form-control" step="0.01" required>
                                        </div>
                                    
                                        <div class="form-group">
                                            <label for="gramasi_b">Gramasi B</label>
                                            <input type="number" id="gramasi_b" name="gramasi_b" class="form-control" step="0.01" required>
                                        </div>
                                    </div>
                                <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Simpan Gramasi</button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                <!-- /.container-fluid -->
                </section>
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        @include('Template.footer')
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
  
    @include('Template.script')
   
    <script type="text/javascript">
    $(document).ready(function () {
    $('#tbl_list_bahan_masak').DataTable({
            
            ajax: '{{ url()->current() }}',
            columns: [
                 { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
               
                { data: 'nama_bahan', name: 'nama_bahan' },
                { data: 'jumlah', name: 'jumlah' },
                { data: 'nama_satuan_bahan', name: 'nama_satuan_bahan' },
                { data: 'status_bahan', name: 'status_bahan' },
                { data: 'perhitungan_bahan', name: 'perhitungan_bahan' },
                { data: 'gramasi', name: 'gramasi' },
                                
                                
                
                {data: 'action', name: 'action', orderable: false, searchable: false}, // Aksi (tombol)


            ]
        });
    });
    </script>
     <script src="{{ asset('AdminLte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
     <script>
        //message with sweetalert
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
        // Menampilkan modal dan isi data saat klik tombol edit
        $(document).on('click', '.btn-gramasi', function () {
            const idresep = $(this).data('idresep');
            const gramasia = $(this).data('gramasia');
            const gramasib = $(this).data('gramasib');
            const statusbahanbaku = $(this).data('statusbahanbaku');
    
            $('#id_resep').val(idresep);
            $('#status_bahan_baku').val(statusbahanbaku);
            $('#gramasi_a').val(gramasia);
            $('#gramasi_b').val(gramasib);
            $('#modalEditBox').modal('show');
        });
    
        // Submit form edit
        document.getElementById('formEditBox').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('id_resep').value;
            const status_bahan_baku = document.getElementById('status_bahan_baku').value;
            const gramasi_a = document.getElementById('gramasi_a').value;
            const gramasi_b = document.getElementById('gramasi_b').value;
    
            fetch(`/gramasi-resep/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    status_bahan_baku: status_bahan_baku,
                    gramasi_a :gramasi_a,
                    gramasi_b :gramasi_b
                 })
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
                        $('#tbl_list_bahan_masak').DataTable().ajax.reload(null, false);
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
    <!-- jQuery -->
</body>
</html>
