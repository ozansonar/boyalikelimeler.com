{{-- Writer Application Modal --}}
<div class="modal fade writer-modal" id="writerApplicationModal"
     tabindex="-1"
     aria-labelledby="writerModalLabel"
     aria-modal="true"
     role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content writer-modal__content">

            {{-- Modal Header --}}
            <div class="modal-header writer-modal__header">
                <div class="writer-modal__header-icon">
                    <i class="fa-solid fa-feather-pointed"></i>
                </div>
                <div>
                    <h2 class="modal-title writer-modal__title" id="writerModalLabel">
                        Yazar Başvurusu
                    </h2>
                    <p class="writer-modal__subtitle">Boyalı Kelimeler yazarlık programına katılın</p>
                </div>
                <button type="button"
                        class="btn-close writer-modal__close"
                        data-bs-dismiss="modal"
                        aria-label="Kapat"></button>
            </div>

            {{-- Modal Body — Form --}}
            <div class="modal-body writer-modal__body">
                <form id="writerApplicationForm" novalidate>
                    @csrf

                    <div class="writer-form__group">
                        <label class="writer-form__label" for="wf_motivation">
                            Neden Yazar Olmak İstiyorsunuz? <span class="writer-form__required">*</span>
                        </label>
                        <textarea class="writer-form__input writer-form__textarea"
                                  id="wf_motivation"
                                  name="motivation"
                                  rows="6"
                                  placeholder="Kendinizi tanıtın, yazarlık motivasyonunuzu ve hedeflerinizi anlatın..."
                                  required
                                  minlength="50"
                                  maxlength="1000"></textarea>
                        <div class="writer-form__meta">
                            <span class="writer-form__hint">
                                <i class="fa-solid fa-circle-info me-1"></i>En az 50, en fazla 1000 karakter
                            </span>
                            <span class="writer-form__char-counter">
                                <span id="motivationCharCount">0</span>/1000
                            </span>
                        </div>
                    </div>

                    {{-- Info Box --}}
                    <div class="writer-modal__info-box">
                        <i class="fa-solid fa-circle-check writer-modal__info-icon"></i>
                        <div>
                            <strong>Başvuru Süreci</strong>
                            <p>Başvurunuz editör ekibimiz tarafından 3–5 iş günü içinde değerlendirilecek. Sonuç e-posta ile bildirilecektir.</p>
                        </div>
                    </div>

                </form>
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer writer-modal__footer">
                <button type="button" class="writer-modal__nav-btn writer-modal__nav-btn--back" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark me-1"></i>Vazgeç
                </button>
                <button type="button" class="writer-modal__nav-btn writer-modal__nav-btn--submit" id="btnSubmit">
                    <i class="fa-solid fa-paper-plane me-2"></i>Başvuruyu Gönder
                </button>
            </div>

        </div>
    </div>
</div>
