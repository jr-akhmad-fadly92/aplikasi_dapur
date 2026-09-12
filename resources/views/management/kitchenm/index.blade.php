<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Kitchen</title>
    <link rel="stylesheet" href="{{ asset('AdminLte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/font-awesome/css/all.min.css')}}">
    <style>
        /* Custom Style */
        .card {
            margin-bottom: 20px;
        }

        .card-body {
            text-align: center;
        }

        .content {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 20px;
        }

        .filter-section {
            margin-bottom: 20px;
        }

        .table thead {
            background-color: #f8f9fa;
        }

        /* Responsiveness */
        @media (max-width: 768px) {
            .content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    @include('management.sidebar')
    @include('management.navbar')
    @include('management.header')

    <div class="card">
    <div class="d-flex">

<!-- Main Content -->
<div class="content">
    <!-- Kitchen Section -->
    <div class="container mt-4">
        <!-- Filter Section -->
        <div class="card filter-section">
            <div class="card-body">
                <h5 class="card-title">Filter Kitchen</h5>
                <form class="row g-3">
                    <div class="col-md-4">
                        <label for="provinsi" class="form-label">Provinsi</label>
                        <select id="provinsi" class="form-select">
                            <option selected>Choose...</option>
                            <option value="1">Jawa Barat</option>
                            <option value="2">Jawa Timur</option>
                            <option value="3">Bali</option>
                            <option value="4">Sumatera Utara</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="kota" class="form-label">Kota</label>
                        <select id="kota" class="form-select">
                            <option selected>Choose...</option>
                            <option value="1">Bandung</option>
                            <option value="2">Surabaya</option>
                            <option value="3">Denpasar</option>
                            <option value="4">Medan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="kabupaten" class="form-label">Kabupaten</label>
                        <select id="kabupaten" class="form-select">
                            <option selected>Choose...</option>
                            <option value="1">Cimahi</option>
                            <option value="2">Sidoarjo</option>
                            <option value="3">Badung</option>
                            <option value="4">Deli Serdang</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Data Button -->
        <div class="mb-3">
            <button class="btn btn-success">Tambah Data</button>
        </div>

        <!-- Kitchen Table -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">List Kitchen</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kitchen</th>
                            <th>Alamat</th>
                            <th>Provinsi-Kota</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Fake Data Rows -->
                        <tr>
                            <td>1</td>
                            <td>Kitchen A</td>
                            <td>Jawa Barat</td>
                            <td>Bandung</td>
                            <td>
                                <button class="btn btn-warning btn-sm">Edit</button>
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Kitchen B</td>
                            <td>Jawa Timur</td>
                            <td>Surabaya</td>
                            <td>
                                <button class="btn btn-warning btn-sm">Edit</button>
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Kitchen C</td>
                            <td>Bali</td>
                            <td>Denpasar</td>
                            <td>
                                <button class="btn btn-warning btn-sm">Edit</button>
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Kitchen D</td>
                            <td>Sumatera Utara</td>
                            <td>Medan</td>
                            <td>
                                <button class="btn btn-warning btn-sm">Edit</button>
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
