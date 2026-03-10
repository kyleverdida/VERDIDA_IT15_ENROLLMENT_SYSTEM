<?php
// USER model
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_number',
        'first_name',
        'last_name',
        'middle_name',
        'gender',
        'date_of_birth',
        'contact_number',
        'address',
        'email',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    protected $appends = ['name'];

    public function getNameAttribute(): string
    {
        return $this->full_name;
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments')
                    ->withTimestamps();
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isEnrolledIn(int $courseId): bool
    {
        return $this->courses()->where('course_id', $courseId)->exists();
    }
}