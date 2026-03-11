<x-mail::message>
# Cevabınız Onaylandı!

Merhaba {{ $answer->user?->name }},

Harika haber! **Söz Meydanı**'nda yazdığınız cevap onaylandı ve yayına alındı.

**Soru:** {{ $answer->question?->title }}

Cevabınızı ve diğer katılımcıların görüşlerini görmek için aşağıdaki butonu kullanabilirsiniz.

<x-mail::button :url="route('qna.show', ['categorySlug' => $answer->question?->category?->slug ?? '', 'questionSlug' => $answer->question?->slug ?? ''])">
Soruyu Görüntüleyin
</x-mail::button>

Saygılarımızla,
Boyalı Kelimeler
</x-mail::message>
