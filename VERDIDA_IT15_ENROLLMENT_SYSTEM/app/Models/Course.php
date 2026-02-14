<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'course_name',
        'capacity',
    ];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'enrollments')
                    ->withTimestamps();
    }

    public function getEnrolledCountAttribute(): int
    {
        return $this->students()->count();
    }

    public function getAvailableSlotsAttribute(): int
    {
        return $this->capacity - $this->enrolled_count;
    }

    public function isFull(): bool
    {
        return $this->students()->count() >= $this->capacity;
    }

    public function canEnroll(Student $student): bool
    {
        return !$this->isFull() && !$student->isEnrolledIn($this->id);
    }
}