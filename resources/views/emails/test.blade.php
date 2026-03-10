<x-mail::message>
# Test E-postası

{!! nl2br(e($body)) !!}

Saygılarımızla,
{{ config('app.name') }}
</x-mail::message>
