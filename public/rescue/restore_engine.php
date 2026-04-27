<?php
/**
 * SABS Restore Engine - Streaming SQL Execution
 * Reads large SQL files line-by-line to prevent memory exhaustion.
 */
require_once 'config.php';
session_start();

if (!($_SESSION['rescue_auth'] ?? false)) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

header('Content-Type: application/json');

function connect() {
    return new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET FOREIGN_KEY_CHECKS=0"
    ]);
}

$action = $_POST['action'] ?? '';

try {
    $pdo = connect();

    // 1. UNZIP BACKUP
    if ($action === 'unzip_backup') {
        $zipFile = $_POST['filename'] ?? '';
        $zipPath = BACKUP_DIR . '/' . $zipFile;
        
        if (!file_exists($zipPath)) throw new Exception("Zip file not found.");

        $zip = new ZipArchive;
        if ($zip->open($zipPath) === TRUE) {
            $sqlFile = str_replace('.zip', '', $zipFile);
            $zip->extractTo(BACKUP_DIR);
            $zip->close();
            echo json_encode(['status' => 'success', 'sql_file' => $sqlFile]);
        } else {
            throw new Exception("Failed to open zip archive.");
        }
    }

    // 2. STREAM SQL RESTORE (Chunked execution)
    elseif ($action === 'stream_restore') {
        $sqlFile = $_POST['sql_file'] ?? '';
        $pointer = (int)($_POST['pointer'] ?? 0);
        $filepath = BACKUP_DIR . '/' . $sqlFile;

        if (!file_exists($filepath)) throw new Exception("SQL file not found for restoration.");

        $handle = fopen($filepath, 'r');
        fseek($handle, $pointer);

        $queryCount = 0;
        $tempQuery = '';
        $maxQueriesPerRequest = 200; // Small batches to prevent timeouts

        while ($queryCount < $maxQueriesPerRequest && ($line = fgets($handle)) !== false) {
            $trimmedLine = trim($line);
            
            // Skip comments and empty lines
            if (empty($trimmedLine) || strpos($trimmedLine, '--') === 0 || strpos($trimmedLine, '/*') === 0) {
                continue;
            }

            $tempQuery .= $line;

            // If line ends with semicolon, it's a complete query
            if (substr($trimmedLine, -1) === ';') {
                try {
                    $pdo->exec($tempQuery);
                    $queryCount++;
                    $tempQuery = '';
                } catch (Exception $e) {
                    fclose($handle);
                    throw new Exception("SQL Error at byte " . ftell($handle) . ": " . $e->getMessage() . "\nQuery: " . $tempQuery);
                }
            }
        }

        $newPointer = ftell($handle);
        $isFinished = ($line === false);
        fclose($handle);

        if ($isFinished) {
            // Clean up: Optional, keeping it for now for safety
        }

        echo json_encode([
            'status' => 'success',
            'queries_executed' => $queryCount,
            'next_pointer' => $newPointer,
            'is_finished' => $isFinished
        ]);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
