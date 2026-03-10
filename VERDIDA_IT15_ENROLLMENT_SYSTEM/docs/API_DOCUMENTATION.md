# VERDIDA Enrollment System API Documentation

## Overview
This document describes how each backend API endpoint works in the current Laravel backend.

- Base URL (local): `http://127.0.0.1:8000/api`
- Auth: Laravel Sanctum Bearer token
- Content type: `application/json`
- Protected endpoints: all routes except `POST /login`

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

### GET `/students/{student}`
Returns one student in UI-ready format.

Success `200`: student object with `course` and `courses` fields.

### PUT/PATCH `/students/{student}`
Updates student fields.

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
- `per_page` optional integer, max `100`, default `15`
- `search` optional text (matches code, name, department)

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

## 5) School Day Endpoints

### GET `/school-days`
Returns paginated school-day records.

Query params:
- `per_page` optional integer, max `100`, default `20`
- `day_type` optional: `regular`, `holiday`, `event`

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

## 6) Subject Endpoint

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

## Current API Route Inventory (22 routes)
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
