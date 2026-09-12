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
                                <li class="breadcrumb-item"><a href="{{ route('master_bahan_nutrisi.index') }}">Master Bahan Nutrisi</a></li>
                                <li class="breadcrumb-item active">Detail</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $formatNilai = function ($value) {
                    if ($value === null || $value === '') {
                        return '-';
                    }

                    return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
                };
            @endphp

            @if(session('success'))
                <div class="container-fluid">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            @endif

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <h3 class="card-title mb-0">{{ $masterBahanNutrisi->nama_bahan }}</h3>
                                        <small class="text-muted d-block">Kode: {{ $masterBahanNutrisi->kode }} | Kelompok: {{ $masterBahanNutrisi->kelompok ?? '-' }}</small>
                                    </div>
                                    <div class="mt-2 mt-md-0">
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editMasterBahanModal">Edit Semua Nilai</button>
                                        <a href="{{ route('master_bahan_nutrisi.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-bordered">
                                                <tr><th style="width: 40%">No</th><td>{{ $masterBahanNutrisi->no ?? '-' }}</td></tr>
                                                <tr><th>Kode</th><td>{{ $masterBahanNutrisi->kode }}</td></tr>
                                                <tr><th>Nama Bahan</th><td>{{ $masterBahanNutrisi->nama_bahan }}</td></tr>
                                                <tr><th>Kelompok</th><td>{{ $masterBahanNutrisi->kelompok ?? '-' }}</td></tr>
                                                <tr><th>Mentah / Olahan</th><td>{{ $masterBahanNutrisi->mentah_olahan ?? '-' }}</td></tr>
                                                <tr><th>Sumber</th><td>{{ $masterBahanNutrisi->sumber ?? '-' }}</td></tr>
                                                <tr><th>BDD (%)</th><td>{{ $formatNilai($masterBahanNutrisi->bdd) }}</td></tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered">
                                                <tr><th style="width: 40%">Air</th><td>{{ $formatNilai($masterBahanNutrisi->air) }}</td></tr>
                                                <tr><th>Energi</th><td>{{ $formatNilai($masterBahanNutrisi->energi) }}</td></tr>
                                                <tr><th>Protein</th><td>{{ $formatNilai($masterBahanNutrisi->protein) }}</td></tr>
                                                <tr><th>Lemak</th><td>{{ $formatNilai($masterBahanNutrisi->lemak) }}</td></tr>
                                                <tr><th>Karbohidrat</th><td>{{ $formatNilai($masterBahanNutrisi->karbohidrat) }}</td></tr>
                                                <tr><th>Serat</th><td>{{ $formatNilai($masterBahanNutrisi->serat) }}</td></tr>
                                                <tr><th>Abu</th><td>{{ $formatNilai($masterBahanNutrisi->abu) }}</td></tr>
                                                <tr><th>Natrium</th><td>{{ $formatNilai($masterBahanNutrisi->natrium) }}</td></tr>
                                                <tr><th>Kalium</th><td>{{ $formatNilai($masterBahanNutrisi->kalium) }}</td></tr>
                                                <tr><th>Kalsium</th><td>{{ $formatNilai($masterBahanNutrisi->kalsium) }}</td></tr>
                                                <tr><th>Fosfor</th><td>{{ $formatNilai($masterBahanNutrisi->fosfor) }}</td></tr>
                                                <tr><th>Besi</th><td>{{ $formatNilai($masterBahanNutrisi->besi) }}</td></tr>
                                                <tr><th>Seng</th><td>{{ $formatNilai($masterBahanNutrisi->seng) }}</td></tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="modal fade" id="editMasterBahanModal" tabindex="-1" role="dialog" aria-labelledby="editMasterBahanModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editMasterBahanModalLabel">Edit Master Bahan Nutrisi</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="POST" action="{{ route('master_bahan_nutrisi.update', $masterBahanNutrisi->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label>No</label>
                                        <input type="text" name="no" class="form-control" value="{{ old('no', $masterBahanNutrisi->no) }}">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>Kode</label>
                                        <input type="text" name="kode" class="form-control @error('kode') is-invalid @enderror" value="{{ old('kode', $masterBahanNutrisi->kode) }}" required>
                                        @error('kode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>BDD (%)</label>
                                        <input type="number" name="bdd" class="form-control" value="{{ old('bdd', $masterBahanNutrisi->bdd) }}" min="0" max="100" step="0.01">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>Nama Bahan</label>
                                        <input type="text" name="nama_bahan" class="form-control @error('nama_bahan') is-invalid @enderror" value="{{ old('nama_bahan', $masterBahanNutrisi->nama_bahan) }}" required>
                                        @error('nama_bahan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>Kelompok</label>
                                        <input type="text" name="kelompok" class="form-control" value="{{ old('kelompok', $masterBahanNutrisi->kelompok) }}">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>Mentah / Olahan</label>
                                        <input type="text" name="mentah_olahan" class="form-control" value="{{ old('mentah_olahan', $masterBahanNutrisi->mentah_olahan) }}">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>Sumber</label>
                                        <input type="text" name="sumber" class="form-control" value="{{ old('sumber', $masterBahanNutrisi->sumber) }}">
                                    </div>
                                </div>

                                <div class="row">
                                    @php
                                        $fields = [
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
                                    @foreach($fields as $field => $label)
                                        <div class="col-md-3 form-group">
                                            <label>{{ $label }}</label>
                                            <input type="number" name="{{ $field }}" class="form-control" value="{{ old($field, $masterBahanNutrisi->{$field}) }}" step="0.01">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @include('Template.footer')
    </div>

    @include('Template.script')
</body>
</html>
