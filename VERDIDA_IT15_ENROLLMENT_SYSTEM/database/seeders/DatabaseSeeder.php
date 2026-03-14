<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            StudentsSeeder::class,
            CourseSeeder::class,
            SchoolDaySeeder::class,
        ]);

        DB::table('enrollments')->truncate();

        $studentIds = DB::table('students')->pluck('id')->all();
        $courseIds  = DB::table('courses')->pluck('id')->all();
        $courseCount = count($courseIds);

        // Assign every student to exactly ONE randomly chosen program.
        $enrollments = [];
        foreach ($studentIds as $index => $studentId) {
            // Cycle through courses evenly first, then add uniform random on top
            // to guarantee full coverage while keeping natural variance.
            $courseId = $courseIds[($index + random_int(0, $courseCount - 1)) % $courseCount];
            $enrollments[] = [
                'student_id' => $studentId,
                'course_id'  => $courseId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($enrollments, 1000) as $chunk) {
            DB::table('enrollments')->insert($chunk);
        }

        Admin::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password123'),
            ]
        );

        $courseCount = DB::table('courses')->count();
        $this->command->info("Seeded: 500 students, {$courseCount} courses, school days, enrollments, and default admin account.");
    }
}