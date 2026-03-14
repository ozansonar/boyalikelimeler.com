<x-mail::message>
# Tebrikler! Yazar Olarak Kabul Edildiniz

Merhaba **{{ $application->user->name }}**,

Yazar başvurunuz editör ekibimiz tarafından değerlendirilmiş ve **onaylanmıştır**.

Artık Boyalı Kelimeler platformunda eserlerinizi yayınlayabilir, topluluğumuzla buluşabilir ve yarışmalara katılabilirsiniz.

@if($application->user->username)
<x-mail::button :url="route('profile.show', $application->user->username)">
Profilime Git
</x-mail::button>
@endif

Boyalı Kelimeler ailesine hoş geldiniz!

Boyalı Kelimeler
</x-mail::message>
