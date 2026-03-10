<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'day_type',
        'title',
        'description',
        'is_attendance_required',
    ];

    protected $casts = [
        'date' => 'date',
        'is_attendance_required' => 'boolean',
    ];
}
