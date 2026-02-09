<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionsImport implements ToArray, WithHeadingRow
{
    public array $rows = [];

    public function array(array $array)
    {
        $this->rows = $array;
    }
}
