<!-- This view is loaded dynamically via AJAX -->
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header bg-warning text-white">
            <h5 class="modal-title">Edit Data Upload</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="editForm" enctype="multipart/form-data">
            <input type="hidden" id="edit-id" value="{{ $upload->id }}">
            <div class="modal-body">
                                <input type="hidden" id="current-menu-id" value="{{ $upload->id_menu }}">
                <div class="form-group">
                    <label for="edit_tanggal_pelayanan">Tanggal Pelayanan *</label>
                    <input type="date" class="form-control" id="edit_tanggal_pelayanan" name="tanggal_pelayanan" value="{{ \Carbon\Carbon::parse($upload->tanggal_pelayanan)->format('Y-m-d') }}" required>
                </div>

                <div class="form-group">
                    <label for="edit_id_menu">Menu (Opsional)</label>
                    <select class="form-control" id="edit_id_menu" name="id_menu">
                        <option value="">-- Pilih Menu (Opsional) --</option>
                        @foreach($menus as $menu)
                            <option value="{{ $menu->id }}" {{ $upload->id_menu == $menu->id ? 'selected' : '' }}>
                                {{ $menu->menu }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" data-current-id="{{ $upload->id_menu }}">
                            <label for="edit_data_menu">
                                <i class="fas fa-file-pdf text-danger"></i> Menu
                                @if($upload->data_menu)
                                    <span class="badge badge-success"><i class="fas fa-check"></i> Ada</span>
                                @endif
                            </label>
                            <input type="file" class="form-control-file" id="edit_data_menu" name="data_menu" accept=".pdf">
                            <small class="form-text text-muted">Max 1MB (auto compress)</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="edit_data_po">
                                <i class="fas fa-file-pdf text-danger"></i> PO
                                @if($upload->data_po)
                                    <span class="badge badge-success"><i class="fas fa-check"></i> Ada</span>
                                @endif
                            </label>
                            <input type="file" class="form-control-file" id="edit_data_po" name="data_po" accept=".pdf">
                            <small class="form-text text-muted">Max 1MB (auto compress)</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="edit_data_sj_kp">
                                <i class="fas fa-file-pdf text-danger"></i> SJ/KP
                                @if($upload->data_sj_kp)
                                    <span class="badge badge-success"><i class="fas fa-check"></i> Ada</span>
                                @endif
                            </label>
                            <input type="file" class="form-control-file" id="edit_data_sj_kp" name="data_sj_kp" accept=".pdf">
                            <small class="form-text text-muted">Max 1MB (auto compress)</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="edit_data_invoice">
                                <i class="fas fa-file-pdf text-danger"></i> Invoice
                                @if($upload->data_invoice)
                                    <span class="badge badge-success"><i class="fas fa-check"></i> Ada</span>
                                @endif
                            </label>
                            <input type="file" class="form-control-file" id="edit_data_invoice" name="data_invoice" accept=".pdf">
                            <small class="form-text text-muted">Max 1MB (auto compress)</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="edit_data_penerimaan_pangan">
                                <i class="fas fa-file-pdf text-danger"></i> Penerimaan Pangan
                                @if($upload->data_penerimaan_pangan)
                                    <span class="badge badge-success"><i class="fas fa-check"></i> Ada</span>
                                @endif
                            </label>
                            <input type="file" class="form-control-file" id="edit_data_penerimaan_pangan" name="data_penerimaan_pangan" accept=".pdf">
                            <small class="form-text text-muted">Max 1MB (auto compress)</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="edit_data_penerimaan_non_pangan">
                                <i class="fas fa-file-pdf text-danger"></i> Penerimaan Non-Pangan
                                @if($upload->data_penerimaan_non_pangan)
                                    <span class="badge badge-success"><i class="fas fa-check"></i> Ada</span>
                                @endif
                            </label>
                            <input type="file" class="form-control-file" id="edit_data_penerimaan_non_pangan" name="data_penerimaan_non_pangan" accept=".pdf">
                            <small class="form-text text-muted">Max 1MB (auto compress)</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="edit_data_gudang">
                                <i class="fas fa-file-pdf text-danger"></i> Gudang
                                @if($upload->data_gudang)
                                    <span class="badge badge-success"><i class="fas fa-check"></i> Ada</span>
                                @endif
                            </label>
                            <input type="file" class="form-control-file" id="edit_data_gudang" name="data_gudang" accept=".pdf">
                            <small class="form-text text-muted">Max 1MB (auto compress)</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="edit_data_hasil_masak">
                                <i class="fas fa-file-pdf text-danger"></i> Hasil Masak
                                @if($upload->data_hasil_masak)
                                    <span class="badge badge-success"><i class="fas fa-check"></i> Ada</span>
                                @endif
                            </label>
                            <input type="file" class="form-control-file" id="edit_data_hasil_masak" name="data_hasil_masak" accept=".pdf">
                            <small class="form-text text-muted">Max 1MB (auto compress)</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="edit_data_sj_sekolah">
                                <i class="fas fa-file-pdf text-danger"></i> SJ Sekolah
                                @if($upload->data_sj_sekolah)
                                    <span class="badge badge-success"><i class="fas fa-check"></i> Ada</span>
                                @endif
                            </label>
                            <input type="file" class="form-control-file" id="edit_data_sj_sekolah" name="data_sj_sekolah" accept=".pdf">
                            <small class="form-text text-muted">Max 1MB (auto compress)</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="edit_data_counter_ompreng">
                                <i class="fas fa-file-pdf text-danger"></i> Counter Ompreng
                                @if($upload->data_counter_ompreng)
                                    <span class="badge badge-success"><i class="fas fa-check"></i> Ada</span>
                                @endif
                            </label>
                            <input type="file" class="form-control-file" id="edit_data_counter_ompreng" name="data_counter_ompreng" accept=".pdf">
                            <small class="form-text text-muted">Max 1MB (auto compress)</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning" id="editSubmitBtn">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
