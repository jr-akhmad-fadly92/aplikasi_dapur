<!-- Modal Export Data Siswa ke PDF -->
<div class="container-fluid">
    <div class="card card-danger">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-file-pdf mr-2\1"></i>Export Presensi Siswa ke PDF
            </h3>
        </div>
        <div class="card-body">
            <form id="form_export_siswa">
                @csrf

                <!-- Filter Section -->
                <div class="row">
                    <div class="col-12">
                        <h5 class="text-bold mb-3">
                            <i class="fas fa-filter mr-2"></i>Filter Data
                        </h5>
                    </div>
                </div>
                <div class="row">
                    @if(isset($selected_sekolah) && $selected_sekolah)
                        <!-- Mode Direct: Sekolah sudah dipilih, tampilkan info saja -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-school mr-1"></i>Sekolah Terpilih
                                </label>
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>{{ $selected_sekolah->nama_sekolah }}</strong><br>
                                    <small>NPSN: {{ $selected_sekolah->npsn }}</small>
                                </div>
                                <!-- Hidden input untuk sekolah yang terpilih -->
                                <input type="hidden" name="sekolah_id" value="{{ $selected_sekolah->id }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="export_kelas">
                                    <i class="fas fa-chalkboard mr-1"></i>Kelas <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" name="kelas" id="export_kelas"
                                    placeholder="Contoh: 1A, 2B, 3C" required>
                                <small class="form-text text-muted">Masukkan nama kelas yang akan diekspor</small>
                            </div>
                        </div>
                    @else
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="export_kelas">
                                    <i class="fas fa-chalkboard mr-1"></i>Kelas <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" name="kelas" id="export_kelas"
                                    placeholder="Contoh: 1A, 2B, 3C" required>
                                <small class="form-text text-muted">Masukkan nama kelas yang akan diekspor</small>
                            </div>
                        </div>
                    @endif
                </div>

                <hr>

                <!-- Export Options Section -->
                <div class="row">
                    <div class="col-12">
                        <h5 class="text-bold mb-3">
                            <i class="fas fa-cogs mr-2"></i>Pengaturan Export
                        </h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="export_format">
                                <i class="fas fa-file-pdf mr-1"></i>Format File
                            </label>
                            <select class="form-control" name="format" id="export_format" required readonly>
                                <option value="pdf" selected>PDF (.pdf)</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Saat ini hanya mendukung export ke format PDF
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="icheck-primary">
                                <input type="checkbox" id="include_header" name="include_header" checked disabled>
                                <label for="include_header">
                                    <i class="fas fa-table mr-1"></i>Sertakan header kolom
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Header otomatis disertakan untuk format PDF
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Column Selection - Fixed untuk format PDF -->
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-columns mr-1"></i>Kolom yang akan diekspor:
                            </label>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Kolom tetap yang akan diekspor:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>No</li>
                                    <li>NISN</li>
                                    <li>Nama Siswa</li>
                                    <li>Jenis Kelamin</li>
                                    <li>TTD Siswa (kolom kosong untuk tanda tangan)</li>
                                </ul>
                            </div>
                            <!-- Hidden inputs untuk kolom yang akan diekspor -->
                            <input type="hidden" name="columns[]" value="nisn">
                            <input type="hidden" name="columns[]" value="nama">
                            <input type="hidden" name="columns[]" value="jenis_kelamin">
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Preview Section -->
                <div class="row">
                    <div class="col-12">
                        <h5 class="text-bold mb-3">
                            <i class="fas fa-eye mr-2"></i>Preview
                        </h5>
                        <button type="button" class="btn btn-info" onclick="previewExportCount()">
                            <i class="fas fa-search mr-1"></i>Lihat Jumlah Data
                        </button>
                        <small class="form-text text-muted mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            @if(isset($selected_sekolah) && $selected_sekolah)
                                Pastikan kelas sudah diisi sebelum preview
                            @endif
                        </small>
                    </div>
                </div>
                <div class="row mt-3" id="export_preview" style="display: none;">
                    <div class="col-12">
                        <div class="info-box bg-info">
                            <span class="info-box-icon">
                                <i class="fas fa-users"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Jumlah Data yang akan Diekspor</span>
                                <span class="info-box-number" id="preview_count">0</span>
                                <span class="info-box-more">siswa</span>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Submit Button -->
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-danger btn-lg btn-block">
                            <i class="fas fa-file-pdf mr-2"></i>Export Data Siswa ke PDF
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Initialize iCheck for checkboxes
        $('input[type="checkbox"].icheck').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
        });

        // Format sudah tetap PDF, tidak perlu handler change
        // Header otomatis checked dan disabled untuk PDF
        $('#include_header').prop('checked', true).prop('disabled', true);

        // Show info about PDF format
        showInfo('File PDF akan langsung diunduh tanpa perlu konversi manual.');

        @if(isset($selected_sekolah) && $selected_sekolah)
            // Mode Direct: Sekolah sudah terpilih, focus ke kelas
            $('#export_kelas').focus();

            // Tampilkan info bahwa sekolah sudah terpilih
            showAlert('info', 'Info', 'Sekolah {{ $selected_sekolah->nama_sekolah }} sudah terpilih. Silakan masukkan kelas yang ingin diekspor.');
        @endif

        function showInfo(message) {
            $('#format-info').remove(); // Remove existing info
            var infoHtml = '<div class="alert alert-info mt-2" id="format-info">';
            infoHtml += '<i class="fas fa-info-circle mr-2"></i>' + message;
            infoHtml += '</div>';
            $('#export_format').closest('.form-group').append(infoHtml);
        }
    });

    // Handle export form - PERBAIKAN UTAMA
    $('#form_export_siswa').on('submit', function (e) {
        e.preventDefault();

        var formData = $(this).serialize();
        var submitBtn = $(this).find('button[type="submit"]');

        // Validate sekolah dan kelas
        var sekolahId = $('input[name="sekolah_id"]').val() || $('#export_sekolah_id').val();
        var kelas = $('#export_kelas').val();

        if (!sekolahId) {
            @if(isset($selected_sekolah) && $selected_sekolah)
                showAlert('error', 'Validasi Error', 'Data sekolah tidak valid. Silakan muat ulang halaman.');
            @endif
            return false;
        }

        if (!kelas.trim()) {
            showAlert('error', 'Validasi Error', 'Masukkan nama kelas terlebih dahulu');
            return false;
        }

        // Disable submit button
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Menyiapkan Export PDF...');

        // METODE BARU: Gunakan XMLHttpRequest untuk menangani blob response
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ url("inputdatasiswa/exportSiswa_presensi") }}', true);
        xhr.responseType = 'blob';

        // Set headers
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));

        xhr.onload = function () {
            // Re-enable submit button
            submitBtn.prop('disabled', false).html('<i class="fas fa-file-pdf mr-2"></i>Export Data Siswa ke PDF');

            if (xhr.status === 200) {
                // Check if response is actually a file
                var contentType = xhr.getResponseHeader('Content-Type');

                if (contentType && contentType.includes('application/json')) {
                    // If JSON response, it's likely an error
                    var reader = new FileReader();
                    reader.onload = function () {
                        try {
                            var response = JSON.parse(reader.result);
                            if (response.status === 'error') {
                                showAlert('error', 'Export Gagal', response.message);
                            } else {
                                showAlert('info', 'Info', response.message);
                            }
                        } catch (e) {
                            showAlert('error', 'Error', 'Terjadi kesalahan saat memproses response');
                        }
                    };
                    reader.readAsText(xhr.response);
                } else {
                    // Handle file download (PDF format)
                    var blob = xhr.response;
                    var filename = getFilenameFromContentDisposition(xhr.getResponseHeader('Content-Disposition'));

                    if (!filename) {
                        var timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
                        filename = 'Presensi_siswa_' + timestamp + '.pdf'; // PDF langsung
                    }

                    // Create download link
                    var url = window.URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);

                    showAlert('success', 'Export Berhasil', 'File PDF berhasil diunduh: ' + filename);
                }
            } else {
                showAlert('error', 'Export Gagal', 'Terjadi kesalahan server. Status: ' + xhr.status);
            }
        };

        xhr.onerror = function () {
            submitBtn.prop('disabled', false).html('<i class="fas fa-file-pdf mr-2"></i>Export Data Siswa ke PDF');
            showAlert('error', 'Export Gagal', 'Terjadi kesalahan jaringan');
        };

        // Send request
        xhr.send(formData);
    });

    // Helper function to extract filename from Content-Disposition header
    function getFilenameFromContentDisposition(disposition) {
        if (!disposition) return null;

        var filenameMatch = disposition.match(/filename\*?=['"]?([^;'"]+)['"]?/);
        return filenameMatch ? filenameMatch[1] : null;
    }

    // Preview export data count
    function previewExportCount() {
        var sekolahId = $('input[name="sekolah_id"]').val() || $('#export_sekolah_id').val();
        var kelas = $('#export_kelas').val();

        if (!sekolahId) {
            @if(isset($selected_sekolah) && $selected_sekolah)
                showAlert('warning', 'Peringatan', 'Data sekolah tidak valid. Silakan muat ulang halaman.');
            @endif
            return;
        }

        if (!kelas.trim()) {
            showAlert('warning', 'Peringatan', 'Masukkan nama kelas terlebih dahulu');
            return;
        }

        var formData = $('#form_export_siswa').serialize();
        var previewBox = $('#export_preview');
        var countElement = $('#preview_count');

        // Show loading
        previewBox.show();
        countElement.html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            type: 'POST',
            url: '{{ url("inputdatasiswa/previewExport_presensi") }}',
            data: formData + '&_token={{ csrf_token() }}',
            dataType: 'json',
            success: function (response) {
                countElement.text(response.count.toLocaleString());

                if (response.count === 0) {
                    previewBox.find('.info-box').removeClass('bg-info').addClass('bg-warning');
                    previewBox.find('.info-box-more').text('tidak ada data yang sesuai dengan filter');
                } else {
                    previewBox.find('.info-box').removeClass('bg-warning').addClass('bg-info');
                    previewBox.find('.info-box-more').text('siswa');
                }
            },
            error: function (xhr) {
                countElement.text('Error');
                showAlert('error', 'Error', 'Gagal memuat preview data');
                previewBox.hide();
            }
        });
    }

    // Filter input handlers for live preview
    $('#export_sekolah_id, #export_kelas').on('input change', function () {
        // Clear previous preview
        $('#export_preview').hide();
        $('.alert-warning').remove();
    });

    @if(isset($selected_sekolah) && $selected_sekolah)
        // Untuk mode direct, hanya monitor perubahan kelas
        $('#export_kelas').on('input change', function () {
            // Clear previous preview
            $('#export_preview').hide();
            $('.alert-warning').remove();
        });
    @endif

    // Helper function to show alerts
    function showAlert(type, title, message) {
        var alertClass = 'alert-' + (type === 'error' ? 'danger' : type);
        var icon = type === 'error' ? 'fas fa-exclamation-triangle' :
            type === 'warning' ? 'fas fa-exclamation-circle' :
                type === 'success' ? 'fas fa-check-circle' : 'fas fa-info-circle';

        var alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="${icon} mr-2"></i><strong>${title}:</strong> ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;

        $('#form_export_siswa').prepend(alertHtml);

        // Auto dismiss after 5 seconds
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);
    }
</script>