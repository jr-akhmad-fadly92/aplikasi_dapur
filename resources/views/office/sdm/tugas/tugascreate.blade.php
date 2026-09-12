<!DOCTYPE html>
<html lang="en">
<head>
    <title>Form Tambah Tugas / Kegiatan</title>
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
                           
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Form Tambah Tugas / Kegiatan</h3>
                        </div>
                        
                        <form action="{{ route('tugas.store') }}" method="POST">
                            @csrf
                            <div class="form-group" hidden>
                                <label for="id_tugas">ID Tugas</label>
                                <input type="number" name="id_tugas" id="id_tugas" 
                                    class="form-control @error('id_tugas') is-invalid @enderror" 
                                    value="{{ old('id_tugas') }}">
                                @error('id_tugas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="kegiatan">Kegiatan</label>
                                <input type="text" name="kegiatan" id="kegiatan" 
                                    class="form-control @error('kegiatan') is-invalid @enderror" 
                                    value="{{ old('kegiatan') }}">
                                @error('kegiatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan</button>
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