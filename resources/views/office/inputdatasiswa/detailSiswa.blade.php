<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <i class="fa fa-solid fa-user img-circle elevation-2 fa-2x"></i>
                    </div>

                    <h3 class="profile-username text-center">{{ $siswa->nama ?? 'Nama tidak tersedia' }}</h3>

                    <p class="text-muted text-center">Siswa</p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>NISN</b> <a class="float-right">{{ $siswa->nisn ?? 'NISN tidak tersedia' }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Kelas</b> <a class="float-right">{{ $siswa->kelas ?? 'Belum diisi' }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Sekolah</b> <a
                                class="float-right">{{ optional($siswa->sekolah)->nama_sekolah ?? 'Sekolah tidak ditemukan' }}</a>
                        </li>
                    </ul>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- About Me Box -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">About Me</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <strong><i class="fas fa-graduation-cap mr-1"></i> Pendidikan</strong>
                    <p class="text-muted">
                        {{ optional($siswa->sekolah)->nama_sekolah ?? 'Sekolah tidak ditemukan' }} - Kelas
                        {{ $siswa->kelas ?? 'Belum diisi' }}
                    </p>
                    <hr>

                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Tempat, Tanggal Lahir</strong>
                    <p class="text-muted">
                        {{ isset($siswa->tempat_lahir) ? $siswa->tempat_lahir : 'Belum diisi' }}{{ isset($siswa->tanggal_lahir) ? ', ' . \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d F Y') : '' }}
                    </p>
                    <hr>

                    <strong><i class="fas fa-venus-mars mr-1"></i> Identitas</strong>
                    <p class="text-muted">
                        <span
                            class="tag tag-info">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin == 'P' ? 'Perempuan' : 'Belum diisi') }}</span>
                        <span
                            class="tag tag-warning">{{ isset($siswa->golongan_darah) ? strtoupper($siswa->golongan_darah) : 'Darah: Belum diisi' }}</span>
                        <span
                            class="tag tag-success">{{ isset($siswa->golongan_penerimaan) ? strtoupper($siswa->golongan_penerimaan) : 'Belum diisi' }}</span>
                    </p>
                    <hr>

                    <strong><i class="far fa-file-alt mr-1"></i> Catatan</strong>
                    <p class="text-muted">{{ $siswa->keterangan ?? 'Tidak ada catatan khusus untuk siswa ini.' }}</p>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Data Siswa
                                Umum</a></li>
                        <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Data Kesehatan
                                Siswa</a></li>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Data Siswa Umum Tab -->
                        <div class="active tab-pane" id="activity">
                            <!-- Informasi Identitas Siswa -->
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-user"></i> Informasi Identitas Siswa
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-primary"><i
                                                        class="fas fa-id-card"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">NISN</span>
                                                    <span class="info-box-number">
                                                        {{ $siswa->nisn ?? 'Belum diisi' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-info"><i
                                                        class="fas fa-user-circle"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Nama Lengkap</span>
                                                    <span class="info-box-number">
                                                        {{ $siswa->nama ?? 'Belum diisi' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success"><i
                                                        class="fas fa-chalkboard-teacher"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Kelas</span>
                                                    <span class="info-box-number">
                                                        {{ $siswa->kelas ?? 'Belum diisi' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-warning"><i
                                                        class="fas fa-venus-mars"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Jenis Kelamin</span>
                                                    <span class="info-box-number">
                                                        {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin == 'P' ? 'Perempuan' : 'Belum diisi') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Sekolah -->
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-school"></i> Informasi Sekolah
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-navy"><i
                                                        class="fas fa-building"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">NPSN Sekolah</span>
                                                    <span class="info-box-number">
                                                        {{ $siswa->npsn ?? 'Belum diisi' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-purple"><i
                                                        class="fas fa-graduation-cap"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Nama Sekolah</span>
                                                    <span class="info-box-number">
                                                        {{ optional($siswa->sekolah)->nama_sekolah ?? 'Sekolah tidak ditemukan' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Informasi Keluarga -->
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-users"></i> Informasi Keluarga & Kontak Darurat
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success"><i
                                                        class="fas fa-user-friends"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Nama Orang Tua / Wali</span>
                                                    <span class="info-box-number">
                                                        {{ $siswa->nama_orangtua ?? 'Belum diisi' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-warning"><i
                                                        class="fas fa-phone-alt"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Nomor Telepon Darurat</span>
                                                    <span class="info-box-number">
                                                        {{ $siswa->nomor_telp_emergency ?? 'Belum diisi' }}
                                                    </span>
                                                    @if($siswa->nomor_telp_emergency)
                                                        <div class="progress">
                                                            <div class="progress-bar bg-warning" style="width: 100%"></div>
                                                        </div>
                                                        <span class="progress-description">
                                                            <small class="text-muted">Dapat dihubungi untuk keadaan
                                                                darurat</small>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- Keterangan Tambahan -->
                            @if($siswa->keterangan)
                                <div class="card card-secondary">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-sticky-note"></i> Keterangan Tambahan
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="callout callout-info">
                                            <h5><i class="fas fa-info"></i> Catatan:</h5>
                                            {{ $siswa->keterangan }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Informasi Timestamp -->
                            <div class="card card-light">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-clock"></i> Informasi Data
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Data Dibuat:</strong><br>
                                            <p class="text-muted">
                                                {{ $siswa->created_at ? $siswa->created_at->format('d F Y, H:i') : 'Tidak tersedia' }}
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Terakhir Diupdate:</strong><br>
                                            <p class="text-muted">
                                                {{ $siswa->updated_at ? $siswa->updated_at->format('d F Y, H:i') : 'Tidak tersedia' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="timeline">
                            <!-- Data Fisik Siswa -->
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-weight"></i> Data Fisik Siswa
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-info"><i class="fas fa-weight"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Berat Badan</span>
                                                    <span class="info-box-number">
                                                        {{ $siswa->berat_badan ? $siswa->berat_badan . ' kg' : 'Belum diisi' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-primary"><i
                                                        class="fas fa-ruler-vertical"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Tinggi Badan</span>
                                                    <span class="info-box-number">
                                                        {{ $siswa->tinggi_badan ? $siswa->tinggi_badan . ' cm' : 'Belum diisi' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Kesehatan Umum -->
                            <div class="card card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-heartbeat"></i> Data Kesehatan Umum
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-danger"><i class="fas fa-tint"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Golongan Darah</span>
                                                    <span class="info-box-number">
                                                        {{ $siswa->golongan_darah ? strtoupper($siswa->golongan_darah) : 'Belum diisi' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success"><i
                                                        class="fas fa-clipboard-list"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Golongan Penerimaan</span>
                                                    <span class="info-box-number">
                                                        {{ $siswa->golongan_penerimaan ? 'Golongan ' . strtoupper($siswa->golongan_penerimaan) : 'Belum diisi' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Riwayat Penyakit -->
                            <div class="card card-danger">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-file-medical"></i> Riwayat Penyakit & Alergi
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card card-outline card-secondary">
                                                <div class="card-header">
                                                    <h6 class="card-title">
                                                        <i class="fas fa-dna mr-1"></i> Riwayat Penyakit Bawaan
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="callout callout-secondary">
                                                        {{ $siswa->riwayat_penyakit_bawaan ?? 'Tidak ada riwayat penyakit bawaan' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card card-outline card-warning">
                                                <div class="card-header">
                                                    <h6 class="card-title">
                                                        <i class="fas fa-virus mr-1"></i> Riwayat Penyakit Menular
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="callout callout-warning">
                                                        {{ $siswa->riwayat_penyakit_menular ?? 'Tidak ada riwayat penyakit menular' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card card-outline card-danger">
                                                <div class="card-header">
                                                    <h6 class="card-title">
                                                        <i class="fas fa-exclamation-circle mr-1"></i> Informasi Alergi
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="callout callout-danger">
                                                        {{ $siswa->alergi ?? 'Tidak ada alergi yang diketahui' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- Ringkasan Status Kesehatan -->
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-chart-pie"></i> Ringkasan Status Kesehatan
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="progress-group">
                                                Kelengkapan Data Fisik
                                                <span class="float-right">
                                                    <b>{{ ($siswa->berat_badan && $siswa->tinggi_badan) ? '100' : (($siswa->berat_badan || $siswa->tinggi_badan) ? '50' : '0') }}%</b>
                                                </span>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar bg-primary"
                                                        style="width: {{ ($siswa->berat_badan && $siswa->tinggi_badan) ? '100' : (($siswa->berat_badan || $siswa->tinggi_badan) ? '50' : '0') }}%">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="progress-group">
                                                Kelengkapan Data Kesehatan
                                                <span class="float-right">
                                                    <b>{{ (($siswa->golongan_darah ? 1 : 0) + ($siswa->riwayat_penyakit_bawaan ? 1 : 0) + ($siswa->riwayat_penyakit_menular ? 1 : 0) + ($siswa->alergi ? 1 : 0)) * 25 }}%</b>
                                                </span>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar bg-success"
                                                        style="width: {{ (($siswa->golongan_darah ? 1 : 0) + ($siswa->riwayat_penyakit_bawaan ? 1 : 0) + ($siswa->riwayat_penyakit_menular ? 1 : 0) + ($siswa->alergi ? 1 : 0)) * 25 }}%">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="progress-group">
                                                Kelengkapan Kontak Darurat
                                                <span class="float-right">
                                                    <b>{{ $siswa->nomor_telp_emergency ? '100' : '0' }}%</b>
                                                </span>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar bg-warning"
                                                        style="width: {{ $siswa->nomor_telp_emergency ? '100' : '0' }}%">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $totalFields = 8; // Total field kesehatan
                                        $filledFields = 0;
                                        if ($siswa->berat_badan)
                                            $filledFields++;
                                        if ($siswa->tinggi_badan)
                                            $filledFields++;
                                        if ($siswa->riwayat_penyakit_bawaan)
                                            $filledFields++;
                                        if ($siswa->riwayat_penyakit_menular)
                                            $filledFields++;
                                        if ($siswa->alergi)
                                            $filledFields++;
                                        if ($siswa->nomor_telp_emergency)
                                            $filledFields++;
                                        if ($siswa->golongan_darah)
                                            $filledFields++;
                                        if ($siswa->golongan_penerimaan)
                                            $filledFields++;
                                        $completionPercentage = round(($filledFields / $totalFields) * 100);
                                    @endphp

                                    <div
                                        class="callout {{ $completionPercentage >= 80 ? 'callout-success' : ($completionPercentage >= 50 ? 'callout-warning' : 'callout-danger') }}">
                                        <h5>
                                            <i class="fas fa-info"></i> Status Kelengkapan Data Kesehatan:
                                            <strong>{{ $completionPercentage }}%</strong>
                                        </h5>
                                        <p>
                                            {{ $filledFields }} dari {{ $totalFields }} field data kesehatan telah
                                            diisi.
                                            @if($completionPercentage < 100)
                                                Silakan lengkapi data yang masih kosong untuk profil kesehatan yang lebih
                                                lengkap.
                                            @else
                                                Data kesehatan siswa sudah lengkap!
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>