<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        @include('Template.navbar')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('Template.left-sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark" id="currentTime">Starter Page</h1>
                        </div><!-- /.col -->
                        
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-8">
                                        <h1 >{{ $header }}</h1>
                                    </div>
                                    <div class="col-4 text-right">
                                        <a class="btn  btn-default" href="/datasekolah">Kembali</a>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                        
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                        <div class="row">
                            <div class="col-md-8 col-xs-12">
                                <div class="card">
                                    <!--start card header-->
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-8">
                                                <h2>Data Sekolah</h2>
                                            </div>
                                            <div class="col-4 text-right">
                                                <a class="btn btn-sm btn-success" href="/datasekolah/edit/{{$sekolah->id}}"><span class="fa fa-pencil"></span> Edit</a>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end card header-->
                                    <!--start card body-->
                                    <div class="card-body">
                                        <table class="table table-hover table-sm">
                                            <tr>
                                                <td>ID Sekolah</td>
                                                <td> : </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" name="id_sekolah" id="id_sekolah" value="@if(isset($sekolah)) {{ $sekolah->id }} @endif" disabled>
                                                    
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Nama Sekolah</td>
                                                <td> : </td>
                                                <td>
                                                    @if(isset($sekolah))
                                                    {{ $sekolah->nama_sekolah }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Jenjang Sekolah</td>
                                                <td> : </td>
                                                <td>
                                                    @if(isset($sekolah))
                                                    {{ $sekolah->jenjang_sekolah }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr></tr>
                                                <td>Alamat Sekolah</td>
                                                <td> : </td>
                                                <td>
                                                    @if(isset($sekolah))
                                                    {{ $sekolah->alamat_sekolah }}
                                                    @endif
                                                </td>
                                        </table>
                                    </div>
                                    <!--end card body-->
                                </div>

                                <div class="card">
                                    <div class="card-header" >
                                        <div class="row">
                                            <div class="col-8">
                                                Data history jumlah siswa penerima makan gratis
                                            </div>
                                            <div class="col-4 text-right">
                                                <button class="btn btn-sm btn-success" onclick="formSiswa({{$sekolah->id}})">Input siswa</button>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <table id="dt_siswa" class="table table-bordered table-striped" cellspacing="0" style="width: 100%;">
                                            <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tahun Ajaran</th>
                                                <th>Kelompok A</th>
                                                <th>Kelompok B</th>
                                                <th>Status</th>
                                            </tr>
                                            </thead>
                                        
                                        
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-xs-12">
                                <div class="card">
                                    <div class="card-header d-flex p-0">
                                        <h3 class="card-title p-3">Informasi</h3>
                                        
                                    </div><!-- /.card-header -->
                                    <!-- /.card-body -->
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-md-9 col-xs-9">
                                                <h4>Hari Aktif Sekolah</h4>
                                            </div>
                                            <div class="col-md-3 col-xs-3" style="text-align: right;">
                                                <button class="btn btn-sm btn-success" onclick="formHariAktif('{{$sekolah->id}}')">Edit</button>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-bordered table-striped">
                                            <tr>
                                                <td>Senin</td>
                                                <td> : </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" disabled id="hari_aktif_senin" value="@if (isset($hari_aktif) && $hari_aktif->senin == 1) Masuk @else Libur @endif">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Selasa</td>
                                                <td> : </td>
                                                <td><input type="text" class="form-control form-control-sm" disabled id="hari_aktif_selasa" value="@if (isset($hari_aktif) && $hari_aktif->selasa == 1) Masuk @else Libur @endif"></td>
                                            </tr>
                                            <tr>
                                                <td>Rabu</td>
                                                <td> : </td>
                                                <td><input type="text" class="form-control form-control-sm" disabled id="hari_aktif_rabu" value="@if (isset($hari_aktif) && $hari_aktif->rabu == 1) Masuk @else Libur @endif"></td>
                                            </tr>
                                            <tr>
                                                <td>Kamis</td>
                                                <td> : </td>
                                                <td><input type="text" class="form-control form-control-sm" disabled id="hari_aktif_kamis" value="@if (isset($hari_aktif) && $hari_aktif->kamis == 1) Masuk @else Libur @endif"></td>
                                            </tr>
                                            <tr>
                                                <td>Jumat</td>
                                                <td> : </td>
                                                <td><input type="text" class="form-control form-control-sm" disabled id="hari_aktif_jumat" value="@if (isset($hari_aktif) && $hari_aktif->jumat == 1) Masuk @else Libur @endif"></td>
                                            </tr>
                                            <tr>
                                                <td>Sabtu</td>
                                                <td> : </td>
                                                <td><input type="text" class="form-control form-control-sm" disabled id="hari_aktif_sabtu" value="@if (isset($hari_aktif) && $hari_aktif->sabtu == 1) Masuk @else Libur @endif"></td>
                                            </tr>
                                            <tr>
                                                <td>Minggu</td>
                                                <td> : </td>
                                                <td><input type="text" class="form-control form-control-sm" disabled id="hari_aktif_minggu" value="@if (isset($hari_aktif) && $hari_aktif->minggu == 1) Masuk @else Libur @endif"></td>
                                            </tr>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                           
                        
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
                </section>
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        @include('Template.footer')
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
  
    @include('Template.script')
   
    <script type="text/javascript">
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });

        var table = $('#dt_siswa').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url("detailSekolah/dt_dataSiswa") }}',
                data: {id: $("#id_sekolah").val()}
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'tahun_ajaran', name: 'tahun_ajaran'},
                {data: 'jumlah_a', name: 'jumlah_a'},
                {data: 'jumlah_b', name: 'jumlah_b'},
                {data: 'status', name: 'status'},
            ]
        
    
        });
        
    function formSiswa(id){
        $.confirm({
            title: 'Tambah Siswa',
            columnClass: 'col-md-6 col-md-offset-3 col-xs-12 col-sm-12',
            content: 'url:{{url("/detailSekolah/form_dataSiswa")}}?id='+id,
            type: 'blue',
            buttons:{
            simpan:{
                text: "<i class='fa fa-floppy-o'></i> Simpan",
                btnClass: 'btn-success',
                action: function(){
                $.confirm({
                    title: 'Simpan',
                    content: 'Apakah anda yakin? data yang sudah disimpan tidak bisa dihapus',
                    type: 'red',
                    buttons:{
                    simpan:{
                        text: 'Simpan',
                        btnClass: 'btn-success',
                        action: function(){
                        var formData = new FormData(document.getElementById('form_data_siswa'));
                        formData.append('id_sekolah', $("#id_sekolah").val());
                        $.ajax({
                            type: 'post',
                            url: '{{url("/detailSekolah/ajax_simpanSiswa")}}',
                            data: formData,
                            dataType: 'json',
                            contentType: false,
                            cache: false,
                            processData: false,
                            success: function(show){
                            Toast.fire({
                                type: 'success',
                                title: show.message
                            });
                            jconfirm.instances.forEach(instance => instance.close());
                            table.ajax.reload(null, false);
                            },
                            error: function(jqXHR, exception){
                                var errors = JSON.parse(jqXHR.responseText);
                                var message = "";
                                if(errors['errors']){
                                    message = errors.message + "<ul>";
                                    $.each(errors.errors, function(i, item){
                                        message = message + "<li>" + item + "</li>";
                                    });
                                    message = message + "</ul>"
                                }
                                else{
                                    message = jqXHR.responseText;
                                }
                                $.alert({
                                    title: 'Error',
                                    content: jqXHR.responseText,
                                    type: 'red',
                                    backgroundDismiss: true,
                                    columnClass: 'col-xs-10 col-xs-offset-1 col-md-8 col-md-offset-2'
                                });
                                table.ajax.reload(null, false);
                            }
                        })
                        }
                    },
                    batal: function(){
                        //jconfirm.instances.forEach(instance => instance.close());// Menutup semua alert, confirm, dan dialog

                    },
                    },
                    onOpenBefore: function(){
                    //this.buttons.batal.hide();
                    }
                });
                return false;
                }
            },
            batal: function(){},
            }
        });
    }

    function formHariAktif(id){
        $.confirm({
            title: 'Edit Hari Aktif',
            columnClass: 'col-md-6 col-md-offset-3 col-xs-12 col-sm-12',
            content: 'url:{{url("/detailSekolah/form_hariAktifSekolah")}}?id='+id,
            type: 'blue',
            buttons:{
            simpan:{
                text: "<i class='fa fa-floppy-o'></i> Simpan",
                btnClass: 'btn-success',
                action: function(){
                    var formData = new FormData(document.getElementById('form_hari_aktif_sekolah'));
                        formData.append('id_sekolah', $("#id_sekolah").val());
                        $.ajax({
                            type: 'post',
                            url: '{{url("/detailSekolah/ajax_simpanHariAktifSekolah")}}',
                            data: formData,
                            dataType: 'json',
                            contentType: false,
                            cache: false,
                            processData: false,
                            success: function(show){
                                //alert(JSON.stringify(show));
                                Toast.fire({
                                    type: show.type,
                                    title: show.message
                                });
                                $("#hari_aktif_senin").val(show.hari.senin);
                                $("#hari_aktif_selasa").val(show.hari.selasa);
                                $("#hari_aktif_rabu").val(show.hari.rabu);
                                $("#hari_aktif_kamis").val(show.hari.kamis);
                                $("#hari_aktif_jumat").val(show.hari.jumat);
                                $("#hari_aktif_sabtu").val(show.hari.sabtu);
                                $("#hari_aktif_minggu").val(show.hari.minggu);
                                
                            },
                            error: function(jqXHR, exception){
                                var errors = JSON.parse(jqXHR.responseText);
                                var message = "";
                                if(errors['errors']){
                                    message = errors.message + "<ul>";
                                    $.each(errors.errors, function(i, item){
                                        message = message + "<li>" + item + "</li>";
                                    });
                                    message = message + "</ul>"
                                }
                                else{
                                    message = jqXHR.responseText;
                                }
                                $.alert({
                                    title: 'Error',
                                    content: jqXHR.responseText,
                                    type: 'red',
                                    backgroundDismiss: true,
                                    columnClass: 'col-xs-10 col-xs-offset-1 col-md-8 col-md-offset-2'
                                });
                                //table.ajax.reload(null, false);
                            }
                        })
                }
            },
            batal: function(){},
            }
        });
    }
            
    </script>

    
    
    <!-- jQuery -->
</body>
</html>
