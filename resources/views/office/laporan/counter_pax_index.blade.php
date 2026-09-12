<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
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
                        <h1 class="m-0 text-dark">{{ $header }}</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('counter-pax.index') }}" class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ $tanggal }}">
                            </div>
                            <div class="col-md-8" style="margin-top: 31px;">
                                <button type="submit" class="btn btn-primary">Tampilkan</button>
                                <a href="{{ route('counter-pax.export.excel', ['tanggal' => $tanggal]) }}" class="btn btn-success">Export Excel</a>
                                <a href="{{ route('counter-pax.export.pdf', ['tanggal' => $tanggal]) }}" class="btn btn-danger">Export PDF</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Counter Pax - {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</h3>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-hover table-sm">
                            <thead>
                                <tr>
                                    <th style="width: 70px;" class="text-center">No</th>
                                    <th>Nama Sekolah</th>
                                    <th class="text-right">Jumlah Penerima A</th>
                                    <th class="text-right">Jumlah Penerima B</th>
                                    <th class="text-right">Jumlah Penerima Total</th>
                                    <th style="width: 220px; text-align:center;">Counter Ompreng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $index => $row)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $row->nama_sekolah }}</td>
                                        <td class="text-right">{{ number_format($row->jumlah_penerima_a, 0, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($row->jumlah_penerima_b, 0, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($row->jumlah_penerima_total, 0, ',', '.') }}</td>
                                        <td style="min-height: 60px; height: 60px;"></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Tidak ada data pada tanggal terpilih.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-center">Total</th>
                                    <th class="text-right">{{ number_format($totals['a'], 0, ',', '.') }}</th>
                                    <th class="text-right">{{ number_format($totals['b'], 0, ',', '.') }}</th>
                                    <th class="text-right">{{ number_format($totals['total'], 0, ',', '.') }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @include('Template.footer')
</div>
@include('Template.script')
</body>
</html>
