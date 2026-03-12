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
            'BSN' => [
                '1st year' => [
                    ['id' => 11101, 'code' => 'NUR111', 'title' => 'Anatomy and Physiology', 'units' => 4, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 11102, 'code' => 'NUR121', 'title' => 'Fundamentals of Nursing Practice', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '2nd year' => [
                    ['id' => 11201, 'code' => 'NUR211', 'title' => 'Health Assessment', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['NUR121']],
                    ['id' => 11202, 'code' => 'NUR221', 'title' => 'Maternal and Child Nursing', 'units' => 4, 'semester' => '2nd', 'prerequisites' => ['NUR211']],
                ],
                '3rd year' => [
                    ['id' => 11301, 'code' => 'NUR311', 'title' => 'Medical-Surgical Nursing 1', 'units' => 4, 'semester' => '1st', 'prerequisites' => ['NUR221']],
                    ['id' => 11302, 'code' => 'NUR321', 'title' => 'Community Health Nursing', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['NUR211']],
                ],
                '4th year' => [
                    ['id' => 11401, 'code' => 'NUR411', 'title' => 'Medical-Surgical Nursing 2', 'units' => 4, 'semester' => '1st', 'prerequisites' => ['NUR311']],
                    ['id' => 11402, 'code' => 'NUR421', 'title' => 'Nursing Leadership and Management', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['NUR321']],
                ],
            ],
            'BSMT' => [
                '1st year' => [
                    ['id' => 12101, 'code' => 'MT111', 'title' => 'General Pathology', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 12102, 'code' => 'MT121', 'title' => 'Clinical Microscopy', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '2nd year' => [
                    ['id' => 12201, 'code' => 'MT211', 'title' => 'Hematology 1', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['MT121']],
                    ['id' => 12202, 'code' => 'MT221', 'title' => 'Clinical Chemistry 1', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['MT121']],
                ],
                '3rd year' => [
                    ['id' => 12301, 'code' => 'MT311', 'title' => 'Microbiology and Parasitology', 'units' => 4, 'semester' => '1st', 'prerequisites' => ['MT221']],
                    ['id' => 12302, 'code' => 'MT321', 'title' => 'Immunology and Serology', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['MT211']],
                ],
                '4th year' => [
                    ['id' => 12401, 'code' => 'MT411', 'title' => 'Laboratory Management', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['MT321']],
                    ['id' => 12402, 'code' => 'MT421', 'title' => 'Medical Technology Internship', 'units' => 6, 'semester' => '2nd', 'prerequisites' => ['MT311']],
                ],
            ],
            'BSPSY' => [
                '1st year' => [
                    ['id' => 13101, 'code' => 'PSY111', 'title' => 'Introduction to Psychology', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 13102, 'code' => 'PSY121', 'title' => 'Developmental Psychology', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '2nd year' => [
                    ['id' => 13201, 'code' => 'PSY211', 'title' => 'Abnormal Psychology', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['PSY121']],
                    ['id' => 13202, 'code' => 'PSY221', 'title' => 'Social Psychology', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['PSY111']],
                ],
                '3rd year' => [
                    ['id' => 13301, 'code' => 'PSY311', 'title' => 'Psychological Assessment', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['PSY211']],
                    ['id' => 13302, 'code' => 'PSY321', 'title' => 'Experimental Psychology', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['PSY221']],
                ],
                '4th year' => [
                    ['id' => 13401, 'code' => 'PSY411', 'title' => 'Counseling Psychology', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['PSY311']],
                    ['id' => 13402, 'code' => 'PSY421', 'title' => 'Psychology Practicum', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['PSY321']],
                ],
            ],
            'ABCOMM' => [
                '1st year' => [
                    ['id' => 14101, 'code' => 'COMM111', 'title' => 'Introduction to Communication', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 14102, 'code' => 'COMM121', 'title' => 'Speech and Oral Communication', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '2nd year' => [
                    ['id' => 14201, 'code' => 'COMM211', 'title' => 'Journalism Fundamentals', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['COMM121']],
                    ['id' => 14202, 'code' => 'COMM221', 'title' => 'Media Writing and Production', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['COMM111']],
                ],
                '3rd year' => [
                    ['id' => 14301, 'code' => 'COMM311', 'title' => 'Broadcast Communication', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['COMM221']],
                    ['id' => 14302, 'code' => 'COMM321', 'title' => 'Communication Research Methods', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['COMM211']],
                ],
                '4th year' => [
                    ['id' => 14401, 'code' => 'COMM411', 'title' => 'Communication Campaign Planning', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['COMM321']],
                    ['id' => 14402, 'code' => 'COMM421', 'title' => 'Communication Internship', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['COMM311']],
                ],
            ],
            'BSEE' => [
                '1st year' => [
                    ['id' => 15101, 'code' => 'EE111', 'title' => 'Engineering Drawing', 'units' => 2, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 15102, 'code' => 'EE121', 'title' => 'Basic Electrical Circuits', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '2nd year' => [
                    ['id' => 15201, 'code' => 'EE211', 'title' => 'Circuit Analysis', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['EE121']],
                    ['id' => 15202, 'code' => 'EE221', 'title' => 'Electronics 1', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['EE211']],
                ],
                '3rd year' => [
                    ['id' => 15301, 'code' => 'EE311', 'title' => 'Power Systems 1', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['EE221']],
                    ['id' => 15302, 'code' => 'EE321', 'title' => 'Control Systems', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['EE221']],
                ],
                '4th year' => [
                    ['id' => 15401, 'code' => 'EE411', 'title' => 'Electrical Design', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['EE311']],
                    ['id' => 15402, 'code' => 'EE421', 'title' => 'EE Plant and Industrial Safety', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['EE321']],
                ],
            ],
            'BSCE' => [
                '1st year' => [
                    ['id' => 16101, 'code' => 'CE111', 'title' => 'Engineering Surveying', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 16102, 'code' => 'CE121', 'title' => 'Engineering Mechanics', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '2nd year' => [
                    ['id' => 16201, 'code' => 'CE211', 'title' => 'Strength of Materials', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['CE121']],
                    ['id' => 16202, 'code' => 'CE221', 'title' => 'Hydraulics', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['CE121']],
                ],
                '3rd year' => [
                    ['id' => 16301, 'code' => 'CE311', 'title' => 'Structural Theory', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['CE211']],
                    ['id' => 16302, 'code' => 'CE321', 'title' => 'Transportation Engineering', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['CE221']],
                ],
                '4th year' => [
                    ['id' => 16401, 'code' => 'CE411', 'title' => 'Construction Methods and Project Management', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['CE311']],
                    ['id' => 16402, 'code' => 'CE421', 'title' => 'Geotechnical Engineering', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['CE321']],
                ],
            ],
            'BSME' => [
                '1st year' => [
                    ['id' => 17101, 'code' => 'ME111', 'title' => 'Engineering Materials', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 17102, 'code' => 'ME121', 'title' => 'Engineering Thermodynamics 1', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '2nd year' => [
                    ['id' => 17201, 'code' => 'ME211', 'title' => 'Mechanics of Deformable Bodies', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['ME111']],
                    ['id' => 17202, 'code' => 'ME221', 'title' => 'Fluid Mechanics', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['ME121']],
                ],
                '3rd year' => [
                    ['id' => 17301, 'code' => 'ME311', 'title' => 'Machine Design 1', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['ME211']],
                    ['id' => 17302, 'code' => 'ME321', 'title' => 'Manufacturing Processes', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['ME221']],
                ],
                '4th year' => [
                    ['id' => 17401, 'code' => 'ME411', 'title' => 'ME Plant Design', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['ME311']],
                    ['id' => 17402, 'code' => 'ME421', 'title' => 'Mechanical Engineering Practice', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['ME321']],
                ],
            ],
            'BSAIS' => [
                '1st year' => [
                    ['id' => 18101, 'code' => 'AIS111', 'title' => 'Introduction to Accounting Information Systems', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 18102, 'code' => 'AIS121', 'title' => 'Fundamentals of Financial Accounting', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '2nd year' => [
                    ['id' => 18201, 'code' => 'AIS211', 'title' => 'Database Management for AIS', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['AIS111']],
                    ['id' => 18202, 'code' => 'AIS221', 'title' => 'Accounting Systems Analysis', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['AIS121']],
                ],
                '3rd year' => [
                    ['id' => 18301, 'code' => 'AIS311', 'title' => 'Enterprise Resource Planning', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['AIS211']],
                    ['id' => 18302, 'code' => 'AIS321', 'title' => 'Internal Controls and IT Audit', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['AIS221']],
                ],
                '4th year' => [
                    ['id' => 18401, 'code' => 'AIS411', 'title' => 'Forensic Accounting Systems', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['AIS321']],
                    ['id' => 18402, 'code' => 'AIS421', 'title' => 'AIS Capstone Project', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['AIS311']],
                ],
            ],
            'BSTM' => [
                '1st year' => [
                    ['id' => 19101, 'code' => 'TM111', 'title' => 'Introduction to Tourism and Hospitality', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 19102, 'code' => 'TM121', 'title' => 'Philippine Tourism Geography', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '2nd year' => [
                    ['id' => 19201, 'code' => 'TM211', 'title' => 'Travel and Tour Operations', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['TM111']],
                    ['id' => 19202, 'code' => 'TM221', 'title' => 'Airline Operations Management', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['TM121']],
                ],
                '3rd year' => [
                    ['id' => 19301, 'code' => 'TM311', 'title' => 'Events Management', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['TM211']],
                    ['id' => 19302, 'code' => 'TM321', 'title' => 'Sustainable Tourism Development', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['TM221']],
                ],
                '4th year' => [
                    ['id' => 19401, 'code' => 'TM411', 'title' => 'Tourism Policy and Planning', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['TM321']],
                    ['id' => 19402, 'code' => 'TM421', 'title' => 'Tourism Internship', 'units' => 6, 'semester' => '2nd', 'prerequisites' => ['TM311']],
                ],
            ],
            'BSED-MATH' => [
                '1st year' => [
                    ['id' => 20101, 'code' => 'MTHED111', 'title' => 'College Algebra for Teachers', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 20102, 'code' => 'MTHED121', 'title' => 'Trigonometry and Analytic Geometry', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '2nd year' => [
                    ['id' => 20201, 'code' => 'MTHED211', 'title' => 'Calculus for Secondary Mathematics', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['MTHED121']],
                    ['id' => 20202, 'code' => 'MTHED221', 'title' => 'Teaching Mathematics in the Secondary Level 1', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['MTHED111']],
                ],
                '3rd year' => [
                    ['id' => 20301, 'code' => 'MTHED311', 'title' => 'Probability and Statistics', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['MTHED211']],
                    ['id' => 20302, 'code' => 'MTHED321', 'title' => 'Teaching Mathematics in the Secondary Level 2', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['MTHED221']],
                ],
                '4th year' => [
                    ['id' => 20401, 'code' => 'MTHED411', 'title' => 'Assessment and Evaluation in Mathematics', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['MTHED321']],
                    ['id' => 20402, 'code' => 'MTHED421', 'title' => 'Practice Teaching (Mathematics)', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['MTHED411']],
                ],
            ],
            'BPOLSCI' => [
                '1st year' => [
                    ['id' => 21101, 'code' => 'POL111', 'title' => 'Introduction to Political Science', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 21102, 'code' => 'POL121', 'title' => 'Philippine Politics and Governance', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '2nd year' => [
                    ['id' => 21201, 'code' => 'POL211', 'title' => 'Comparative Politics', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['POL111']],
                    ['id' => 21202, 'code' => 'POL221', 'title' => 'Public Administration', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['POL121']],
                ],
                '3rd year' => [
                    ['id' => 21301, 'code' => 'POL311', 'title' => 'Political Theory', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['POL211']],
                    ['id' => 21302, 'code' => 'POL321', 'title' => 'International Relations', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['POL211']],
                ],
                '4th year' => [
                    ['id' => 21401, 'code' => 'POL411', 'title' => 'Public Policy Analysis', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['POL321']],
                    ['id' => 21402, 'code' => 'POL421', 'title' => 'Political Science Research', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['POL311']],
                ],
            ],
            'BSOA' => [
                '1st year' => [
                    ['id' => 22101, 'code' => 'OA111', 'title' => 'Office Procedures and Records Management', 'units' => 3, 'semester' => '1st', 'prerequisites' => []],
                    ['id' => 22102, 'code' => 'OA121', 'title' => 'Keyboarding and Office Applications', 'units' => 3, 'semester' => '2nd', 'prerequisites' => []],
                ],
                '2nd year' => [
                    ['id' => 22201, 'code' => 'OA211', 'title' => 'Administrative Office Management', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['OA111']],
                    ['id' => 22202, 'code' => 'OA221', 'title' => 'Business Communication', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['OA121']],
                ],
                '3rd year' => [
                    ['id' => 22301, 'code' => 'OA311', 'title' => 'Human Resource Office Operations', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['OA211']],
                    ['id' => 22302, 'code' => 'OA321', 'title' => 'Office Systems Analysis', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['OA221']],
                ],
                '4th year' => [
                    ['id' => 22401, 'code' => 'OA411', 'title' => 'Executive Office Support Systems', 'units' => 3, 'semester' => '1st', 'prerequisites' => ['OA321']],
                    ['id' => 22402, 'code' => 'OA421', 'title' => 'Office Administration Internship', 'units' => 3, 'semester' => '2nd', 'prerequisites' => ['OA311']],
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