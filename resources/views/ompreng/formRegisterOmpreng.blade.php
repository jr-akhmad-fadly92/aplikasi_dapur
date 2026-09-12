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
                                <h1 >{{ $header }}</h1>
                            </div>
                            <!-- /.card-header -->
                        
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                        <div class="row">
                            <div class="col-md-8 col-xs-12">
                                <div class="card">
                                    <form method="post" id="form_scan_QR" enctype="multipart/form-data" class="col">
                                        @csrf
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="kodeQR">QRCode</label>
                                                <input type="text" class="form-control form-control-sm" id="kode_ompreng" name="kode_ompreng" placeholder="Kode QR" autofocus autocomplete="false">
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                    <table id="dt_ompreng" class="table table-bordered table-striped" cellspacing="0" style="width: 100%;">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th>ID</th>
                                            <th>Kode Rantang</th>
                                            <th>Nomor Register</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                        </thead>
                                        
                                        
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-xs-12">
                                <div class="card">
                                    <div class="card-header d-flex p-0">
                                        <h3 class="card-title p-3">Data detail</h3>
                                        
                                    </div><!-- /.card-header -->
                                    <div class="card-body">
                                        
                                        <div class="tab-content">
                                            <div class="row">
                                           
                                            <!-- small box -->
                                            <div class="col-lg-12 col-sm-12">
                                                <div class="small-box bg-info">
                                                    <div class="inner">
                                                        
                                                    </div>
                                                    <div class="icon">
                                                        <i class="ion ion-bag"></i>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                            <!-- small box -->
                                            </div>

                                        </div>
                                        <!-- /.tab-content -->
                                    </div><!-- /.card-body -->
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

        var dt_ompreng = $('#dt_ompreng').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{url('ompreng/dt_ompreng')}}",
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex',  orderable: false, searchable: false},
                    {data: 'id', name: 'id'},
                    {data: 'kode_ompreng', name: 'kode_ompreng'},
                    {data: 'nomor', name: 'nomor'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });
        $('#kode_ompreng').keypress(function(event) {
            if (event.keyCode === 13) { // Enter key
                event.preventDefault();
                formRegisterNomorOmpreng();
            }
        });
        function formRegisterNomorOmpreng(){
            
            confirmNomor = $.confirm({
                title: 'Register nomor ompreng',
                columnClass: 'col-md-6 col-md-offset-3 col-xs-12 col-sm-12',
                content: 'url:{{url("/ompreng/form_registerNomorOmpreng")}}',
                type: 'blue',
                buttons:{
                
                Tutup:{
                    text: "<i class='fa fa-floppy-o'></i> Batal",
                    btnClass: 'btn-success',
                    action: function(){
                        //simpanOmpreng();    // Jalankan AJAX

                    }
                },
                
                }
            });
        }

        function simpanOmpreng() {
            var formData = new FormData(document.getElementById('form_scan_QR'));
            formData.append('nomor',$('#nomor').val());
            $.ajax({
                type: 'post',
                url: "{{url('ompreng/ajax_simpanRegisterOmpreng')}}",
                data: formData,
                
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function(show){
                    //alert(JSON.stringify(show));
                    //PNotify.removeAll();
                    Toast.fire({
                        icon: show.type,
                        title: show.message
                    });
                    $("#kode_ompreng").val('');
                    dt_ompreng.ajax.reload();
                    confirmNomor.close();
                    $("#kode_ompreng").focus();


                },
                error: function(jqXHR, exception){
                    var errors = JSON.parse(jqXHR.responseText);
                    //alert();
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
                        content: message,
                        type: 'red',
                        backgroundDismiss: true,
                        columnClass: 'col-xs-10 col-xs-offset-1 col-md-8 col-md-offset-2'
                    });
                    dt_ompreng.ajax.reload();
                    $("#kode_ompreng").focus();


                }
            })
        }

        
            
    </script>
    
    <!-- jQuery -->
</body>
</html>
