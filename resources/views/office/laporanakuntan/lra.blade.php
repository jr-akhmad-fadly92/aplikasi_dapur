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
                                            Tambah
                                        </button>
                                        <a href="{{ route('laporan_harian_dapur.excel') }}" class="btn btn-primary btn-sm">Download Laporan Periode</a>
                                    </h3>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="tb_list_box" class="table table-bordered table-striped table-hover nowrap" style="width: 100%; table-layout: fixed;">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th style="width: 50px; text-align: center; vertical-align: middle;">No</th>
                                                    <th style="width: 150px; text-align: center; vertical-align: middle;">Periode</th>
                                                    <th style="width: 130px; text-align: center; vertical-align: middle;">Dana BGN</th>
                                                    <th style="width: 130px; text-align: center; vertical-align: middle;">Dana Yayasan</th>
                                                    <th style="width: 130px; text-align: center; vertical-align: middle;">Dana Pihak Lain</th>
                                                    <th style="width: 140px; text-align: center; vertical-align: middle;">Total Pemasukan</th>
                                                    <th style="width: 140px; text-align: center; vertical-align: middle;">Biaya Bahan Baku</th>
                                                    <th style="width: 140px; text-align: center; vertical-align: middle;">Biaya Operasional</th>
                                                    <th style="width: 180px; text-align: center; vertical-align: middle;">Biaya Infrastruktur</th>
                                                    <th style="width: 140px; text-align: center; vertical-align: middle;">Total Pengeluaran</th>
                                                    <th style="width: 130px; text-align: center; vertical-align: middle;">Saldo</th>
                                                    <th style="width: 160px; text-align: center; vertical-align: middle;">Aksi</th>
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
                    <h5 class="modal-title">Tambah Periode</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="periode_awal">Periode Awal</label>
                        <input type="date" name="periode_awal" id="periode_awal" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="periode_akhir">Periode Akhir</label>
                        <input type="date" name="periode_akhir" id="periode_akhir" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="periode">Periode (Keterangan)</label>
                        <input type="text" name="periode" id="periode" class="form-control" placeholder="Contoh: Januari 2025">
                    </div>
                    
                    <div class="form-group">
                        <label for="jumlah_hari">Jumlah Hari</label>
                        <input type="number" name="jumlah_hari" id="jumlah_hari" class="form-control" value="0" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="bgn">Dana BGN</label>
                        <input type="text" name="bgn" id="bgn" class="form-control" value=0 required>
                    </div>

                    <div class="form-group">
                        <label for="yayasan">Dana yayasan</label>
                        <input type="text" name="yayasan" id="yayasan" class="form-control" value=0 required>
                    </div>
                    <div class="form-group">
                        <label for="pihak_lain">Dana Pihak Lain</label>
                        <input type="text" name="pihak_lain" id="pihak_lain" class="form-control" value=0 required>
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
                                <label for="periode_awal">Periode Awal</label>
                                <input type="date" name="edit_periode_awal" id="edit_periode_awal" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="periode_akhir">Periode Akhir</label>
                                <input type="date" name="edit_periode_akhir" id="edit_periode_akhir" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_periode">Periode (Keterangan)</label>
                                <input type="text" name="edit_periode" id="edit_periode" class="form-control" placeholder="Contoh: Januari 2025">
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_jumlah_hari">Jumlah Hari</label>
                                <input type="number" name="edit_jumlah_hari" id="edit_jumlah_hari" class="form-control" value="0" min="0">
                            </div>
                            
                            <div class="form-group">
                                <label for="bgn">Dana BGN</label>
                                <input type="text" name="edit_bgn" id="edit_bgn" class="form-control" value=0 required>
                            </div>

                            <div class="form-group">
                                <label for="yayasan">Dana yayasan</label>
                                <input type="text" name="edit_yayasan" id="edit_yayasan" class="form-control" value=0 required>
                            </div>
                            <div class="form-group">
                                <label for="pihak_lain">Dana Pihak Lain</label>
                                <input type="text" name="edit_pihak_lain" id="edit_pihak_lain" class="form-control" value=0 required>
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
                responsive: true,
                scrollX: true,
                autoWidth: false,
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    zeroRecords: "Data tidak ditemukan",
                    emptyTable: "Tidak ada data tersedia",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                ajax: '{{ route('laporan.keuangan.dt_laporan') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '50px', className: 'text-center' },
                    { data: 'periode', name: 'periode', width: '150px', className: 'text-center' },
                    { data: 'dana_bgn', name: 'dana_bgn', width: '130px', className: 'text-right' },
                    { data: 'dana_yayasan', name: 'dana_yayasan', width: '130px', className: 'text-right' },
                    { data: 'dana_pihak_lain', name: 'dana_pihak_lain', width: '130px', className: 'text-right' },
                    { data: 'total_pemasukan', name: 'total_pemasukan', width: '140px', className: 'text-right font-weight-bold' },
                    { data: 'biaya_bahan_baku', name: 'biaya_bahan_baku', width: '140px', className: 'text-right' },
                    { data: 'biaya_non_pangan', name: 'biaya_non_pangan', width: '140px', className: 'text-right' },
                    { data: 'biaya_infrastuktur_dan_peralatan', name: 'biaya_infrastuktur_dan_peralatan', width: '180px', className: 'text-right' },
                    { data: 'total_pengeluaran', name: 'total_pengeluaran', width: '140px', className: 'text-right font-weight-bold' },
                    { data: 'saldo', name: 'saldo', width: '130px', className: 'text-right font-weight-bold text-primary' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, width: '160px', className: 'text-center' }
                ],
                order: [[1, 'desc']]
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
        // Fungsi untuk format angka ke ribuan (1.000.000)
        function formatRibuan(angka) {
            return angka.replace(/\D/g, "") // hapus semua non-digit
                        .replace(/\B(?=(\d{3})+(?!\d))/g, "."); // tambahkan titik tiap 3 digit
        }

        // Fungsi untuk hapus titik saat dikirim ke backend
        function unformatRibuan(angka) {
            return angka.replace(/\./g, ""); 
        }

        // Daftar input yang mau diformat
        const inputFields = ["bgn", "yayasan", "pihak_lain","edit_bgn", "edit_yayasan", "edit_pihak_lain"];

        // Event input: langsung ubah ke format ribuan
        inputFields.forEach(id => {
            const input = document.getElementById(id);
            input.addEventListener("input", function(e) {
                this.value = formatRibuan(this.value);
            });
        });
        document.getElementById('formBox').addEventListener('submit', function(e) {
            e.preventDefault();
        
            const formData = {
                periode_awal: document.getElementById('periode_awal').value,
                periode_akhir: document.getElementById('periode_akhir').value,
                periode: document.getElementById('periode').value,
                jumlah_hari: document.getElementById('jumlah_hari').value,
                bgn: unformatRibuan(document.getElementById('bgn').value),
                yayasan: unformatRibuan(document.getElementById('yayasan').value),
                pihak_lain: unformatRibuan(document.getElementById('pihak_lain').value),
                _token: '{{ csrf_token() }}'
            };
        
            fetch("{{ route('laporan-realisasi-anggaran.store') }}", {
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
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Fungsi hapus format (untuk submit)
        function unformatNumber(num) {
            return num.replace(/\./g, "");
        }
        // Menampilkan modal dan isi data saat klik tombol edit
        $(document).on('click', '.btn-edit-box', function () {
            const id = $(this).data('id');
            const bgn = $(this).data('bgn');
            const yayasan = $(this).data('yayasan');
            const pihak_lain = $(this).data('pihak');
            const periode_awal = $(this).data('periodeawal');
            const periode_akhir = $(this).data('periodeakhir');
            const periode = $(this).data('periode');
            const jumlah_hari = $(this).data('jumlahhari');
            
            $('#edit_id').val(id);
            $('#edit_bgn').val(formatNumber(bgn));
            $('#edit_yayasan').val(formatNumber(yayasan));
            $('#edit_pihak_lain').val(formatNumber(pihak_lain));
            $('#edit_periode_awal').val(periode_awal);
            $('#edit_periode_akhir').val(periode_akhir);
            $('#edit_periode').val(periode);
            $('#edit_jumlah_hari').val(jumlah_hari);
            
            $('#modalEditBox').modal('show');
        });
    
        // Submit form edit
        document.getElementById('formEditBox').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('edit_id').value;
            const  periode_awal = document.getElementById('edit_periode_awal').value;
            const  periode_akhir = document.getElementById('edit_periode_akhir').value;
            const periode = document.getElementById('edit_periode').value;
            const jumlah_hari = document.getElementById('edit_jumlah_hari').value;
            const bgn           = unformatNumber(document.getElementById('edit_bgn').value);
            const yayasan       = unformatNumber(document.getElementById('edit_yayasan').value);
            const pihak_lain    = unformatNumber(document.getElementById('edit_pihak_lain').value);

            fetch(`/laporan-realisasi-anggaran/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    periode_awal: periode_awal,
                    periode_akhir :periode_akhir,
                    periode: periode,
                    jumlah_hari: jumlah_hari,
                    bgn :bgn,
                    yayasan :yayasan,
                    pihak_lain :pihak_lain,
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
