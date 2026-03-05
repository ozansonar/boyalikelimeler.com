<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\LiteraryWorkStatus;
use App\Models\LiteraryWork;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoCommentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all(['id', 'name']);

        $publishedWorkIds = LiteraryWork::where('status', LiteraryWorkStatus::Approved)
            ->pluck('id')
            ->toArray();

        if (empty($publishedWorkIds) || $users->isEmpty()) {
            $this->command->warn('Kullanıcı veya yayınlanmış eser bulunamadı.');
            return;
        }

        $commentTemplates = [
            'Bu eseri okurken içime bir huzur doldu, kelimelerin akışı çok doğal ve etkileyici.',
            'Yazarın duygu dünyasını bu kadar güzel yansıtması beni derinden etkiledi.',
            'Harika bir eser, her satırında ayrı bir anlam keşfettim. Tekrar tekrar okuyacağım.',
            'Çok beğendim, dili akıcı ve samimi. Yazarın diğer eserlerini de merakla bekliyorum.',
            'Bu tarz eserlere daha çok ihtiyacımız var. Edebiyatımız için değerli bir katkı.',
            'Kelimelerin gücünü bir kez daha hissettim. Çok dokunaklı ve anlamlı bir çalışma.',
            'Okurken kendimi eserin içinde buldum, karakterlerin duygularını yaşadım.',
            'Sade ve etkileyici bir anlatım. Gereksiz süslemeler yok, özün ta kendisi.',
            'Bu eserdeki imgeler çok güçlü, her bir cümle ayrı bir tablo gibi.',
            'Edebiyatın iyileştirici gücünü bu eserle bir kez daha deneyimledim.',
            'Yazarın bakış açısı çok özgün, farklı bir perspektif sunuyor okuyucuya.',
            'Akıcı bir dil, derin bir içerik. Okumaya başlayınca bırakamadım.',
            'Her okuduğumda yeni bir şey keşfediyorum, katmanlı bir eser.',
            'Duygusal derinliği ve edebi kalitesiyle öne çıkan güzel bir çalışma.',
            'Yazarın samimiyeti her satırdan hissediliyor, içten ve doğal bir anlatım.',
            'Bu eser beni düşündürdü, sorgulamama neden oldu. Gerçek edebiyat budur.',
            'Kısa ama etkili bir eser. Az sözcükle çok şey anlatmayı başarmış.',
            'Türk edebiyatına değerli bir katkı, yazarı tebrik ediyorum.',
            'Okuduktan sonra uzun süre etkisinden çıkamadım, muhteşem bir eser.',
            'Dili ve üslubu çok hoşuma gitti. Modern edebiyatın güzel bir örneği.',
            'Bu eserdeki doğa tasvirleri çok canlı, gözlerimin önünde canlandı her şey.',
            'Yazarın kelime seçimi çok başarılı, her sözcük yerli yerinde kullanılmış.',
            'Okurken zaman kavramını yitirdim, o kadar sürükleyici bir anlatımı var.',
            'Eserin ritmi ve akışı mükemmel, şiirsel bir düzyazı gibi.',
            'Bu eser bana çocukluğumu hatırlattı, nostaljik ve sıcak bir anlatım.',
            'Toplumsal mesajları incelikle işlenmiş, didaktik olmadan düşündürüyor.',
            'Çok özgün bir bakış açısı, daha önce böyle bir anlatım okumamıştım.',
            'Eserin sonunda gözyaşlarımı tutamadım, çok duygusal ve güçlü.',
            'Yazarın dil hakimiyeti takdire şayan, her cümle özenle kurulmuş.',
            'Bu eseri arkadaşlarıma da tavsiye ettim, herkesin okuması gereken bir yapıt.',
            'Modern Türk edebiyatının parlayan yıldızlarından biri bu yazar.',
            'Eserdeki karakter gelişimi çok başarılı, gerçekçi ve inandırıcı.',
            'Okumaya değer bir eser, kaliteli vakit geçirmek isteyenlere tavsiye ederim.',
            'Yazarın hayal gücü sınır tanımıyor, muhteşem bir kurgu.',
            'Bu eser beni farklı dünyalara götürdü, kaçış edebiyatının güzel bir örneği.',
            'Sıcak ve samimi bir anlatım, yazarla sohbet eder gibi okuyorsunuz.',
            'Edebiyat dünyamıza renk katan güzel bir eser, devamını bekliyoruz.',
            'Çok etkileyici bir başlangıç ve sürpriz bir final. Harika kurgulanmış.',
            'Bu eserdeki felsefi derinlik beni çok etkiledi, düşündürücü.',
            'Yazarın üslubu kendine has ve özgün, kolayca tanınabilir.',
            'Okurken hem güldüm hem ağladım, duygusal bir roller coaster.',
            'Eser çok iyi kurgulanmış, her detay bir bütünün parçası gibi.',
            'Bu tür samimi ve dürüst eserlere her zaman ihtiyacımız var.',
            'Yazarın gözlem yeteneği muhteşem, hayatın küçük detaylarını yakalamış.',
            'Kendi hayatımdan izler buldum bu eserde, evrensel temalar işlenmiş.',
            'Dil bilinci yüksek bir yazar, Türkçeyi çok güzel kullanıyor.',
            'Bu eser bana ilham verdi, ben de yazmak istiyorum artık.',
            'Akıcı, sürükleyici ve düşündürücü. Üç kelimeyle özetlemek gerekirse.',
            'Her sayfada ayrı bir sürpriz var, merak uyandıran bir anlatım.',
            'Yazarın cesaretine hayranım, zor konuları ustalıkla işlemiş.',
        ];

        $now = now();
        $adminUser = User::whereHas('role', fn ($q) => $q->whereIn('slug', ['admin', 'super-admin']))->first();
        $adminId = $adminUser?->id;

        $comments = [];
        $batchCount = 0;

        foreach ($users as $user) {
            $randomWorkIds = collect($publishedWorkIds)->shuffle()->take(5)->values();
            $nameParts = explode(' ', $user->name, 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';

            foreach ($randomWorkIds as $workId) {
                $template = $commentTemplates[array_rand($commentTemplates)];
                $createdAt = $now->copy()->subDays(rand(1, 60))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

                $comments[] = [
                    'commentable_type' => LiteraryWork::class,
                    'commentable_id'   => $workId,
                    'user_id'          => $user->id,
                    'first_name'       => $firstName,
                    'last_name'        => $lastName,
                    'email'            => null,
                    'body'             => $template,
                    'rating'           => rand(3, 5),
                    'is_approved'      => true,
                    'approved_at'      => $createdAt->copy()->addHours(rand(1, 12))->format('Y-m-d H:i:s'),
                    'approved_by'      => $adminId,
                    'ip_address'       => rand(1, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 254),
                    'created_at'       => $createdAt->format('Y-m-d H:i:s'),
                    'updated_at'       => $createdAt->format('Y-m-d H:i:s'),
                ];

                $batchCount++;

                if ($batchCount >= 50) {
                    DB::table('comments')->insert($comments);
                    $comments = [];
                    $batchCount = 0;
                }
            }
        }

        if (!empty($comments)) {
            DB::table('comments')->insert($comments);
        }

        $totalComments = $users->count() * 5;
        $this->command->info("{$totalComments} demo yorum başarıyla oluşturuldu ({$users->count()} kullanıcı × 5 yorum).");
    }
}
