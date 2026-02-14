@extends('layouts.app')

@section('title', 'Create New Student')

@section('content')
<div class="mb-6">
    <a href="{{ route('students.index') }}" class="text-red-800 hover:text-indigo-800 flex items-center">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Students
    </a>
</div>

<div class="bg-white rounded-lg shadow-lg p-8 max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Create New Student</h1>

    <form action="{{ route('students.store') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label for="student_number" class="block text-sm font-medium text-gray-700 mb-2">
                Student Number <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   name="student_number" 
                   id="student_number" 
                   value="{{ old('student_number') }}"
                   required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('student_number') border-red-500 @enderror">
            @error('student_number')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                    First Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="first_name" 
                       id="first_name" 
                       value="{{ old('first_name') }}"
                       required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('first_name') border-red-500 @enderror">
                @error('first_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Last Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="last_name" 
                       id="last_name" 
                       value="{{ old('last_name') }}"
                       required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('last_name') border-red-500 @enderror">
                @error('last_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Email Address <span class="text-red-500">*</span>
            </label>
            <input type="email" 
                   name="email" 
                   id="email" 
                   value="{{ old('email') }}"
                   required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-red-500 @enderror">
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex space-x-4 pt-4">
            <button type="submit" 
                    class="flex-1 bg-red-800 text-white px-6 py-3 rounded-lg hover:bg-red-900 transition font-semibold">
                Create Student
            </button>
            <a href="{{ route('students.index') }}" 
               class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold text-center">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection