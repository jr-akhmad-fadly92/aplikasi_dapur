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
                                <li class="breadcrumb-item"><a href="{{ route('dashboard_office') }}">Dashboard</a></li>
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

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ number_format(\App\Models\MasterBahanNutrisi::count()) }}</h3>
                                    <p>Total Bahan</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-leaf"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ number_format($kelompokList->count()) }}</h3>
                                    <p>Kelompok Bahan</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>TKPI 2019</h3>
                                    <p>Sumber Data</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-book"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <h3 class="card-title mb-0">Daftar master bahan nutrisi</h3>
                                    </div>
                                    <div class="mt-2 mt-md-0 d-flex flex-wrap" style="gap: 8px;">
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createMasterBahanModal">
                                            <i class="fas fa-plus"></i> Tambah Master Nutrisi
                                        </button>
                                        <a href="{{ route('master_bahan_nutrisi.template') }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-file-excel"></i> Download Template
                                        </a>
                                        <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#importMasterBahanModal">
                                            <i class="fas fa-upload"></i> Upload File Import
                                        </button>
                                   </div>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label>Filter Kelompok</label>
                                            <select id="filterKelompok" class="form-control select2">
                                                <option value="">Semua Kelompok</option>
                                                @foreach ($kelompokList as $kelompok)
                                                    <option value="{{ $kelompok }}">{{ $kelompok }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="tbl_master_bahan_nutrisi" class="table table-bordered table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Kode</th>
                                                    <th>Nama Bahan</th>
                                                    <th>Kelompok</th>
                                                    <th>Energi</th>
                                                    <th>Protein</th>
                                                    <th>Lemak</th>
                                                    <th>Karbohidrat</th>
                                                    <th>Sumber</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="modal fade" id="importMasterBahanModal" tabindex="-1" role="dialog" aria-labelledby="importMasterBahanModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="importMasterBahanModalLabel">Upload File Master Bahan Nutrisi</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="POST" action="{{ route('master_bahan_nutrisi.import') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    Gunakan file template yang didownload dari tombol di atas. Kolom BDD boleh kosong dan akan disimpan sebagai null.
                                </div>
                                <div class="form-group">
                                    <label>Pilih file Excel</label>
                                    <input type="file" name="file" class="form-control-file" accept=".xlsx,.xls,.csv" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Upload & Import</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="createMasterBahanModal" tabindex="-1" role="dialog" aria-labelledby="createMasterBahanModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createMasterBahanModalLabel">Tambah Master Bahan Nutrisi</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="POST" action="{{ route('master_bahan_nutrisi.store') }}">
                            @csrf
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label>No</label>
                                        <input type="text" name="no" class="form-control" value="{{ old('no') }}">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>Kode</label>
                                        <input type="text" name="kode" class="form-control" value="{{ old('kode') }}" required>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>BDD (%)</label>
                                        <input type="number" name="bdd" class="form-control" value="{{ old('bdd') }}" min="0" max="100" step="0.01">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>Sumber</label>
                                        <select name="sumber" id="sumber" class="form-control select2-tags">
                                            <option value="">-- Pilih atau ketik sumber --</option>
                                            @if(old('sumber') && !$sourceList->contains(old('sumber')))
                                                <option value="{{ old('sumber') }}" selected>{{ old('sumber') }}</option>
                                            @endif
                                            @foreach ($sourceList as $source)
                                                <option value="{{ $source }}" {{ old('sumber') == $source ? 'selected' : '' }}>{{ $source }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>Nama Bahan</label>
                                        <input type="text" name="nama_bahan" class="form-control" value="{{ old('nama_bahan') }}" required>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>Kelompok</label>
                                        <select name="kelompok" id="kelompok" class="form-control select2-tags">
                                            <option value="">-- Pilih atau ketik kelompok --</option>
                                            @if(old('kelompok') && !$kelompokList->contains(old('kelompok')))
                                                <option value="{{ old('kelompok') }}" selected>{{ old('kelompok') }}</option>
                                            @endif
                                            @foreach ($kelompokList as $kelompok)
                                                <option value="{{ $kelompok }}" {{ old('kelompok') == $kelompok ? 'selected' : '' }}>{{ $kelompok }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>Mentah / Olahan</label>
                                        <select name="mentah_olahan" id="mentah_olahan" class="form-control select2-tags">
                                            <option value="">-- Pilih atau ketik mentah/olahan --</option>
                                            @if(old('mentah_olahan') && !$mentahOlahanList->contains(old('mentah_olahan')))
                                                <option value="{{ old('mentah_olahan') }}" selected>{{ old('mentah_olahan') }}</option>
                                            @endif
                                            @foreach ($mentahOlahanList as $value)
                                                <option value="{{ $value }}" {{ old('mentah_olahan') == $value ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                @php
                                    $nutrisiFields = [
                                        'air' => 'Air',
                                        'energi' => 'Energi',
                                        'protein' => 'Protein',
                                        'lemak' => 'Lemak',
                                        'karbohidrat' => 'Karbohidrat',
                                        'serat' => 'Serat',
                                        'abu' => 'Abu',
                                        'natrium' => 'Natrium',
                                        'kalium' => 'Kalium',
                                        'kalsium' => 'Kalsium',
                                        'magnesium' => 'Magnesium',
                                        'fosfor' => 'Fosfor',
                                        'besi' => 'Besi',
                                        'seng' => 'Seng',
                                    ];
                                @endphp

                                <div class="row">
                                    @foreach ($nutrisiFields as $field => $label)
                                        <div class="col-md-3 form-group">
                                            <label>{{ $label }}</label>
                                            <input type="number" name="{{ $field }}" class="form-control" value="{{ old($field) }}" step="0.01">
                                        </div>
                                    @endforeach
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
        </div>

        @include('Template.footer')
    </div>

    @include('Template.script')
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                placeholder: 'Pilih kelompok',
                allowClear: true
            });

            $('.select2-tags').select2({
                placeholder: 'Pilih atau ketik data baru',
                allowClear: true,
                tags: true,
                width: '100%',
                createTag: function (params) {
                    var term = $.trim(params.term);
                    if (!term) {
                        return null;
                    }

                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                }
            });

            var table = $('#tbl_master_bahan_nutrisi').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('master_bahan_nutrisi.index') }}',
                    data: function (d) {
                        d.kelompok = $('#filterKelompok').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'kode', name: 'kode' },
                    { data: 'nama_bahan', name: 'nama_bahan' },
                    { data: 'kelompok', name: 'kelompok' },
                    { data: 'energi', name: 'energi' },
                    { data: 'protein', name: 'protein' },
                    { data: 'lemak', name: 'lemak' },
                    { data: 'karbohidrat', name: 'karbohidrat' },
                    { data: 'sumber', name: 'sumber' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $('#filterKelompok').on('change', function () {
                table.ajax.reload();
            });
        });
    </script>
</body>
</html>
