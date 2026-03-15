<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MailTemplate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class MailTemplateService
{
    private const CACHE_KEY = 'mail_templates';
    private const CACHE_TTL = 3600;

    /**
     * All mail template definitions with default subjects, bodies, and variables.
     *
     * @return array<string, array{mailable_class: string, subject: string, body: string, description: string, variables: array<array{key: string, label: string}>}>
     */
    public static function templateDefinitions(): array
    {
        return [
            'verify_email' => [
                'mailable_class' => 'App\Mail\VerifyEmailMail',
                'subject'        => 'E-posta Adresinizi Doğrulayın — Boyalı Kelimeler',
                'body'           => '<h1>E-posta Adresinizi Doğrulayın</h1><p>Merhaba {user_name},</p><p>Boyalı Kelimeler topluluğuna hoş geldiniz! Hesabınızı aktif hale getirmek için lütfen aşağıdaki butona tıklayın.</p><p><a href="{verification_url}" class="button">E-posta Adresimi Doğrula</a></p><p>Bu link <strong>60 dakika</strong> içinde geçerliliğini yitirecektir.</p><p>Eğer bu hesabı siz oluşturmadıysanız herhangi bir işlem yapmanıza gerek yoktur.</p><p>Saygılarımızla,<br>Boyalı Kelimeler</p>',
                'description'    => 'Kullanıcı e-posta doğrulama maili',
                'variables'      => [
                    ['key' => '{user_name}', 'label' => 'Kullanıcı adı'],
                    ['key' => '{verification_url}', 'label' => 'Doğrulama linki'],
                ],
            ],
            'reset_password' => [
                'mailable_class' => 'App\Mail\ResetPasswordMail',
                'subject'        => 'Şifre Sıfırlama Talebi — Boyalı Kelimeler',
                'body'           => '<h1>Şifre Sıfırlama Talebi</h1><p>Merhaba {user_name},</p><p>Hesabınız için şifre sıfırlama talebinde bulunuldu. Aşağıdaki butona tıklayarak yeni şifrenizi belirleyebilirsiniz.</p><p><a href="{reset_url}" class="button">Şifremi Sıfırla</a></p><p>Bu link <strong>60 dakika</strong> içinde geçerliliğini yitirecektir.</p><p>Eğer bu talebi siz yapmadıysanız herhangi bir işlem yapmanıza gerek yoktur.</p><p>Saygılarımızla,<br>Boyalı Kelimeler</p>',
                'description'    => 'Şifre sıfırlama maili',
                'variables'      => [
                    ['key' => '{user_name}', 'label' => 'Kullanıcı adı'],
                    ['key' => '{reset_url}', 'label' => 'Şifre sıfırlama linki'],
                ],
            ],
            'new_user_registered' => [
                'mailable_class' => 'App\Mail\NewUserRegisteredMail',
                'subject'        => 'Yeni Kullanıcı Kaydı — Boyalı Kelimeler',
                'body'           => '<h1>Yeni Kullanıcı Kaydı</h1><p>Merhaba,</p><p>Siteye yeni bir kullanıcı kayıt oldu.</p><p><strong>Ad Soyad:</strong> {user_name}<br><strong>E-posta:</strong> {user_email}<br><strong>Kayıt Tarihi:</strong> {register_date}</p><p><a href="{admin_url}" class="button">Admin Paneline Git</a></p><p>Saygılarımızla,<br>Boyalı Kelimeler</p>',
                'description'    => 'Admin bildirimi: Yeni kullanıcı kaydı',
                'variables'      => [
                    ['key' => '{user_name}', 'label' => 'Kullanıcı adı'],
                    ['key' => '{user_email}', 'label' => 'Kullanıcı e-posta'],
                    ['key' => '{register_date}', 'label' => 'Kayıt tarihi'],
                    ['key' => '{admin_url}', 'label' => 'Admin panel linki'],
                ],
            ],
            'comment_approved' => [
                'mailable_class' => 'App\Mail\CommentApprovedMail',
                'subject'        => 'İçeriğinize Yorum Yapıldı — Boyalı Kelimeler',
                'body'           => '<h1>İçeriğinize Yorum Yapıldı!</h1><p>Merhaba {author_name},</p><p>Harika haber! <strong>"{content_title}"</strong> başlıklı içeriğinize yapılan bir yorum onaylandı ve yayına alındı.</p><p><strong>Yazan:</strong> {commenter_name}<br><strong>Puan:</strong> {rating}<br><strong>Yorum:</strong></p><blockquote>{comment_body}</blockquote><p><a href="{content_url}" class="button">İçeriği Görüntüle</a></p><p>Saygılarımızla,<br>Boyalı Kelimeler</p>',
                'description'    => 'Yazara yorum onaylandı bildirimi',
                'variables'      => [
                    ['key' => '{author_name}', 'label' => 'Yazar adı'],
                    ['key' => '{content_title}', 'label' => 'İçerik başlığı'],
                    ['key' => '{commenter_name}', 'label' => 'Yorumcu adı'],
                    ['key' => '{comment_body}', 'label' => 'Yorum metni'],
                    ['key' => '{rating}', 'label' => 'Puan (1-5)'],
                    ['key' => '{content_url}', 'label' => 'İçerik linki'],
                ],
            ],
            'new_comment' => [
                'mailable_class' => 'App\Mail\NewCommentMail',
                'subject'        => 'Yeni Yorum Bekliyor — Boyalı Kelimeler',
                'body'           => '<h1>Yeni Yorum Onay Bekliyor</h1><p>Merhaba,</p><p><strong>"{content_title}"</strong> başlıklı içeriğe yeni bir yorum yapıldı.</p><p><strong>Yazan:</strong> {commenter_name}<br><strong>E-posta:</strong> {commenter_email}<br><strong>Puan:</strong> {rating}<br><strong>Yorum:</strong></p><blockquote>{comment_body}</blockquote><p>Yorumu incelemek ve onaylamak/reddetmek için aşağıdaki butonu kullanabilirsiniz.</p><p><a href="{admin_url}" class="button">Yorumları İncele</a></p><p>Saygılarımızla,<br>Boyalı Kelimeler</p>',
                'description'    => 'Admin bildirimi: Yeni yorum onay bekliyor',
                'variables'      => [
                    ['key' => '{content_title}', 'label' => 'İçerik başlığı'],
                    ['key' => '{commenter_name}', 'label' => 'Yorumcu adı'],
                    ['key' => '{commenter_email}', 'label' => 'Yorumcu e-posta'],
                    ['key' => '{comment_body}', 'label' => 'Yorum metni'],
                    ['key' => '{rating}', 'label' => 'Puan (1-5)'],
                    ['key' => '{admin_url}', 'label' => 'Admin yorumlar linki'],
                ],
            ],
            'literary_work_submitted' => [
                'mailable_class' => 'App\Mail\LiteraryWorkSubmittedMail',
                'subject'        => 'Yeni Edebiyat Eseri Gönderildi — Boyalı Kelimeler',
                'body'           => '<h1>Yeni Edebiyat Eseri Gönderildi</h1><p>Merhaba,</p><p><strong>{author_name}</strong> yeni bir edebiyat eseri gönderdi ve onayınızı bekliyor.</p><p><strong>Eser Başlığı:</strong> {work_title}<br><strong>Kategori:</strong> {category_name}<br><strong>Gönderim Tarihi:</strong> {submit_date}</p><p><a href="{admin_url}" class="button">Eseri İncele</a></p><p>Boyalı Kelimeler Sistem Bildirimi</p>',
                'description'    => 'Admin bildirimi: Yeni eser gönderildi',
                'variables'      => [
                    ['key' => '{author_name}', 'label' => 'Yazar adı'],
                    ['key' => '{work_title}', 'label' => 'Eser başlığı'],
                    ['key' => '{category_name}', 'label' => 'Kategori adı'],
                    ['key' => '{submit_date}', 'label' => 'Gönderim tarihi'],
                    ['key' => '{admin_url}', 'label' => 'Admin eser linki'],
                ],
            ],
            'literary_work_approved' => [
                'mailable_class' => 'App\Mail\LiteraryWorkApprovedMail',
                'subject'        => 'Eseriniz Onaylandı — Boyalı Kelimeler',
                'body'           => '<h1>Eseriniz Onaylandı!</h1><p>Merhaba {author_name},</p><p>Harika haber! <strong>"{work_title}"</strong> başlıklı eseriniz editörlerimiz tarafından incelendi ve <strong>onaylanarak yayına alındı</strong>.</p><p>Tebrik ederiz! Eseriniz artık tüm okuyucular tarafından görüntülenebilir.</p><p><a href="{works_url}" class="button">Eserlerimi Görüntüle</a></p><p>Saygılarımızla,<br>Boyalı Kelimeler</p>',
                'description'    => 'Yazara eser onay bildirimi',
                'variables'      => [
                    ['key' => '{author_name}', 'label' => 'Yazar adı'],
                    ['key' => '{work_title}', 'label' => 'Eser başlığı'],
                    ['key' => '{works_url}', 'label' => 'Eserlerim sayfası linki'],
                ],
            ],
            'literary_work_rejected' => [
                'mailable_class' => 'App\Mail\LiteraryWorkRejectedMail',
                'subject'        => 'Eseriniz Hakkında Bilgilendirme — Boyalı Kelimeler',
                'body'           => '<h1>Eseriniz Hakkında Bilgilendirme</h1><p>Merhaba {author_name},</p><p><strong>"{work_title}"</strong> başlıklı eseriniz editörlerimiz tarafından incelendi, ancak maalesef bu aşamada yayınlanması uygun görülmedi.</p><p>Yeni eserlerinizi bekliyoruz. Daha fazla bilgi için bizimle iletişime geçebilirsiniz.</p><p><a href="{works_url}" class="button">Eserlerimi Görüntüle</a></p><p>Saygılarımızla,<br>Boyalı Kelimeler</p>',
                'description'    => 'Yazara eser ret bildirimi',
                'variables'      => [
                    ['key' => '{author_name}', 'label' => 'Yazar adı'],
                    ['key' => '{work_title}', 'label' => 'Eser başlığı'],
                    ['key' => '{works_url}', 'label' => 'Eserlerim sayfası linki'],
                ],
            ],
            'literary_work_revision_requested' => [
                'mailable_class' => 'App\Mail\LiteraryWorkRevisionRequestedMail',
                'subject'        => 'Eseriniz İçin Revize Talebi — Boyalı Kelimeler',
                'body'           => '<h1>Eseriniz İçin Revize Talebi</h1><p>Merhaba {author_name},</p><p><strong>"{work_title}"</strong> başlıklı eseriniz editörlerimiz tarafından incelendi. Yayına alınabilmesi için bazı düzenlemeler yapmanız gerekmektedir.</p><p><strong>Editör Notu:</strong></p><blockquote>{reason}</blockquote><p>Lütfen gerekli düzenlemeleri yaparak eserinizi tekrar gönderin.</p><p><a href="{edit_url}" class="button">Eseri Düzenle</a></p><p>Saygılarımızla,<br>Boyalı Kelimeler</p>',
                'description'    => 'Yazara revize talep bildirimi',
                'variables'      => [
                    ['key' => '{author_name}', 'label' => 'Yazar adı'],
                    ['key' => '{work_title}', 'label' => 'Eser başlığı'],
                    ['key' => '{reason}', 'label' => 'Revize nedeni'],
                    ['key' => '{edit_url}', 'label' => 'Eser düzenleme linki'],
                ],
            ],
            'literary_work_revised' => [
                'mailable_class' => 'App\Mail\LiteraryWorkRevisedMail',
                'subject'        => 'Eser Revize Edildi — Boyalı Kelimeler',
                'body'           => '<h1>Eser Revize Edildi</h1><p>Merhaba,</p><p><strong>{author_name}</strong>, daha önce revize istenen <strong>"{work_title}"</strong> başlıklı eserini düzenleyerek tekrar gönderdi.</p><p>Eseri inceleyip onaylayabilir veya tekrar revize talep edebilirsiniz.</p><p><a href="{admin_url}" class="button">Eseri İncele</a></p><p>Boyalı Kelimeler Sistem Bildirimi</p>',
                'description'    => 'Admin bildirimi: Eser revize edildi',
                'variables'      => [
                    ['key' => '{author_name}', 'label' => 'Yazar adı'],
                    ['key' => '{work_title}', 'label' => 'Eser başlığı'],
                    ['key' => '{admin_url}', 'label' => 'Admin eser linki'],
                ],
            ],
            'literary_work_updated' => [
                'mailable_class' => 'App\Mail\LiteraryWorkUpdatedMail',
                'subject'        => 'Yayındaki Eser Güncellendi — Boyalı Kelimeler',
                'body'           => '<h1>Yayındaki Eser Güncellendi</h1><p>Merhaba,</p><p><strong>{author_name}</strong>, daha önce onaylanmış olan <strong>"{work_title}"</strong> başlıklı eserinde güncelleme yaptı.</p><p>Eser yayından kaldırılmış olup tekrar onayınızı beklemektedir.</p><p><a href="{admin_url}" class="button">Eseri İncele</a></p><p>Boyalı Kelimeler Sistem Bildirimi</p>',
                'description'    => 'Admin bildirimi: Onaylı eser güncellendi',
                'variables'      => [
                    ['key' => '{author_name}', 'label' => 'Yazar adı'],
                    ['key' => '{work_title}', 'label' => 'Eser başlığı'],
                    ['key' => '{admin_url}', 'label' => 'Admin eser linki'],
                ],
            ],
            'writer_application_received' => [
                'mailable_class' => 'App\Mail\WriterApplicationReceivedMail',
                'subject'        => 'Yazar Başvurunuz Alındı — Boyalı Kelimeler',
                'body'           => '<h1>Yazar Başvurunuz Alındı</h1><p>Merhaba <strong>{user_name}</strong>,</p><p>Yazar başvurunuz başarıyla alınmıştır. Editör ekibimiz başvurunuzu en kısa sürede değerlendirecektir.</p><p><strong>Başvuru Tarihi:</strong> {submit_date}</p><p>Değerlendirme süreci tamamlandığında size e-posta ile bilgi verilecektir. Bu süre genellikle 3–5 iş gününü kapsamaktadır.</p><p>Saygılarımızla,<br>Boyalı Kelimeler</p>',
                'description'    => 'Kullanıcıya yazar başvurusu alındı bildirimi',
                'variables'      => [
                    ['key' => '{user_name}', 'label' => 'Kullanıcı adı'],
                    ['key' => '{submit_date}', 'label' => 'Başvuru tarihi'],
                ],
            ],
            'writer_application_submitted' => [
                'mailable_class' => 'App\Mail\WriterApplicationSubmittedMail',
                'subject'        => 'Yeni Yazar Başvurusu — Boyalı Kelimeler',
                'body'           => '<h1>Yeni Yazar Başvurusu</h1><p>Merhaba,</p><p><strong>{user_name}</strong> ({user_email}) yeni bir yazar başvurusu yaptı ve değerlendirmenizi bekliyor.</p><p><strong>Başvuru Tarihi:</strong> {submit_date}</p><p><a href="{admin_url}" class="button">Başvuruyu İncele</a></p><p>Boyalı Kelimeler Sistem Bildirimi</p>',
                'description'    => 'Admin bildirimi: Yeni yazar başvurusu',
                'variables'      => [
                    ['key' => '{user_name}', 'label' => 'Başvuran kullanıcı adı'],
                    ['key' => '{user_email}', 'label' => 'Başvuran e-posta'],
                    ['key' => '{submit_date}', 'label' => 'Başvuru tarihi'],
                    ['key' => '{admin_url}', 'label' => 'Admin başvuru detay linki'],
                ],
            ],
            'writer_application_approved' => [
                'mailable_class' => 'App\Mail\WriterApplicationApprovedMail',
                'subject'        => 'Yazar Başvurunuz Onaylandı — Boyalı Kelimeler',
                'body'           => '<h1>Tebrikler! Yazar Olarak Kabul Edildiniz</h1><p>Merhaba <strong>{user_name}</strong>,</p><p>Yazar başvurunuz editör ekibimiz tarafından değerlendirilmiş ve <strong>onaylanmıştır</strong>.</p><p>Artık Boyalı Kelimeler platformunda eserlerinizi yayınlayabilir, topluluğumuzla buluşabilir ve yarışmalara katılabilirsiniz.</p><p><a href="{profile_url}" class="button">Profilime Git</a></p><p>Boyalı Kelimeler ailesine hoş geldiniz!</p><p>Saygılarımızla,<br>Boyalı Kelimeler</p>',
                'description'    => 'Kullanıcıya yazar başvurusu onay bildirimi',
                'variables'      => [
                    ['key' => '{user_name}', 'label' => 'Kullanıcı adı'],
                    ['key' => '{profile_url}', 'label' => 'Profil sayfası linki'],
                ],
            ],
            'writer_application_rejected' => [
                'mailable_class' => 'App\Mail\WriterApplicationRejectedMail',
                'subject'        => 'Yazar Başvurunuz Hakkında — Boyalı Kelimeler',
                'body'           => '<h1>Yazar Başvurunuz Hakkında</h1><p>Merhaba <strong>{user_name}</strong>,</p><p>Yazar başvurunuz editör ekibimiz tarafından değerlendirilmiştir. Maalesef başvurunuz şu an için uygun bulunamamıştır.</p><p><strong>Değerlendirme Notu:</strong></p><blockquote>{admin_note}</blockquote><p>30 gün sonra tekrar başvurabilirsiniz. Başvurunuzu geliştirmek için yukarıdaki değerlendirme notunu dikkate almanızı öneririz.</p><p>Saygılarımızla,<br>Boyalı Kelimeler</p>',
                'description'    => 'Kullanıcıya yazar başvurusu red bildirimi',
                'variables'      => [
                    ['key' => '{user_name}', 'label' => 'Kullanıcı adı'],
                    ['key' => '{admin_note}', 'label' => 'Red gerekçesi'],
                ],
            ],
        ];
    }

    /**
     * Get all templates from DB (cached).
     *
     * @return Collection<int, MailTemplate>
     */
    public function all(): Collection
    {
        return Cache::remember(self::CACHE_KEY . '.all', self::CACHE_TTL, function (): Collection {
            return MailTemplate::orderBy('id')->get();
        });
    }

    /**
     * Find a template by key (cached).
     */
    public function findByKey(string $key): ?MailTemplate
    {
        return $this->all()->firstWhere('key', $key);
    }

    /**
     * Find a template by mailable class name (cached).
     */
    public function findByClass(string $mailableClass): ?MailTemplate
    {
        return $this->all()->firstWhere('mailable_class', $mailableClass);
    }

    /**
     * Find a template by ID.
     */
    public function find(int $id): ?MailTemplate
    {
        return MailTemplate::find($id);
    }

    /**
     * Get subject by mailable class (for BaseMailable compatibility).
     */
    public function getSubjectByClass(string $mailableClass): ?string
    {
        $template = $this->findByClass($mailableClass);

        if ($template === null || !$template->is_active) {
            return null;
        }

        return $template->subject;
    }

    /**
     * Get template body by mailable class.
     */
    public function getBodyByClass(string $mailableClass): ?string
    {
        $template = $this->findByClass($mailableClass);

        if ($template === null || !$template->is_active) {
            return null;
        }

        return $template->body;
    }

    /**
     * Get rendered body with variables replaced.
     *
     * @param array<string, string> $variables
     */
    public function renderBody(string $mailableClass, array $variables = []): ?string
    {
        $template = $this->findByClass($mailableClass);

        if ($template === null || !$template->is_active) {
            return null;
        }

        return $template->renderBody($variables);
    }

    /**
     * Get rendered subject with variables replaced.
     *
     * @param array<string, string> $variables
     */
    public function renderSubject(string $mailableClass, array $variables = []): ?string
    {
        $template = $this->findByClass($mailableClass);

        if ($template === null || !$template->is_active) {
            return null;
        }

        return $template->renderSubject($variables);
    }

    /**
     * Get all templates for admin listing.
     *
     * @return Collection<int, MailTemplate>
     */
    public function getAllForAdmin(): Collection
    {
        return MailTemplate::orderBy('id')->get();
    }

    /**
     * Update a template.
     *
     * @param array<string, mixed> $data
     */
    public function update(MailTemplate $template, array $data): MailTemplate
    {
        DB::transaction(function () use ($template, $data): void {
            $template->update($data);
        });

        $this->clearCache();

        return $template->fresh();
    }

    /**
     * Reset a single template to defaults.
     */
    public function resetToDefault(MailTemplate $template): MailTemplate
    {
        $definitions = self::templateDefinitions();
        $definition = $definitions[$template->key] ?? null;

        if ($definition === null) {
            return $template;
        }

        DB::transaction(function () use ($template, $definition): void {
            $template->update([
                'subject' => $definition['subject'],
                'body'    => $definition['body'],
            ]);
        });

        $this->clearCache();

        return $template->fresh();
    }

    /**
     * Reset all templates to defaults.
     */
    public function resetAllToDefaults(): void
    {
        $definitions = self::templateDefinitions();

        DB::transaction(function () use ($definitions): void {
            foreach ($definitions as $key => $definition) {
                MailTemplate::where('key', $key)->update([
                    'subject' => $definition['subject'],
                    'body'    => $definition['body'],
                ]);
            }
        });

        $this->clearCache();
    }

    /**
     * Seed all templates from definitions (for migration/seeder).
     */
    public function seedTemplates(): void
    {
        $definitions = self::templateDefinitions();

        DB::transaction(function () use ($definitions): void {
            foreach ($definitions as $key => $definition) {
                MailTemplate::updateOrCreate(
                    ['key' => $key],
                    [
                        'mailable_class'  => $definition['mailable_class'],
                        'subject'         => $definition['subject'],
                        'default_subject' => $definition['subject'],
                        'body'            => $definition['body'],
                        'default_body'    => $definition['body'],
                        'description'     => $definition['description'],
                        'variables'       => $definition['variables'],
                        'is_active'       => true,
                    ]
                );
            }
        });

        $this->clearCache();
    }

    /**
     * Get admin stats.
     *
     * @return array{total: int, active: int, customized: int}
     */
    public function getAdminStats(): array
    {
        return Cache::remember(self::CACHE_KEY . '.stats', 300, function (): array {
            $templates = MailTemplate::all();

            return [
                'total'      => $templates->count(),
                'active'     => $templates->where('is_active', true)->count(),
                'customized' => $templates->filter(fn (MailTemplate $t): bool => $t->hasCustomSubject() || $t->hasCustomBody())->count(),
            ];
        });
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::CACHE_KEY . '.all');
        Cache::forget(self::CACHE_KEY . '.stats');
    }
}
