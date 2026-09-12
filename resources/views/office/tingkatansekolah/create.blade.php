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
                            <h1 class="m-0 text-dark">Tambah Data Tingkatan Sekolah</h1>
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
                                    <form action="{{ route('tingkatansekolah.store') }}" method="POST">
                                        @csrf

                                        <!-- Jenjang Sekolah -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Jenjang Sekolah</label>
                                            <select class="form-control @error('jenjang_sekolah') is-invalid @enderror" name="jenjang_sekolah">
                                                <option value="tk-sd_kelas_3">TK - SD Kelas 3</option>
                                                <option value="sd_kelas_4-6">SD Kelas 4-6</option>
                                                <option value="smp">SMP</option>
                                                <option value="sma">SMA</option>
                                                <option value="ibu_hamil">Ibu Hamil</option>
                                            </select>
                                            @error('jenjang_sekolah')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        

                                        <!-- Gramasi Nasi -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Gramasi Nasi (gram)</label>
                                            <input type="number" class="form-control @error('gramasi_nasi') is-invalid @enderror" name="gramasi_nasi" step="any" placeholder="Gramasi Nasi">
                                            @error('gramasi_nasi')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        

                                        <!-- Gramasi Sayur -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Gramasi Sayur (gram)</label>
                                            <input type="number" class="form-control @error('gramasi_sayur') is-invalid @enderror" name="gramasi_sayur" step="any" placeholder="Gramasi Sayur">
                                            @error('gramasi_sayur')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Gramasi Protein -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Gramasi Protein (gram)</label>
                                            <input type="number" class="form-control @error('gramasi_protein') is-invalid @enderror" name="gramasi_protein" step="any" placeholder="Gramasi Protein">
                                            @error('gramasi_protein')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Gramasi Buah -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Gramasi Buah (gram)</label>
                                            <input type="number" class="form-control @error('gramasi_buah') is-invalid @enderror" name="gramasi_buah" step="any" placeholder="Gramasi Buah">
                                            @error('gramasi_buah')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Gramasi Susu -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Gramasi Susu (gram)</label>
                                            <input type="number" class="form-control @error('gramasi_susu') is-invalid @enderror" name="gramasi_susu" step="any" placeholder="Gramasi Susu">
                                            @error('gramasi_susu')
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
