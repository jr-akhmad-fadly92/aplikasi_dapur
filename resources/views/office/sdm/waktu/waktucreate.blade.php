<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form Tambah Waktu Kerja</title>
    @include('Template.head')
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        {{-- Navbar & Sidebar --}}
        @include('Template.navbar')
        @include('Template.left-sidebar')

        {{-- Content --}}
        <div class="content-wrapper">

            {{-- Header --}}
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">Form Tambah Waktu Kerja</h1>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Error Alert --}}
            @if ($errors->any())
                <div class="alert alert-danger mx-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form Input --}}
            <section class="content">
                <div class="container-fluid">
                    <div class="card card-primary">
                        <div class="card-body">
                            <form action="{{ route('waktu.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="id" class="form-label">ID</label>
                                    <input type="text" id="id" name="id" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="jam_kerja" class="form-label">Jam Kerja</label>
                                    <input type="text" id="jam_kerja" name="jam_kerja" class="form-control" required placeholder="Contoh: 08:00 - 16:00">
                                </div>

                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="{{ route('waktu.index') }}" class="btn btn-primary">Kembali</a>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

        </div>

        {{-- Footer --}}
        @include('Template.footer')
        @include('Template.script')

    </div>
</body>
</html>
