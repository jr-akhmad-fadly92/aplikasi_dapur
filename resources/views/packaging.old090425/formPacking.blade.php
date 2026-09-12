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
                            <div class="col-md-6 col-xs-12">
                                <div class="card">
                                    <form method="post" id="form_scan_QR" enctype="multipart/form-data" class="col">
                                        @csrf
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="kodeQR">QRCode</label>
                                                <input type="text" class="form-control form-control-sm" id="kodeQR" name="kodeQR" placeholder="Kode QR" autofocus>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <table id="dt_ompreng_transaksi" class="table table-bordered table-striped" cellspacing="0" style="width: 100%;">
                                            <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Kode Ompreng</th>
                                                <th>Jenis Porsi</th>
                                                <th>Aksi</th>
                                            </tr>
                                            </thead>
                                        
                                        
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-xs-12">
                                <div class="card">
                                    <div class="card-header d-flex p-0">
                                        <h3 class="card-title p-3">Data detail</h3>
                                        
                                    </div><!-- /.card-header -->
                                    <div class="card-body">
                                        <table class="table table-sm">
                                            <tr>
                                                <td>ID. Menu</td>
                                                <td>:&nbsp;</td>
                                                <td><input type="text" class="form-control form-control-sm" disabled value="@if (isset($menu)){{$menu->id}}@endif" placeholder="Data menu hari ini tidak ditemukan " id="menu_id"></td>
                                               
                                            </tr>
                                            <tr>
                                                <td>Menu</td>
                                                <td>:&nbsp;</td>
                                                <td>@if (isset($menu)){{$menu->menu}}@endif</td>
                                               
                                            </tr>
                                            <tr>
                                                <td>Jumlah</td>
                                                <td>:</td>
                                                <td>$menu->jumlah_kirim</td>
                                            </tr>
                                            <tr>
                                                <td>Tgl. Pengiriman &nbsp;</td>
                                                <td>:</td>
                                                <td>@if (isset($menu)){{ \Carbon\Carbon::parse($menu->tanggal_kirim)->locale('id')->translatedFormat('l, d F Y') }}@endif</td>
                                             
                                            </tr>
                                        </table>
                                        <hr>
                                        <div class="tab-content">
                                            <div class="row">
                                            <!-- small box -->
                                            <div class="col-lg-6 col-sm-12">
                                                <div class="small-box bg-info">
                                                    <div class="inner">
                                                        <p>Jenis Porsi</p>

                                                        <h1 style="text-align: center;"><span id="jenis_porsi">{{$porsi}}</span></h1>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="ion ion-bag"></i>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                            <!-- small box -->

                                            <!-- small box -->
                                            <div class="col-lg-6 col-sm-12">
                                                <div class="small-box bg-info">
                                                    <div class="inner">
                                                        <p>Total scan</p>

                                                        <h1 style="text-align: center;"><span id="total_ompreng_keluar">@if (isset($total_ompreng_keluar)){{$total_ompreng_keluar['total_ompreng_keluar']}}@endif</span> &nbsp;pax</h1>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="ion ion-bag"></i>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                            <!-- small box -->
                                             <!-- small box -->
                                            <div class="col-lg-6 col-sm-12">
                                                <div class="small-box bg-info">
                                                    <div class="inner">
                                                        <p>Total scan porsi A</p>

                                                        <h1 style="text-align: center;"><span id="jumlah_ompreng_a">@if (isset($total_ompreng_keluar)){{$total_ompreng_keluar['jumlah_ompreng_porsi_a']}}@endif
                                                            
                                                        </span> &nbsp;pax</h1>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="ion ion-bag"></i>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                            <!-- small box -->
                                             <!-- small box -->
                                            <div class="col-lg-6 col-sm-12">
                                                <div class="small-box bg-info">
                                                    <div class="inner">
                                                        <p>Total scan porsi B</p>

                                                        <h1 style="text-align: center;"><span id="jumlah_ompreng_b">@if (isset($total_ompreng_keluar)){{$total_ompreng_keluar['jumlah_ompreng_porsi_b']}}@endif</span> &nbsp;pax</h1>
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

        var table = $('#dt_ompreng_transaksi').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url("formPacking/dt_formPacking") }}',
                data: {
                    menu_id: $("#menu_id").val(),
                    jenis_porsi: $("#jenis_porsi").text()
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'kode_ompreng', name: 'kode_ompreng'},
                {data: 'porsi', name: 'porsi'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        
    
        });
        $('#kodeQR').keypress(function(event) {
            var formData = new FormData(document.getElementById('form_scan_QR'));
            //formData.append('kode_rantang', $('#kode_rantang').text());
            formData.append('tb_menu_id', $('#menu_id').val());
            formData.append('jenis_porsi', $('#jenis_porsi').text());
            if (event.keyCode === 13) { // Enter key
                event.preventDefault();
                //alert(formData);
                $.ajax({
                    url: "{{ url('formPacking/ajax_scanQR') }}",
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(response) {
                        // Handle success response
                        //alert(JSON.stringify(response));
                        console.log(response);
                        if (response.success) {
                            //alert(response.kode_rantang);
                            Toast.fire({
                                icon: response.type,
                                title: response.message
                            })
                            //$("#kode_rantang").html(response.kode_rantang);
                            //$("#jumlah_ompreng").html(response.jumlah_ompreng);
                            $("#total_ompreng_keluar").html(response.total_ompreng_keluar);
                            $("#jumlah_ompreng_a").html(response.jumlah_ompreng_porsi_a);
                            $("#jumlah_ompreng_b").html(response.jumlah_ompreng_porsi_b);
                            $("#kodeQR").val('');

                            table.ajax.reload();
                        } else {
                            //alert(response.message);
                            Toast.fire({
                                icon: response.type,
                                title: response.message
                            });
                            if (!response.success) {
                                var audio = new Audio('{{ asset('sounds/warning.mp3') }}');
                                audio.play();
                            }
                            $("#kodeQR").val('');

                        }
                    },
                    error: function(xhr) {
                        // Handle error response
                        console.error(xhr.responseText);
                    }
                });
            }
        });

        
            
    </script>
    
    <!-- jQuery -->
</body>
</html>
