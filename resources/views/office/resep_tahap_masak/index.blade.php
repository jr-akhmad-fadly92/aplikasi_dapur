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
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title mb-0">
                                        Resep: <strong>{{ $resep->nama_resep }}</strong> (ID {{ $resep->id }})
                                    </h3>
                                    <div class="card-tools">
                                        <a href="{{ route('resep.index') }}" class="btn btn-info btn-sm">Kembali ke Halaman Resep</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @php
                                        $isEdit = !is_null($editStep);
                                        $formAction = $isEdit
                                            ? route('resep.tahap-masak.update', ['id' => $resep->id, 'tahapMasak' => $editStep->id])
                                            : route('resep.tahap-masak.store', ['id' => $resep->id]);
                                    @endphp

                                    <form action="{{ $formAction }}" method="POST" class="mb-4">
                                        @csrf
                                        @if($isEdit)
                                            @method('PUT')
                                        @endif

                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Tahap</label>
                                                    <input type="number" min="1" class="form-control @error('tahap') is-invalid @enderror" name="tahap" value="{{ old('tahap', $isEdit ? $editStep->tahap : '') }}" placeholder="1" required>
                                                    @error('tahap')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <label>Keterangan Cara Memasak</label>
                                                    <textarea class="form-control @error('keterangan') is-invalid @enderror" name="keterangan" rows="2" placeholder="Contoh: Cuci beras lalu masak sampai matang" required>{{ old('keterangan', $isEdit ? $editStep->keterangan : '') }}</textarea>
                                                    @error('keterangan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Pilih Bahan Untuk Tahap Ini (boleh lebih dari satu)</label>
                                            @php
                                                $selectedBahan = old('id_bahan', $isEdit ? ($editStepSelectedBahanIds ?? []) : []);
                                            @endphp
                                            <div class="border rounded p-2" style="max-height: 220px; overflow-y: auto;">
                                                @forelse($bahanOptions as $bahan)
                                                    <div class="custom-control custom-checkbox mb-1">
                                                        <input
                                                            type="checkbox"
                                                            class="custom-control-input"
                                                            id="bahan_{{ $bahan->id }}"
                                                            name="id_bahan[]"
                                                            value="{{ $bahan->id }}"
                                                            {{ in_array($bahan->id, $selectedBahan) ? 'checked' : '' }}
                                                        >
                                                        <label class="custom-control-label" for="bahan_{{ $bahan->id }}">
                                                            {{ $bahan->bahan }}
                                                        </label>
                                                    </div>
                                                @empty
                                                    <div class="text-muted">Belum ada bahan pada tb_menu_bahan untuk resep ini.</div>
                                                @endforelse
                                            </div>
                                            <small class="text-muted">Centang bahan sesuai tahap. Setiap tahap bisa beda bahan.</small>
                                            @error('id_bahan')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            @error('id_bahan.*')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-sm">
                                            {{ $isEdit ? 'Update Tahap' : 'Simpan Tahap' }}
                                        </button>
                                        @if($isEdit)
                                            <a href="{{ route('resep.tahap-masak.index', $resep->id) }}" class="btn btn-secondary btn-sm">Batal Edit</a>
                                        @endif
                                        <a href="{{ route('resep.index') }}" class="btn btn-info btn-sm">Kembali ke Master Resep</a>
                                    </form>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th style="width: 70px;">Tahap</th>
                                                    <th>Keterangan</th>
                                                    <th>Nama Bahan (JSON)</th>
                                                    <th style="width: 180px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($steps as $step)
                                                    @php
                                                        $storedBahan = $step->id_bahan ?? [];
                                                        $namaBahanArray = collect($storedBahan)
                                                            ->map(function ($value) use ($bahanMap) {
                                                                if (is_numeric($value)) {
                                                                    $idBahan = (int) $value;
                                                                    return $bahanMap[$idBahan] ?? ('ID ' . $idBahan . ' (tidak ditemukan)');
                                                                }

                                                                return $value;
                                                            })
                                                            ->filter()
                                                            ->values()
                                                            ->all();
                                                        $namaBahan = collect($namaBahanArray)
                                                            ->implode(', ');
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $step->tahap }}</td>
                                                        <td>{{ $step->keterangan }}</td>
                                                        <td>
                                                            <code>{{ json_encode($namaBahanArray, JSON_UNESCAPED_UNICODE) }}</code>
                                                            <div class="small text-muted mt-1">{{ $namaBahan ?: '-' }}</div>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('resep.tahap-masak.edit', ['id' => $resep->id, 'tahapMasak' => $step->id]) }}" class="btn btn-warning btn-sm">Edit</a>
                                                            <form action="{{ route('resep.tahap-masak.destroy', ['id' => $resep->id, 'tahapMasak' => $step->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus tahap ini?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">Belum ada tahap memasak untuk resep ini.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <aside class="control-sidebar control-sidebar-dark">
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>

        @include('Template.footer')
    </div>

    @include('Template.script')
    <script src="{{ asset('AdminLte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'BERHASIL',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000
            });
        @endif
    </script>
</body>
</html>
