{{-- Editor Image Gallery Modal — TinyMCE entegrasyonu --}}
<div class="modal fade" id="editorImageGallery" tabindex="-1" aria-labelledby="editorImageGalleryLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header border-secondary py-2">
                <h6 class="modal-title mb-0" id="editorImageGalleryLabel">
                    <i class="bi bi-images me-1"></i>Görsel Galerisi
                </h6>
                <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body py-2">
                {{-- Upload Area (compact) --}}
                <div class="eig-upload mb-3">
                    <div class="eig-upload__dropzone" id="eigDropzone">
                        <input type="file" id="eigFileInput" accept="image/jpeg,image/png,image/gif,image/webp" class="d-none" multiple>
                        <div class="eig-upload__placeholder" id="eigPlaceholder">
                            <i class="bi bi-cloud-arrow-up me-2"></i>
                            <span class="small">Görsel yüklemek için tıklayın veya sürükleyin</span>
                            <small class="text-secondary ms-1">(Maks. 1 MB)</small>
                        </div>
                        <div class="eig-upload__progress d-none" id="eigProgress">
                            <div class="spinner-border text-warning spinner-border-sm me-2" role="status"></div>
                            <span class="small">Yükleniyor...</span>
                        </div>
                    </div>
                    <div class="eig-upload__error text-danger small mt-1 d-none" id="eigError"></div>
                </div>

                {{-- Gallery Grid --}}
                <div class="eig-gallery" id="eigGallery">
                    <div class="eig-gallery__loading text-center py-3" id="eigLoading">
                        <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                        <span class="text-secondary small ms-2">Görseller yükleniyor...</span>
                    </div>
                    <div class="eig-gallery__empty text-center py-3 d-none" id="eigEmpty">
                        <i class="bi bi-image text-secondary d-block mb-1"></i>
                        <p class="text-secondary small mb-0">Henüz yüklenmiş görsel yok.</p>
                    </div>
                    <div class="row g-2" id="eigGrid"></div>
                </div>
            </div>
            <div class="modal-footer border-secondary py-2">
                <span class="text-secondary small me-auto" id="eigSelectedInfo">Görsel seçin veya yükleyin</span>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">İptal</button>

                {{-- Grid insert (visible when 2+ selected) --}}
                <select id="eigGridColSelect" class="form-select form-select-sm d-none" style="width:auto;display:inline-block!important;">
                    <option value="2">2 Sütun</option>
                    <option value="3" selected>3 Sütun</option>
                    <option value="4">4 Sütun</option>
                </select>
                <button type="button" class="btn btn-outline-warning btn-sm d-none" id="eigInsertGridBtn" disabled>
                    <i class="bi bi-grid me-1"></i>Galeri
                </button>

                <button type="button" class="btn btn-warning btn-sm" id="eigInsertBtn" disabled>
                    <i class="bi bi-check-lg me-1"></i>Ekle
                </button>
            </div>
        </div>
    </div>
</div>
