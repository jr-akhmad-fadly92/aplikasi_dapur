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
                            <h1 class="m-0 text-dark" id="currentTime">{{ $header }}</h1>
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
                                    <h1>{{ $header }}</h1>
                                    <h3 class="card-title d-inline-block me-3">
                                       
                                    </h3>
                                                                        @if(auth()->check() && auth()->user()->level === 'admin')
                                                                        <form action="{{ route('rekap_po_PDF') }}" method="GET" class="form-inline">
                                        <div class="form-group mb-2 mr-2">
                                          <label for="tanggal_awal" class="mr-2">Tanggal Awal</label>
                                          <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control" required>
                                        </div>
                                      
                                        <div class="form-group mb-2 mr-2">
                                          <label for="tanggal_akhir" class="mr-2">Tanggal Akhir</label>
                                          <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" required>
                                        </div>
                                      
                                        <button type="submit" class="btn btn-primary mb-2 mr-2">Download PDF</button>
                                        <button type="submit" formaction="{{ route('laporan-po.export') }}" class="btn btn-success mb-2 mr-2">Download Excel</button>
                                        <button type="submit" formaction="{{ route('laporan-po-karbo.export') }}" class="btn btn-warning mb-2 mr-2">Download Excel Karbo</button>
                                        <button type="submit" formaction="{{ route('laporan-po-lauk.export') }}" class="btn btn-info mb-2 mr-2">Download Excel Lauk</button>
                                        <button type="submit" formaction="{{ route('laporan-po-sayur.export') }}" class="btn btn-secondary mb-2 mr-2">Download Excel Sayur</button>
                                        <button type="submit" formaction="{{ route('laporan-po-buah.export') }}" class="btn btn-dark mb-2 mr-2">Download Excel Buah</button>
                                        <button type="submit" formaction="{{ route('laporan-po-pendamping.export') }}" class="btn btn-primary mb-2">Download Excel Pendamping</button>
                                      </form>
                                    
                                    <div class="card-header mt-3">
                                        <h5 class="mb-3">Filter Tanggal Kirim Bahan</h5>
                                        <div class="form-inline">
                                            <div class="form-group mb-2 mr-2">
                                              <label for="filter_tanggal_kirim_awal" class="mr-2">Dari Tanggal</label>
                                              <input type="date" name="filter_tanggal_kirim_awal" id="filter_tanggal_kirim_awal" class="form-control">
                                            </div>
                                            <div class="form-group mb-2 mr-2">
                                              <label for="filter_tanggal_kirim_akhir" class="mr-2">Sampai Tanggal</label>
                                              <input type="date" name="filter_tanggal_kirim_akhir" id="filter_tanggal_kirim_akhir" class="form-control">
                                            </div>
                                            <button type="button" class="btn btn-info mb-2 mr-2" onclick="filterTable()">Filter</button>
                                            <button type="button" class="btn btn-secondary mb-2 mr-2" onclick="clearFilter()">Hapus Filter</button>
                                            <button type="button" class="btn btn-success mb-2 mr-2" id="btnDownloadRekapKarbo" onclick="downloadRekapKarbo(event)">Download Excel Rekap Karbo</button>
                                            <button type="button" class="btn btn-warning mb-2 mr-2" id="btnDownloadRekapKarboV2" onclick="downloadRekapKarboV2(event)">Download Excel Rekap Karbo V2</button>
                                            <button type="button" class="btn btn-info mb-2 mr-2" id="btnDownloadRekapLauk" onclick="downloadRekapLauk(event)">Download Excel Rekap Lauk</button>
                                            <button type="button" class="btn btn-secondary mb-2 mr-2" id="btnDownloadRekapSayur" onclick="downloadRekapSayur(event)">Download Excel Rekap Sayur</button>
                                            <button type="button" class="btn btn-dark mb-2 mr-2" id="btnDownloadRekapBuah" onclick="downloadRekapBuah(event)">Download Excel Rekap Buah</button>
                                            <button type="button" class="btn btn-primary mb-2" id="btnDownloadRekapPendamping" onclick="downloadRekapPendamping(event)">Download Excel Rekap Pendamping</button>
                                        </div>
                                    </div>   
                                    @endif
                                    
                                </div>
                                 
                                <!-- Card Body -->
                                <div class="card-body">
                                    
                                    
                                    <!-- Tabel PO -->
                                    <table id="tbl_list_po" class="table table-bordered table-hover" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nomor PO</th>
                                                <th>tanggal Kirim Bahan</th>
                                                <th>Tanggal Pengajuan</th>
                                                <th>Bahan Baku</th>
                                                <th>Status PO</th>
                                                <th>Action</th>
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
        
  
        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>
        
        <!-- Footer -->
        @include('Template.footer')

