<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
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
                             
                          <div class="row invoice-info">
                                <div class="col-sm-6 ">
                                <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Nomor PO</b> <a class="float-right">{{ $po->nomor_po }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Tanggal pengajuan</b> <a class="float-right">{{ \Carbon\Carbon::parse($po->tanggal_pengajuan)->translatedFormat('l, d-m-Y') }}</a>
                                </li>
                              
                                </ul>
                                <h3 class="card-title">
                                    <div class="btn-group w-100" role="group">
                                        @if($po->status_po == 'draft')
                                        <a href="{{ route('simpan_draft_pengajuan_po',[$po->id,$id_menu]) }}" onclick="return confirm('Apakah Anda yakin ingin menyimpan draft dan menyelesaikan proses ini?')" class="btn btn-primary" id="btn-edit-post">Selesai</a>
                                        @else
                                        <a href="{{ route('rincian_menu_po', $id_menu) }}"  class="btn btn-primary" id="btn-edit-post">Kembali</a>
                                        @endif
                                        
                                        <a href="{{ route('pengajuan_po.bulk_pengiriman', [$po->id, $id_menu, $po->id_kontrak]) }}" 
                                            class="btn btn-info" title="Input pengiriman untuk semua bahan berdasarkan kategori resep">
                                            <i class="fas fa-truck"></i> Pengiriman Otomatis
                                        </a>
                                    </div>
                               </h3>
                                
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-6 invoice-col">
                                <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Status</b> <a class="float-right">{{ $po->status_po }}</a>
                                </li>
                                
                                
                                </ul>
                                
                                </div>
                                <!-- /.col -->
                                
                                <!-- /.col -->
                            </div>
                        </div>
                        <!-- /.card-header -->
                       
                        </div>
                        <!-- /.card -->
                        <div class="card">
                            <div class="card-header">
                           <h1 >{{ $header }}</h1>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                
                                <table id="tbl_list_master_menu" class="table table-bordered table-hover " style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Resep</th>
                                        <th>Bahan</th>
                                        <th>Jumlah</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                </table>
                            </div>
                        <!-- /.card-body -->
                        </div>
                        

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
    <div class="modal fade" id="myModal" tabindex="-1"  >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Detail Pengiriman</h5>
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('simpan_rincian_pengajuan_po') }}" method="POST" enctype="multipart/form-data">
                        
                                @csrf
                             
                    <div class="modal-body">
                       
                        <input type="text" name="_token" value="{{ csrf_token() }}" hidden>
                        <input type="text" name="id_menu" id="id_menu" value="{{ $id_menu}}" hidden>
                        <input type="text" name="idpo" id="idpo" value="{{ $po->id }}" hidden>
                        <input type="text" name="idbahan" id="idBahan" hidden>
                        <div class="mb-3" hidden>
                            <label class="form-label">id rincian bahan</label>
                            <input type="text" class="form-control" name="idrincian" id="modalidrincian" readonly>
                        </div>
                        <div class="mb-3" hidden>
                            <label class="form-label">Bahan</label>
                            <input type="text" class="form-control" name="bahan" id="modalBahan" readonly>
                        </div>
                        <div class="mb-3" hidden>
                            <label class="form-label">Merek Bahan</label>
                            <select class="form-control select2" id="merek_bahan" name="merek_bahan" style="width: 100%" >
                                <option value="">-- Pilih Bahan --</option>
                                @foreach ($data_bahan_koperasi as $data)
                                    <option value="{{ $data->id }}"
                                        data-harga="{{ $data->harga_bahan }}">
                                        {{ $data->merek_bahan }}, {{ number_format($data->harga_bahan, 0, ',', '.') }} / {{ $data->satuan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Merek dan harga bahan</label>
                            <input type="text" class="form-control" hidden name="idmerek" id="idmerek" required>
                            <input type="text" class="form-control" name="merek" id="merek" readonly>
                            <input type="text" class="form-control" name="hargamerek" id="hargamerek" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah</label>
                            <input type="number" class="form-control" name="jumlah" id="modalJumlah" readonly>
                        </div>
                        <div class="mb-3" hidden>
                            <label class="form-label">Buffer</label>
                            <input type="number" class="form-control" name="buffer" id="buffer" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Satuan</label>
                            <input type="text" class="form-control" name="satuan" id="modalSatuan" readonly>
                        </div>
                        <div class="mb-3" hidden>
                            <label class="form-label">box</label>
                            <input type="number" class="form-control" name="box" id="box" required>
                        </div>
                        

                        <div class="mb-3" hidden>
                            <label class="form-label">Tanggal Digunakan</label>
                            <input type="date" class="form-control" name="tanggal_digunakan" id="modalTanggal" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rencana Kirim bahan</label>
                            <input type="datetime-local" class="form-control" name="tanggal_kirim" id="tanggal_kirim" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Harga</label>
                            <input type="text" id="modalJumlahPO" class="form-control" required>
                            <input type="hidden" name="jumlahpo" id="jumlahpo_hidden">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <input type="text" class="form-control" name="keterangan" id="keterangan">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- REQUIRED SCRIPTS -->
  
    @include('Template.script')
   
    <script type="text/javascript">
    $(document).ready(function () {
    $('#tbl_list_master_menu').DataTable({
            
            ajax: '{{ url()->current() }}',
            columns: [
                 { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama_resep', name: 'nama_resep' },
                { data: 'bahan_dan_total', name: 'bahan_dan_total' },
                { data: 'terpenuhi', name: 'terpenuhi' },
                { data: 'action', name: 'action' },
               

            ]
        });
        // Event submit form untuk insert data
        
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
            // Event saat tombol Update diklik
            $(document).on('click', '.update-jumlah', function() {
                let id = $(this).data('id');
                let jumlah = $(this).closest('tr').find('.jumlah').val();

                $.ajax({
                    url: "{{ route('rincian_sekolah.update_jumlah') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        jumlah : jumlah
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: "success",
                            title: "BERHASIL",
                            text: "{{ session('success') }}",
                            showConfirmButton: false,
                            timer: 2000
                        });
                        $('#tbl_list_master_menu').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan, coba lagi!');
                    }
                });
            });
        });
        
    </script>
 
    
    
    <script>
        $(document).ready(function() {
            $('#bahan_id').select2({
                placeholder: "Pilih Bahan",
                allowClear: true
            });
        });
    </script>
    
    <script>
        function formatRupiah(angka) {
            angka = angka.toString().replace(/\D/g, '');
            return angka ? parseInt(angka).toLocaleString('id-ID') : '';
        }
        let satuan;
        let jumlah_akhir;
        
        $('#modalJumlahPO').on('input', function () {
            let nilai = $(this).val();

            // Hapus semua karakter non-angka untuk ambil nilai numeriknya
            let angka = parseFloat(nilai.replace(/[^\d]/g, ''));

            if (!isNaN(angka)) {
                $('#jumlahpo_hidden').val(angka);
            } else {
                $('#jumlahpo_hidden').val(0);
            }
        });
        $(document).ready(function() {
            function hitungJumlahPO() {
                let harga = parseInt($('#merek_bahan option:selected').data('harga')) || 0;
                let jumlah = parseInt($('#modalJumlah').val()) || 0;
                let jumlah_buffer = parseInt($('#buffer').val()) || 0;
                let total;
                if (satuan == 'Gram' || satuan == 'ml')
                {
                    total = harga * (jumlah+jumlah_buffer) / 1000;
                }else{
                    total = harga * (jumlah+jumlah_buffer) ;
                }
                

                // Format ke rupiah
                let formatted = new Intl.NumberFormat('id-ID').format(total);

                $('#modalJumlahPO').val(formatted);
                $('#jumlahpo_hidden').val(total);
            }   

            $('#merek_bahan').on('change', hitungJumlahPO);
            $('#modalJumlah').on('input', hitungJumlahPO);
        });
        document.addEventListener("DOMContentLoaded", function() {
            var modalElement = document.getElementById("myModal");
            var modal = new bootstrap.Modal(modalElement); // Inisialisasi modal

            $(document).on("click", ".openModalBtn", function() {
                let bahan = $(this).data("bahan");
                let jumlah = $(this).data("jumlahbahan");
                satuan = $(this).data("satuan");
                let tanggal = $(this).data("tanggal_digunakan");
                let tanggal_kirim = $(this).data("tanggal_kirim");
                let jumlahpo = $(this).data("jumlahpo");
                let rincian = $(this).data("rincian");
                let keterangan = $(this).data("keterangan");
                let buffer = $(this).data("buffer");
                let box = $(this).data("box");
                let merek = $(this).data("merek");
                let idmerek = $(this).data("idmerek");
                let harga = $(this).data("harga");


                let satuan_fix = satuan;
                
                if (satuan_fix === "ml") {
                    satuan_fix = "liter";
                } else if (satuan_fix === "Gram") {
                    satuan_fix = "Kg";
                }
                $("#idmerek").val(idmerek);
                $("#merek").val(merek);
                $("#hargamerek").val(formatRupiah(harga) + ' @ ' + satuan_fix);
                $("#merek_bahan").val(idmerek).trigger('change');
                $("#idBahan").val($(this).data("idbahan"));
                $("#modalBahan").val(bahan);
                $("#modalJumlah").val(jumlah);
                $("#modalSatuan").val(satuan);
                $("#modalTanggal").val(tanggal);
                $("#tanggal_kirim").val(tanggal_kirim);
                $("#modalJumlahPO").val(formatRupiah(jumlahpo));
                $("#jumlahpo_hidden").val(formatRupiah(jumlahpo));
                $("#modalidrincian").val(rincian);
                $("#keterangan").val(keterangan);
                $("#buffer").val(buffer);
                $("#box").val(box);
                
                modal.show(); // Buka modal
            });

            
        });

    </script>
    <script>
        const input = document.getElementById('modalJumlahPO');
        const hidden = document.getElementById('jumlahpo_hidden');
        
        input.addEventListener('input', function () {
            const raw = this.value.replace(/\./g, '');
            
        });
        
        document.getElementById('formPO').addEventListener('submit', function () {
            hidden.value = input.value.replace(/\./g, '');
        });
        
        </script>
        <script>
            $(document).ready(function() {
                $('#merek_bahan').select2({
                    placeholder: "Pilih Merek",
                    allowClear: true
                });
            });
        </script>
    <!-- jQuery -->
</body>
</html>
