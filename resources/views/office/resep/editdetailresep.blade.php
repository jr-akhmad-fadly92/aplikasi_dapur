<!DOCTYPE html>
<!--
Template Edit Bahan
Halaman ini digunakan untuk mengubah jumlah bahan dalam suatu menu.
Elemen form hanya memperbolehkan edit jumlah, sedangkan bahan tidak dapat diubah (hanya ditampilkan).
-->
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head') <!-- Memanggil file head yang berisi meta, css, dsb -->

    <style>
        /* Styling untuk form input */
        .form-control {
            height: 40px;
            width: 100%;
        }

        /* Styling untuk select2 agar sejajar dengan input */
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

        <!-- Navbar atas -->
        @include('Template.navbar')

        <!-- Sidebar kiri -->
        @include('Template.left-sidebar')

        <!-- Konten utama -->
        <div class="content-wrapper">

            <!-- Header halaman -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">{{ $header }}</h1>
                        </div>
                        <div class="col-sm-6">
                            <!-- Kosong (bisa digunakan untuk breadcrumb) -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Konten utama -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">

                                <!-- Header card -->
                                <div class="card-header">
                                    <h3>{{ $header }}</h3>
                                </div>

                                <!-- Body card -->
                                <div class="card-body">

                                    <!-- Form edit -->
                                    <form action="{{ route('proseseditbahan') }}" method="post">
                                        {{ csrf_field() }}

                                        <!-- Input hidden ID bahan -->
                                        <input type="hidden" name="id" value="{{ $menubahan->id }}">

                                        <!-- Pilihan bahan (disabled, tidak bisa diubah) -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Bahan</label>
                                            <select class="form-control select2" id="bahan_id" name="bahan_id" disabled>
                                                @foreach ($masterbahan as $data)
                                                    <option value="{{ $data->id }}"
                                                        @if($data->id == $menubahan->bahan_id) selected @else disabled @endif>
                                                        {{ $data->bahan }} ({{ $data->nama_satuan }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('bahan_id')
                                                <div class="alert alert-danger mt-2">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <!-- Input jumlah -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Jumlah</label>
                                            <input type="number" id="jumlah" class="form-control @error('jumlah') is-invalid @enderror" name="jumlah" value="{{ $menubahan->jumlah }}" placeholder="---">
                                            @error('jumlah')
                                                <div class="alert alert-danger mt-2">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Input Status bahan baku --}}
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Status Bahan Baku</label>
                                             <select class="form-control " id="status_bahan_baku" name="status_bahan_baku">
                                                <option value=1 @if($menubahan->status_bahan_baku == 1) selected @endif >Ke 1</option>
                                                <option value=2 @if($menubahan->status_bahan_baku == 2) selected @endif >Ke 2</option>
                                                <option value=4 @if($menubahan->status_bahan_baku == 4) selected @endif >Ke 3</option>
                                                <option value=5 @if($menubahan->status_bahan_baku == 5) selected @endif >Ke 4</option>
                                                <option value=3 @if($menubahan->status_bahan_baku == 3) selected @endif >bumbu</option>
                                            </select>
                                            @error('status_bahan_baku')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        {{-- Input Pembagi --}}
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Jumlah Bahan Utama ( ml / gram / satuan terkecil  )</label>
                                            <input type="number" id="pembagi" class="form-control @error('pembagi') is-invalid @enderror" name="pembagi" value="{{ $defaultPembagi ?? ($perihitungan_bahan->pembagi ?? 1) }}" placeholder="---">
                                            @error('pembagi')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Input Pengali --}}
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Jumlah Bumbu Yang digunakan ( ml / gram / kg  )</label>
                                            <input type="number" id="pengali" class="form-control @error('pengali') is-invalid @enderror" name="pengali" value="{{ $defaultPengali ?? ($perihitungan_bahan->pengali ?? 1) }}" placeholder="---">
                                            @error('pengali')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        {{-- Input Keterangan --}}
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Keterangan perhitungan</label>
                                            <input type="text" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" name="keterangan" value="{{ $perihitungan_bahan->keterangan ?? "(jumlah x porsi ) / pembagi x pengali" }}" placeholder="---">
                                            @error('keterangan')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Tombol aksi -->
                                        <button type="submit" class="btn btn-md btn-primary me-3">UPDATE</button>
                                        <a href="{{ url()->previous() }}" class="btn btn-md btn-warning">Kembali</a>
                                    </form>
                                </div> <!-- /.card-body -->
                            </div> <!-- /.card -->
                        </div> <!-- /.col -->
                    </div> <!-- /.row -->
                </div> <!-- /.container-fluid -->
            </section>
        </div> <!-- /.content-wrapper -->

        <!-- Sidebar kanan opsional -->
        <aside class="control-sidebar control-sidebar-dark">
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>

        <!-- Footer -->
        @include('Template.footer')
    </div>

    <!-- Script JS -->
    @include('Template.script')
    
    <script>
        const jumlahInput = document.getElementById('jumlah');
        const pengaliInput = document.getElementById('pengali');

        const syncPengaliWithJumlah = () => {
            pengaliInput.value = jumlahInput.value;
        };

        syncPengaliWithJumlah();
        jumlahInput.addEventListener('input', syncPengaliWithJumlah);
        jumlahInput.addEventListener('change', syncPengaliWithJumlah);
    </script>
</body>
</html>
