@props(['url'])
@php
    $settingService = app(\App\Services\SettingService::class);
    $siteName = $settingService->get('site_name', config('app.name'));
    $smtpSettings = $settingService->getGroup('smtp');
    $theme = $settingService->getGroup('mail_theme');
    $hasLogo = !empty($smtpSettings['mail_logo']);
    $primaryColor = $theme['primary_color'] ?? '#D4AF37';
    $primaryDark = $theme['primary_dark'] ?? '#A68B4B';
    $bgColor = $theme['bg_color'] ?? '#0F0F12';
@endphp
<tr>
<td class="header" style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $primaryDark }}); border-radius: 12px 12px 0 0; padding: 30px 40px; text-align: center;">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none; color: {{ $bgColor }};">
@if ($hasLogo)
<img src="cid:mail-logo" alt="{{ $siteName }}" style="max-height: 150px; width: auto; display: block; margin: 0 auto;">
@else
<span style="font-family: Georgia, 'Times New Roman', Times, serif; font-size: 24px; font-weight: bold; color: {{ $bgColor }}; letter-spacing: 1px;">{{ $siteName }}</span>
@endif
</a>
</td>
</tr>
