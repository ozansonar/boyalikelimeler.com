<x-mail::message>
{!! nl2br(e($body)) !!}

Saygılarımızla,
{{ app(\App\Services\SettingService::class)->get('site_name', config('app.name')) }}
</x-mail::message>
