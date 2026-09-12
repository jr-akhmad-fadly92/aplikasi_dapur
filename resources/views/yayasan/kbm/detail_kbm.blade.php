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

    <!-- Sidebar -->
    @include('Template.left-sidebar')

    <!-- Content Wrapper -->
    <div class="content-wrapper">

        <!-- Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark" id="currentTime">Starter Page</h1>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <div class="card-header p-2">
                                <ul class="nav nav-pills w-100 text-center">
                                    <li class="nav-item w-1/3"><a class="nav-link active" href="#total" data-toggle="tab">Total Jadwal</a></li>
                                    <li class="nav-item w-1/3"><a class="nav-link" href="#jadwal" data-toggle="tab">Jadwal Mingguan</a></li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <div class="tab-content">

                                    <!-- Tab Jadwal Mingguan -->
                                    <div class="tab-pane" id="jadwal">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <table class="table table-bordered table-striped w-100" id="tableKbm">
                                                        <thead>
                                                            <tr>
                                                                <th>Minggu Ke</th>
                                                                <th>Tanggal KBM</th>
                                                                <th>Keterangan</th>
                                                                <th>PO</th>
                                                                <th>Pengiriman</th>
                                                                <th>Pembayaran</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab Total Jadwal -->
                                    <div class="tab-pane active" id="total">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalKbm">
                                                        Tambah KBM
                                                    </button>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-bordered table-striped w-100">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Minggu Awal</th>
                                                                <th>Minggu Akhir</th>
                                                                <th>Tanggal Awal</th>
                                                                <th>Tanggal Akhir</th>
                                                                <th>Total KBM</th>
                                                                <th>Total Libur</th>
                                                                <th>Total Cuti</th>
                                                                <th>Total Semester</th>
                                                                <th>Jumlah PO</th>
                                                                <th>Jumlah Pengiriman</th>
                                                                <th>Jumlah Pembayaran</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php $i = 0; @endphp
                                                            @foreach($data as $r)
                                                                <tr>
                                                                    <td>{{ ++$i }}</td>
                                                                    <td>Minggu Ke {{ $r->minggu_awal }}</td>
                                                                    <td>Minggu Ke {{ $r->minggu_akhir }}</td>
                                                                    <td>{{ Carbon\Carbon::parse($r->tanggal_awal)->translatedFormat('l, d F Y') }}</td>
                                                                    <td>{{ Carbon\Carbon::parse($r->tanggal_akhir)->translatedFormat('l, d F Y') }}</td>
                                                                    <td>{{ $r->total_count_pembayaran }}</td>
                                                                    <td>{{ $r->total_libur_non_weekend }}</td>
                                                                    <td>{{ $r->total_cuti }}</td>
                                                                    <td>{{ $r->total_semester }}</td>
                                                                    <td>{{ $r->total_count_po }}</td>
                                                                    <td>{{ $r->total_count_pengiriman }}</td>
                                                                    <td>{{ $r->total_count_pembayaran }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="settings"></div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>

    <!-- Modal Tambah KBM -->
    <div class="modal fade" id="modalKbm" tabindex="-1" role="dialog" aria-labelledby="modalKbmLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formKbm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalKbmLabel">Tambah KBM</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if($kbm)
                            <div class="mb-3">
                                <label>Tahun Ajaran</label>
                                <input type="text" name="tahun_ajaran" class="form-control" placeholder="2025/2026" value="{{ $kbm->tahun_ajaran ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label>Semester</label>
                                <select name="semester" class="form-control">
                                    <option value="">-- Pilih Semester --</option>
                                    <option value="ganjil" @if($kbm->semester == 'ganjil') selected @endif>Ganjil</option>
                                    <option value="genap" @if($kbm->semester == 'genap') selected @endif>Genap</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Tanggal Mulai KBM</label>
                                <input type="date" name="tanggal_mulai_kbm" class="form-control" value="{{ $kbm->tanggal_mulai_kbm ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label>Tanggal Selesai KBM</label>
                                <input type="date" name="tanggal_selesai_kbm" class="form-control" value="{{ $kbm->tanggal_selesai_kbm ?? '' }}">
                            </div>
                        @else
                            <div class="mb-3">
                                <label>Tahun Ajaran</label>
                                <input type="text" name="tahun_ajaran" class="form-control" placeholder="2025/2026">
                            </div>
                            <div class="mb-3">
                                <label>Semester</label>
                                <select name="semester" class="form-control">
                                    <option value="">-- Pilih Semester --</option>
                                    <option value="ganjil">Ganjil</option>
                                    <option value="genap">Genap</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Tanggal Mulai KBM</label>
                                <input type="date" name="tanggal_mulai_kbm" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label>Tanggal Selesai KBM</label>
                                <input type="date" name="tanggal_selesai_kbm" class="form-control">
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <div class="p-3">
            <h5>Title</h5>
            <p>Sidebar content</p>
        </div>
    </aside>

    <!-- Footer -->
    @include('Template.footer')

</div>

<!-- Scripts -->
@include('Template.script')
<script>
$(document).ready(function(){
    var table = $('#tableKbm').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('detail_kbm.data') }}",
        pageLength: 7,
        columns: [
            { data: 'minggu_ke', name: 'minggu_ke' },
            { data: 'tanggal_kbm', name: 'tanggal_kbm' },
            { data: 'Hari_libur', name: 'Hari_libur' },
            { data: 'po', name: 'po' },
            { data: 'pengiriman', name: 'pengiriman' },
            { data: 'pembayaran', name: 'pembayaran' },
        ]
    });

    $('#formKbm').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url: "{{ route('kbm.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res){
                $('#modalKbm').modal('hide');
                $('#formKbm')[0].reset();
                table.ajax.reload(null, false);
                Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message });
            },
            error: function(xhr){
                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors;
                    let msg = "";
                    $.each(errors, function(key, val){ msg += val[0] + "<br>"; });
                    Swal.fire({ icon: 'error', title: 'Validasi Gagal', html: msg });
                } else {
                    Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: 'Silakan coba lagi.' });
                }
            }
        });
    });
});
</script>
</body>
</html>
