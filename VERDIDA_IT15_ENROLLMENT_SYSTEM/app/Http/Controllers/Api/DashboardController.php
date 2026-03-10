<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\SchoolDay;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $totalPrograms = Course::count();
        $totalStudents = Student::count();
        $totalEnrollments = DB::table('enrollments')->count();

        $totalSubjects = Course::query()
            ->get(['course_code'])
            ->sum(function (Course $course): int {
                return collect(Course::curriculumForProgram((string) $course->course_code))
                    ->flatten(1)
                    ->count();
            });

        // Monthly enrollment trend (last 6 months)
        $monthlyEnrollment = DB::table('enrollments')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as enrollments')
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->groupByRaw("YEAR(created_at), MONTH(created_at)")
            ->orderByRaw("YEAR(created_at), MONTH(created_at)")
            ->get()
            ->map(fn ($row) => [
                'name' => Carbon::createFromDate((int) $row->year, (int) $row->month, 1)->format('M Y'),
                'enrollments' => (int) $row->enrollments,
            ])
            ->values();

        // Course distribution by student enrollment count per program
        $courseDistribution = Course::query()
            ->leftJoin('enrollments', 'courses.id', '=', 'enrollments.course_id')
            ->selectRaw('courses.course_code as code, courses.course_name, COUNT(enrollments.id) as value')
            ->groupBy('courses.id', 'courses.course_code', 'courses.course_name')
            ->orderByDesc('value')
            ->get()
            ->map(fn ($row) => [
                'name'  => $row->course_name,
                'code'  => $row->code,
                'value' => (int) $row->value,
            ])
            ->values();

        // Attendance pattern from school days (regular school days per month)
        $attendancePattern = SchoolDay::selectRaw('YEAR(date) as year, MONTH(date) as month, COUNT(*) as attendance')
            ->where('day_type', 'regular')
            ->where('date', '>=', now()->subMonths(6)->startOfMonth())
            ->groupByRaw("YEAR(date), MONTH(date)")
            ->orderByRaw("YEAR(date), MONTH(date)")
            ->get()
            ->map(fn ($row) => [
                'day' => Carbon::createFromDate((int) $row->year, (int) $row->month, 1)->format('M Y'),
                'attendance' => (int) $row->attendance,
            ])
            ->values();

        return response()->json([
            // Overview stats (field names matched to frontend)
            'total_programs'              => $totalPrograms,
            'total_subjects'              => $totalSubjects,
            'active_programs'             => $totalPrograms,
            'inactive_programs'           => 0,
            'subjects_with_prerequisites' => 0,
            'students_total'              => $totalStudents,
            'enrollments_total'           => $totalEnrollments,

            // Chart data arrays
            'monthly_enrollment'   => $monthlyEnrollment,
            'course_distribution'  => $courseDistribution,
            'attendance_pattern'   => $attendancePattern,
        ]);
    }
}
