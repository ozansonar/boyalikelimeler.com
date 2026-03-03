<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ContactMessage;
use Illuminate\Database\Seeder;

class ContactMessageSeeder extends Seeder
{
    public function run(): void
    {
        $messages = [
            [
                'name'       => 'Ahmet Yılmaz',
                'email'      => 'ahmet.yilmaz@example.com',
                'subject'    => 'genel',
                'message'    => 'Merhaba, Boyalı Kelimeler platformunu çok beğeniyorum. Edebiyat kategorisinde daha fazla içerik olması harika olurdu. Ayrıca mobil uygulamanız var mı? Telefondan da takip etmek istiyorum.',
                'is_read'    => false,
                'ip_address' => '192.168.1.10',
                'created_at' => now()->subHours(2),
            ],
            [
                'name'       => 'Zeynep Kaya',
                'email'      => 'zeynep.kaya@example.com',
                'subject'    => 'isbirligi',
                'message'    => 'Merhaba, bir yayınevi olarak sizinle iş birliği yapmak istiyoruz. Genç yazarlarımızın eserlerini platformunuzda yayınlama imkânı sunmak istiyoruz. Detayları konuşmak için uygun bir zaman belirleyebilir misiniz?',
                'is_read'    => false,
                'ip_address' => '10.0.0.15',
                'created_at' => now()->subHours(5),
            ],
            [
                'name'       => 'Mehmet Demir',
                'email'      => 'mehmet.demir@example.com',
                'subject'    => 'teknik',
                'message'    => 'Yazılarım sayfasında kapak görseli yüklerken hata alıyorum. Chrome kullanıyorum, sürüm 120. Dosya boyutu 2MB civarında, JPEG formatında. Lütfen yardımcı olur musunuz?',
                'is_read'    => true,
                'is_starred' => true,
                'ip_address' => '172.16.0.20',
                'created_at' => now()->subDays(1),
            ],
            [
                'name'       => 'Elif Özkan',
                'email'      => 'elif.ozkan@example.com',
                'subject'    => 'yarisma',
                'message'    => 'Altın Kalem yarışması için başvuru tarihlerini öğrenmek istiyorum. Geçen yılki yarışmayı kaçırmıştım, bu yıl mutlaka katılmak istiyorum. Ayrıca şiir kategorisi var mı?',
                'is_read'    => true,
                'reply_body' => 'Merhaba Elif Hanım, Altın Kalem yarışmamız her yıl Nisan ayında başlamaktadır. Şiir, öykü ve deneme kategorilerimiz mevcuttur. Detaylı bilgi için yarışma sayfamızı takip edebilirsiniz. Katılımınızı bekliyoruz!',
                'replied_by' => 1,
                'replied_at' => now()->subHours(12),
                'ip_address' => '192.168.1.55',
                'created_at' => now()->subDays(2),
            ],
            [
                'name'       => 'Can Çelik',
                'email'      => 'can.celik@example.com',
                'subject'    => 'oneri',
                'message'    => 'Platformda karanlık mod çok güzel ama bazı sayfalarda metin okunurluğu düşük. Özellikle blog detay sayfasında paragraf aralarındaki boşluklar artırılabilir. Ayrıca yazı tipi boyutu biraz büyütülebilir.',
                'is_read'    => false,
                'ip_address' => '10.10.10.30',
                'created_at' => now()->subDays(3),
            ],
            [
                'name'       => 'Selin Arslan',
                'email'      => 'selin.arslan@example.com',
                'subject'    => 'diger',
                'message'    => 'Merhaba, ben bir lise edebiyat öğretmeniyim. Öğrencilerimle birlikte platformunuzu kullanmak istiyorum. Toplu kayıt için özel bir imkânınız var mı? Eğitim amaçlı kullanım için indirim yapıyor musunuz?',
                'is_read'    => true,
                'is_starred' => true,
                'ip_address' => '172.20.0.10',
                'created_at' => now()->subDays(4),
            ],
            [
                'name'       => 'Burak Tuncer',
                'email'      => 'burak.tuncer@example.com',
                'subject'    => 'isbirligi',
                'message'    => 'Selam, ben bir podcast yapımcısıyım. Edebiyat ve sanat üzerine bir podcast serisi hazırlıyorum. Platformunuzdaki yazarlarla röportaj yapmak isterim. Bu konuda nasıl ilerleyebiliriz?',
                'is_read'    => false,
                'ip_address' => '192.168.2.100',
                'created_at' => now()->subDays(5),
            ],
            [
                'name'       => 'Deniz Yıldırım',
                'email'      => 'deniz.yildirim@example.com',
                'subject'    => 'genel',
                'message'    => 'Platformunuzu bir arkadaşımın tavsiyesiyle keşfettim. Gerçekten çok kaliteli içerikler var. Yazar olarak nasıl başvuru yapabilirim? Şiir ve deneme yazıyorum.',
                'is_read'    => true,
                'reply_body' => 'Merhaba Deniz Bey, platformumuza ilginiz için teşekkür ederiz! Yazar olmak için ücretsiz kayıt olduktan sonra profil sayfanızdan "Yazar Başvurusu" butonuna tıklayabilirsiniz. Editör ekibimiz başvurunuzu değerlendirecektir.',
                'replied_by' => 1,
                'replied_at' => now()->subDays(4),
                'ip_address' => '10.0.1.25',
                'created_at' => now()->subDays(6),
            ],
        ];

        foreach ($messages as $messageData) {
            ContactMessage::updateOrCreate(
                ['email' => $messageData['email'], 'subject' => $messageData['subject']],
                $messageData,
            );
        }
    }
}
