<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="{{ asset('AdminLte/plugins/fontawesome-free/css/all.min.css') }}">
<!-- Theme style -->
<link rel="stylesheet" href="{{ asset('AdminLte/dist/css/adminlte.min.css') }}">
<!-- selected style -->
<link rel="stylesheet" href="{{ asset('AdminLte/plugins/select2/css/select2.min.css') }}">
<!-- Google Font: Source Sans Pro -->
<!--link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet"-->
<!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('AdminLte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('AdminLte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('AdminLte/plugins/datatables-select/css/select.bootstrap4.min.css') }}">
  
  <!-- jQuery Confirm -->
  <link rel="stylesheet" href="{{ asset('vendors/jquery-confirm/jquery-confirm.min.css') }}">
  
  <!--jquery orgchart-->
  <link rel="stylesheet" href="{{ asset('vendors/OrgChart-master/jquery.orgchart.css') }}">

  <!--boostrap switch-->
  <link rel="stylesheet" href="{{ asset('AdminLte/plugins/bootstrap-switch/css/bootstrap3/bootstrap-switch.min.css') }}">

  <link rel="stylesheet" href="{{ asset('AdminLte/plugins/sweetalert2/sweetalert2.min.css') }}">

  <!-- daterange picker -->
  <link rel="stylesheet" href="{{ asset('AdminLte/plugins/daterangepicker/daterangepicker.css') }}">
<style>
  .bg-soft-primary {
      background-color: #FFD580; /* Warna orange muda */
  }

  .bg-primary-edit{
      background-color: #FFD580; /* Warna orange muda */
  } 
</style>
<style>
        .form-control {
            height: 40px; /* Sesuaikan tinggi */
            width: 100%; /* Pastikan width full */
        }
        
        .select2-container .select2-selection--single {
            height: 40px !important; /* Samakan dengan input lainnya */
            padding: 5px;
            display: flex;
            align-items: center;
        }
        
        .select2-selection__rendered {
            line-height: 30px !important;
        }
    </style>
