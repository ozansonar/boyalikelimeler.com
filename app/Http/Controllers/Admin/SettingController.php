<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TestMail;
use App\Services\SettingService;
use App\Services\UploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        private readonly SettingService $settingService,
        private readonly UploadService $uploadService,
    ) {}

    public function index(Request $request): View
    {
        return view('admin.settings.index', [
            'general'     => $this->settingService->getGroup('general'),
            'contact'     => $this->settingService->getGroup('contact'),
            'social'      => $this->settingService->getGroup('social'),
            'seo'         => $this->settingService->getGroup('seo'),
            'smtp'        => $this->settingService->getGroup('smtp'),
            'maintenance' => $this->settingService->getGroup('maintenance'),
            'tab'         => $request->query('tab', 'general'),
        ]);
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name'        => 'required|string|max:200',
            'site_description' => 'nullable|string|max:500',
            'site_url'         => 'nullable|string|max:200',
            'timezone'         => 'required|string|max:50',
            'language'         => 'required|string|max:5',
        ]);

        if ($request->hasFile('logo')) {
            $request->validate(['logo' => 'image|max:2048']);
            $oldLogo = $this->settingService->getGroup('general')['logo'] ?? null;
            $data['logo'] = $this->uploadService->replaceImage($request->file('logo'), 'settings', $oldLogo, 'site-logo');
        }

        if ($request->hasFile('favicon')) {
            $request->validate(['favicon' => 'file|max:512']);
            $oldFavicon = $this->settingService->getGroup('general')['favicon'] ?? null;
            $data['favicon'] = $this->uploadService->replaceImage($request->file('favicon'), 'settings', $oldFavicon, 'site-favicon');
        }

        $this->settingService->updateGroup('general', $data);

        return redirect()->route('admin.settings.index', ['tab' => 'general'])
            ->with('success', 'Genel ayarlar başarıyla güncellendi.');
    }

    public function updateContact(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email'     => 'nullable|email|max:200',
            'phone'     => 'nullable|string|max:50',
            'address'   => 'nullable|string|max:500',
            'map_embed' => 'nullable|string|max:1000',
        ]);

        $this->settingService->updateGroup('contact', $data);

        return redirect()->route('admin.settings.index', ['tab' => 'contact'])
            ->with('success', 'İletişim bilgileri başarıyla güncellendi.');
    }

    public function updateSocial(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'facebook'  => 'nullable|url|max:300',
            'twitter'   => 'nullable|url|max:300',
            'instagram' => 'nullable|url|max:300',
            'youtube'   => 'nullable|url|max:300',
            'tiktok'    => 'nullable|url|max:300',
            'linkedin'  => 'nullable|url|max:300',
        ]);

        $this->settingService->updateGroup('social', $data);

        return redirect()->route('admin.settings.index', ['tab' => 'social'])
            ->with('success', 'Sosyal medya linkleri başarıyla güncellendi.');
    }

    public function updateSeo(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'meta_title'          => 'nullable|string|max:70',
            'meta_description'    => 'nullable|string|max:170',
            'meta_keywords'       => 'nullable|string|max:300',
            'google_analytics'    => 'nullable|string|max:50',
            'google_verification' => 'nullable|string|max:100',
            'robots_txt'          => 'nullable|string|max:2000',
        ]);

        $this->settingService->updateGroup('seo', $data);

        return redirect()->route('admin.settings.index', ['tab' => 'seo'])
            ->with('success', 'SEO ayarları başarıyla güncellendi.');
    }

    public function updateSmtp(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'host'         => 'nullable|string|max:200',
            'port'         => 'nullable|string|max:10',
            'username'     => 'nullable|string|max:200',
            'password'     => 'nullable|string|max:200',
            'encryption'   => 'required|string|in:tls,ssl,none',
            'from_name'    => 'nullable|string|max:200',
            'from_email'   => 'nullable|email|max:200',
            'send_mode'    => 'required|string|in:normal,developer',
            'debug_emails' => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('mail_logo')) {
            $request->validate(['mail_logo' => 'image|mimes:png,jpg,jpeg|max:1024']);
            $oldLogo = $this->settingService->getGroup('smtp')['mail_logo'] ?? null;
            $data['mail_logo'] = $this->uploadService->replaceImage($request->file('mail_logo'), 'settings', $oldLogo, 'mail-logo');
        }

        $this->settingService->updateGroup('smtp', $data);

        return redirect()->route('admin.settings.index', ['tab' => 'smtp'])
            ->with('success', 'E-posta (SMTP) ayarları başarıyla güncellendi.');
    }

    public function removeMailLogo(): RedirectResponse
    {
        $smtp = $this->settingService->getGroup('smtp');
        $this->uploadService->deleteImage($smtp['mail_logo'] ?? null);
        $this->settingService->set('smtp', 'mail_logo', null);

        return redirect()->route('admin.settings.index', ['tab' => 'smtp'])
            ->with('success', 'Mail logosu başarıyla kaldırıldı.');
    }

    public function updateMaintenance(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'enabled'     => 'required|in:0,1',
            'message'     => 'nullable|string|max:1000',
            'allowed_ips' => 'nullable|string|max:500',
        ]);

        $this->settingService->updateGroup('maintenance', $data);

        return redirect()->route('admin.settings.index', ['tab' => 'maintenance'])
            ->with('success', 'Bakım modu ayarları başarıyla güncellendi.');
    }

    public function removeLogo(): RedirectResponse
    {
        $general = $this->settingService->getGroup('general');
        $this->uploadService->deleteImage($general['logo'] ?? null);
        $this->settingService->set('general', 'logo', null);

        return redirect()->route('admin.settings.index', ['tab' => 'general'])
            ->with('success', 'Logo başarıyla kaldırıldı.');
    }

    public function removeFavicon(): RedirectResponse
    {
        $general = $this->settingService->getGroup('general');
        $this->uploadService->deleteImage($general['favicon'] ?? null);
        $this->settingService->set('general', 'favicon', null);

        return redirect()->route('admin.settings.index', ['tab' => 'general'])
            ->with('success', 'Favicon başarıyla kaldırıldı.');
    }

    public function clearCache(): RedirectResponse
    {
        Artisan::call('cache:clear');
        $this->settingService->clearCache();

        return redirect()->route('admin.settings.index', ['tab' => 'maintenance'])
            ->with('success', 'Önbellek başarıyla temizlendi.');
    }

    public function sendTestMail(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'test_to'      => 'required|email|max:200',
            'test_subject' => 'required|string|max:200',
            'test_body'    => 'required|string|max:5000',
        ]);

        try {
            Mail::to($data['test_to'])
                ->send(new TestMail($data['test_subject'], $data['test_body']));

            return redirect()->route('admin.settings.index', ['tab' => 'smtp'])
                ->with('success', 'Test e-postası ' . $data['test_to'] . ' adresine başarıyla gönderildi.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.settings.index', ['tab' => 'smtp'])
                ->with('error', 'Mail gönderilemedi: ' . $e->getMessage());
        }
    }
}
