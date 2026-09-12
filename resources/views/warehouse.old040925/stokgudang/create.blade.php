<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        @include('Template.navbar')

        <!-- Sidebar -->
        @include('Template.left-sidebar')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">Tambah Stok Gudang</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3>Tambah Data</h3>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('stokgudang.store') }}" method="POST">
                                        @csrf

                                        <!-- Bahan -->
                                        <div class="form-group">
                                            <label class="font-weight-bold">Nama Barang</label>
                                            <select class="form-control" name="nama_barang" id="nama_barang" required>
                                                <option value="" disabled selected>Pilih Bahan</option>
                                                @foreach ($bahan as $data)
                                                <option value="{{ $data->id }}" data-satuan="{{ $data->satuan }}">
                                                    {{ $data->bahan }}
                                                </option>                                                
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Jumlah -->
                                        <div class="form-group">
                                            <label class="font-weight-bold">Jumlah</label>
                                            <input type="number" class="form-control" name="jumlah" required>
                                        </div>

                                        <!-- Satuan (Otomatis Terisi dari tb_satuan) -->
                                        <div class="form-group">
                                            <label class="font-weight-bold">Satuan</label>
                                            <input type="text" class="form-control" name="satuan_gudang" id="satuan_gudang" readonly required>
                                        </div>
                                        
                                        <!-- Kategori Bahan -->
                                        <div class="form-group">
                                            <label class="font-weight-bold">Kategori Bahan</label>
                                            <select class="form-control" name="kategori_bahan" required>
                                                <option value="Matang">Matang</option>
                                                <option value="Mentah">Mentah</option>
                                            </select>
                                        </div>

                                        <!-- Status Bahan -->
                                        <div class="form-group">
                                            <label class="font-weight-bold">Status Bahan</label>
                                            <select class="form-control" name="status_bahan" required>
                                                <option value="Aman">Aman</option>
                                                <option value="Sedikit">Sedikit</option>
                                                <option value="Habis">Habis</option>
                                            </select>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <button type="reset" class="btn btn-warning">Reset</button>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Footer -->
        @include('Template.footer')

    </div>

    <!-- Scripts -->
    @include('Template.script')

    <script>
        document.getElementById('nama_barang').addEventListener('change', function() {
            let selectedOption = this.options[this.selectedIndex];
            let satuanGudang = selectedOption.getAttribute('data-satuan');

            console.log("Bahan dipilih:", selectedOption.text);
            console.log("Satuan bahan:", satuanGudang);

            document.getElementById('satuan_gudang').value = satuanGudang;
        });
    </script>

</body>
</html>
