{{-- Editor Image Gallery Modal — TinyMCE entegrasyonu --}}
<div class="modal fade" id="editorImageGallery" tabindex="-1" aria-labelledby="editorImageGalleryLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="editorImageGalleryLabel">
                    <i class="bi bi-images me-2"></i>Görsel Galerisi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                {{-- Upload Area --}}
                <div class="eig-upload mb-4">
                    <div class="eig-upload__dropzone" id="eigDropzone">
                        <input type="file" id="eigFileInput" accept="image/jpeg,image/png,image/gif,image/webp" class="d-none">
                        <div class="eig-upload__placeholder" id="eigPlaceholder">
                            <i class="bi bi-cloud-arrow-up fs-1 mb-2"></i>
                            <p class="mb-1">Görsel yüklemek için tıklayın veya sürükleyin</p>
                            <small class="text-secondary">JPG, PNG, GIF, WebP — Maks. 5 MB</small>
                        </div>
                        <div class="eig-upload__progress d-none" id="eigProgress">
                            <div class="spinner-border text-warning spinner-border-sm me-2" role="status"></div>
                            <span>Yükleniyor...</span>
                        </div>
                    </div>
                    <div class="eig-upload__error text-danger small mt-1 d-none" id="eigError"></div>
                </div>

                {{-- Gallery Grid --}}
                <div class="eig-gallery" id="eigGallery">
                    <div class="eig-gallery__loading text-center py-4" id="eigLoading">
                        <div class="spinner-border text-warning" role="status"></div>
                        <p class="mt-2 text-secondary">Görseller yükleniyor...</p>
                    </div>
                    <div class="eig-gallery__empty text-center py-4 d-none" id="eigEmpty">
                        <i class="bi bi-image fs-1 text-secondary mb-2 d-block"></i>
                        <p class="text-secondary">Henüz yüklenmiş görsel yok.</p>
                    </div>
                    <div class="row g-3" id="eigGrid"></div>
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <span class="text-secondary me-auto" id="eigSelectedInfo">Görsel seçin veya yükleyin</span>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-warning" id="eigInsertBtn" disabled>
                    <i class="bi bi-check-lg me-1"></i>Görseli Ekle
                </button>
            </div>
        </div>
    </div>
</div>
