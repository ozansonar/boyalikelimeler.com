<x-mail::message>
# Yayındaki Eser Güncellendi

Merhaba,

**{{ $work->author->name }}**, daha önce onaylanmış olan **"{{ $work->title }}"** başlıklı eserinde güncelleme yaptı.

Eser yayından kaldırılmış olup tekrar onayınızı beklemektedir.

<x-mail::button :url="route('admin.literary-works.show', $work->id)">
Eseri İncele
</x-mail::button>

Boyalı Kelimeler Sistem Bildirimi
</x-mail::message>
