<!DOCTYPE html>
<html lang="en">
<head>
    <title>Data Bagian</title>
    @include('Template.head')
    
    <style>
    thead.custom-header th {
        background-color: #00A6B4;
        color: #fff; /* supaya teksnya tetap terlihat */
        text-align: center;
        vertical-align: middle;
    }
    </style>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

    {{-- Navbar & Sidebar --}}
    @include('Template.navbar')
    @include('Template.left-sidebar')

    {{-- Content Wrapper --}}
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                            <h1 class="m-0 text-dark" id="currentTime">Starter Page</h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <section class="content">
            <div class="container-fluid">

                {{-- Alert sukses --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="fw-bold">Daftar Bagian</h3>
                        <a href="{{ route('bagian.create') }}" class="btn btn-primary btn-sm">
                            + Tambah Bagian
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="custom-header">
                                <tr>
                                    <th style="width: 150px;">ID Bagian</th>
                                    <th>Nama Bagian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bagian as $row)
                                    <tr>
                                        <td>{{ $row->id_bagian }}</td>
                                        <td>{{ $row->nama_bagian }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </section>
    </div>

    {{-- Footer --}}
    @include('Template.footer')

</div>
 @include('Template.script')
<script>
    function updateTime() {
        const now = new Date();

        // Format tanggal bulan tahun
        const dateString = now.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });

        // Format jam:menit:detik
        const timeString = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });

        document.getElementById('currentTime').innerText = dateString + ' ' + timeString;
    }

    setInterval(updateTime, 1000);
    updateTime();
</script>
</body>
</html>
