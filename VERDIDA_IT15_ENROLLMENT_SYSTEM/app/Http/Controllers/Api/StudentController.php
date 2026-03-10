<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $search = trim((string) $request->query('search', ''));

        $students = Student::query()
            ->with('courses:id,course_code,course_name')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('student_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(max(1, min($perPage, 100)));

        // Transform each student to include their first enrolled course
        $items = $students->items();
        foreach ($items as $student) {
            $student->student_id = $student->student_number;
            $enrolledCourse = $student->courses->first();
            $student->course = $enrolledCourse ? [
                'code' => $enrolledCourse->course_code,
                'name' => $enrolledCourse->course_name,
            ] : null;
        }

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $students->currentPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
                'last_page' => $students->lastPage(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_number' => ['required', 'string', 'max:50', 'unique:students,student_number'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other', 'prefer_not_to_say'])],
            'date_of_birth' => ['nullable', 'date'],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'email' => ['required', 'email', 'max:255', 'unique:students,email'],
        ]);

        $student = Student::create($this->sanitizePayload($validated));
        $student->load('courses:id,course_code,course_name');

        return response()->json($this->formatStudentForUI($student), 201);
    }

    public function show(Student $student): JsonResponse
    {
        $student->load('courses:id,course_code,course_name');
        return response()->json($this->formatStudentForUI($student));
    }

    public function update(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'student_number' => ['sometimes', 'required', 'string', 'max:50', Rule::unique('students', 'student_number')->ignore($student->id)],
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['sometimes', 'required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other', 'prefer_not_to_say'])],
            'date_of_birth' => ['nullable', 'date'],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('students', 'email')->ignore($student->id)],
        ]);

        $student->update($this->sanitizePayload($validated));
        $student = $student->fresh()->load('courses:id,course_code,course_name');

        return response()->json($this->formatStudentForUI($student));
    }

    public function destroy(Student $student): JsonResponse
    {
        $student->delete();

        return response()->json(['message' => 'Student deleted successfully.']);
    }

    public function enroll(Student $student, Course $course): JsonResponse
    {
        if ($student->isEnrolledIn($course->id)) {
            return response()->json(['message' => 'Student is already enrolled in this course.'], 422);
        }

        if ($course->isFull()) {
            return response()->json(['message' => 'Course is at full capacity.'], 422);
        }

        $student->courses()->attach($course->id);

        return response()->json(['message' => 'Enrollment successful.']);
    }

    public function unenroll(Student $student, Course $course): JsonResponse
    {
        $student->courses()->detach($course->id);

        return response()->json(['message' => 'Unenrolled successfully.']);
    }

    private function sanitizePayload(array $payload): array
    {
        $stringFields = [
            'student_number',
            'first_name',
            'last_name',
            'middle_name',
            'contact_number',
            'address',
            'email',
        ];

        foreach ($stringFields as $field) {
            if (array_key_exists($field, $payload) && is_string($payload[$field])) {
                $payload[$field] = trim(strip_tags($payload[$field]));
            }
        }

        if (isset($payload['email'])) {
            $payload['email'] = strtolower($payload['email']);
        }

        return $payload;
    }

    private function formatStudentForUI(Student $student): array
    {
        $enrolledCourse = $student->courses->first();

        return [
            ...$student->toArray(),
            'student_id' => $student->student_number,
            'course' => $enrolledCourse ? [
                'code' => $enrolledCourse->course_code,
                'name' => $enrolledCourse->course_name,
            ] : null,
        ];
    }
}
