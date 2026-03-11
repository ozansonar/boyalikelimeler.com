<x-mail::message>
# Yeni Cevap Onay Bekliyor

Merhaba,

**Söz Meydanı**'nda bir soruya yeni bir cevap yazıldı ve onayınızı bekliyor.

**Soru:** {{ $answer->question?->title }}

**Cevaplayan:** {{ $answer->user?->name }}

**Cevap:**
> {{ Str::limit($answer->body, 300) }}

Cevabı incelemek ve onaylamak/reddetmek için aşağıdaki butonu kullanabilirsiniz.

<x-mail::button :url="route('admin.qna.answers.index', ['status' => 'pending'])">
Cevapları İncele
</x-mail::button>

Saygılarımızla,
Boyalı Kelimeler
</x-mail::message>
