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
                                <!-- Filter -->
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="jenis_filter">Jenis Bahan:</label>
                                            <select id="jenis_filter" class="form-control form-control-sm select2" style="width: 100%;">
                                                <option value="">Semua Jenis</option>
                                                @foreach($master_bahan as $bahan)
                                                    <option value="{{ $bahan->id }}">{{ $bahan->bahan }}</option>   
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="start_date">Tanggal Mulai:</label>
                                            <input type="date" id="start_date" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-2">
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
                                                <button type="button" class="btn btn-success btn-sm ml-2" id="btn-excel">
                                                    <i class="fas fa-file-excel"></i> Excel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i>
                                                <span id="date-info">Data ditampilkan untuk 30 hari terakhir. Gunakan filter tanggal untuk menyesuaikan rentang data.</span>
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Body -->
                                <div class="card-body">
                                    <!-- Tabel Laporan Bahan Baku -->
                                    <table id="tbl_laporan_bahan_baku" class="table table-bordered table-hover" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Nomor PO</th>
                                                <th>Nama Bahan</th>
                                                <th>Jenis Bahan</th>
                                                <th>Total Bahan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data akan diload via DataTables -->
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="5" style="text-align:right">Total Keseluruhan:</th>
                                                <th id="total-bahan">0</th>
                                            </tr>
                                        </tfoot>
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
                <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Footer -->
        @include('Template.footer')

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    @include('Template.script')

    <script>
        $(document).ready(function() {
            // Set default dates
            $('#start_date').val(moment().subtract(29, 'days').format('YYYY-MM-DD'));
            $('#end_date').val(moment().format('YYYY-MM-DD'));

            // CSRF Token setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize DataTable
            var table = $('#tbl_laporan_bahan_baku').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('laporan-bahan-baku.getData') }}",
                    data: function(d) {
                        d.jenis = $('#jenis_filter').val();
                        d.start_date = formatDateForServer($('#start_date').val());
                        d.end_date = formatDateForServer($('#end_date').val());
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: '5%'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal',
                        width: '12%'
                    },
                    {
                        data: 'nomor_po',
                        name: 'nomor_po',
                        width: '15%'
                    },
                    {
                        data: 'nama_bahan',
                        name: 'nama_bahan',
                        width: '25%'
                    },
                    {
                        data: 'jenis_bahan',
                        name: 'jenis_bahan',
                        width: '15%'
                    },
                    {
                        data: 'total_bahan',
                        name: 'total_bahan',
                        width: '13%',
                        className: 'text-right'
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                lengthChange: false,
                dom: 'Bfrtip',
                buttons: [],
                searching: false,
                language: {
                    processing: "Sedang memuat data...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    loadingRecords: "Memuat...",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    emptyTable: "Tidak ada data yang tersedia",
                    paginate: {
                        first: "Pertama",
                        previous: "Sebelumnya",
                        next: "Selanjutnya",
                        last: "Terakhir"
                    }
                },
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();

                    // Calculate total on this page
                    var pageTotal = api
                        .column(5, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            // Remove number formatting and convert to integer
                            var val = parseFloat(b.toString().replace(/\./g, '').replace(/,/g, '')) || 0;
                            return a + val;
                        }, 0);

                    // Update footer
                    $(api.column(5).footer()).html(
                        pageTotal.toLocaleString('id-ID')
                    );
                }
            });

            // Search button click
            $('#btn-search').click(function() {
                table.ajax.reload();
                updateDateInfo();
            });

            // Export PDF button
            $('#btn-pdf').click(function() {
                var jenis = $('#jenis_filter').val();
                var startDate = formatDateForServer($('#start_date').val());
                var endDate = formatDateForServer($('#end_date').val());

                var params = new URLSearchParams();
                if (jenis) params.append('jenis', jenis);
                if (startDate) params.append('start_date', startDate);
                if (endDate) params.append('end_date', endDate);

                var url = "{{ route('laporan-bahan-baku.export-pdf') }}?" + params.toString();
                window.open(url, '_blank');
            });

            // Export Excel button
            $('#btn-excel').click(function() {
                var jenis = $('#jenis_filter').val();
                var startDate = formatDateForServer($('#start_date').val());
                var endDate = formatDateForServer($('#end_date').val());

                var params = new URLSearchParams();
                if (jenis) params.append('jenis', jenis);
                if (startDate) params.append('start_date', startDate);
                if (endDate) params.append('end_date', endDate);

                var url = "{{ route('laporan-bahan-baku.export-excel') }}?" + params.toString();
                window.location.href = url;
            });

            // Helper function to format date for server
            function formatDateForServer(dateString) {
                if (!dateString) return '';
                
                try {
                    var date = new Date(dateString);
                    var day = String(date.getDate()).padStart(2, '0');
                    var month = String(date.getMonth() + 1).padStart(2, '0');
                    var year = date.getFullYear();
                    return day + '/' + month + '/' + year;
                } catch (e) {
                    return '';
                }
            }

            // Update date info
            function updateDateInfo() {
                var jenis = $('#jenis_filter').val();
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();

                var info = [];
                
                if (startDate && endDate) {
                    var formattedStart = moment(startDate).format('DD/MM/YYYY');
                    var formattedEnd = moment(endDate).format('DD/MM/YYYY');
                    info.push("Periode: " + formattedStart + " - " + formattedEnd);
                } else {
                    info.push("Periode: 30 hari terakhir");
                }
                
                if (jenis) {
                    var jenisNames = {
                        '1': 'Beras',
                        '2': 'Lauk', 
                        '3': 'Sayur',
                        '4': 'Buah',
                        '5': 'Suplemen',
                        '6': 'Bumbu',
                        '7': 'Penunjang'
                    };
                    info.push("Jenis: " + jenisNames[jenis]);
                }

                var infoText = info.length > 0 ? 
                    "Filter aktif: " + info.join(", ") :
                    "Data ditampilkan untuk 30 hari terakhir. Gunakan filter untuk menyesuaikan data.";

                $('#date-info').html(infoText);
            }

            // Load initial data
            table.ajax.reload();

            // Call updateDateInfo on page load and date change
            updateDateInfo();
            $('#start_date, #end_date').on('change', updateDateInfo);

            // Auto-search when filter changes
            $('#jenis_filter').change(function() {
                updateDateInfo();
            });
        });
    </script>   
    <script>
        $(document).ready(function() {
            $('#jenis_filter').select2({
                placeholder: "Pilih Bahan",
                allowClear: true
            });
        });
    </script>

</body>

</html>
