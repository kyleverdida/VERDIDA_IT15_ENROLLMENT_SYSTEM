<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Course;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample students
        $students = [
            [
                'student_number' => '2024-001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
            ],
            [
                'student_number' => '2024-002',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@example.com',
            ],
            [
                'student_number' => '2024-003',
                'first_name' => 'Michael',
                'last_name' => 'Johnson',
                'email' => 'michael.johnson@example.com',
            ],
            [
                'student_number' => '2024-004',
                'first_name' => 'Emily',
                'last_name' => 'Brown',
                'email' => 'emily.brown@example.com',
            ],
            [
                'student_number' => '2024-005',
                'first_name' => 'David',
                'last_name' => 'Wilson',
                'email' => 'david.wilson@example.com',
            ],
        ];

        foreach ($students as $studentData) {
            Student::create($studentData);
        }

        // Create sample courses
        $courses = [
            [
                'course_code' => 'CS101',
                'course_name' => 'Introduction to Computer Science',
                'capacity' => 30,
            ],
            [
                'course_code' => 'MATH201',
                'course_name' => 'Calculus I',
                'capacity' => 25,
            ],
            [
                'course_code' => 'ENG101',
                'course_name' => 'English Composition',
                'capacity' => 20,
            ],
            [
                'course_code' => 'PHY101',
                'course_name' => 'General Physics',
                'capacity' => 35,
            ],
            [
                'course_code' => 'HIST101',
                'course_name' => 'World History',
                'capacity' => 40,
            ],
        ];

        foreach ($courses as $courseData) {
            Course::create($courseData);
        }

        // Create some sample enrollments
        $student1 = Student::where('student_number', '2024-001')->first();
        $student2 = Student::where('student_number', '2024-002')->first();
        $student3 = Student::where('student_number', '2024-003')->first();

        $cs101 = Course::where('course_code', 'CS101')->first();
        $math201 = Course::where('course_code', 'MATH201')->first();
        $eng101 = Course::where('course_code', 'ENG101')->first();

        // Enroll students in courses
        $student1->courses()->attach([$cs101->id, $math201->id]);
        $student2->courses()->attach([$cs101->id, $eng101->id]);
        $student3->courses()->attach([$math201->id, $eng101->id]);

        $this->command->info('Database seeded successfully!');
    }
}