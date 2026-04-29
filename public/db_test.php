<?php
/**
 * SABS Database Diagnostic Tool
 * Upload this to /public/db_test.php and run in browser
 */

// Load the environment manually if not in a framework context
function get_env_var($key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        // Try reading .env file manually if getenv fails
        if (file_exists('../.env')) {
            $lines = file('../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                list($name, $val) = explode('=', $line, 2);
                if (trim($name) == $key) {
                    return trim($val, " \t\n\r\0\x0B\"'");
                }
            }
        }
        return $default;
    }
    return $value;
}

$host = get_env_var('DB_HOST', '127.0.0.1');
$db   = get_env_var('DB_DATABASE', 'not_set');
$user = get_env_var('DB_USERNAME', 'not_set');
$pass = get_env_var('DB_PASSWORD', '');
$port = get_env_var('DB_PORT', '3306');

echo "<h2>SABS Database Connection Test</h2>";
echo "<b>Attempting connection with:</b><br>";
echo "Host: $host<br>";
echo "Database: $db<br>";
echo "User: $user<br>";
echo "Port: $port<br><br>";

try {
    $dsn = "mysql:host=$host;dbname=$db;port=$port;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "<span style='color:green;'><b>SUCCESS:</b> Connection established!</span>";
    
} catch (\PDOException $e) {
    echo "<span style='color:red;'><b>CONNECTION FAILED:</b></span><br>";
    echo "Error Message: " . $e->getMessage() . "<br>";
    echo "Error Code: " . (int)$e->getCode() . "<br>";
}

echo "<hr><b>Suggestions:</b><br>";
echo "1. If the User above is 'websxpvw_nobs_backup001' but your .env says something else, your server is caching old config.<br>";
echo "2. Run <code>php artisan config:clear</code> on the server to fix caching.<br>";
echo "3. If the connection succeeded here but the App still fails, check if your .env file has hidden spaces or BOM characters.";
