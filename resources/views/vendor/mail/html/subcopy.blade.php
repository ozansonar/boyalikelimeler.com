@php
    $theme = app(\App\Services\SettingService::class)->getGroup('mail_theme');
    $textMuted = $theme['text_muted'] ?? '#9B9EA3';
    $primaryColor = $theme['primary_color'] ?? '#D4AF37';
@endphp
<table class="subcopy" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-top: 1px solid rgba(255,255,255,0.06); margin-top: 24px; padding-top: 16px;">
<tr>
<td style="font-size: 12px; color: {{ $textMuted }}; line-height: 1.5;">
{{ Illuminate\Mail\Markdown::parse($slot) }}
</td>
</tr>
</table>
