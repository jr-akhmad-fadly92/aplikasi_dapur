<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @media (min-width: 1200px) {
        .modal-xl {
           max-width: 80%;
        }
        }
    </style>
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
                                            Tambah Paket
                                        </button>
                                    </h3>
                                </div>

                                <div class="card-body">
                                    <table id="tabelPaketMenu" class="table table-bordered table-striped" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Paket</th>
                                                <th>Resep Karbo</th>
                                                <th>Resep Protein</th>
                                                <th>Resep Sayur</th>
                                                <th>Resep Buah</th>
                                                <th>Resep Suplemen</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
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
            <div class="modal-dialog modal-lg" role="document"> <!-- ukuran besar -->
            <div class="modal-content">
                <form id="formBox">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Paket</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Karbohidrat -->
                    <div class="form-group row align-items-center">
                        <label for="id_karbo" class="col-sm-2 col-form-label">Karbohidrat</label>
                        <div class="col-sm-3">
                            <select name="id_karbo" id="id_karbo" class="form-control select2" required style="width: 100%">
                                <option value="">-- Pilih Resep --</option>
                                @foreach($karbohidrat as $data)
                                    <option value="{{ $data->id }}">{{ $data->nama_resep }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <input type="number" name="berat_mentah_karbo_a" id="berat_mentah_karbo_a" class="form-control" placeholder="berat A">
                        </div>
                        <div class="col-sm-1 text-center">gram</div>
                        <div class="col-sm-2">
                            <input type="number" name="berat_mentah_karbo_b" id="berat_mentah_karbo_b" class="form-control" placeholder="berat B">
                        </div>
                        <div class="col-sm-1 text-center">gram</div>
                    </div>

                    <!-- Lauk -->
                    <div class="form-group row align-items-center">
                        <label for="id_lauk" class="col-sm-2 col-form-label">Lauk</label>
                        <div class="col-sm-3">
                            <select name="id_lauk" id="id_lauk" class="form-control select2" required style="width: 100%">
                                <option value="">-- Pilih Resep --</option>
                                @foreach($lauk as $data)
                                    <option value="{{ $data->id }}">{{ $data->nama_resep }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <input type="number" name="berat_mentah_protein_a" id="berat_mentah_protein_a" class="form-control" placeholder="berat A">
                        </div>
                        <div class="col-sm-1 text-center">gram</div>
                        <div class="col-sm-2">
                            <input type="number" name="berat_mentah_protein_b" id="berat_mentah_protein_b" class="form-control" placeholder="berat B">
                        </div>
                        <div class="col-sm-1 text-center">gram</div>
                    </div>

                    <!-- Buah -->
                    <div class="form-group row align-items-center">
                        <label for="id_buah" class="col-sm-2 col-form-label">Buah</label>
                        <div class="col-sm-3">
                            <select name="id_buah" id="id_buah" class="form-control select2" required style="width: 100%">
                                <option value="">-- Pilih Resep --</option>
                                @foreach($buah as $data)
                                    <option value="{{ $data->id }}">{{ $data->nama_resep }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <input type="number" name="berat_mentah_buah_a" id="berat_mentah_buah_a" class="form-control" placeholder="berat A">
                        </div>
                        <div class="col-sm-1 text-center">gram</div>
                        <div class="col-sm-2">
                            <input type="number" name="berat_mentah_buah_b" id="berat_mentah_buah_b" class="form-control" placeholder="berat B">
                        </div>
                        <div class="col-sm-1 text-center">gram</div>
                    </div>

                    <!-- Suplemen -->
                    <div class="form-group row align-items-center">
                        <label for="id_suplemen" class="col-sm-2 col-form-label">Suplemen</label>
                        <div class="col-sm-3">
                            <select name="id_suplemen" id="id_suplemen" class="form-control select2" required style="width: 100%">
                                <option value="">-- Pilih Resep --</option>
                                @foreach($suplemen as $data)
                                    <option value="{{ $data->id }}">{{ $data->nama_resep }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <input type="number" name="berat_mentah_suplemen_a" id="berat_mentah_suplemen_a" class="form-control" placeholder="berat A">
                        </div>
                        <div class="col-sm-1 text-center">gram</div>
                        <div class="col-sm-2">
                            <input type="number" name="berat_mentah_suplemen_b" id="berat_mentah_suplemen_b" class="form-control" placeholder="berat B">
                        </div>
                        <div class="col-sm-1 text-center">gram</div>
                    </div>

                    <!-- Sayur A-->
                    <div class="form-group row align-items-center">
                        <label for="id_sayur" class="col-sm-2 col-form-label">Sayur Gol A</label>
                        <div class="col-sm-3">
                            <select name="id_sayur" id="id_sayur" class="form-control select2" required style="width: 100%">
                                <option value="">-- Pilih Resep --</option>
                                @foreach($sayur as $data)
                                    <option value="{{ $data->id }}">{{ $data->nama_resep }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <input type="number" name="berat_mentah_sayur1_a" id="berat_mentah_sayur1_a" class="form-control" placeholder="Sayur 1">
                        </div>
                        <div class="col-sm-1 text-center">gram</div>
                        <div class="col-sm-2">
                            <input type="number" name="berat_mentah_sayur2_a" id="berat_mentah_sayur2_a" class="form-control" placeholder="Sayur 2">
                        </div>
                        <div class="col-sm-1 text-center">gram</div>
                    </div>
                    <div class="form-group row align-items-center">
                        <label for="id_sayur" class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-3">
                           
                        </div>
                        <div class="col-sm-2">
                            <input type="number" name="berat_mentah_sayur3_a" id="berat_mentah_sayur3_a" class="form-control" placeholder="Sayur 3">
                        </div>
                        <div class="col-sm-1 text-center">gram</div>
                        <div class="col-sm-2">
                            <input type="number" name="berat_mentah_sayur4_a" id="berat_mentah_sayur4_a" class="form-control" placeholder="Sayur 4">
                        </div>
                        <div class="col-sm-1 text-center">gram</div>
                    </div>

                    <!-- Sayur B-->
                    <div class="form-group row align-items-center">
                        <label for="id_sayur" class="col-sm-2 col-form-label">Sayur Gol B</label>
                        <div class="col-sm-3">
                            
                        </div>
                        <div class="col-sm-2">
                            <input type="number" name="berat_mentah_sayur1_b" id="berat_mentah_sayur1_b" class="form-control" placeholder="Sayur 1">
                        </div>
                        <div class="col-sm-1 text-center">gram</div>
                        <div class="col-sm-2">
                            <input type="number" name="berat_mentah_sayur2_b" id="berat_mentah_sayur2_b" class="form-control" placeholder="Sayur 2">
                        </div>
                        <div class="col-sm-1 text-center">gram</div>
                    </div>
                    <div class="form-group row align-items-center">
                        <label for="id_sayur" class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-3">
                           
                        </div>
                        <div class="col-sm-2">
                            <input type="number" name="berat_mentah_sayur3_b" id="berat_mentah_sayur3_b" class="form-control" placeholder="Sayur 3">
                        </div>
                        <div class="col-sm-1 text-center">gram</div>
                        <div class="col-sm-2">
                            <input type="number" name="berat_mentah_sayur4_b" id="berat_mentah_sayur4_b" class="form-control" placeholder="Sayur 4">
                        </div>
                        <div class="col-sm-1 text-center">gram</div>
                    </div>

                    <!-- Golongan A -->
                    <div class="form-group row align-items-center">
                        <label for="paket" class="col-sm-2 col-form-label">golongan</label>
                        <div class="col-sm-9">
                            <select name="paket" id="paket" class="form-control select2" required style="width: 100%">
                                <option value="A">-- Golongan A --</option>
                                <option value="B">-- Golongan B --</option>
                               
                            </select>
                        </div>
                        
                    </div>

                </div>


                

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
                </form>
            </div>
            </div>
        </div>

        <!--modal update-->

        <div class="modal fade" id="modalEditBox" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form id="formEditBox">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Edit Box</h5>
                </div>
                        <div class="modal-body">
                            <input type="hidden" id="edit_id">

                            <!-- Karbohidrat -->
                            <div class="form-group row align-items-center">
                                <label for="id_karbo_edit" class="col-sm-2 col-form-label">Karbohidrat</label>
                                <div class="col-sm-3">
                                    <select name="id_karbo_edit" id="id_karbo_edit" class="form-control select2" required style="width: 100%">
                                        <option value="">-- Pilih Resep --</option>
                                        @foreach($karbohidrat as $data)
                                            <option value="{{ $data->id }}">{{ $data->nama_resep }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <input type="number" name="berat_mentah_karbo_a_edit" id="berat_mentah_karbo_a_edit" class="form-control" placeholder="berat A">
                                </div>
                                <div class="col-sm-1 text-center">gram</div>
                                <div class="col-sm-2">
                                    <input type="number" name="berat_mentah_karbo_b_edit" id="berat_mentah_karbo_b_edit" class="form-control" placeholder="berat B">
                                </div>
                                <div class="col-sm-1 text-center">gram</div>
                            </div>

                            <!-- Lauk -->
                            <div class="form-group row align-items-center">
                                <label for="id_lauk_edit" class="col-sm-2 col-form-label">Lauk</label>
                                <div class="col-sm-3">
                                    <select name="id_lauk_edit" id="id_lauk_edit" class="form-control select2" required style="width: 100%">
                                        <option value="">-- Pilih Resep --</option>
                                        @foreach($lauk as $data)
                                            <option value="{{ $data->id }}">{{ $data->nama_resep }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <input type="number" name="berat_mentah_protein_a_edit" id="berat_mentah_protein_a_edit" class="form-control" placeholder="berat A">
                                </div>
                                <div class="col-sm-1 text-center">gram</div>
                                <div class="col-sm-2">
                                    <input type="number" name="berat_mentah_protein_b_edit" id="berat_mentah_protein_b_edit" class="form-control" placeholder="berat B">
                                </div>
                                <div class="col-sm-1 text-center">gram</div>
                            </div>

                            <!-- Buah -->
                            <div class="form-group row align-items-center">
                                <label for="id_buah_edit" class="col-sm-2 col-form-label">Buah</label>
                                <div class="col-sm-3">
                                    <select name="id_buah_edit" id="id_buah_edit" class="form-control select2" required style="width: 100%">
                                        <option value="">-- Pilih Resep --</option>
                                        @foreach($buah as $data)
                                            <option value="{{ $data->id }}">{{ $data->nama_resep }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <input type="number" name="berat_mentah_buah_a_edit" id="berat_mentah_buah_a_edit" class="form-control" placeholder="berat A">
                                </div>
                                <div class="col-sm-1 text-center">gram</div>
                                <div class="col-sm-2">
                                    <input type="number" name="berat_mentah_buah_b_edit" id="berat_mentah_buah_b_edit" class="form-control" placeholder="berat B">
                                </div>
                                <div class="col-sm-1 text-center">gram</div>
                            </div>

                            <!-- Suplemen -->
                            <div class="form-group row align-items-center">
                                <label for="id_suplemen_edit" class="col-sm-2 col-form-label">Suplemen</label>
                                <div class="col-sm-3">
                                    <select name="id_suplemen_edit" id="id_suplemen_edit" class="form-control select2" required style="width: 100%">
                                        <option value="">-- Pilih Resep --</option>
                                        @foreach($suplemen as $data)
                                            <option value="{{ $data->id }}">{{ $data->nama_resep }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <input type="number" name="berat_mentah_suplemen_a_edit" id="berat_mentah_suplemen_a_edit" class="form-control" placeholder="berat A">
                                </div>
                                <div class="col-sm-1 text-center">gram</div>
                                <div class="col-sm-2">
                                    <input type="number" name="berat_mentah_suplemen_b_edit" id="berat_mentah_suplemen_b_edit" class="form-control" placeholder="berat B">
                                </div>
                                <div class="col-sm-1 text-center">gram</div>
                            </div>

                            <!-- Sayur A-->
                            <div class="form-group row align-items-center">
                                <label for="id_sayur_edit" class="col-sm-2 col-form-label">Sayur Gol A</label>
                                <div class="col-sm-3">
                                    <select name="id_sayur_edit" id="id_sayur_edit" class="form-control select2" required style="width: 100%">
                                        <option value="">-- Pilih Resep --</option>
                                        @foreach($sayur as $data)
                                            <option value="{{ $data->id }}">{{ $data->nama_resep }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <input type="number" name="berat_mentah_sayur1_a_edit" id="berat_mentah_sayur1_a_edit" class="form-control" placeholder="Sayur 1">
                                </div>
                                <div class="col-sm-1 text-center">gram</div>
                                <div class="col-sm-2">
                                    <input type="number" name="berat_mentah_sayur2_a_edit" id="berat_mentah_sayur2_a_edit" class="form-control" placeholder="Sayur 2">
                                </div>
                                <div class="col-sm-1 text-center">gram</div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label for="id_sayur" class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-3">
                                
                                </div>
                                <div class="col-sm-2">
                                    <input type="number" name="berat_mentah_sayur3_a_edit" id="berat_mentah_sayur3_a_edit" class="form-control" placeholder="Sayur 3">
                                </div>
                                <div class="col-sm-1 text-center">gram</div>
                                <div class="col-sm-2">
                                    <input type="number" name="berat_mentah_sayur4_a_edit" id="berat_mentah_sayur4_a_edit" class="form-control" placeholder="Sayur 4">
                                </div>
                                <div class="col-sm-1 text-center">gram</div>
                            </div>

                            <!-- Sayur B-->
                            <div class="form-group row align-items-center">
                                <label for="id_sayur" class="col-sm-2 col-form-label">Sayur Gol B</label>
                                <div class="col-sm-3">
                                    
                                </div>
                                <div class="col-sm-2">
                                    <input type="number" name="berat_mentah_sayur1_b_edit" id="berat_mentah_sayur1_b_edit" class="form-control" placeholder="Sayur 1">
                                </div>
                                <div class="col-sm-1 text-center">gram</div>
                                <div class="col-sm-2">
                                    <input type="number" name="berat_mentah_sayur2_b_edit" id="berat_mentah_sayur2_b_edit" class="form-control" placeholder="Sayur 2">
                                </div>
                                <div class="col-sm-1 text-center">gram</div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label for="id_sayur" class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-3">
                                
                                </div>
                                <div class="col-sm-2">
                                    <input type="number" name="berat_mentah_sayur3_b_edit" id="berat_mentah_sayur3_b_edit" class="form-control" placeholder="Sayur 3">
                                </div>
                                <div class="col-sm-1 text-center">gram</div>
                                <div class="col-sm-2">
                                    <input type="number" name="berat_mentah_sayur4_b_edit" id="berat_mentah_sayur4_b_edit" class="form-control" placeholder="Sayur 4">
                                </div>
                                <div class="col-sm-1 text-center">gram</div>
                            </div>

                            <!-- Golongan A -->
                            <div class="form-group row align-items-center">
                                <label for="paket_edit" class="col-sm-2 col-form-label">golongan</label>
                                <div class="col-sm-9">
                                    <select name="paket_edit" id="paket_edit" class="form-control select2" required style="width: 100%">
                                        <option value="A">-- Golongan A --</option>
                                        <option value="B">-- Golongan B --</option>
                                    
                                    </select>
                                </div>
                                
                            </div>
                        </div>
                    <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
                </form>
            </div>
        </div>

        <!-- Modal Konfirmasi Delete -->
        <div class="modal fade" id="modalDeleteBox" tabindex="-1" aria-labelledby="deleteBoxLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteBoxLabel">Konfirmasi Hapus</h5>
          
                </div>
                <div class="modal-body">
                Apakah Anda yakin ingin menghapus data ini?
                </div>
                <div class="modal-footer">
                <button type="button" id="btn-confirm-delete" class="btn btn-danger">Ya, Hapus</button>
                </div>
            </div>
            </div>
        </div>

        <!-- Footer -->
        @include('Template.footer')

    </div>

    @include('Template.script')

    <script type="text/javascript">
        $(document).ready(function () {
            $('#tabelPaketMenu').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('paketmenu.dt_paket') }}',
                columns: [
                    { data: 'id' },
                    { data: 'paket' },
                    { data: 'nama_karbo' },
                    { data: 'nama_lauk' },
                    { data: 'nama_sayur' },
                    { data: 'nama_buah' },
                    { data: 'nama_suplemen' },
                    { data: 'action' },
                    
                ]
            });
        });
    </script>

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
                id_karbo: document.getElementById('id_karbo').value,
                berat_mentah_karbo_a: document.getElementById('berat_mentah_karbo_a').value,
                berat_mentah_karbo_b: document.getElementById('berat_mentah_karbo_b').value,
               
                id_lauk: document.getElementById('id_lauk').value,
                berat_mentah_protein_a: document.getElementById('berat_mentah_protein_a').value,
                berat_mentah_protein_b: document.getElementById('berat_mentah_protein_b').value,
               
                id_buah: document.getElementById('id_buah').value,
                berat_mentah_buah_a: document.getElementById('berat_mentah_buah_a').value,
                berat_mentah_buah_b: document.getElementById('berat_mentah_buah_b').value,
               
                id_suplemen: document.getElementById('id_suplemen').value,
                berat_mentah_suplemen_a: document.getElementById('berat_mentah_suplemen_a').value,
                berat_mentah_suplemen_b: document.getElementById('berat_mentah_suplemen_b').value,
               
                id_sayur: document.getElementById('id_sayur').value,
                berat_mentah_sayur1_a: document.getElementById('berat_mentah_sayur1_a').value,
                berat_mentah_sayur2_a: document.getElementById('berat_mentah_sayur2_a').value,
                berat_mentah_sayur3_a: document.getElementById('berat_mentah_sayur3_a').value,
                berat_mentah_sayur4_a: document.getElementById('berat_mentah_sayur4_a').value,
                
                berat_mentah_sayur1_b: document.getElementById('berat_mentah_sayur1_b').value,
                berat_mentah_sayur2_b: document.getElementById('berat_mentah_sayur2_b').value,
                berat_mentah_sayur3_b: document.getElementById('berat_mentah_sayur3_b').value,
                berat_mentah_sayur4_b: document.getElementById('berat_mentah_sayur4_b').value,
                
                paket: document.getElementById('paket').value,
               

                _token: '{{ csrf_token() }}'
            };
        
            fetch("{{ route('paketmenu.store') }}", {
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
                        $('#tabelPaketMenu').DataTable().ajax.reload(null, false); // reload datatable tanpa reset halaman
                        $('#modalBox').modal('hide'); // tutup modal jika perlu
                        document.getElementById('formBox').reset(); // reset form
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
            $('#id_karbo').select2({
                placeholder: "Pilih Karbo",
                allowClear: true
            });
           $('#id_lauk').select2({
                placeholder: "Pilih Lauk",
                allowClear: true
            });
            $('#id_sayur').select2({
                placeholder: "Pilih Sayur",
                allowClear: true
            });
            $('#id_buah').select2({
                placeholder: "Pilih Buah",
                allowClear: true
            });
            $('#id_suplemen').select2({
                placeholder: "Pilih Suplemen",
                allowClear: true
            });


        });
    </script>
    <script>
        // Menampilkan modal dan isi data saat klik tombol edit
        $(document).on('click', '.btn-edit-box', function () {
            const id = $(this).data('id');
            const paket = $(this).data('paket');
            const resepkarbo = $(this).data('resepkarbo');
            const reseplauk = $(this).data('reseplauk');
            const resepsayur = $(this).data('resepsayur');
            const resepbuah = $(this).data('resepbuah');
            const resepsuplemen = $(this).data('resepsuplemen');
            const beratmentahkarboa = $(this).data('beratmentahkarboa');
            const beratmentahkarbob = $(this).data('beratmentahkarbob');
            const beratmentahproteina = $(this).data('beratmentahproteina');
            const beratmentahproteinb = $(this).data('beratmentahproteinb');
            const beratmentahsayur1a = $(this).data('beratmentahsayur1a');
            const beratmentahsayur2a = $(this).data('beratmentahsayur2a');
            const beratmentahsayur3a = $(this).data('beratmentahsayur3a');
            const beratmentahsayur4a = $(this).data('beratmentahsayur4a');
            const beratmentahsayur1b = $(this).data('beratmentahsayur1b');
            const beratmentahsayur2b = $(this).data('beratmentahsayur2b');
            const beratmentahsayur3b = $(this).data('beratmentahsayur3b');
            const beratmentahsayur4b = $(this).data('beratmentahsayur4b');
            const beratmentahbuaha = $(this).data('beratmentahbuaha');
            const beratmentahbuahb = $(this).data('beratmentahbuahb');
            const beratmentahsuplemena = $(this).data('beratmentahsuplemena');
            const beratmentahsuplemenb = $(this).data('beratmentahsuplemenb');
            
            
            $('#edit_id').val(id);
            $('#paket_edit').val(paket);
            $('#id_karbo_edit').val(resepkarbo);
            $('#berat_mentah_karbo_a_edit').val(beratmentahkarboa);
            $('#berat_mentah_karbo_b_edit').val(beratmentahkarbob);
            $('#id_lauk_edit').val(reseplauk);
            $('#berat_mentah_protein_a_edit').val(beratmentahproteina);
            $('#berat_mentah_protein_b_edit').val(beratmentahproteinb);
            $('#id_buah_edit').val(resepbuah);
            $('#berat_mentah_buah_a_edit').val(beratmentahbuaha);
            $('#berat_mentah_buah_b_edit').val(beratmentahbuahb);
            $('#id_suplemen_edit').val(resepsuplemen);
            $('#berat_mentah_suplemen_a_edit').val(beratmentahsuplemena);
            $('#berat_mentah_suplemen_b_edit').val(beratmentahsuplemenb);
            $('#id_sayur_edit').val(resepsayur);
            $('#berat_mentah_sayur1_a_edit').val(beratmentahsayur1a);
            $('#berat_mentah_sayur2_a_edit').val(beratmentahsayur2a);
            $('#berat_mentah_sayur3_a_edit').val(beratmentahsayur3a);
            $('#berat_mentah_sayur4_a_edit').val(beratmentahsayur4a);
            $('#berat_mentah_sayur1_b_edit').val(beratmentahsayur1b);
            $('#berat_mentah_sayur2_b_edit').val(beratmentahsayur2b);
            $('#berat_mentah_sayur3_b_edit').val(beratmentahsayur3b);
            $('#berat_mentah_sayur4_b_edit').val(beratmentahsayur4b);

            $('#modalEditBox').modal('show');
        });
    
        // Submit form edit
        document.getElementById('formEditBox').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('edit_id').value;
            const paket = document.getElementById('paket_edit').value;
            const id_karbo = document.getElementById('id_karbo_edit').value;
            const id_lauk = document.getElementById('id_lauk_edit').value;
            const id_sayur = document.getElementById('id_sayur_edit').value;
            const id_buah = document.getElementById('id_buah_edit').value;
            const id_suplemen = document.getElementById('id_suplemen_edit').value;
            const berat_mentah_karbo_a = document.getElementById('berat_mentah_karbo_a_edit').value;
            const berat_mentah_karbo_b = document.getElementById('berat_mentah_karbo_b_edit').value;
            const berat_mentah_protein_a = document.getElementById('berat_mentah_protein_a_edit').value;
            const berat_mentah_protein_b = document.getElementById('berat_mentah_protein_b_edit').value;
            const berat_mentah_buah_a = document.getElementById('berat_mentah_buah_a_edit').value;
            const berat_mentah_buah_b = document.getElementById('berat_mentah_buah_b_edit').value;
            const berat_mentah_suplemen_a = document.getElementById('berat_mentah_suplemen_a_edit').value;
            const berat_mentah_suplemen_b = document.getElementById('berat_mentah_suplemen_b_edit').value;
            const berat_mentah_sayur1_a = document.getElementById('berat_mentah_sayur1_a_edit').value;
            const berat_mentah_sayur2_a = document.getElementById('berat_mentah_sayur2_a_edit').value;
            const berat_mentah_sayur3_a = document.getElementById('berat_mentah_sayur3_a_edit').value;
            const berat_mentah_sayur4_a = document.getElementById('berat_mentah_sayur4_a_edit').value;
            const berat_mentah_sayur1_b = document.getElementById('berat_mentah_sayur1_b_edit').value;
            const berat_mentah_sayur2_b = document.getElementById('berat_mentah_sayur2_b_edit').value;
            const berat_mentah_sayur3_b = document.getElementById('berat_mentah_sayur3_b_edit').value;
            const berat_mentah_sayur4_b = document.getElementById('berat_mentah_sayur4_b_edit').value;
            
    
            fetch(`/paket-menu/update/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    id              : id,
                    paket           : paket,
                    id_karbo        : id_karbo,
                    id_lauk         : id_lauk,
                    id_sayur        : id_sayur,
                    id_buah         : id_buah,
                    id_suplemen     : id_suplemen,
                    berat_mentah_karbo_a    : berat_mentah_karbo_a,
                    berat_mentah_karbo_b    : berat_mentah_karbo_b,
                    berat_mentah_protein_a  : berat_mentah_protein_a,
                    berat_mentah_protein_b  : berat_mentah_protein_b,
                    berat_mentah_buah_a     : berat_mentah_buah_a,
                    berat_mentah_buah_b     : berat_mentah_buah_b,
                    berat_mentah_suplemen_a : berat_mentah_suplemen_a,
                    berat_mentah_suplemen_b : berat_mentah_suplemen_a,
                    berat_mentah_sayur1_a   : berat_mentah_sayur1_a,
                    berat_mentah_sayur2_a   : berat_mentah_sayur2_a,
                    berat_mentah_sayur3_a   : berat_mentah_sayur3_a,
                    berat_mentah_sayur4_a   : berat_mentah_sayur4_a,
                    berat_mentah_sayur1_b   : berat_mentah_sayur1_b,
                    berat_mentah_sayur2_b   : berat_mentah_sayur2_b,
                    berat_mentah_sayur3_b   : berat_mentah_sayur3_b,
                    berat_mentah_sayur4_b   : berat_mentah_sayur4_b,
                    
                 })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#tabelPaketMenu').DataTable().ajax.reload(null, false);
                        $('#modalEditBox').modal('hide');
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message
                    });
                }
            });
        });
    </script>
    <script>
        let boxIdToDelete = null;
    
        // Saat tombol hapus ditekan, simpan id dan tampilkan modal
        $(document).on('click', '.btn-delete-box', function () {
            boxIdToDelete = $(this).data('id');
            $('#modalDeleteBox').modal('show');
        });
    
        // Ketika tombol konfirmasi hapus ditekan
        $('#btn-confirm-delete').on('click', function () {
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
    
            fetch(`/paket-menu/${boxIdToDelete}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                $('#modalDeleteBox').modal('hide');
    
                if (data.success) {
                    // Refresh DataTables tanpa reload halaman
                    $('#tabelPaketMenu').DataTable().ajax.reload(null, false);
    
                    // Optional: notifikasi
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.fire('Terjadi kesalahan', 'Gagal menghapus data.', 'error');
                $('#modalDeleteBox').modal('hide');
            });
        });
    </script>
    
        
        
    
</body>
</html>
