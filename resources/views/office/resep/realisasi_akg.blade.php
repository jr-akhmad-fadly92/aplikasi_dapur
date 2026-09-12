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
                        <small class="text-muted">Tambahkan realisasi AKG per bahan untuk resep ini</small>
                    </div>
                    <div class="col-sm-6 text-right">
                        <a href="{{ route('detailresep.index', $resep->id) }}" class="btn btn-info btn-sm">Kembali ke Detail Resep</a>
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

                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Tambah Realisasi AKG</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('resep.realisasi-akg.store', $resep->id) }}" class="row" id="formRealisasiAkg">
                            @csrf
                            <div class="form-group col-md-6">
                                <label>Bahan Nutrisi</label>
                                <select name="id_master_bahan_nutrisi" id="id_master_bahan_nutrisi" class="form-control select2" required>
                                    <option value="">Pilih bahan nutrisi</option>
                                    @foreach($masterBahanList as $bahan)
                                        <option value="{{ $bahan->id }}" data-bdd="{{ (float) ($bahan->bdd ?? 0) }}">{{ $bahan->kode }} - {{ $bahan->nama_bahan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Jumlah Gram</label>
                                <input type="number" name="jumlah_gram" id="jumlah_gram" class="form-control" step="0.01" min="0.01" value="100" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label>BDD %</label>
                                <input type="number" name="bdd_pct" id="bdd_pct" class="form-control" step="0.01" min="0" max="100" value="0" readonly>
                            </div>
                            <div class="form-group col-12">
                                <button type="submit" class="btn btn-primary">Simpan Realisasi</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Daftar Realisasi AKG</h3>
                        <div class="text-muted">Resep: {{ $resep->nama_resep }}</div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Bahan</th>
                                    <th>Gram</th>
                                    <th>BDD %</th>
                                    <th>Energi</th>
                                    <th>Protein</th>
                                    <th>Lemak</th>
                                    <th>Karbo</th>
                                    <th>Serat</th>
                                    <th>Natrium</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($realisasiList as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->masterBahanNutrisi->nama_bahan ?? '-' }}</td>
                                        <td>{{ number_format((float) $item->jumlah_gram, 2, ',', '.') }}</td>
                                        <td>{{ number_format((float) $item->bdd_pct, 2, ',', '.') }}</td>
                                        <td>{{ number_format((float) $item->energi_kcal, 2, ',', '.') }}</td>
                                        <td>{{ number_format((float) $item->protein_g, 2, ',', '.') }}</td>
                                        <td>{{ number_format((float) $item->lemak_g, 2, ',', '.') }}</td>
                                        <td>{{ number_format((float) $item->karbohidrat_g, 2, ',', '.') }}</td>
                                        <td>{{ number_format((float) $item->serat_g, 2, ',', '.') }}</td>
                                        <td>{{ number_format((float) $item->natrium_mg, 2, ',', '.') }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('resep.realisasi-akg.destroy', [$resep->id, $item->id]) }}" onsubmit="return confirm('Hapus realisasi AKG ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center text-muted">Belum ada realisasi AKG untuk resep ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td colspan="4" class="text-right">Total</td>
                                    <td>{{ number_format($totals['energi_kcal'], 2, ',', '.') }}</td>
                                    <td>{{ number_format($totals['protein_g'], 2, ',', '.') }}</td>
                                    <td>{{ number_format($totals['lemak_g'], 2, ',', '.') }}</td>
                                    <td>{{ number_format($totals['karbohidrat_g'], 2, ',', '.') }}</td>
                                    <td>{{ number_format($totals['serat_g'], 2, ',', '.') }}</td>
                                    <td>{{ number_format($totals['natrium_mg'], 2, ',', '.') }}</td>
                                    <td></td>
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
<script>
    $(document).ready(function() {
        var inputGram = document.getElementById('jumlah_gram');
        var inputBdd = document.getElementById('bdd_pct');

        function syncBdd() {
            var selectedValue = $('#id_master_bahan_nutrisi').val();
            if (!selectedValue) {
                inputBdd.value = '0';
                return;
            }
            var selectedOption = $('#id_master_bahan_nutrisi').find('option[value="' + selectedValue + '"]');
            var bdd = selectedOption.data('bdd') || '0';
            inputBdd.value = parseFloat(bdd).toFixed(2);
            if (!inputGram.value) {
                inputGram.value = 100;
            }
        }

        // Initialize Select2
        $('#id_master_bahan_nutrisi').select2({
            placeholder: "Pilih Bahan",
            allowClear: true
        });

        // Listen to Select2 change event
        $('#id_master_bahan_nutrisi').on('change', function() {
            syncBdd();
        });

        // Set default values
        inputGram.value = 100;
        syncBdd();
    });
</script>
</body>
</html>