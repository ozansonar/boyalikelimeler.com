<x-mail::message>
# Yeni Kullanıcı Kaydı

Merhaba,

Siteye yeni bir kullanıcı kayıt oldu.

**Ad Soyad:** {{ $newUser->name }}
**E-posta:** {{ $newUser->email }}
**Kayıt Tarihi:** {{ $newUser->created_at->format('d.m.Y H:i') }}

<x-mail::button :url="url(route('admin.dashboard', [], false))">
Admin Paneline Git
</x-mail::button>

Saygılarımızla,
Boyalı Kelimeler
</x-mail::message>
