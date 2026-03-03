{{-- Unpublish Confirmation Modal — shared partial (index, show, form) --}}
<div class="modal fade" id="unpublishConfirmModal" tabindex="-1" aria-labelledby="unpublishModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content unpublish-modal">
            <div class="unpublish-modal__icon-wrap">
                <div class="unpublish-modal__icon-ring">
                    <i class="fa-solid fa-eye-slash"></i>
                </div>
            </div>
            <h5 class="unpublish-modal__title" id="unpublishModalLabel">Yayından Kaldırmak İstiyor musunuz?</h5>
            <p class="unpublish-modal__desc">
                <strong id="unpublishItemName"></strong> başlıklı eseriniz yayından kaldırılacak ve sitede görünmeyecektir.
            </p>
            <p class="unpublish-modal__warning">
                <i class="fa-solid fa-circle-info me-1"></i>Tekrar yayınlamak istediğinizde editör onayı gerekecektir.
            </p>
            <div class="unpublish-modal__actions">
                <button type="button" class="unpublish-modal__btn unpublish-modal__btn--cancel" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark me-1"></i>Vazgeç
                </button>
                <form id="unpublishForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="unpublish-modal__btn unpublish-modal__btn--confirm">
                        <i class="fa-solid fa-eye-slash me-1"></i>Evet, Yayından Kaldır
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
