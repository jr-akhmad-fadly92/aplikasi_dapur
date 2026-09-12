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
                            <div class="card-header" id="head-scroll">
                                <h1 >{{ $header }}</h1>
                            </div>
                            <!-- /.card-header -->
                        
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                        <div class="row">
                            <div class="col-md-6 col-xs-12">
                                <div class="card">
                                    <form method="post" action="{{ route('packing.ajax_scanQR', [], false) }}" id="form_scan_QR" enctype="multipart/form-data" class="col">
                                        @csrf
                                        <div class="card-body">
                                            <div class="btn-group btn-group-toggle d-flex mb-3" data-toggle="buttons" id="scanModeGroup">
                                                <label class="btn btn-outline-primary active w-50">
                                                    <input type="radio" name="scan_mode" value="usb" autocomplete="off" checked> 1. Scanner USB / Ketik Serial
                                                </label>
                                                <label class="btn btn-outline-success w-50">
                                                    <input type="radio" name="scan_mode" value="camera" autocomplete="off"> 2. Scan Barcode HP via Web
                                                </label>
                                            </div>

                                            <div id="usbScanSection">
                                                <div class="form-group mb-1">
                                                    <label for="kodeQR">Input Barcode / Serial</label>
                                                    <input type="text" class="form-control form-control-sm" id="kodeQR" name="kodeQR" placeholder="Scan barcode atau ketik serial lalu Enter" autofocus autocomplete="off">
                                                </div>
                                                <small class="text-muted">Cocok untuk scanner barcode USB / serial input yang bertindak seperti keyboard.</small>
                                                <div class="mt-2 p-2 border rounded bg-light">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <button type="button" class="btn btn-sm btn-warning mr-2 mb-2" id="runDebugLoop100">
                                                            <i class="fas fa-flask"></i> Uji Loop 100x (Line Aktif)
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary mr-2 mb-2" id="runDebugSingle">
                                                            <i class="fas fa-bolt"></i> Uji 1x Debug
                                                        </button>
                                                        <small class="text-muted mb-2">Mode ini hanya untuk debug. Hasil akan ditampilkan tanpa mengganggu scan normal.</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="cameraScanSection" style="display:none;">
                                                <div class="alert alert-info py-2 px-3 mb-2">
                                                    Arahkan kamera HP ke barcode. Sistem akan otomatis membaca lalu mengirim hasil scan.
                                                </div>
                                                <div id="qr-reader" class="border rounded p-2 mb-2" style="width:100%; display:none;"></div>
                                                <div class="d-flex flex-wrap">
                                                    <button type="button" class="btn btn-sm btn-success mr-2 mb-2" id="startCameraScan">
                                                        <i class="fas fa-camera"></i> Aktifkan Kamera
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-secondary mr-2 mb-2" id="stopCameraScan" disabled>
                                                        <i class="fas fa-stop"></i> Stop Kamera
                                                    </button>
                                                    <label for="barcodeImageInput" class="btn btn-sm btn-outline-primary mr-2 mb-2">
                                                        <i class="fas fa-image"></i> Foto / Upload Barcode
                                                    </label>
                                                    <input type="file" id="barcodeImageInput" accept="image/*" capture="environment" style="display:none;">
                                                </div>
                                                <small id="cameraStatus" class="text-muted d-block">Gunakan browser HP dan izinkan akses kamera saat diminta.</small>
                                                <small id="cameraHint" class="text-danger d-block mt-1" style="display:none;">Jika permission kamera tidak muncul di HP, biasanya karena halaman dibuka lewat HTTP/IP biasa. Gunakan tombol Foto / Upload Barcode sebagai alternatif.</small>
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
                                                <th>Nomor Register</th>
                                                <th>Jenis Porsi</th>
                                            </tr>
                                            </thead>
                                        
                                        
                                        </table>
                                    </div>
                                </div>

                                
                            </div>

                            <div class="col-md-6 col-xs-12">
                                <div class="card">
                                    
                                    <div class="card-body">
                                        
                                      
                                        <div class="tab-content">
                                            <div class="row">
                                                <div class="col-lg-4 col-sm-12">
                                                    <div class="small-box bg-info">
                                                        <div class="inner">
                                                            <p>Total Scan</p>

                                                            <h1 style="text-align: center;"><span id="total_ompreng_keluar_id">
                                                                @if (isset($total_ompreng_keluar))
                                                                    {{$total_ompreng_keluar['total_ompreng_keluar_id']}}
                                                                @else
                                                                    0
                                                                    
                                                                @endif
                                                            </span> Pax
                                                            </h1>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="ion ion-bag"></i>
                                                        </div>
                                                        
                                                    </div>
                                                </div>

                                                <!-- small box -->
                                            <div class="col-lg-4 col-sm-12">
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
                                            <!--<div class="col-lg-4 col-sm-12">
                                                <div class="small-box bg-info">
                                                    <div class="inner">
                                                        <p>Total scan</p>

                                                        <h1 style="text-align: center;"><span id="total_ompreng_keluar">@if (isset($total_ompreng_keluar)){{$total_ompreng_keluar['total_ompreng_keluar']}}@endif</span> &nbsp;pax</h1>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="ion ion-bag"></i>
                                                    </div>
                                                    
                                                </div>
                                            </div>-->
                                            <!-- small box -->
                                            <!-- small box -->
                                            <div class="col-lg-4 col-sm-12">
                                                <div class="small-box bg-info">
                                                    <div class="inner">
                                                        <p>Line</p>

                                                        <h1 style="text-align: center;"><span id="user_id">
                                                            @if (isset($id_ip))
                                                                {{$id_ip}}
                                                            @else
                                                                0
                                                                
                                                            @endif
                                                        </span>
                                                        </h1>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="ion ion-bag"></i>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                            

                                            
                                             <!-- small box -->
                                            <!--<div class="col-lg-6 col-sm-12">
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
                                            </div>-->
                                            <!-- small box -->
                                             <!-- small box -->
                                            <!--<div class="col-lg-6 col-sm-12">
                                                <div class="small-box bg-info">
                                                    <div class="inner">
                                                        <p>Total scan porsi B</p>

                                                        <h1 style="text-align: center;"><span id="jumlah_ompreng_b">@if (isset($total_ompreng_keluar)){{$total_ompreng_keluar['jumlah_ompreng_porsi_b']}}@endif</span> &nbsp;pax</h1>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="ion ion-bag"></i>
                                                    </div>
                                                    
                                                </div>
                                            </div>-->
                                            <!-- small box -->
                                             
                                            </div>

                                        </div>
                                        <!-- /.tab-content -->
                                    </div><!-- /.card-body -->
                                    
                                    </div>
                                    <div class="card">
                                    
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
                                                <td>@if (isset($menu)){{$menu->menu}}
                                                @else
                                                    <span class="text-danger">Data menu hari ini tidak ditemukan</span>    
                                                @endif</td>
                                               
                                            </tr>
                                            <tr>
                                                <td>Jumlah</td>
                                                <td>:</td>
                                                <td>
                                                    @if (isset($jumlah_kirim))
                                                        {{$jumlah_kirim}}&nbsp;pax
                                                    @else
                                                        <span class="text-danger">Data menu hari ini tidak ditemukan</span>
                                                    @endif</td>
                                            </tr>
                                            <tr>
                                                <td>Tgl. Pengiriman &nbsp;</td>
                                                <td>:</td>
                                                <td>@if (isset($menu)){{ \Carbon\Carbon::parse($menu->tanggal_kirim)->locale('id')->translatedFormat('l, d F Y') }}
                                                @else
                                                    <span class="text-danger">Data menu hari ini tidak ditemukan</span>    
                                                @endif</td>
                                             
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
<!-- Loading Spinner (Hidden by Default) -->
    <div id="loading" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- REQUIRED SCRIPTS -->
  
    @include('Template.script')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
   
    <script type="text/javascript">
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 500,
            timerProgressBar: false
        });

        var table = $('#dt_ompreng_transaksi').DataTable({
            processing: false,  // Matikan processing indicator untuk speedup
            serverSide: false,
            searching: false,
            lengthChange: false,
            paging: false,      // Matikan paging karena hanya 10 record
            info: false,        // Matikan info "Showing x to y of z entries"
            ordering: false,    // Matikan ordering (sudah sorted dari backend)
            deferRender: true,  // Render DOM lebih cepat
            dom: 't',           // Hanya tampilkan table (tanpa kontrol tambahan)
            rowId: function(row) {
                return 'ompreng-' + String(row.kode_ompreng || '').replace(/[^A-Za-z0-9_-]/g, '_');
            },
            ajax: {
                url: '{{ route("packing.dt_formPacking", [], false) }}',
                type: 'GET',
                data: function(d) {
                    d.menu_id = $("#menu_id").val();
                    d.jenis_porsi = $("#jenis_porsi").text();
                    d.user_id = $("#user_id").text();
                },
                dataSrc: function(json) {
                    return (json && json.data) ? json.data : [];
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'kode_ompreng', name: 'kode_ompreng'},
                {data: 'nomor', name: 'nomor'},
                {data: 'porsi', name: 'porsi'}
            ]
        });
        var isProcessing = false; // Flag untuk mencegah multiple submission
        var html5QrCode = null;
        var isCameraScanning = false;
        var isShowingLoopNotifications = false;

        function delay(ms) {
            return new Promise(function(resolve) {
                setTimeout(resolve, ms);
            });
        }

        function generateSequentialOmprengCodes(baseKode, loopCount) {
            var match = String(baseKode || '').trim().match(/^(OP_(?:01_)?)(\d+)$/);
            if (!match) {
                return [];
            }

            var prefix = match[1];
            var rawNumber = match[2];
            var startNumber = parseInt(rawNumber, 10);
            var digitLength = rawNumber.length;
            var codes = [];

            for (var i = 1; i <= loopCount; i++) {
                var nextNumber = String(startNumber + i).padStart(digitLength, '0');
                codes.push(prefix + nextNumber);
            }

            return codes;
        }

        function applyScanSuccessToUi(response, showToastSuccess) {
            if (!response || !response.success) {
                return;
            }

            if (response.message == 'redirect') {
                window.location.href = response.url;
                return;
            }

            if (response.message == 'update_petugas') {
                $("#user_id").html(response.user_id);
                if (showToastSuccess) {
                    Toast.fire({
                        icon: 'success',
                        title: 'ID: ' + response.user_id
                    });
                }
                return;
            }

            if (typeof response.total_ompreng_keluar !== 'undefined' && response.total_ompreng_keluar !== null) {
                $("#total_ompreng_keluar").html(response.total_ompreng_keluar);
            }
            if (typeof response.jumlah_ompreng_porsi_a !== 'undefined' && response.jumlah_ompreng_porsi_a !== null) {
                $("#jumlah_ompreng_a").html(response.jumlah_ompreng_porsi_a);
            }
            if (typeof response.jumlah_ompreng_porsi_b !== 'undefined' && response.jumlah_ompreng_porsi_b !== null) {
                $("#jumlah_ompreng_b").html(response.jumlah_ompreng_porsi_b);
            }
            if (typeof response.total_ompreng_keluar_id !== 'undefined' && response.total_ompreng_keluar_id !== null) {
                $("#total_ompreng_keluar_id").html(response.total_ompreng_keluar_id);
            }

            if (response.row_data) {
                var rowId = 'ompreng-' + String(response.row_data.kode_ompreng || '').replace(/[^A-Za-z0-9_-]/g, '_');
                var currentRows = table.rows().data().toArray();

                currentRows = currentRows.filter(function(row) {
                    var existingId = 'ompreng-' + String(row.kode_ompreng || '').replace(/[^A-Za-z0-9_-]/g, '_');
                    return existingId !== rowId;
                });

                response.row_data.DT_RowIndex = 1;
                currentRows.unshift(response.row_data);
                currentRows = currentRows.slice(0, 10);

                currentRows.forEach(function(row, index) {
                    row.DT_RowIndex = index + 1;
                });

                table.clear();
                table.rows.add(currentRows).draw(false);
            }

            if (showToastSuccess) {
                Toast.fire({
                    icon: 'success',
                    title: '✓'
                });
            }
        }

        function sendScanRequest(kodeQRValue, extraData, showToastSuccess) {
            if (typeof showToastSuccess === 'undefined') {
                showToastSuccess = true;
            }

            kodeQRValue = (kodeQRValue || '').trim();
            if (!kodeQRValue || isProcessing) {
                return Promise.resolve(null);
            }

            isProcessing = true;
            $("#kodeQR").val('');

            var formData = new FormData(document.getElementById('form_scan_QR'));
            formData.append('tb_menu_id', $('#menu_id').val());
            formData.append('jenis_porsi', $('#jenis_porsi').text());
            formData.append('user_id', $('#user_id').text());
            formData.append('kodeQR', kodeQRValue);

            if (extraData && typeof extraData === 'object') {
                Object.keys(extraData).forEach(function(key) {
                    formData.append(key, extraData[key]);
                });
            }

            return new Promise(function(resolve) {
                $.ajax({
                    url: "{{ route('packing.ajax_scanQR', [], false) }}",
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(response) {
                        resetIdleTime();

                        if (response && response.success) {
                            applyScanSuccessToUi(response, showToastSuccess);
                        } else if (response) {
                            Toast.fire({
                                icon: 'error',
                                title: response.message,
                                timer: 1500
                            });
                        }

                        resolve(response || null);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        Toast.fire({
                            icon: 'error',
                            title: 'Error koneksi',
                            timer: 1500
                        });

                        resolve({
                            success: false,
                            message: 'Error koneksi'
                        });
                    },
                    complete: function() {
                        isProcessing = false;
                    }
                });
            });
        }

        function processScan(kodeQRValue) {
            if (isShowingLoopNotifications) {
                return false;
            }

            sendScanRequest(kodeQRValue, {}, true);
            return true;
        }

        async function runDebugScan(loopCount) {
            if (isShowingLoopNotifications) {
                Toast.fire({
                    icon: 'info',
                    title: 'Tunggu loop sebelumnya selesai',
                    timer: 1200
                });
                return;
            }

            var kodeQRValue = ($('#kodeQR').val() || '').trim();
            if (!kodeQRValue) {
                Toast.fire({
                    icon: 'error',
                    title: 'Isi barcode dulu',
                    timer: 1500
                });
                return;
            }

            var generatedCodes = generateSequentialOmprengCodes(kodeQRValue, loopCount);
            if (!generatedCodes.length) {
                Toast.fire({
                    icon: 'error',
                    title: 'Format harus OP_01_01000',
                    timer: 1500
                });
                return;
            }

            isShowingLoopNotifications = true;
            var failedCount = 0;
            var activeLine = ($('#user_id').text() || '').trim();

            try {
                for (var i = 0; i < generatedCodes.length; i++) {
                    var currentCode = generatedCodes[i];
                    var response = await sendScanRequest(currentCode, {
                        debug: 1,
                        line: activeLine
                    }, false);

                    var success = response && response.success;
                    var message = response && response.message ? response.message : 'Gagal scan';

                    if (!success) {
                        failedCount++;
                    }

                    Toast.fire({
                        icon: success ? 'success' : 'error',
                        title: (i + 1) + '/' + generatedCodes.length + ' - ' + message,
                        timer: 450
                    });

                    await delay(500);
                }
            } finally {
                isShowingLoopNotifications = false;
            }

            Toast.fire({
                icon: failedCount === 0 ? 'success' : 'warning',
                title: 'Loop selesai. Gagal: ' + failedCount,
                timer: 1800
            });
        }

        function setScanMode(mode) {
            if (mode === 'camera') {
                $('#usbScanSection').hide();
                $('#cameraScanSection').show();
                if (!window.isSecureContext) {
                    $('#cameraHint').show();
                } else {
                    $('#cameraHint').hide();
                }
            } else {
                $('#cameraScanSection').hide();
                $('#usbScanSection').show();
                $('#kodeQR').focus();
                stopCameraScanner();
            }
        }

        function stopCameraScanner() {
            if (html5QrCode && isCameraScanning) {
                html5QrCode.stop().then(function() {
                    isCameraScanning = false;
                    $('#qr-reader').hide().empty();
                    $('#stopCameraScan').prop('disabled', true);
                    $('#startCameraScan').prop('disabled', false);
                }).catch(function() {
                    isCameraScanning = false;
                    $('#qr-reader').hide().empty();
                    $('#stopCameraScan').prop('disabled', true);
                    $('#startCameraScan').prop('disabled', false);
                });
            } else {
                $('#qr-reader').hide().empty();
                $('#stopCameraScan').prop('disabled', true);
                $('#startCameraScan').prop('disabled', false);
            }
        }

        async function startCameraScanner() {
            if (typeof Html5Qrcode === 'undefined') {
                $('#cameraStatus').text('Fitur scan kamera belum termuat. Refresh halaman lalu coba lagi.');
                return;
            }

            if (!window.isSecureContext) {
                $('#cameraHint').show();
                $('#cameraStatus').text('Akses kamera web di HP butuh HTTPS atau localhost. Gunakan Foto / Upload Barcode bila halaman dibuka lewat IP HTTP.');
                return;
            }

            if (isCameraScanning) {
                return;
            }

            try {
                $('#qr-reader').show();
                html5QrCode = new Html5Qrcode('qr-reader');
                $('#cameraStatus').text('Kamera aktif. Arahkan ke barcode.');
                $('#startCameraScan').prop('disabled', true);
                $('#stopCameraScan').prop('disabled', false);

                html5QrCode.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    function(decodedText) {
                        $('#cameraStatus').text('Barcode terdeteksi: ' + decodedText);
                        processScan(decodedText);
                    },
                    function() {}
                ).then(function() {
                    isCameraScanning = true;
                }).catch(function() {
                    $('#startCameraScan').prop('disabled', false);
                    $('#stopCameraScan').prop('disabled', true);
                    $('#qr-reader').hide().empty();
                    $('#cameraStatus').text('Kamera tidak bisa diakses. Izinkan kamera di browser HP.');
                });
            } catch (error) {
                console.error(error);
                stopCameraScanner();
                $('#cameraStatus').text('Gagal mengaktifkan kamera. Pastikan izin kamera diberikan.');
            }
        }

        $('#barcodeImageInput').on('change', function(event) {
            const file = event.target.files && event.target.files[0];
            if (!file) {
                return;
            }

            if (typeof Html5Qrcode === 'undefined') {
                $('#cameraStatus').text('Fitur scan barcode belum termuat. Refresh halaman lalu coba lagi.');
                this.value = '';
                return;
            }

            stopCameraScanner();
            $('#cameraStatus').text('Memproses gambar barcode...');

            const imageScanner = new Html5Qrcode('qr-reader');
            $('#qr-reader').show();

            imageScanner.scanFile(file, true)
                .then(function(decodedText) {
                    $('#cameraStatus').text('Barcode terdeteksi dari gambar: ' + decodedText);
                    processScan(decodedText);
                    $('#qr-reader').hide().empty();
                })
                .catch(function(error) {
                    console.error(error);
                    $('#cameraStatus').text('Barcode pada foto tidak terbaca. Coba ambil foto lebih dekat dan lebih terang.');
                    $('#qr-reader').hide().empty();
                })
                .finally(() => {
                    $('#barcodeImageInput').val('');
                });
        });

        $('#kodeQR').on('keydown', function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                processScan($(this).val());
            }
        });

        // Safety net: cegah submit native ke URL halaman (intermiten pada scanner cepat)
        $('#form_scan_QR').on('submit', function(event) {
            event.preventDefault();
            processScan($('#kodeQR').val());
        });

        $('input[name="scan_mode"]').on('change', function() {
            setScanMode($(this).val());
        });

        $('#startCameraScan').on('click', function() {
            startCameraScanner();
        });

        $('#stopCameraScan').on('click', function() {
            stopCameraScanner();
            $('#cameraStatus').text('Kamera dihentikan.');
        });

        $('#runDebugLoop100').on('click', function() {
            runDebugScan(100);
        });

        $('#runDebugSingle').on('click', function() {
            runDebugScan(1);
        });

        
        window.onload = function() {
            const element = document.getElementById("head-scroll");
            $('#kodeQR').val('').focus();  // Fokuskan pada input kodeQR

            // Cek apakah ada elemen yang disimpan sebelumnya untuk difokuskan
            const focusedElement = localStorage.getItem('focusedElement');
            if (focusedElement) {
                $(focusedElement).focus();  // Fokuskan elemen yang disimpan
                localStorage.removeItem('focusedElement');  // Hapus informasi setelah digunakan
            }

            if (element) {
                element.scrollIntoView({ behavior: "smooth" });
            }
        };
        $(document).ready(function() {
            $('#kodeQR').focus();  // Fokuskan elemen saat halaman sudah selesai dimuat
            setScanMode($('input[name="scan_mode"]:checked').val());
        });
        $('#kodeQR').focus(function() {
            localStorage.setItem('focusedElement', '#kodeQR');
        });

        //auto refresh jika idle
        var idleTime = 0;  // Variabel untuk melacak waktu idle
        var idleTimeout = 300000;  // Waktu idle dalam milidetik (5 menit = 300000 ms)
        
        // Fungsi untuk mereset waktu idle saat ada aktivitas
        function resetIdleTime() {
            idleTime = 0;
        }

        setInterval(function() {
            idleTime++;
            keepAlive(); // panggil fungsi keep alive setiap 30 detik
            console.log(idleTime);
            if (idleTime > (idleTimeout / 60000)) { // Jika lebih dari 5 menit (300000ms)
                //alert("Tidak ada pemindaian QR dalam waktu yang lama, halaman akan di-refresh.");
                location.reload();  // Refresh halaman
            }
        }, 30000); // Cek setiap menit (60000 ms)

        //fungsi keep alive ajax
        function keepAlive() {
            $.ajax({
                url: "{{ route('packing.ajax_keepAlive', [], false) }}",
                type: 'GET',
                success: function(response) {
                    console.log(response.message);
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        }

        window.addEventListener('beforeunload', function() {
            stopCameraScanner();
        });
    </script>
    
    <!-- jQuery -->
</body>
</html>
`