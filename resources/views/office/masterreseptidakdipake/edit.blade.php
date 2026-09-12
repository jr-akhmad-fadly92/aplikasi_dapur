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
                        <div class="card-body">
                            <form action="{{ route('masterresep.update', $Resep->id) }}" method="POST" enctype="multipart/form-data">
                        
                                @csrf
                                @method('PUT')

                                

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Nama Resep</label>
                                    <input type="text" class="form-control @error('resep') is-invalid @enderror" name="resep" value="{{ old('resep', $Resep->resep) }}" placeholder="resep">
                                
                                    <!-- error message untuk name -->
                                    @error('resep')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Karbohidrat</label>
                                            <select class="form-control" id="karbohidrat" name="karbohidrat">
                                                 @foreach ($karbohidrat as $data)
                                                 
                                                 <option value={{ $data->id }}
                                                    @if( $data->id == $Resep->karbohidrat) selected @endif
                                                    >{{ $data->nama_menu }} </option>
                                                 
                                                 @endforeach
                                            </select>
                                    <!-- error message untuk name -->
                                    @error('karbohidrat')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Protein</label>
                                            <select class="form-control" id="protein" name="protein">
                                                 @foreach ($protein as $data)
                                                 
                                                 <option value={{ $data->id }} 
                                                     @if( $data->id == $Resep->protein) selected @endif
                                                    >{{ $data->nama_menu }} </option>
                                                 
                                                 @endforeach
                                            </select>
                                    <!-- error message untuk name -->
                                    @error('protein')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Sayur</label>
                                            <select class="form-control" id="sayur" name="sayur">
                                                 @foreach ($sayur as $data)
                                                 
                                                 <option value={{ $data->id }} 
                                                    @if( $data->id == $Resep->sayur) selected @endif
                                                    >{{ $data->nama_menu }} </option>
                                                 
                                                 @endforeach
                                            </select>
                                    <!-- error message untuk name -->
                                    @error('sayur')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Buah</label>
                                            <select class="form-control" id="buah" name="buah">
                                                 @foreach ($buah as $data)
                                                 
                                                 <option value={{ $data->id }} 
                                                    @if( $data->id == $Resep->buah) selected @endif
                                                    >{{ $data->nama_menu }} </option>
                                                 
                                                 @endforeach
                                            </select>
                                    <!-- error message untuk name -->
                                    @error('buah')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Susu</label>
                                            <select class="form-control" id="susu" name="susu">
                                                 @foreach ($susu as $data)
                                                 
                                                 <option value={{ $data->id }} 
                                                    @if( $data->id == $Resep->susu) selected @endif
                                                    >{{ $data->nama_menu }} </option>
                                                 
                                                 @endforeach
                                            </select>
                                    <!-- error message untuk name -->
                                    @error('susu')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                
                                        
                                

                                <button type="submit" class="btn btn-md btn-primary me-3">UPDATE</button>
                                <button type="reset" class="btn btn-md btn-warning">RESET</button>

                            </form> 
                        </div>
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                       >
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
  
    @include('Template.script')
    
    <!-- jQuery -->
</body>
</html>
