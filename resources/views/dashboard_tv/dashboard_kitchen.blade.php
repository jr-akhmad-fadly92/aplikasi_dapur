<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
    <style>
        body { overflow: hidden; }
        .card { height: 100%; }
        .row-cols-2 > .col, .row-cols-md-5 > .col { flex: 1; }
        img { max-width: 100px; }
        .content-wrapper {
            min-height: calc(100vh - 100px); /* Sesuaikan tinggi footer */
            padding-bottom: 10px;
        }
    </style>
</head>
<body class="sidebar-mini sidebar-collapse sidebar-closed">
    <div class="wrapper">
        @include('Template.navbar')
        @include('Template.left-sidebar-dashboard')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark" id="currentTime">{{ $header }}</h1>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <!-- Menu Hari Ini -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h2>Menu Hari Ini</h2>
                                </div>
                                <div class="card-body">
                                    <div class="row row-cols-2 row-cols-md-5 gap-4">
                                        @foreach ([
                                            ['img' => 'nasi.png', 'title' => 'Karbohidrat', 'desc' => $menus->nama_karbohidrat ?? '-', 'kode_a' => $histori_masak_a->hasil_porsi_karbohidrat ?? 0, 'kode_b' => $histori_masak_b->hasil_porsi_karbohidrat ?? 0],
                                            ['img' => 'protein.png', 'title' => 'Protein', 'desc' => $menus->nama_protein ?? '-', 'kode_a' => $histori_masak_a->hasil_porsi_protein ?? 0, 'kode_b' => $histori_masak_b->hasil_porsi_protein ?? 0],
                                            ['img' => 'sayur.png', 'title' => 'Sayur', 'desc' => $menus->nama_sayur ?? '-', 'kode_a' => $histori_masak_a->hasil_porsi_sayur ?? 0, 'kode_b' => $histori_masak_b->hasil_porsi_sayur ?? 0],
                                            ['img' => 'buah.png', 'title' => 'Buah', 'desc' => $menus->nama_buah ?? '-', 'kode_a' => $histori_masak_a->hasil_porsi_buah ?? 0, 'kode_b' => $histori_masak_b->hasil_porsi_buah ?? 0],
                                            ['img' => 'susu.png', 'title' => 'Pelengkap', 'desc' => $menus->nama_susu ?? '-', 'kode_a' => $histori_masak_a->hasil_porsi_susu ?? 0, 'kode_b' => $histori_masak_b->hasil_porsi_susu ?? 0]
                                        ] as $item)
                                            <div class="col">
                                                <div class="card bg-soft-primary h-100 text-center">
                                                    <div class="card-body">
                                                        <img src="{{ asset('image/' . $item['img']) }}" alt="">
                                                        <h4>{{ $item['desc'] }}</h4>
                                                        <h5>Pack A: {{ $item['kode_a'] }} Pack</h5>
                                                        <h5>Pack B: {{ $item['kode_b'] }} Pack</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jumlah Hari Ini & Sudah Packing -->
                    <div class="row mt-4 gap-4">
                        <div class="col">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h2>Jumlah Hari Ini</h2>
                                    <h2><span>{{ $total }} Pack</span></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h2>Sudah Packing</h2>
                                    <h2><span>50 Pack</span></h2>
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
    setInterval(function() {
        location.reload();
    }, 30000); // 30 detik
</script>
</body>
</html>
