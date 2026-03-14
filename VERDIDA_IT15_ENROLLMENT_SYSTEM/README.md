# VERDIDA IT15 Enrollment System (Backend)

Laravel backend API for student enrollment, programs, subjects, dashboard analytics, and school-day calendar.

## Documentation
- API reference: `docs/API_DOCUMENTATION.md`
- Frontend integration guide: `docs/FRONTEND_INTEGRATION.md`


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
APP_URL=https://VERDIDA_IT15_ENROLLMENT_SYSTEM.test
FORCE_HTTPS=true
SESSION_SECURE_COOKIE=true

WEATHER_API_KEY=your_weatherapi_key_here
WEATHER_API_BASE_URL=https://api.weatherapi.com/v1
WEATHER_CACHE_TTL_MINUTES=10
WEATHER_STALE_TTL_MINUTES=180

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

### 7. Start backend server (HTTPS - required)
Use Laragon Apache/Nginx (not `php artisan serve`) so API calls use HTTPS.

API base URL (HTTPS):
- `https://VERDIDA_IT15_ENROLLMENT_SYSTEM.test/api`

## HTTPS API Calling Tutorial (Laragon + Laravel)

Follow this once per machine/project setup.

### 1. Enable virtual host and SSL in Laragon
1. Open Laragon as Administrator.
2. Go to Preferences and ensure `Auto-create Virtual Hosts` is enabled.
3. Ensure hostname pattern is `{name}.test`.
4. In Laragon menu, enable Apache SSL.
5. Restart all Laragon services.

### 2. Use the generated project domain
For this project, the domain is:
- `https://VERDIDA_IT15_ENROLLMENT_SYSTEM.test`

### 3. Configure Laravel environment for HTTPS
Update `.env` values:

```env
APP_URL=https://VERDIDA_IT15_ENROLLMENT_SYSTEM.test
FORCE_HTTPS=true
SESSION_SECURE_COOKIE=true
```

### 4. Clear Laravel caches
```powershell
php artisan config:clear
php artisan cache:clear
```

### 5. Verify HTTPS endpoint behavior
Open:
- `https://VERDIDA_IT15_ENROLLMENT_SYSTEM.test/api/students`

Expected result when not logged in:
- HTTP `401`
- Body: `{"message":"Unauthenticated."}`

### 6. Important usage rule
- For grading/secure API testing, use only `https://VERDIDA_IT15_ENROLLMENT_SYSTEM.test/...`
- Do not use `http://127.0.0.1:8000/...` for the HTTPS requirement.

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
- `/api/weather`

Frontend note for full program list:
- Use `/api/courses?all=1` (or `/api/courses?per_page=all`) when you need all programs at once.

Frontend note for full school calendar list:
- Use `/api/school-days?all=1` (or `/api/school-days?per_page=all`) when you need all school-day records for calendar highlights.

For full endpoint docs, see:
- `docs/API_DOCUMENTATION.md`

## Weather API Integration (Backend + Frontend)

Weather endpoint:
- `GET /api/weather`

Query options:
- by city: `/api/weather?city=Davao`
- by coordinates: `/api/weather?lat=7.1907&lon=125.4553`
- include forecast days (1 to 5): `/api/weather?city=Davao&days=5`

Returned fields include:
- current temperature, humidity, wind speed
- 5-day forecast entries with weather icon URL
- location details and local time

Rate limit and error handling:
- If upstream weather provider rate-limits or fails, backend returns cached stale weather (if available) with `meta.warning`.
- If no cache is available, backend returns a clear error JSON with proper status code.

Smooth setup checklist:
- Ensure `WEATHER_API_KEY` is from `weatherapi.com`.
- Run `php artisan optimize:clear` after editing `.env`.
- Keep frontend API URL pointed to backend HTTPS API: `VITE_API_URL=https://VERDIDA_IT15_ENROLLMENT_SYSTEM.test/api`.
- If key was just created, allow a few minutes for provider activation.

Quick backend verification:
```powershell
php artisan tinker --execute "echo config('services.weather.key') ? 'HAS_KEY' : 'EMPTY_KEY';"
```
Expected: `HAS_KEY`

## Current Status (2026-03-11)
- API auth is protected by Sanctum (`auth:sanctum`) except `POST /api/login`.
- Unauthenticated protected calls return `401` JSON: `{"message":"Unauthenticated."}`.
- HTTPS API base for local development is `https://VERDIDA_IT15_ENROLLMENT_SYSTEM.test/api`.

## Update (2026-03-11)

### What Changed Today
- Enforced API unauthenticated behavior to return JSON `401` (`{"message":"Unauthenticated."}`) instead of redirecting to a missing `login` route.
- Confirmed protected API routes are guarded by `auth:sanctum`.
- Enabled Laravel HTTPS URL forcing via `FORCE_HTTPS=true`.
- Configured Laragon Apache SSL virtual host for this project so API calls can be served over HTTPS.
- Updated environment URL to the Laragon HTTPS domain.

### Why This Matters
- Protected endpoints now fail securely and predictably for unauthenticated requests.
- The backend now satisfies the requirement to use HTTPS API calls when accessed through the Laragon domain.