<!DOCTYPE html>
<html lang="en">
<head>
    <title>Input Pengiriman Otomatis</title>
    @include('Template.head')
    <style>
        .form-control {
            height: 40px;
            width: 100%;
        }

        .border-left-primary {
            border-left: 4px solid #4e73df;
        }

        .border-left-warning {
            border-left: 4px solid #ffc107;
        }

        .list-group-item {
            padding: 0.75rem 1.25rem;
        }

        .card {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 0.35rem;
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
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">Input Pengiriman Otomatis</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-truck"></i> Input Pengiriman Otomatis - Menu {{ $menu->menu ?? $id_menu }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('pengajuan_po.simpan_bulk_pengiriman') }}" method="POST" id="bulkForm">
                                        @csrf

                                        <input type="hidden" name="id_po" value="{{ $po->id }}">
                                        <input type="hidden" name="id_menu" value="{{ $id_menu }}">

                                        @if($sorted->isEmpty())
                                            <div class="alert alert-info">
                                                Tidak ada item yang perlu diinput pengiriman
                                            </div>
                                        @else
                                            <!-- Select All / Deselect All Buttons -->
                                            <div class="mb-3">
                                                <button type="button" class="btn btn-outline-primary btn-sm" id="selectAllBtn">
                                                    <i class="fas fa-check-circle"></i> Pilih Semua
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm" id="deselectAllBtn">
                                                    <i class="fas fa-times-circle"></i> Batalkan Semua
                                                </button>
                                            </div>

                                            <!-- Unified date/time inputs at the top -->
                                            <div class="card mb-4 border-left-warning">
                                                <div class="card-header bg-warning">
                                                    <h6 class="mb-0"><i class="fas fa-clock"></i> Setting Tanggal & Jam Pengiriman</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="tanggal_kirim">
                                                                    <i class="fas fa-calendar"></i> Tanggal Kirim
                                                                </label>
                                                                <input type="date" 
                                                                    class="form-control" 
                                                                    id="tanggal_kirim"
                                                                    name="tanggal_kirim"
                                                                    value="{{ $default_tanggal_kirim }}"
                                                                    required>
                                                                <small class="text-muted d-block mt-1">
                                                                    <i class="fas fa-info-circle"></i> Default: H-1 dari tanggal menu ({{ \Carbon\Carbon::parse($menu->tanggal_kirim)->translatedFormat('d-m-Y') ?? '-' }})
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="jam_kirim">
                                                                    <i class="fas fa-clock"></i> Jam Kirim
                                                                </label>
                                                                <input type="time" 
                                                                    class="form-control" 
                                                                    id="jam_kirim"
                                                                    name="jam_kirim"
                                                                    required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="tanggal_digunakan">
                                                                    <i class="fas fa-calendar-check"></i> Tanggal Digunakan
                                                                </label>
                                                                <input type="date" 
                                                                    class="form-control" 
                                                                    id="tanggal_digunakan"
                                                                    name="tanggal_digunakan"
                                                                    value="{{ \Carbon\Carbon::parse($menu->tanggal_kirim)->format('Y-m-d') ?? date('Y-m-d') }}"
                                                                    required>
                                                                <small class="text-muted d-block mt-1">
                                                                    <i class="fas fa-info-circle"></i> Default: Tanggal menu
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Recipe groups with items -->
                                            @foreach($sorted as $resep_name => $items)
                                                <div class="card mb-3 border-left-primary">
                                                    <div class="card-header bg-light">
                                                        <h6 class="mb-0">
                                                            <i class="fas fa-layer-group"></i> {{ $resep_name }}
                                                            <span class="badge badge-secondary float-right">{{ $items->count() }} item</span>
                                                        </h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <!-- Items list with checkboxes -->
                                                        <div class="list-group list-group-sm">
                                                            @foreach($items as $item)
                                                                <div class="list-group-item bg-white">
                                                                    <div class="row align-items-top mb-2">
                                                                        <div class="col-auto">
                                                                            <input type="checkbox" class="form-check-input item-checkbox" 
                                                                                name="items[]" 
                                                                                value="{{ $item->id_rincian }}" 
                                                                                data-item-id="{{ $item->id_rincian }}"
                                                                                checked>
                                                                        </div>
                                                                        <div class="col">
                                                                            <strong>{{ $item->bahan }}</strong>
                                                                            <br>
                                                                            <small class="text-muted">
                                                                                <i class="fas fa-cube"></i> Satuan: {{ $item->satuan ?? '(ID: ' . $item->id_satuan . ')' }}
                                                                            </small>
                                                                        </div>
                                                                        <div class="col-auto">
                                                                            <button type="button" class="btn btn-sm btn-outline-secondary toggle-edit-btn" 
                                                                                data-item-id="{{ $item->id_rincian }}"
                                                                                onclick="toggleEditForm({{ $item->id_rincian }})">
                                                                                <i class="fas fa-edit"></i> Edit
                                                                            </button>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Editable fields (hidden by default) -->
                                                                    <div class="edit-form-container" id="edit-item-{{ $item->id_rincian }}" style="display: none;">
                                                                        <div class="card card-body bg-light mt-2">
                                                                            <h6 class="mb-3">Edit - {{ $item->bahan }}</h6>
                                                                            <div class="row">
                                                                                <div class="col-md-4">
                                                                                    <div class="form-group">
                                                                                        <label class="small font-weight-bold">
                                                                                            <i class="fas fa-box"></i> Jumlah
                                                                                        </label>
                                                                                        <input type="number" 
                                                                                            class="form-control form-control-sm jumlah-input" 
                                                                                            name="items_jumlah[{{ $item->id_rincian }}]"
                                                                                            value="{{ $item->jumlah }}"
                                                                                            min="0"
                                                                                            step="0.1"
                                                                                            data-item-id="{{ $item->id_rincian }}">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <div class="form-group">
                                                                                        <label class="small font-weight-bold">
                                                                                            <i class="fas fa-tag"></i> Harga/Unit
                                                                                        </label>
                                                                                        <input type="number" 
                                                                                            class="form-control form-control-sm harga-input" 
                                                                                            name="items_harga[{{ $item->id_rincian }}]"
                                                                                            value="{{ $item->harga ?? 0 }}"
                                                                                            min="0"
                                                                                            data-item-id="{{ $item->id_rincian }}">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <div class="form-group">
                                                                                        <label class="small font-weight-bold">
                                                                                            <i class="fas fa-calculator"></i> Total
                                                                                        </label>
                                                                                        <div class="alert alert-info mb-0 py-2">
                                                                                            <strong>Rp <span class="total-display" data-item-id="{{ $item->id_rincian }}">{{ number_format(($item->harga ?? 0) * ($item->jumlah ?? 0), 0, ',', '.') }}</span></strong>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="mt-2">
                                                                                <button type="button" class="btn btn-sm btn-secondary" onclick="toggleEditForm({{ $item->id_rincian }})">
                                                                                    <i class="fas fa-times"></i> Tutup
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Display mode (when not editing) -->
                                                                    <div class="row align-items-center mt-2 display-mode">
                                                                        <div class="col-md-4">
                                                                            <small class="text-muted">
                                                                                <i class="fas fa-cube"></i> Jumlah: <strong>{{ $item->jumlah }}</strong>
                                                                            </small>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <small class="text-muted">
                                                                                <i class="fas fa-tag"></i> Harga: <strong>Rp {{ number_format($item->harga ?? 0, 0, ',', '.') }}</strong>
                                                                            </small>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <small class="text-muted">
                                                                                <i class="fas fa-calculator"></i> Total: <strong>Rp {{ number_format($item->total_harga ?? 0, 0, ',', '.') }}</strong>
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif

                                        <div class="form-group mt-4">
                                            <button type="submit" class="btn btn-success btn-block">
                                                <i class="fas fa-save"></i> Simpan Pengiriman untuk Semua Item
                                            </button>
                                            <a href="{{ route('rincian_pengajuan_po', [$po->id, $id_menu, $id_kontrak]) }}" 
                                                class="btn btn-secondary btn-block mt-2">
                                                <i class="fas fa-times"></i> Batal
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <div class="p-3">
                <h5>Info</h5>
                <p>Input pengiriman otomatis berdasarkan kategori resep</p>
            </div>
        </aside>

        <!-- Main Footer -->
        @include('Template.footer')
    </div>

    @include('Template.script')

    <script>
        // Toggle Edit Form Function
        function toggleEditForm(itemId) {
            const editForm = document.getElementById('edit-item-' + itemId);
            if (editForm) {
                if (editForm.style.display === 'none') {
                    editForm.style.display = 'block';
                } else {
                    editForm.style.display = 'none';
                }
            }
        }

        // Select All functionality
        document.getElementById('selectAllBtn').addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('input[name="items[]"]').forEach(checkbox => {
                checkbox.checked = true;
            });
        });

        // Deselect All functionality
        document.getElementById('deselectAllBtn').addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('input[name="items[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });
        });

        // Calculate total when jumlah or harga changes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('jumlah-input') || e.target.classList.contains('harga-input')) {
                const itemId = e.target.getAttribute('data-item-id');
                if (itemId) {
                    updateItemTotal(itemId);
                }
            }
        });

        // Also handle input event for real-time calculation
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('jumlah-input') || e.target.classList.contains('harga-input')) {
                const itemId = e.target.getAttribute('data-item-id');
                if (itemId) {
                    updateItemTotal(itemId);
                }
            }
        });

        function updateItemTotal(itemId) {
            const jumlahInputs = document.querySelectorAll(`input[name="items_jumlah[${itemId}]"]`);
            const hargaInputs = document.querySelectorAll(`input[name="items_harga[${itemId}]"]`);
            const totalDisplays = document.querySelectorAll(`span[data-item-id="${itemId}"].total-display`);

            if (jumlahInputs.length > 0 && hargaInputs.length > 0 && totalDisplays.length > 0) {
                const jumlah = parseFloat(jumlahInputs[0].value) || 0;
                const harga = parseFloat(hargaInputs[0].value) || 0;
                const total = jumlah * harga;

                // Format as currency
                const formatted = new Intl.NumberFormat('id-ID', {
                    style: 'decimal',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(total);

                totalDisplays.forEach(display => {
                    display.textContent = formatted;
                });
            }
        }
    </script>
</body>
</html>
