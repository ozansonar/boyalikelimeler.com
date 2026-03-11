<x-mail::message>
# Yeni Soru Onay Bekliyor

Merhaba,

**Söz Meydanı**'nda yeni bir soru soruldu ve onayınızı bekliyor.

**Kategori:** {{ $question->category?->name }}

**Soran:** {{ $question->user?->name }}

**Soru Başlığı:** {{ $question->title }}

**Soru Detayı:**
> {{ Str::limit($question->body, 300) }}

Soruyu incelemek ve onaylamak/reddetmek için aşağıdaki butonu kullanabilirsiniz.

<x-mail::button :url="route('admin.qna.questions.index', ['status' => 'pending'])">
Soruları İncele
</x-mail::button>

Saygılarımızla,
Boyalı Kelimeler
</x-mail::message>
