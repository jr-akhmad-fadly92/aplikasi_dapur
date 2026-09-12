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
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Transaksi Wadah</h3>
                                </div>
                                <div class="card-body">
                                    <table id="tbl_list_tb_transaksi_wadah" class="table table-bordered table-hover" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Kode Wadah</th>
                                                <th>Tempat</th>
                                                <th>Rincian Isi</th>
                                                <th>Detail</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Scan Wadah</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>QR Code Wadah</label>
                                        <input type="text" class="form-control" id="qr_code" name="qr_code" autofocus>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        
        @include('Template.footer')
    </div>

    @include('Template.script')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#tbl_list_tb_transaksi_wadah').DataTable({
                ajax: '{{ url()->current() }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'qr_code', name: 'qr_code' },
                    { data: 'posisi_wadah', name: 'posisi_wadah' },
                    { data: 'isi_wadah', name: 'isi_wadah' },
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
        document.addEventListener('DOMContentLoaded', function () {
            let inputQr = document.getElementById('qr_code');

            // Fokus hanya saat halaman pertama kali dibuka
            inputQr.focus();

            // Event listener untuk menangkap scan barcode
            inputQr.addEventListener('keypress', function (event) {
                if (event.key === 'Enter') { // Jika barcode scanner mengirimkan Enter
                    let qrCode = this.value.trim();
                    if (qrCode) {
                        window.location.href = `/detail_wadah/${qrCode}`;
                    }
                }
            });

            // Event input untuk scanner yang tidak mengirim Enter
            inputQr.addEventListener('input', function () {
                let qrCode = this.value.trim();
                if (qrCode.length > 2) { // Pastikan barcode memiliki panjang minimal
                    setTimeout(() => { 
                        if (inputQr.value === qrCode) { // Cek jika tidak ada tambahan input
                            window.location.href = `/detail_wadah/${qrCode}`;
                        }
                    }, 500); // Tunggu sejenak sebelum redirect
                }
            });

            // Tidak memaksa fokus kembali setelah pengguna klik di luar input
        });
    </script>
</body>
</html>
