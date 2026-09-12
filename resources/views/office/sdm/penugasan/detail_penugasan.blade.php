<!DOCTYPE html>
<html>
<head>
    <title>Penugasan Harian</title>
</head>
<body>
    <h1>Penugasan Harian ({{ $tanggal }})</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <a href="{{ route('penugasan.generate') }}">Generate Penugasan Hari Ini</a>

    <table border="1" cellpadding="5" cellspacing="0" style="width: 100%">
        <thead>
            <tr>
                <th>Nama Karyawan</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Kegiatan</th>
                <th>Waktu Mulai</th>
                <th>Waktu Selesai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penugasan as $p)
                <tr>
                    <td>{{ $p->nama_karyawan }}</td>
                    <td>{{ $p->masuk_kerja }}</td>
                    <td>{{ $p->keluar_kerja }}</td>
                    <td>{{ $p->kegiatan }}</td>
                    <td>{{ $p->waktu_mulai }}</td>
                    <td>{{ $p->waktu_selesai }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @include('Template.footer')
    @include('Template.script')
</body>
</html>
