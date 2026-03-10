<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $search = trim((string) $request->query('search', ''));

        $courses = Course::query()
            ->withCount('students')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('course_code', 'like', "%{$search}%")
                    ->orWhere('course_name', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            })
            ->orderBy('course_code')
            ->paginate(max(1, min($perPage, 100)));

        $courses->getCollection()->transform(function (Course $course): Course {
            return $course->append('year_levels');
        });

        return response()->json($courses);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_code' => ['required', 'string', 'max:20', 'unique:courses,course_code'],
            'course_name' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'units' => ['nullable', 'integer', 'min:1', 'max:10'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
        ]);

        $course = Course::create($this->sanitizePayload($validated));

        return response()->json($course, 201);
    }

    public function show(Course $course): JsonResponse
    {
        return response()->json($course->load('students')->append('year_levels'));
    }

    public function update(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'course_code' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('courses', 'course_code')->ignore($course->id)],
            'course_name' => ['sometimes', 'required', 'string', 'max:255'],
            'department' => ['sometimes', 'required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'units' => ['nullable', 'integer', 'min:1', 'max:10'],
            'capacity' => ['sometimes', 'required', 'integer', 'min:1', 'max:500'],
        ]);

        $course->update($this->sanitizePayload($validated));

        return response()->json($course->fresh()->append('year_levels'));
    }

    public function destroy(Course $course): JsonResponse
    {
        $course->delete();

        return response()->json(['message' => 'Course deleted successfully.']);
    }

    private function sanitizePayload(array $payload): array
    {
        foreach (['course_code', 'course_name', 'department', 'description'] as $field) {
            if (array_key_exists($field, $payload) && is_string($payload[$field])) {
                $payload[$field] = trim(strip_tags($payload[$field]));
            }
        }

        return $payload;
    }
}
