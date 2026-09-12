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
                            <form action="" method="GET" id="formCetak" class="form-horizontal">
                                <div class="form-group row">
                                    <label for="bulan" class="col-sm-2 col-form-label">Pilih Bulan</label>
                                    <div class="col-sm-4">
                                        <select class="form-control" name="bulan" id="bulan" required>
                                            <option value="">-- Pilih Bulan --</option>
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}">{{ \Carbon\Carbon::create()->month($i)->format('F') }}</option>
                                            @endfor
                                        </select>
                                        
                                    </div>
                                </div>
                
                                <div class="form-group row">
                                    <label for="tahun" class="col-sm-2 col-form-label">Pilih Tahun</label>
                                    <div class="col-sm-4">
                                        <select class="form-control" name="tahun" id="tahun" required>
                                            <option value="">-- Pilih Tahun --</option>
                                            @for ($y = date('Y'); $y >= 2020; $y--)
                                                <option value="{{ $y }}">{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                
                                <div class="form-group row">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-4">
                                        <button type="submit" class="btn btn-primary btn-block">Cetak PDF</button>
                                    </div>
                                </div>
                            </form>
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <!-- Tombol untuk buka modal -->
                                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalBox">
                                            Tambah
                                        </button>
                                    </h3>
                                    
                                </div>
                                
                                <div class="card-body">
                                    <table id="tb_list_box" class="table table-bordered table-hover" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Nomor Transaksi</th>
                                                <th>Barang</th>
                                                <th>Keluar</th>
                                                <th>Nama Yang Mengajukan</th>
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
                <form id="form_peneluaran">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Masukan Pengeluaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="bahan_id">Pilih Pengeluaran</label>
                        <select name="master_bahan_id" id="master_bahan_id" class="form-control select2" required style="width: 100%">
                            @foreach($bahan as $data)

                            <option value="{{ $data->id }}">-- {{ $data->bahan }} --</option>
                            
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">

                        <label for="isi_box">Tanggal Pengeluaran</label>
                        <input type="datetime-local" name="tanggal" id="tanggal" class="form-control" 
                            value="{{ date('Y-m-d') }}T08:00"  required>

                    </div>

                    <div class="form-group">
                        
                        <label for="isi_box">Nomor Transaksi ( jika ada )</label>
                        <input type="text" name="nomor_transaksi" id="nomor_transaksi" class="form-control" >

                    </div>

                    <div class="form-group">
                        
                        <label for="isi_box">Biaya</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" required>

                    </div>

                    <div class="form-group">
                        
                        <label for="isi_box">Keterangan</label>
                        <input type="text" name="deskripsi" id="deskripsi" class="form-control" value="-" required>

                    </div><div class="form-group" hidden>
                        
                        <label for="isi_box">Nama Karyawan</label>
                        <input type="text" name="nama_karyawan" id="nama_karyawan" class="form-control" >

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
                                <label for="bahan_id">Pilih Pengeluaran</label>
                                <select name="edit_master_bahan_id" id="edit_master_bahan_id" class="form-control select2"  style="width: 100%">
                                    @foreach($bahan as $data)
        
                                    <option value="{{ $data->id }}" >-- {{ $data->bahan }} --</option>
                                    
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
        
                                <label for="isi_box">Tanggal Pengeluaran</label>
                                <input type="datetime-local" name="edit_tanggal" id="edit_tanggal" class="form-control" required>
        
                            </div>
        
                            <div class="form-group">
                                
                                <label for="isi_box">Nomor Transaksi ( jika ada )</label>
                                <input type="text" name="edit_nomor" id="edit_nomor" class="form-control" >
        
                            </div>
        
                            <div class="form-group">
                                
                                <label for="isi_box">Biaya</label>
                                <input type="number" name="edit_jumlah" id="edit_jumlah" class="form-control" required>
        
                            </div>
        
                            <div class="form-group">
                                
                                <label for="isi_box">Keterangan</label>
                                <input type="text" name="edit_deskripsi" id="edit_deskripsi" class="form-control" value="-" required>
        
                            </div><div class="form-group" hidden>
                                
                                <label for="isi_box">Nama Karyawan</label>
                                <input type="text" name="edit_nama" id="edit_nama" class="form-control" >
        
                            </div>
                            
                        </div>
                    <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
                </form>
            </div>
        </div>

        <!--modal Delete-->

        <div class="modal fade" id="modalDeleteBox" tabindex="-1">
            <div class="modal-dialog">
                <form id="formDeleteBox">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Delete Box</h5>
                </div>
                        <div class="modal-body">
                            <input type="hidden" id="delete_id">
                            <div class="form-group">
                                <label for="bahan_id">Apakah Anda Yakin ingin Menghapus</label>
                                <select name="delete_master_bahan_id" id="delete_master_bahan_id" class="form-control select2" required style="width: 100%" @readonly(true)>
                                    @foreach($bahan as $data)
        
                                    <option value="{{ $data->id }}" >-- {{ $data->bahan }} --</option>
                                    
                                    @endforeach
                                </select>
                            </div>
                           
        
                            <div class="form-group">
                                
                                <label for="isi_box">Keterangan</label>
                                <input type="text" name="delete_deskripsi" id="delete_deskripsi" class="form-control" value="-" required>
        
                           
                            
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
        

    </div>
    @include('Template.footer')
    @include('Template.script')

    <script type="text/javascript">
        $(document).ready(function () {
            $('#tb_list_box').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('kas-kecil.index') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'tanggal_transaksi', name: 'tanggal_transaksi' },
                    { data: 'nomor_transaksi', name: 'nomor_transaksi' },
                    { data: 'deskripsi', name: 'deskripsi' },
                    { data: 'jumlah_pembayaran', name: 'jumlah_pembayaran' },
                    { data: 'nama_karyawan', name: 'nama_karyawan' },
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
        document.getElementById('form_peneluaran').addEventListener('submit', function(e) {
            e.preventDefault();
        
            const formData = {
                master_bahan_id: document.getElementById('master_bahan_id').value,
                tanggal: document.getElementById('tanggal').value,
                nomor_transaksi: document.getElementById('nomor_transaksi').value,
                jumlah: document.getElementById('jumlah').value,
                deskripsi: document.getElementById('deskripsi').value,
                ///nama_karyawan: document.getElementById('nama_karyawan').value,
                _token: '{{ csrf_token() }}'
            };
        
            fetch("{{ route('kas-kecil.store') }}", {
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
                        document.getElementById('form_peneluaran').reset(); // reset form
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
            $('#edit_master_bahan_id').select2({
                placeholder: "Pilih Bahan",
                allowClear: true
            });
            $('#master_bahan_id').select2({
                placeholder: "Pilih Bahan",
                allowClear: true
            });

        });
    </script>
    <script>
        // Menampilkan modal dan isi data saat klik tombol edit
        $(document).on('click', '.btn-edit-box', function () {
            const id = $(this).data('id');
            const idbahan = $(this).data('idbahan');
            const tanggal = $(this).data('tanggal');
            const jenis = $(this).data('jenis');
            const deskripsi = $(this).data('deskripsi');
            const jumlah = $(this).data('jumlah');
            //const nama = $(this).data('nama');
            const nomor = $(this).data('nomor');
    
            $('#edit_id').val(id);
            $('#edit_master_bahan_id').val(idbahan).trigger('change');

           // $('#edit_tanggal').val(tanggal);
            $('#edit_jenis').val(jenis);
            $('#edit_deskripsi').val(deskripsi);
            $('#edit_jumlah').val(jumlah);
            //$('#edit_nama').val(nama);
            $('#edit_nomor').val(nomor);
            $('#modalEditBox').modal('show');
        });
    
        // Submit form edit
        document.getElementById('formEditBox').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('edit_id').value;
            const master_bahan_id = document.getElementById('edit_master_bahan_id').value;
            const tanggal = document.getElementById('edit_tanggal').value;
            const deskripsi = document.getElementById('edit_deskripsi').value;
            const jumlah = document.getElementById('edit_jumlah').value;
           // const nama_karyawan = document.getElementById('edit_nama').value;
            const nomor_transaksi = document.getElementById('edit_nomor').value;
    
            fetch(`/kas-kecil/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                   // master_bahan_id: master_bahan_id,
                    tanggal: tanggal,
                    deskripsi: deskripsi,
                    jumlah: jumlah,
                  //  nama_karyawan: nama_karyawan,
                    nomor_transaksi: nomor_transaksi })
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
        // Menampilkan modal dan isi data saat klik tombol edit
        $(document).on('click', '.btn-delete-box', function () {
            const id = $(this).data('id');
            
            $('#delete_id').val(id);
            $('#modalDeleteBox').modal('show');
        });

        // Submit form edit
        document.getElementById('formDeleteBox').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('delete_id').value;
            const deskripsi = document.getElementById('delete_deskripsi').value;
            
            fetch(`/kas-kecil-delete/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    
                    deskripsi: deskripsi
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
                        $('#tb_list_box').DataTable().ajax.reload(null, false);
                        $('#modalDeleteBox').modal('hide');
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
        const form = document.getElementById('formCetak');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const bulan = document.getElementById('bulan').value;
            const tahun = document.getElementById('tahun').value;
    
            if (bulan && tahun) {
                // Ini BASE URL route tanpa parameter
                const baseUrl = "{{ route('laporan.pdf', ['bulan' => '__BULAN__', 'tahun' => '__TAHUN__']) }}";
                // Replace placeholder dengan value
                const finalUrl = baseUrl
                    .replace('__BULAN__', bulan)
                    .replace('__TAHUN__', tahun);
    
                window.open(finalUrl, "_blank");
            } else {
                alert("Silakan pilih bulan dan tahun.");
            }
        });
    </script>
    
    
        
    
</body>
</html>
