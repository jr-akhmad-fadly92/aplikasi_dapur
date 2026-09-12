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
                    <div class="col-sm-6 text-right">
                        <a href="{{ route('master_bahan.index') }}" class="btn btn-secondary btn-sm">Kembali ke Master Bahan</a>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card mb-3">
                    <div class="card-header"><strong>Tambah AKG Bahan</strong></div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('master_bahan.akg.store', $bahan->id) }}" class="row">
                            @csrf
                            <div class="col-md-3 form-group">
                                <label>Master Bahan Nutrisi</label>
                                <select id="id_master_bahan_nutrisi" name="id_master_bahan_nutrisi" class="form-control select2" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach($masterBahanNutrisiList as $item)
                                        <option value="{{ $item->id }}" 
                                            data-bdd="{{ $item->bdd ?? 0 }}"
                                            data-energi="{{ $item->energi ?? 0 }}"
                                            data-protein="{{ $item->protein ?? 0 }}"
                                            data-lemak="{{ $item->lemak ?? 0 }}"
                                            data-karbohidrat="{{ $item->karbohidrat ?? 0 }}"
                                            data-serat="{{ $item->serat ?? 0 }}"
                                            data-natrium="{{ $item->natrium ?? 0 }}">
                                            {{ $item->kode }} - {{ $item->nama_bahan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1 form-group">
                                <label>BDD</label>
                                <input type="number" id="bdd" step="0.01" min="0" name="bdd" class="form-control" value="0.00" readonly style="background-color: #f0f0f0;">
                            </div>
                            <div class="col-md-2 form-group">
                                <label>Energi</label>
                                <input type="number" id="energi" step="0.01" min="0" name="energi" class="form-control" value="0.00" readonly style="background-color: #f0f0f0;">
                            </div>
                            <div class="col-md-1 form-group">
                                <label>Protein</label>
                                <input type="number" id="protein" step="0.01" min="0" name="protein" class="form-control" value="0.00" readonly style="background-color: #f0f0f0;">
                            </div>
                            <div class="col-md-1 form-group">
                                <label>Lemak</label>
                                <input type="number" id="lemak" step="0.01" min="0" name="lemak" class="form-control" value="0.00" readonly style="background-color: #f0f0f0;">
                            </div>
                            <div class="col-md-1 form-group">
                                <label>Karbo</label>
                                <input type="number" id="karbohidrat" step="0.01" min="0" name="karbohidrat" class="form-control" value="0.00" readonly style="background-color: #f0f0f0;">
                            </div>
                            <div class="col-md-1 form-group">
                                <label>Serat</label>
                                <input type="number" id="serat" step="0.01" min="0" name="serat" class="form-control" value="0.00" readonly style="background-color: #f0f0f0;">
                            </div>
                            <div class="col-md-1 form-group">
                                <label>Natrium</label>
                                <input type="number" id="natrium" step="0.01" min="0" name="natrium" class="form-control" value="0.00" readonly style="background-color: #f0f0f0;">
                            </div>
                            <div class="col-md-12 form-group">
                                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><strong>Daftar AKG Bahan: {{ $bahan->bahan }}</strong></div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Master Nutrisi</th>
                                    <th>BDD</th>
                                    <th>Energi</th>
                                    <th>Protein</th>
                                    <th>Lemak</th>
                                    <th>Karbohidrat</th>
                                    <th>Serat</th>
                                    <th>Natrium</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($akgList as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row->masterBahanNutrisi->kode ?? '-' }} - {{ $row->masterBahanNutrisi->nama_bahan ?? '-' }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('master_bahan.akg.update', [$bahan->id, $row->id]) }}" class="form-inline">
                                            @csrf
                                            <input type="number" step="0.01" min="0" name="bdd" class="form-control form-control-sm mr-1" value="{{ number_format($row->bdd, 2) }}">
                                    </td>
                                    <td><input type="number" step="0.01" min="0" name="energi" class="form-control form-control-sm" value="{{ number_format($row->energi, 2) }}"></td>
                                    <td><input type="number" step="0.01" min="0" name="protein" class="form-control form-control-sm" value="{{ number_format($row->protein, 2) }}"></td>
                                    <td><input type="number" step="0.01" min="0" name="lemak" class="form-control form-control-sm" value="{{ number_format($row->lemak, 2) }}"></td>
                                    <td><input type="number" step="0.01" min="0" name="karbohidrat" class="form-control form-control-sm" value="{{ number_format($row->karbohidrat, 2) }}"></td>
                                    <td><input type="number" step="0.01" min="0" name="serat" class="form-control form-control-sm" value="{{ number_format($row->serat, 2) }}"></td>
                                    <td><input type="number" step="0.01" min="0" name="natrium" class="form-control form-control-sm" value="{{ number_format($row->natrium, 2) }}"></td>
                                    <td>
                                            <button type="submit" class="btn btn-success btn-sm">Update</button>
                                        </form>
                                        <form method="POST" action="{{ route('master_bahan.akg.destroy', [$bahan->id, $row->id]) }}" onsubmit="return confirm('Hapus data AKG ini?')" class="mt-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="text-center">Belum ada data AKG bahan.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @include('Template.footer')
</div>

@include('Template.script')
<script>
    $(function () {
        $('.select2').select2({
            width: '100%'
        });

        // Auto-fill nutrition fields when master_bahan_nutrisi is selected
        $('#id_master_bahan_nutrisi').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const bdd = parseFloat(selectedOption.data('bdd') || 0);
            const energi = parseFloat(selectedOption.data('energi') || 0);
            const protein = parseFloat(selectedOption.data('protein') || 0);
            const lemak = parseFloat(selectedOption.data('lemak') || 0);
            const karbohidrat = parseFloat(selectedOption.data('karbohidrat') || 0);
            const serat = parseFloat(selectedOption.data('serat') || 0);
            const natrium = parseFloat(selectedOption.data('natrium') || 0);

            $('#bdd').val(bdd.toFixed(2));
            $('#energi').val(energi.toFixed(2));
            $('#protein').val(protein.toFixed(2));
            $('#lemak').val(lemak.toFixed(2));
            $('#karbohidrat').val(karbohidrat.toFixed(2));
            $('#serat').val(serat.toFixed(2));
            $('#natrium').val(natrium.toFixed(2));
        });
    });
</script>
</body>
</html>
