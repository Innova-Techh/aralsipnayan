<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class QuestionExportController extends Controller
{
    public function exportCsv(Request $request)
    {
        $questions = $this->getQuestionsData($request);
        $filename = 'questions_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($questions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $this->getHeadings());

            foreach ($questions as $q) {
                fputcsv($file, $this->mapRow($q));
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportExcel(Request $request)
    {
        $questions = $this->getQuestionsData($request);
        $filename = 'questions_' . date('Y-m-d') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\QuestionsExport($questions),
            $filename
        );
    }

    public function exportTiff(Request $request)
    {
        if (!extension_loaded('imagick') || !class_exists(\Imagick::class)) {
            return response(
                'TIFF export requires the Imagick PHP extension (php_imagick). Enable it and restart the server.',
                501,
                ['Content-Type' => 'text/plain']
            );
        }

        $questions = $this->getQuestionsData($request);
        $lines = $this->buildTiffLines($questions);
        $tiff = $this->renderTiffFromLines($lines);
        $filename = 'questions_' . date('Y-m-d') . '.tiff';

        return response($tiff, 200, [
            'Content-Type' => 'image/tiff',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function getQuestionsData(Request $request)
    {
        $category = $request->get('category');
        $difficulty = $request->get('difficulty');
        $type = $request->get('type');
        $search = $request->get('search');

        $dbQuery = Question::query();
        if ($category) {
            $dbQuery->where('competency', $category);
        }
        if ($difficulty) {
            $dbQuery->where('difficulty_level', $difficulty);
        }
        if ($type) {
            $dbQuery->where('question_type', $type);
        }
        $db = $dbQuery->get()->map(function ($q) {
            return $q->toArray();
        })->all();

        $combined = $db;

        if ($search) {
            $combined = array_values(array_filter($combined, function ($q) use ($search) {
                return stripos($q['question_text'] ?? '', $search) !== false ||
                       stripos($q['question_id'] ?? '', $search) !== false ||
                       stripos($q['topic_tag'] ?? '', $search) !== false;
            }));
        }

        return array_map(function ($q) {
            $q['blooms_level'] = $q['blooms_level'] ?? 'remember';
            $q['school_year'] = $q['school_year'] ?? '2024-2025';
            return $q;
        }, $combined);
    }

    private function getHeadings()
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

    private function mapRow($q)
    {
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
    }

    private function buildTiffLines($questions)
    {
        $lines = [];
        $lines[] = 'Questions Export';
        $lines[] = 'Exported: ' . date('Y-m-d H:i');
        $lines[] = str_repeat('-', 160);

        $widths = [12, 14, 10, 14, 10, 10, 16, 80];
        $headers = [
            'Question ID',
            'Category',
            'Difficulty',
            'Type',
            'Bloom',
            'Year',
            'Topic',
            'Question Text'
        ];

        $lines[] = $this->formatTiffRow($headers, $widths);
        $lines[] = str_repeat('-', 160);

        foreach ($questions as $q) {
            $lines[] = $this->formatTiffRow([
                $q['question_id'] ?? 'N/A',
                $q['competency'] ?? 'N/A',
                $q['difficulty_level'] ?? 'N/A',
                $q['question_type'] ?? 'N/A',
                $q['blooms_level'] ?? 'N/A',
                $q['school_year'] ?? 'N/A',
                $q['topic_tag'] ?? 'N/A',
                $q['question_text'] ?? 'N/A',
            ], $widths);
        }

        return $lines;
    }

    private function renderTiffFromLines($lines)
    {
        $width = 2200;
        $height = 2200;
        $margin = 40;
        $lineHeight = 18;
        $linesPerPage = (int) floor(($height - ($margin * 2)) / $lineHeight);

        $pages = array_chunk($lines, max(1, $linesPerPage));
        $tiff = new \Imagick();

        foreach ($pages as $pageLines) {
            $img = new \Imagick();
            $img->newImage($width, $height, new \ImagickPixel('white'));
            $img->setImageFormat('tiff');

            $draw = new \ImagickDraw();
            $draw->setFontSize(12);
            $draw->setFillColor('black');

            $y = $margin + $lineHeight;
            foreach ($pageLines as $line) {
                $img->annotateImage($draw, $margin, $y, 0, $line);
                $y += $lineHeight;
            }

            $tiff->addImage($img);
        }

        $tiff->setFormat('tiff');
        return $tiff->getImagesBlob();
    }

    private function formatTiffRow($columns, $widths)
    {
        $out = [];
        foreach ($columns as $i => $value) {
            $width = $widths[$i] ?? 10;
            $text = $this->truncateText((string) $value, $width);
            $out[] = str_pad($text, $width);
        }
        return implode(' ', $out);
    }

    private function truncateText($text, $width)
    {
        if (strlen($text) <= $width) {
            return $text;
        }
        if ($width <= 3) {
            return substr($text, 0, $width);
        }
        return substr($text, 0, $width - 3) . '...';
    }
}
