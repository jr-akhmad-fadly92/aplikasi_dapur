<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .print-card {
            border-radius: 12px;
            border: 1px solid #dee2e6;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .print-card .card-header {
            background: linear-gradient(135deg, #0f4c81 0%, #1572a1 100%);
            color: #fff;
            border-bottom: 0;
        }

        .print-group {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 16px;
            height: 100%;
            background: #fafcfe;
        }

        .print-group h5 {
            margin-bottom: 10px;
        }

        .print-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .print-hint {
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        @include('Template.navbar')
        @include('Template.left-sidebar')

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

            <section class="content">
                <div class="container-fluid">
                    <div class="card print-card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Satu Halaman Untuk Semua Aksi Cetak</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group">
                                        <label for="tanggal_acuan">Tanggal Acuan</label>
                                        <input type="date" id="tanggal_acuan" class="form-control" value="{{ $tanggalAcuan ?? $today }}">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group">
                                        <label for="tanggal_awal">Tanggal Awal Rekap Menu</label>
                                        <input type="date" id="tanggal_awal" class="form-control" value="{{ $tanggalAwal ?? $today }}">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group">
                                        <label for="tanggal_akhir">Tanggal Akhir Rekap Menu</label>
                                        <input type="date" id="tanggal_akhir" class="form-control" value="{{ $tanggalAkhir ?? $today }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-group">
                                        <label for="id_menu">Pilih Menu Untuk Ceklist Masak</label>
                                        <select id="id_menu" class="form-control"></select>
                                        <small class="print-hint">Menu di daftar ini otomatis mengikuti tanggal acuan.</small>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-group">
                                        <label>Info Checklist Penerimaan</label>
                                        <input type="text" class="form-control" value="{{ $checklistPoId ? 'PO ID ' . $checklistPoId : 'Belum ada PO checklist untuk tanggal ini' }}" readonly>
                                        <small class="print-hint">Checklist penerimaan mengikuti tanggal acuan atau ID PO yang dikirim dari halaman lain.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4 print-actions">
                                <button type="button" id="btn-sync-tanggal" class="btn btn-outline-primary">Samakan Semua Tanggal</button>
                                <button type="button" id="btn-open-all" class="btn btn-success">Buka Semua Dokumen</button>
                                <a href="{{ route('mastermenu.index') }}" class="btn btn-secondary">Kembali</a>
                            </div>

                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <div class="print-group">
                                        <h5>Rekap Menu</h5>
                                        <p class="print-hint">Endpoint: laporan-rekap-menu.excel dengan tanggal awal dan akhir.</p>
                                        <div class="print-actions">
                                            <button type="button" class="btn btn-success single-open" data-target="rekap-menu">Download Rekap Menu Excel</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <div class="print-group">
                                        <h5>Ceklist Masak</h5>
                                        <p class="print-hint">Endpoint: /cetak-rekap-ceklist-masak/{id_menu}</p>
                                        <div class="print-actions">
                                            <button type="button" class="btn btn-info single-open" data-target="ceklist-masak">Buka Rekap Ceklist Masak</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <div class="print-group">
                                        <h5>Uji Organoleptik</h5>
                                        <p class="print-hint">Endpoint: checklistKerja/pdfChecklistOrganoleptik/{idmenu} dengan tanggal acuan/menu aktif.</p>
                                        <div class="print-actions">
                                            <button type="button" class="btn btn-danger single-open" data-target="uji-organoleptik">Buka PDF Uji Organoleptik</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <div class="print-group">
                                        <h5>Checklist Penerimaan</h5>
                                        <p class="print-hint">Endpoint mengikuti tombol checklist di halaman laporan penerimaan.</p>
                                        <div class="print-actions">
                                            <button type="button" class="btn btn-warning single-open" data-target="checklist-penerimaan">Buka Checklist Penerimaan (Landscape)</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <div class="print-group">
                                        <h5>Stok Opnam PDF</h5>
                                        <p class="print-hint">Endpoint: /stok_opnam_pdf?tanggal=...</p>
                                        <div class="print-actions">
                                            <button type="button" class="btn btn-secondary single-open" data-target="stok-opnam">Buka Stok Opnam PDF</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <div class="print-group">
                                        <h5>Counter Pax</h5>
                                        <p class="print-hint">Endpoint mengikuti export di halaman counter-pax berdasarkan tanggal acuan.</p>
                                        <div class="print-actions">
                                            <button type="button" class="btn btn-success single-open" data-target="counter-pax-excel">Export Counter Pax Excel</button>
                                            <button type="button" class="btn btn-danger single-open" data-target="counter-pax-pdf">Export Counter Pax PDF</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <div class="print-group">
                                        <h5>Laporan Masak Harian</h5>
                                        <p class="print-hint">Endpoint mengikuti tombol download PDF di halaman menu-laporan-masak-harian berdasarkan tanggal acuan.</p>
                                        <div class="print-actions">
                                            <button type="button" class="btn btn-danger single-open" data-target="laporan-masak-harian-pdf">Export PDF Laporan Masak Harian</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        @include('Template.footer')
    </div>

    @include('Template.script')
    <script>
        (function () {
            const initialTanggalAcuan = @json($tanggalAcuan ?? $today);
            const initialChecklistPoId = @json($checklistPoId);
            const initialSelectedMenuId = @json((string) ($idMenu ?? ''));
            const availableMenus = @json($menuOptionsJson);
            const baseUrls = {
                rekapMenu: '{{ route('laporan-rekap-menu.excel') }}',
                ceklistMasak: '{{ url('/cetak-rekap-ceklist-masak') }}',
                organoleptikPdf: '{{ route('pdfChecklistOrganoleptik', ['idmenu' => '__ID_MENU__']) }}',
                checklistPenerimaan: '{{ url('/cheklist_penerimaan_bgn') }}',
                stokOpnam: '{{ route('stok_opnam_pdf') }}',
                counterPaxExcel: '{{ route('counter-pax.export.excel') }}',
                counterPaxPdf: '{{ route('counter-pax.export.pdf') }}',
                laporanMasakHarianPdf: '{{ route('laporan-masak-harian') }}'
            };

            const fieldTanggalAcuan = document.getElementById('tanggal_acuan');
            const fieldTanggalAwal = document.getElementById('tanggal_awal');
            const fieldTanggalAkhir = document.getElementById('tanggal_akhir');
            const fieldMenu = document.getElementById('id_menu');

            function renderMenuOptions() {
                const selectedTanggal = fieldTanggalAcuan.value;
                const currentValue = fieldMenu.value || initialSelectedMenuId;
                const filteredMenus = availableMenus.filter(function (menu) {
                    return menu.tanggal_kirim === selectedTanggal;
                });

                fieldMenu.innerHTML = '';

                const placeholderOption = document.createElement('option');
                placeholderOption.value = '';
                placeholderOption.textContent = filteredMenus.length > 0
                    ? '-- Pilih Menu --'
                    : '-- Tidak ada menu pada tanggal ini --';
                fieldMenu.appendChild(placeholderOption);

                filteredMenus.forEach(function (menu) {
                    const option = document.createElement('option');
                    option.value = menu.id;
                    option.textContent = menu.label;
                    if (menu.id === currentValue) {
                        option.selected = true;
                    }
                    fieldMenu.appendChild(option);
                });

                if (!filteredMenus.some(function (menu) { return menu.id === fieldMenu.value; })) {
                    fieldMenu.value = '';
                }
            }

            function getUrls() {
                const tanggalAcuan = fieldTanggalAcuan.value;
                const tanggalAwal = fieldTanggalAwal.value || tanggalAcuan;
                const tanggalAkhir = fieldTanggalAkhir.value || tanggalAcuan;
                const idMenu = fieldMenu.value.trim();

                return {
                    rekapMenu: baseUrls.rekapMenu + '?tanggal_awal=' + encodeURIComponent(tanggalAwal) + '&tanggal_akhir=' + encodeURIComponent(tanggalAkhir),
                    ceklistMasak: idMenu ? baseUrls.ceklistMasak + '/' + idMenu : null,
                    organoleptikPdf: idMenu ? baseUrls.organoleptikPdf.replace('__ID_MENU__', encodeURIComponent(idMenu)) + '?tanggal=' + encodeURIComponent(tanggalAcuan) + '&tanggal_awal=' + encodeURIComponent(tanggalAwal) + '&tanggal_akhir=' + encodeURIComponent(tanggalAkhir) : null,
                    checklistPenerimaan: null, // akan di-resolve asynchronously
                    stokOpnam: baseUrls.stokOpnam + '?tanggal=' + encodeURIComponent(tanggalAcuan),
                    counterPaxExcel: baseUrls.counterPaxExcel + '?tanggal=' + encodeURIComponent(tanggalAcuan),
                    counterPaxPdf: baseUrls.counterPaxPdf + '?tanggal=' + encodeURIComponent(tanggalAcuan),
                    laporanMasakHarianPdf: baseUrls.laporanMasakHarianPdf + '?tanggal=' + encodeURIComponent(tanggalAcuan)
                };
            }

            function resolveChecklistPenerimaanUrl(orientation) {
                const tanggalAcuan = fieldTanggalAcuan.value;
                const selectedOrientation = orientation || 'portrait';
                
                return fetch('{{ route('mastermenu.get-checklist-po-id') }}?tanggal=' + encodeURIComponent(tanggalAcuan))
                    .then(response => response.json())
                    .then(data => {
                        if (data.poId) {
                            return baseUrls.checklistPenerimaan + '/' + data.poId + '?orientation=' + encodeURIComponent(selectedOrientation);
                        }
                        return null;
                    })
                    .catch(() => null);
            }

            function openUrl(target) {
                if (target === 'checklistPenerimaan') {
                    resolveChecklistPenerimaanUrl('landscape').then(url => {
                        if (!url) {
                            alert('Parameter untuk dokumen ini belum lengkap. Tidak ada PO ACC untuk tanggal ini.');
                            return;
                        }
                        window.open(url, '_blank');
                    });
                } else {
                    const urls = getUrls();
                    const url = urls[target];

                    if (!url) {
                        alert('Parameter untuk dokumen ini belum lengkap.');
                        return;
                    }

                    window.open(url, '_blank');
                }
            }

            document.getElementById('btn-sync-tanggal').addEventListener('click', function () {
                const tanggalAcuan = fieldTanggalAcuan.value;
                fieldTanggalAwal.value = tanggalAcuan;
                fieldTanggalAkhir.value = tanggalAcuan;
                renderMenuOptions();
            });

            document.getElementById('btn-open-all').addEventListener('click', function () {
                const urls = getUrls();

                Object.keys(urls).forEach(function (key) {
                    if (urls[key]) {
                        window.open(urls[key], '_blank');
                    }
                });

                // Handle checklist penerimaan separately (async)
                resolveChecklistPenerimaanUrl('landscape').then(url => {
                    if (url) {
                        window.open(url, '_blank');
                    }
                });
            });

            const targetMap = {
                'rekap-menu': 'rekapMenu',
                'ceklist-masak': 'ceklistMasak',
                'uji-organoleptik': 'organoleptikPdf',
                'checklist-penerimaan': 'checklistPenerimaan',
                'stok-opnam': 'stokOpnam',
                'counter-pax-excel': 'counterPaxExcel',
                'counter-pax-pdf': 'counterPaxPdf',
                'laporan-masak-harian-pdf': 'laporanMasakHarianPdf'
            };

            document.querySelectorAll('.single-open').forEach(function (button) {
                button.addEventListener('click', function () {
                    const target = button.getAttribute('data-target');
                    openUrl(targetMap[target]);
                });
            });

            fieldTanggalAcuan.addEventListener('input', function () {
                renderMenuOptions();
            });

            fieldTanggalAcuan.addEventListener('change', function () {
                renderMenuOptions();
            });

            renderMenuOptions();
        })();
    </script>
</body>
</html>