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
                                        <a class="btn  btn-default" href="/suratJalan">Kembali</a>
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
                                                <h2>Detail Surat Jalan</h2>
                                            </div>
                                            <div class="col-4 text-right">
                                                @if($surat_jalan->status == 0)
                                                <a class="btn btn-sm btn-success" href="/suratJalan/formSuratJalan-{{$surat_jalan->id}}"><span class="fa fa-pencil-square"></span> Edit</a>
                                                @elseif($surat_jalan->status == 1)
                                                <button class="btn btn-sm btn-info" onClick="cetak({{$surat_jalan->id}})"><span class="fa fa-print"></span> Cetak A5</button>
                                                <button class="btn btn-sm btn-info" onClick="cetakA4({{$surat_jalan->id}})"><span class="fa fa-print"></span> Cetak A4</button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <!--end card header-->
                                    <!--start card body-->
                                    <div class="card-body">
                                        <table class="table table-hover table-sm table-striped">
                                            <tr>
                                                <td>Rererensi</td>
                                                <td> : </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" name="referensi" id="referensi" value="@if(isset($surat_jalan)) {{ $surat_jalan->referensi }} @endif" readonly>
                                                    
                                                    
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>No. Surat Jalan</td>
                                                <td> : </td>
                                                <td>
                                                    @if(isset($surat_jalan))
                                                    {{ $surat_jalan->no_surat_jalan }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Petugas pengiriman</td>
                                                <td> : </td>
                                                <td>
                                                    @if(isset($surat_jalan))
                                                    {{ $surat_jalan->driver }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Asisten Petugas pengiriman</td>
                                                <td> : </td>
                                                <td>
                                                    @if(isset($surat_jalan))
                                                    {{ $surat_jalan->driver_assistant }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Plat No.</td>
                                                <td> : </td>
                                                <td>
                                                    @if(isset($surat_jalan))
                                                    {{ $surat_jalan->plat_nomor }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Tanggal Kirim</td>
                                                <td> : </td>
                                                <td>
                                                    @if(isset($surat_jalan))
                                                    {{ \Carbon\Carbon::parse($surat_jalan->published_at)->translatedFormat('l, d F Y') }}
                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Status</td>
                                                <td> : </td>
                                                <td>
                                                    @if(isset($surat_jalan))
                                                        @if($surat_jalan->status == 0)
                                                            <span class="badge badge-warning">Draft</span>
                                                        @elseif($surat_jalan->status == 1)
                                                            <span class="badge badge-primary">Dikirim</span>
                                                        @elseif($surat_jalan->status == 2)
                                                            <span class="badge badge-primary">Revised</span>
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                            
                                        </table>
                                    </div>
                                    <!--end card body-->
                                </div>

                                <div class="card">
                                    <div class="card-header" >
                                        <div class="row">
                                            <div class="col-8">
                                                Keterangan
                                            </div>
                                            <div class="col-4 text-right">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <table id="dt_surat_jalan_item" class="table table-bordered table-striped table-sm" cellspacing="0" style="width: 100%;">
                                            <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama sekolah</th>
                                                <th>Alamat</th>
                                                <th>Jumlah A</th>
                                                <th>Jumlah B</th>
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
                                    
                                    <div class="card-body">
                                        
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
        var dt_surat_jalan_item = $('#dt_surat_jalan_item').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url("formSuratJalan/dt_suratJalanItem") }}',
                data: {
                    referensi: $("#referensi").val(),
                }
            },
            columns: [
                
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'nama_sekolah', name: 'nama_sekolah'},
                {data: 'alamat_sekolah', name: 'alamat_sekolah'},
                {data: 'jumlah_a', name: 'jumlah_a'},
                {data: 'jumlah_b', name: 'jumlah_b'},
            ],
            
            responsive: true,
            stateSave: true,
            stateDuration: 60*30,
        
    
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

    function cetak(id){
        window.open("{{url('/suratJalan/pdfSuratJalan')}}?id="+id, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,location=no,width=720");
    }
    function cetakA4(id){
        window.open("{{url('/suratJalan/pdfSuratJalanA4')}}?id="+id, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,location=no,width=720");
    }
            
    </script>

    
    
    <!-- jQuery -->
</body>
</html>
