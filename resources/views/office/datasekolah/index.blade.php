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

        <!-- Main Sidebar Container -->
        @include('Template.left-sidebar')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">

                            <h1 class="m-0 text-dark" >{{ $header }}</h1>

                            <h1 class="m-0 text-dark" id="currentTime">Starter Page</h1>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <a href="{{ route('datasekolah.create') }}" class="btn btn-primary btn-sm">
                                            Tambah Sekolah
                                        </a>
                                    </h3>
                                </div>

                                <div class="card-body">
                                    <table id="tb_list_data_sekolah" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Sekolah</th>
                                                <th>Jenjang Sekolah</th>
                                                <th>Jumlah Siswa</th>
                                                <th>Alamat Sekolah</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data akan di-load oleh DataTables -->
                                        </tbody>
                                    </table>
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

    <script type="text/javascript">
        $(document).ready(function () {
            $('#tb_list_data_sekolah').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{url('datasekolah/dt_dataSekolah')}}",
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nama_sekolah', name: 'nama_sekolah' },
                    { data: 'jenjang_sekolah', name: 'jenjang_sekolah' },
                    { data: 'jumlah_siswa', name: 'jumlah_siswa' },
                    { data: 'alamat_sekolah', name: 'alamat_sekolah' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
            
        });
        
    </script>

   

</body>
</html>
