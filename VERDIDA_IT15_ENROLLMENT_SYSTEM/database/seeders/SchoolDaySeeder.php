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

        $start = Carbon::create(2026, 6, 1);
        $end = Carbon::create(2027, 3, 31);

        $holidays = [
            '2026-06-12' => 'Independence Day',
            '2026-08-31' => 'National Heroes Day',
            '2026-11-30' => 'Bonifacio Day',
            '2026-12-25' => 'Christmas Day',
            '2027-01-01' => 'New Year\'s Day',
        ];

        $events = [
            '2026-07-15' => 'Student Orientation',
            '2026-09-10' => 'Midterm Examinations',
            '2026-10-22' => 'Foundation Day',
            '2026-12-10' => 'Final Examinations',
            '2027-03-20' => 'Recognition Day',
        ];

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
