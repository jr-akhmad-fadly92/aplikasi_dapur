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
                                <!-- Filter Tanggal -->
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="start_date">Tanggal Mulai:</label>
                                            <input type="date" id="start_date" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="end_date">Tanggal Berakhir:</label>
                                            <input type="date" id="end_date" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-6">
                                            <label>&nbsp;</label>
                                            <div>
                                                <button type="button" class="btn btn-info btn-sm" id="btn-search">
                                                    <i class="fas fa-search"></i> Search
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm ml-2" id="btn-pdf">
                                                    <i class="fas fa-file-pdf"></i> PDF
                                                </button>
                                                <button type="button" class="btn btn-success btn-sm ml-2"
                                                    id="btn-excel">
                                                    <i class="fas fa-file-excel"></i> Excel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i>
                                                <span id="date-info">Data ditampilkan untuk 30 hari terakhir. Gunakan
                                                    filter tanggal untuk menyesuaikan rentang data.</span>
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Body -->
                                <div class="card-body">
                                    <!-- Tabel Laporan PO Pembelian -->
                                    <table id="tbl_laporan_po" class="table table-bordered table-hover"
                                        style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Nomor PO</th>
                                                <th>Bahan</th>
                                                <th>Jumlah</th>
                                                <th>Total Harga</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data akan diload via DataTables -->
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="5" style="text-align:right">Total:</th>
                                                <th id="total-harga">Rp 0</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <div class="p-3">
                <h5>Filter</h5>
                <p>Sidebar content</p>
            </div>
        </aside>

        <!-- Main Footer -->
        @include('Template.footer')
    </div>

    <!-- Scripts -->
    @include('Template.script')

    <script>
        $(document).ready(function () {
            // Set default dates
            $('#start_date').val(moment().subtract(29, 'days').format('YYYY-MM-DD'));
            $('#end_date').val(moment().format('YYYY-MM-DD'));

            // Inisialisasi DataTable
            var table = $('#tbl_laporan_po').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                order: [[1, 'desc']], // Order by tanggal descending
                ajax: {
                    url: "{{ route('laporan-bahan.getData') }}",
                    type: 'GET',
                    data: function (d) {
                        var startDate = $('#start_date').val();
                        var endDate = $('#end_date').val();
                        if (startDate && endDate) {
                            d.start_date = moment(startDate).format('DD/MM/YYYY');
                            d.end_date = moment(endDate).format('DD/MM/YYYY');
                        }
                        return d;
                    },
                    error: function (xhr, error, thrown) {
                        console.error('Error loading data:', error);
                        alert('Terjadi kesalahan saat memuat data. Silakan coba lagi.');
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'tanggal', name: 'tanggal' },
                    { data: 'nomor_po', name: 'nomor_po' },
                    { data: 'bahan', name: 'bahan' },
                    { data: 'total_jumlah_bahan', name: 'total_jumlah_bahan' },
                    { data: 'total_harga', name: 'total_harga' }
                ],
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                searching: false,
                language: {
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data yang tersedia",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    search: "Cari:",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                    processing: "Memproses..."
                },
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
                    var total = 0;

                    // Menghitung total dari kolom total_harga (index 5) - halaman saat ini
                    api.column(5, { page: 'current' }).data().each(function (value) {
                        // Hapus 'Rp' dan pemisah ribuan untuk mendapatkan angka
                        var cleanValue = value.replace(/[^0-9]/g, '');
                        var numericValue = parseFloat(cleanValue);
                        if (!isNaN(numericValue)) {
                            total += numericValue;
                        }
                    });

                    // Update footer dengan format yang benar
                    $('#total-harga').html('Rp ' + formatNumber(total));
                }
            });

            // Event search tanggal
            $('#btn-search').on('click', function () {
                var $btn = $(this);
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();

                if (!startDate || !endDate) {
                    alert('Silakan pilih tanggal mulai dan tanggal berakhir');
                    return;
                }

                if (moment(startDate).isAfter(moment(endDate))) {
                    alert('Tanggal mulai tidak boleh lebih besar dari tanggal berakhir');
                    return;
                }

                // Show loading
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');

                table.ajax.reload(function () {
                    // Reset button
                    $btn.prop('disabled', false).html('<i class="fas fa-search"></i> Search');
                }, false); // false = tidak reset ke halaman 1
            });



            // Event export PDF
            $('#btn-pdf').on('click', function () {
                var $btn = $(this);
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();

                if (!startDate || !endDate) {
                    alert('Silakan pilih tanggal mulai dan tanggal berakhir untuk export PDF');
                    return;
                }

                if (moment(startDate).isAfter(moment(endDate))) {
                    alert('Tanggal mulai tidak boleh lebih besar dari tanggal berakhir');
                    return;
                }

                // Show loading state
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generate PDF...');

                var url = "{{ route('laporan-bahan.export-pdf') }}";
                var formattedStartDate = moment(startDate).format('DD/MM/YYYY');
                var formattedEndDate = moment(endDate).format('DD/MM/YYYY');
                url += '?start_date=' + formattedStartDate + '&end_date=' + formattedEndDate;

                // Create a temporary iframe for download
                var iframe = $('<iframe>', {
                    src: url,
                    style: 'display: none;'
                }).appendTo('body');

                // Reset button after a delay
                setTimeout(function () {
                    $btn.prop('disabled', false).html('<i class="fas fa-file-pdf"></i> PDF');
                    iframe.remove();
                }, 3000);
            });

            // Event export Excel
            $('#btn-excel').on('click', function () {
                var $btn = $(this);
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();

                if (!startDate || !endDate) {
                    alert('Silakan pilih tanggal mulai dan tanggal berakhir untuk export Excel');
                    return;
                }

                if (moment(startDate).isAfter(moment(endDate))) {
                    alert('Tanggal mulai tidak boleh lebih besar dari tanggal berakhir');
                    return;
                }

                // Show loading state
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generate Excel...');

                var formattedStartDate = moment(startDate).format('DD/MM/YYYY');
                var formattedEndDate = moment(endDate).format('DD/MM/YYYY');

                // Use GET parameters like PDF export
                var params = new URLSearchParams();
                params.append('start_date', formattedStartDate);
                params.append('end_date', formattedEndDate);

                var url = "{{ route('laporan-bahan.export-excel') }}?" + params.toString();
                window.location.href = url;

                var url = "{{ route('laporan-bahan.export-excel') }}?" + params.toString();
                window.location.href = url;

                // Reset button after a delay
                setTimeout(function () {
                    $btn.prop('disabled', false).html('<i class="fas fa-file-excel"></i> Excel');
                }, 3000);
            });

            // Format number function
            function formatNumber(num) {
                var number = parseFloat(num);
                if (isNaN(number)) return '0';
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // Update date info
            function updateDateInfo() {
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();

                if (startDate && endDate) {
                    var formattedStart = moment(startDate).format('DD/MM/YYYY');
                    var formattedEnd = moment(endDate).format('DD/MM/YYYY');
                    $('#date-info').html('Data ditampilkan dari tanggal <strong>' + formattedStart + '</strong> sampai <strong>' + formattedEnd + '</strong>');
                } else {
                    $('#date-info').html('Data ditampilkan untuk 30 hari terakhir. Gunakan filter tanggal untuk menyesuaikan rentang data.');
                }
            }

            // Call updateDateInfo on page load and date change
            updateDateInfo();
            $('#start_date, #end_date').on('change', updateDateInfo);
        });
    </script>

</body>

</html>