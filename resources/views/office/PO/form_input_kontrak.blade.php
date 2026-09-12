<!DOCTYPE html>
<html lang="en">
<head>
    <title>Input Kontrak Rincian</title>
    @include('Template.head')
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        @include('Template.navbar')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('Template.left-sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">Input Kontrak Rincian</h1>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                        <div class="card-header">
                           <h3 class="card-title">Input Harga Bahan</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <form action="{{ route('rincian_menu_po.simpan_kontrak', $rincian_harian->id) }}" method="POST">
                                @csrf
                                
                                <div class="form-group mb-3">
                                    <label for="nama_bahan">Nama Bahan</label>
                                    <input type="text" class="form-control" id="nama_bahan" value="{{ $bahan->bahan }}" disabled>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="menu">Menu</label>
                                    <input type="text" class="form-control" id="menu" value="{{ $menu->tanggal_kirim }}" disabled>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="jumlah">Jumlah Dibutuhkan</label>
                                    <input type="text" class="form-control" id="jumlah" value="{{ $rincian_harian->jumlah }}" disabled>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="harga_bahan">Harga Bahan <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('harga_bahan') is-invalid @enderror" 
                                        id="harga_bahan" name="harga_bahan" placeholder="Masukkan harga" 
                                        step="0.01" value="{{ old('harga_bahan') }}" required>
                                    @error('harga_bahan')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan Kontrak
                                    </button>
                                    <a href="{{ route('rincian_menu_po', $rincian_harian->id_menu_harian) }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Batal
                                    </a>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </section>
        </div>
        
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        @include('Template.footer')
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    @include('Template.script')
    <script src="{{ asset('AdminLte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        @if(session('success'))
            Swal.fire({
                icon: "success",
                title: "BERHASIL",
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @elseif(session('error'))
            Swal.fire({
                icon: "error",
                title: "GAGAL!",
                text: "{{ session('error') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @endif
    </script>
</body>
</html>
