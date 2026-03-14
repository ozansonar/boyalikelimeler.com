@extends('layouts.front')

@section('title', 'İletişim — Boyalı Kelimeler')
@section('meta_description', 'Boyalı Kelimeler ile iletişime geçin. Sorularınız, önerileriniz ve iş birliği talepleriniz için bize ulaşın.')
@section('canonical', route('contact.show'))
@section('og_title', 'İletişim — Boyalı Kelimeler')
@section('og_description', 'Boyalı Kelimeler ile iletişime geçin. Sorularınız ve önerileriniz için bize ulaşın.')

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'ContactPage',
            'name' => 'İletişim — Boyalı Kelimeler',
            'description' => 'Boyalı Kelimeler ile iletişime geçin.',
            'url' => route('contact.show'),
        ],
        [
            '@type' => 'BreadcrumbList',
            'name' => 'Breadcrumb',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Ana Sayfa', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'İletişim'],
            ],
        ],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

@section('content')

    <!-- Page Header -->
    <section class="page-header" aria-label="Sayfa başlığı">
        <div class="container">
            <h1 class="page-header__title">İletişim</h1>
            <div class="page-header__divider"></div>
            <p class="page-header__desc">
                Sorularınız, önerileriniz ve iş birliği talepleriniz için bize ulaşın.<br>
                Her mesajınız bizim için değerlidir.
            </p>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="section section--dark contact-section" aria-label="İletişim formu ve bilgiler">
        <div class="container">
            <div class="row g-4 g-lg-5">

                <!-- Left: Contact Form -->
                <div class="col-lg-7" data-aos="fade-up">
                    <div class="contact-card">
                        <div class="contact-card__header">
                            <div class="contact-card__icon">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </div>
                            <h2 class="contact-card__title">Bize Yazın</h2>
                            <p class="contact-card__subtitle">Formu doldurarak mesajınızı iletebilirsiniz</p>
                        </div>

                        <form class="contact-form" id="contactForm" novalidate>
                            @csrf
                            <div class="row g-3">
                                <!-- Ad Soyad -->
                                <div class="col-md-6">
                                    <div class="contact-form__group">
                                        <label class="contact-form__label" for="fullname">
                                            <i class="fa-solid fa-user me-1"></i>Ad Soyad
                                        </label>
                                        <input type="text"
                                               class="contact-form__input"
                                               id="fullname"
                                               name="fullname"
                                               placeholder="Adınız ve soyadınız"
                                               required>
                                    </div>
                                </div>

                                <!-- E-posta -->
                                <div class="col-md-6">
                                    <div class="contact-form__group">
                                        <label class="contact-form__label" for="email">
                                            <i class="fa-solid fa-envelope me-1"></i>E-posta
                                        </label>
                                        <input type="email"
                                               class="contact-form__input"
                                               id="email"
                                               name="email"
                                               placeholder="ornek@email.com"
                                               required>
                                    </div>
                                </div>

                                <!-- Konu -->
                                <div class="col-12">
                                    <div class="contact-form__group">
                                        <label class="contact-form__label" for="subject">
                                            <i class="fa-solid fa-tag me-1"></i>Konu
                                        </label>
                                        <select class="contact-form__input contact-form__select"
                                                id="subject"
                                                name="subject"
                                                required>
                                            <option value="" disabled selected>Konu seçiniz</option>
                                            <option value="genel">Genel Bilgi</option>
                                            <option value="isbirligi">İş Birliği Talebi</option>
                                            <option value="yarisma">Yarışma Hakkında</option>
                                            <option value="teknik">Teknik Destek</option>
                                            <option value="oneri">Öneri / Şikayet</option>
                                            <option value="diger">Diğer</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Mesaj -->
                                <div class="col-12">
                                    <div class="contact-form__group">
                                        <label class="contact-form__label" for="message">
                                            <i class="fa-solid fa-message me-1"></i>Mesajınız
                                        </label>
                                        <textarea class="contact-form__input contact-form__textarea"
                                                  id="message"
                                                  name="message"
                                                  placeholder="Mesajınızı buraya yazın..."
                                                  rows="6"
                                                  required></textarea>
                                    </div>
                                </div>

                                <!-- reCAPTCHA -->
                                <div class="col-12">
                                    <x-recaptcha />
                                </div>

                                <!-- Submit -->
                                <div class="col-12">
                                    <button type="submit" class="contact-form__submit" id="contactSubmitBtn">
                                        <i class="fa-solid fa-paper-plane me-2"></i>Mesajı Gönder
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right: Contact Info -->
                <div class="col-lg-5" data-aos="fade-up" data-aos-delay="100">

                    <!-- Info Cards -->
                    <div class="contact-info">
                        @if(!empty($contactSettings['email']))
                            <div class="contact-info__card">
                                <div class="contact-info__icon-wrap">
                                    <i class="fa-solid fa-envelope"></i>
                                </div>
                                <div class="contact-info__body">
                                    <h3 class="contact-info__title">E-posta</h3>
                                    @foreach(explode(',', $contactSettings['email']) as $mail)
                                        <a href="mailto:{{ trim($mail) }}" class="contact-info__link">{{ trim($mail) }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(!empty($socialLinks['whatsapp']))
                            <div class="contact-info__card">
                                <div class="contact-info__icon-wrap">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </div>
                                <div class="contact-info__body">
                                    <h3 class="contact-info__title">WhatsApp</h3>
                                    <a href="https://wa.me/+90{{ $socialLinks['whatsapp'] }}?text=Merhaba" target="_blank" rel="noopener noreferrer nofollow" class="contact-info__link">+90 {{ $socialLinks['whatsapp'] }}</a>
                                    <p class="contact-info__note">Hafta içi 09:00 – 18:00</p>
                                </div>
                            </div>
                        @endif

                        @if(!empty($contactSettings['phone']))
                            <div class="contact-info__card">
                                <div class="contact-info__icon-wrap">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <div class="contact-info__body">
                                    <h3 class="contact-info__title">Telefon</h3>
                                    <a href="tel:{{ $contactSettings['phone'] }}" class="contact-info__link">{{ $contactSettings['phone'] }}</a>
                                </div>
                            </div>
                        @endif

                        @if(!empty($contactSettings['address']))
                            <div class="contact-info__card">
                                <div class="contact-info__icon-wrap">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div class="contact-info__body">
                                    <h3 class="contact-info__title">Adres</h3>
                                    <p class="contact-info__text">{!! nl2br(e($contactSettings['address'])) !!}</p>
                                </div>
                            </div>
                        @endif

                    </div>

                    <!-- Social Media -->
                    <div class="contact-social">
                        <h3 class="contact-social__title">Sosyal Medya</h3>
                        <p class="contact-social__desc">Bizi takip edin, sanatın nabzını birlikte tutalım.</p>
                        <div class="contact-social__links">
                            @if(!empty($socialLinks['instagram']))
                                <a href="{{ $socialLinks['instagram'] }}" class="contact-social__link" target="_blank" rel="noopener noreferrer nofollow" aria-label="Instagram">
                                    <i class="fa-brands fa-instagram"></i>
                                </a>
                            @endif
                            @if(!empty($socialLinks['twitter']))
                                <a href="{{ $socialLinks['twitter'] }}" class="contact-social__link" target="_blank" rel="noopener noreferrer nofollow" aria-label="Twitter">
                                    <i class="fa-brands fa-x-twitter"></i>
                                </a>
                            @endif
                            @if(!empty($socialLinks['youtube']))
                                <a href="{{ $socialLinks['youtube'] }}" class="contact-social__link" target="_blank" rel="noopener noreferrer nofollow" aria-label="YouTube">
                                    <i class="fa-brands fa-youtube"></i>
                                </a>
                            @endif
                            @if(!empty($socialLinks['facebook']))
                                <a href="{{ $socialLinks['facebook'] }}" class="contact-social__link" target="_blank" rel="noopener noreferrer nofollow" aria-label="Facebook">
                                    <i class="fa-brands fa-facebook-f"></i>
                                </a>
                            @endif
                            @if(!empty($socialLinks['tiktok']))
                                <a href="{{ $socialLinks['tiktok'] }}" class="contact-social__link" target="_blank" rel="noopener noreferrer nofollow" aria-label="TikTok">
                                    <i class="fa-brands fa-tiktok"></i>
                                </a>
                            @endif
                            @if(!empty($socialLinks['linkedin']))
                                <a href="{{ $socialLinks['linkedin'] }}" class="contact-social__link" target="_blank" rel="noopener noreferrer nofollow" aria-label="LinkedIn">
                                    <i class="fa-brands fa-linkedin-in"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section section--surface" aria-label="Sıkça sorulan sorular">
        <div class="container">
            <h2 class="section__title" data-aos="fade-up">Sıkça Sorulan Sorular</h2>
            <div class="section__divider"></div>
            <p class="section__slogan" data-aos="fade-up">"Merak ettikleriniz, bir tık uzağınızda."</p>

            <div class="row justify-content-center">
                <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
                    <div class="accordion contact-faq" id="contactFaq">

                        <div class="accordion-item contact-faq__item">
                            <h3 class="accordion-header">
                                <button class="accordion-button contact-faq__button" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq1"
                                        aria-expanded="true" aria-controls="faq1">
                                    <i class="fa-solid fa-circle-question me-2"></i>
                                    Boyalı Kelimeler'e nasıl yazar olarak katılabilirim?
                                </button>
                            </h3>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#contactFaq">
                                <div class="accordion-body contact-faq__body">
                                    Ücretsiz kayıt olduktan sonra profil sayfanızdan yazar başvurusu yapabilirsiniz.
                                    Editoryal ekibimiz başvurunuzu inceleyip size en kısa sürede dönüş yapacaktır.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item contact-faq__item">
                            <h3 class="accordion-header">
                                <button class="accordion-button contact-faq__button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq2"
                                        aria-expanded="false" aria-controls="faq2">
                                    <i class="fa-solid fa-circle-question me-2"></i>
                                    Yarışmalara kimler katılabilir?
                                </button>
                            </h3>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#contactFaq">
                                <div class="accordion-body contact-faq__body">
                                    Altın Kalem ve Altın Fırça yarışmalarımız herkese açıktır. Yaş ve deneyim
                                    fark etmeksizin tüm sanat severler katılabilir. Detaylı bilgi için yarışma
                                    sayfalarını ziyaret edebilirsiniz.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item contact-faq__item">
                            <h3 class="accordion-header">
                                <button class="accordion-button contact-faq__button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq3"
                                        aria-expanded="false" aria-controls="faq3">
                                    <i class="fa-solid fa-circle-question me-2"></i>
                                    İş birliği ve reklam taleplerim için ne yapmalıyım?
                                </button>
                            </h3>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#contactFaq">
                                <div class="accordion-body contact-faq__body">
                                    İş birliği ve reklam talepleriniz için yukarıdaki iletişim formunu "İş Birliği Talebi"
                                    konusuyla doldurabilir veya doğrudan info@boyalikelimeler.com adresine e-posta
                                    gönderebilirsiniz.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item contact-faq__item">
                            <h3 class="accordion-header">
                                <button class="accordion-button contact-faq__button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq4"
                                        aria-expanded="false" aria-controls="faq4">
                                    <i class="fa-solid fa-circle-question me-2"></i>
                                    Mesajıma ne kadar sürede dönüş yapılır?
                                </button>
                            </h3>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#contactFaq">
                                <div class="accordion-body contact-faq__body">
                                    Mesajlarınıza en geç 48 saat içinde dönüş sağlamaya çalışıyoruz.
                                    Acil konularda WhatsApp hattımızı kullanabilirsiniz.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item contact-faq__item">
                            <h3 class="accordion-header">
                                <button class="accordion-button contact-faq__button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq5"
                                        aria-expanded="false" aria-controls="faq5">
                                    <i class="fa-solid fa-circle-question me-2"></i>
                                    Eserlerimin telif hakkı korunuyor mu?
                                </button>
                            </h3>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#contactFaq">
                                <div class="accordion-body contact-faq__body">
                                    Evet, platformumuza yüklediğiniz tüm eserlerin telif hakkı size aittir.
                                    Boyalı Kelimeler yalnızca platform içinde sergileme hakkına sahiptir.
                                    Detaylar için Gizlilik Politikası sayfamızı inceleyebilirsiniz.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script src="{{ asset('js/contact.js') }}"></script>
@endpush
