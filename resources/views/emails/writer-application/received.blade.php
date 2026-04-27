<x-mail::message>
# Yazar & Ressam Başvurunuz Alındı

Merhaba **{{ $application->user->name }}**,

Yazar & Ressam başvurunuz başarıyla alınmıştır. Editör ekibimiz başvurunuzu en kısa sürede değerlendirecektir.

**Başvuru Tarihi:** {{ $application->created_at->format('d.m.Y H:i') }}

Değerlendirme süreci tamamlandığında size e-posta ile bilgi verilecektir. Bu süre genellikle 3–5 iş gününü kapsamaktadır.

Boyalı Kelimeler
</x-mail::message>
