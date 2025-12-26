<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class TestRunnerController extends Controller
{
    public function index()
    {
        $rootPath = base_path('tests');
        $structure = $this->scanDirectory($rootPath, 'tests');
        return view('test-runner', compact('structure'));
    }

    public function runTest(Request $request)
    {
        $path = $request->input('path', 'tests');
        
        // Basic security check to prevent traversing outside project
        if (strpos($path, '..') !== false) {
            return response()->json(['output' => 'Security Error: Invalid path provided.']);
        }

        // CLEAR ROUTE CACHE to ensure new routes are recognized
        Artisan::call('route:clear');
        Artisan::call('config:clear');

        // Construct command
        // We use 'php artisan test' which is available in Laravel 7+
        // We append --no-ansi to avoid color codes cluttering the raw output, 
        // or we can keep them if we use a JS library to render ANSI. 
        // For now, plain text is safer.
        $command = "php artisan test " . escapeshellarg($path);
        
        // Run command from project root
        $cwd = base_path();
        
        $descriptorspec = [
           0 => ["pipe", "r"],  // stdin
           1 => ["pipe", "w"],  // stdout
           2 => ["pipe", "w"]   // stderr
        ];
        
        $process = proc_open($command, $descriptorspec, $pipes, $cwd);
        
        $output = "";
        
        if (is_resource($process)) {
            fclose($pipes[0]);
            
            $output .= stream_get_contents($pipes[1]);
            $output .= stream_get_contents($pipes[2]);
            
            fclose($pipes[1]);
            fclose($pipes[2]);
            
            proc_close($process);
        }

        return response()->json([
            'command' => $command,
            'output' => $output
        ]);
    }

    private function scanDirectory($dir, $relativePath)
    {
        $result = [];
        $files = scandir($dir);

        foreach ($files as $value) {
            if ($value === '.' || $value === '..') {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $value;
            $relative = $relativePath . '/' . $value;

            if (is_dir($path)) {
                $result[] = [
                    'name' => $value,
                    'path' => $relative,
                    'type' => 'folder',
                    'children' => $this->scanDirectory($path, $relative)
                ];
            } else {
                // Only include PHP files
                if (pathinfo($value, PATHINFO_EXTENSION) === 'php') {
                    $result[] = [
                        'name' => $value,
                        'path' => $relative,
                        'type' => 'file'
                    ];
                }
            }
        }
        return $result;
    }
}