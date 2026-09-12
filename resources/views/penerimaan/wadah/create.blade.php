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
        
        <!-- Sidebar -->
        @include('Template.left-sidebar')
        
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark" id="currentTime">Starter Page</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Tambah Wadah</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ $header }}</h3>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('master_wadah.store') }}" method="POST">
                                        @csrf
                                        
                                        <!-- Input QR Code -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">QR Code</label>
                                            <input type="text" class="form-control @error('qr_code') is-invalid @enderror" name="qr_code" placeholder="Masukkan QR Code">
                                            @error('qr_code')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Tombol Submit dan Kembali -->
                                        <button type="submit" class="btn btn-md btn-primary me-3">Tambah</button>
                                        <a href="{{ route('master_wadah.index') }}" class="btn btn-md btn-warning">Kembali</a>
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
    
    @include('Template.script')
</body>
</html>
