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
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('menu-laporan-masak-harian') }}" class="form-inline">
                            <label for="tanggal" class="mr-2 mb-2">Tanggal</label>
                            <input
                                type="date"
                                id="tanggal"
                                name="tanggal"
                                class="form-control form-control-sm mr-2 mb-2"
                                value="{{ $tanggalInput }}"
                            >
                            <button type="submit" class="btn btn-sm btn-primary mr-2 mb-2">
                                Tampilkan
                            </button>
                            <a href="{{ $reportUrl }}" target="_blank" class="btn btn-sm btn-danger mb-2">
                                <i class="fas fa-file-pdf"></i> Download Laporan
                            </a>
                            <button type="button" class="btn btn-sm btn-success ml-2 mb-2" id="btnTambahSisa" data-toggle="modal" data-target="#modalSisaBahanBaku">
                                <i class="fas fa-plus"></i> Input Sisa Bahan
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Sisa Bahan Baku Setelah Packing</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Menu</th>
                                        <th>Karbo (Sisa)</th>
                                        <th>Lauk / Protein (Sisa)</th>
                                        <th>Sayur (Sisa)</th>
                                        <th>Buah (Sisa)</th>
                                        <th>Pendamping (Sisa)</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($sisaBahanRows as $idx => $row)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>{{ $row['menu'] }}</td>
                                        <td>{{ number_format($row['karbo']['qty'], 0, ',', '.') }} {{ $row['karbo']['satuan'] }}</td>
                                        <td>{{ number_format($row['protein']['qty'], 0, ',', '.') }} {{ $row['protein']['satuan'] }}</td>
                                        <td>{{ number_format($row['sayur']['qty'], 0, ',', '.') }} {{ $row['sayur']['satuan'] }}</td>
                                        <td>{{ number_format($row['buah']['qty'], 0, ',', '.') }} {{ $row['buah']['satuan'] }}</td>
                                        <td>{{ number_format($row['susu']['qty'], 0, ',', '.') }} {{ $row['susu']['satuan'] }}</td>
                                        <td>{{ $row['keterangan'] ?: '-' }}</td>
                                        <td>
                                            <button
                                                type="button"
                                                class="btn btn-xs btn-primary btn-edit-sisa"
                                                data-toggle="modal"
                                                data-target="#modalSisaBahanBaku"
                                                data-menu-id="{{ $row['id_menu'] }}"
                                                data-menu-name="{{ $row['menu'] }}"
                                                data-sisa-karbo="{{ $row['karbo']['qty'] }}"
                                                data-sisa-protein="{{ $row['protein']['qty'] }}"
                                                data-sisa-sayur="{{ $row['sayur']['qty'] }}"
                                                data-sisa-buah="{{ $row['buah']['qty'] }}"
                                                data-sisa-susu="{{ $row['susu']['qty'] }}"
                                                data-keterangan="{{ $row['keterangan'] }}"
                                            >
                                                Edit/Input
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">Tidak ada data menu untuk tanggal ini.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Laporan (Tabel)</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Menu</th>
                                        <th>Jumlah Porsi</th>
                                        <th>Jam Mulai</th>
                                        <th>Karbohidrat</th>
                                        <th>Lauk / Protein</th>
                                        <th>Sayur</th>
                                        <th>Buah</th>
                                        <th>Susu / Pendamping</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($tableRows as $idx => $row)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>{{ $row['menu'] }}</td>
                                        <td>{{ number_format($row['jumlah_porsi'], 0, ',', '.') }}</td>
                                        <td>{{ $row['jam_mulai'] }}</td>
                                        <td>
                                            <strong>{{ $row['karbo']['nama'] }}</strong><br>
                                            {{ number_format($row['karbo']['qty'], 0, ',', '.') }} {{ $row['karbo']['satuan'] }}
                                        </td>
                                        <td>
                                            <strong>{{ $row['protein']['nama'] }}</strong><br>
                                            {{ number_format($row['protein']['qty'], 0, ',', '.') }} {{ $row['protein']['satuan'] }}
                                        </td>
                                        <td>
                                            <strong>{{ $row['sayur']['nama'] }}</strong><br>
                                            {{ number_format($row['sayur']['qty'], 0, ',', '.') }} {{ $row['sayur']['satuan'] }}
                                        </td>
                                        <td>
                                            <strong>{{ $row['buah']['nama'] }}</strong><br>
                                            {{ number_format($row['buah']['qty'], 0, ',', '.') }} {{ $row['buah']['satuan'] }}
                                        </td>
                                        <td>
                                            <strong>{{ $row['susu']['nama'] }}</strong><br>
                                            {{ number_format($row['susu']['qty'], 0, ',', '.') }} {{ $row['susu']['satuan'] }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">Tidak ada data menu untuk tanggal ini.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modalSisaBahanBaku" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('menu-laporan-masak-harian.sisa.store') }}">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggalInput }}">

                    <div class="modal-header">
                        <h5 class="modal-title">Input Sisa Bahan Baku</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="modal_id_menu">Menu</label>
                            <select class="form-control" id="modal_id_menu" name="id_menu" required>
                                <option value="">-- Pilih Menu --</option>
                                @foreach($sisaBahanRows as $row)
                                    <option value="{{ $row['id_menu'] }}">{{ $row['menu'] }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Menu mengikuti data pada tanggal yang dipilih.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modal_sisa_karbo">Sisa Karbo</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="modal_sisa_karbo" name="sisa_karbo" value="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modal_sisa_protein">Sisa Lauk / Protein</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="modal_sisa_protein" name="sisa_protein" value="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modal_sisa_sayur">Sisa Sayur</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="modal_sisa_sayur" name="sisa_sayur" value="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modal_sisa_buah">Sisa Buah</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="modal_sisa_buah" name="sisa_buah" value="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="modal_sisa_susu">Sisa Susu / Pendamping</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="modal_sisa_susu" name="sisa_susu" value="0" required>
                        </div>
                        <div class="form-group mb-0">
                            <label for="modal_keterangan">Keterangan</label>
                            <textarea class="form-control" id="modal_keterangan" name="keterangan" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('Template.footer')
</div>

@include('Template.script')
<script>
    $(document).on('click', '#btnTambahSisa', function() {
        $('#modal_id_menu').val('');
        $('#modal_sisa_karbo').val(0);
        $('#modal_sisa_protein').val(0);
        $('#modal_sisa_sayur').val(0);
        $('#modal_sisa_buah').val(0);
        $('#modal_sisa_susu').val(0);
        $('#modal_keterangan').val('');
    });

    $(document).on('click', '.btn-edit-sisa', function() {
        $('#modal_id_menu').val($(this).data('menu-id'));
        $('#modal_sisa_karbo').val($(this).data('sisa-karbo'));
        $('#modal_sisa_protein').val($(this).data('sisa-protein'));
        $('#modal_sisa_sayur').val($(this).data('sisa-sayur'));
        $('#modal_sisa_buah').val($(this).data('sisa-buah'));
        $('#modal_sisa_susu').val($(this).data('sisa-susu'));
        $('#modal_keterangan').val($(this).data('keterangan'));
    });
</script>
</body>
</html>
