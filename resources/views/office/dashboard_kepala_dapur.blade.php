<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
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
                @php
                    $menuAwal = $menuHarianList[0] ?? [
                        'id' => '-',
                        'nama_menu' => 'Belum ada menu',
                        'golongan' => '-',
                        'karbohidrat' => '-',
                        'protein' => '-',
                        'sayur' => '-',
                        'buah' => '-',
                        'suplemen' => '-',
                        'sekolah_penerima' => [],
                        'akg' => [
                            'energi_kcal' => 0,
                            'protein_gram' => 0,
                            'lemak_gram' => 0,
                            'karbo_gram' => 0,
                            'serat_gram' => 0,
                            'natrium_mg' => 0,
                        ],
                    ];
                @endphp
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Filter Tanggal</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('dashboard_kepala_dapur') }}" class="form-inline">
                            <label for="tanggal" class="mr-2">Pilih Tanggal:</label>
                            <input type="date" name="tanggal" id="tanggal" value="{{ $selectedDate }}" class="form-control mr-2" required>
                            <button type="submit" class="btn btn-primary">Tampilkan</button>
                        </form>
                        <small class="text-muted d-block mt-2">Semua data pada halaman ini masih dummy untuk observasi format data.</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title text-white"><i class="fas fa-utensils mr-2"></i>1) Menu Hari Ini ({{ \Carbon\Carbon::parse($selectedDate)->format('d-m-Y') }})</h3>
                                <div class="card-tools text-white" id="menuPaginationInfo">Menu 1 / {{ count($menuHarianList) }}</div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-sm">
                                    <tbody>
                                        <tr>
                                            <th style="width: 220px;">ID Menu</th>
                                            <td id="menuId">{{ $menuAwal['id'] }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width: 220px;">Nama Paket</th>
                                            <td id="menuNamaPaket">{{ $menuAwal['nama_menu'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Golongan</th>
                                            <td id="menuGolongan">{{ $menuAwal['golongan'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Karbohidrat</th>
                                            <td id="menuKarbohidrat">{{ $menuAwal['karbohidrat'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Protein</th>
                                            <td id="menuProtein">{{ $menuAwal['protein'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Sayur</th>
                                            <td id="menuSayur">{{ $menuAwal['sayur'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Buah</th>
                                            <td id="menuBuah">{{ $menuAwal['buah'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Suplemen</th>
                                            <td id="menuSuplemen">{{ $menuAwal['suplemen'] }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header bg-success">
                                <h3 class="card-title text-white"><i class="fas fa-heartbeat mr-2"></i>2) Kadar AKG</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between"><span>Energi</span><strong id="akgEnergi">{{ number_format((float) ($menuAwal['akg']['energi_kcal'] ?? 0), 2, ',', '.') }} kcal</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Protein</span><strong id="akgProtein">{{ number_format((float) ($menuAwal['akg']['protein_gram'] ?? 0), 2, ',', '.') }} g</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Lemak</span><strong id="akgLemak">{{ number_format((float) ($menuAwal['akg']['lemak_gram'] ?? 0), 2, ',', '.') }} g</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Karbohidrat</span><strong id="akgKarbo">{{ number_format((float) ($menuAwal['akg']['karbo_gram'] ?? 0), 2, ',', '.') }} g</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Serat</span><strong id="akgSerat">{{ number_format((float) ($menuAwal['akg']['serat_gram'] ?? 0), 2, ',', '.') }} g</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Natrium</span><strong id="akgNatrium">{{ number_format((float) ($menuAwal['akg']['natrium_mg'] ?? 0), 2, ',', '.') }} mg</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header bg-warning">
                                <h3 class="card-title text-dark"><i class="fas fa-school mr-2"></i>3) Sekolah Penerima</h3>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap mb-0">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Sekolah</th>
                                        <th>Wilayah</th>
                                        <th>Porsi</th>
                                    </tr>
                                    </thead>
                                    <tbody id="tbodySekolahPenerima">
                                    @if(!empty($menuAwal['sekolah_penerima']))
                                        @foreach($menuAwal['sekolah_penerima'] as $index => $sekolah)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $sekolah['nama'] }}</td>
                                                <td>{{ $sekolah['wilayah'] }}</td>
                                                <td>{{ number_format($sekolah['porsi']) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>1</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>0</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header bg-secondary">
                                <h3 class="card-title text-white"><i class="fas fa-truck-loading mr-2"></i>4) Bahan Baku Datang Hari Ini</h3>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-8 col-lg-7">
                                        <label for="searchBahanDatang" class="mb-1">Pencarian Bahan Baku Datang</label>
                                        <input type="text" id="searchBahanDatang" class="form-control" placeholder="Ketik nama bahan baku...">
                                    </div>
                                </div>
                                <div class="table-responsive">
                                <table id="tableBahanDatang" class="table table-hover text-nowrap mb-0" style="width: 100%">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Bahan</th>
                                        <th>Supplier</th>
                                        <th>Qty</th>
                                        <th>ETA</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($bahanDatangHariIni as $index => $bahan)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $bahan['bahan'] }}</td>
                                            <td>{{ $bahan['supplier'] }}</td>
                                            <td>{{ $bahan['qty'] }}</td>
                                            <td>{{ $bahan['eta'] }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>Rp {{ number_format($dana['saldo_saat_ini'], 0, ',', '.') }}</h3>
                                <p>5) Jumlah Dana Saat Ini</p>
                            </div>
                            <div class="icon"><i class="fas fa-wallet"></i></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>Rp {{ number_format($dana['pengeluaran_hari_ini'], 0, ',', '.') }}</h3>
                                <p>Pengeluaran Dana Hari Ini</p>
                            </div>
                            <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>Rp {{ number_format($dana['sisa_setelah_pengeluaran'], 0, ',', '.') }}</h3>
                                <p>Sisa Dana Setelah Pengeluaran</p>
                            </div>
                            <div class="icon"><i class="fas fa-piggy-bank"></i></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card card-outline card-success">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-tags mr-2"></i>Table HET Terupdate
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6 col-lg-4">
                                        <label for="searchBahanBaku" class="mb-1">Pencarian Bahan Baku</label>
                                        <input type="text" id="searchBahanBaku" class="form-control" placeholder="Ketik nama bahan baku...">
                                    </div>
                                </div>
                                <table id="tableHetTerupdate" class="table table-bordered table-hover" style="width: 100%">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID Bahan</th>
                                        <th>Nama Bahan</th>
                                        <th>Tanggal Update</th>
                                        <th>Harga HET</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($hetTerupdate as $index => $het)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $het->id_bahan }}</td>
                                            <td>{{ $het->nama_bahan }}</td>
                                            <td>{{ \Carbon\Carbon::parse($het->tanggal_update)->format('d-m-Y') }}</td>
                                            <td>Rp {{ number_format((int) $het->harga_het, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
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
        $(document).ready(function () {
        const menuHarianList = @json($menuHarianList);
        let menuIndex = 0;

        function formatNumber(value) {
            const num = Number(value || 0);
            return num.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function escapeHtml(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderSekolahPenerima(list) {
            let html = '';
            const sekolahList = Array.isArray(list) ? list : [];

            if (sekolahList.length === 0) {
                html = '<tr><td>1</td><td>-</td><td>-</td><td>0</td></tr>';
                $('#tbodySekolahPenerima').html(html);
                return;
            }

            sekolahList.forEach(function (sekolah, idx) {
                const nama = escapeHtml(sekolah.nama || '-');
                const wilayah = escapeHtml(sekolah.wilayah || '-');
                const porsi = Number(sekolah.porsi || 0).toLocaleString('id-ID');

                html += '<tr>' +
                    '<td>' + (idx + 1) + '</td>' +
                    '<td>' + nama + '</td>' +
                    '<td>' + wilayah + '</td>' +
                    '<td>' + porsi + '</td>' +
                    '</tr>';
            });

            $('#tbodySekolahPenerima').html(html);
        }

        function renderMenu(index) {
            const menu = menuHarianList[index] || {};
            const akg = menu.akg || {};

            $('#menuId').text(menu.id || '-');
            $('#menuNamaPaket').text(menu.nama_menu || '-');
            $('#menuGolongan').text(menu.golongan || '-');
            $('#menuKarbohidrat').text(menu.karbohidrat || '-');
            $('#menuProtein').text(menu.protein || '-');
            $('#menuSayur').text(menu.sayur || '-');
            $('#menuBuah').text(menu.buah || '-');
            $('#menuSuplemen').text(menu.suplemen || '-');

            $('#akgEnergi').text(formatNumber(akg.energi_kcal) + ' kcal');
            $('#akgProtein').text(formatNumber(akg.protein_gram) + ' g');
            $('#akgLemak').text(formatNumber(akg.lemak_gram) + ' g');
            $('#akgKarbo').text(formatNumber(akg.karbo_gram) + ' g');
            $('#akgSerat').text(formatNumber(akg.serat_gram) + ' g');
            $('#akgNatrium').text(formatNumber(akg.natrium_mg) + ' mg');
            renderSekolahPenerima(menu.sekolah_penerima || []);

            const current = index + 1;
            const total = menuHarianList.length;
            $('#menuPaginationInfo').text('Menu ' + current + ' / ' + total);
        }

        if (menuHarianList.length > 0) {
            renderMenu(0);
        }

        if (menuHarianList.length > 1) {
            setInterval(function () {
                menuIndex = (menuIndex + 1) % menuHarianList.length;
                renderMenu(menuIndex);
            }, 3000);
        }

            const hetTable = $('#tableHetTerupdate').DataTable({
                pageLength: 10,
                lengthChange: false,
                searching: true,
                ordering: false,
                info: true,
                autoWidth: false,
                dom: 'rtip',
                language: {
                    paginate: {
                        previous: 'Prev',
                        next: 'Next'
                    }
                }
            });

            $('#searchBahanBaku').on('keyup change', function () {
                hetTable.column(2).search(this.value).draw();
            });

            setInterval(function () {
                const searchValue = ($('#searchBahanBaku').val() || '').trim();
                if (searchValue !== '') {
                    return;
                }

                const pageInfo = hetTable.page.info();
                if (!pageInfo || pageInfo.pages <= 1) {
                    return;
                }

                let nextPage = pageInfo.page + 1;
                if (nextPage >= pageInfo.pages) {
                    nextPage = 0;
                }

                hetTable.page(nextPage).draw('page');
            }, 3000);

            const bahanDatangTable = $('#tableBahanDatang').DataTable({
                pageLength: 10,
                lengthChange: false,
                searching: true,
                ordering: false,
                info: true,
                autoWidth: false,
                dom: 'rtip',
                language: {
                    paginate: {
                        previous: 'Prev',
                        next: 'Next'
                    }
                }
            });

            $('#searchBahanDatang').on('keyup change', function () {
                bahanDatangTable.column(1).search(this.value).draw();
            });
        });
    </script>
</body>
</html>
