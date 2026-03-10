<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;

final class MailTemplateService
{
    private const CACHE_KEY = 'mail_templates';
    private const CACHE_TTL = 3600;

    /**
     * All mail template keys with their default subjects and descriptions.
     *
     * @return array<string, array{subject: string, description: string, variables: string[]}>
     */
    public static function templateDefinitions(): array
    {
        return [
            'verify_email' => [
                'subject'     => 'E-posta Adresinizi Doğrulayın — Boyalı Kelimeler',
                'description' => 'Kullanıcı e-posta doğrulama maili',
                'variables'   => ['{user_name}', '{verification_url}'],
            ],
            'reset_password' => [
                'subject'     => 'Şifre Sıfırlama Talebi — Boyalı Kelimeler',
                'description' => 'Şifre sıfırlama maili',
                'variables'   => ['{user_name}', '{reset_url}'],
            ],
            'new_user_registered' => [
                'subject'     => 'Yeni Kullanıcı Kaydı — Boyalı Kelimeler',
                'description' => 'Admin bildirimi: Yeni kullanıcı kaydı',
                'variables'   => ['{user_name}', '{user_email}', '{register_date}'],
            ],
            'comment_approved' => [
                'subject'     => 'İçeriğinize Yorum Yapıldı — Boyalı Kelimeler',
                'description' => 'Yazara yorum onaylandı bildirimi',
                'variables'   => ['{author_name}', '{content_title}', '{commenter_name}', '{comment_body}', '{rating}'],
            ],
            'new_comment' => [
                'subject'     => 'Yeni Yorum Bekliyor — Boyalı Kelimeler',
                'description' => 'Admin bildirimi: Yeni yorum onay bekliyor',
                'variables'   => ['{content_title}', '{commenter_name}', '{comment_body}', '{rating}'],
            ],
            'literary_work_submitted' => [
                'subject'     => 'Yeni Edebiyat Eseri Gönderildi — Boyalı Kelimeler',
                'description' => 'Admin bildirimi: Yeni eser gönderildi',
                'variables'   => ['{author_name}', '{work_title}', '{category_name}'],
            ],
            'literary_work_approved' => [
                'subject'     => 'Eseriniz Onaylandı — Boyalı Kelimeler',
                'description' => 'Yazara eser onay bildirimi',
                'variables'   => ['{author_name}', '{work_title}'],
            ],
            'literary_work_rejected' => [
                'subject'     => 'Eseriniz Hakkında Bilgilendirme — Boyalı Kelimeler',
                'description' => 'Yazara eser ret bildirimi',
                'variables'   => ['{author_name}', '{work_title}'],
            ],
            'literary_work_revision_requested' => [
                'subject'     => 'Eseriniz İçin Revize Talebi — Boyalı Kelimeler',
                'description' => 'Yazara revize talep bildirimi',
                'variables'   => ['{author_name}', '{work_title}', '{reason}'],
            ],
            'literary_work_revised' => [
                'subject'     => 'Eser Revize Edildi — Boyalı Kelimeler',
                'description' => 'Admin bildirimi: Eser revize edildi',
                'variables'   => ['{author_name}', '{work_title}'],
            ],
            'literary_work_updated' => [
                'subject'     => 'Yayındaki Eser Güncellendi — Boyalı Kelimeler',
                'description' => 'Admin bildirimi: Onaylı eser güncellendi',
                'variables'   => ['{author_name}', '{work_title}'],
            ],
        ];
    }

    /**
     * Mailable class => template key mapping.
     *
     * @return array<string, string>
     */
    public static function classToKeyMap(): array
    {
        return [
            'App\Mail\VerifyEmailMail'                    => 'verify_email',
            'App\Mail\ResetPasswordMail'                  => 'reset_password',
            'App\Mail\NewUserRegisteredMail'               => 'new_user_registered',
            'App\Mail\CommentApprovedMail'                => 'comment_approved',
            'App\Mail\NewCommentMail'                     => 'new_comment',
            'App\Mail\LiteraryWorkSubmittedMail'          => 'literary_work_submitted',
            'App\Mail\LiteraryWorkApprovedMail'           => 'literary_work_approved',
            'App\Mail\LiteraryWorkRejectedMail'           => 'literary_work_rejected',
            'App\Mail\LiteraryWorkRevisionRequestedMail'  => 'literary_work_revision_requested',
            'App\Mail\LiteraryWorkRevisedMail'            => 'literary_work_revised',
            'App\Mail\LiteraryWorkUpdatedMail'            => 'literary_work_updated',
        ];
    }

    public function __construct(
        private readonly SettingService $settingService,
    ) {}

    /**
     * Get all template overrides from DB (cached).
     *
     * @return array<string, array{subject?: string}>
     */
    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function (): array {
            $raw = $this->settingService->getGroup('mail_templates');

            $templates = [];
            foreach ($raw as $key => $value) {
                if (str_ends_with($key, '_subject') && $value !== null && $value !== '') {
                    $templateKey = substr($key, 0, -8); // remove _subject
                    $templates[$templateKey]['subject'] = $value;
                }
            }

            return $templates;
        });
    }

    /**
     * Get subject for a template key (DB override or default).
     */
    public function getSubject(string $templateKey): string
    {
        $overrides = $this->all();

        if (isset($overrides[$templateKey]['subject'])) {
            return $overrides[$templateKey]['subject'];
        }

        $definitions = self::templateDefinitions();

        return $definitions[$templateKey]['subject'] ?? '';
    }

    /**
     * Get subject by Mailable class name.
     */
    public function getSubjectByClass(string $mailableClass): ?string
    {
        $map = self::classToKeyMap();
        $key = $map[$mailableClass] ?? null;

        if ($key === null) {
            return null;
        }

        return $this->getSubject($key);
    }

    /**
     * Get all templates with their current subjects (for admin form).
     *
     * @return array<string, array{subject: string, default_subject: string, description: string, variables: string[]}>
     */
    public function getAllForAdmin(): array
    {
        $definitions = self::templateDefinitions();
        $overrides = $this->all();
        $result = [];

        foreach ($definitions as $key => $def) {
            $result[$key] = [
                'subject'         => $overrides[$key]['subject'] ?? $def['subject'],
                'default_subject' => $def['subject'],
                'description'     => $def['description'],
                'variables'       => $def['variables'],
            ];
        }

        return $result;
    }

    /**
     * Save template subject overrides.
     *
     * @param array<string, string> $subjects key => subject
     */
    public function saveSubjects(array $subjects): void
    {
        $definitions = self::templateDefinitions();
        $data = [];

        foreach ($subjects as $key => $subject) {
            if (!isset($definitions[$key])) {
                continue;
            }

            $data[$key . '_subject'] = $subject !== '' ? $subject : null;
        }

        $this->settingService->updateGroup('mail_templates', $data);
        $this->clearCache();
    }

    /**
     * Reset all templates to defaults.
     */
    public function resetToDefaults(): void
    {
        $definitions = self::templateDefinitions();
        $data = [];

        foreach (array_keys($definitions) as $key) {
            $data[$key . '_subject'] = null;
        }

        $this->settingService->updateGroup('mail_templates', $data);
        $this->clearCache();
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
