<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form Tambah Waktu Kerja</title>
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

        {{-- Content --}}
        <div class="content-wrapper">

            {{-- Header --}}
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
                            <h3 class="fw-bold">Daftar Waktu Kerja</h3>
                                <a href="{{ route('waktu.create') }}" class="btn btn-primary btn-sm">
                                    Tambah Waktu Kerja
                                </a>
                        </div>
                        
                        <div class="card-body p-0">
                            <table class="table table-bordered table-striped mb-0">
                                <thead class="custom-header">
                                    <tr>
                                        <th width="80">ID</th>
                                        <th>Jam Kerja</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    @forelse($waktu as $row)
                                        <tr>
                                            <td>{{ $row->id }}</td>
                                            <td>{{ $row->jam_kerja }}</td>
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
            </section>
        </div>

         {{-- Footer --}}
        @include('Template.footer')
        @include('Template.script')

    </div>

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
