<x-mail::message>
# Eseriniz Onaylandı!

Merhaba {{ $work->author->name }},

Harika haber! **"{{ $work->title }}"** başlıklı eseriniz editörlerimiz tarafından incelendi ve **onaylanarak yayına alındı**.

Tebrik ederiz! Eseriniz artık tüm okuyucular tarafından görüntülenebilir.

<x-mail::button :url="route('myposts.index')">
Eserlerimi Görüntüle
</x-mail::button>

Saygılarımızla,
Boyalı Kelimeler
</x-mail::message>
