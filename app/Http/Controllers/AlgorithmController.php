<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlgorithmController extends Controller
{
    public function runBKT()
    {
        $python = 'python'; // or 'python3' depending on your system
        $scriptPath = public_path('algorithm/bkt.py');

        // Run the script and capture output
        $output = shell_exec("$python $scriptPath 2>&1");

        return response()->json([
            'output' => $output
        ]);
    }
}
