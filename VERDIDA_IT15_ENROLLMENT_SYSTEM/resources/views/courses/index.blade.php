@extends('layouts.app')

@section('title', 'All Courses')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">All Courses</h1>
    <a href="{{ route('courses.create') }}" 
       class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
        + Add New Course
    </a>
</div>

@if($courses->isEmpty())
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <p class="text-gray-500 text-lg">No courses found. Create your first course!</p>
    </div>
@else
    <div class="grid gap-6">
        @foreach($courses as $course)
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3">
                            <h2 class="text-2xl font-bold text-gray-800">{{ $course->course_name }}</h2>
                            @if($course->isFull())
                                <span class="bg-red-100 text-red-800 text-xs font-semibold px-3 py-1 rounded-full">
                                    FULL
                                </span>
                            @else
                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                                    AVAILABLE
                                </span>
                            @endif
                        </div>
                        <p class="text-gray-600 mt-1">Course Code: <span class="font-semibold">{{ $course->course_code }}</span></p>
                        
                        <div class="mt-4 flex items-center space-x-6">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <span class="text-gray-700">
                                    <span class="font-semibold">{{ $course->students_count }}</span> / {{ $course->capacity }} enrolled
                                </span>
                            </div>
                            
                            <div class="flex-1 bg-gray-200 rounded-full h-2.5">
                                <div class="bg-indigo-600 h-2.5 rounded-full" 
                                     style="width: {{ $course->capacity > 0 ? ($course->students_count / $course->capacity * 100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('courses.show', $course) }}" 
                       class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition font-semibold">
                        View Details
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $courses->links() }}
    </div>
@endif
@endsection