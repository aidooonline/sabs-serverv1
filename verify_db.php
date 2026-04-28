<?php
/**
 * SABS Database & Environment Verification
 */
require 'public/rescue/config.php';

header('Content-Type: text/plain');
echo "SABS PRE-FLIGHT CHECK\n";
echo "=====================\n\n";

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "[OK] Database Connection Successful.\n";

    $tables = [
        'nobs_user_account_numbers',
        'nobs_registration',
        'nobs_transactions',
        'users',
        'accounts',
        'sms_logs'
    ];

    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "[OK] Table '$table' exists. Row count: $count\n";
        } else {
            echo "[MISSING] Table '$table' DOES NOT EXIST!\n";
        }
    }

    echo "\nPHP Version: " . PHP_VERSION . "\n";

} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}
