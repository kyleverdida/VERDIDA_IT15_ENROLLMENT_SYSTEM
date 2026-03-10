<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolDay;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolDayController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 20);
        $type = $request->query('day_type');

        $days = SchoolDay::query()
            ->when($type, fn ($query) => $query->where('day_type', $type))
            ->orderBy('date')
            ->paginate(max(1, min($perPage, 100)));

        return response()->json($days);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date', 'unique:school_days,date'],
            'day_type' => ['required', Rule::in(['regular', 'holiday', 'event'])],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_attendance_required' => ['boolean'],
        ]);

        $schoolDay = SchoolDay::create($this->sanitizePayload($validated));

        return response()->json($schoolDay, 201);
    }

    public function show(SchoolDay $schoolDay): JsonResponse
    {
        return response()->json($schoolDay);
    }

    public function update(Request $request, SchoolDay $schoolDay): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['sometimes', 'required', 'date', Rule::unique('school_days', 'date')->ignore($schoolDay->id)],
            'day_type' => ['sometimes', 'required', Rule::in(['regular', 'holiday', 'event'])],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_attendance_required' => ['boolean'],
        ]);

        $schoolDay->update($this->sanitizePayload($validated));

        return response()->json($schoolDay->fresh());
    }

    public function destroy(SchoolDay $schoolDay): JsonResponse
    {
        $schoolDay->delete();

        return response()->json(['message' => 'School day deleted successfully.']);
    }

    private function sanitizePayload(array $payload): array
    {
        foreach (['title', 'description'] as $field) {
            if (array_key_exists($field, $payload) && is_string($payload[$field])) {
                $payload[$field] = trim(strip_tags($payload[$field]));
            }
        }

        return $payload;
    }
}
