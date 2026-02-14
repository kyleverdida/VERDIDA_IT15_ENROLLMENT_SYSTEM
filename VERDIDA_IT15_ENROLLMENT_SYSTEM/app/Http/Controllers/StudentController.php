<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class StudentController extends Controller
{
    public function index(): View
    {
        $students = Student::orderBy('last_name')
                          ->orderBy('first_name')
                          ->paginate(15);

        return view('students.index', compact('students'));
    }

    public function show(Student $student): View
    {
        $student->load('courses');
        
        $availableCourses = Course::whereDoesntHave('students', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })->get();

        return view('students.show', compact('student', 'availableCourses'));
    }

    public function create(): View
    {
        return view('students.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_number' => 'required|string|unique:students,student_number|max:50',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:students,email|max:255',
        ]);

        $student = Student::create($validated);

        return redirect()->route('students.show', $student)
                        ->with('success', 'Student created successfully!');
    }

    public function enroll(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::findOrFail($validated['course_id']);

        if ($student->isEnrolledIn($course->id)) {
            return redirect()->back()
                           ->with('error', 'Student is already enrolled in this course!');
        }

        if ($course->isFull()) {
            return redirect()->back()
                           ->with('error', 'Course is already at full capacity!');
        }

        $student->courses()->attach($course->id);

        return redirect()->back()
                        ->with('success', "Successfully enrolled in {$course->course_name}!");
    }

    public function unenroll(Student $student, Course $course): RedirectResponse
    {
        if (!$student->isEnrolledIn($course->id)) {
            return redirect()->back()
                           ->with('error', 'Student is not enrolled in this course!');
        }

        $student->courses()->detach($course->id);

        return redirect()->back()
                        ->with('success', "Successfully unenrolled from {$course->course_name}!");
    }
}