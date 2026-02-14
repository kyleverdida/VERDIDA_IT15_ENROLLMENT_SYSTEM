<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mini Academic Portal')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-indigo-600 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-8">
                    <a href="/" class="text-2xl font-bold">Mini Academic Portal</a>
                    <div class="flex space-x-4">
                        <a href="{{ route('students.index') }}" 
                           class="hover:bg-indigo-700 px-3 py-2 rounded transition">
                            Students
                        </a>
                        <a href="{{ route('courses.index') }}" 
                           class="hover:bg-indigo-700 px-3 py-2 rounded transition">
                            Courses
                        </a>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('students.create') }}" 
                       class="bg-white text-indigo-600 px-4 py-2 rounded font-semibold hover:bg-gray-100 transition">
                        + Add Student
                    </a>
                    <a href="{{ route('courses.create') }}" 
                       class="bg-white text-indigo-600 px-4 py-2 rounded font-semibold hover:bg-gray-100 transition">
                        + Add Course
                    </a>
                </div>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <strong class="font-bold">Validation Error!</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white mt-12">
        <div class="container mx-auto px-4 py-6 text-center">
            <p>&copy; {{ date('Y') }} Mini Academic Portal. Built with Laravel.</p>
        </div>
    </footer>
</body>
</html>