<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CourseController extends Controller
{
    public function index(): View
    {
        $courses = Course::withCount('students')
                        ->orderBy('course_code')
                        ->paginate(15);

        return view('courses.index', compact('courses'));
    }

    public function show(Course $course): View
    {
        $course->load('students');
        
        $availableStudents = Student::whereDoesntHave('courses', function ($query) use ($course) {
            $query->where('course_id', $course->id);
        })->get();

        return view('courses.show', compact('course', 'availableStudents'));
    }

    public function create(): View
    {
        return view('courses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_code' => 'required|string|unique:courses,course_code|max:20',
            'course_name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:500',
        ]);

        $course = Course::create($validated);

        return redirect()->route('courses.show', $course)
                        ->with('success', 'Course created successfully!');
    }

    public function enrollStudent(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $student = Student::findOrFail($validated['student_id']);

        if ($student->isEnrolledIn($course->id)) {
            return redirect()->back()
                           ->with('error', 'Student is already enrolled in this course!');
        }

        if ($course->isFull()) {
            return redirect()->back()
                           ->with('error', 'Course is already at full capacity!');
        }

        $course->students()->attach($student->id);

        return redirect()->back()
                        ->with('success', "{$student->full_name} has been enrolled successfully!");
    }

    public function removeStudent(Course $course, Student $student): RedirectResponse
    {
        if (!$student->isEnrolledIn($course->id)) {
            return redirect()->back()
                           ->with('error', 'Student is not enrolled in this course!');
        }

        $course->students()->detach($student->id);

        return redirect()->back()
                        ->with('success', "{$student->full_name} has been removed from the course!");
    }
}