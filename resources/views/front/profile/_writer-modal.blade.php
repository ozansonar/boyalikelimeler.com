{{-- Writer Application Modal --}}
<div class="modal fade writer-modal" id="writerApplicationModal"
     tabindex="-1"
     aria-labelledby="writerModalLabel"
     aria-modal="true"
     role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
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

            {{-- Step Indicator --}}
            <div class="writer-modal__steps">
                <div class="writer-modal__step writer-modal__step--active" data-step="1">
                    <span class="writer-modal__step-num">1</span>
                    <span class="writer-modal__step-label">Kişisel</span>
                </div>
                <div class="writer-modal__step-line"></div>
                <div class="writer-modal__step" data-step="2">
                    <span class="writer-modal__step-num">2</span>
                    <span class="writer-modal__step-label">Deneyim</span>
                </div>
                <div class="writer-modal__step-line"></div>
                <div class="writer-modal__step" data-step="3">
                    <span class="writer-modal__step-num">3</span>
                    <span class="writer-modal__step-label">Eserler</span>
                </div>
            </div>

            {{-- Modal Body — Form --}}
            <div class="modal-body writer-modal__body">
                <form id="writerApplicationForm" novalidate>
                    @csrf

                    {{-- Step 1: Kişisel Bilgiler --}}
                    <div class="writer-modal__panel" id="step1">
                        <h5 class="writer-modal__section-title">
                            <i class="fa-solid fa-user me-2"></i>Kişisel Bilgiler
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="writer-form__group">
                                    <label class="writer-form__label" for="wf_firstName">Ad <span class="writer-form__required">*</span></label>
                                    <input type="text" class="writer-form__input" id="wf_firstName" name="first_name" value="{{ explode(' ', auth()->user()->name)[0] ?? '' }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="writer-form__group">
                                    <label class="writer-form__label" for="wf_lastName">Soyad <span class="writer-form__required">*</span></label>
                                    <input type="text" class="writer-form__input" id="wf_lastName" name="last_name" value="{{ explode(' ', auth()->user()->name, 2)[1] ?? '' }}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="writer-form__group">
                                    <label class="writer-form__label" for="wf_email">E-posta <span class="writer-form__required">*</span></label>
                                    <input type="email" class="writer-form__input" id="wf_email" name="email" value="{{ auth()->user()->email }}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="writer-form__group">
                                    <label class="writer-form__label" for="wf_motivation">Neden Yazar Olmak İstiyorsunuz? <span class="writer-form__required">*</span></label>
                                    <textarea class="writer-form__input writer-form__textarea"
                                              id="wf_motivation"
                                              name="motivation"
                                              rows="4"
                                              placeholder="Kendinizi tanıtın, yazarlık motivasyonunuzu ve hedeflerinizi anlatın..."
                                              required
                                              minlength="100"></textarea>
                                    <span class="writer-form__hint">
                                        <i class="fa-solid fa-circle-info me-1"></i>En az 100 karakter
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Deneyim --}}
                    <div class="writer-modal__panel d-none" id="step2">
                        <h5 class="writer-modal__section-title">
                            <i class="fa-solid fa-pen-nib me-2"></i>Yazarlık Deneyimi
                        </h5>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="writer-form__label mb-2">
                                    Hangi alanlarda yazıyorsunuz? <span class="writer-form__required">*</span>
                                </label>
                                <div class="writer-form__check-grid">
                                    <label class="writer-form__check-item">
                                        <input type="checkbox" class="writer-form__checkbox" name="categories[]" value="siir">
                                        <span class="writer-form__check-box"><i class="fa-solid fa-feather-pointed"></i></span>
                                        <span class="writer-form__check-text">Şiir</span>
                                    </label>
                                    <label class="writer-form__check-item">
                                        <input type="checkbox" class="writer-form__checkbox" name="categories[]" value="hikaye">
                                        <span class="writer-form__check-box"><i class="fa-solid fa-book"></i></span>
                                        <span class="writer-form__check-text">Hikaye</span>
                                    </label>
                                    <label class="writer-form__check-item">
                                        <input type="checkbox" class="writer-form__checkbox" name="categories[]" value="deneme">
                                        <span class="writer-form__check-box"><i class="fa-solid fa-scroll"></i></span>
                                        <span class="writer-form__check-text">Deneme</span>
                                    </label>
                                    <label class="writer-form__check-item">
                                        <input type="checkbox" class="writer-form__checkbox" name="categories[]" value="roman">
                                        <span class="writer-form__check-box"><i class="fa-solid fa-book-open"></i></span>
                                        <span class="writer-form__check-text">Roman</span>
                                    </label>
                                    <label class="writer-form__check-item">
                                        <input type="checkbox" class="writer-form__checkbox" name="categories[]" value="elestiri">
                                        <span class="writer-form__check-box"><i class="fa-solid fa-magnifying-glass"></i></span>
                                        <span class="writer-form__check-text">Eleştiri</span>
                                    </label>
                                    <label class="writer-form__check-item">
                                        <input type="checkbox" class="writer-form__checkbox" name="categories[]" value="resim">
                                        <span class="writer-form__check-box"><i class="fa-solid fa-paintbrush"></i></span>
                                        <span class="writer-form__check-text">Görsel Sanat</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="writer-form__group">
                                    <label class="writer-form__label" for="wf_experience">Yazarlık Deneyimi <span class="writer-form__required">*</span></label>
                                    <select class="writer-form__select" id="wf_experience" name="experience" required>
                                        <option value="" disabled selected>Seçiniz</option>
                                        <option value="beginner">Yeni Başlıyorum</option>
                                        <option value="1-3">1–3 Yıl</option>
                                        <option value="3-5">3–5 Yıl</option>
                                        <option value="5+">5+ Yıl</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="writer-form__group">
                                    <label class="writer-form__label" for="wf_publish_count">Yayınlanan Eser Sayısı</label>
                                    <select class="writer-form__select" id="wf_publish_count" name="publish_count">
                                        <option value="" disabled selected>Seçiniz</option>
                                        <option value="0">Henüz yayınlamadım</option>
                                        <option value="1-5">1–5 eser</option>
                                        <option value="5-20">5–20 eser</option>
                                        <option value="20+">20+ eser</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3: Örnek Eserler --}}
                    <div class="writer-modal__panel d-none" id="step3">
                        <h5 class="writer-modal__section-title">
                            <i class="fa-solid fa-link me-2"></i>Örnek Eserler
                        </h5>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="writer-form__group">
                                    <label class="writer-form__label" for="wf_sample_text">Örnek Metin <span class="writer-form__required">*</span></label>
                                    <textarea class="writer-form__input writer-form__textarea"
                                              id="wf_sample_text"
                                              name="sample_text"
                                              rows="6"
                                              placeholder="En iyi eserinizden bir örnek paylaşın (şiir, hikaye, deneme vb.)..."
                                              required
                                              minlength="50"></textarea>
                                    <span class="writer-form__hint">
                                        <i class="fa-solid fa-circle-info me-1"></i>Editörlerimiz bu metni değerlendirmede kullanacak
                                    </span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="writer-form__group">
                                    <label class="writer-form__label" for="wf_portfolio_url">Portföy / Blog Linki</label>
                                    <input type="url"
                                           class="writer-form__input"
                                           id="wf_portfolio_url"
                                           name="portfolio_url"
                                           placeholder="https://blogunuz.com veya sosyal medya profil linki">
                                    <span class="writer-form__hint">
                                        <i class="fa-solid fa-circle-info me-1"></i>İsteğe bağlı — daha önce yayınlanan eserlerinizin linki
                                    </span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="writer-form__group">
                                    <label class="writer-form__label" for="wf_ref">Referans / Öneri</label>
                                    <input type="text"
                                           class="writer-form__input"
                                           id="wf_ref"
                                           name="reference"
                                           placeholder="Sizi öneren bir Boyalı Kelimeler üyesi var mı?">
                                </div>
                            </div>
                            {{-- Agreement --}}
                            <div class="col-12">
                                <label class="writer-form__agreement">
                                    <input type="checkbox" class="writer-form__checkbox" id="wf_agree" name="agreement" required>
                                    <span class="writer-form__agreement-text">
                                        <a href="#" class="writer-form__link">Yazar Sözleşmesi</a>'ni ve
                                        <a href="#" class="writer-form__link">Telif Hakları Politikası</a>'nı
                                        okudum, kabul ediyorum.
                                    </span>
                                </label>
                            </div>
                            {{-- Info Box --}}
                            <div class="col-12">
                                <div class="writer-modal__info-box">
                                    <i class="fa-solid fa-circle-check writer-modal__info-icon"></i>
                                    <div>
                                        <strong>Başvuru Süreci</strong>
                                        <p>Başvurunuz editör ekibimiz tarafından 3–5 iş günü içinde değerlendirilecek. Sonuç e-posta ile bildirilecektir.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            {{-- Modal Footer — Navigation Buttons --}}
            <div class="modal-footer writer-modal__footer">
                <button type="button" class="writer-modal__nav-btn writer-modal__nav-btn--back d-none" id="btnBack">
                    <i class="fa-solid fa-arrow-left me-1"></i>Geri
                </button>
                <div class="writer-modal__footer-step-info">
                    <span id="currentStepLabel">Adım 1 / 3</span>
                </div>
                <button type="button" class="writer-modal__nav-btn writer-modal__nav-btn--next" id="btnNext">
                    Devam Et <i class="fa-solid fa-arrow-right ms-1"></i>
                </button>
                <button type="button" class="writer-modal__nav-btn writer-modal__nav-btn--submit d-none" id="btnSubmit">
                    <i class="fa-solid fa-paper-plane me-2"></i>Başvuruyu Gönder
                </button>
            </div>

        </div>
    </div>
</div>
