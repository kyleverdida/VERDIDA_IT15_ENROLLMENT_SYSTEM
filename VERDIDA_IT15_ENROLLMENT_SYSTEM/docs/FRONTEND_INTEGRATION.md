# Frontend Integration Guide

Use this setup in your React frontend so it can connect to the Laravel backend over HTTPS.

Backend API base:
- `https://VERDIDA_IT15_ENROLLMENT_SYSTEM.test/api`

Status verified on 2026-03-11:
- HTTPS API is reachable
- Protected API calls return JSON `401` when no token is provided

## Frontend Environment Variables

Create or update your frontend `.env` or `.env.local` file:

```env
VITE_API_URL=https://VERDIDA_IT15_ENROLLMENT_SYSTEM.test/api
VITE_API_KEY=change-this-api-key
VITE_API_KEY_HEADER=X-API-KEY
```

If your frontend runs on Vite dev server, allow both HTTP and HTTPS origins in backend CORS.

Recommended frontend origins during local development:
- `http://localhost:5173`
- `https://localhost:5173`
- `http://127.0.0.1:5173`
- `https://127.0.0.1:5173`

## Axios Client Example

Create a file like `src/lib/api.js`:

```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    [import.meta.env.VITE_API_KEY_HEADER || 'X-API-KEY']: import.meta.env.VITE_API_KEY,
  },
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');

  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
});

export default api;
```

## Auth API Example

Create a file like `src/services/auth.js`:

```javascript
import api from '../lib/api';

export async function login(payload) {
  const { data } = await api.post('/login', {
    email: payload.email,
    password: payload.password,
    device_name: 'react-client',
  });

  localStorage.setItem('auth_token', data.token);
  localStorage.setItem('auth_user', JSON.stringify(data.user));

  return data;
}

export async function getMe() {
  const { data } = await api.get('/me');
  return data;
}

export async function logout() {
  const { data } = await api.post('/logout');
  localStorage.removeItem('auth_token');
  localStorage.removeItem('auth_user');
  return data;
}
```

## Students API Example

Create a file like `src/services/students.js`:

```javascript
import api from '../lib/api';

export async function getStudents(params = {}) {
  const { data } = await api.get('/students', { params });
  return data;
}

export async function getStudent(studentId) {
  const { data } = await api.get(`/students/${studentId}`);
  return data;
}

export async function createStudent(payload) {
  const { data } = await api.post('/students', payload);
  return data;
}

export async function updateStudent(studentId, payload) {
  const { data } = await api.put(`/students/${studentId}`, payload);
  return data;
}

export async function deleteStudent(studentId) {
  const { data } = await api.delete(`/students/${studentId}`);
  return data;
}

export async function enrollStudent(studentId, courseId) {
  const { data } = await api.post(`/students/${studentId}/courses/${courseId}`);
  return data;
}

export async function unenrollStudent(studentId, courseId) {
  const { data } = await api.delete(`/students/${studentId}/courses/${courseId}`);
  return data;
}
```

## Courses API Example

Create a file like `src/services/courses.js`:

```javascript
import api from '../lib/api';

export async function getCourses(params = {}) {
  const { data } = await api.get('/courses', { params });
  return data;
}

// For dropdowns/forms that need the full program list:
export async function getAllCourses() {
  const { data } = await api.get('/courses', { params: { all: 1 } });
  return data;
}

export async function getCourse(courseId) {
  const { data } = await api.get(`/courses/${courseId}`);
  return data;
}

export async function createCourse(payload) {
  const { data } = await api.post('/courses', payload);
  return data;
}

export async function updateCourse(courseId, payload) {
  const { data } = await api.put(`/courses/${courseId}`, payload);
  return data;
}

export async function deleteCourse(courseId) {
  const { data } = await api.delete(`/courses/${courseId}`);
  return data;
}
```

## School Days API Example

Create a file like `src/services/schoolDays.js`:

```javascript
import api from '../lib/api';

export async function getSchoolDays(params = {}) {
  const { data } = await api.get('/school-days', { params });
  return data;
}

export async function getSchoolDay(id) {
  const { data } = await api.get(`/school-days/${id}`);
  return data;
}

export async function createSchoolDay(payload) {
  const { data } = await api.post('/school-days', payload);
  return data;
}

export async function updateSchoolDay(id, payload) {
  const { data } = await api.put(`/school-days/${id}`, payload);
  return data;
}

export async function deleteSchoolDay(id) {
  const { data } = await api.delete(`/school-days/${id}`);
  return data;
}
```

## Dashboard API Example

Create a file like `src/services/dashboard.js`:

```javascript
import api from '../lib/api';

export async function getDashboard() {
  const { data } = await api.get('/dashboard');
  return data;
}
```

## Fetch Alternative

If you prefer `fetch` instead of Axios:

```javascript
const API_URL = import.meta.env.VITE_API_URL;
const API_KEY = import.meta.env.VITE_API_KEY;
const API_KEY_HEADER = import.meta.env.VITE_API_KEY_HEADER || 'X-API-KEY';

export async function apiFetch(path, options = {}) {
  const token = localStorage.getItem('auth_token');

  const response = await fetch(`${API_URL}${path}`, {
    ...options,
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      [API_KEY_HEADER]: API_KEY,
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
      ...(options.headers || {}),
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw data;
  }

  return data;
}
```

## Quick Test

After adding the frontend env values and API client code:

1. Restart the frontend dev server.
2. Send a login request using:

```json
{
  "email": "admin@example.com",
  "password": "password123",
  "device_name": "react-client"
}
```

3. Confirm the frontend stores the token.
4. Call `/me` or `/dashboard` to verify authenticated requests work.

Expected unauthenticated behavior for protected routes:

```json
{
  "message": "Unauthenticated."
}
```

## HTTPS Notes

1. If browser shows certificate warnings, trust Laragon SSL certificate first.
2. If frontend is on HTTPS, API must also be HTTPS to avoid mixed-content blocking.
3. Restart frontend dev server after changing `VITE_*` variables.
4. Do not use `http://127.0.0.1:8000/api` for security/grading checks.
