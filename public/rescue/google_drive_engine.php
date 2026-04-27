<?php
/**
 * SABS Rescue Portal - Google Drive Integration
 * Handles Resumable Uploads and OAuth2 via raw cURL with memory-safe streaming.
 */
require_once 'config.php';
session_start();

if (!($_SESSION['rescue_auth'] ?? false)) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

header('Content-Type: application/json');

$tokenPath = __DIR__ . '/google_token.json';

// Helper to Refresh Access Token
function getAccessToken() {
    global $tokenPath;
    if (!file_exists($tokenPath)) return null;

    $tokenData = json_decode(file_get_contents($tokenPath), true);
    if (!isset($tokenData['refresh_token'])) return null;

    // Check if expired
    if ($tokenData['expires_at'] > time()) {
        return $tokenData['access_token'];
    }

    // Refresh it
    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'client_id' => GD_CLIENT_ID,
        'client_secret' => GD_CLIENT_SECRET,
        'refresh_token' => $tokenData['refresh_token'],
        'grant_type' => 'refresh_token'
    ]));

    $response = curl_exec($ch);
    $data = json_decode($response, true);
    curl_close($ch);

    if (isset($data['access_token'])) {
        $tokenData['access_token'] = $data['access_token'];
        $tokenData['expires_at'] = time() + $data['expires_in'];
        file_put_contents($tokenPath, json_encode($tokenData));
        return $data['access_token'];
    }

    return null;
}

$action = $_POST['action'] ?? '';

try {
    // 1. UPLOAD TO DRIVE (Resumable & Streaming)
    if ($action === 'upload_to_drive') {
        $filename = $_POST['filename'] ?? '';
        $filepath = BACKUP_DIR . '/' . $filename;

        if (!file_exists($filepath)) throw new Exception("File not found locally.");

        $accessToken = getAccessToken();
        if (!$accessToken) throw new Exception("Google Drive not linked. Please authenticate.");

        // A. Initiate Resumable Upload
        $metadata = json_encode(['name' => $filename, 'description' => 'SABS Automated Backup']);
        
        $ch = curl_init('https://www.googleapis.com/upload/drive/v3/files?uploadType=resumable');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json; charset=UTF-8",
            "X-Upload-Content-Type: application/zip"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $metadata);
        curl_setopt($ch, CURLOPT_HEADER, true);
        
        $response = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $headerSize);
        curl_close($ch);

        preg_match('/location: (.*)/i', $headers, $matches);
        $uploadUrl = trim($matches[1] ?? '');

        if (!$uploadUrl) throw new Exception("Failed to get upload URL from Google.");

        // B. Perform actual upload using STREAMING (Memory Safe)
        $fileSize = filesize($filepath);
        $fileHandle = fopen($filepath, 'rb');
        
        $ch = curl_init($uploadUrl);
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_INFILE, $fileHandle);
        curl_setopt($ch, CURLOPT_INFILESIZE, $fileSize);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/zip"
        ]);
        
        $res = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fileHandle);

        if ($status == 200 || $status == 201) {
            echo json_encode(['status' => 'success', 'message' => 'Uploaded to Google Drive successfully.']);
        } else {
            throw new Exception("Google Upload Failed with Status $status: $res");
        }
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
