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
                            <div class="row">
                                <div class="col-md-9 col-xs-12">
                                    <h3 class="card-title p-3">Form Surat Jalan</h3>
                                </div>
                                <div class="col-md-3 col-xs-12" style="text-align: right;">
                                    <button class="btn btn-sm btn-info" onclick="simpanSuratJalan()">Simpan</button>
                                    <button class="btn btn-sm btn-success" onclick="pubSuratJalan()">Publikasi</button>
                                    <button onclick="history.back()" class="btn btn-link btn-sm">Kembali</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="form_surat_jalan" method="post" action="{{ url('suratJalan/save') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 col-xs-12">

                                        <div class="form-group row">
                                            <label for="referensi" class="col-sm-3 col-xs-12 col-form-label">Referensi</label>
                                            <div class="col-sm-9 col-xs-12">
                                                <input type="text" class="form-control form-control-sm" id="referensi" name="referensi" placeholder="No referensi (otomatis)" readonly @if (isset($surat_jalan))
                                                value="{{$surat_jalan->referensi}}"

                                                @else
                                                value="{{$referensi}}"
                                                @endif>
                                            </div>
                                        </div>
                                        <!--end form-group-->

                                        <div class="form-group row">
                                            <label for="no_surat_jalan" class="col-sm-3 col-xs-12 col-form-label">No Surat Jalan</label>
                                            <div class="col-sm-9 col-xs-12">
                                                <input type="text" class="form-control form-control-sm" id="no_surat_jalan" name="no_surat_jalan" placeholder="No Surat Jalan (otomatis)" disabled>
                                            </div>
                                        </div>
                                        <!--end form-group-->

                                        <div class="form-group row">
                                            <label for="driver" class="col-sm-3 col-xs-12 col-form-label">Petugas pengirim</label>
                                            <div class="col-sm-9 col-xs-12">
                                                <input type="text" class="form-control form-control-sm" id="driver" name="driver" placeholder="Nama petugas pengiriman"
                                                @if (isset($surat_jalan))
                                                    value="{{$surat_jalan->driver}}"
                                                @endif>
                                            </div>
                                        </div>
                                        <!--end form-group-->

                                        <div class="form-group row">
                                            <label for="plat_nomor" class="col-sm-3 col-xs-12 col-form-label">Plat nomor</label>
                                            <div class="col-sm-9 col-xs-12">
                                                <input type="text" class="form-control form-control-sm" id="plat_nomor" name="plat_nomor" placeholder="Plat nomor kendaraan pengiriman"
                                                @if (isset($surat_jalan))
                                                    value="{{$surat_jalan->plat_nomor}}"
                                                @endif>
                                            </div>
                                        </div>
                                        <!--end form-group-->
                                    </div>
                                    <!--end col-->
                                    <div class="col-md-6 col-xs-12">
                                        @if(isset($menu) || isset($surat_jalan))
                                        <table class="table table-sm">
                                            <tr>
                                                <td>ID. Menu</td>
                                                <td>:&nbsp;</td>
                                                <td>
                                                    <!--<input type="text" class="form-control form-control-sm" readonly value="@if (isset($menu)){{$menu->id}}@endif" placeholder="Data menu hari ini tidak ditemukan " id="menu_id" name="menu_id">-->
                                                    <input type="text" id="menu_id" name="menu_id"
                                                    @if (isset($menu))
                                                        value="{{$menu->id}}"
                                                    
                                                        
                                                    @endif readonly  placeholder="Data menu hari ini tidak ditemukan " class="form-control form-control-sm" >
                                                </td>
                                               
                                            </tr>
                                            <tr>
                                                <td>Menu</td>
                                                <td>:&nbsp;</td>
                                                <td>@if (isset($menu)){{$menu->menu}}@endif</td>
                                               
                                            </tr>
                                            
                                            <tr>
                                                <td>Tgl. Pengiriman &nbsp;</td>
                                                <td>:</td>
                                                <td>@if (isset($menu)){{ \Carbon\Carbon::parse($menu->tanggal_kirim)->locale('id')->translatedFormat('l, d F Y') }}@endif</td>
                                             
                                            </tr>
                                        </table>
                                        @else
                                        <!--hanya testing-->
                                        <input type="text" id="menu_id" name="menu_id"
                                                        value="1"
                                                    
                                                        
                                                     readonly  placeholder="Data menu hari ini tidak ditemukan " class="form-control form-control-sm" >
                                        <!--end testing-->
                                        <h1 style="text-align: center; ">Tidak ada menu hari ini</h1>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- end card -->
                     
                    <div class="row">
                        <div class="col-md-6 col-xs-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-md-9 col-xs-12">
                                            <h3 class="card-title p-3">Data sekolah</h3>
                                        </div>
                                        <div class="col-md-3 col-xs-12" style="text-align: right;">
                                                <button class="btn btn-sm btn-info" onclick="formTransaksiBarangMasuk()">
                                                    Kirim
                                                </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="dt_sekolah" class="table table-bordered table-striped table-sm" cellspacing="0" style="width: 100%;">
                                        <thead>
                                        <tr>
                                            <th rowspan="2"><input type="checkbox" id="select-all"></th>
                                            <th rowspan="2">No</th>
                                            <th rowspan="2">Nama sekolah</th>
                                            <th rowspan="2">Alamat</th>
                                            <th colspan="2">Kekurangan Kirim</th>
                                            <th rowspan="2">Aksi</th>
                                        </tr>
                                        <tr>
                                            <th>Porsi A</th>
                                            <th>Porsi B</th>
                                        </tr>
                                        </thead>
                                    
                                    
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xs-12">
                            <div class="card">
                                <div class="card-header d-flex p-0">
                                    <h3 class="card-title p-3">Data pengiriman</h3>
                                    
                                </div><!-- /.card-header -->
                                <div class="card-body">
                                <table id="dt_surat_jalan_item" class="table table-bordered table-striped table-sm" cellspacing="0" style="width: 100%;">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th>No</th>
                                            <th>Nama sekolah</th>
                                            <th>Alamat</th>
                                            <th>Jumlah A</th>
                                            <th>Jumlah B</th>
                                            <th>Aksi</th>
                                        </tr>
                                        </thead>
                                    
                                    
                                    </table> 
                                    
                                    
                                </div><!-- /.card-body -->
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
        var selectedRows = new Set();
        var dt_sekolah = $('#dt_sekolah').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url("formSuratJalan/dt_dataRincianSekolah") }}',
                data: {
                    referensi: $("#referensi").val(),
                    menu_id: $("#menu_id").val()
                }
            },
            columns: [
                {data: 'id', name: 'id', orderable: false, searchable: false, render: function(data) {
                    return '<input type="checkbox" class="dt-checkboxes" value="'+data+'">';
                }},
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'nama_sekolah', name: 'nama_sekolah'},
                {data: 'alamat_sekolah', name: 'alamat_sekolah'},
                {data: 'sisa_jumlah_penerima_a', name: 'sisa_jumlah_penerima_a'},
                {data: 'sisa_jumlah_penerima_b', name: 'sisa_jumlah_penerima_b'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            
            responsive: true,
            stateSave: true,
            stateDuration: 60*30,
        
    
        });
        // ✅ Menyimpan ID yang dipilih
        $('#dt_sekolah tbody').on('change', '.dt-checkboxes', function() {
            var rowId = $(this).val();
            if (this.checked) {
                selectedRows.add(rowId);
            } else {
                selectedRows.delete(rowId);
            }
        });

        dt_sekolah.on('draw', function () {
            $('#dt_sekolah tbody .dt-checkboxes').each(function () {
                var rowId = $(this).val();
                $(this).prop('checked', selectedRows.has(rowId));
            });
        });

        // ✅ "Select All" Checkbox di Header
        $('#select-all').on('click', function(){
            var isChecked = this.checked;
            $('#dt_sekolah tbody .dt-checkboxes').each(function () {
                $(this).prop('checked', isChecked);
                var rowId = $(this).val();
                if (isChecked) {
                    selectedRows.add(rowId);
                } else {
                    selectedRows.delete(rowId);
                }
            });
        });


        function deselect_dtSekolah(){
            $('#dt_sekolah tbody .dt-checkboxes').each(function () {
                $(this).prop('checked', false);  // Hilangkan centang
                var rowId = $(this).val();
                selectedRows.delete(rowId);      // Hapus dari set
            });

            // Pastikan checkbox "Select All" di header juga tidak tercentang
            $('#select-all').prop('checked', false);
        }

        function simpanSuratJalan(){
            var formData = new FormData(document.getElementById('form_surat_jalan'));
            $.ajax({
                type: 'post',
                url: '{{url("/formSuratJalan/ajax_simpanSuratJalan")}}',
                data: formData,
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function(show){
                    //alert(JSON.stringify(show));
                    Toast.fire({
                        icon: show.status,
                        title: show.message
                    });
                    //$("#kode_wadah_masuk").val('');
                    
                    
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


                }
            })
        }

        function simpanSuratJalanItem() {
            simpanSuratJalan();
            var formData = $('#form_surat_jalan_item').serializeArray(); // Ambil data dari form
            formData.push({ name: "referensi", value: $('#referensi').val() }); 
            $.ajax({
                url: '{{ url("/formSuratJalan/ajax_simpanSuratJalanItem") }}',
                method: 'POST',
                data: formData,
                success: function(show) {
                    //alert(JSON.stringify(show));

                    //alert(response.message);
                    Toast.fire({
                        icon: show.status,
                        title: show.message
                    })
                    deselect_dtSekolah();
                    dt_sekolah.ajax.reload();
                    dt_surat_jalan_item.ajax.reload();
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
                    deselect_dtSekolah();
                    dt_sekolah.ajax.reload();
                    dt_surat_jalan_item.ajax.reload();
                    //$("#kode_wadah_masuk").val('');


                }
            });
        }

        function formTransaksiBarangMasuk(id){
            //var id_rincian_sekolah = Array.from(selectedRows).join(',');
            var id_rincian_sekolah = id ? id : Array.from(selectedRows).join(',');
            var referensi = $("#referensi").val();
            $.confirm({
                title: 'Edit jumlah barang dikirim',
                columnClass: 'col-md-6 col-md-offset-3 col-xs-12 col-sm-12',
                content: 'url:{{url("/formSuratJalan/form_suratJalanItem")}}?id_rincian_sekolah='+id_rincian_sekolah+'&referensi='+referensi,
                type: 'blue',
                buttons:{
                
                simpan:{
                    text: "<i class='fa fa-floppy-o'></i> Simpan",
                    btnClass: 'btn-success',
                    action: function(){
                        simpanSuratJalanItem();
                    }
                },
                batal: function(){},
                }
            });
        }
        

        var dt_surat_jalan_item = $('#dt_surat_jalan_item').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url("formSuratJalan/dt_suratJalanItem") }}',
                data: {
                    referensi: $("#referensi").val(),
                    menu_id: $("#menu_id").text()
                }
            },
            columns: [
                {data: 'id', name: 'id', orderable: false, searchable: false, render: function(data) {
                    return '<input type="checkbox" class="dt-checkboxes" value="'+data+'">';
                }},
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'nama_sekolah', name: 'nama_sekolah'},
                {data: 'alamat_sekolah', name: 'alamat_sekolah'},
                {data: 'jumlah_a', name: 'jumlah_a'},
                {data: 'jumlah_b', name: 'jumlah_b'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            
            responsive: true,
            stateSave: true,
            stateDuration: 60*30,
        
    
        });

        function pubSuratJalan(){
            $.confirm({
            title: 'Publikasi Surat Jalan',
            content: 'Surat yang sudah dipublikasi tidak bisa diedit. Ingin publikasi?',
            //columnClass: 'col-md-8 col-md-offset-2 col-xs-6',
            type: 'green',
            buttons: {
              publikasi: {
                text: 'Publikasi',
                btnClass: 'btn btn-sm btn-dark',
                action: function(){
                  //save first
                  //simpanPoPurchase();
                  
                  var formData = new FormData(document.getElementById('form_surat_jalan'));
                  $.ajax({
                    type: 'post',
                    data: formData,
                    dataType: 'json',
                    url: "{{url('formSuratJalan/ajax_pubSuratJalan')}}",
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(show){
                        //alert(show);
                      //$.alert(JSON.stringify(show));
                      //PNotify.removeAll();
                      Toast.fire({
                        icon: show.status,
                        title: show.message
                      })
                      if(show.status == 'success'){
                        window.location.replace("/suratJalan/detailSuratJalan-"+show.id);
                      }
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
                    }
                  })
                }
              },
              kembali: function(){}
            }
          });
        }

        function deleteSuratJalanItem(id){
          
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
                    url: '/formSuratJalan/ajax_deleteSuratJalanItem',
                    data: {id: id},
                    headers: {
                      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    },
                    dataType: 'json',
                    success: function(show){
                      //alert(JSON.stringify(show));
                      //PNotify.removeAll();
                        Toast.fire({
                            icon: show.status,
                            title: show.message
                        })
                        dt_sekolah.ajax.reload(null, false);
                        dt_surat_jalan_item.ajax.reload(null, false);
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
                      dt_sekolah.ajax.reload(null, false);
                      dt_surat_jalan_item.ajax.reload(null, false);
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
