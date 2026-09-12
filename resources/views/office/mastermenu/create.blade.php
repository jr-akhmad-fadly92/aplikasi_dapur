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
                            <form id="myForm" action="{{ route('mastermenu.store') }}" method="POST" enctype="multipart/form-data">

                                @csrf
                                
                          

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Nama Menu</label>
                                    <input readonly type="text" class="form-control @error('menu') is-invalid @enderror" name="menu" id="menu" value="" placeholder="---">
                                
                                    <!-- error message untuk name -->
                                    @error('menu')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Golongan</label>
                                    <select class="form-control @error('golongan') is-invalid @enderror" name="golongan" id="golongan">
                                        <option value="">-- Pilih Golongan --</option>
                                        <option value="umum">Umum</option>
                                        <option value="pax_a">Pax A</option>
                                        <option value="pax_b">Pax B</option>
                                        <option value="busui_bumil">Busui/Bumil</option>
                                        <option value="balita">Balita</option>
                                        <option value="baduta">Baduta</option>
                                    </select>
                                    @error('golongan')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" hidden>
                                    <label class="font-weight-bold">Karbohidrat</label>
                                            <select class="form-control select2" id="karbohidrat" name="karbohidrat">
                                                 @foreach ($karbohidrat as $data)
                                                 
                                                 <option value={{ $data->id }} >{{ $data->nama_resep }} </option>
                                                 
                                                 @endforeach
                                            </select>
                                    <!-- error message untuk name -->
                                    @error('karbohidrat')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3" hidden>
                                    <label class="font-weight-bold">Protein</label>
                                            <select class="form-control select2" id="protein" name="protein">
                                                 @foreach ($protein as $data)
                                                 
                                                 <option value={{ $data->id }} >{{ $data->nama_resep }} </option>
                                                 
                                                 @endforeach
                                            </select>
                                    <!-- error message untuk name -->
                                    @error('protein')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3" hidden>
                                    <label class="font-weight-bold">Sayur</label>
                                            <select class="form-control select2" id="sayur" name="sayur">
                                                 @foreach ($sayur as $data)
                                                 
                                                 <option value={{ $data->id }} >{{ $data->nama_resep }} </option>
                                                 
                                                 @endforeach
                                            </select>
                                    <!-- error message untuk name -->
                                    @error('sayur')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" hidden>
                                    <label class="font-weight-bold">Buah</label>
                                            <select class="form-control select2" id="buah" name="buah">
                                                 @foreach ($buah as $data)
                                                 
                                                 <option value={{ $data->id }} >{{ $data->nama_resep }} </option>
                                                 
                                                 @endforeach
                                            </select>
                                    <!-- error message untuk name -->
                                    @error('buah')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" hidden>
                                    <label class="font-weight-bold">Susu</label>
                                            <select class="form-control select2" id="susu" name="susu">
                                                 @foreach ($susu as $data)
                                                 
                                                 <option value={{ $data->id }} >{{ $data->nama_resep }} </option>
                                                 
                                                 @endforeach
                                            </select>
                                    <!-- error message untuk name -->
                                    @error('susu')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Nama Pengaju</label>
                                    <select id="nama_pengaju_select" class="form-control">
                                        <option value="">-- Pilih Pengaju --</option>
                                        {{-- opsi dari tb_karyawan --}}
                                        @foreach ($karyawan as $data)
                                            <option value="{{ $data->nama ?? $data->nama_karyawan ?? $data->name }}">{{ $data->nama ?? $data->nama_karyawan ?? $data->name }}</option>
                                        @endforeach
                                        {{-- opsi dari tb_data_dapur (admin_dapur, ahli_gizi) --}}
                                        @php $dapur = \App\Models\DataDapur::firstOrDefault(); @endphp
                                        @if(!empty($dapur->admin_dapur) && $dapur->admin_dapur !== '-')
                                            <option value="{{ $dapur->admin_dapur }}">{{ $dapur->admin_dapur }} (Admin Dapur)</option>
                                        @endif
                                        @if(!empty($dapur->ahli_gizi) && $dapur->ahli_gizi !== '-')
                                            <option value="{{ $dapur->ahli_gizi }}">{{ $dapur->ahli_gizi }} (Ahli Gizi)</option>
                                        @endif
                                        <option value="__MANUAL__">Lainnya (Isi sendiri)</option>
                                    </select>

                                    <input type="hidden" name="nama_pengaju" id="nama_pengaju" value="">

                                    <input type="text" id="nama_pengaju_manual" class="form-control mt-2" placeholder="Isi nama pengaju" style="display:none;">

                                    @error('nama_pengaju')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Tanggal Pengajuan</label>
                                    <input type="date" class="form-control @error('tanggal_pengajuan') is-invalid @enderror" value="{{ old('tanggal_pengajuan', date('Y-m-d')) }}" name="tanggal_pengajuan" id="tanggal_pengajuan">
                                    @error('tanggal_pengajuan')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Tanggal Digunakan</label>
                                    <input type="date" class="form-control @error('tanggal_kirim') is-invalid @enderror"  value="" name="tanggal_kirim" id="tanggal_kirim">
                                    @error('tanggal_kirim')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                               <!-- Checkbox untuk menyembunyikan input -->
                                <div class="form-check mb-3" hidden>
                                    <input class="form-check-input" type="checkbox" id="hide_gramasi_checkbox">
                                    <label class="form-check-label" for="hide_gramasi_checkbox" checked>Gramasi Bawaan</label>
                                </div>
                                
                                <div class="form-group mb-3" id="gramasi_karbo_a_group" hidden>
                                    <label class="font-weight-bold">Gramasi Karbo A</label>
                                    <input type="text" class="form-control @error('gramasi_karbo_a') is-invalid @enderror" name="gramasi_karbo_a" value="100">
                                    @error('gramasi_karbo_a')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" id="gramasi_porsi_tray_a_group" hidden>
                                    <label class="font-weight-bold">Porsi Tray A</label>
                                    <input type="text" class="form-control @error('porsi_tray_a') is-invalid @enderror" name="porsi_tray_a"  value="38">
                                    @error('porsi_tray_a')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" id="gramasi_kg_tray_a_group" hidden>
                                    <label class="font-weight-bold">KG Tray A</label>
                                    <input type="text" class="form-control @error('kg_tray_a') is-invalid @enderror" name="kg_tray_a"  value="3">
                                    @error('kg_tray_a')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" id="gramasi_lauk_a_group" hidden>
                                    <label class="font-weight-bold">Gramasi Lauk A</label>
                                    <input type="text" class="form-control @error('gramasi_lauk_a') is-invalid @enderror" name="gramasi_lauk_a"  value="100">
                                    @error('gramasi_lauk_a')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" id="gramasi_sayur_utama_a_group" hidden>
                                    <label class="font-weight-bold">Gramasi Sayur Utama A</label>
                                    <input type="text" class="form-control @error('gramasi_sayur_utama_a') is-invalid @enderror" name="gramasi_sayur_utama_a"  value="75">
                                    @error('gramasi_sayur_utama_a')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" id="gramasi_sayur_kedua_a_group" hidden>
                                    <label class="font-weight-bold">Gramasi Sayur Kedua A</label>
                                    <input type="text" class="form-control @error('gramasi_sayur_kedua_a') is-invalid @enderror" name="gramasi_sayur_kedua_a"  value="25">
                                    @error('gramasi_sayur_kedua_a')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" id="gramasi_buah_a_group" hidden>
                                    <label class="font-weight-bold">Gramasi Buah A</label>
                                    <input type="text" class="form-control @error('gramasi_buah_a') is-invalid @enderror" name="gramasi_buah_a"  value="100">
                                    @error('gramasi_buah_a')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" id="gramasi_suplemen_a_group" hidden>
                                    <label class="font-weight-bold">Gramasi Suplemen A</label>
                                    <input type="text" class="form-control @error('gramasi_suplemen_a') is-invalid @enderror" name="gramasi_suplemen_a"  value="100">
                                    @error('gramasi_suplemen_a')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Bagian B --}}

                                <div class="form-group mb-3" id="gramasi_karbo_b_group" hidden>
                                    <label class="font-weight-bold">Gramasi Karbo B</label>
                                    <input type="text" class="form-control @error('gramasi_karbo_b') is-invalid @enderror" name="gramasi_karbo_b" value="180">
                                    @error('gramasi_karbo_b')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" id="gramasi_porsi_tray_b_group" hidden>
                                    <label class="font-weight-bold">Porsi Tray B</label>
                                    <input type="text" class="form-control @error('porsi_tray_b') is-invalid @enderror" name="porsi_tray_b" value="38">
                                    @error('porsi_tray_b')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" id="gramasi_kg_tray_b_group" hidden>
                                    <label class="font-weight-bold">KG Tray B</label>
                                    <input type="text" class="form-control @error('kg_tray_b') is-invalid @enderror" name="kg_tray_b" value="3">
                                    @error('kg_tray_b')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" id="gramasi_lauk_b_group" hidden>
                                    <label class="font-weight-bold">Gramasi Lauk B</label>
                                    <input type="text" class="form-control @error('gramasi_lauk_b') is-invalid @enderror" name="gramasi_lauk_b" value="100">
                                    @error('gramasi_lauk_b')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" id="gramasi_sayur_utama_b_group" hidden>
                                    <label class="font-weight-bold">Gramasi Sayur Utama B</label>
                                    <input type="text" class="form-control @error('gramasi_sayur_utama_b') is-invalid @enderror" name="gramasi_sayur_utama_b" value="75">
                                    @error('gramasi_sayur_utama_b')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" id="gramasi_sayur_kedua_b_group" hidden>
                                    <label class="font-weight-bold">Gramasi Sayur Kedua B</label>
                                    <input type="text" class="form-control @error('gramasi_sayur_kedua_b') is-invalid @enderror" name="gramasi_sayur_kedua_b" value="25">
                                    @error('gramasi_sayur_kedua_b')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" id="gramasi_buah_b_group" hidden>
                                    <label class="font-weight-bold">Gramasi Buah B</label>
                                    <input type="text" class="form-control @error('gramasi_buah_b') is-invalid @enderror" name="gramasi_buah_b" value="100">
                                    @error('gramasi_buah_b')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" id="gramasi_suplemen_b_group" hidden>
                                    <label class="font-weight-bold">Gramasi Suplemen B</label>
                                    <input type="text" class="form-control @error('gramasi_suplemen_b') is-invalid @enderror" name="gramasi_suplemen_b" value="100">
                                    @error('gramasi_suplemen_b')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>



                                <!--button type="submit"  id="submitBtn" class="btn btn-md btn-primary me-3">Simpan</button-->

                                <button type="submit"   class="btn btn-md btn-primary me-3">Simpan</button>
                                <a type="reset" href="{{ route('mastermenu.index') }}" class="btn btn-md btn-warning">Kembali</a>

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
    <script>
        // Mapping golongan ke display name
        const golonganMap = {
            'umum': 'Umum',
            'pax_a': 'Pax A',
            'pax_b': 'Pax B',
            'busui_bumil': 'Busui/Bumil',
            'balita': 'Balita',
            'baduta': 'Baduta'
        };

        // Format tanggal ke: "senin, 6 mei 2026"
        function formatTanggalIndonesia(dateStr) {
            if (!dateStr) return '';
            
            const hariList = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
            const bulanList = ['januari', 'februari', 'maret', 'april', 'mei', 'juni', 
                               'juli', 'agustus', 'september', 'oktober', 'november', 'desember'];
            
            const date = new Date(dateStr + 'T00:00:00');
            const hari = hariList[date.getDay()];
            const tanggal = date.getDate();
            const bulan = bulanList[date.getMonth()];
            const tahun = date.getFullYear();
            
            return `${hari}, ${tanggal} ${bulan} ${tahun}`;
        }

        // Generate nama menu otomatis
        function generateMenuName() {
            const golonganValue = document.getElementById('golongan').value;
            const tanggalKirimValue = document.getElementById('tanggal_kirim').value;
            
            if (!golonganValue || !tanggalKirimValue) {
                document.getElementById('menu').value = '';
                return;
            }
            
            const golonganDisplay = golonganMap[golonganValue] || golonganValue;
            const tanggalDisplay = formatTanggalIndonesia(tanggalKirimValue);
            
            document.getElementById('menu').value = `${golonganDisplay} - ${tanggalDisplay}`;
        }

        // Listen to changes pada golongan dan tanggal_kirim
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('golongan').addEventListener('change', generateMenuName);
            document.getElementById('tanggal_kirim').addEventListener('change', generateMenuName);
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#karbohidrat').select2({
                placeholder: "Pilih karbohidrat",
                allowClear: true
            });
            $('#protein').select2({
                placeholder: "Pilih protein",
                allowClear: true
            });
            $('#sayur').select2({
                placeholder: "Pilih sayur",
                allowClear: true
            });
            $('#buah').select2({
                placeholder: "Pilih buah",
                allowClear: true
            });
            $('#susu').select2({
                placeholder: "Pilih susu",
                allowClear: true
            });

        });
    </script>
    <!--script src="{{ asset('js/sweetalert2.all.min.js') }}"></script-->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('myForm');

        form.addEventListener('submits', function (e) {
            e.preventDefault();

            const menu = document.getElementById('menu').value;
            const karbohidratSelect = document.getElementById('karbohidrat');
            const karbohidratNama = karbohidratSelect.options[karbohidratSelect.selectedIndex].text;
            
            const proteinSelect = document.getElementById('protein');
            const proteinNama = proteinSelect.options[proteinSelect.selectedIndex].text;    
            
            const sayurSelect = document.getElementById('sayur');
            const sayurNama = sayurSelect.options[sayurSelect.selectedIndex].text;
            
            const buahSelect = document.getElementById('buah');
            const buahNama = buahSelect.options[buahSelect.selectedIndex].text;
            
            const susuSelect = document.getElementById('susu');
            const susuNama = susuSelect.options[susuSelect.selectedIndex].text;
            
            
            const nama_pengaju = document.getElementById('nama_pengaju').value;
            const tanggal_pengajuan = document.getElementById('tanggal_pengajuan').value;
            const tanggal_kirim = document.getElementById('tanggal_kirim').value;

            if (!menu || !nama_pengaju || !tanggal_pengajuan || !tanggal_kirim) {
                Swal.fire('Lengkapi semua data!', '', 'warning');
                return;
            }

            const formatTanggal = (tgl) => {
                const hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                const bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                const d = new Date(tgl);
                return `${hari[d.getDay()]}, ${d.getDate()} ${bulan[d.getMonth()]} ${d.getFullYear()}`;
            };

            Swal.fire({
                title: 'Konfirmasi Data',
                html: `
                    <p><strong>Menu:</strong> ${menu}</p>
                    <!--p><strong>Karbohidrat:</strong> ${karbohidratNama}</!--p>
                    <p><strong>Protein:</strong> ${proteinNama}</p>
                    <p><strong>Sayur:</strong> ${sayurNama}</p>
                    <p><strong>Buah:</strong> ${buahNama}</p>
                    <p><strong>Suplemen:</strong> ${susuNama}</p-->
                    <p><strong>Nama Pengaju:</strong> ${nama_pengaju}</p>
                    <p><strong>Tanggal Pengajuan:</strong> ${formatTanggal(tanggal_pengajuan)}</p>
                    <p><strong>Tanggal Kirim:</strong> ${formatTanggal(tanggal_kirim)}</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim!',
                cancelButtonText: 'Periksa Lagi'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // ⬅️ INI HARUS JALAN
                }
            });
        });
    });
    </script>
    <script>
        // Sync nama_pengaju selection/manual input into hidden `nama_pengaju` field
        document.addEventListener('DOMContentLoaded', function () {
            const select = document.getElementById('nama_pengaju_select');
            const hidden = document.getElementById('nama_pengaju');
            const manual = document.getElementById('nama_pengaju_manual');

            function updateHidden() {
                const val = select.value;
                if (!val) {
                    hidden.value = '';
                    manual.style.display = 'none';
                    return;
                }
                if (val === '__MANUAL__') {
                    manual.style.display = '';
                    hidden.value = manual.value || '';
                    return;
                }
                // regular nama from list (including admin_dapur or ahli_gizi)
                hidden.value = val;
                manual.style.display = 'none';
            }

            select.addEventListener('change', updateHidden);
            manual.addEventListener('input', function () {
                if (select.value === '__MANUAL__') hidden.value = manual.value;
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkbox = document.getElementById('hide_gramasi_checkbox');
            const inputGroup = document.getElementById('gramasi_karbo_a_group');
            const inputGroup1 = document.getElementById('gramasi_porsi_tray_a_group');
            const inputGroup2 = document.getElementById('gramasi_kg_tray_a_group');
            const inputGroup3 = document.getElementById('gramasi_lauk_a_group');
            const inputGroup4 = document.getElementById('gramasi_sayur_utama_a_group');
            const inputGroup5 = document.getElementById('gramasi_sayur_kedua_a_group');
            const inputGroup6 = document.getElementById('gramasi_buah_a_group');
            const inputGroup7 = document.getElementById('gramasi_suplemen_a_group');
            const inputGroup8 = document.getElementById('gramasi_karbo_b_group');
            const inputGroup9 = document.getElementById('gramasi_porsi_tray_b_group');
            const inputGroup10 = document.getElementById('gramasi_kg_tray_b_group');
            const inputGroup11 = document.getElementById('gramasi_lauk_b_group');
            const inputGroup12 = document.getElementById('gramasi_sayur_utama_b_group');
            const inputGroup13 = document.getElementById('gramasi_sayur_kedua_b_group');
            const inputGroup14 = document.getElementById('gramasi_buah_b_group');
            const inputGroup15 = document.getElementById('gramasi_suplemen_b_group');
        
            checkbox.addEventListener('change', function () {
                if (checkbox.checked) {
                    inputGroup.style.display = 'none';
                    inputGroup1.style.display = 'none';
                    inputGroup2.style.display = 'none';
                    inputGroup3.style.display = 'none';
                    inputGroup4.style.display = 'none';
                    inputGroup5.style.display = 'none';
                    inputGroup6.style.display = 'none';
                    inputGroup7.style.display = 'none';
                    inputGroup8.style.display = 'none';
                    inputGroup9.style.display = 'none';
                    inputGroup10.style.display = 'none';
                    inputGroup11.style.display = 'none';
                    inputGroup12.style.display = 'none';
                    inputGroup13.style.display = 'none';
                    inputGroup14.style.display = 'none';
                    inputGroup15.style.display = 'none';
                } else {
                    inputGroup.style.display = 'block';
                    inputGroup1.style.display = 'block';
                    inputGroup2.style.display = 'block';
                    inputGroup3.style.display = 'block';
                    inputGroup4.style.display = 'block';
                    inputGroup5.style.display = 'block';
                    inputGroup6.style.display = 'block';
                    inputGroup7.style.display = 'block';
                    inputGroup8.style.display = 'block';
                    inputGroup9.style.display = 'block';
                    inputGroup10.style.display = 'block';
                    inputGroup11.style.display = 'block';
                    inputGroup12.style.display = 'block';
                    inputGroup13.style.display = 'block';
                    inputGroup14.style.display = 'block';
                    inputGroup15.style.display = 'block';
                }
            });
        });
        </script>


    <!-- jQuery -->
</body>
</html>
