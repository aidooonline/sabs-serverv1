<?php
/**
 * SABS Rescue Portal - Standalone Login & Dashboard
 */
require_once 'config.php';
session_start();

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Handle Login
$error = '';
if (isset($_POST['password'])) {
    if (password_verify($_POST['password'], RESCUE_MASTER_PASSWORD)) {
        $_SESSION['rescue_auth'] = true;
        $_SESSION['rescue_token'] = bin2hex(random_bytes(32)); // Standalone CSRF Protection
    } else {
        $error = 'Invalid Master Password';
    }
}

$isAuthenticated = $_SESSION['rescue_auth'] ?? false;
$rescueToken = $_SESSION['rescue_token'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SABS Rescue Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .rescue-container { max-width: 900px; margin: 50px auto; }
        .login-box { max-width: 400px; margin: 100px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .card { border: none; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .status-dot { height: 10px; width: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        .dot-online { background: #28a745; }
        .dot-offline { background: #dc3545; }
        .progress { height: 25px; border-radius: 5px; }
    </style>
</head>
<body>

<div class="container rescue-container">
    <?php if (!$isAuthenticated): ?>
        <div class="login-box">
            <h3 class="text-center mb-4">Rescue Portal</h3>
            <p class="text-muted text-center small">Emergency Database Management System</p>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Master Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter rescue password" required>
                </div>
                <button type="submit" class="btn btn-danger w-100">Authenticate Access</button>
            </form>
        </div>
    <?php else: ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>🛡️ SABS Rescue Dashboard</h2>
            <a href="?logout=1" class="btn btn-outline-secondary btn-sm">Secure Logout</a>
        </div>

        <div class="row">
            <!-- System Health -->
            <div class="col-md-4">
                <div class="card p-3">
                    <h5>System Status</h5>
                    <hr>
                    <div class="mb-2">
                        <span class="status-dot dot-online"></span> Core Portal: Active
                    </div>
                    <div class="mb-2">
                        <?php
                        $dbStatus = 'offline';
                        try {
                            $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
                            $dbStatus = 'online';
                        } catch (Exception $e) {}
                        ?>
                        <span class="status-dot dot-<?php echo $dbStatus; ?>"></span> Database: <?php echo ucfirst($dbStatus); ?>
                    </div>
                    <div class="mb-2">
                        <?php
                        $isLinked = file_exists(__DIR__ . '/google_token.json');
                        ?>
                        <span class="status-dot dot-<?php echo $isLinked ? 'online' : 'offline'; ?>"></span> Google Drive: <?php echo $isLinked ? 'Linked' : 'Unlinked'; ?>
                    </div>
                </div>
            </div>

            <!-- Backup Control -->
            <div class="col-md-8">
                <div class="card p-4">
                    <h5>🚀 High-Integrity Backup Engine</h5>
                    <p class="text-muted small">Creates a chunked, non-blocking SQL dump of all tables.</p>
                    
                    <div id="backup-ui">
                        <button id="start-backup" class="btn btn-primary">Start New Database Backup</button>
                    </div>

                    <div id="progress-container" class="mt-4 d-none">
                        <h6 id="current-task">Preparing engine...</h6>
                        <div class="progress mb-2">
                            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                        </div>
                        <p id="progress-stats" class="small text-muted">Tables: 0/0 | Rows: 0</p>
                    </div>
                    
                    <div id="log-window" class="mt-3 p-2 bg-dark text-success small overflow-auto d-none" style="height: 150px; border-radius: 5px; font-family: monospace;">
                        > Initializing rescue stream...
                    </div>
                </div>

                <!-- Google Drive Sync -->
                <div class="card p-4">
                    <h5>☁️ Cloud Vault (Google Drive)</h5>
                    <?php if (!$isLinked): ?>
                        <div id="cloud-status" class="alert alert-warning py-2 small">
                            Google Drive API not configured. <a href="oauth_callback.php" class="btn btn-sm btn-outline-dark ms-2">Link Google Account</a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success py-2 small">
                            Cloud link established.
                        </div>
                        <button class="btn btn-outline-success d-none" id="upload-now">🚀 Upload Latest Backup to Drive</button>
                    <?php endif; ?>
                </div>

                <!-- Restore Engine -->
                <div class="card p-4">
                    <h5>🛠️ Critical Recovery (Restore)</h5>
                    <p class="text-muted small">Restore the database from a local backup file. <b>Warning: This wipes existing data.</b></p>
                    
                    <div id="local-backups-list">
                        <?php
                        $files = glob(BACKUP_DIR . '/*.zip');
                        if (empty($files)): ?>
                            <p class="small text-muted italic">No local backups found.</p>
                        <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($files as $file): 
                                    $base = basename($file);
                                ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 small">
                                        <?php echo $base; ?>
                                        <button class="btn btn-sm btn-outline-danger btn-restore" data-file="<?php echo $base; ?>">Restore</button>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let tables = [];
    let currentTableIndex = 0;
    let backupFilename = '';
    let totalRowsProcessed = 0;

    // --- BACKUP LOGIC ---
    $('#start-backup').click(async function() {
        if(!confirm('This will perform a full database scan. Start?')) return;
        
        $(this).prop('disabled', true).text('Backup in Progress...');
        $('#progress-container, #log-window').removeClass('d-none');
        addLog('Requesting table list from server...');
        
        try {
            const res = await api('backup_engine.php', 'get_tables');
            tables = res.tables;
            addLog('Found ' + tables.length + ' tables to process.');
            updateProgress(0, 'Initializing backup file...');

            const initRes = await api('backup_engine.php', 'init_file');
            backupFilename = initRes.filename;
            addLog('Storage file initialized: ' + backupFilename);

            for(let i = 0; i < tables.length; i++) {
                currentTableIndex = i;
                const tableName = tables[i];
                const percent = Math.round((i / tables.length) * 100);
                updateProgress(percent, 'Processing structure: ' + tableName);
                
                await api('backup_engine.php', 'dump_schema', { table: tableName, filename: backupFilename });
                addLog('Structure saved: ' + tableName);

                let offset = 0;
                let hasMore = true;
                while(hasMore) {
                    updateProgress(percent, 'Processing data: ' + tableName + ' (Row ' + offset + '+)');
                    const chunkRes = await api('backup_engine.php', 'dump_chunk', { 
                        table: tableName, 
                        filename: backupFilename, 
                        offset: offset, 
                        limit: 5000 
                    });
                    
                    totalRowsProcessed += chunkRes.rows_count;
                    offset = chunkRes.next_offset;
                    hasMore = (chunkRes.rows_count === 5000); 
                    $('#progress-stats').text('Tables: ' + (i+1) + '/' + tables.length + ' | Rows: ' + totalRowsProcessed);
                }
            }

            updateProgress(95, 'Compressing backup...');
            const finalRes = await api('backup_engine.php', 'finalize', { filename: backupFilename });
            backupFilename = finalRes.zip_filename || finalRes.raw_sql;
            
            updateProgress(100, 'Backup Complete!');
            addLog('Archive Created: ' + backupFilename);
            
            $('#backup-ui').html('<div class="alert alert-success">Backup Ready: <b>' + backupFilename + '</b></div><a href="backups/' + backupFilename + '" class="btn btn-success me-2">Download Backup</a>');
            $('#upload-now').removeClass('d-none');

        } catch (err) {
            addLog('<span class="text-danger">ERROR: ' + err + '</span>');
            alert('Backup failed: ' + err);
        }
    });

    // --- DRIVE UPLOAD LOGIC ---
    $('#upload-now').click(async function() {
        $(this).prop('disabled', true).text('Uploading to Drive...');
        addLog('Initiating cloud upload for: ' + backupFilename);
        
        try {
            const res = await api('google_drive_engine.php', 'upload_to_drive', { filename: backupFilename });
            addLog('<span class="text-info">' + res.message + '</span>');
            $(this).text('Cloud Sync Finished').addClass('btn-success').removeClass('btn-outline-success');
        } catch (err) {
            addLog('<span class="text-danger">Cloud Error: ' + err + '</span>');
            $(this).prop('disabled', false).text('Retry Upload');
        }
    });

    // --- RESTORE LOGIC ---
    $('.btn-restore').click(async function() {
        const file = $(this).data('file');
        if(!confirm('DANGER: This will wipe your database and restore from ' + file + '. Proceed?')) return;

        $(this).prop('disabled', true).text('Restoring...');
        $('#progress-container, #log-window').removeClass('d-none');
        updateProgress(0, 'Preparing restoration stream...');
        addLog('Initializing restoration for: ' + file);

        try {
            // 1. Unzip
            addLog('Unzipping archive...');
            const unzipRes = await api('restore_engine.php', 'unzip_backup', { filename: file });
            const sqlFile = unzipRes.sql_file;

            // 2. Stream SQL
            let pointer = 0;
            let isFinished = false;
            let totalQueries = 0;

            while(!isFinished) {
                const streamRes = await api('restore_engine.php', 'stream_restore', { sql_file: sqlFile, pointer: pointer });
                pointer = streamRes.next_pointer;
                isFinished = streamRes.is_finished;
                totalQueries += streamRes.queries_executed;
                
                updateProgress(50, 'Executing queries... (' + totalQueries + ' run)');
                $('#progress-stats').text('Queries Executed: ' + totalQueries);
            }

            updateProgress(100, 'Restoration Complete!');
            addLog('Database restored successfully. Total queries: ' + totalQueries);
            alert('Restoration Complete! Please refresh the portal.');
            location.reload();

        } catch (err) {
            addLog('<span class="text-danger">RESTORE ERROR: ' + err + '</span>');
            alert('Restoration failed: ' + err);
        }
    });

    async function api(endpoint, action, data = {}, retries = 3) {
        data.action = action;
        data.token = '<?php echo $rescueToken; ?>'; // Inject standalone security token
        return new Promise((resolve, reject) => {
            const attempt = (remaining) => {
                $.ajax({
                    url: endpoint,
                    method: 'POST',
                    data: data,
                    success: function(res) {
                        if(res.status === 'success') resolve(res);
                        else reject(res.message);
                    },
                    error: function() { 
                        if (remaining > 0) {
                            addLog('<span class="text-warning">Network glitch. Retrying... (' + remaining + ' left)</span>');
                            setTimeout(() => attempt(remaining - 1), 2000);
                        } else {
                            reject('Network or Server Error after multiple attempts.'); 
                        }
                    }
                });
            };
            attempt(retries);
        });
    }

    function updateProgress(percent, task) {
        $('#progress-bar').css('width', percent + '%').text(percent + '%');
        $('#current-task').text(task);
    }

    function addLog(msg) {
        const log = $('#log-window');
        log.append('<div>> ' + msg + '</div>');
        log.scrollTop(log[0].scrollHeight);
    }
</script>

</body>
</html>
