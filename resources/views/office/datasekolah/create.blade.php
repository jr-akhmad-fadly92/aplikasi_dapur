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
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                    <h1 class="m-0 text-dark" id="currentTime">Starter Page</h1>
                        
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
                                <h1 >{{ $header }}</h1>
                                <div class="col text-right">
                                    <button onclick="simpanDataSekolah()" class="btn btn-primary">Simpan</button>
                                    <a href="{{ url()->previous() }}" class="btn btn-default">Kembali</a>
                            </div>
                            <!-- /.card-header -->
                        
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                        <div class="row">
                            <div class="col-md-6 col-xs-12 offset-md-3 ">
                                <div class="card">
                                <div class="card-header d-flex p-0">
                                        <h3 class="card-title p-3">Data Sekolah</h3>
                                        
                                    </div>
                                    <form method="post" id="form_data_sekolah" enctype="multipart/form-data" class="col">
                                        @csrf
                                        <div class="card-body">                                          @if(isset($sekolah))
                                            <input type="hidden" name="id" value="{{ $sekolah->id }}">
                                        @endif
                                            <div class="form-group mb-3">
                                                <label class="font-weight-bold">Nama Sekolah</label>
                                                <input type="text" class="form-control @error('nama_sekolah') is-invalid @enderror" name="nama_sekolah" step="any" placeholder="Nama Sekolah" @if (isset($sekolah)) value="{{ $sekolah->nama_sekolah }}" @endif>
                                                    
                                                
                                                
                                            </div>

                                            <!-- Jenjang Sekolah -->
                                            <div class="form-group mb-3">
                                                <label class="font-weight-bold">Jenjang Sekolah</label>
                                                <select class="form-control" name="jenjang_sekolah">
                                                        <option value="KB/Sederajat" @if(isset($sekolah)) @if($sekolah->jenjang_sekolah == 'KB/Sederajat') selected @endif @endif>KB/Sederajat</option>
                                                        <option value="TK/Sederajat" @if (isset($sekolah)) @if($sekolah->jenjang_sekolah == 'TK/Sederajat') selected @endif @endif>TK/Sederajat</option>
                                                        <option value="SD/Sederajat" @if (isset($sekolah)) @if($sekolah->jenjang_sekolah == 'SD/Sederajat') selected @endif @endif>SD/Sederajat</option>   
                                                        <option value="SMP/Sederajat" @if (isset($sekolah)) @if($sekolah->jenjang_sekolah == 'SMP/Sederajat') selected @endif @endif>SMP/Sederajat</option>
                                                        <option value="SMA/Sederajat" @if (isset($sekolah)) @if($sekolah->jenjang_sekolah == 'SMA/Sederajat') selected @endif @endif>SMA/Sederajat</option>
                                                        <option value="SMK/Sederajat" @if (isset($sekolah)) @if($sekolah->jenjang_sekolah == 'SMK/Sederajat') selected @endif @endif>SMK/Sederajat</option>
                                                        <option value="Taruna" @if (isset($sekolah)) @if($sekolah->jenjang_sekolah == 'Taruna') selected @endif @endif>Taruna</option>
                                                        <option value="Bumil/Busui" @if (isset($sekolah)) @if($sekolah->jenjang_sekolah == 'Bumil/Busui') selected @endif @endif>Bumil/Busui</option>
                                                        <option value="Balita/Baduta" @if (isset($sekolah)) @if($sekolah->jenjang_sekolah == 'Balita/Baduta') selected @endif @endif>Balita/Baduta</option>
                                                </select>
                                                
                                            </div>

                                            <div class="form-group mb-3">
                                                <label class="font-weight-bold">Alamat Sekolah</label>
                                                <textarea type="text" class="form-control @error('alamat_sekolah') is-invalid @enderror" name="alamat_sekolah" step="any" placeholder="Alamat_ Sekolah">@if (isset($sekolah)){{ $sekolah->alamat_sekolah }} @endif</textarea>
                                                    
                                                
                                                
                                            </div>

                                            <div class="form-group mb-3">
                                                <label class="font-weight-bold">NPSN (Nomor Pokok Sekolah Nasional)</label>
                                                <input type="text" class="form-control @error('npsn') is-invalid @enderror" name="npsn" step="any" placeholder=" npsn" @if (isset($sekolah)) value="{{ $sekolah->npsn }}" @endif>
                                                    
                                                
                                                
                                            </div>

                                            <div class="form-group mb-3" style="text-align: right;">
                                                
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                
                            </div>

                            

                        </div>
                           
                        
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </section>
        </div>

        <!-- Footer -->
        @include('Template.footer')

    </div>

    <!-- Required Scripts -->
    @include('Template.script')
    <script>
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    function simpanDataSekolah(){
          var formData = new FormData(document.getElementById('form_data_sekolah'));
          
          $.ajax({
            type: 'post',
            url: "{{url('datasekolah/ajax_simpanDataSekolah')}}",
            data: formData,
            headers: {
              "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function(show){
                Toast.fire({
                    icon: show.type,
                    title: show.message
                })
                window.location.replace("/detailSekolah-"+show.id);
              
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
              Toast.fire({
                    icon: 'error',
                    title: message
                })
              
              //$(".loading").removeAttr('readonly','readonly');
            }
          })
        }
    </script>

</body>
</html>
