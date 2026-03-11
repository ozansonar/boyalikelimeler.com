<x-mail::message>
# Sorunuz Onaylandı!

Merhaba {{ $question->user?->name }},

Harika haber! **Söz Meydanı**'nda sorduğunuz soru onaylandı ve yayına alındı.

**Kategori:** {{ $question->category?->name }}

**Soru:** {{ $question->title }}

Artık diğer üyeler sorunuzu görebilir ve cevap yazabilir.

<x-mail::button :url="route('qna.show', ['categorySlug' => $question->category?->slug ?? '', 'questionSlug' => $question->slug])">
Sorunuzu Görüntüleyin
</x-mail::button>

Saygılarımızla,
Boyalı Kelimeler
</x-mail::message>
