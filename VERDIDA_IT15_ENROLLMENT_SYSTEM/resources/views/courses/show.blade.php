@extends('layouts.app')

@section('title', $course->course_name . ' - Details')

@section('content')
<div class="mb-6">
    <a href="{{ route('courses.index') }}" class="text-red-800 hover:text-red-900 flex items-center">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Courses
    </a>
</div>

<div class="bg-white rounded-lg shadow-lg p-8 mb-8">
    <div class="flex items-start justify-between mb-6">
        <div class="flex-1">
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-bold text-gray-800">{{ $course->course_name }}</h1>
                @if($course->isFull())
                    <span class="bg-red-100 text-red-800 text-sm font-semibold px-3 py-1 rounded-full">
                        FULL CAPACITY
                    </span>
                @else
                    <span class="bg-green-100 text-green-800 text-sm font-semibold px-3 py-1 rounded-full">
                        {{ $course->available_slots }} SLOTS AVAILABLE
                    </span>
                @endif
            </div>
            <p class="text-gray-600 mt-2">Course Code: <span class="font-semibold text-lg">{{ $course->course_code }}</span></p>
        </div>
    </div>

    <div class="border-t pt-6">
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-red-50 rounded-lg p-4">
                <p class="text-2xl font-bold text-red-800">{{ $course->students->count() }}</p>
                <p class="text-gray-600 text-sm">Enrolled Students</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-2xl font-bold text-blue-600">{{ $course->capacity }}</p>
                <p class="text-gray-600 text-sm">Total Capacity</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <p class="text-2xl font-bold text-green-600">{{ $course->available_slots }}</p>
                <p class="text-gray-600 text-sm">Available Slots</p>
            </div>
        </div>

        <div class="mt-6">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-medium text-gray-700">Enrollment Progress</span>
                <span class="text-sm font-medium text-gray-700">
                    {{ $course->capacity > 0 ? round(($course->students->count() / $course->capacity) * 100, 1) : 0 }}%
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-red-800 h-3 rounded-full transition-all" 
                     style="width: {{ $course->capacity > 0 ? ($course->students->count() / $course->capacity * 100) : 0 }}%">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Enrolled Students</h2>
    
    @if($course->students->isEmpty())
        <p class="text-gray-500 text-center py-8">No students enrolled in this course yet.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Student Number
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Enrolled Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($course->students as $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $student->student_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $student->full_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $student->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $student->pivot->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-3">
                                <a href="{{ route('students.show', $student) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 font-medium">
                                    View Profile
                                </a>
                                <form action="{{ route('courses.removeStudent', [$course, $student]) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('Remove {{ $student->full_name }} from this course?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900 font-medium">
                                        Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="bg-white rounded-lg shadow-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Enroll a New Student</h2>
    
    @if($course->isFull())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                <strong>Course is at full capacity.</strong> Cannot enroll more students until slots become available.
            </p>
        </div>
    @elseif($availableStudents->isEmpty())
        <p class="text-gray-500 text-center py-8">No available students to enroll.</p>
    @else
        <form action="{{ route('courses.enrollStudent', $course) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Select a Student
                </label>
                <select name="student_id" 
                        id="student_id" 
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">-- Choose a student --</option>
                    @foreach($availableStudents as $student)
                        <option value="{{ $student->id }}">
                            {{ $student->student_number }} - {{ $student->full_name }} ({{ $student->email }})
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" 
                    class="bg-red-800 text-white px-6 py-2 rounded-lg hover:bg-red-900 transition">
                Enroll Student
            </button>
        </form>
    @endif
</div>
@endsection