<?php
/**
 * SABS Backup Engine - Core AJAX Logic
 * Handles table listing, schema dumping, and data chunking.
 */
require_once 'config.php';
session_start();

// Security: Check Authentication
if (!($_SESSION['rescue_auth'] ?? false)) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

header('Content-Type: application/json');

function connect() {
    return new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
}

$action = $_POST['action'] ?? '';
$response = ['status' => 'error', 'message' => 'Invalid action'];

try {
    $pdo = connect();

    // 1. GET TABLE LIST
    if ($action === 'get_tables') {
        $stmt = $pdo->prepare("SHOW TABLES");
        $stmt->execute();
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $response = ['status' => 'success', 'tables' => $tables];
    }

    // 2. DUMP SCHEMA & INITIALIZE FILE
    elseif ($action === 'init_file') {
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = BACKUP_DIR . '/' . $filename;
        
        $sql = "-- SABS Database Backup\n-- Date: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
        
        file_put_contents($filepath, $sql);
        
        $response = ['status' => 'success', 'filename' => $filename];
    }

    // 3. GET TABLE SCHEMA
    elseif ($action === 'dump_schema') {
        $table = $_POST['table'] ?? '';
        $filename = $_POST['filename'] ?? '';
        $filepath = BACKUP_DIR . '/' . $filename;

        $stmt = $pdo->prepare("SHOW CREATE TABLE `$table`");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_NUM);
        
        $sql = "\n-- Structure for table `$table` --\n";
        $sql .= "DROP TABLE IF EXISTS `$table`;\n";
        $sql .= $row[1] . ";\n\n";
        
        file_put_contents($filepath, $sql, FILE_APPEND);
        
        $response = ['status' => 'success'];
    }

    // 4. DUMP DATA CHUNK (The most critical part)
    elseif ($action === 'dump_chunk') {
        $table = $_POST['table'] ?? '';
        $filename = $_POST['filename'] ?? '';
        $offset = (int)($_POST['offset'] ?? 0);
        $limit = (int)($_POST['limit'] ?? 1000);
        $filepath = BACKUP_DIR . '/' . $filename;

        // Fetch data
        $stmt = $pdo->prepare("SELECT * FROM `$table` LIMIT $offset, $limit");
        $stmt->execute();
        $rows = $stmt->fetchAll();

        if (count($rows) > 0) {
            $sql = "";
            foreach ($rows as $row) {
                $keys = array_keys($row);
                $values = array_values($row);
                
                // Sanitize values
                $escapedValues = array_map(function($v) use ($pdo) {
                    if ($v === null) return 'NULL';
                    return $pdo->quote($v);
                }, $values);

                $sql .= "INSERT INTO `$table` (`" . implode("`, `", $keys) . "`) VALUES (" . implode(", ", $escapedValues) . ");\n";
            }
            file_put_contents($filepath, $sql, FILE_APPEND);
        }

        $response = [
            'status' => 'success', 
            'rows_count' => count($rows),
            'next_offset' => $offset + $limit
        ];
    }

    // 5. FINALIZE (ZIP)
    elseif ($action === 'finalize') {
        $filename = $_POST['filename'] ?? '';
        $filepath = BACKUP_DIR . '/' . $filename;
        $zipPath = $filepath . '.zip';

        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
                $zip->addFile($filepath, $filename);
                $zip->close();
                unlink($filepath); // Remove raw SQL file
                $response = ['status' => 'success', 'zip_filename' => $filename . '.zip'];
            } else {
                $response = ['status' => 'error', 'message' => 'Failed to create Zip archive.'];
            }
        } else {
            $response = ['status' => 'success', 'message' => 'ZipArchive missing. Raw SQL preserved.', 'raw_sql' => $filename];
        }
    }

} catch (Exception $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
