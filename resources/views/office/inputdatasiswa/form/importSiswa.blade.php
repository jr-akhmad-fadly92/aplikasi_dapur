<div class="container-fluid">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-upload mr-2"></i>Import Data Siswa
            </h3>
            <div class="card-tools">
                <a href="{{ url('inputdatasiswa/downloadTemplate') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-download mr-1"></i>Download Template
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <h6><i class="fas fa-info-circle mr-1"></i>Petunjuk Import:</h6>
                <ol class="mb-0">
                    <li>Download template Excel dengan mengklik tombol <strong>"Download Template"</strong> di atas</li>
                    <li>Isi data siswa sesuai format yang tersedia di template</li>
                    <li>Kolom yang tersedia:
                        <ul class="mt-2">
                            <li><strong>Data Dasar:</strong> NPSN, NISN, Nama, Kelas, Jenis Kelamin (L/P), Nama
                                Orangtua, Keterangan</li>
                            <li><strong>Data Fisik:</strong> Berat Badan (kg), Tinggi Badan (cm), Tanggal Lahir
                                (YYYY-MM-DD), Tempat Lahir</li>
                            <li><strong>Data Kesehatan:</strong> Riwayat Penyakit Bawaan, Riwayat Penyakit Menular,
                                Alergi, Golongan Darah (A/B/AB/O)</li>
                            <li><strong>Data Kontak:</strong> Nomor Telp Emergency (12 digit), Golongan Penerimaan (A/B)
                            </li>
                        </ul>
                    </li>
                    <li>Upload file yang sudah diisi ke form di bawah ini</li>
                    <li><strong>Catatan:</strong> Semua kolom bersifat opsional, namun disarankan untuk mengisi data
                        selengkap mungkin</li>
                </ol>
            </div>

            <form id="form_import_siswa" enctype="multipart/form-data">
                @csrf
                <!-- File Upload Section -->
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="file_import">
                                <i class="fas fa-file mr-1"></i>Pilih File
                            </label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="file_import" name="file"
                                        accept=".csv,.xlsx,.xls" required>
                                    <label class="custom-file-label" for="file_import">Pilih file...</label>
                                </div>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </span>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>
                                Format yang didukung: CSV, XLSX, XLS (maksimal 2MB)
                                <br>
                                <strong>Format Data:</strong>
                                <ul class="mb-0 mt-1" style="font-size: 0.85em;">
                                    <li>Tanggal Lahir: YYYY-MM-DD (contoh: 2010-01-15)</li>
                                    <li>Jenis Kelamin: L (Laki-laki) atau P (Perempuan)</li>
                                    <li>Golongan Darah: A, B, AB, atau O</li>
                                    <li>Golongan Penerimaan: A atau B</li>
                                    <li>Berat/Tinggi Badan: angka (contoh: 45.5 atau 150)</li>
                                    <li>Nomor Telp Emergency: maksimal 12 digit</li>
                                </ul>
                            </small>
                        </div>
                        <!-- File Info Display -->
                        <div id="file-info" class="alert alert-info" style="display: none;">
                            <h6><i class="fas fa-file-alt mr-1"></i>File yang dipilih:</h6>
                            <p class="mb-1">
                                <strong>Nama:</strong> <span id="file-name"></span>
                            </p>
                            <p class="mb-0">
                                <strong>Ukuran:</strong> <span id="file-size"></span>
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Progress -->
                <div class="row" id="import_progress" style="display: none;">
                    <div class="col-12">
                        <div class="form-group">
                            <label>Progress Import:</label>
                            <div class="progress">
                                <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated"
                                    role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                                    aria-valuemax="100">
                                    <span class="sr-only">0% Complete</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Submit  -->
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-upload mr-2"></i>Import Data Siswa
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('AdminLte/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script>
    $(document).ready(function () {
        // Initialize Bootstrap custom file input
        bsCustomFileInput.init();

        // File input change handler
        $('#file_import').on('change', function () {
            if (this.files.length > 0) {
                updateFileInfo(this.files[0]);
            } else {
                $('#file-info').hide();
            }
        });

        function updateFileInfo(file) {
            if (validateFile(file)) {
                $('#file-name').text(file.name);
                $('#file-size').text(formatFileSize(file.size));
                $('#file-info').show();
            }
        }

        function validateFile(file) {
            const maxSize = 2 * 1024 * 1024; // 2MB
            const allowedTypes = [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/csv'
            ];

            if (file.size > maxSize) {
                showAlert('error', 'File terlalu besar', 'Ukuran file maksimal 2MB');
                $('#file_import').val('');
                $('.custom-file-label').text('Pilih file...');
                return false;
            }

            if (allowedTypes.indexOf(file.type) === -1 && !file.name.match(/\.(csv|xlsx|xls)$/i)) {
                showAlert('error', 'Format file tidak valid', 'Hanya file CSV, XLS, dan XLSX yang diperbolehkan');
                $('#file_import').val('');
                $('.custom-file-label').text('Pilih file...');
                return false;
            }

            return true;
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function showAlert(type, title, message) {
            const alertClass = type === 'error' ? 'alert-danger' : 'alert-info';
            const icon = type === 'error' ? 'fas fa-exclamation-triangle' : 'fas fa-info-circle';

            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="${icon} mr-2"></i><strong>${title}:</strong> ${message}
                    <button type="button" class="close" data-dismiss="alert">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;

            $('#form_import_siswa').prepend(alertHtml);
            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        }
    });

    // Handle import form
    $('#form_import_siswa').on('submit', function (e) {
        e.preventDefault();

        var formData = new FormData(this);
        var submitBtn = $(this).find('button[type="submit"]');
        var progressContainer = $('#import_progress');
        var progressBar = progressContainer.find('.progress-bar');

        // Disable submit button and show progress
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Mengimpor...');
        progressContainer.show();

        $.ajax({
            type: 'POST',
            url: '{{ url("inputdatasiswa/importSiswa") }}',
            data: formData,
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                        progressBar.css('width', percentComplete + '%')
                            .attr('aria-valuenow', percentComplete)
                            .find('.sr-only').text(percentComplete + '% Complete');
                    }
                }, false);
                return xhr;
            },
            success: function (response) {
                // Show success toast
                if (typeof Toast !== 'undefined') {
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                } else {
                    // Fallback alert
                    showSuccessAlert('Import Berhasil', response.message);
                }

                // Show import summary if available
                if (response.summary) {
                    var summaryContent = '<div class="alert alert-success alert-dismissible fade show">';
                    summaryContent += '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>';
                    summaryContent += '<h6><i class="fas fa-check-circle mr-2"></i>Ringkasan Import:</h6>';
                    summaryContent += '<ul class="mb-0">';
                    summaryContent += '<li>Total data diproses: <strong>' + (response.summary.total || 0) + '</strong></li>';
                    summaryContent += '<li>Berhasil diimpor: <strong>' + (response.summary.success || 0) + '</strong></li>';
                    summaryContent += '<li>Gagal diimpor: <strong>' + (response.summary.failed || 0) + '</strong></li>';
                    if (response.summary.updated && response.summary.updated > 0) {
                        summaryContent += '<li>Data diperbarui: <strong>' + response.summary.updated + '</strong></li>';
                    }
                    if (response.summary.skipped && response.summary.skipped > 0) {
                        summaryContent += '<li>Data dilewati: <strong>' + response.summary.skipped + '</strong></li>';
                    }
                    if (response.summary.errors && response.summary.errors.length > 0) {
                        summaryContent += '<li>Error:<ul>';
                        response.summary.errors.forEach(function (error) {
                            summaryContent += '<li>' + error + '</li>';
                        });
                        summaryContent += '</ul></li>';
                    }
                    summaryContent += '</ul></div>';

                    $('#form_import_siswa').prepend(summaryContent);
                }

                // Close modal and reload table
                setTimeout(() => {
                    $('.jconfirm').remove();
                    if (typeof table !== 'undefined') {
                        table.ajax.reload();
                    }
                }, 2000);
            },
            error: function (xhr) {
                var response = JSON.parse(xhr.responseText);
                var message = response.message || 'Terjadi kesalahan';

                if (response.errors) {
                    message += "<ul>";
                    $.each(response.errors, function (field, errors) {
                        $.each(errors, function (index, error) {
                            message += "<li>" + error + "</li>";
                        });
                    });
                    message += "</ul>";
                }

                var errorContent = '<div class="alert alert-danger alert-dismissible fade show">';
                errorContent += '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>';
                errorContent += '<h6><i class="fas fa-exclamation-triangle mr-2"></i>Error Import:</h6>';
                errorContent += '<p>' + message + '</p>';
                errorContent += '</div>';

                $('#form_import_siswa').prepend(errorContent);
            },
            complete: function () {
                // Re-enable submit button and hide progress
                submitBtn.prop('disabled', false).html('<i class="fas fa-upload mr-2"></i>Import Data Siswa');
                progressContainer.hide();
                progressBar.css('width', '0%').attr('aria-valuenow', 0).find('.sr-only').text('0% Complete');
            }
        });
    });

    function showSuccessAlert(title, message) {
        const alertHtml = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i><strong>${title}:</strong> ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        $('#form_import_siswa').prepend(alertHtml);
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);
    }
</script>