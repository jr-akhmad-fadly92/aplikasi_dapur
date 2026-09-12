<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <style type="text/css">
        #orgChart{
            width: auto;
            height: auto;
        }

        #orgChartContainer{
            width: auto;
            height: 0px auto;
            overflow: auto;
            background: #eeeeee;
        }
    </style>
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
                                <div class="col-md-9 col-xs-12">
                                    <h3 class="card-title">Data golongan</h3>
                                </div>
                                <div class="col-md-3 col-xs-12"> 
                                    <button class="btn btn-primary btn-xs float-right" onclick="newGolongan()"><i class="fas fa-plus"></i> Tambah Golongan</button>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="orgChartContainer">
                                        <div id="orgChart"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                        
                    </div>
                    <!-- /.col -->
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

    <!-- jQuery -->
    @include('Template.script')

    <script>
      
    $(document).ready(function() {
        getData();
    });
    var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    function getData(){
        $.ajax({
            url: "{{ url('golongan/ajax_getGolongan') }}",
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                
                jsonData = data;
                renderChart();
            },
            error: function() {
                alert('Error fetching org chart data.');
            }
        });
        //console.log("Data JSON:", jsonData);
    }

    function renderChart() {
        org_chart = $('#orgChart').orgChart({
            data: jsonData,
            showControls: true,
            allowEdit: false,
            onClickNode: function(node) {
                newGolongan(node.data.id, undefined);
            },
            onAddNode: function(node) {
                newGolongan(undefined, node.data.id);
            },
            onDeleteNode: function(node) {
                deleteGolongan(node.data.id);
            }
        });
    }
    
    function newGolongan(id_golongan, id_parent){
      $.confirm({
          title: 'Tambah Golongan',
          columnClass: 'col-md-6 col-md-offset-3 col-xs-12 col-sm-12',
          content: 'url:{{url("/golongan/form_golongan")}}?id='+id_golongan+'&id_parent='+id_parent,
          type: 'blue',
          buttons:{
            simpan:{
              text: "<i class='fa fa-floppy-o'></i> Simpan",
              btnClass: 'btn-success',
              action: function(){
                var formData = new FormData(document.getElementById('form_golongan'));
                $.ajax({
                  type: 'post',
                  url: "{{url('golongan/ajax_simpanGolongan')}}",
                  data: formData,
                  
                  dataType: 'json',
                  contentType: false,
                  cache: false,
                  processData: false,
                  success: function(show){
                    //alert(JSON.stringify(show));
                    //PNotify.removeAll();
                    Toast.fire({
                        icon: show.status,
                        title: show.message
                    });
                    getData();
                    //$(".loading").removeAttr('readonly','readonly');
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
                    //$(".loading").removeAttr('readonly','readonly');
                    getData();

                  }
                })
                
              }
            },
            batal: function(){},
          }
        })
    }

    function deleteGolongan(id){
        $.confirm({
          title: 'Hapus Golongan',
          columnClass: 'col-md-6 col-md-offset-3 col-xs-12 col-sm-12',
          content: 'Anda yakin ingin menghapus golongan?',
          type: 'red',
          buttons:{
            simpan:{
              text: "<i class='fa fa-trash'></i> hapus",
              btnClass: 'btn-danger',
              action: function(){
                $.ajax({
                  type: 'post',
                  url: '{{url("/golongan/ajax_deleteGolongan")}}',
                  data: {
                    id: id
                  },
                  dataType: 'json',
                  headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                  },
                  success: function(show){
                    //alert(show);
                    // alert(JSON.stringify(show));
                    //jconfirm.instances[0].close();
                    Toast.fire({
                        icon: show.status,
                        title: show.message
                    });
                    getData();
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
                    //$(".loading").removeAttr('readonly','readonly');
                    getData();

                  }
                })
              }
            },
            batal: function(){},
          }
        })
      } 
    </script>
</body>
</html>
