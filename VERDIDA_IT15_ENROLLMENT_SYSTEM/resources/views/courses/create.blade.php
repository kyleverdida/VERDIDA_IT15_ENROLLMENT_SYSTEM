@extends('layouts.app')

@section('title', 'Create New Course')

@section('content')
<div class="mb-6">
    <a href="{{ route('courses.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Courses
    </a>
</div>

<div class="bg-white rounded-lg shadow-lg p-8 max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Create New Course</h1>

    <form action="{{ route('courses.store') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label for="course_code" class="block text-sm font-medium text-gray-700 mb-2">
                Course Code <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   name="course_code" 
                   id="course_code" 
                   value="{{ old('course_code') }}"
                   placeholder="e.g., CS101"
                   required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('course_code') border-red-500 @enderror">
            @error('course_code')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="course_name" class="block text-sm font-medium text-gray-700 mb-2">
                Course Name <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   name="course_name" 
                   id="course_name" 
                   value="{{ old('course_name') }}"
                   placeholder="e.g., Introduction to Computer Science"
                   required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('course_name') border-red-500 @enderror">
            @error('course_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">
                Course Capacity <span class="text-red-500">*</span>
            </label>
            <input type="number" 
                   name="capacity" 
                   id="capacity" 
                   value="{{ old('capacity', 30) }}"
                   min="1"
                   max="500"
                   required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('capacity') border-red-500 @enderror">
            <p class="text-gray-500 text-sm mt-1">Maximum number of students (1-500)</p>
            @error('capacity')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex space-x-4 pt-4">
            <button type="submit" 
                    class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold">
                Create Course
            </button>
            <a href="{{ route('courses.index') }}" 
               class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold text-center">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection