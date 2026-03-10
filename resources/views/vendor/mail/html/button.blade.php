@props([
    'url',
    'color' => 'primary',
    'align' => 'center',
])
@php
    $theme = app(\App\Services\SettingService::class)->getGroup('mail_theme');
    $primaryColor = $theme['primary_color'] ?? '#D4AF37';
    $primaryDark = $theme['primary_dark'] ?? '#A68B4B';
    $bgColor = $theme['bg_color'] ?? '#0F0F12';
@endphp
<table class="action" align="{{ $align }}" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin: 28px 0;">
<tr>
<td align="{{ $align }}">
<table border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<a href="{{ $url }}" class="button" target="_blank" rel="noopener" style="display: inline-block; background: linear-gradient(135deg, {{ $primaryColor }}, {{ $primaryDark }}); color: {{ $bgColor }} !important; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 15px; font-weight: 600; text-decoration: none; padding: 14px 32px; border-radius: 8px; letter-spacing: 0.3px;">{!! $slot !!}</a>
</td>
</tr>
</table>
</td>
</tr>
</table>
