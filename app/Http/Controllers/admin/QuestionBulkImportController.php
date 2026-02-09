<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionBulkImportController extends Controller
{
    public function show(string $type)
    {
        if (!in_array($type, ['csv', 'excel', 'tiff'], true)) {
            abort(404);
        }

        return view('admin.admin.management.questions-bulk-import', [
            'type' => $type,
        ]);
    }

    public function upload(Request $request, string $type)
    {
        if (!in_array($type, ['csv', 'excel', 'tiff'], true)) {
            abort(404);
        }

        if ($type === 'tiff') {
            return back()->withErrors(['file' => 'TIFF bulk import is not supported. Use CSV or Excel.']);
        }

        $request->validate([
            'file' => 'required|file',
        ]);

        $file = $request->file('file');

        $sheets = \Maatwebsite\Excel\Facades\Excel::toArray(new \App\Exports\QuestionsImport(), $file);
        $rows = $sheets[0] ?? [];

        if (empty($rows)) {
            return back()->withErrors(['file' => 'No rows found. Make sure the file has a header row and at least one data row.']);
        }

        \Log::info('Bulk import first row (raw)', [
            'row' => $rows[0] ?? null,
            'keys' => is_array($rows[0] ?? null) ? array_keys($rows[0]) : null,
        ]);
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $data = $this->coerceTypes($this->normalizeRow($row));

            $validator = Validator::make($data, [
                'question_id' => 'required|string|max:50',
                'competency' => 'required|in:number_algebra,measurement_geometry,data_probability',
                'difficulty_level' => 'required|in:beginner,intermediate,advanced',
                'question_type' => 'required|in:multiple_choice,fill_blanks,true_false,drag_drop,connect_dots',
                'topic_tag' => 'required|string|max:100',
                'question_text' => 'required|string',
                'correct_answer' => 'required',
                'max_allowed_time' => 'required|integer|min:5',
                'base_points' => 'required|integer|min:1',
                'blooms_level' => 'nullable|string',
                'school_year' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $skipped++;
                $rowErrors = $validator->errors()->all();
                $errors[] = 'Row ' . ($index + 2) . ': ' . implode('; ', $rowErrors);
                \Log::warning('Bulk import row validation failed', [
                    'row' => $index + 2,
                    'data' => $data,
                    'errors' => $rowErrors,
                ]);
                continue;
            }

            $existing = Question::where('question_id', $data['question_id'])->first();

            $payload = array_merge($data, [
                'question_source' => $existing ? $existing->question_source : 'custom',
                'is_active' => $existing ? $existing->is_active : true,
            ]);

            Question::updateOrCreate(
                ['question_id' => $data['question_id']],
                $payload
            );

            if ($existing) {
                $updated++;
            } else {
                $created++;
            }
        }

        $message = "Bulk import complete. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}.";

        return redirect()
            ->route('admin.management.questions')
            ->with('success', $message)
            ->withErrors($errors);
    }

    private function normalizeRow(array $row): array
    {
        return [
            'question_id' => $row['question_id'] ?? $row['question id'] ?? null,
            'competency' => $row['category'] ?? $row['competency'] ?? null,
            'difficulty_level' => $row['difficulty'] ?? $row['difficulty_level'] ?? null,
            'question_type' => $row['type'] ?? $row['question_type'] ?? null,
            'blooms_level' => $row['blooms_level'] ?? $row['bloom\'s level'] ?? $row['blooms level'] ?? null,
            'school_year' => $row['school_year'] ?? $row['school year'] ?? null,
            'topic_tag' => $row['topic_tag'] ?? $row['topic tag'] ?? null,
            'question_text' => $row['question_text'] ?? $row['question text'] ?? null,
            'correct_answer' => $row['correct_answer'] ?? $row['correct answer'] ?? null,
            'base_points' => $row['base_points'] ?? $row['base points'] ?? null,
            'max_allowed_time' => $row['max_time'] ?? $row['max allowed time'] ?? $row['max_allowed_time'] ?? $row['max time'] ?? null,
        ];
    }

    private function coerceTypes(array $data): array
    {
        $stringFields = [
            'question_id',
            'competency',
            'difficulty_level',
            'question_type',
            'blooms_level',
            'school_year',
            'topic_tag',
            'question_text',
            'correct_answer',
        ];

        foreach ($stringFields as $field) {
            if (array_key_exists($field, $data) && $data[$field] !== null) {
                $data[$field] = trim((string) $data[$field]);
            }
        }

        if (isset($data['base_points']) && $data['base_points'] !== null && is_numeric($data['base_points'])) {
            $data['base_points'] = (int) $data['base_points'];
        }

        if (isset($data['max_allowed_time']) && $data['max_allowed_time'] !== null && is_numeric($data['max_allowed_time'])) {
            $data['max_allowed_time'] = (int) $data['max_allowed_time'];
        }

        return $data;
    }
}
