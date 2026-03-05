<x-mail::message>
# Yeni Yorum Onay Bekliyor

Merhaba,

**"{{ $comment->contentTitle() }}"** başlıklı {{ $comment->contentTypeLabel() }} içeriğine yeni bir yorum yapıldı.

**Yazan:** {{ $comment->fullName() }}
**E-posta:** {{ $comment->commenterEmail() }}
**Puan:** {{ str_repeat('★', $comment->rating) }}{{ str_repeat('☆', 5 - $comment->rating) }} ({{ $comment->rating }}/5)

**Yorum:**
> {{ $comment->body }}

Yorumu incelemek ve onaylamak/reddetmek için aşağıdaki butonu kullanabilirsiniz.

<x-mail::button :url="route('admin.comments.index', ['status' => 'pending'])">
Yorumları İncele
</x-mail::button>

Saygılarımızla,
Boyalı Kelimeler
</x-mail::message>
