<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
	<title>{{ $header }}</title>
	@include('Template.head')
	<meta name="csrf-token" content="{{ csrf_token() }}">
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
						   <form action="{{ route('rekap_menu_PDF') }}" method="GET" class="form-inline mb-3">
						   <div class="form-group mb-2 mr-2">
								<input type="text" name="search_menu" id="search_menu" class="form-control form-control-sm" placeholder="Cari menu...">
							</div> 
						   <div class="form-group mb-2 mr-2">
							  <label for="tanggal_awal" class="mr-2">Tanggal Awal</label>
							  <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control" required>
							</div>
                          
							<div class="form-group mb-2 mr-2">
							  <label for="tanggal_akhir" class="mr-2">Tanggal Akhir</label>
							  <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" required>
							</div>
							<div class="form-group mb-2 mr-2">
								<button type="submit" class="btn btn-primary mb-2">Download Rekap Pdf</button>
							</div>
							<div class="form-group mb-2 mr-2">
								<button type="submit" formaction="{{ url('/Laporan/rekap_menu/download/excel') }}" class="btn btn-success mb-2">Download Rekap Excel</button>
							</div>
							<div class="form-group mb-2 mr-2">
								<a href="#" id="btn-cetak" class="btn btn-success mb-2">Cetak Data</a>
							</div>
                            
							<div class="form-group mb-2 mr-2">
								<button type="button" id="btn-filter" class="btn btn-info mb-2">Filter</button>
							</div>
							</form>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							@if (auth()->check() && in_array(auth()->user()->level, ["backoffice", "admin"]))
							<h3 class="card-title"><a href="{{ route('menu_yayasan.create') }}" class="edit btn btn-primary btn-sm " id="btn-edit-post">Tambah</a></h3>
							@endif
							<table id="tbl_list_master_menu" class="table table-bordered table-hover" style="width: 100%">
							<thead>
								<tr>
									<th>No</th>
									<!--th>Menu</th-->
									<th>Tanggal</th>
									<th>Karbo</th>
									<th>Lauk</th>
									<th>Sayur</th>
									<th>Buah</th>
									<th>Penunjang</th>
									<th>Total Penerima</th>
									<!--th>Status</th-->
									<th>action</th>
								</tr>
							</thead>
							<tbody>

							</tbody>
							</table>
						</div>
						<!-- /.card-body -->
						</div>
						<!-- /.card -->
                        
                        
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
	<!-- Modal -->
	<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<form id="cancelForm">
			@csrf
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Konfirmasi Pembatalan</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					Apakah Anda yakin ingin membatalkan menu ini?
				</div>
				<div class="modal-footer">
					<input type="hidden" name="cancel_id" id="cancel_id">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
					<button type="submit" class="btn btn-warning">Ya, Batalkan</button>
				</div>
			</div>
		</form>
	</div>
	</div>
	<!-- Modal Konfirmasi ACC Manual -->
	<div class="modal fade" id="accModal" tabindex="-1" aria-labelledby="accModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="accModalLabel">Form ACC Menu</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		</div>
		<div class="modal-body">
			<form id="accForm">
			@csrf
			<div class="mb-3">
				<label for="nama_acc" class="form-label">Nama ACC</label>
				<input type="text" class="form-control" id="nama_acc" name="nama_acc" required>
			</div>
			<div class="mb-3">
				<label for="tanggal_acc" class="form-label">Tanggal ACC</label>
				<input type="date" class="form-control" id="tanggal_acc" name="tanggal_acc" required>
			</div>
			</form>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
			<button type="button" class="btn btn-success" id="confirmAccBtn">Simpan ACC</button>
		</div>
		</div>
	</div>
	</div>
    
	<!-- REQUIRED SCRIPTS -->
  
	@include('Template.script')
   
	<script type="text/javascript">
	let table;
	$(document).ready(function () {
		table = $('#tbl_list_master_menu').DataTable({
            
			ajax: {
				url: '{{ url()->current() }}',
				data: function(d) {
					d.search_menu = $('#search_menu').val();
					d.tanggal_awal = $('#tanggal_awal').val();
					d.tanggal_akhir = $('#tanggal_akhir').val();
				}
			},
			columns: [
				 { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
				//{ data: 'menu', name: 'menu' },
				{ data: 'tanggal_masak', name: 'tanggal_masak' },
				{ data: 'nama_karbohidrat', name: 'nama_karbohidrat' },
				{ data: 'nama_protein', name: 'nama_protein' },
				{ data: 'nama_sayur', name: 'nama_sayur' },
				{ data: 'nama_buah', name: 'nama_buah' },
				{ data: 'nama_susu', name: 'nama_susu' },
				{ data: 'jumlah_porsi', name: 'jumlah_porsi' },
				//{ data: 'status_pengajuan', name: 'status_pengajuan' },
				{data: 'action', name: 'action', orderable: false, searchable: false}, // Aksi (tombol)


			]
		});
        
		// Filter button handler
		$('#btn-filter').click(function() {
			table.ajax.reload();
		});
	});
	</script>
	 <script src="{{ asset('AdminLte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
	 <script>
		//message with sweetalert
		@if(session('success'))
			Swal.fire({
				icon: "success",
				title: "BERHASIL",
				text: "{{ session('success') }}",
				showConfirmButton: false,
				timer: 2000
			});
		@elseif(session('error'))
			Swal.fire({
				icon: "error",
				title: "GAGAL!",
				text: "{{ session('error') }}",
				showConfirmButton: false,
				timer: 2000
			});
		@endif
            
	</script>
	<script>
	$(document).ready(function() {
		// Setup CSRF Token for AJAX
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		// Open Modal
		$(document).on('click', '.cancel-button', function () {
			var id = $(this).data('id');
			$('#cancel_id').val(id);
			$('#cancelModal').modal('show');
		});

		// Submit Cancel
		$('#cancelForm').submit(function(e) {
			e.preventDefault();
			var id = $('#cancel_id').val();

			$.ajax({
				url: '{{ url('/menu_yayasan/cancel-confirm') }}/' + id,
				type: 'POST',
				data: $(this).serialize(),
				success: function(response) {
					if (response.success) {
						$('#cancelModal').modal('hide');
						window.location.href = response.redirect;
					}
				},
				error: function(xhr) {
					alert('Terjadi kesalahan: ' + xhr.responseText);
				}
			});
		});
	});
	</script>
	<script>
		let selectedAccId = null;

		$(document).on('click', '.acc-button', function () {
			selectedAccId = $(this).data('id');
			$('#accModal').modal('show');
		});

		$('#confirmAccBtn').click(function () {
			let namaAcc = $('#nama_acc').val();
			let tanggalAcc = $('#tanggal_acc').val();

			$.ajax({
				url: '{{ url('/menu_yayasan/acc') }}/' + selectedAccId,
				type: 'POST',
				data: {
					_token: '{{ csrf_token() }}',
					nama_acc: namaAcc,
					tanggal_acc: tanggalAcc,
				},
				success: function (response) {
					if (response.success) {
						$('#tbl_list_master_menu').DataTable().ajax.reload();
						Swal.fire({
							icon: "success",
							title: "BERHASIL",
							text: response.message,
							showConfirmButton: false,
							timer: 2000
						});
					} else {
						Swal.fire({
							icon: "error",
							title: "GAGAL",
							text: response.message,
							showConfirmButton: true
						});
					}
				},
				error: function (xhr) {
					alert('Gagal menyimpan ACC: ' + xhr.responseJSON.message);
				}
			});
		});
	</script>
	<script>
		$(document).on('click', '.delete-button', function () {
			let id = $(this).data('id');
			if (confirm('Yakin ingin menghapus menu ini beserta data terkait?')) {
				$.ajax({
					url: '{{ url('/menu_yayasan') }}/' + id,
					type: 'DELETE',
					data: {
						_token: '{{ csrf_token() }}'
					},
					success: function (response) {
						$('#tbl_list_master_menu').DataTable().ajax.reload();
						Swal.fire({
							icon: "success",
							title: "BERHASIL",
							text: response.message,
							showConfirmButton: false,
							timer: 2000
						});
					},
					error: function (xhr) {
						alert('Gagal menghapus data: ' + xhr.responseJSON.message);
					}
				});
			}
		});
	</script>
	<script>
	document.getElementById('btn-cetak').addEventListener('click', function(e) {
		e.preventDefault();
		const awal = document.getElementById('tanggal_awal').value;
		const akhir = document.getElementById('tanggal_akhir').value;

		if (awal && akhir) {
			window.location.href = `{{ route('laporan-rekap-menu.excel') }}?tanggal_awal=${awal}&tanggal_akhir=${akhir}&action=cetak`;
		} else {
			alert('Silakan isi tanggal awal dan akhir terlebih dahulu.');
		}
	});
	</script>
	<!-- jQuery -->
</body>
</html>
