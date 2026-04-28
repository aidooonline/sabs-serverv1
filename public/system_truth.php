<?php
/**
 * SABS System Truth - Complete Diagnostics
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>SABS System Truth</h1>";
echo "<strong>PHP Version:</strong> " . PHP_VERSION . "<br>";
echo "<strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "<br><br>";

// 1. Load DB Credentials manually from .env
$env = file_get_contents(__DIR__ . '/../.env');
preg_match('/DB_HOST=(.*)/', $env, $host);
preg_match('/DB_DATABASE=(.*)/', $env, $db);
preg_match('/DB_USERNAME=(.*)/', $env, $user);
preg_match('/DB_PASSWORD=(.*)/', $env, $pass);

$db_host = trim($host[1] ?? 'localhost');
$db_name = trim($db[1] ?? '');
$db_user = trim($user[1] ?? '');
$db_pass = trim($pass[1] ?? '');

echo "<strong>Attempting DB Connection...</strong><br>";
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "<span style='color:green'>[OK] Database Connected.</span><br><br>";

    $tables = ['nobs_user_account_numbers', 'nobs_registration', 'nobs_transactions'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "[OK] Table <strong>$table</strong> exists. ($count rows)<br>";
        } else {
            echo "<span style='color:red'>[MISSING] Table <strong>$table</strong> DOES NOT EXIST!</span><br>";
        }
    }

    // 2. Test the specific query logic for Dormancy
    echo "<br><strong>Testing Dormancy Query Logic...</strong><br>";
    $test = $pdo->query("SELECT id, account_number, account_status FROM nobs_user_account_numbers LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if ($test) {
        echo "[OK] Successfully read from nobs_user_account_numbers.<br>";
        print_r($test);
    }

} catch (Exception $e) {
    echo "<span style='color:red'>[ERROR] " . $e->getMessage() . "</span><br>";
}
