<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tambah Menu Bahan</title>
    @include('Template.head')
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        @include('Template.navbar')

        <!-- Main Sidebar Container -->
        @include('Template.left-sidebar')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Tambah Menu Bahan</h1>
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
                                    <h3>Form Tambah Menu Bahan</h3>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('menubahan.store') }}" method="POST">
                                        @csrf

                                        <!-- Menu -->
                                        <div class="form-group">
                                            <label class="font-weight-bold">Menu</label>
                                            <select class="form-control" name="menu_id" required>
                                                <option value="" disabled selected>Pilih Menu</option>
                                                @foreach ($menus as $menu)
                                                    <option value="{{ $menu->id }}">{{ $menu->nama_menu }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Bahan -->
                                        <div class="form-group">
                                            <label class="font-weight-bold">Bahan</label>
                                            <select class="form-control" name="bahan_id" id="bahan_id" required>
                                                <option value="" disabled selected>Pilih Bahan</option>
                                                @foreach ($bahanMasak as $bahan)
                                                    <option value="{{ $bahan->id }}" data-id_satuan="{{ $bahan->id_satuan }}">
                                                        {{ $bahan->bahan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>


                                        <!-- Jumlah -->
                                        <div class="form-group">
                                            <label class="font-weight-bold">Jumlah</label>
                                            <input type="number" class="form-control" name="jumlah" placeholder="Masukkan jumlah" required>
                                        </div>

                                        <!-- Satuan (Readonly) -->
                                        <div class="form-group">
                                            <label class="font-weight-bold">Satuan</label>
                                            <input type="text" class="form-control" id="satuan_text" readonly>
                                            <input type="hidden" name="id_satuan" id="id_satuan">
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

    <!-- Required Scripts -->
    @include('Template.script')

    <script type="text/javascript">
        $(document).ready(function () {
            $('#bahan_id').change(function () {
                var satuan = $(this).find(':selected').data('id_satuan');
                $('#satuan_text').val(satuan); // Menampilkan satuan di input readonly
                $('#id_satuan').val(satuan);   // Menyimpan id_satuan yang dipilih
            });
        });
    </script>
</body>
</html>
