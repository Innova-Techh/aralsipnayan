<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SectionStudentsExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $studentsData;
    protected $sectionName;

    public function __construct($studentsData, $sectionName)
    {
        $this->studentsData = $studentsData;
        $this->sectionName = $sectionName;
    }

    /**
     * Return the collection of data
     */
    public function collection()
    {
        return collect($this->studentsData)->map(function($student) {
            // Format needs improvement
            $needsImprovement = implode(', ', $student['needs_improvement']);
            
            // Format competency details
            $compDetails = [];
            foreach ($student['competency_details'] as $comp) {
                $compDetails[] = [
                    'accuracy' => $comp['accuracy'],
                    'bkt_score' => $comp['bkt_score'],
                    'avg_response_time' => $comp['avg_response_time'],
                    'difficulty' => $comp['current_difficulty']
                ];
            }
            
            // Format struggling areas
            $strugglingAreasText = [];
            foreach ($student['struggling_areas'] as $area) {
                $strugglingAreasText[] = $area['topic'] . ': ' . $area['score'] . '% - ' . $area['attempts'] . ' attempts';
            }
            $strugglingAreasFormatted = empty($strugglingAreasText) ? 'None' : implode(' | ', $strugglingAreasText);
            
            return [
                $student['student_id'] ?? 'N/A',
                $student['name'],
                $student['email'],
                $student['section'],
                $student['section_rank'] ?? 'N/A',
                $student['total_points'],
                $student['overall_score'],
                $student['numerical_literacy_score'],
                $student['geometric_reasoning_score'],
                $student['problem_solving_score'],
                $student['diagnostic_count'] . '/3',
                $student['regular_count'],
                $student['best_streak'],
                $student['current_streak'],
                $student['time_spent'],
                $student['avg_time_per_question'],
                $student['last_assessment_name'],
                $student['last_assessment_score'],
                $student['last_assessment_date'] ?? 'N/A',
                // Needs Improvement
                $needsImprovement,
                // Competency Details (3 competencies x 4 fields each)
                $compDetails[0]['accuracy'] ?? 0,
                $compDetails[0]['bkt_score'] ?? 0,
                $compDetails[0]['avg_response_time'] ?? 0,
                $compDetails[0]['difficulty'] ?? 'Beginner',
                $compDetails[1]['accuracy'] ?? 0,
                $compDetails[1]['bkt_score'] ?? 0,
                $compDetails[1]['avg_response_time'] ?? 0,
                $compDetails[1]['difficulty'] ?? 'Beginner',
                $compDetails[2]['accuracy'] ?? 0,
                $compDetails[2]['bkt_score'] ?? 0,
                $compDetails[2]['avg_response_time'] ?? 0,
                $compDetails[2]['difficulty'] ?? 'Beginner',
                // Struggling Areas
                $strugglingAreasFormatted,
                $student['achievements_count']
            ];
        });
    }

    /**
     * Define the headings
     */
    public function headings(): array
    {
        return [
            'Student ID',
            'Name',
            'Email',
            'Section',
            'Rank',
            'Total Points',
            'Overall Score (%)',
            'Number & Algebra Score (%)',
            'Measurement & Geometry Score (%)',
            'Data & Probability Score (%)',
            'Diagnostics Completed',
            'Regular Assessments',
            'Best Streak',
            'Current Streak',
            'Time Spent (hours)',
            'Avg Time per Question (min)',
            'Last Assessment Name',
            'Last Assessment Score (%)',
            'Last Assessment Date',
            // Needs Improvement
            'Needs Improvement',
            // Competency Details
            'Number & Algebra - Accuracy (%)',
            'Number & Algebra - BKT Score (%)',
            'Number & Algebra - Avg Response Time (min)',
            'Number & Algebra - Difficulty Level',
            'Measurement & Geometry - Accuracy (%)',
            'Measurement & Geometry - BKT Score (%)',
            'Measurement & Geometry - Avg Response Time (min)',
            'Measurement & Geometry - Difficulty Level',
            'Data & Probability - Accuracy (%)',
            'Data & Probability - BKT Score (%)',
            'Data & Probability - Avg Response Time (min)',
            'Data & Probability - Difficulty Level',
            // Struggling Areas
            'Struggling Areas (Topic: Score% - Attempts)',
            'Achievements Count'
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (headers)
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4A90E2']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],
        ];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 12,  // Student ID
            'B' => 20,  // Name
            'C' => 25,  // Email
            'D' => 12,  // Section
            'E' => 8,   // Rank
            'F' => 12,  // Total Points
            'G' => 15,  // Overall Score
            'H' => 18,  // Number & Algebra Score
            'I' => 22,  // Measurement & Geometry Score
            'J' => 20,  // Data & Probability Score
            'K' => 18,  // Diagnostics Completed
            'L' => 18,  // Regular Assessments
            'M' => 12,  // Best Streak
            'N' => 14,  // Current Streak
            'O' => 16,  // Time Spent
            'P' => 20,  // Avg Time per Question
            'Q' => 25,  // Last Assessment Name
            'R' => 20,  // Last Assessment Score
            'S' => 18,  // Last Assessment Date
            'T' => 25,  // Needs Improvement
            'U' => 20,  // N&A Accuracy
            'V' => 20,  // N&A BKT Score
            'W' => 25,  // N&A Avg Response Time
            'X' => 22,  // N&A Difficulty
            'Y' => 20,  // M&G Accuracy
            'Z' => 20,  // M&G BKT Score
            'AA' => 25, // M&G Avg Response Time
            'AB' => 22, // M&G Difficulty
            'AC' => 20, // D&P Accuracy
            'AD' => 20, // D&P BKT Score
            'AE' => 25, // D&P Avg Response Time
            'AF' => 22, // D&P Difficulty
            'AG' => 50, // Struggling Areas
            'AH' => 18, // Achievements Count
        ];
    }

    /**
     * Set the worksheet title
     */
    public function title(): string
    {
        return substr($this->sectionName, 0, 31); // Excel sheet names max 31 chars
    }
}
