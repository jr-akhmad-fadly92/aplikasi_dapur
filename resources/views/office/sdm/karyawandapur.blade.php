<!DOCTYPE html>
<html lang="en">

<head>
    <title>Daftar Tugas Karyawan</title>
    @include('Template.head')
    <style>
        thead.custom-header th {
            background-color: #00A6B4;
            color: #fff;
            /* supaya teksnya tetap terlihat */
            text-align: center;
            vertical-align: middle;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="sidebar-mini sidebar-collapse sidebar-closed">
    <div class="wrapper">
        @include('Template.navbar')
        @include('Template.left-sidebar')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark" id="currentTime">Starter Page</h1>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <!-- Header + Button -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3>Daftar Karyawan</h3>
                        <a href="{{ route('karyawan-dapur.create') }}" class="btn btn-primary btn-sm">
                            Tambah Karyawan
                        </a>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm" style="width: 100%">
                            <thead class="custom-header">
                                <tr>
                                    <th>ID Karyawan</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    <th>No. HP</th>
                                    <th>Status</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Tanggal Keluar</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody style="text-align: center">
                                @foreach ($karyawan as $k)
                                <tr>
                                    <td>{{ $k->id_karyawan }}</td>
                                    <td>{{ $k->nik }}</td>
                                    <td>{{ $k->nama_karyawan }}</td>
                                    <td>{{ $k->alamat }}</td>
                                    <td>{{ $k->no_hp }}</td>
                                    <td>{{ $k->status_karyawan }}</td>
                                    <td>{{ \Carbon\Carbon::parse($k->masuk_kerja)->format('d-m-Y') }}</td>
                        
                                    {{-- Perbaikan ada di sini: --}}
                                    <td>
                                        @if ($k->keluar_kerja)
                                        {{ \Carbon\Carbon::parse($k->keluar_kerja)->format('d-m-Y') }}
                                        @else
                                        -
                                        @endif
                                    </td>
                        
                                    <td>
                                        <a href="{{ route('karyawan-dapur.edit', $k->id_karyawan) }}" class="btn btn-danger btn-sm">
                                            Keluar
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </section>
        </div>

        @include('Template.footer')
    </div>
    @include('Template.script')
    <!-- sebelum tag </body> -->
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