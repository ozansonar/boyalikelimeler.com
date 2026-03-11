<!-- Approve Confirm Modal -->
<div class="modal fade" id="qnaApproveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="status-modal-icon success">
                    <i class="bi bi-check-circle"></i>
                </div>
                <h5 class="cl-modal-heading">Onayla</h5>
                <p class="cl-modal-body-text">
                    <strong id="qnaApproveName"></strong> öğesini onaylamak istediğinize emin misiniz?
                </p>
                <p class="cl-modal-body-text small text-muted">Onaylanan içerik yayına alınacak ve ilgili kullanıcıya bildirim gönderilecektir.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="button" class="btn-teal" id="qnaApproveBtn"><i class="bi bi-check-circle me-1"></i>Onayla</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Confirm Modal -->
<div class="modal fade" id="qnaRejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="status-modal-icon danger">
                    <i class="bi bi-x-circle"></i>
                </div>
                <h5 class="cl-modal-heading">Reddet</h5>
                <p class="cl-modal-body-text">
                    <strong id="qnaRejectName"></strong> öğesini reddetmek istediğinize emin misiniz?
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="button" class="btn-teal btn-danger-gradient" id="qnaRejectBtn"><i class="bi bi-x-circle me-1"></i>Reddet</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal fade" id="qnaDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="status-modal-icon danger">
                    <i class="bi bi-trash"></i>
                </div>
                <h5 class="cl-modal-heading">Sil</h5>
                <p class="cl-modal-body-text">
                    <strong id="qnaDeleteName"></strong> öğesini silmek istediğinize emin misiniz?
                </p>
                <p class="cl-modal-warning"><i class="bi bi-exclamation-triangle me-1"></i>Bu işlem geri alınamaz.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn-glass" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="button" class="btn-teal btn-danger-gradient" id="qnaDeleteBtn"><i class="bi bi-trash me-1"></i>Sil</button>
                </div>
            </div>
        </div>
    </div>
</div>
