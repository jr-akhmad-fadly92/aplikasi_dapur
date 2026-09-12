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
                            <h1 class="m-0 text-dark">Tambah Data Dapur</h1>
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
                                    <form action="{{ route('datadapur.store') }}" method="POST">
                                        @csrf


                                        <!-- Nama Dapur-->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Nama Dapur</label>
                                            <input type="text" class="form-control @error('nama_dapur') is-invalid @enderror" name="nama_dapur" step="any" placeholder="Nama Dapur">
                                            @error('nama_dapur')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>


                                        
                                        <!-- Nomor Dapur -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Nomor Dapur</label>
                                            <input type="text" class="form-control @error('nomor_dapur') is-invalid @enderror" name="nomor_dapur" step="any" placeholder="---">
                                            @error('nomor_dapur')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">IP Dapur</label>
                                            <input type="text" class="form-control @error('ip_dapur') is-invalid @enderror" name="ip_dapur" step="any" placeholder="Contoh: 192.168.1.10">
                                            @error('ip_dapur')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- telepon -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Telepon</label>
                                            <input type="text" class="form-control @error('no_telp') is-invalid @enderror" name="no_telp" step="any" placeholder="---">
                                             @error('no_telp')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Email -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Email</label>
                                            <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" step="any" placeholder="---">
                                             @error('email')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Alamat Dapur -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Alamat Dapur</label>
                                            <textarea type="text" class="form-control @error('alamat_dapur') is-invalid @enderror" name="alamat_dapur" step="any" placeholder="Alamat Dapur"></textarea>
                                            @error('alamat_dapur')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- kelurahan -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">kelurahan</label>
                                            <input type="text" class="form-control @error('kelurahan') is-invalid @enderror" name="kelurahan" step="any" placeholder="---">
                                             @error('kelurahan')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- kecamatan -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Kecamatan</label>
                                           <input type="text" class="form-control @error('kecamatan') is-invalid @enderror" name="kecamatan" step="any" placeholder="---">
                                            
                                            @error('kecamatan')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Kota -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Kota</label>
                                            <input type="text" class="form-control @error('kota') is-invalid @enderror" name="kota" step="any" placeholder="---">
                                             @error('kota')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Provinsi -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Provinsi</label>
                                           <input type="text" class="form-control @error('provinsi') is-invalid @enderror" name="provinsi" step="any" placeholder="---">
                                            
                                            @error('provinsi')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a type="reset" href="{{ route('datadapur.index') }}" class="btn btn-warning">Kembali</a>

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
