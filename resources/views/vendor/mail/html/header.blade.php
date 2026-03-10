@props(['url'])
@php
    $settingService = app(\App\Services\SettingService::class);
    $siteName = $settingService->get('site_name', config('app.name'));
    $smtpSettings = $settingService->getGroup('smtp');
    $hasLogo = !empty($smtpSettings['mail_logo']);
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if ($hasLogo)
<img src="cid:mail-logo" class="logo" alt="{{ $siteName }}" style="max-height: 60px; width: auto;">
@else
{{ $siteName }}
@endif
</a>
</td>
</tr>
