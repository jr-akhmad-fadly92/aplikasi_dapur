<!DOCTYPE html>
<html>
<head>
    <title>Penugasan Harian</title>
    @include('Template.head')
</head>
<body>
    <h1>Penugasan Harian ({{ $tanggal }})</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif


    <table border="1" cellpadding="5" cellspacing="0"  class="display nowrap" style="width:100%" id="rolePenugasanTable">
        <thead>
            <tr>
                <th>Id</th>
                <th>tanggal</th>
                <th>tanggal_selesai</th>
                <th>Detail</th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>
    @include('Template.footer')
    @include('Template.script')
    <script type="text/javascript">
        $(function () {
            $('#rolePenugasanTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dt_role_penugasan') }}",
                columns: [
                    { data: 'id_penugasan', name: 'id_penugasan' },
                    { data: 'tanggal', name: 'tanggal' },
                    { data: 'tanggal_selesai', name: 'tanggal_selesai' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });
    </script>
</body>
</html>
