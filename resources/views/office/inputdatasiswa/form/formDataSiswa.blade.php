<!-- Form Input Data Siswa -->
<form id="form_data_siswa">
    @csrf
    @if(isset($siswa))
        <input type="hidden" name="id" value="{{ $siswa->id }}">
    @endif

    <div class="row">
        @if(isset($selected_sekolah) && $selected_sekolah)
            <!-- Mode Direct: Sekolah sudah dipilih, tampilkan info saja -->
            <div class="col-md-6">
                <div class="form-group">
                    <label><i class="fas fa-school mr-1"></i>Sekolah Terpilih</label>
                    <div class="alert alert-info mb-0" style="background-color: #17a2b8; border-color: #17a2b8;">
                        <i class="fas fa-info-circle mr-2 text-white"></i>
                        <strong class="text-white">{{ $selected_sekolah->nama_sekolah }}</strong><br>
                        <small class="text-white">NPSN: {{ $selected_sekolah->npsn }}</small>   
                    </div>
                    <!-- Hidden input untuk sekolah yang terpilih -->
                    <input type="hidden" name="sekolah_id" value="{{ $selected_sekolah->id }}">
                    <input type="hidden" name="npsn" value="{{ $selected_sekolah->npsn }}">
                </div>
            </div>
        @else
            <!-- Mode Normal: Pilih sekolah dari dropdown -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="npsn"><i class="fas fa-school mr-1"></i>NPSN Sekolah <span class="text-muted">(Optional)</span></label>
                    <select class="form-control" name="npsn" id="npsn">
                        <option value="">-- Pilih Sekolah --</option>
                        @if(isset($sekolah_list))
                            @foreach($sekolah_list as $sekolah)
                                <option value="{{ $sekolah->npsn }}" @if(isset($siswa) && $siswa->npsn == $sekolah->npsn) selected @endif>
                                    {{ $sekolah->nama_sekolah }} ({{ $sekolah->npsn }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        @endif

        <div class="col-md-6">
            <div class="form-group">
                <label for="nisn"><i class="fas fa-id-card mr-1"></i>NISN Siswa <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nisn" id="nisn"
                    value="@if(isset($siswa)){{ $siswa->nisn }}@endif" placeholder="Masukkan NISN siswa" maxlength="20"
                    required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="nama"><i class="fas fa-user mr-1"></i>Nama Siswa <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nama" id="nama"
                    value="@if(isset($siswa)){{ $siswa->nama }}@endif" placeholder="Masukkan nama lengkap siswa"
                    maxlength="255" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="kelas"><i class="fas fa-chalkboard mr-1"></i>Kelas <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="kelas" id="kelas"
                    value="@if(isset($siswa)){{ $siswa->kelas }}@endif" placeholder="Contoh: 7A, 6B" maxlength="10"
                    required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="jenis_kelamin"><i class="fas fa-venus-mars mr-1"></i>Jenis Kelamin <span class="text-danger">*</span></label>
                <select class="form-control" name="jenis_kelamin" id="jenis_kelamin" required>
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="L" @if(isset($siswa) && $siswa->jenis_kelamin == 'L') selected @endif>Laki-laki
                    </option>
                    <option value="P" @if(isset($siswa) && $siswa->jenis_kelamin == 'P') selected @endif>Perempuan
                    </option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="nama_orangtua"><i class="fas fa-users mr-1"></i>Nama Orang Tua/Wali</label>
                <input type="text" class="form-control" name="nama_orangtua" id="nama_orangtua"
                    value="@if(isset($siswa)){{ $siswa->nama_orangtua }}@endif"
                    placeholder="Masukkan nama orang tua/wali" maxlength="255">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="keterangan"><i class="fas fa-sticky-note mr-1"></i>Keterangan</label>
                <textarea class="form-control" name="keterangan" id="keterangan" rows="3"
                    placeholder="Masukkan keterangan tambahan jika ada">@if(isset($siswa)){{ $siswa->keterangan }}@endif</textarea>
            </div>
        </div>
    </div>

    <!-- Data Fisik & Kelahiran -->
    <div class="card card-outline card-info mb-3">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-user-circle mr-2"></i>Data Fisik & Kelahiran</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="berat_badan"><i class="fas fa-weight mr-1"></i>Berat Badan (kg)</label>
                        <input type="number" class="form-control" name="berat_badan" id="berat_badan" step="0.1" min="0" max="999.99"
                            value="{{ isset($siswa) && $siswa->berat_badan ? $siswa->berat_badan : '' }}" placeholder="Contoh: 45.5">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tinggi_badan"><i class="fas fa-ruler-vertical mr-1"></i>Tinggi Badan (cm)</label>
                        <input type="number" class="form-control" name="tinggi_badan" id="tinggi_badan" step="0.1" min="0" max="999.99"
                            value="{{ isset($siswa) && $siswa->tinggi_badan ? $siswa->tinggi_badan : '' }}" placeholder="Contoh: 150.5">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tanggal_lahir"><i class="fas fa-calendar-alt mr-1"></i>Tanggal Lahir</label>
                        <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir"
                            value="{{ isset($siswa) && $siswa->tanggal_lahir ? $siswa->tanggal_lahir : '' }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tempat_lahir"><i class="fas fa-map-marker-alt mr-1"></i>Tempat Lahir</label>
                        <input type="text" class="form-control" name="tempat_lahir" id="tempat_lahir" maxlength="255"
                            value="{{ isset($siswa) && $siswa->tempat_lahir ? $siswa->tempat_lahir : '' }}" placeholder="Contoh: Jakarta">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Kesehatan -->
    <div class="card card-outline card-warning mb-3">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-heartbeat mr-2"></i>Data Kesehatan</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="riwayat_penyakit_bawaan"><i class="fas fa-file-medical mr-1"></i>Riwayat Penyakit Bawaan</label>
                        <textarea class="form-control" name="riwayat_penyakit_bawaan" id="riwayat_penyakit_bawaan" rows="3"
                            placeholder="Tuliskan riwayat penyakit bawaan atau tulis 'Tidak ada'">{{ isset($siswa) && $siswa->riwayat_penyakit_bawaan ? $siswa->riwayat_penyakit_bawaan : '' }}</textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="riwayat_penyakit_menular"><i class="fas fa-virus mr-1"></i>Riwayat Penyakit Menular</label>
                        <textarea class="form-control" name="riwayat_penyakit_menular" id="riwayat_penyakit_menular" rows="3"
                            placeholder="Tuliskan riwayat penyakit menular atau tulis 'Tidak ada'">{{ isset($siswa) && $siswa->riwayat_penyakit_menular ? $siswa->riwayat_penyakit_menular : '' }}</textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="alergi"><i class="fas fa-exclamation-triangle mr-1"></i>Alergi</label>
                        <textarea class="form-control" name="alergi" id="alergi" rows="3"
                            placeholder="Tuliskan alergi yang dimiliki atau tulis 'Tidak ada'">{{ isset($siswa) && $siswa->alergi ? $siswa->alergi : '' }}</textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="golongan_darah"><i class="fas fa-tint mr-1"></i>Golongan Darah</label>
                        <select class="form-control" name="golongan_darah" id="golongan_darah">
                            <option value="">-- Pilih Golongan Darah --</option>
                            <option value="A" {{ isset($siswa) && $siswa->golongan_darah == 'A' ? 'selected' : '' }}>A</option>
                            <option value="B" {{ isset($siswa) && $siswa->golongan_darah == 'B' ? 'selected' : '' }}>B</option>
                            <option value="AB" {{ isset($siswa) && $siswa->golongan_darah == 'AB' ? 'selected' : '' }}>AB</option>
                            <option value="O" {{ isset($siswa) && $siswa->golongan_darah == 'O' ? 'selected' : '' }}>O</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Kontak & Administrasi -->
    <div class="card card-outline card-success mb-3">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-phone mr-2"></i>Data Kontak & Administrasi</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nomor_telp_emergency"><i class="fas fa-phone-alt mr-1"></i>Nomor Telepon Darurat</label>
                        <input type="text" class="form-control" name="nomor_telp_emergency" id="nomor_telp_emergency" 
                            maxlength="15" pattern="[0-9+\-\s\(\)]{10,15}"
                            value="{{ isset($siswa) && $siswa->nomor_telp_emergency ? $siswa->nomor_telp_emergency : '' }}" 
                            placeholder="Contoh: 081234567890">
                        <small class="text-muted">Format: 10-15 karakter (angka, +, -, spasi, kurung)</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="golongan_penerimaan"><i class="fas fa-clipboard-list mr-1"></i>Golongan Penerimaan</label>
                        <select class="form-control" name="golongan_penerimaan" id="golongan_penerimaan">
                            <option value="">-- Pilih Golongan Penerimaan --</option>
                            <option value="A" {{ isset($siswa) && $siswa->golongan_penerimaan == 'A' ? 'selected' : '' }}>Golongan A</option>
                            <option value="B" {{ isset($siswa) && $siswa->golongan_penerimaan == 'B' ? 'selected' : '' }}>Golongan B</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <small class="text-muted">
                <i class="fas fa-info-circle mr-1"></i>
                <span class="text-danger">*</span> Field yang wajib diisi
                @if(isset($selected_sekolah) && $selected_sekolah)
                    <br><i class="fas fa-check-circle text-success mr-1"></i>
                    Siswa akan otomatis terdaftar di <strong>{{ $selected_sekolah->nama_sekolah }}</strong>
                @endif
            </small>
        </div>
    </div>
</form>

<script>
    $(document).ready(function () {
        // Validasi form sebelum submit
        $('#form_data_siswa').on('submit', function (e) {
            e.preventDefault();

            // Validasi NISN
            if ($('#nisn').val().trim() === '') {
                $.alert({
                    title: 'Peringatan',
                    content: 'NISN siswa wajib diisi',
                    type: 'orange'
                });
                $('#nisn').focus();
                return false;
            }

            // Validasi Nama
            if ($('#nama').val().trim() === '') {
                $.alert({
                    title: 'Peringatan',
                    content: 'Nama siswa wajib diisi',
                    type: 'orange'
                });
                $('#nama').focus();
                return false;
            }

            // Validasi Kelas
            if ($('#kelas').val().trim() === '') {
                $.alert({
                    title: 'Peringatan',
                    content: 'Kelas wajib diisi',
                    type: 'orange'
                });
                $('#kelas').focus();
                return false;
            }

            // Validasi Jenis Kelamin
            if ($('#jenis_kelamin').val() === '') {
                $.alert({
                    title: 'Peringatan',
                    content: 'Jenis kelamin wajib dipilih',
                    type: 'orange'
                });
                $('#jenis_kelamin').focus();
                return false;
            }

            // Validasi Berat Badan (jika diisi)
            var beratBadan = $('#berat_badan').val();
            if (beratBadan !== '' && (isNaN(beratBadan) || beratBadan < 0 || beratBadan > 999.99)) {
                $.alert({
                    title: 'Peringatan',
                    content: 'Berat badan harus berupa angka antara 0-999.99 kg',
                    type: 'orange'
                });
                $('#berat_badan').focus();
                return false;
            }

            // Validasi Tinggi Badan (jika diisi)
            var tinggiBadan = $('#tinggi_badan').val();
            if (tinggiBadan !== '' && (isNaN(tinggiBadan) || tinggiBadan < 0 || tinggiBadan > 999.99)) {
                $.alert({
                    title: 'Peringatan',
                    content: 'Tinggi badan harus berupa angka antara 0-999.99 cm',
                    type: 'orange'
                });
                $('#tinggi_badan').focus();
                return false;
            }

            // Validasi Nomor Telepon Emergency (jika diisi)
            var nomorTelp = $('#nomor_telp_emergency').val();
            if (nomorTelp !== '' && (!/^[0-9+\-\s\(\)]{10,15}$/.test(nomorTelp))) {
                $.alert({
                    title: 'Peringatan',
                    content: 'Nomor telepon darurat harus 10-15 karakter (angka, +, -, spasi, kurung)',
                    type: 'orange'
                });
                $('#nomor_telp_emergency').focus();
                return false;
            }

            return true;
        });

        // Auto focus ke field pertama yang relevan
        @if(isset($selected_sekolah) && $selected_sekolah)
            // Jika sekolah sudah dipilih, fokus ke NISN
            $('#nisn').focus();
        @else
            // Jika belum ada sekolah, fokus ke dropdown sekolah
            $('#npsn').focus();
        @endif

        // Format input nomor telepon (angka, +, -, spasi, kurung)
        $('#nomor_telp_emergency').on('input', function() {
            this.value = this.value.replace(/[^0-9+\-\s\(\)]/g, '');
        });

        // Validasi real-time untuk berat badan
        $('#berat_badan').on('input', function() {
            var value = parseFloat(this.value);
            if (this.value !== '' && (isNaN(value) || value < 0 || value > 999.99)) {
                $(this).addClass('is-invalid');
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback">Berat badan harus antara 0-999.99 kg</div>');
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        // Validasi real-time untuk tinggi badan
        $('#tinggi_badan').on('input', function() {
            var value = parseFloat(this.value);
            if (this.value !== '' && (isNaN(value) || value < 0 || value > 999.99)) {
                $(this).addClass('is-invalid');
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback">Tinggi badan harus antara 0-999.99 cm</div>');
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        // Validasi real-time untuk nomor telepon
        $('#nomor_telp_emergency').on('input', function() {
            if (this.value !== '' && !/^[0-9+\-\s\(\)]{10,15}$/.test(this.value)) {
                $(this).addClass('is-invalid');
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback">Nomor telepon harus 10-15 karakter (angka, +, -, spasi, kurung)</div>');
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        });
    });
</script>