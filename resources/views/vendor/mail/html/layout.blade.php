@php
    $settingService = app(\App\Services\SettingService::class);
    $theme = $settingService->getGroup('mail_theme');
    $social = $settingService->getGroup('social');
    $siteName = $settingService->get('site_name', config('app.name'));
    $siteUrl = config('app.url');

    // Theme colors with defaults matching front site
    $primaryColor = $theme['primary_color'] ?? '#D4AF37';
    $primaryDark = $theme['primary_dark'] ?? '#A68B4B';
    $bgColor = $theme['bg_color'] ?? '#0F0F12';
    $cardBg = $theme['card_bg'] ?? '#1A1A1E';
    $textColor = $theme['text_color'] ?? '#F5F5F0';
    $textMuted = $theme['text_muted'] ?? '#9B9EA3';
    $footerText = $theme['footer_text'] ?? '';
    $showSocial = ($theme['show_social'] ?? '1') === '1';
@endphp
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<title>{{ $siteName }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light dark">
<meta name="supported-color-schemes" content="light dark">
<style>
/* Reset */
body, table, td, p, a, li, blockquote { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
table, td { mso-table-lspace: 0; mso-table-rspace: 0; }
img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
body { margin: 0; padding: 0; width: 100% !important; height: 100% !important; }
a { color: {{ $primaryColor }}; text-decoration: none; }
a:hover { text-decoration: underline; }

/* Layout */
.wrapper { background-color: {{ $bgColor }}; width: 100%; padding: 30px 0; }
.container { max-width: 600px; margin: 0 auto; }

/* Header */
.header { background: linear-gradient(135deg, {{ $primaryColor }}, {{ $primaryDark }}); border-radius: 12px 12px 0 0; padding: 30px 40px; text-align: center; }
.header a { color: {{ $bgColor }}; text-decoration: none; }
.header img { max-height: 50px; width: auto; }
.header .site-name { font-family: Georgia, 'Times New Roman', Times, serif; font-size: 24px; font-weight: bold; color: {{ $bgColor }}; letter-spacing: 1px; }

/* Body */
.body-wrapper { background-color: {{ $cardBg }}; padding: 40px; }
.body-wrapper h1, .body-wrapper h2, .body-wrapper h3 { color: {{ $textColor }}; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; margin-top: 0; }
.body-wrapper h1 { font-size: 22px; margin-bottom: 16px; }
.body-wrapper p { color: {{ $textMuted }}; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 15px; line-height: 1.7; margin: 0 0 16px; }
.body-wrapper strong { color: {{ $textColor }}; }

/* Divider */
.body-wrapper hr { border: none; border-top: 1px solid rgba(255,255,255,0.08); margin: 24px 0; }

/* Button */
.action { margin: 28px 0; }
.button { display: inline-block; background: linear-gradient(135deg, {{ $primaryColor }}, {{ $primaryDark }}); color: {{ $bgColor }} !important; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 15px; font-weight: 600; text-decoration: none !important; padding: 14px 32px; border-radius: 8px; letter-spacing: 0.3px; }

/* Footer */
.footer-wrapper { background-color: {{ $cardBg }}; border-top: 1px solid rgba(255,255,255,0.06); padding: 28px 40px; border-radius: 0 0 12px 12px; text-align: center; }
.footer-wrapper p { color: {{ $textMuted }}; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 12px; line-height: 1.6; margin: 0 0 8px; }
.footer-divider { border: none; border-top: 1px solid rgba(255,255,255,0.06); margin: 16px 0; }
.social-links { margin: 12px 0; }
.social-link { display: inline-block; margin: 0 8px; color: {{ $textMuted }}; text-decoration: none; font-size: 13px; }
.social-link:hover { color: {{ $primaryColor }}; }

/* Subcopy */
.subcopy { border-top: 1px solid rgba(255,255,255,0.06); margin-top: 24px; padding-top: 16px; }
.subcopy p { font-size: 12px; color: {{ $textMuted }}; line-height: 1.5; }
.subcopy a { color: {{ $primaryColor }}; word-break: break-all; }

/* Responsive */
@media only screen and (max-width: 620px) {
    .wrapper { padding: 16px 0 !important; }
    .header { padding: 24px 20px !important; border-radius: 0 !important; }
    .body-wrapper { padding: 28px 20px !important; }
    .footer-wrapper { padding: 24px 20px !important; border-radius: 0 !important; }
    .button { padding: 12px 24px !important; font-size: 14px !important; }
}
</style>
{!! $head ?? '' !!}
</head>
<body>
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" class="wrapper" style="background-color: {{ $bgColor }}; padding: 30px 0;">
<tr>
<td align="center">
<table width="600" cellpadding="0" cellspacing="0" role="presentation" class="container" style="max-width: 600px; margin: 0 auto;">

{!! $header ?? '' !!}

<!-- Body -->
<tr>
<td class="body-wrapper" style="background-color: {{ $cardBg }}; padding: 40px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
{!! Illuminate\Mail\Markdown::parse($slot) !!}

{!! $subcopy ?? '' !!}
</td>
</tr>

<!-- Footer -->
<tr>
<td class="footer-wrapper" style="background-color: {{ $cardBg }}; border-top: 1px solid rgba(255,255,255,0.06); padding: 28px 40px; border-radius: 0 0 12px 12px; text-align: center;">

@if($showSocial && collect($social)->filter()->isNotEmpty())
<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center" style="padding-bottom: 16px;">
@if(!empty($social['instagram']))
<a href="{{ $social['instagram'] }}" target="_blank" style="display: inline-block; margin: 0 6px; color: {{ $textMuted }}; text-decoration: none; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 13px;">Instagram</a>
@endif
@if(!empty($social['twitter']))
<a href="{{ $social['twitter'] }}" target="_blank" style="display: inline-block; margin: 0 6px; color: {{ $textMuted }}; text-decoration: none; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 13px;">Twitter</a>
@endif
@if(!empty($social['facebook']))
<a href="{{ $social['facebook'] }}" target="_blank" style="display: inline-block; margin: 0 6px; color: {{ $textMuted }}; text-decoration: none; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 13px;">Facebook</a>
@endif
@if(!empty($social['youtube']))
<a href="{{ $social['youtube'] }}" target="_blank" style="display: inline-block; margin: 0 6px; color: {{ $textMuted }}; text-decoration: none; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 13px;">YouTube</a>
@endif
@if(!empty($social['tiktok']))
<a href="{{ $social['tiktok'] }}" target="_blank" style="display: inline-block; margin: 0 6px; color: {{ $textMuted }}; text-decoration: none; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 13px;">TikTok</a>
@endif
@if(!empty($social['linkedin']))
<a href="{{ $social['linkedin'] }}" target="_blank" style="display: inline-block; margin: 0 6px; color: {{ $textMuted }}; text-decoration: none; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 13px;">LinkedIn</a>
@endif
</td>
</tr>
</table>
<hr style="border: none; border-top: 1px solid rgba(255,255,255,0.06); margin: 0 0 16px;">
@endif

@if($footerText !== '')
<p style="color: {{ $textMuted }}; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 12px; line-height: 1.6; margin: 0 0 8px;">
{!! nl2br(e($footerText)) !!}
</p>
@endif

<p style="color: {{ $textMuted }}; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 12px; line-height: 1.6; margin: 0;">
&copy; {{ date('Y') }} <a href="{{ $siteUrl }}" style="color: {{ $primaryColor }}; text-decoration: none;">{{ $siteName }}</a>. T&#252;m haklar&#305; sakl&#305;d&#305;r.
</p>
</td>
</tr>

</table>
</td>
</tr>
</table>
</body>
</html>
