<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        Course::query()->delete();

        $courseData = [
            ['BSIT', 'Bachelor of Science in Information Technology', 'College of Computing Education', 156, 280, 'Focuses on software, networking, databases, and enterprise information systems.'],
            ['BSCS', 'Bachelor of Science in Computer Science', 'College of Computing Education', 164, 220, 'Emphasizes algorithms, software engineering, data science, and intelligent systems.'],
            ['BSIS', 'Bachelor of Science in Information Systems', 'College of Computing Education', 158, 200, 'Prepares students to bridge business processes and information technology solutions.'],
            ['BSBA-MM', 'Bachelor of Science in Business Administration major in Marketing Management', 'College of Business Administration', 156, 260, 'Develops competencies in consumer behavior, digital marketing, and strategic brand management.'],
            ['BSBA-FM', 'Bachelor of Science in Business Administration major in Financial Management', 'College of Business Administration', 156, 240, 'Covers investments, risk management, banking operations, and corporate finance.'],
            ['BSA', 'Bachelor of Science in Accountancy', 'College of Accountancy Education', 170, 180, 'Comprehensive accounting program covering auditing, taxation, and financial reporting standards.'],
            ['BSCRIM', 'Bachelor of Science in Criminology', 'College of Criminal Justice Education', 162, 260, 'Includes criminal law, forensic sciences, criminological research, and law enforcement administration.'],
            ['BEED', 'Bachelor of Elementary Education', 'College of Teacher Education', 152, 210, 'Professional preparation for elementary teaching with focus on pedagogy and child development.'],
            ['BSED-ENG', 'Bachelor of Secondary Education major in English', 'College of Teacher Education', 152, 190, 'Specialized secondary teacher training focused on English language and literature instruction.'],
            ['BSHM', 'Bachelor of Science in Hospitality Management', 'College of Hospitality Education', 158, 230, 'Covers hotel operations, food and beverage management, tourism, and service excellence.'],
        ];

        foreach ($courseData as [$code, $name, $department, $units, $capacity, $description]) {
            Course::create([
                'course_code' => $code,
                'course_name' => $name,
                'department' => $department,
                'description' => $description,
                'units' => $units,
                'capacity' => $capacity,
            ]);
        }
    }
}