<script>
function filterTable() {
    var tgl_awal = document.getElementById('filter_tanggal_kirim_awal').value;
    var tgl_akhir = document.getElementById('filter_tanggal_kirim_akhir').value;
    
    if (tgl_awal && tgl_akhir) {
        var table = $('#tbl_list_po').DataTable();
        table.column(1).search('');
        table.draw();
        
        // Reload datatable dengan parameter filter
        var url = window.location.origin + window.location.pathname + 
                  '?tanggal_kirim_awal=' + tgl_awal + '&tanggal_kirim_akhir=' + tgl_akhir;
        table.ajax.url(url).load();
    } else {
        alert('Pilih kedua tanggal terlebih dahulu!');
    }
}

function clearFilter() {
    document.getElementById('filter_tanggal_kirim_awal').value = '';
    document.getElementById('filter_tanggal_kirim_akhir').value = '';
    
    var table = $('#tbl_list_po').DataTable();
    table.ajax.url('{{ route("Rekap_po.index") }}').load();
}

function downloadRekapKarbo(event) {
    event.preventDefault();
    var tgl_awal = document.getElementById('filter_tanggal_kirim_awal').value;
    var tgl_akhir = document.getElementById('filter_tanggal_kirim_akhir').value;
    
    var url = '{{ route("laporan-rekap-karbo.export") }}';
    
    if (tgl_awal && tgl_akhir) {
        url += '?tanggal_awal=' + tgl_awal + '&tanggal_akhir=' + tgl_akhir;
    }
    
    window.location.href = url;
}

function downloadRekapKarboV2(event) {
    event.preventDefault();
    var tgl_awal = document.getElementById('filter_tanggal_kirim_awal').value;
    var tgl_akhir = document.getElementById('filter_tanggal_kirim_akhir').value;
    
    var url = '{{ route("laporan-rekap-karbo-v2.export") }}';
    
    if (tgl_awal && tgl_akhir) {
        url += '?tanggal_awal=' + tgl_awal + '&tanggal_akhir=' + tgl_akhir;
    }
    
    window.location.href = url;
}

function downloadRekapLauk(event) {
    event.preventDefault();
    var tgl_awal = document.getElementById('filter_tanggal_kirim_awal').value;
    var tgl_akhir = document.getElementById('filter_tanggal_kirim_akhir').value;

    var url = '{{ route("laporan-rekap-lauk.export") }}';

    if (tgl_awal && tgl_akhir) {
        url += '?tanggal_awal=' + tgl_awal + '&tanggal_akhir=' + tgl_akhir;
    }

    window.location.href = url;
}

function downloadRekapSayur(event) {
    event.preventDefault();
    var tgl_awal = document.getElementById('filter_tanggal_kirim_awal').value;
    var tgl_akhir = document.getElementById('filter_tanggal_kirim_akhir').value;

    var url = '{{ route("laporan-rekap-sayur.export") }}';

    if (tgl_awal && tgl_akhir) {
        url += '?tanggal_awal=' + tgl_awal + '&tanggal_akhir=' + tgl_akhir;
    }

    window.location.href = url;
}

function downloadRekapBuah(event) {
    event.preventDefault();
    var tgl_awal = document.getElementById('filter_tanggal_kirim_awal').value;
    var tgl_akhir = document.getElementById('filter_tanggal_kirim_akhir').value;

    var url = '{{ route("laporan-rekap-buah.export") }}';

    if (tgl_awal && tgl_akhir) {
        url += '?tanggal_awal=' + tgl_awal + '&tanggal_akhir=' + tgl_akhir;
    }

    window.location.href = url;
}

function downloadRekapPendamping(event) {
    event.preventDefault();
    var tgl_awal = document.getElementById('filter_tanggal_kirim_awal').value;
    var tgl_akhir = document.getElementById('filter_tanggal_kirim_akhir').value;

    var url = '{{ route("laporan-rekap-pendamping.export") }}';

    if (tgl_awal && tgl_akhir) {
        url += '?tanggal_awal=' + tgl_awal + '&tanggal_akhir=' + tgl_akhir;
    }

    window.location.href = url;
}
</script>
    </div>
    
    <!-- REQUIRED SCRIPTS -->
    @include('Template.script')
    
    <!-- DataTable Initialization -->
    <script type="text/javascript">
        $(document).ready(function () {
            $('#tbl_list_po').DataTable({
                ajax: '{{ url()->current() }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nomor_po', name: 'nomor_po' },
                    { data: 'tanggal_kirim', name: 'tanggal_kirim' },
                    { data: 'tanggal_po_dibuat', name: 'tanggal_po_dibuat' },
                    { data: 'bahan_po', name: 'bahan_po' },
                    { data: 'status_po', name: 'status_po' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });
    </script>
    
    <!-- SweetAlert Notification -->
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
    
    <!-- Update PO Event -->
    
</body>
</html>
