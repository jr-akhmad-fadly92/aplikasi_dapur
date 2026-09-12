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

        <!-- Main Sidebar Container -->
        @include('Template.left-sidebar')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
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
                                    <h3>Tambah Karyawan</h3>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('karyawan.store') }}" method="POST">
                                        @csrf
                                        
                                        <!-- Nama Karyawan -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Nama</label>
                                            <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" placeholder="Nama karyawan">
                                            @error('nama')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- id_karyawan --->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">id_karyawan</label>
                                            <input type="number" class="form-control @error('id_karyawan') is-invalid @enderror" name="id_karyawan" placeholder="id_karyawan">
                                            @error('id_karyawan')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- Jabatan -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Jabatan</label>
                                            <select class="form-control @error('jabatan') is-invalid @enderror" name="jabatan">
                                                <option value="">-- Pilih Jabatan --</option>
                                                <option value="kepala" {{ old('jabatan') == 'kepala' ? 'selected' : '' }}>Kepala</option>
                                                <option value="sopir" {{ old('jabatan') == 'sopir' ? 'selected' : '' }}>Sopir</option>
                                                <option value="analis" {{ old('jabatan') == 'analis' ? 'selected' : '' }}>Analis</option>
                                            </select>
                                            @error('jabatan')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>


                                        <!-- Tanggal Masuk -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Tanggal Masuk</label>
                                            <input type="date" class="form-control @error('tanggal_masuk') is-invalid @enderror" name="tanggal_masuk" value="{{ old('tanggal_masuk') }}">
                                            @error('tanggal_masuk')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Status -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Status</label>
                                            <select class="form-control @error('status') is-invalid @enderror" name="status">
                                                <option value="">-- Pilih Status --</option>
                                                <option value="kontrak" {{ old('status') == 'kontrak' ? 'selected' : '' }}>Kontrak</option>
                                                <option value="tetap" {{ old('status') == 'tetap' ? 'selected' : '' }}>Tetap</option>
                                            </select>
                                            @error('status')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>


                                        <!-- Kontak --->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Kontak</label>
                                            <input type="number" class="form-control @error('kontak') is-invalid @enderror" name="kontak" placeholder="kontak">
                                            @error('kontak')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
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

</body>
</html>
