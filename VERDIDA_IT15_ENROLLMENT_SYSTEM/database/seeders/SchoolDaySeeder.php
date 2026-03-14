<?php

namespace Database\Seeders;

use App\Models\SchoolDay;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SchoolDaySeeder extends Seeder
{
    public function run(): void
    {
        SchoolDay::query()->delete();

        $this->seedSchoolYear(
            Carbon::create(2025, 6, 1),
            Carbon::create(2026, 3, 31),
            [
                '2025-06-12' => 'Independence Day',
                '2025-08-25' => 'National Heroes Day',
                '2025-12-25' => 'Christmas Day',
                '2026-01-01' => 'New Year\'s Day',
            ],
            [
                '2025-07-16' => 'Student Orientation',
                '2025-09-11' => 'Midterm Examinations',
                '2025-10-23' => 'Foundation Day',
                '2025-12-11' => 'Final Examinations',
                '2026-03-20' => 'Recognition Day',
            ]
        );

        $this->seedSchoolYear(
            Carbon::create(2026, 6, 1),
            Carbon::create(2027, 3, 31),
            [
                '2026-06-12' => 'Independence Day',
                '2026-08-31' => 'National Heroes Day',
                '2026-11-30' => 'Bonifacio Day',
                '2026-12-25' => 'Christmas Day',
                '2027-01-01' => 'New Year\'s Day',
            ],
            [
                '2026-07-15' => 'Student Orientation',
                '2026-09-10' => 'Midterm Examinations',
                '2026-10-22' => 'Foundation Day',
                '2026-12-10' => 'Final Examinations',
                '2027-03-20' => 'Recognition Day',
            ]
        );
    }

    private function seedSchoolYear(Carbon $start, Carbon $end, array $holidays, array $events): void
    {
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if ($date->isWeekend()) {
                continue;
            }

            $dateString = $date->toDateString();

            if (isset($holidays[$dateString])) {
                SchoolDay::create([
                    'date' => $dateString,
                    'day_type' => 'holiday',
                    'title' => $holidays[$dateString],
                    'description' => 'No classes and attendance.',
                    'is_attendance_required' => false,
                ]);
                continue;
            }

            if (isset($events[$dateString])) {
                SchoolDay::create([
                    'date' => $dateString,
                    'day_type' => 'event',
                    'title' => $events[$dateString],
                    'description' => 'Special academic event day.',
                    'is_attendance_required' => true,
                ]);
                continue;
            }

            SchoolDay::create([
                'date' => $dateString,
                'day_type' => 'regular',
                'title' => 'Regular Class Day',
                'description' => 'Regular academic day.',
                'is_attendance_required' => true,
            ]);
        }
    }
}
