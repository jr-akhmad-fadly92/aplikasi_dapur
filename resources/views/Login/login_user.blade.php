<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Unit Pelayanan Makanan Bergizi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('AdminLte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="{{ asset('AdminLte/dist/css/adminlte.min.css') }}">

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
    </style>
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <img src="{{ asset('image\logo.jpg') }}" alt="Logo">
            <h2>UNIT PELAYANAN MAKANAN BERGIZI</h2>
        </div>
        <div class="card">
            <div class="card-body login-card-body">
                <form action="#" method="post">
                    {{ csrf_field() }}
                    <div class="mb-3">
                        <label>Id Kitchen</label>
                        <input type="id_kitchen" class="form-control" name="id_kitchen" placeholder="Kitchen" required>
                    </div>
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

    <!-- jQuery -->
    @include('Template.script')
</body>
</html>
