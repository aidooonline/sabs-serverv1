<?php
/**
 * SABS Database & Environment Verification
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- EMERGENCY MANUAL CONFIG ---
// If you get "Access Denied", fill these in manually for this test:
$manual_host = 'localhost';
$manual_user = ''; 
$manual_pass = '';
$manual_db   = '';
// -------------------------------

function getRescueEnv($key, $default = null) {
    $envPath = __DIR__ . '/.env';
    if (!file_exists($envPath)) $envPath = __DIR__ . '/../.env'; // Check parent if in public
    
    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (!$line || strpos($line, '#') === 0) continue;
            $parts = explode('=', $line, 2);
            if (count($parts) < 2) continue;
            if (trim($parts[0]) == $key) {
                return trim($parts[1], " \"'\t\n\r\0\x0B");
            }
        }
    }
    return $default;
}

$host = $manual_host ?: getRescueEnv('DB_HOST', 'localhost');
$user = $manual_user ?: getRescueEnv('DB_USERNAME', 'root');
$pass = $manual_pass ?: getRescueEnv('DB_PASSWORD', '');
$db   = $manual_db   ?: getRescueEnv('DB_DATABASE', 'sabs_db');

header('Content-Type: text/plain');
echo "SABS PRE-FLIGHT CHECK\n";
echo "=====================\n\n";
echo "Attempting connection to: $db on $host as $user\n\n";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "[OK] Database Connection Successful.\n\n";

    $tables = [
        'nobs_user_account_numbers',
        'nobs_registration',
        'nobs_transactions',
        'users',
        'accounts',
        'sms_logs',
        'agent_pouch_ledger',
        'treasury_accounts'
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
    echo "OS: " . PHP_OS . "\n";

} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    echo "\nTIP: If you see 'Access Denied', open verify_db.php and fill in the \$manual_user, \$manual_pass, and \$manual_db variables at the top.\n";
}
