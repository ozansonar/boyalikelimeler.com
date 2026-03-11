<x-mail::message>
# İçeriğinize Yorum Yapıldı!

Merhaba {{ $comment->commentable?->author?->name }},

Harika haber! **"{{ $comment->contentTitle() }}"** başlıklı {{ $comment->contentTypeLabel() }} içeriğinize yapılan bir yorum onaylandı ve yayına alındı.

**Yazan:** {{ $comment->fullName() }}

**Puan:** {{ str_repeat('★', $comment->rating) }}{{ str_repeat('☆', 5 - $comment->rating) }} ({{ $comment->rating }}/5)

**Yorum:**
> {{ $comment->body }}

@if($comment->commentable_type === \App\Models\LiteraryWork::class)
<x-mail::button :url="route('literary-works.show', $comment->commentable?->slug ?? '')">
İçeriği Görüntüle
</x-mail::button>
@else
<x-mail::button :url="route('blog.show', $comment->commentable?->slug ?? '')">
Blog Yazısını Görüntüle
</x-mail::button>
@endif

Saygılarımızla,
Boyalı Kelimeler
</x-mail::message>
