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
        'department',
        'description',
        'units',
        'capacity',
    ];

    protected $appends = ['name', 'title', 'type', 'duration', 'status', 'total_units', 'year_levels'];

    public function getNameAttribute(mixed $value = null): string
    {
        return (string) ($this->attributes['course_name'] ?? $value ?? '');
    }

    public function getTitleAttribute(mixed $value = null): string
    {
        return (string) ($this->attributes['course_name'] ?? $this->attributes['name'] ?? $value ?? '');
    }

    public function getTypeAttribute(): string
    {
        return "Bachelor's";
    }

    public function getDurationAttribute(): string
    {
        return '4 years';
    }

    public function getStatusAttribute(): string
    {
        return 'active';
    }

    public function getTotalUnitsAttribute(): int
    {
        return (int) ($this->attributes['units'] ?? 0);
    }

    public function getYearLevelsAttribute(): array
    {
        return self::curriculumForProgram((string) $this->course_code);
    }

    public static function curriculumForProgram(string $programCode): array
    {
        $catalog = self::curriculaCatalog();
        return $catalog[$programCode] ?? [];
    }

    public static function curriculaCatalog(): array
    {
        return [
            'BSIT' => [
                '1st year' => [
                    ['id' => 1101, 'code' => 'IT111', 'title' => 'Introduction to Computing', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 1102, 'code' => 'IT112', 'title' => 'Computer Programming 1', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 1103, 'code' => 'GE-PCW', 'title' => 'Purposive Communication', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 1104, 'code' => 'IT121', 'title' => 'Computer Programming 2', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['IT112']],
                    ['id' => 1105, 'code' => 'IT123', 'title' => 'Discrete Structures', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '2nd year' => [
                    ['id' => 1201, 'code' => 'IT131', 'title' => 'Data Structures and Algorithms', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['IT121']],
                    ['id' => 1202, 'code' => 'IT141', 'title' => 'Information Management', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['IT121']],
                    ['id' => 1203, 'code' => 'IT151', 'title' => 'Platform Technologies', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                    ['id' => 1204, 'code' => 'IT171', 'title' => 'Networking 1', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '3rd year' => [
                    ['id' => 1301, 'code' => 'IT181', 'title' => 'Object-Oriented Programming', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['IT131']],
                    ['id' => 1302, 'code' => 'IT191', 'title' => 'Web Systems and Technologies', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['IT141']],
                    ['id' => 1303, 'code' => 'IT201', 'title' => 'Systems Integration and Architecture', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['IT151']],
                    ['id' => 1304, 'code' => 'IT211', 'title' => 'Information Assurance and Security', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['IT171']],
                ],
                '4th year' => [
                    ['id' => 1401, 'code' => 'IT221', 'title' => 'Capstone Project and Research 1', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['IT201']],
                    ['id' => 1402, 'code' => 'IT222', 'title' => 'Capstone Project and Research 2', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['IT221']],
                ],
            ],
            'BSCS' => [
                '1st year' => [
                    ['id' => 2101, 'code' => 'CS111', 'title' => 'Programming Fundamentals', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 2102, 'code' => 'CS112', 'title' => 'Calculus for Computing', 'units' => 4, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 2103, 'code' => 'CS121', 'title' => 'Object-Oriented Programming', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['CS111']],
                    ['id' => 2104, 'code' => 'CS122', 'title' => 'Discrete Mathematics', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '2nd year' => [
                    ['id' => 2201, 'code' => 'CS211', 'title' => 'Data Structures', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['CS121']],
                    ['id' => 2202, 'code' => 'CS212', 'title' => 'Algorithms and Complexity', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['CS211']],
                    ['id' => 2203, 'code' => 'CS221', 'title' => 'Computer Architecture', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                    ['id' => 2204, 'code' => 'CS222', 'title' => 'Operating Systems', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['CS221']],
                ],
                '3rd year' => [
                    ['id' => 2301, 'code' => 'CS311', 'title' => 'Software Engineering', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['CS212']],
                    ['id' => 2302, 'code' => 'CS312', 'title' => 'Database Management Systems', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['CS211']],
                    ['id' => 2303, 'code' => 'CS321', 'title' => 'Artificial Intelligence', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['CS212']],
                ],
                '4th year' => [
                    ['id' => 2401, 'code' => 'CS411', 'title' => 'CS Thesis 1', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['CS311']],
                    ['id' => 2402, 'code' => 'CS412', 'title' => 'CS Thesis 2', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['CS411']],
                ],
            ],
            'BSIS' => [
                '1st year' => [
                    ['id' => 3101, 'code' => 'IS111', 'title' => 'Foundations of Information Systems', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 3102, 'code' => 'IS112', 'title' => 'Business Communication', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 3103, 'code' => 'IS121', 'title' => 'Fundamentals of Programming', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '2nd year' => [
                    ['id' => 3201, 'code' => 'IS211', 'title' => 'Business Process Management', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 3202, 'code' => 'IS221', 'title' => 'Systems Analysis and Design', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['IS211']],
                ],
                '3rd year' => [
                    ['id' => 3301, 'code' => 'IS311', 'title' => 'Enterprise Architecture', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['IS221']],
                    ['id' => 3302, 'code' => 'IS321', 'title' => 'IS Project Management', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['IS221']],
                ],
                '4th year' => [
                    ['id' => 3401, 'code' => 'IS411', 'title' => 'IS Capstone 1', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['IS321']],
                    ['id' => 3402, 'code' => 'IS412', 'title' => 'IS Capstone 2', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['IS411']],
                ],
            ],
            'BSBA-MM' => [
                '1st year' => [
                    ['id' => 4101, 'code' => 'MM111', 'title' => 'Principles of Marketing', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 4102, 'code' => 'MM112', 'title' => 'Business Mathematics', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '2nd year' => [
                    ['id' => 4201, 'code' => 'MM211', 'title' => 'Consumer Behavior', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['MM111']],
                    ['id' => 4202, 'code' => 'MM221', 'title' => 'Sales Management', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['MM111']],
                ],
                '3rd year' => [
                    ['id' => 4301, 'code' => 'MM311', 'title' => 'Digital Marketing', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['MM211']],
                    ['id' => 4302, 'code' => 'MM321', 'title' => 'Integrated Marketing Communications', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['MM211']],
                ],
                '4th year' => [
                    ['id' => 4401, 'code' => 'MM411', 'title' => 'Marketing Research', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['MM311']],
                    ['id' => 4402, 'code' => 'MM421', 'title' => 'Strategic Marketing Management', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['MM321']],
                ],
            ],
            'BSBA-FM' => [
                '1st year' => [
                    ['id' => 5101, 'code' => 'FM111', 'title' => 'Fundamentals of Financial Management', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 5102, 'code' => 'FM121', 'title' => 'Business Statistics', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '2nd year' => [
                    ['id' => 5201, 'code' => 'FM211', 'title' => 'Investment Analysis', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['FM111']],
                    ['id' => 5202, 'code' => 'FM221', 'title' => 'Financial Markets and Institutions', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['FM111']],
                ],
                '3rd year' => [
                    ['id' => 5301, 'code' => 'FM311', 'title' => 'Risk Management', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['FM211']],
                    ['id' => 5302, 'code' => 'FM321', 'title' => 'Corporate Finance', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['FM221']],
                ],
                '4th year' => [
                    ['id' => 5401, 'code' => 'FM411', 'title' => 'Treasury and Banking Operations', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['FM321']],
                    ['id' => 5402, 'code' => 'FM421', 'title' => 'Financial Management Practicum', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['FM311']],
                ],
            ],
            'BSA' => [
                '1st year' => [
                    ['id' => 6101, 'code' => 'ACCT111', 'title' => 'Fundamentals of Accountancy and Business', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 6102, 'code' => 'ACCT121', 'title' => 'Partnership and Corporation Accounting', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['ACCT111']],
                ],
                '2nd year' => [
                    ['id' => 6201, 'code' => 'ACCT211', 'title' => 'Intermediate Accounting 1', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['ACCT121']],
                    ['id' => 6202, 'code' => 'ACCT221', 'title' => 'Cost Accounting and Control', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['ACCT211']],
                ],
                '3rd year' => [
                    ['id' => 6301, 'code' => 'ACCT311', 'title' => 'Auditing and Assurance', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['ACCT221']],
                    ['id' => 6302, 'code' => 'ACCT321', 'title' => 'Taxation', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['ACCT221']],
                ],
                '4th year' => [
                    ['id' => 6401, 'code' => 'ACCT411', 'title' => 'Accounting Information Systems', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['ACCT311']],
                    ['id' => 6402, 'code' => 'ACCT421', 'title' => 'Integrated Review and Evaluation', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['ACCT321']],
                ],
            ],
            'BSCRIM' => [
                '1st year' => [
                    ['id' => 7101, 'code' => 'CRIM111', 'title' => 'Introduction to Criminology', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 7102, 'code' => 'CRIM121', 'title' => 'Philippine Criminal Justice System', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '2nd year' => [
                    ['id' => 7201, 'code' => 'CRIM211', 'title' => 'Criminal Law and Jurisprudence 1', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['CRIM111']],
                    ['id' => 7202, 'code' => 'CRIM221', 'title' => 'Criminalistics 1', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['CRIM121']],
                ],
                '3rd year' => [
                    ['id' => 7301, 'code' => 'CRIM311', 'title' => 'Institutional Corrections', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['CRIM211']],
                    ['id' => 7302, 'code' => 'CRIM321', 'title' => 'Forensic Photography', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['CRIM221']],
                ],
                '4th year' => [
                    ['id' => 7401, 'code' => 'CRIM411', 'title' => 'Law Enforcement Administration', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['CRIM311']],
                    ['id' => 7402, 'code' => 'CRIM421', 'title' => 'Criminology Internship', 'units' => 6, 'semester' => '2nd', 'prerequisites' => ['CRIM321']],
                ],
            ],
            'BEED' => [
                '1st year' => [
                    ['id' => 8101, 'code' => 'EDUC111', 'title' => 'Foundations of Education', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 8102, 'code' => 'EDUC121', 'title' => 'Child and Adolescent Learners', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '2nd year' => [
                    ['id' => 8201, 'code' => 'EDUC211', 'title' => 'Teaching Profession', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['EDUC111']],
                    ['id' => 8202, 'code' => 'EDUC221', 'title' => 'Assessment of Learning', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['EDUC121']],
                ],
                '3rd year' => [
                    ['id' => 8301, 'code' => 'EDUC311', 'title' => 'Curriculum Development', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['EDUC211']],
                    ['id' => 8302, 'code' => 'EDUC321', 'title' => 'Teaching Science in Elementary Grades', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['EDUC221']],
                ],
                '4th year' => [
                    ['id' => 8401, 'code' => 'EDUC411', 'title' => 'Practice Teaching 1', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['EDUC311']],
                    ['id' => 8402, 'code' => 'EDUC421', 'title' => 'Practice Teaching 2', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['EDUC411']],
                ],
            ],
            'BSED-ENG' => [
                '1st year' => [
                    ['id' => 9101, 'code' => 'ENGED111', 'title' => 'Structure of English', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 9102, 'code' => 'ENGED121', 'title' => 'Introduction to Linguistics', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '2nd year' => [
                    ['id' => 9201, 'code' => 'ENGED211', 'title' => 'Language Teaching Theories', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['ENGED111']],
                    ['id' => 9202, 'code' => 'ENGED221', 'title' => 'Campus Journalism', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['ENGED121']],
                ],
                '3rd year' => [
                    ['id' => 9301, 'code' => 'ENGED311', 'title' => 'Teaching Literature', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['ENGED211']],
                    ['id' => 9302, 'code' => 'ENGED321', 'title' => 'Materials Development for Language Teaching', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['ENGED221']],
                ],
                '4th year' => [
                    ['id' => 9401, 'code' => 'ENGED411', 'title' => 'Practice Teaching (English) 1', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['ENGED311']],
                    ['id' => 9402, 'code' => 'ENGED421', 'title' => 'Practice Teaching (English) 2', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['ENGED411']],
                ],
            ],
            'BSHM' => [
                '1st year' => [
                    ['id' => 10101, 'code' => 'HM111', 'title' => 'Introduction to Hospitality Industry', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 10102, 'code' => 'HM121', 'title' => 'Food Safety and Sanitation', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '2nd year' => [
                    ['id' => 10201, 'code' => 'HM211', 'title' => 'Front Office Operations', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['HM111']],
                    ['id' => 10202, 'code' => 'HM221', 'title' => 'Housekeeping Operations', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['HM121']],
                ],
                '3rd year' => [
                    ['id' => 10301, 'code' => 'HM311', 'title' => 'Hospitality Marketing', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['HM211']],
                    ['id' => 10302, 'code' => 'HM321', 'title' => 'Menu Planning and Cost Control', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['HM221']],
                ],
                '4th year' => [
                    ['id' => 10401, 'code' => 'HM411', 'title' => 'Hospitality Strategic Management', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['HM311']],
                    ['id' => 10402, 'code' => 'HM421', 'title' => 'Internship and Practicum', 'units' => 6, 'semester' => '2nd', 'prerequisites' => ['HM321']],
                ],
            ],
        ];
    }

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