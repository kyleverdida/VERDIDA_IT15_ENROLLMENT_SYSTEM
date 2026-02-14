@extends('layouts.app')

@section('title', $student->full_name . ' - Profile')

@section('content')
<div class="mb-6">
    <a href="{{ route('students.index') }}" class="text-red-800 hover:text-red-900 flex items-center">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Students
    </a>
</div>

<div class="bg-white rounded-lg shadow-lg p-8 mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ $student->full_name }}</h1>
            <p class="text-gray-600 mt-1">Student Number: <span class="font-semibold">{{ $student->student_number }}</span></p>
        </div>
        <div class="text-right">
            <p class="text-gray-600">Email</p>
            <p class="font-semibold text-gray-800">{{ $student->email }}</p>
        </div>
    </div>

    <div class="border-t pt-6">
        <div class="grid grid-cols-3 gap-4 text-center">
            <div class="bg-red-50 rounded-lg p-4">
                <p class="text-2xl font-bold text-red-800">{{ $student->courses->count() }}</p>
                <p class="text-gray-600 text-sm">Enrolled Courses</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <p class="text-2xl font-bold text-green-600">{{ $availableCourses->count() }}</p>
                <p class="text-gray-600 text-sm">Available Courses</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-2xl font-bold text-gray-600">{{ $student->created_at->format('M d, Y') }}</p>
                <p class="text-gray-600 text-sm">Registered Date</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Enrolled Courses</h2>
    
    @if($student->courses->isEmpty())
        <p class="text-gray-500 text-center py-8">Not enrolled in any courses yet.</p>
    @else
        <div class="grid gap-4">
            @foreach($student->courses as $course)
                <div class="border rounded-lg p-4 hover:shadow-md transition flex justify-between items-center">
                    <div>
                        <h3 class="font-semibold text-lg text-gray-800">{{ $course->course_name }}</h3>
                        <p class="text-gray-600">{{ $course->course_code }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            Enrolled: {{ $course->students->count() }} / {{ $course->capacity }} students
                        </p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('courses.show', $course) }}" 
                           class="text-red-800 hover:text-red-900 font-medium">
                            View Course
                        </a>
                        <form action="{{ route('students.unenroll', [$student, $course]) }}" 
                              method="POST" 
                              onsubmit="return confirm('Are you sure you want to unenroll from this course?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                                Unenroll
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<div class="bg-white rounded-lg shadow-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Enroll in a New Course</h2>
    
    @if($availableCourses->isEmpty())
        <p class="text-gray-500 text-center py-8">No available courses to enroll in.</p>
    @else
        <form action="{{ route('students.enroll', $student) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Select a Course
                </label>
                <select name="course_id" 
                        id="course_id" 
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">-- Choose a course --</option>
                    @foreach($availableCourses as $course)
                        <option value="{{ $course->id }}" 
                                @if($course->isFull()) disabled @endif>
                            {{ $course->course_code }} - {{ $course->course_name }}
                            ({{ $course->students->count() }}/{{ $course->capacity }} enrolled)
                            @if($course->isFull()) - FULL @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" 
                    class="bg-red-800 text-white px-6 py-2 rounded-lg hover:bg-red-900 transition">
                Enroll in Course
            </button>
        </form>
    @endif
</div>
@endsection