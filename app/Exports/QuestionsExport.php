<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuestionsExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $questions;

    public function __construct($questions)
    {
        $this->questions = $questions;
    }

    public function collection()
    {
        return collect($this->questions)->map(function ($q) {
            return [
                $q['question_id'] ?? 'N/A',
                $q['competency'] ?? 'N/A',
                $q['difficulty_level'] ?? 'N/A',
                $q['question_type'] ?? 'N/A',
                $q['blooms_level'] ?? 'N/A',
                $q['school_year'] ?? 'N/A',
                $q['topic_tag'] ?? 'N/A',
                $q['question_text'] ?? 'N/A',
                $q['correct_answer'] ?? 'N/A',
                $q['question_source'] ?? 'N/A',
                $q['base_points'] ?? 'N/A',
                $q['max_allowed_time'] ?? 'N/A',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Question ID',
            'Category',
            'Difficulty',
            'Type',
            'Bloom\'s Level',
            'School Year',
            'Topic Tag',
            'Question Text',
            'Correct Answer',
            'Source',
            'Base Points',
            'Max Time',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
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

    public function columnWidths(): array
    {
        return [
            'A' => 14,
            'B' => 18,
            'C' => 12,
            'D' => 16,
            'E' => 14,
            'F' => 12,
            'G' => 20,
            'H' => 60,
            'I' => 20,
            'J' => 10,
            'K' => 12,
            'L' => 12,
        ];
    }

    public function title(): string
    {
        return 'Questions';
    }
}
