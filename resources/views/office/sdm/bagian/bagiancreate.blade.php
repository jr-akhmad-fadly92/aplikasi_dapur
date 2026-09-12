<!DOCTYPE html>
<html lang="en">
<head>
    <title>Form Tambah Bagian</title>
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
                            <h1 class="m-0 text-dark">Form Tambah Bagian</h1>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="card card-primary">

                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                                <form action="{{ route('bagian.store') }}" method="POST">
                                    @csrf
                                        <div class="mb-3" hidden>
                                            <label for="id_bagian" class="form-label">ID Bagian</label>
                                            <input type="text" class="form-control" id="id_bagian" name="id_bagian" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="nama_bagian" class="form-label">Nama Bagian</label>
                                            <input type="text" class="form-control" id="nama_bagian" name="nama_bagian" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="{{ route('bagian.index') }}" class="btn btn-secondary">Batal</a>
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
