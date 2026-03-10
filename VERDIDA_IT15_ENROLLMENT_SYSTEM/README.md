# VERDIDA IT15 Enrollment System (Backend)

Laravel backend API for student enrollment, programs, subjects, dashboard analytics, and school-day calendar.

## Tech Stack
- PHP `8.2+` (project currently runs on PHP 8.5.x)
- Laravel `12`
- MySQL/MariaDB
- Laravel Sanctum for token auth

## Backend Setup Instructions

### 1. Go to backend folder
```powershell
cd C:\laragon\www\VERDIDA_IT15_ENROLLMENT_SYSTEM\VERDIDA_IT15_ENROLLMENT_SYSTEM
```

### 2. Install dependencies
```powershell
composer install
```

### 3. Create environment file
```powershell
Copy-Item .env.example .env
```

### 4. Configure `.env`
Set your DB values:

```env
APP_NAME=VERDIDA_Enrollment
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=verdida_enrollment
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Generate app key
```powershell
php artisan key:generate
```

### 6. Run migrations and seeders
```powershell
php artisan migrate:fresh --seed
```

This seeds:
- 500 students
- 10 degree programs
- school-day records
- randomized enrollments
- default admin account

Default admin login:
- Email: `admin@example.com`
- Password: `password123`

### 7. Start backend server
```powershell
php artisan serve
```

API base URL:
- `http://127.0.0.1:8000/api`

## Useful Commands

Clear route/config/cache:

```powershell
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

Run tests:

```powershell
php artisan test
```

## API Quick Start

1. Login:
```http
POST /api/login
```

2. Use token in protected requests:
```http
Authorization: Bearer <token>
Accept: application/json
```

3. Main endpoints:
- `/api/dashboard`
- `/api/students`
- `/api/courses`
- `/api/subjects`
- `/api/school-days`

For full endpoint docs, see:
- `docs/API_DOCUMENTATION.md`

## Summary of Files Added Today (2026-03-10)

Based on current git working tree (`??` entries) and files touched today, these are the key newly added backend files/directories:

- `app/Http/Controllers/Api/CourseController.php`
- `app/Http/Controllers/Api/DashboardController.php`
- `app/Http/Controllers/Api/SchoolDayController.php`
- `app/Http/Controllers/Api/StudentController.php`
- `app/Http/Controllers/Api/SubjectController.php`
- `app/Http/Controllers/AuthController.php`
- `app/Http/Controllers/DashboardController.php`
- `app/Http/Middleware/EnsureApiKeyIsValid.php`
- `app/Models/SchoolDay.php`
- `app/Models/User.php`
- `database/migrations/2026_03_10_000001_add_demographics_to_students_table.php`
- `database/migrations/2026_03_10_000002_add_department_fields_to_courses_table.php`
- `database/migrations/2026_03_10_000003_create_school_days_table.php`
- `database/seeders/CourseSeeder.php`
- `database/seeders/SchoolDaySeeder.php`
- `database/seeders/StudentSeeder.php`
- `database/seeders/StudentsSeeder.php`
- `docs/`

## Summary of Files Modified Today (2026-03-10)

Based on current git working tree (`M` entries), these are the tracked files modified today:

- `README.md`
- `app/Http/Controllers/Api/AuthController.php`
- `app/Models/Course.php`
- `app/Models/Student.php`
- `app/Providers/AppServiceProvider.php`
- `bootstrap/app.php`
- `config/cors.php`
- `config/services.php`
- `database/seeders/DatabaseSeeder.php`
- `routes/api.php`
- `tests/Feature/ExampleTest.php`

## Change Narrative (2026-03-10)

Today, the backend was expanded into a complete API layer for enrollment operations, including authentication, dashboard analytics, students, courses/programs, subjects, and school-day management. The data model and seeders were updated so programs are degree-based (for example, BSIT and BSCS), students are automatically enrolled into randomized programs, and student records include demographic fields. Dashboard outputs were aligned with frontend requirements by returning chart-ready payloads and course-distribution values that use full program names and counts. Documentation was also refreshed with full endpoint behavior and updated backend setup instructions in `README.md` and `docs/API_DOCUMENTATION.md`.

Notes:
- Frontend work is still incomplete.
