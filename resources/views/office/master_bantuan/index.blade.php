<!DOCTYPE html>
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

        <!-- Main Sidebar Container -->
        @include('Template.left-sidebar')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">{{ $header }}</h1>
                        </div>
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
                                    <h3 class="card-title">
                                        <!-- Tombol untuk buka modal -->
                                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalBox">
                                             Update
                                        </button>
                                    </h3>
                                </div>

                                <div class="card-body">
                                    <table id="tb_list_box" class="table table-bordered table-hover" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center">Bantuan Pangan A</th>
                                                <th style="text-align: center">Bantuan Pangan B</th>
                                                <th style="text-align: center">Bantuan Non Pangan</th>
                                                <th style="text-align: center">Bantuan Infrastruktur dan Peralatan</th>
                                                <th style="text-align: center">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                            $jumlah_a= $bantuan->bantuan_pangan_A+$bantuan->bantuan_operasional+$bantuan->bantuan_infra;
                                            $jumlah_b= $bantuan->bantuan_pangan_B+$bantuan->bantuan_operasional+$bantuan->bantuan_infra;
                                            @endphp
                                            <tr>
                                                <th style="text-align: center">Rp. {{ number_format($bantuan->bantuan_pangan_A,0,',','.') ?? 0 }}</th>
                                                <th style="text-align: center">Rp. {{ number_format($bantuan->bantuan_pangan_B,0,',','.') ?? 0 }}</th>
                                                <th style="text-align: center">Rp. {{ number_format($bantuan->bantuan_operasional,0,',','.') ?? 0 }}</th>
                                                <th style="text-align: center">Rp. {{ number_format($bantuan->bantuan_infra,0,',','.') ?? 0 }}</th>
                                                <th style="text-align: center">A : Rp. {{ number_format($jumlah_a,0,',','.') ?? 0 }} || B : Rp. {{ number_format($jumlah_b,0,',','.') ?? 0 }}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        
        <!-- Modal Input -->
        <div class="modal fade" id="modalBox" tabindex="-1" role="dialog" aria-labelledby="modalBoxLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formBox">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Update Bantuan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="bantuan_pangan_A">Bantuan Pangan A</label>
                        <input type="number" name="bantuan_pangan_A" id="bantuan_pangan_A" class="form-control" value="{{ $bantuan->bantuan_pangan_A ?? 0}}" required>
                    </div>
                    <div class="form-group">
                        <label for="bantuan_pangan_B">Bantuan Pangan B</label>
                        <input type="number" name="bantuan_pangan_B" id="bantuan_pangan_B" class="form-control" value="{{ $bantuan->bantuan_pangan_B ?? 0}}" required>
                    </div>
                    <div class="form-group">
                        <label for="bantuan_operasional">Bantuan Non Pangan</label>
                        <input type="number" name="bantuan_operasional" id="bantuan_operasional" class="form-control" value="{{ $bantuan->bantuan_operasional ?? 0 }}" required>
                    </div>
                    <div class="form-group">
                        <label for="bantuan_infra">Bantuan Infrastruktur</label>
                        <input type="number" name="bantuan_infra" id="bantuan_infra" class="form-control" value="{{ $bantuan->bantuan_infra ?? 0 }}" required>
                    </div>
                    
                    
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
                </form>
            </div>
            </div>
        </div>

        

        
        <!-- Footer -->
        @include('Template.footer')

    </div>

    @include('Template.script')

    
    <script src="{{ asset('AdminLte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
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
        document.getElementById('formBox').addEventListener('submit', function(e) {
            e.preventDefault();
        
            const formData = {
                bantuan_pangan_A: document.getElementById('bantuan_pangan_A').value,
                bantuan_pangan_B: document.getElementById('bantuan_pangan_B').value,
                bantuan_operasional: document.getElementById('bantuan_operasional').value,
                bantuan_infra : document.getElementById('bantuan_infra').value,
                _token: '{{ csrf_token() }}'
            };
        
            fetch("{{ route('master-bantuan.store') }}", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': formData._token
                },
                body: JSON.stringify(formData)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                    }).then(() => {
                        $('#modalBox').modal('hide'); // tutup modal jika perlu
                        document.getElementById('formBox').reset(); // reset form
                        location.reload(); // reload page
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Terjadi kesalahan.'
                    });
                }
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
    </body>
</html>
