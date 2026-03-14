# VERDIDA Enrollment System API Documentation

## Overview
This document describes how each backend API endpoint works in the current Laravel backend.

- Base URL (local HTTPS): `https://VERDIDA_IT15_ENROLLMENT_SYSTEM.test/api`
- Auth: Laravel Sanctum Bearer token
- Content type: `application/json`
- Protected endpoints: all routes except `POST /login`

Verified on 2026-03-11:
- Unauthenticated protected requests return JSON `401` with `{"message":"Unauthenticated."}`.

## Common Request Headers
Use these for authenticated routes:

```http
Accept: application/json
Content-Type: application/json
Authorization: Bearer <sanctum-token>
```

## Authentication Flow
1. Call `POST /login` with admin credentials.
2. Save the returned token.
3. Send `Authorization: Bearer <token>` for protected endpoints.
4. Call `POST /logout` to revoke current token.

Seeded admin account:

```json
{
  "email": "admin@example.com",
  "password": "password123"
}
```

## Error Conventions
- `401 Unauthorized`: invalid credentials or missing/invalid token
- `422 Unprocessable Entity`: validation errors or enrollment rule violations
- `200/201`: successful request

Unauthenticated response example (`401`):

```json
{
  "message": "Unauthenticated."
}
```

Validation error format example:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email field is required."
    ]
  }
}
```

## 1) Auth Endpoints

### POST `/login`
Authenticates an admin and issues a Sanctum token.

Request body:

```json
{
  "email": "admin@example.com",
  "password": "password123",
  "device_name": "react-client"
}
```

Success `200`:

```json
{
  "message": "Authenticated successfully.",
  "token": "1|...",
  "user": {
    "id": 1,
    "name": "System Admin",
    "email": "admin@example.com"
  }
}
```

Failure `401`:

```json
{
  "message": "Invalid credentials."
}
```

### GET `/me`
Returns currently authenticated admin user.

Success `200`: user object

### POST `/logout`
Revokes the current access token.

Success `200`:

```json
{
  "message": "Logged out successfully."
}
```

## 2) Dashboard Endpoint

### GET `/dashboard`
Returns dashboard cards and chart datasets.

Success `200` (shape):

```json
{
  "total_programs": 10,
  "total_subjects": 93,
  "active_programs": 10,
  "inactive_programs": 0,
  "subjects_with_prerequisites": 0,
  "students_total": 500,
  "enrollments_total": 500,
  "monthly_enrollment": [
    { "name": "Mar 2026", "enrollments": 500 }
  ],
  "course_distribution": [
    {
      "name": "Bachelor of Science in Information Technology",
      "code": "BSIT",
      "value": 46
    }
  ],
  "attendance_pattern": [
    { "day": "Mar 2026", "attendance": 22 }
  ]
}
```

Notes:
- `course_distribution.name` is the full program name (used by pie chart labels/legend).
- `total_subjects` is computed from static curriculum definitions per program.

## 3) Student Endpoints

### GET `/students`
Returns paginated students with UI-ready fields.

Query params:
- `per_page` optional integer, min `1`, max `100`, default `15`
- `search` optional text (matches student number, first name, last name, email)

Success `200`:

```json
{
  "data": [
    {
      "id": 1707,
      "student_number": "2026-0207",
      "student_id": "2026-0207",
      "name": "Bradly Abbott",
      "email": "student207@example.edu",
      "course": {
        "code": "BSBA-MM",
        "name": "Bachelor of Science in Business Administration major in Marketing Management"
      },
      "courses": [
        {
          "id": 84,
          "course_code": "BSBA-MM",
          "course_name": "Bachelor of Science in Business Administration major in Marketing Management"
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 500,
    "last_page": 34
  }
}
```

### POST `/students`
Creates a student record.

Request body:

```json
{
  "student_number": "2026-0501",
  "year_level": 1,
  "first_name": "Maria",
  "last_name": "Cruz",
  "middle_name": "Lopez",
  "gender": "female",
  "date_of_birth": "2005-02-14",
  "contact_number": "09171234567",
  "address": "Manila, Philippines",
  "email": "maria.cruz@example.edu"
}
```

Success `201`: created student in UI-ready format (includes `student_id` and `course`).

Optional fields include `year_level` (integer `1` to `4`).

### GET `/students/{student}`
Returns one student in UI-ready format.

Success `200`: student object with `course` and `courses` fields.

### PUT/PATCH `/students/{student}`
Updates student fields.

Optional fields include `year_level` (integer `1` to `4`).

Success `200`: updated student in UI-ready format.

### DELETE `/students/{student}`
Deletes a student.

Success `200`:

```json
{
  "message": "Student deleted successfully."
}
```

### POST `/students/{student}/courses/{course}`
Enrolls student into a specific course.

Success `200`:

```json
{
  "message": "Enrollment successful."
}
```

Possible `422` responses:
- `Student is already enrolled in this course.`
- `Course is at full capacity.`

### DELETE `/students/{student}/courses/{course}`
Unenrolls student from a course.

Success `200`:

```json
{
  "message": "Unenrolled successfully."
}
```

## 4) Course Endpoints (Programs)

### GET `/courses`
Returns paginated programs with `students_count` and computed fields.

Query params:
- `per_page` optional integer, max `100`, default `15` when pagination is explicitly requested
- `search` optional text (matches code, name, department)
- `all` optional boolean (`1`, `true`) to return all programs in one response
- `per_page=all` optional alias to return all programs

Behavior:
- Default response returns all programs so frontend lists match database count.
- Use `per_page` only when you want paginated program lists.

Success `200` (Laravel paginator object):

```json
{
  "data": [
    {
      "id": 81,
      "course_code": "BSIT",
      "course_name": "Bachelor of Science in Information Technology",
      "department": "CCE",
      "capacity": 280,
      "students_count": 46,
      "name": "Bachelor of Science in Information Technology",
      "title": "Bachelor of Science in Information Technology",
      "type": "Bachelor's",
      "duration": "4 years",
      "status": "active",
      "total_units": 156,
      "year_levels": {
        "1st year": [],
        "2nd year": [],
        "3rd year": [],
        "4th year": []
      }
    }
  ]
}
```

### POST `/courses`
Creates a program entry.

Required:
- `course_code`
- `course_name`
- `department`
- `capacity`

Optional:
- `description`, `units`

Success `201`: created course object.

### GET `/courses/{course}`
Returns one course with enrolled students plus computed `year_levels`.

### PUT/PATCH `/courses/{course}`
Updates a course.

Success `200`: updated course object.

### DELETE `/courses/{course}`
Deletes a course.

Success `200`:

```json
{
  "message": "Course deleted successfully."
}
```

## 5) Weather Endpoint

### GET `/weather`
Returns current weather and forecast data for a location.

Backend implementation summary:
- Controller: `App\\Http\\Controllers\\Api\\WeatherController@index`
- Route protection: inside `auth:sanctum` group (requires Bearer token)
- External provider (primary): WeatherAPI `/forecast.json`
- Response contract: backend-normalized JSON shape used by frontend (`data.location`, `data.current`, `data.forecast`, `meta`)

Query params:
- `city` optional string (e.g. `Davao`)
- `lat` optional numeric latitude
- `lon` optional numeric longitude
- `days` optional integer `1-5`, default `5`

Validation rule:
- Provide either `city`, or both `lat` and `lon`.

Environment/config used by backend:
- `WEATHER_API_KEY`
- `WEATHER_API_BASE_URL` (default `https://api.weatherapi.com/v1`)
- `WEATHER_CACHE_TTL_MINUTES` (default `10`)
- `WEATHER_STALE_TTL_MINUTES` (default `180`)

Provider requirement:

- `WEATHER_API_KEY` must be a key from `weatherapi.com` when using `WEATHER_API_BASE_URL=https://api.weatherapi.com/v1`.
- Do not use an OpenWeather key with WeatherAPI base URL.

Backend request flow:
1. Validate location input and normalize query.
2. Check fresh cache first (`weather:<query>:days:<n>`).
3. If cached, return immediately.
4. If `WEATHER_API_KEY` is present, call WeatherAPI.
5. If key is missing, use Open-Meteo fallback path.
6. Normalize provider response to the same frontend-friendly schema.
7. Store both fresh cache and stale cache copies.

Examples:
- `/api/weather?city=Davao&days=5`
- `/api/weather?lat=7.1907&lon=125.4553`

Success `200`:

```json
{
  "data": {
    "location": {
      "name": "Davao",
      "region": "Davao del Sur",
      "country": "Philippines",
      "lat": 7.07,
      "lon": 125.6,
      "localtime": "2026-03-12 18:00"
    },
    "current": {
      "temperature_c": 30.2,
      "temperature_f": 86.4,
      "humidity": 68,
      "wind_kph": 11.2,
      "wind_mps": 3.11,
      "condition": "Partly cloudy",
      "icon": "https://cdn.weatherapi.com/weather/64x64/day/116.png",
      "last_updated": "2026-03-12 17:45"
    },
    "forecast": [
      {
        "date": "2026-03-13",
        "max_temp_c": 31.0,
        "min_temp_c": 24.0,
        "avg_temp_c": 27.1,
        "humidity": 72,
        "max_wind_kph": 13.0,
        "condition": "Patchy rain nearby",
        "icon": "https://cdn.weatherapi.com/weather/64x64/day/176.png"
      }
    ]
  },
  "meta": {
    "provider": "weatherapi",
    "query": "Davao",
    "days": 5,
    "cached": false,
    "stale": false
  }
}
```

Possible errors:
- `422` when location input is missing/invalid
- `429` when provider rate limit is reached and no stale cache is available
- `502` when provider is unavailable and no stale cache is available

Graceful fallback behavior:
- When provider fails/rate-limits and stale cache exists, response still returns `200` weather data with:
  - `meta.cached = true`
  - `meta.stale = true`
  - `meta.warning` with fallback reason

Provider notes:
- `meta.provider` is `weatherapi` when WeatherAPI is used.
- If WeatherAPI key is absent, backend can return `open-meteo` provider data using the same response structure.
- Icons are always normalized to absolute URLs so frontend can render them directly.

Operational tips:
- Run `php artisan optimize:clear` after any `.env` weather changes.
- Newly created provider keys can take a few minutes before successful upstream responses.

## 6) School Day Endpoints

### GET `/school-days`
Returns paginated school-day records.

Query params:
- `per_page` optional integer, max `100`, default `20`
- `all` optional boolean; when `true`, returns all school-day records
- `per_page=all` optional alias to return all school-day records
- `day_type` optional: `regular`, `holiday`, `event`

Behavior:
- Use `all=1` or `per_page=all` when frontend calendar views need full school-year highlights in one response.

Examples:
- `/api/school-days?all=1`
- `/api/school-days?per_page=all`

### POST `/school-days`
Creates a school-day record.

Required:
- `date` (unique)
- `day_type`
- `title`

Optional:
- `description`, `is_attendance_required`

Success `201`: created school-day object.

### GET `/school-days/{school_day}`
Returns one school-day record.

### PUT/PATCH `/school-days/{school_day}`
Updates school-day fields.

### DELETE `/school-days/{school_day}`
Deletes school-day record.

Success `200`:

```json
{
  "message": "School day deleted successfully."
}
```

## 7) Subject Endpoint

### GET `/subjects`
Returns flattened curriculum subjects from all programs.

Success `200`:

```json
{
  "data": [
    {
      "id": 1101,
      "code": "IT111",
      "title": "Introduction to Computing",
      "units": 3,
      "semester_offer": "semester",
      "term_offer": "1st Semester",
      "program_code": "BSIT",
      "program": "Bachelor of Science in Information Technology",
      "description": "Introduction to Computing under Bachelor of Science in Information Technology (1st year).",
      "prerequisites": [],
      "co_requisites": []
    }
  ],
  "meta": {
    "total": 93
  }
}
```

## Current API Route Inventory (23 routes)
- `POST /api/login`
- `GET /api/me`
- `POST /api/logout`
- `GET /api/dashboard`
- `GET /api/students`
- `POST /api/students`
- `GET /api/students/{student}`
- `PUT|PATCH /api/students/{student}`
- `DELETE /api/students/{student}`
- `POST /api/students/{student}/courses/{course}`
- `DELETE /api/students/{student}/courses/{course}`
- `GET /api/courses`
- `POST /api/courses`
- `GET /api/courses/{course}`
- `PUT|PATCH /api/courses/{course}`
- `DELETE /api/courses/{course}`
- `GET /api/school-days`
- `POST /api/school-days`
- `GET /api/school-days/{school_day}`
- `PUT|PATCH /api/school-days/{school_day}`
- `DELETE /api/school-days/{school_day}`
- `GET /api/subjects`
- `GET /api/weather`

## HTTPS Verification Commands

Use these commands for quick local checks:

```powershell
curl.exe -k -i "https://VERDIDA_IT15_ENROLLMENT_SYSTEM.test/api/students"
curl.exe -k -i -X OPTIONS "https://VERDIDA_IT15_ENROLLMENT_SYSTEM.test/api/students" -H "Origin: https://localhost:5173" -H "Access-Control-Request-Method: GET"
```
