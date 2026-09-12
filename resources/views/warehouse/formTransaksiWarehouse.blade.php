<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
    <title>{{ $header }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />

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
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title p-3">Form Surat Jalan</h4>
                        </div>
                        <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 col-xs-12">

                                        <div class="form-group row">
                                            <label for="referensi" class="col-sm-3 col-xs-12 col-form-label">Referensi</label>
                                            <div class="col-sm-9 col-xs-12">
                                                <input type="text" class="form-control form-control-sm" id="referensi" name="referensi" placeholder="No referensi (otomatis)" disabled value="{{$referensi}}">
                                            </div>
                                        </div>
                                        <!--end form-group-->

                                        <div class="form-group row">
                                            <label for="jenis_transaksi" class="col-sm-3 col-xs-12 col-form-label">Transaksi</label>
                                            <div class="col-sm-9 col-xs-12">
                                                <input type="text" class="form-control form-control-sm" id="jenis_transaksi" name="jenis_transaksi" placeholder="jenis transaksi masuk/keluar" disabled value="@if (isset($jenis_transaksi)){{$jenis_transaksi}}
                                                @endif">
                                            </div>
                                        </div>
                                        <!--end form-group-->

                                    </div>
                                    <!--end col-->
                                    
                                </div>
                        </div>
                    </div>
                    
                    <!-- end card -->
                     <div class="card">
                        <div class="card-body">
                            <div class="row">
                                @if ($jenis_transaksi == 'in')
                                    
                                <div class="col-md-6 col-sm-6 col-12">
                                    <div class="form-group">
                                        <input type="text" name="kode_wadah_masuk" id="kode_wadah_masuk" class="form-control form-control-sm" placeholder="masukkan kode tag/tempat penyimpanan jika ada. jika tidak ada klik tambah transaksi" autofocus>

                                    </div>
                                    <!-- /.info-box -->
                                </div>
                                
                                <div class="col-md-4 col-sm-6 col-12">
                                    <div class="form-group">

                                        <button class="btn btn-success btn-sm" onclick="formTransaksiBarangMasuk();"><i class="fa fa-plus-circle"></i> Tambah Transaksi</button>


                                        <button class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> Tambah Transaksi</button>
                                            <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
                                                <i class="fa fa-arrow-left"></i> Kembali
                                            </a>    

                                    </div>
                                </div>
                                @else
                                <div class="col-md-6 col-sm-6 col-12">
                                    <div class="form-group">
                                        <input type="text" name="kode_wadah_keluar" id="kode_wadah_keluar" class="form-control form-control-sm" placeholder="masukkan kode tag/tempat penyimpanan jika ada. jika tidak ada klik tambah transaksi" autofocus>

                                    </div>
                                    <!-- /.info-box -->
                                </div>
                                <div class="col-md-4 col-sm-6 col-12">
                                    <div class="form-group">
                                        <button class="btn btn-primary btn-sm" type="button" onclick="submitTransaksiKeluar();"><i class="fa fa-share"></i> Keluarkan</button>
                                        <button class="btn btn-danger btn-sm" type="button" onclick="submitTransaksiKeluarSemua();"><i class="fa fa-share-square"></i> Keluarkan Semua</button>
                                        <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
                                            <i class="fa fa-arrow-left"></i> Kembali
                                        </a>
                                    </div>
                                </div>
                                @endif

                                
                                <!-- /.col -->
                            </div>
                        </div>
                     </div>
                     
                    @if ($jenis_transaksi == 'out')
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <div class="card">
                                <div class="card-header d-flex p-0">
                                    <h3 class="card-title p-3">Data barang di Gudang
                                        
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <table id="dt_warehouse" class="table table-bordered table-striped" cellspacing="0" style="width: 100%;">
                                        <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Wadah</th>
                                            <th>Nama barang</th>
                                            <th>Jadwal keluar</th>
                                            <th>Jumlah</th>
                                            <th>Satuan</th>
                                            <th>Lokasi</th>
                                            <th>Aksi</th>
                                        </tr>
                                        </thead>
                                    
                                    
                                    </table>
                                </div>
                            </div>
                        </div>

                        
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <div class="card">
                                <div class="card-header d-flex p-0">
                                    <h3 class="card-title p-3">Data barang {{ $jenis_transaksi == 'in' ? 'Masuk' : 'Keluar' }}
                                        
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <table id="dt_transaksi_warehouse" class="table table-bordered table-striped" cellspacing="0" style="width: 100%;">
                                        <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama barang</th>
                                            <th>Tgl. Transaksi</th>
                                            <th>Jadwal keluar</th>
                                            <th>Jumlah</th>
                                            <th>Satuan</th>
                                            <th>Aksi</th>
                                        </tr>
                                        </thead>
                                    
                                    
                                    </table>
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
        var satuanOptions = @json($satuan->map(function ($item) {
            return ['id' => $item->id, 'satuan' => $item->satuan];
        })->values());

        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });

        var table = $('#dt_transaksi_warehouse').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url("form_warehouse/dt_transaksiWarehouse") }}',
                data: {
                    referensi: $("#referensi").val(),
                    jenis_transaksi: $("#jenis_transaksi").val()
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'nama_barang', name: 'nama_barang'},
                {data: 'tanggal_transaksi', name: 'tanggal_transaksi'},
                {data: 'tanggal_akan_keluar', name: 'tanggal_akan_keluar'},
                {data: 'jumlah', name: 'jumlah'},
                {data: 'nama_satuan', name: 'nama_satuan'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        
    
        });

        var tableWarehouse = $('#dt_warehouse').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url("form_warehouse/dt_warehouse") }}',
                
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'kode_wadah', name: 'kode_wadah'},
                {data: 'nama_barang', name: 'nama_barang'},
                {data: 'tanggal_akan_keluar', name: 'tanggal_akan_keluar'},
                {data: 'jumlah', name: 'jumlah'},
                {data: 'nama_satuan', name: 'nama_satuan'},
                {data: 'lokasi', name: 'lokasi'},
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        if (data && data.length) {
                            return data;
                        }
                        return '<button onClick="formTransaksiKeluarById(' + (row.id || 0) + ')" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i> Keluarkan</button>';
                    }
                },
            ]
        });

        function ajax_updateSatuanGudang(id, currentSatuanId){
            var optionsHtml = satuanOptions.map(function(item) {
                var selected = Number(item.id) === Number(currentSatuanId) ? 'selected' : '';
                return '<option value="' + item.id + '" ' + selected + '>' + item.satuan + '</option>';
            }).join('');

            $.confirm({
                title: 'Ubah Satuan Barang',
                content: '' +
                    '<form action="" class="formName">' +
                    '<div class="form-group">' +
                    '<label>Pilih satuan baru</label>' +
                    '<select class="id_satuan_baru form-control" required>' + optionsHtml + '</select>' +
                    '<small>Perubahan ini hanya mengubah satuan item stok terpilih. Jumlah tidak otomatis dikonversi.</small>' +
                    '</div>' +
                    '</form>',
                onOpen: function () {
                    var $dlg = this;
                    $dlg.$content.find('.id_satuan_baru').select2({
                        width: '100%',
                        dropdownParent: $dlg.$body
                    });
                },
                buttons: {
                    simpan: {
                        text: 'Simpan',
                        btnClass: 'btn-primary',
                        action: function () {
                            var idSatuanBaru = this.$content.find('.id_satuan_baru').val();
                            if (!idSatuanBaru) {
                                $.alert('Satuan baru wajib dipilih');
                                return false;
                            }

                            $.ajax({
                                type: 'post',
                                url: '{{url("/form_warehouse/ajax_updateSatuanGudang")}}',
                                data: {
                                    id: id,
                                    id_satuan: idSatuanBaru
                                },
                                headers: {
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                                },
                                dataType: 'json',
                                success: function(show){
                                    Toast.fire({
                                        type: show.status,
                                        title: show.message
                                    });
                                    tableWarehouse.ajax.reload();
                                },
                                error: function(jqXHR){
                                    $.alert({
                                        title: 'Error',
                                        content: jqXHR.responseText,
                                        type: 'red',
                                        backgroundDismiss: true,
                                        columnClass: 'col-xs-10 col-xs-offset-1 col-md-8 col-md-offset-2'
                                    });
                                }
                            });
                        }
                    },
                    batal: function () {}
                }
            });
        }
        
            
        function formTransaksiBarangMasuk(kode_wadah){
            $.confirm({
                title: 'Input Transaksi Barang Masuk',
                columnClass: 'col-md-6 col-md-offset-3 col-xs-12 col-sm-12',
                content: 'url:{{url("/form_warehouse/form_barangTransaksiMasuk")}}?kode_wadah='+kode_wadah,
                type: 'blue',
                buttons:{
                simpanBaru:{
                    text: "<i class='fa fa-floppy-o'></i> Simpan dan input baru",
                    btnClass: 'btn-success',
                    action: function(){
                        var formData = new FormData(document.getElementById('form_transaksi_barang_masuk'));
                        formData.append('referensi_masuk', $("#referensi").val());
                            $.ajax({
                                type: 'post',
                                url: '{{url("/form_warehouse/ajax_simpanBarangTransaksiMasuk")}}',
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
                                    table.ajax.reload();
                                    //$("#kode_wadah_masuk").val('');
                                    formTransaksiBarangMasuk(kode_wadah);
                                    
                                    
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
                                    table.ajax.reload();
                                    $("#kode_wadah_masuk").val('');


                                }
                            })
                    }
                },
                simpanTutup:{
                    text: "<i class='fa fa-floppy-o'></i> Simpan dan tutup",
                    btnClass: 'btn-success',
                    action: function(){
                        var formData = new FormData(document.getElementById('form_transaksi_barang_masuk'));
                        formData.append('referensi_masuk', $("#referensi").val());
                            $.ajax({
                                type: 'post',
                                url: '{{url("/form_warehouse/ajax_simpanBarangTransaksiMasuk")}}',
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
                                    table.ajax.reload();
                                    $("#kode_wadah_masuk").val('');

                                    
                                    
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
                                    table.ajax.reload();
                                    $("#kode_wadah_masuk").val('');


                                }
                            })
                    }
                },
                batal: function(){
                    
                    $("#kode_wadah_masuk").val('');

                },
                }
            });
        }
        $('#kode_wadah_masuk').keypress(function(event) {
            
            var kode_wadah = $("#kode_wadah_masuk").val();
            
            if (event.keyCode === 13) { // Enter key
                formTransaksiBarangMasuk(kode_wadah);
                event.preventDefault();
                //alert(formData);
                
            }
        });

        $('#kode_wadah_keluar').keypress(function(event) {
            
            //var kode_wadah = $("#kode_wadah_keluar").val();
            
            if (event.keyCode === 13) { // Enter key
                //formTransaksiBarangMasuk(kode_wadah);
                event.preventDefault();
                submitTransaksiKeluar();
                
            }
        });

        function submitTransaksiKeluar(idWarehouse = null) {
            $.ajax({
                type: 'post',
                url: '{{url("/form_warehouse/ajax_simpanBarangTransaksiKeluar")}}',
                data: {
                    referensi_keluar: $("#referensi").val(),
                    kode_wadah: $("#kode_wadah_keluar").val(),
                    id_warehouse: idWarehouse,
                },
                headers: {
                  "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                dataType: 'json',
                cache: false,
                success: function(show){
                    Toast.fire({
                        type: show.type,
                        title: show.message
                    });
                    table.ajax.reload();
                    tableWarehouse.ajax.reload();
                    $("#kode_wadah_keluar").val('');
                },
                error: function(jqXHR){
                    $.alert({
                        title: 'Error',
                        content: jqXHR.responseText,
                        type: 'red',
                        backgroundDismiss: true,
                        columnClass: 'col-xs-10 col-xs-offset-1 col-md-8 col-md-offset-2'
                    });
                    $("#kode_wadah_keluar").val('');
                    table.ajax.reload();
                    tableWarehouse.ajax.reload();
                }
            });
        }

        function formTransaksiKeluarById(idWarehouse){
            submitTransaksiKeluar(idWarehouse);
        }

        function ajax_kembalikanBarangKeluar(id){
            $.confirm({
                title: 'Konfirmasi',
                content: 'Yakin ingin mengembalikan data keluar ini ke gudang?',
                type: 'orange',
                buttons: {
                    ya: {
                        text: 'Ya, Kembalikan',
                        btnClass: 'btn-warning',
                        action: function(){
                            $.ajax({
                                type: 'post',
                                url: '{{url("/form_warehouse/ajax_kembalikanBarangTransaksiKeluar")}}',
                                data: { id: id },
                                headers: {
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                                },
                                dataType: 'json',
                                success: function(show){
                                    Toast.fire({
                                        type: show.status,
                                        title: show.message
                                    });
                                    table.ajax.reload();
                                    tableWarehouse.ajax.reload();
                                },
                                error: function(jqXHR){
                                    $.alert({
                                        title: 'Error',
                                        content: jqXHR.responseText,
                                        type: 'red',
                                        backgroundDismiss: true,
                                        columnClass: 'col-xs-10 col-xs-offset-1 col-md-8 col-md-offset-2'
                                    });
                                }
                            });
                        }
                    },
                    batal: function(){}
                }
            });
        }

        function ajax_koreksiJumlahKeluar(id, jumlahLama){
            $.confirm({
                title: 'Koreksi Jumlah Keluar',
                content: '' +
                    '<form action="" class="formName">' +
                    '<div class="form-group">' +
                    '<label>Jumlah keluar lama: <b>' + jumlahLama + '</b></label>' +
                    '<input type="number" min="1" max="' + jumlahLama + '" placeholder="Masukkan jumlah keluar baru" class="jumlah_keluar_baru form-control" required />' +
                    '<small>Sisa akan otomatis dikembalikan ke stok gudang.</small>' +
                    '</div>' +
                    '</form>',
                buttons: {
                    simpan: {
                        text: 'Simpan Koreksi',
                        btnClass: 'btn-primary',
                        action: function () {
                            var jumlahBaru = this.$content.find('.jumlah_keluar_baru').val();
                            if(!jumlahBaru){
                                $.alert('Jumlah keluar baru wajib diisi');
                                return false;
                            }

                            $.ajax({
                                type: 'post',
                                url: '{{url("/form_warehouse/ajax_koreksiJumlahKeluar")}}',
                                data: {
                                    id: id,
                                    jumlah_keluar_baru: jumlahBaru
                                },
                                headers: {
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                                },
                                dataType: 'json',
                                success: function(show){
                                    Toast.fire({
                                        type: show.status,
                                        title: show.message
                                    });
                                    table.ajax.reload();
                                    tableWarehouse.ajax.reload();
                                },
                                error: function(jqXHR){
                                    $.alert({
                                        title: 'Error',
                                        content: jqXHR.responseText,
                                        type: 'red',
                                        backgroundDismiss: true,
                                        columnClass: 'col-xs-10 col-xs-offset-1 col-md-8 col-md-offset-2'
                                    });
                                }
                            });
                        }
                    },
                    batal: function () {}
                }
            });
        }

        function submitTransaksiKeluarSemua() {
            $.confirm({
                title: 'Konfirmasi',
                content: 'Yakin ingin mengeluarkan semua bahan baku yang masih aktif di gudang?',
                type: 'orange',
                buttons: {
                    ya: {
                        text: 'Ya, Keluarkan Semua',
                        btnClass: 'btn-danger',
                        action: function () {
                            $.ajax({
                                type: 'post',
                                url: '{{url("/form_warehouse/ajax_simpanBarangTransaksiKeluarSemua")}}',
                                data: {
                                    referensi_keluar: $("#referensi").val(),
                                },
                                headers: {
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                                },
                                dataType: 'json',
                                cache: false,
                                success: function(show){
                                    Toast.fire({
                                        type: show.status,
                                        title: show.message
                                    });
                                    table.ajax.reload();
                                    tableWarehouse.ajax.reload();
                                },
                                error: function(jqXHR){
                                    $.alert({
                                        title: 'Error',
                                        content: jqXHR.responseText,
                                        type: 'red',
                                        backgroundDismiss: true,
                                        columnClass: 'col-xs-10 col-xs-offset-1 col-md-8 col-md-offset-2'
                                    });
                                }
                            });
                        }
                    },
                    batal: function () {}
                }
            });
        }

        function ajax_hapusBarangTransakiWarehouse(id){
          
          $.confirm({
            theme: 'dark',
            type: 'red',
            title: 'Hapus Data',
            content: 'Anda yakin ingin menghapus data?',
            buttons: {
              hapus:{
                text: 'Hapus',
                btnClass: 'btn btn-danger btn-sm',
                action:function(){
                  
                  $.ajax({
                    type: 'post',
                    url: '/form_warehouse/ajax_hapusBarangTransaksiMasuk',
                    data: {id: id},
                    headers: {
                      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    },
                    dataType: 'json',
                    success: function(show){
                      //alert(JSON.stringify(show));
                      //PNotify.removeAll();
                      Toast.fire({
                            type: show.type,
                            title: show.message
                        });
                      table.ajax.reload(null, false);
                    }, 
                    error: function(jqXHR, exception){
                      table.ajax.reload(null, false);
                      $.alert({
                        title: 'Error',
                        content: jqXHR.responseText,
                        type: 'red',
                        backgroundDismiss: true,
                        columnClass: 'col-xs-10 col-xs-offset-1 col-md-8 col-md-offset-2'
                      });
                      //$(".loading").removeAttr('readonly','readonly');
                    }
                  })
                }
              },
              batal:function(){}
            }
          })
        }
            
    </script>
    
    <!-- jQuery -->
</body>
</html>
