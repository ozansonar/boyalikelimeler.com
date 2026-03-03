<x-mail::message>
# Eseriniz İçin Revize Talebi

Merhaba {{ $work->author->name }},

**"{{ $work->title }}"** başlıklı eseriniz editörlerimiz tarafından incelendi. Yayına alınabilmesi için bazı düzenlemeler yapmanız gerekmektedir.

**Editör Notu:**

<x-mail::panel>
{{ $reason }}
</x-mail::panel>

Lütfen gerekli düzenlemeleri yaparak eserinizi tekrar gönderin.

<x-mail::button :url="route('myposts.edit', $work)">
Eseri Düzenle
</x-mail::button>

Saygılarımızla,
Boyalı Kelimeler
</x-mail::message>
