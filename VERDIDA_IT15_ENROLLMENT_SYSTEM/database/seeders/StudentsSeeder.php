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

        $middleNames = array_values(array_unique(array_merge(
            $maleFirstNames,
            $femaleFirstNames,
            $neutralFirstNames
        )));

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
                'contact_number' => fake()->phoneNumber(),
                'address' => fake()->address(),
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