/**
 * Nutrition Calculator Widget
 * Reusable component untuk menghitung nutrisi bahan
 * 
 * Usage:
 * <div id="nutrisi-calculator"></div>
 * <script src="/js/nutrisi-calculator.js"></script>
 * <script>
 *   new NutrisiCalculator('nutrisi-calculator');
 * </script>
 */

class NutrisiCalculator {
    constructor(containerId, options = {}) {
        this.containerId = containerId;
        this.container = document.getElementById(containerId);
        this.apiToken = options.apiToken || document.querySelector('meta[name="csrf-token"]')?.content || '';
        this.selectedBahan = null;
        this.lastResult = null;
        
        this.init();
    }

    init() {
        this.container.innerHTML = this.getTemplate();
        this.attachEventListeners();
        this.loadBahanList();
    }

    getTemplate() {
        return `
            <div class="nutrition-calculator">
                <div class="calculator-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="bahan-select">Pilih Bahan</label>
                                <select id="bahan-select" class="form-control select2" placeholder="Cari bahan..."></select>
                                <small class="form-text text-muted">Pilih dari database master bahan nutrisi (1,148 items)</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="jumlah-gram">Jumlah (gram)</label>
                                <input type="number" id="jumlah-gram" class="form-control" placeholder="150" min="0.01" step="0.01" value="150">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="bdd-percent">BDD (%)</label>
                                <input type="number" id="bdd-percent" class="form-control" placeholder="80" min="0" max="100" step="0.1" value="80">
                                <small class="form-text text-muted">Bagian Dapat Dimakan</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" id="btn-calculate" class="btn btn-primary">
                                <i class="fa fa-calculator"></i> Hitung Nutrisi
                            </button>
                            <button type="button" id="btn-reset" class="btn btn-secondary">
                                <i class="fa fa-redo"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Loading spinner -->
                <div id="loading" class="text-center" style="display: none; margin: 20px 0;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p>Menghitung nutrisi...</p>
                </div>

                <!-- Results -->
                <div id="results-container" style="display: none; margin-top: 20px;">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="m-0">Hasil Kalkulasi Nutrisi</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Bahan:</strong> <span id="result-bahan"></span></p>
                                    <p><strong>Kode:</strong> <span id="result-kode"></span></p>
                                    <p><strong>Kelompok:</strong> <span id="result-kelompok"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Jumlah Input:</strong> <span id="result-jumlah"></span> gram</p>
                                    <p><strong>BDD:</strong> <span id="result-bdd"></span>%</p>
                                    <p><strong>Bahan Dapat Dimakan:</strong> <span id="result-edible"></span> gram</p>
                                </div>
                            </div>

                            <hr>

                            <h6 class="mb-3">Kandungan Nutrisi</h6>
                            <div class="row" id="nutrition-grid">
                                <!-- Akan diisi JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error messages -->
                <div id="error-container" style="display: none; margin-top: 20px;">
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>Error:</strong> <span id="error-message"></span>
                    </div>
                </div>
            </div>

            <style>
                .nutrition-calculator {
                    padding: 15px;
                    background-color: #f9f9f9;
                    border-radius: 5px;
                }
                .nutrition-calculator .calculator-form {
                    background-color: white;
                    padding: 15px;
                    border-radius: 5px;
                    margin-bottom: 20px;
                }
                .nutrition-grid-item {
                    margin-bottom: 15px;
                }
                .nutrition-grid-item .value {
                    font-size: 1.2em;
                    font-weight: bold;
                    color: #007bff;
                }
                .nutrition-grid-item .unit {
                    font-size: 0.9em;
                    color: #666;
                }
            </style>
        `;
    }

    attachEventListeners() {
        document.getElementById('btn-calculate').addEventListener('click', () => this.calculate());
        document.getElementById('btn-reset').addEventListener('click', () => this.reset());
        document.getElementById('bahan-select').addEventListener('change', (e) => this.selectBahan(e));
        document.getElementById('jumlah-gram').addEventListener('change', () => this.calculate());
        document.getElementById('bdd-percent').addEventListener('change', () => this.calculate());
    }

    loadBahanList() {
        // This would require an API endpoint to list all bahan
        // For now, we'll use select2 with remote search (post-implementation task)
        console.log('Bahan list would load from API');
    }

    selectBahan(event) {
        const bahanId = event.target.value;
        if (bahanId) {
            this.fetchBahanDetail(bahanId);
        }
    }

