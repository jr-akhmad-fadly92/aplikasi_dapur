<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Karyawan</title>
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
                            <h1 class="m-0 text-dark">Perbarui Data Karyawan</h1>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Tanggal Keluar</h3>
                        </div>
                        
                        <form action="{{ route('karyawan-dapur.update', $karyawan->id_karyawan) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="keluar_kerja">Tanggal Keluar Kerja</label>
                                    <input type="date" name="keluar_kerja" id="keluar_kerja" class="form-control" value="{{ old('keluar_kerja', $karyawan->keluar_kerja) }}">
                                    @error('keluar_kerja')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                <a href="{{ route('karyawan-dapur.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
        @include('Template.footer')
    </div>
    
    @include('Template.script')
</body>
</html>