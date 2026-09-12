<!-- dashboard.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>


@include('management.sidebar')
@include('management.navbar')
@include('management.header')
<div class="d-flex">
    <!-- Sidebar -->
    



    <!-- Main Content -->
    <div class="content" style="margin-left: 250px; width: calc(100% - 250px); padding: 20px;">
        <!-- Header -->

        <!-- Dashboard Content -->
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Kitchen</h5>
                            <p class="card-text">1</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Sekolah</h5>
                            <p class="card-text">32</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('management.footer')
@include('management.script')
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
</body>
</html>