    fetchBahanDetail(bahanId) {
        document.getElementById('loading').style.display = 'block';
        document.getElementById('results-container').style.display = 'none';
        document.getElementById('error-container').style.display = 'none';

        fetch('/api/master-bahan-nutrisi/detail', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.apiToken}`,
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({ bahan_id: bahanId })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loading').style.display = 'none';
            if (data.success) {
                this.selectedBahan = data.data;
                document.getElementById('bdd-percent').value = data.data.bdd || 100;
                this.calculate();
            } else {
                this.showError(data.message || 'Gagal memuat detail bahan');
            }
        })
        .catch(error => {
            document.getElementById('loading').style.display = 'none';
            this.showError('Error: ' + error.message);
        });
    }

    calculate() {
        const bahanId = document.getElementById('bahan-select').value;
        const jumlahGram = parseFloat(document.getElementById('jumlah-gram').value);
        const bdd = parseFloat(document.getElementById('bdd-percent').value);

        if (!bahanId) {
            this.showError('Pilih bahan terlebih dahulu');
            return;
        }

        if (!jumlahGram || jumlahGram <= 0) {
            this.showError('Jumlah gram harus > 0');
            return;
        }

        if (bdd < 0 || bdd > 100) {
            this.showError('BDD harus antara 0-100');
            return;
        }

        document.getElementById('loading').style.display = 'block';
        document.getElementById('results-container').style.display = 'none';
        document.getElementById('error-container').style.display = 'none';

        fetch('/api/master-bahan-nutrisi/hitung', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.apiToken}`,
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({
                bahan_id: bahanId,
                jumlah_gram: jumlahGram,
                bdd: bdd
            })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loading').style.display = 'none';
            if (data.success) {
                this.lastResult = data.data;
                this.displayResults(data.data);
                document.getElementById('results-container').style.display = 'block';
            } else {
                this.showError(data.message || 'Gagal menghitung nutrisi');
            }
        })
        .catch(error => {
            document.getElementById('loading').style.display = 'none';
            this.showError('Error: ' + error.message);
        });
    }

    displayResults(data) {
        document.getElementById('result-bahan').textContent = data.nama_bahan;
        document.getElementById('result-kode').textContent = data.kode || '-';
        document.getElementById('result-kelompok').textContent = data.kode ? data.kode.substring(0, 2) : '-';
        document.getElementById('result-jumlah').textContent = data.jumlah_gram;
        document.getElementById('result-bdd').textContent = data.bdd_persen;
        document.getElementById('result-edible').textContent = data.bahan_yang_dapat_dimakan_gram;

        // Display nutrients in grid
        const nutritionGrid = document.getElementById('nutrition-grid');
        nutritionGrid.innerHTML = '';

        const nutrisiObj = data.nutrisi;
        const satuanObj = data.satuan;
        const mainNutrients = ['energi', 'protein', 'lemak', 'karbohidrat', 'serat', 'natrium'];

        mainNutrients.forEach(nutrient => {
            if (nutrisiObj[nutrient] !== undefined) {
                const value = nutrisiObj[nutrient];
                const unit = satuanObj[nutrient] || '';
                const col = document.createElement('div');
                col.className = 'col-md-4 nutrition-grid-item';
                col.innerHTML = `
                    <div class="border p-3 rounded bg-light">
                        <p class="mb-1"><strong>${this.formatLabel(nutrient)}</strong></p>
                        <p class="value mb-0">${value}</p>
                        <p class="unit mb-0">${unit}</p>
                    </div>
                `;
                nutritionGrid.appendChild(col);
            }
        });
    }

    showError(message) {
        document.getElementById('error-container').style.display = 'block';
        document.getElementById('error-message').textContent = message;
    }

    reset() {
        document.getElementById('bahan-select').value = '';
        document.getElementById('jumlah-gram').value = '150';
        document.getElementById('bdd-percent').value = '80';
        document.getElementById('results-container').style.display = 'none';
        document.getElementById('error-container').style.display = 'none';
        this.selectedBahan = null;
        this.lastResult = null;
    }

    formatLabel(nutrient) {
        const labels = {
            'energi': 'Energi',
            'protein': 'Protein',
            'lemak': 'Lemak',
            'karbohidrat': 'Karbohidrat',
            'serat': 'Serat',
            'natrium': 'Natrium',
            'air': 'Air',
            'abu': 'Abu',
            'kalium': 'Kalium',
            'kalsium': 'Kalsium',
            'magnesium': 'Magnesium',
            'fosfor': 'Fosfor',
            'besi': 'Besi',
            'seng': 'Seng'
        };
        return labels[nutrient] || nutrient.charAt(0).toUpperCase() + nutrient.slice(1);
    }

    getLastResult() {
        return this.lastResult;
    }
}
