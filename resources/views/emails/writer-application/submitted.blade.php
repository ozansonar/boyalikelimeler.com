<x-mail::message>
# Yeni Yazar Başvurusu

Merhaba,

**{{ $application->user->name }}** ({{ $application->user->email }}) yeni bir yazar başvurusu yaptı ve değerlendirmenizi bekliyor.

**Başvuru Tarihi:** {{ $application->created_at->format('d.m.Y H:i') }}

<x-mail::button :url="route('admin.writer-applications.show', $application->id)">
Başvuruyu İncele
</x-mail::button>

Boyalı Kelimeler Sistem Bildirimi
</x-mail::message>
