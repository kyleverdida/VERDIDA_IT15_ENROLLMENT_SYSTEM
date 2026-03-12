<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        Course::query()->delete();

        $courseData = [
            ['BSIT', 'Bachelor of Science in Information Technology', 'College of Computing Education', 156, 280, 'Focuses on software, networking, databases, and enterprise information systems.'],
            ['BSCS', 'Bachelor of Science in Computer Science', 'College of Computing Education', 164, 220, 'Emphasizes algorithms, software engineering, data science, and intelligent systems.'],
            ['BSIS', 'Bachelor of Science in Information Systems', 'College of Computing Education', 158, 200, 'Prepares students to bridge business processes and information technology solutions.'],
            ['BSBA-MM', 'Bachelor of Science in Business Administration major in Marketing Management', 'College of Business Administration', 156, 260, 'Develops competencies in consumer behavior, digital marketing, and strategic brand management.'],
            ['BSBA-FM', 'Bachelor of Science in Business Administration major in Financial Management', 'College of Business Administration', 156, 240, 'Covers investments, risk management, banking operations, and corporate finance.'],
            ['BSA', 'Bachelor of Science in Accountancy', 'College of Accountancy Education', 170, 180, 'Comprehensive accounting program covering auditing, taxation, and financial reporting standards.'],
            ['BSCRIM', 'Bachelor of Science in Criminology', 'College of Criminal Justice Education', 162, 260, 'Includes criminal law, forensic sciences, criminological research, and law enforcement administration.'],
            ['BEED', 'Bachelor of Elementary Education', 'College of Teacher Education', 152, 210, 'Professional preparation for elementary teaching with focus on pedagogy and child development.'],
            ['BSED-ENG', 'Bachelor of Secondary Education major in English', 'College of Teacher Education', 152, 190, 'Specialized secondary teacher training focused on English language and literature instruction.'],
            ['BSHM', 'Bachelor of Science in Hospitality Management', 'College of Hospitality Education', 158, 230, 'Covers hotel operations, food and beverage management, tourism, and service excellence.'],
            ['BSN', 'Bachelor of Science in Nursing', 'College of Nursing Education', 172, 200, 'Builds clinical competencies for patient care, community health, and professional nursing practice.'],
            ['BSMT', 'Bachelor of Science in Medical Technology', 'College of Allied Health Education', 176, 180, 'Covers diagnostic laboratory procedures, clinical microscopy, and biomedical instrumentation.'],
            ['BSPSY', 'Bachelor of Science in Psychology', 'College of Arts and Sciences', 155, 220, 'Focuses on human behavior, psychological assessment, counseling foundations, and research methods.'],
            ['ABCOMM', 'Bachelor of Arts in Communication', 'College of Arts and Sciences', 150, 210, 'Develops skills in media writing, public speaking, digital content production, and communication theory.'],
            ['BSEE', 'Bachelor of Science in Electrical Engineering', 'College of Engineering Education', 170, 170, 'Emphasizes power systems, electronics, control engineering, and electrical design standards.'],
            ['BSCE', 'Bachelor of Science in Civil Engineering', 'College of Engineering Education', 170, 190, 'Covers structural analysis, transportation, geotechnical engineering, and construction management.'],
            ['BSME', 'Bachelor of Science in Mechanical Engineering', 'College of Engineering Education', 170, 180, 'Includes thermodynamics, machine design, manufacturing processes, and mechanical systems integration.'],
            ['BSAIS', 'Bachelor of Science in Accounting Information Systems', 'College of Accountancy Education', 165, 190, 'Integrates accounting practice with enterprise systems, analytics, and information security controls.'],
            ['BSTM', 'Bachelor of Science in Tourism Management', 'College of Hospitality Education', 156, 230, 'Prepares students for travel operations, destination planning, and sustainable tourism development.'],
            ['BSED-MATH', 'Bachelor of Secondary Education major in Mathematics', 'College of Teacher Education', 152, 190, 'Prepares future math educators through content mastery, pedagogy, and classroom assessment.'],
            ['BPOLSCI', 'Bachelor of Arts in Political Science', 'College of Arts and Sciences', 150, 170, 'Explores political institutions, governance, public policy, and comparative political systems.'],
            ['BSOA', 'Bachelor of Science in Office Administration', 'College of Business Administration', 150, 220, 'Trains students in office systems, records management, business communication, and administrative supervision.'],
        ];

        foreach ($courseData as [$code, $name, $department, $units, $capacity, $description]) {
            Course::create([
                'course_code' => $code,
                'course_name' => $name,
                'department' => $department,
                'description' => $description,
                'units' => $units,
                'capacity' => $capacity,
            ]);
        }
    }
}
