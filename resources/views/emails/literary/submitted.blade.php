<x-mail::message>
# Yeni Edebiyat Eseri Gönderildi

Merhaba,

**{{ $work->author->name }}** yeni bir edebiyat eseri gönderdi ve onayınızı bekliyor.

**Eser Başlığı:** {{ $work->title }}
**Kategori:** {{ $work->category->name }}
**Gönderim Tarihi:** {{ $work->created_at->format('d.m.Y H:i') }}

<x-mail::button :url="route('admin.literary-works.show', $work->id)">
Eseri İncele
</x-mail::button>

Boyalı Kelimeler Sistem Bildirimi
</x-mail::message>
