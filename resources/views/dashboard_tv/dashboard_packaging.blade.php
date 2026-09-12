`<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
</head>
<style>
    .info-box .info-box-icon1{
        width: 200px !important;
        height: 100px !important;
        
        border-radius: .25rem;
        -ms-flex-align: center;
        align-items: center;
        display: -ms-flexbox;
        display: flex;
        font-size: 4rem;
        -ms-flex-pack: center;
        justify-content: center;
        text-align: center;
}
    
    .info-box .info-box-content1{
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: column;
        flex-direction: column;
        -ms-flex-pack: center;
        justify-content: center;
        line-height: 120%;
        -ms-flex: 1;
        flex: 1;
        padding: 0 10px;
        font-size: 45px;
    }
    .info-box .info-box-number1 {
        font-size: 15px;
        
        color: grey;
    }
</style>
<body class="hold-transition sidebar-mini sidebar-collapse">
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
                                <h1>Nampan makanan discan di {{ optional($dapur)->nama_dapur ?? '-' }}</h1>
                            </div>
                            <!-- /.card-header -->
                        
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                        <div class="row">
                           @if (isset($detikUser))
                               @foreach ($detikUser as $userId => $detik)
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="info-box mb-3">
                                    <span class="info-box-icon1 bg-info elevation-1" id="jumlah_per_user_{{$userId}}">
                                        @if (isset($jumlahPerUser))
                                            {{$jumlahPerUser[$userId]}}
                                        @else
                                            0
                                        @endif
                                    </span>

                                    <div class="info-box-content1">
                                        <span class="info-box-text">Line {{$userId}}</span>
                                        <span class="info-box-number1"><span id="rata_per_user_{{$userId}}">{{$detik}}</span> detik/ompreng</span>
                                    </div>
                                    <!-- /.info-box-content -->
                                    </div>
                                    <!-- /.info-box -->
                                </div>
                                <!-- /.col -->
                               @endforeach
                           @endif
                            
                            
                             
                        </div>
                        <!-- /.row -->
                        <div class="row">
                            <div class="col-md-6 col-xs-12">
                            <div class="card">
                                    <div class="card-header d-flex p-0">
                                        <h3 class="card-title p-3">Data Menu</h3>
                                        
                                    </div><!-- /.card-header -->
                                    <style>
                                        .table-menu td {
                                            padding: 5px;
                                            font-size: 30px;
                                        }
                                        .table-menu th {
                                            padding: 5px;
                                            font-size: 30px;
                                        }
                                    </style>
                                    <div class="card-body">
                                        <table class="table table-menu">
                                            <tr>
                                                <td>ID. Menu</td>
                                                <td>:&nbsp;</td>
                                                <td>
                                                @if (isset($menu))
                                                {{$menu->id}}
                                                @else
                                                <span class="text-danger">Tidak ada menu</span>
                                                @endif
                                                    <input type="text" class="form-control form-control-sm" readonly value="@if (isset($menu)){{$menu->id}}@endif" placeholder="Data menu hari ini tidak ditemukan " id="menu_id" hidden>
                                                </td>
                                               
                                            </tr>
                                            <tr>
                                                <td>Menu</td>
                                                <td>:&nbsp;</td>
                                                <td>@if (isset($menu)){{$menu->menu}}
                                                @else
                                                    <span class="text-danger">Tidak ada menu</span>
                                                @endif</td>
                                               
                                            </tr>
                                            <tr>
                                                <td>Jumlah</td>
                                                <td>:</td>
                                                <td>
                                                    @if (isset($jumlah_kirim))
                                                        {{$jumlah_kirim}}&nbsp;pax
                                                    @else
                                                        <span class="text-danger">Tidak ada menu</span>
                                                    @endif</td>
                                            </tr>
                                            <tr>
                                                <td>Tgl. Pengiriman &nbsp;</td>
                                                <td>:</td>
                                                <td>@if (isset($menu)){{ \Carbon\Carbon::parse($menu->tanggal_kirim)->locale('id')->translatedFormat('l, d F Y') }}
                                                @else
                                                    <span class="text-danger">Tidak ada menu</span>
                                                @endif</td>
                                             
                                            </tr>
                                        </table>
                                        <hr>
                                        <div style="color: grey;"><i>Waktu scan rata-rata : <span id="waktu_rata">@if (isset($rataRataDetik)){{ $rataRataDetik }}@endif</span> detik/Ompreng</i></div>
                                        <!--<div style="color: grey;"><i>Estimatis Selesai : <span id="waktu_estimasi">@if (isset($estimasiSelesai)){{ $estimasiSelesai }}@endif</span></i></div>-->
                                            
                                    </div><!-- /.card-body -->
                                </div>
                                

                                
                            </div>

                            <div class="col-md-6 col-xs-12">
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
                                                        <h1 style="font-weight: normal;">Total scan</h1>

                                                        <h1 style="text-align: center; font-size: 60px;"><span id="total_ompreng_keluar">@if (isset($total_ompreng_keluar)){{$total_ompreng_keluar['total_ompreng_keluar']}}@endif</span> &nbsp;pax</h1>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="ion ion-bag"></i>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                                
                                                <!-- small box -->
                                                <div class="col-lg-6 col-sm-12">
                                                    <div class="small-box bg-info">
                                                        <div class="inner">
                                                            <h2>Total scan porsi A</h2>

                                                            <h1 style="text-align: center; font-size: 60px;"><span id="jumlah_ompreng_a">@if (isset($total_ompreng_keluar)){{$total_ompreng_keluar['jumlah_ompreng_porsi_a']}}@endif
                                                                
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
                                                            <h2>Total scan porsi B</h2>

                                                            <h1 style="text-align: center;font-size: 60px;"><span id="jumlah_ompreng_b">@if (isset($total_ompreng_keluar)){{$total_ompreng_keluar['jumlah_ompreng_porsi_b']}}@endif</span> &nbsp;pax</h1>
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
        setInterval(function() {
            location.reload();
        }, 180000); // 5 detik

        
        function ajax_getData() {
            $.ajax({
                url: "{{ url('dashboardTvPackaging/ajax_getData') }}",
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $('#total_ompreng_keluar').text(data.total_ompreng_keluar);
                    $('#jumlah_ompreng_a').text(data.jumlah_ompreng_porsi_a);
                    $('#jumlah_ompreng_b').text(data.jumlah_ompreng_porsi_b);
                    $('#waktu_rata').text(data.rataRataDetik);
                    for (const userId in data.detikUser) {
                        // Mengupdate rata-rata waktu untuk setiap user
                        $('#rata_per_user_' + userId).text(data.detikUser[userId]);
                        $('#jumlah_per_user_' + userId).text(data.jumlahPerUser[userId]);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
        setInterval(ajax_getData, 1500); // 5 detik

        
            
    </script>
    
    <!-- jQuery -->
</body>
</html>
`