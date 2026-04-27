<x-mail::message>
# Yazar & Ressam Başvurunuz Hakkında

Merhaba **{{ $application->user->name }}**,

Yazar & Ressam başvurunuz editör ekibimiz tarafından değerlendirilmiştir. Maalesef başvurunuz şu an için uygun bulunamamıştır.

@if($application->admin_note)
**Değerlendirme Notu:**

> {{ $application->admin_note }}
@endif

30 gün sonra tekrar başvurabilirsiniz. Başvurunuzu geliştirmek için yukarıdaki değerlendirme notunu dikkate almanızı öneririz.

Boyalı Kelimeler
</x-mail::message>
