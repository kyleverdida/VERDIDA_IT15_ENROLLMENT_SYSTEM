<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentsSeeder extends Seeder
{
    public function run(): void
    {
        Student::query()->delete();

        $genders = ['male', 'female', 'other', 'prefer_not_to_say'];

        $maleFirstNames = [
            'Jose', 'Juan', 'Mark', 'Carlo', 'Paolo', 'Renz', 'Jomar', 'Christian',
            'Kenneth', 'Bryan', 'Emman', 'Miguel', 'Gabriel', 'Angelo', 'Rico', 'Noel',
            'Raymart', 'Jerome', 'Albert', 'Vincent',
        ];

        $femaleFirstNames = [
            'Maria', 'Angelica', 'Camille', 'Jessa', 'Maricel', 'Joan', 'Lyn', 'Karen',
            'Christine', 'Mikaela', 'Patricia', 'Anne', 'Katrina', 'Aileen', 'Rose', 'Mae',
            'Shiela', 'Lovely', 'Trisha', 'Bianca',
        ];

        $neutralFirstNames = [
            'Alex', 'Sam', 'Charlie', 'Kris', 'Jules', 'Sky', 'Ari', 'Pat', 'Dani', 'Mika',
        ];

        $lastNames = [
            'Dela Cruz', 'Santos', 'Reyes', 'Bautista', 'Garcia', 'Mendoza', 'Torres',
            'Flores', 'Gonzales', 'Ramos', 'Villanueva', 'Aquino', 'Navarro', 'Castro',
            'Fernandez', 'Domingo', 'Mercado', 'Soriano', 'Salazar', 'Rosales',
            'Pascual', 'Manalo', 'Alvarez', 'Padilla', 'Valdez',
        ];

        $mindanaoPlaces = [
            ['city' => 'Davao City', 'province' => 'Davao del Sur', 'region' => 'Davao Region'],
            ['city' => 'Panabo City', 'province' => 'Davao del Norte', 'region' => 'Davao Region'],
            ['city' => 'Tagum City', 'province' => 'Davao del Norte', 'region' => 'Davao Region'],
            ['city' => 'Mati City', 'province' => 'Davao Oriental', 'region' => 'Davao Region'],
            ['city' => 'Digos City', 'province' => 'Davao del Sur', 'region' => 'Davao Region'],
            ['city' => 'Cagayan de Oro City', 'province' => 'Misamis Oriental', 'region' => 'Northern Mindanao'],
            ['city' => 'Iligan City', 'province' => 'Lanao del Norte', 'region' => 'Northern Mindanao'],
            ['city' => 'Valencia City', 'province' => 'Bukidnon', 'region' => 'Northern Mindanao'],
            ['city' => 'Malaybalay City', 'province' => 'Bukidnon', 'region' => 'Northern Mindanao'],
            ['city' => 'Oroquieta City', 'province' => 'Misamis Occidental', 'region' => 'Northern Mindanao'],
            ['city' => 'Butuan City', 'province' => 'Agusan del Norte', 'region' => 'Caraga'],
            ['city' => 'Surigao City', 'province' => 'Surigao del Norte', 'region' => 'Caraga'],
            ['city' => 'Tandag City', 'province' => 'Surigao del Sur', 'region' => 'Caraga'],
            ['city' => 'Bayugan City', 'province' => 'Agusan del Sur', 'region' => 'Caraga'],
            ['city' => 'Bislig City', 'province' => 'Surigao del Sur', 'region' => 'Caraga'],
            ['city' => 'General Santos City', 'province' => 'South Cotabato', 'region' => 'SOCCSKSARGEN'],
            ['city' => 'Koronadal City', 'province' => 'South Cotabato', 'region' => 'SOCCSKSARGEN'],
            ['city' => 'Kidapawan City', 'province' => 'Cotabato', 'region' => 'SOCCSKSARGEN'],
            ['city' => 'Tacurong City', 'province' => 'Sultan Kudarat', 'region' => 'SOCCSKSARGEN'],
            ['city' => 'Cotabato City', 'province' => 'Maguindanao del Norte', 'region' => 'BARMM'],
            ['city' => 'Marawi City', 'province' => 'Lanao del Sur', 'region' => 'BARMM'],
            ['city' => 'Lamitan City', 'province' => 'Basilan', 'region' => 'BARMM'],
            ['city' => 'Isabela City', 'province' => 'Basilan', 'region' => 'BARMM'],
            ['city' => 'Jolo', 'province' => 'Sulu', 'region' => 'BARMM'],
            ['city' => 'Zamboanga City', 'province' => 'Zamboanga del Sur', 'region' => 'Zamboanga Peninsula'],
            ['city' => 'Dipolog City', 'province' => 'Zamboanga del Norte', 'region' => 'Zamboanga Peninsula'],
            ['city' => 'Pagadian City', 'province' => 'Zamboanga del Sur', 'region' => 'Zamboanga Peninsula'],
            ['city' => 'Dapitan City', 'province' => 'Zamboanga del Norte', 'region' => 'Zamboanga Peninsula'],
        ];

        $barangays = [
            'Poblacion', 'San Isidro', 'San Jose', 'Santa Cruz', 'Santa Maria', 'Mabini',
            'Rizal', 'Quezon', 'Bagong Silang', 'Purok 1', 'Purok 2', 'Purok 3',
            'Sampaguita', 'Maligaya', 'Kalinaw', 'Pag-asa', 'Lumbia', 'Buhangin',
            'Mintal', 'Talomo', 'Agdao', 'Matina', 'Tisa', 'Balulang',
        ];

        $middleNames = array_values(array_unique(array_merge(
            $maleFirstNames,
            $femaleFirstNames,
            $neutralFirstNames
        )));

        $buildMindanaoAddress = static function () use ($mindanaoPlaces, $barangays): string {
            $place = fake()->randomElement($mindanaoPlaces);
            $houseNumber = fake()->numberBetween(1, 999);
            $street = fake()->randomElement(['Rizal St', 'Bonifacio St', 'Mabini St', 'Quezon Ave', 'JP Laurel Ave', 'Osmena St']);
            $barangay = fake()->randomElement($barangays);

            return "{$houseNumber} {$street}, Brgy. {$barangay}, {$place['city']}, {$place['province']}, {$place['region']}, Mindanao";
        };

        $buildPhilippineMobile = static function (): string {
            // PH mobile format: 09XXXXXXXXX
            return '09' . fake()->numerify('#########');
        };

        $rows = [];
        for ($i = 1; $i <= 500; $i++) {
            $gender = fake()->randomElement($genders);
            $firstName = match ($gender) {
                'male' => fake()->randomElement($maleFirstNames),
                'female' => fake()->randomElement($femaleFirstNames),
                default => fake()->randomElement($neutralFirstNames),
            };

            $rows[] = [
                'student_number' => '2026-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'first_name' => $firstName,
                'middle_name' => fake()->optional()->randomElement($middleNames),
                'last_name' => fake()->randomElement($lastNames),
                'gender' => $gender,
                'date_of_birth' => fake()->dateTimeBetween('-25 years', '-16 years')->format('Y-m-d'),
                'contact_number' => $buildPhilippineMobile(),
                'address' => $buildMindanaoAddress(),
                'email' => "student{$i}@example.edu",
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            Student::insert($chunk);
        }
    }
}