<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Unit Pelayanan Makanan Bergizi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('Template.head')
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('AdminLte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="{{ asset('AdminLte/dist/css/adminlte.min.css') }}">
    <!-- Google Font -->
    <!--link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet"-->
    
    <style>
        .login-box {
            width: 400px;
        }
        .login-logo img {
            max-width: 100px;
        }
        .login-logo h2 {
            font-size: 20px;
            font-weight: bold;
            color: red;
        }
        .btn-login {
            background-color: red;
            border-color: red;
        }
        .running-text {
            position: fixed;
            bottom: 10px;
            width: 100%;
            white-space: nowrap;
            overflow: hidden;
           
            color: rgb(10, 1, 1);
            padding: 5px;
            font-size: 16px;
        }

        .marquee {
            position: fixed;
            bottom: 10px;
            width: 100%;
            overflow: hidden;
            color: rgb(13, 13, 13);
            padding: 5px;
            font-size: 16px;
            white-space: nowrap;
        }

        .marquee-content {
            display: inline-block;
            padding-left: 100%;
            animation: scrollText 30s linear infinite;
        }

        @keyframes scrollText {
            from {
                transform: translateX(0%);
            }
            to {
                transform: translateX(-100%);
            }
        }


       

    </style>
    
    
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <img src="{{ asset('image/logo.png') }}" alt="Logo">
            <h2>UNIT PELAYANAN MAKANAN BERGIZI </h2>
            
            
        </div>
        <div class="card">
            <div class="card-body login-card-body">
                
                <form action="{{ route('postlogin') }}" method="post">
                    {{ csrf_field() }}
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn btn-login btn-block text-white">Login</button>
                </form>
                
            </div>
        </div>
    </div>
    <div class="marquee">
        <div class="marquee-content">
            {{ $data->pemilik }} | {{ $data->nama_dapur }} : {{ $data->alamat_dapur }} Kecamatan {{ $data->kecamatan }} , {{ $data->kota }} Provinsi {{ $data->provinsi }} , nomor Telp : {{ $data->no_telp }} |  
        </div>
    </div>
    <!-- jQuery -->
    @include('Template.script')
</body>
</html>
