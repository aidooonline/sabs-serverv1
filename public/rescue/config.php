<?php
/**
 * Rescue Portal - Standalone Configuration
 * This file is independent of Laravel's config system.
 */

// Load basic DB credentials from main .env if possible, otherwise use fallbacks
function getRescueEnv($key, $default = null) {
    $envPath = __DIR__ . '/../../.env';
    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            list($name, $value) = explode('=', $line, 2);
            if (trim($name) == $key) {
                return trim($value, '"\' ');
            }
        }
    }
    return getenv($key) ?: $default;
}

// 1. Database Connection
define('DB_HOST', getRescueEnv('DB_HOST', 'localhost'));
define('DB_NAME', getRescueEnv('DB_DATABASE', 'sabs_db'));
define('DB_USER', getRescueEnv('DB_USERNAME', 'root'));
define('DB_PASS', getRescueEnv('DB_PASSWORD', ''));

// 2. Rescue Portal Credentials (Hardcoded for emergency fallback)
// Change this immediately upon deployment!
define('RESCUE_MASTER_PASSWORD', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); // Default is 'password'

// 3. Google Drive API Credentials
define('GD_CLIENT_ID', getRescueEnv('GOOGLE_DRIVE_CLIENT_ID', ''));
define('GD_CLIENT_SECRET', getRescueEnv('GOOGLE_DRIVE_CLIENT_SECRET', ''));
define('GD_REDIRECT_URI', 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/rescue/oauth_callback.php');

// 4. Backup Storage
define('BACKUP_DIR', __DIR__ . '/backups');
if (!is_dir(BACKUP_DIR)) {
    mkdir(BACKUP_DIR, 0777, true);
    // Protect directory from public listing
    file_put_contents(BACKUP_DIR . '/.htaccess', "Deny from all");
} elseif (!file_exists(BACKUP_DIR . '/.htaccess')) {
    file_put_contents(BACKUP_DIR . '/.htaccess', "Deny from all");
}
