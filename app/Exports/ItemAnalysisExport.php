<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ItemAnalysisExport implements FromCollection, WithTitle, WithEvents
{
    protected $assessment;
    protected $itemAnalysis;
    protected $students;
    protected $sections;
    protected $totalExaminees;

    public function __construct($assessment, $itemAnalysis, $students, $sections, $totalExaminees)
    {
        $this->assessment = $assessment;
        $this->itemAnalysis = $itemAnalysis;
        $this->students = $students;
        $this->sections = $sections;
        $this->totalExaminees = $totalExaminees;
    }

    /**
     * Return the collection of data
     */
    public function collection()
    {
        $data = collect();
        
        // Add quiz information section
        $data->push(['QUIZ INFORMATION']);
        $data->push(['Quiz Name:', $this->assessment->title ?? 'N/A']);
        $data->push(['Section:', implode(', ', $this->sections)]);
        $data->push(['Difficulty:', $this->assessment->difficulty ?? 'N/A']);
        $data->push(['Learning Competency:', $this->assessment->category ?? 'N/A']);
        $data->push(['Number of Examinees:', $this->totalExaminees]);
        
        // Add table headers
        $headers = ['Item', 'Question'];
        foreach ($this->students as $student) {
            $headers[] = $student['name'];
        }
        $headers[] = 'No. of Correct';
        $headers[] = '% Correct';
        $headers[] = 'Remarks';
        $data->push($headers);
        
        // Add item analysis data
        foreach ($this->itemAnalysis as $item) {
            $row = [
                $item['item_number'],
                $item['question']
            ];
            
            // Add student responses (check/cross or dash)
            foreach ($this->students as $student) {
                $response = $item['student_responses'][$student['id']] ?? null;
                if ($response) {
                    $row[] = $response['is_correct'] ? '✓' : '✗';
                } else {
                    $row[] = '-';
                }
            }
            
            // Add statistics
            $row[] = $item['correct_responses'];
            $row[] = number_format($item['percentage'], 1) . '%';
            
            // Add remark
            $percentage = $item['percentage'];
            if ($percentage >= 90) {
                $remark = 'High Mastery';
            } elseif ($percentage >= 70) {
                $remark = 'Moderately Average Mastery';
            } elseif ($percentage >= 50) {
                $remark = 'Average Mastery';
            } elseif ($percentage >= 25) {
                $remark = 'Low Mastery';
            } else {
                $remark = 'No Mastery';
            }
            $row[] = $remark;
            
            $data->push($row);
        }
        
        return $data;
    }

    /**
     * Get column letter from index (0 = A, 1 = B, etc.)
     */
    private function getColumnLetter($index)
    {
        if ($index < 26) {
            return chr(65 + $index);
        } else {
            $first = chr(64 + floor($index / 26));
            $second = chr(65 + ($index % 26));
            return $first . $second;
        }
    }

    /**
     * Get the last column letter
     */
    private function getLastColumn()
    {
        // Calculate: Item + Question + Students + 3 stats columns
        $totalCols = 2 + count($this->students) + 3;
        return $this->getColumnLetter($totalCols - 1);
    }

    /**
     * Register events for the export
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Header is at row 7 now (from collection)
                $headerRow = 7;
                $firstDataRow = $headerRow + 1;  // Row 8 is first data row
                $lastColumn = $this->getLastColumn();
                $studentCount = count($this->students);
                $statsStartColIndex = 2 + $studentCount;
                $statsStartCol = $this->getColumnLetter($statsStartColIndex);
                $lastRow = $headerRow + count($this->itemAnalysis);
                
                // FIRST: Apply header styles to row 7 BEFORE any other styling
                $headerRange = 'A' . $headerRow . ':' . $lastColumn . $headerRow;
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '2563EB']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);
                
                // Style for quiz information section (row 1)
                $sheet->mergeCells('A1:' . $lastColumn . '1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => '1F2937']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'DBEAFE']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);
                
                // Style quiz info labels (rows 2-6, column A)
                $sheet->getStyle('A2:A6')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F3F4F6']
                    ]
                ]);
                
                // Set row height for header
                $sheet->getRowDimension($headerRow)->setRowHeight(40);
                
                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(8);   // Item
                $sheet->getColumnDimension('B')->setWidth(50);  // Question
                
                // Set width for student columns
                for ($i = 0; $i < $studentCount; $i++) {
                    $col = $this->getColumnLetter(2 + $i);
                    $sheet->getColumnDimension($col)->setWidth(15);
                }
                
                // Set width for statistics columns
                $sheet->getColumnDimension($statsStartCol)->setWidth(15);
                $sheet->getColumnDimension($this->getColumnLetter($statsStartColIndex + 1))->setWidth(12);
                $sheet->getColumnDimension($lastColumn)->setWidth(25);
                
                // NOW apply data row styling starting from row 9
                $sheet->getStyle('A' . $firstDataRow . ':' . $lastColumn . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E5E7EB']
                        ]
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);
                
                // Style for Item column (center align)
                $sheet->getStyle('A' . $firstDataRow . ':A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Style for Question column (left align)
                $sheet->getStyle('B' . $firstDataRow . ':B' . $lastRow)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ]
                ]);
                
                // Highlight statistics columns (No. of Correct, % Correct, Remarks) - DATA ROWS ONLY
                $sheet->getStyle($statsStartCol . $firstDataRow . ':' . $lastColumn . $lastRow)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'DBEAFE']
                    ],
                    'font' => ['bold' => true]
                ]);
            },
        ];
    }

    /**
     * Set the sheet title
     */
    public function title(): string
    {
        return 'Item Analysis';
    }
}
