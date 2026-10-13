<?php

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Enums\EmploymentType;
use App\Enums\Experience;
use App\Models\Category;
use App\Models\Company;
use App\Models\Resume;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    private const CATEGORIES = [
        ['IT va dasturlash', '💻', ['Backend dasturchi (PHP/Laravel)', 'Frontend dasturchi (Vue.js)', 'Mobil dasturchi (Flutter)', 'QA muhandis', 'DevOps muhandis']],
        ['Savdo', '🛒', ['Savdo menejeri', 'Kassir', 'Sotuvchi-konsultant', 'Hududiy savdo vakili']],
        ['Marketing va reklama', '📣', ['SMM mutaxassisi', 'Marketolog', 'Kontent menejer', 'Grafik dizayner']],
        ["Ta'lim", '🎓', ["Ingliz tili o'qituvchisi", "Matematika o'qituvchisi", 'IT kurs mentori']],
        ['Tibbiyot', '🩺', ['Hamshira', 'Stomatolog', 'Farmatsevt']],
        ['Moliya va buxgalteriya', '💰', ['Buxgalter', 'Moliyaviy tahlilchi', 'Bank operatori']],
        ['Qurilish', '🏗️', ['Muhandis-quruvchi', 'Elektrik', 'Payvandchi']],
        ['Logistika va transport', '🚚', ['Haydovchi (B toifa)', 'Logist', 'Omborchi']],
        ['Restoran va xizmat', '🍽️', ['Oshpaz', 'Ofitsiant', 'Barista']],
        ['Boshqaruv va HR', '🧭', ['HR menejer', 'Ofis menejeri', 'Loyiha menejeri']],
    ];

    private const COMPANIES = [
        ['Uzum Technologies', 'IT', 'Toshkent', 'O\'zbekistondagi yetakchi e-commerce va fintech ekotizimi.'],
        ['Artel Electronics', 'Ishlab chiqarish', 'Toshkent', 'Maishiy texnika ishlab chiqaruvchi yirik kompaniya.'],
        ['Najot Ta\'lim', 'Ta\'lim', 'Toshkent', 'Zamonaviy IT kasblarini o\'rgatuvchi o\'quv markazi.'],
        ['Korzinka', 'Savdo', 'Toshkent', 'Supermarketlar tarmog\'i.'],
        ['Samarqand Darvoza', 'Savdo', 'Samarqand', 'Savdo va ko\'ngilochar markaz.'],
        ['Buxoro Med Servis', 'Tibbiyot', 'Buxoro', 'Xususiy ko\'p tarmoqli klinika.'],
        ['Farg\'ona Qurilish Invest', 'Qurilish', "Farg'ona", 'Turar-joy va tijorat binolari qurilishi.'],
        ['Express Logistic', 'Logistika', 'Andijon', 'Yuk tashish va omborxona xizmatlari.'],
        ['Oqtepa Lavash', 'Restoran', 'Toshkent', 'Fast-food restoranlar tarmog\'i.'],
        ['Hamkor Moliya', 'Moliya', 'Namangan', 'Mikromoliya va lizing xizmatlari.'],
    ];

    public function run(): void
    {
        $categories = collect(self::CATEGORIES)->map(fn (array $c) => Category::create([
            'name' => $c[0], 'slug' => Str::slug($c[0]), 'icon' => $c[1],
        ]));

        $seeker = User::factory()->create([
            'name' => 'Ahliddin Sultonaliyev', 'phone' => '+998901111111', 'email' => 'seeker@ishkerakmi.uz', 'city' => 'Toshkent',
        ]);

        $employer = User::factory()->employer()->create([
            'name' => 'Aziz Karimov', 'phone' => '+998902222222', 'email' => 'hr@ishkerakmi.uz', 'city' => 'Toshkent',
        ]);

        $companies = collect(self::COMPANIES)->map(function (array $c, int $i) use ($employer) {
            $data = ['name' => $c[0], 'industry' => $c[1], 'city' => $c[2], 'description' => $c[3], 'employees_count' => fake()->numberBetween(20, 2000)];

            return $i === 0
                ? Company::factory()->for($employer)->create([...$data, 'is_verified' => true])
                : Company::factory()->create([...$data, 'is_verified' => $i % 2 === 0]);
        });

        $vacancies = collect();
        foreach (self::CATEGORIES as $index => [, , $titles]) {
            foreach ($titles as $title) {
                $vacancies->push(Vacancy::factory()->create([
                    'company_id' => $companies[($index + count($vacancies)) % $companies->count()]->id,
                    'category_id' => $categories[$index]->id,
                    'title' => $title,
                    'description' => "Biz jamoamizga {$title} lavozimiga mas'uliyatli va g'ayratli xodim izlayapmiz. "
                        ."Siz zamonaviy ish sharoiti, rivojlanish imkoniyatlari va do'stona jamoada ishlaysiz.",
                    'requirements' => "- Sohada bilim va ko'nikmalar\n- Mas'uliyat va intizom\n- Jamoada ishlash qobiliyati",
                    'benefits' => "- Rasmiy ishga joylashish\n- O'z vaqtida maosh\n- Bepul tushlik va malaka oshirish",
                    'city' => fake()->randomElement(['Toshkent', 'Toshkent', 'Samarqand', 'Buxoro', "Farg'ona", 'Andijon', 'Namangan']),
                    'created_at' => now()->subHours(fake()->numberBetween(1, 24 * 20)),
                ]));
            }
        }

        $seekers = User::factory(12)->create()->push($seeker);
        $seekers->each(fn (User $user) => Resume::factory()->for($user)->create([
            'full_name' => $user->name,
            'phone' => $user->phone,
            'city' => $user->city,
            'category_id' => $categories->random()->id,
            'title' => Arr::random(Arr::random(self::CATEGORIES)[2]),
        ]));

        $seeker->resumes()->first()->update([
            'title' => 'Backend dasturchi (PHP/Laravel)',
            'category_id' => $categories[0]->id,
            'employment_type' => EmploymentType::FullTime,
            'experience' => Experience::OneToThree,
            'skills' => ['PHP', 'Laravel', 'Vue.js', 'SQL', 'Git'],
            'about' => 'Laravel va Vue.js bilan 2 yillik tajribaga ega dasturchiman. Toza kod va testlarga katta e\'tibor beraman.',
        ]);

        $employerVacancies = $vacancies->where('company_id', $companies[0]->id);
        $seekers->take(8)->each(function (User $user) use ($employerVacancies, $vacancies) {
            $vacancy = $employerVacancies->isNotEmpty() ? $employerVacancies->random() : $vacancies->random();
            $vacancy->applications()->firstOrCreate(['user_id' => $user->id], [
                'resume_id' => $user->resumes()->first()->id,
                'cover_letter' => 'Assalomu alaykum! Ushbu vakansiyaga qiziqaman va jamoangizga foyda keltira olaman.',
                'status' => Arr::random(ApplicationStatus::cases()),
            ]);
        });

        $vacancies->random(15)->each(function (Vacancy $vacancy) use ($seekers) {
            $vacancy->likes()->create(['user_id' => $seekers->random()->id]);
            $vacancy->comments()->create([
                'user_id' => $seekers->random()->id,
                'body' => Arr::random(['Ish vaqti qanday?', 'Masofaviy ishlash imkoni bormi?', 'Ajoyib vakansiya, ariza topshirdim!', 'Tajribasizlarni ham olasizlarmi?']),
            ]);
        });

    }
}
