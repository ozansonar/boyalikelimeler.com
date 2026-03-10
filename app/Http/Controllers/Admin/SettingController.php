<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TestMail;
use App\Services\SettingService;
use App\Services\UploadService;
use Illuminate\Http\JsonResponse;
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
        $allSettings = $this->settingService->getAllGrouped();

        return view('admin.settings.index', [
            'homepage'    => $allSettings['homepage'] ?? [],
            'general'     => $allSettings['general'] ?? [],
            'contact'     => $allSettings['contact'] ?? [],
            'social'      => $allSettings['social'] ?? [],
            'seo'         => $allSettings['seo'] ?? [],
            'smtp'        => $allSettings['smtp'] ?? [],
            'maintenance' => $allSettings['maintenance'] ?? [],
            'tab'         => $request->query('tab', 'general'),
        ]);
    }

    public function updateHomepage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'hero_title'       => 'required|string|max:200',
            'hero_subtitle'    => 'nullable|string|max:300',
            'hero_tagline'     => 'nullable|string|max:200',
            'hero_description' => 'nullable|string|max:1000',
        ]);

        $this->settingService->updateGroup('homepage', $data);

        return redirect()->route('admin.settings.index', ['tab' => 'homepage'])
            ->with('success', 'Anasayfa ayarları başarıyla güncellendi.');
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

        // Keep existing password when field is submitted empty
        if ($data['password'] === null || $data['password'] === '') {
            $existing = $this->settingService->getGroup('smtp');
            $data['password'] = $existing['password'] ?? '';
        }

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

    /**
     * Debug SMTP: raw socket + Symfony transport test with step-by-step output.
     */
    public function debugSmtp(Request $request): JsonResponse
    {
        $smtp = $this->settingService->getGroup('smtp');
        $steps = [];
        $toEmail = $request->query('to', $smtp['from_email'] ?? 'test@test.com');

        // Step 1: Show loaded settings
        $steps[] = [
            'step'   => '1. DB SMTP Ayarları',
            'status' => 'info',
            'data'   => [
                'host'       => $smtp['host'] ?? '(boş)',
                'port'       => $smtp['port'] ?? '(boş)',
                'username'   => $smtp['username'] ?? '(boş)',
                'password'   => isset($smtp['password']) ? str_repeat('*', max(0, strlen($smtp['password']) - 4)) . substr($smtp['password'], -4) : '(boş)',
                'pass_len'   => isset($smtp['password']) ? strlen($smtp['password']) : 0,
                'pass_hex'   => isset($smtp['password']) ? bin2hex($smtp['password']) : '',
                'encryption' => $smtp['encryption'] ?? '(boş)',
                'from_name'  => $smtp['from_name'] ?? '(boş)',
                'from_email' => $smtp['from_email'] ?? '(boş)',
            ],
        ];

        // Step 2: Raw socket test
        $host = $smtp['host'] ?? '';
        $port = (int) ($smtp['port'] ?? 587);

        try {
            $errno = 0;
            $errstr = '';
            $context = stream_context_create(['ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ]]);

            $prefix = ($port === 465) ? 'ssl://' : '';
            $sock = @stream_socket_client(
                $prefix . $host . ':' . $port,
                $errno,
                $errstr,
                15,
                STREAM_CLIENT_CONNECT,
                $context
            );

            if (!$sock) {
                $steps[] = [
                    'step'   => '2. Raw Socket Bağlantısı',
                    'status' => 'fail',
                    'data'   => "Bağlantı kurulamadı: [{$errno}] {$errstr}",
                ];

                return response()->json(['steps' => $steps], 200);
            }

            $banner = fgets($sock, 1024);
            $steps[] = [
                'step'   => '2. Raw Socket Bağlantısı',
                'status' => 'ok',
                'data'   => 'Banner: ' . trim((string) $banner),
            ];

            // EHLO
            fwrite($sock, "EHLO localhost\r\n");
            $ehloResponse = '';
            while ($line = fgets($sock, 512)) {
                $ehloResponse .= $line;
                if (isset($line[3]) && $line[3] === ' ') {
                    break;
                }
            }
            $steps[] = [
                'step'   => '3. EHLO Yanıtı',
                'status' => 'ok',
                'data'   => trim($ehloResponse),
            ];

            // STARTTLS (only for non-SSL port)
            if ($port !== 465) {
                fwrite($sock, "STARTTLS\r\n");
                $starttlsResp = trim((string) fgets($sock, 512));
                $steps[] = [
                    'step'   => '4. STARTTLS Yanıtı',
                    'status' => str_starts_with($starttlsResp, '220') ? 'ok' : 'fail',
                    'data'   => $starttlsResp,
                ];

                if (str_starts_with($starttlsResp, '220')) {
                    $tlsResult = @stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT);
                    $meta = stream_get_meta_data($sock);
                    $steps[] = [
                        'step'   => '5. TLS Handshake',
                        'status' => $tlsResult ? 'ok' : 'fail',
                        'data'   => [
                            'result'  => $tlsResult ? 'BAŞARILI' : 'BAŞARISIZ',
                            'crypto'  => $meta['crypto'] ?? 'yok',
                        ],
                    ];

                    if (!$tlsResult) {
                        fclose($sock);

                        return response()->json(['steps' => $steps], 200);
                    }

                    // EHLO after TLS
                    fwrite($sock, "EHLO localhost\r\n");
                    $ehlo2 = '';
                    while ($line = fgets($sock, 512)) {
                        $ehlo2 .= $line;
                        if (isset($line[3]) && $line[3] === ' ') {
                            break;
                        }
                    }
                    $steps[] = [
                        'step'   => '6. EHLO (TLS sonrası)',
                        'status' => 'ok',
                        'data'   => trim($ehlo2),
                    ];
                }
            }

            // AUTH LOGIN
            $username = $smtp['username'] ?? '';
            $password = $smtp['password'] ?? '';

            fwrite($sock, "AUTH LOGIN\r\n");
            $authResp = trim((string) fgets($sock, 512));
            $steps[] = [
                'step'   => '7. AUTH LOGIN Başlatıldı',
                'status' => str_starts_with($authResp, '334') ? 'ok' : 'fail',
                'data'   => $authResp . ' (beklenen: 334)',
            ];

            if (str_starts_with($authResp, '334')) {
                // Send username
                fwrite($sock, base64_encode($username) . "\r\n");
                $userResp = trim((string) fgets($sock, 512));
                $steps[] = [
                    'step'   => '8. Username Gönderildi',
                    'status' => str_starts_with($userResp, '334') ? 'ok' : 'fail',
                    'data'   => [
                        'response'       => $userResp,
                        'username_b64'   => base64_encode($username),
                        'username_plain' => $username,
                    ],
                ];

                if (str_starts_with($userResp, '334')) {
                    // Send password
                    fwrite($sock, base64_encode($password) . "\r\n");
                    $passResp = trim((string) fgets($sock, 512));
                    $steps[] = [
                        'step'   => '9. Password Gönderildi',
                        'status' => str_starts_with($passResp, '235') ? 'ok' : 'fail',
                        'data'   => [
                            'response'     => $passResp,
                            'password_b64' => base64_encode($password),
                            'password_len' => strlen($password),
                        ],
                    ];
                }
            }

            fwrite($sock, "QUIT\r\n");
            fclose($sock);
        } catch (\Throwable $e) {
            $steps[] = [
                'step'   => 'Raw Socket Hatası',
                'status' => 'fail',
                'data'   => $e->getMessage(),
            ];
        }

        // Step 10: Symfony Transport test
        $steps[] = [
            'step'   => '10. Symfony Transport Testi Başlıyor...',
            'status' => 'info',
            'data'   => 'scheme=' . (($port === 465) ? 'smtps' : 'smtp') . ' port=' . $port,
        ];

        try {
            $scheme = ($port === 465) ? 'smtps' : 'smtp';
            $factory = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory();
            $transport = $factory->create(new \Symfony\Component\Mailer\Transport\Dsn(
                $scheme,
                $host,
                $smtp['username'] ?? null,
                $smtp['password'] ?? null,
                $port,
                ['verify_peer' => 0],
            ));

            $stream = $transport->getStream();
            if ($stream instanceof \Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream) {
                $stream->setTimeout(30);
            }

            $steps[] = [
                'step'   => '11. Symfony Transport Oluşturuldu',
                'status' => 'ok',
                'data'   => get_class($transport),
            ];

            // Try sending a real email via Symfony
            $email = (new \Symfony\Component\Mime\Email())
                ->from(new \Symfony\Component\Mime\Address($smtp['from_email'] ?? $username, $smtp['from_name'] ?? ''))
                ->to($toEmail)
                ->subject('Debug SMTP Test - ' . now()->format('H:i:s'))
                ->text('Bu bir SMTP debug test mailidir. ' . now()->toDateTimeString());

            $transport->send($email);

            $steps[] = [
                'step'   => '12. Symfony ile Mail Gönderildi!',
                'status' => 'ok',
                'data'   => 'Alıcı: ' . $toEmail,
            ];
        } catch (\Throwable $e) {
            $steps[] = [
                'step'   => '12. Symfony Transport HATA',
                'status' => 'fail',
                'data'   => [
                    'message' => $e->getMessage(),
                    'class'   => get_class($e),
                    'trace'   => array_slice(
                        array_map(fn ($t) => ($t['class'] ?? '') . '::' . ($t['function'] ?? '') . ' (' . basename($t['file'] ?? '') . ':' . ($t['line'] ?? '') . ')', $e->getTrace()),
                        0,
                        10
                    ),
                ],
            ];
        }

        return response()->json([
            'steps'     => $steps,
            'php'       => PHP_VERSION,
            'openssl'   => OPENSSL_VERSION_TEXT ?? 'N/A',
            'timestamp' => now()->toDateTimeString(),
        ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
