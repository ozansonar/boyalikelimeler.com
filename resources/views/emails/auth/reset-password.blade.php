<x-mail::message>
# Şifre Sıfırlama Talebi

Merhaba {{ $user->name }},

Hesabınız için şifre sıfırlama talebinde bulunuldu. Aşağıdaki butona tıklayarak yeni şifrenizi belirleyebilirsiniz.

<x-mail::button :url="$resetUrl">
Şifremi Sıfırla
</x-mail::button>

Bu link **60 dakika** içinde geçerliliğini yitirecektir.

Eğer bu talebi siz yapmadıysanız herhangi bir işlem yapmanıza gerek yoktur.

Saygılarımızla,
Boyalı Kelimeler
</x-mail::message>
