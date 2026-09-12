@extends('Template.base')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="m-0">
                        <i class="fa fa-calculator"></i> Kalkulator Nutrisi Bahan
                    </h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Gunakan kalkulator ini untuk menghitung nilai nutrisi bahan makanan berdasarkan:
                    </p>
                    <ul class="text-muted mb-4">
                        <li><strong>Bahan:</strong> Pilih dari database master bahan nutrisi (1,148 item TKPI 2019)</li>
                        <li><strong>Jumlah:</strong> Berapa gram bahan yang digunakan</li>
                        <li><strong>BDD:</strong> Bagian yang Dapat Dimakan (%)  - persentase bagian bahan yang dapat dimakan</li>
                    </ul>
                    <div class="alert alert-info">
                        <strong>Contoh:</strong> Ayam 150g dengan BDD 80% → Bahan yang dapat dimakan = 120g
                    </div>

                    <!-- Nutrition Calculator Widget -->
                    <div id="nutrisi-calculator"></div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="m-0">Informasi Singkat</h5>
                </div>
                <div class="card-body">
                    <h6>Apa itu BDD (Bagian yang Dapat Dimakan)?</h6>
                    <p>
                        BDD adalah persentase bagian bahan makanan yang dapat dimakan setelah proses pembersihan atau pengolahan awal.
                    </p>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Bahan</th>
                                <th>BDD (%)</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Nasi/Beras putih</td>
                                <td>80%</td>
                                <td>Setelah melepas kulit</td>
                            </tr>
                            <tr>
                                <td>Daging ayam</td>
                                <td>100%</td>
                                <td>Dagingnya semua bisa dimakan</td>
                            </tr>
                            <tr>
                                <td>Ikan segar</td>
                                <td>100%</td>
                                <td>Sudah dibersihkan (tanpa tulang besar)</td>
                            </tr>
                            <tr>
                                <td>Sayur bayam</td>
                                <td>85%</td>
                                <td>Setelah buang akar dan daun busuk</td>
                            </tr>
                            <tr>
                                <td>Buah mangga</td>
                                <td>90%</td>
                                <td>Setelah dikupas dan biji dibuang</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('js/nutrisi-calculator.js') }}"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi kalkulator
        const calculator = new NutrisiCalculator('nutrisi-calculator');

        // Initialize Select2 dengan ajax untuk search bahan
        $('#bahan-select').select2({
            placeholder: 'Cari bahan...',
            allowClear: true,
            ajax: {
                url: '/api/master-bahan-nutrisi/list', // TODO: Implementasi endpoint ini
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(item => ({
                            id: item.id,
                            text: item.nama_bahan + ' (' + item.kelompok + ')'
                        }))
                    };
                }
            }
        });
    });
</script>
@endsection
