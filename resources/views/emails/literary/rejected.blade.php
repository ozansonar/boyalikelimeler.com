<x-mail::message>
# Eseriniz Hakkında Bilgilendirme

Merhaba {{ $work->author->name }},

**"{{ $work->title }}"** başlıklı eseriniz editörlerimiz tarafından incelendi, ancak maalesef bu aşamada yayınlanması uygun görülmedi.

Yeni eserlerinizi bekliyoruz. Daha fazla bilgi için bizimle iletişime geçebilirsiniz.

<x-mail::button :url="route('myposts.index')">
Eserlerimi Görüntüle
</x-mail::button>

Saygılarımızla,
Boyalı Kelimeler
</x-mail::message>
