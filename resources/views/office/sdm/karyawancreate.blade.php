<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tambah Karyawan Baru</title>
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
                            <h1 class="m-0 text-dark">Tambah Karyawan Baru</h1>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Form Tambah Karyawan</h3>
                        </div>
                        
                        <form action="{{ route('karyawan-dapur.store') }}" method="POST">
                            @csrf
                            
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success">
                                        <p>{{ session('success') }}</p>
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label for="nik">NIK</label>
                                    <input type="text" name="nik" id="nik" class="form-control" value="{{ old('nik') }}" required>
                                    @error('nik') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="nama_karyawan">Nama Karyawan</label>
                                    <input type="text" name="nama_karyawan" id="nama_karyawan" class="form-control" value="{{ old('nama_karyawan') }}" required>
                                    @error('nama_karyawan') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <textarea name="alamat" id="alamat" class="form-control">{{ old('alamat') }}</textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="no_hp">No. HP</label>
                                    <input type="text" name="no_hp" id="no_hp" class="form-control" value="{{ old('no_hp') }}">
                                </div>

                                <div class="form-group">
                                    <label for="status_karyawan">Status Perkawinan</label>
                                    <select name="status_karyawan" id="status_karyawan" class="form-control">
                                        <option value="Kawin" {{ old('status_karyawan') == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                                        <option value="Belum Kawin" {{ old('status_karyawan') == 'Tidak Kawin' ? 'selected' : '' }}>Tidak Kawin</option>
                                        <option value="Cerai" {{ old('status_karyawan') == 'Cerai' ? 'selected' : '' }}>Cerai</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="masuk_kerja">Tanggal Masuk Kerja</label>
                                    <input type="date" name="masuk_kerja" id="masuk_kerja" class="form-control" value="{{ old('masuk_kerja') }}" required>
                                    @error('masuk_kerja') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Simpan Data</button>
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