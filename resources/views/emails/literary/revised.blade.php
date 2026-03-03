<x-mail::message>
# Eser Revize Edildi

Merhaba,

**{{ $work->author->name }}**, daha önce revize istenen **"{{ $work->title }}"** başlıklı eserini düzenleyerek tekrar gönderdi.

Eseri inceleyip onaylayabilir veya tekrar revize talep edebilirsiniz.

<x-mail::button :url="route('admin.literary-works.show', $work->id)">
Eseri İncele
</x-mail::button>

Boyalı Kelimeler Sistem Bildirimi
</x-mail::message>
