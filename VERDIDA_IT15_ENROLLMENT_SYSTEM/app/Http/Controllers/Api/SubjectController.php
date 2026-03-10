<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\JsonResponse;

class SubjectController extends Controller
{
    public function index(): JsonResponse
    {
        $subjects = [];

        $programs = Course::query()
            ->select(['course_code', 'course_name'])
            ->orderBy('course_code')
            ->get();

        foreach ($programs as $program) {
            $yearLevels = Course::curriculumForProgram((string) $program->course_code);

            foreach ($yearLevels as $year => $items) {
                foreach ($items as $item) {
                    $subjects[] = [
                        'id' => (int) ($item['id'] ?? 0),
                        'code' => (string) ($item['code'] ?? ''),
                        'title' => (string) ($item['title'] ?? ''),
                        'units' => (int) ($item['units'] ?? 3),
                        'semester_offer' => 'semester',
                        'term_offer' => (string) ($item['semester'] ?? '1st') . ' Semester',
                        'program_code' => (string) $program->course_code,
                        'program' => (string) $program->course_name,
                        'description' => (string) ($item['title'] ?? '') . ' under ' . (string) $program->course_name . ' (' . $year . ').',
                        'prerequisites' => array_values($item['prerequisites'] ?? []),
                        'co_requisites' => [],
                    ];
                }
            }
        }

        return response()->json([
            'data' => $subjects,
            'meta' => [
                'total' => count($subjects),
            ],
        ]);
    }
}
