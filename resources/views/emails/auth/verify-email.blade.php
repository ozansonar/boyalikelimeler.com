<x-mail::message>
# E-posta Adresinizi Doğrulayın

Merhaba {{ $user->name }},

Boyalı Kelimeler topluluğuna hoş geldiniz! Hesabınızı aktif hale getirmek için lütfen aşağıdaki butona tıklayın.

<x-mail::button :url="$verificationUrl">
E-posta Adresimi Doğrula
</x-mail::button>

Bu link **60 dakika** içinde geçerliliğini yitirecektir.

Eğer bu hesabı siz oluşturmadıysanız herhangi bir işlem yapmanıza gerek yoktur.

Saygılarımızla,
Boyalı Kelimeler
</x-mail::message>
