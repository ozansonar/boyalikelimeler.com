@php
    $primaryColor = $theme['primary_color'] ?? '#D4AF37';
    $primaryDark = $theme['primary_dark'] ?? '#A68B4B';
    $bgColor = $theme['bg_color'] ?? '#0F0F12';
    $cardBg = $theme['card_bg'] ?? '#1A1A1E';
    $textColor = $theme['text_color'] ?? '#F5F5F0';
    $textMuted = $theme['text_muted'] ?? '#9B9EA3';
    $footerText = $theme['footer_text'] ?? '';
    $showSocial = ($theme['show_social'] ?? '1') === '1';
    $fontStack = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif";
@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body { margin: 0; padding: 0; background: {{ $bgColor }}; font-family: {{ $fontStack }}; }
</style>
</head>
<body>
<table width="100%" cellpadding="0" cellspacing="0" style="background-color: {{ $bgColor }}; padding: 24px 0;">
<tr>
<td align="center">
<table width="520" cellpadding="0" cellspacing="0" style="max-width: 520px; margin: 0 auto;">

{{-- Header --}}
<tr>
<td style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $primaryDark }}); border-radius: 12px 12px 0 0; padding: 24px 32px; text-align: center;">
<span style="font-family: Georgia, 'Times New Roman', Times, serif; font-size: 20px; font-weight: bold; color: {{ $bgColor }}; letter-spacing: 1px;">{{ $siteName }}</span>
</td>
</tr>

{{-- Body --}}
<tr>
<td style="background-color: {{ $cardBg }}; padding: 32px;">
<h1 style="color: {{ $textColor }}; font-family: {{ $fontStack }}; font-size: 20px; margin: 0 0 14px;">E-posta Adresinizi Do&#287;rulay&#305;n</h1>
<p style="color: {{ $textMuted }}; font-family: {{ $fontStack }}; font-size: 14px; line-height: 1.7; margin: 0 0 14px;">Merhaba <strong style="color: {{ $textColor }};">Kullan&#305;c&#305;</strong>,</p>
<p style="color: {{ $textMuted }}; font-family: {{ $fontStack }}; font-size: 14px; line-height: 1.7; margin: 0 0 24px;">{{ $siteName }} toplulu&#287;una ho&#351; geldiniz! Hesab&#305;n&#305;z&#305; aktif hale getirmek i&#231;in l&#252;tfen a&#351;a&#287;&#305;daki butona t&#305;klay&#305;n.</p>

{{-- Button --}}
<table width="100%" cellpadding="0" cellspacing="0" style="margin: 24px 0;">
<tr>
<td align="center">
<table cellpadding="0" cellspacing="0">
<tr>
<td>
<a href="#" style="display: inline-block; background: linear-gradient(135deg, {{ $primaryColor }}, {{ $primaryDark }}); color: {{ $bgColor }}; font-family: {{ $fontStack }}; font-size: 14px; font-weight: 600; text-decoration: none; padding: 12px 28px; border-radius: 8px; letter-spacing: 0.3px;">E-posta Adresimi Do&#287;rula</a>
</td>
</tr>
</table>
</td>
</tr>
</table>

<p style="color: {{ $textMuted }}; font-family: {{ $fontStack }}; font-size: 14px; line-height: 1.7; margin: 0 0 14px;">Bu link <strong style="color: {{ $textColor }};">60 dakika</strong> i&#231;inde ge&#231;erlili&#287;ini yitirecektir.</p>
<p style="color: {{ $textMuted }}; font-family: {{ $fontStack }}; font-size: 14px; line-height: 1.7; margin: 0 0 14px;">E&#287;er bu hesab&#305; siz olu&#351;turmad&#305;ysan&#305;z herhangi bir i&#351;lem yapman&#305;za gerek yoktur.</p>
<p style="color: {{ $textMuted }}; font-family: {{ $fontStack }}; font-size: 14px; line-height: 1.7; margin: 0;">Sayg&#305;lar&#305;m&#305;zla,<br>{{ $siteName }}</p>
</td>
</tr>

{{-- Footer --}}
<tr>
<td style="background-color: {{ $cardBg }}; border-top: 1px solid rgba(255,255,255,0.06); padding: 24px 32px; border-radius: 0 0 12px 12px; text-align: center;">

@if($showSocial && collect($social)->filter()->isNotEmpty())
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td align="center" style="padding-bottom: 14px;">
@if(!empty($social['instagram']))
<a href="#" style="display: inline-block; margin: 0 5px; color: {{ $textMuted }}; text-decoration: none; font-family: {{ $fontStack }}; font-size: 12px;">Instagram</a>
@endif
@if(!empty($social['twitter']))
<a href="#" style="display: inline-block; margin: 0 5px; color: {{ $textMuted }}; text-decoration: none; font-family: {{ $fontStack }}; font-size: 12px;">Twitter</a>
@endif
@if(!empty($social['facebook']))
<a href="#" style="display: inline-block; margin: 0 5px; color: {{ $textMuted }}; text-decoration: none; font-family: {{ $fontStack }}; font-size: 12px;">Facebook</a>
@endif
@if(!empty($social['youtube']))
<a href="#" style="display: inline-block; margin: 0 5px; color: {{ $textMuted }}; text-decoration: none; font-family: {{ $fontStack }}; font-size: 12px;">YouTube</a>
@endif
@if(!empty($social['tiktok']))
<a href="#" style="display: inline-block; margin: 0 5px; color: {{ $textMuted }}; text-decoration: none; font-family: {{ $fontStack }}; font-size: 12px;">TikTok</a>
@endif
@if(!empty($social['linkedin']))
<a href="#" style="display: inline-block; margin: 0 5px; color: {{ $textMuted }}; text-decoration: none; font-family: {{ $fontStack }}; font-size: 12px;">LinkedIn</a>
@endif
</td>
</tr>
</table>
<hr style="border: none; border-top: 1px solid rgba(255,255,255,0.06); margin: 0 0 14px;">
@endif

@if($footerText !== '')
<p style="color: {{ $textMuted }}; font-family: {{ $fontStack }}; font-size: 11px; line-height: 1.6; margin: 0 0 6px;">{!! nl2br(e($footerText)) !!}</p>
@endif

<p style="color: {{ $textMuted }}; font-family: {{ $fontStack }}; font-size: 11px; line-height: 1.6; margin: 0;">&copy; {{ date('Y') }} <a href="{{ $siteUrl }}" style="color: {{ $primaryColor }}; text-decoration: none;">{{ $siteName }}</a>. T&#252;m haklar&#305; sakl&#305;d&#305;r.</p>
</td>
</tr>

</table>
</td>
</tr>
</table>
</body>
</html>
