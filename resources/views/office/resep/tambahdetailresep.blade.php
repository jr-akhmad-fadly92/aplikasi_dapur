<!DOCTYPE html>
<!--
Halaman template awal. Gunakan halaman ini sebagai titik awal project.
Menghilangkan tautan yang tidak perlu dan hanya menyediakan markup utama.
-->
<html lang="en">
<head>
    <title>{{ $header }}</title>

    {{-- Include file head (CSS, meta, dll) --}}
    @include('Template.head')

    {{-- Styling khusus untuk form --}}
    <style>
        .form-control {
            height: 40px;
            width: 100%;
        }

        .select2-container .select2-selection--single {
            height: 40px !important;
            padding: 5px;
            display: flex;
            align-items: center;
        }

        .select2-selection__rendered {
            line-height: 30px !important;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        {{-- Navbar --}}
        @include('Template.navbar')

        {{-- Sidebar kiri --}}
        @include('Template.left-sidebar')

        {{-- Konten utama halaman --}}
        <div class="content-wrapper">

            {{-- Header halaman --}}
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark" id="currentTime">Starter Page</h1>
                        </div>
                        <div class="col-sm-6">
                            {{-- Kosong, bisa diisi breadcrumb atau tombol --}}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Isi konten --}}
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">

                                {{-- Header card --}}
                                <div class="card-header">
                                    <h1>{{ $header }}</h1>
                                </div>

                                {{-- Body card --}}
                                <div class="card-body">
                                    {{-- Form tambah bahan --}}
                                    <form action="{{ route('prosestambahbahan') }}" method="post">
                                        {{ csrf_field() }}

                                        {{-- Input hidden menu_id --}}
                                        <input hidden type="text" class="form-control" name="menu_id" value="{{ $menu_id }}" placeholder="Nama">

                                        {{-- Pilih bahan --}}
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Bahan</label>
                                            <select class="form-control select2" id="bahan_id" name="bahan_id">
                                                @foreach ($masterbahan as $data)
                                                    <option value="{{ $data->id }}">{{ $data->bahan }} ({{ $data->nama_satuan }})</option>
                                                @endforeach
                                            </select>
                                            @error('bahan_id')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Input jumlah --}}
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Jumlah</label>
                                            <input type="number" id="jumlah" class="form-control @error('jumlah') is-invalid @enderror" name="jumlah" value="1" placeholder="1">
                                            @error('jumlah')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Input Status bahan baku --}}
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Status Bahan Baku</label>
                                             <select class="form-control " id="status_bahan_baku" name="status_bahan_baku">
                                                <option value=1 >Ke 1</option>
                                                <option value=2 >Ke 2</option>
                                                <option value=4 >Ke 3</option>
                                                <option value=5 >Ke 4</option>
                                                <option value=3 selected>bumbu</option>
                                               
                                            </select>
                                            @error('status_bahan_baku')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Input Pembagi --}}
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Jumlah Bahan Utama ( ml / gram / satuan terkecil  )</label>
                                            <input type="number" id="pembagi" class="form-control @error('pembagi') is-invalid @enderror" name="pembagi" value="{{ $defaultPembagi ?? 1 }}" placeholder="---">
                                            @error('pembagi')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Input Pengali --}}
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Jumlah Bumbu Yang Digunakan ( ml / gram / kg  )</label>
                                            <input type="number" id="pengali" class="form-control @error('pengali') is-invalid @enderror" name="pengali" value="{{ $defaultPengali ?? 1 }}" placeholder="---">
                                            @error('pengali')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        {{-- Input Keterangan --}}
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Keterangan perhitungan</label>
                                            <input type="text" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" name="keterangan" value="" placeholder="(jumlah x gramasi ) / pembagi x pengali contoh : (200 siswa x 100 grm ) / 1 kg * 10 potong |  setiap 1 kg = 10 potong">
                                            @error('keterangan')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        

                                        {{-- Tombol aksi --}}
                                        <button type="submit" class="btn btn-md btn-primary me-3">Simpan</button>
                                        <a href="{{ route('detailresep.index', $menu_id) }}" class="btn btn-md btn-warning">Kembali</a>
                                    </form>
                                </div>
                                {{-- End card-body --}}
                            </div>
                            {{-- End card --}}
                        </div>
                    </div>
                </div>
            </section>
            {{-- End content --}}
        </div>
        {{-- End content-wrapper --}}

        {{-- Sidebar kontrol tambahan (jika digunakan) --}}
        <aside class="control-sidebar control-sidebar-dark">
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>

        {{-- Footer --}}
        @include('Template.footer')
    </div>
    {{-- End wrapper --}}

    {{-- Scripts tambahan --}}
    @include('Template.script')

    {{-- Inisialisasi Select2 --}}
    <script>
        $(document).ready(function() {
            $('#bahan_id').select2({
                placeholder: "Pilih Bahan",
                allowClear: true
            });

            const jumlahInput = document.getElementById('jumlah');
            const pengaliInput = document.getElementById('pengali');

            const syncPengaliWithJumlah = () => {
                pengaliInput.value = jumlahInput.value;
            };

            syncPengaliWithJumlah();
            jumlahInput.addEventListener('input', syncPengaliWithJumlah);
            jumlahInput.addEventListener('change', syncPengaliWithJumlah);
        });
    </script>
</body>
</html>
