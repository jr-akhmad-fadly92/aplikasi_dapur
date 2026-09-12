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
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <!-- Header + Button -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3>Daftar Tugas Karyawan</h3>
                        <a href="{{ route('tugas.create') }}" class="btn btn-primary btn-sm">
                            Tambah Tugas
                        </a>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive">
                        <table  id="tb_tugas" class="table table-bordered table-striped table-sm">
                            <thead class="custom-header">
                                <tr>
                                    <th>ID Tugas</th>
                                    <th>Kegiatan</th>
                                    <th>Terlampir</th>
                                </tr>
                            </thead>
                    
                            <tbody>
                                @foreach($tugas as $t)
                                <tr>
                                    <td>{{ $t->id_tugas }}</td>
                                    <td>{{ $t->kegiatan }}</td>
                                    <td>{{ $t->terlampir ?? '-' }}</td>
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
    $(document).ready(function() {
        $('#tb_tugas').DataTable({
            responsive: true,
            pageLength: 10,
            ordering: true,
            autoWidth: false,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            }
        });
    });
    </script>
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