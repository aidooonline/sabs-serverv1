<?php
/**
 * SABS Rescue Portal - Google OAuth2 Callback
 */
require_once 'config.php';
session_start();

if (!($_SESSION['rescue_auth'] ?? false)) {
    die("Unauthorized");
}

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'code' => $code,
        'client_id' => GD_CLIENT_ID,
        'client_secret' => GD_CLIENT_SECRET,
        'redirect_uri' => GD_REDIRECT_URI,
        'grant_type' => 'authorization_code',
        'access_type' => 'offline' // CRITICAL: This gives us the refresh_token
    ]));

    $response = curl_exec($ch);
    $data = json_decode($response, true);
    curl_close($ch);

    if (isset($data['access_token'])) {
        $tokenData = [
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? null,
            'expires_at' => time() + $data['expires_in']
        ];
        
        // If it's a reconnect, we might not get a new refresh_token, so keep the old one
        if (!isset($data['refresh_token']) && file_exists(__DIR__ . '/google_token.json')) {
            $oldToken = json_decode(file_get_contents(__DIR__ . '/google_token.json'), true);
            $tokenData['refresh_token'] = $oldToken['refresh_token'];
        }

        file_put_contents(__DIR__ . '/google_token.json', json_encode($tokenData));
        header('Location: index.php?linked=1');
        exit;
    } else {
        die("Error obtaining access token: " . print_r($data, true));
    }
} else {
    // Start OAuth Flow
    $params = [
        'client_id' => GD_CLIENT_ID,
        'redirect_uri' => GD_REDIRECT_URI,
        'response_type' => 'code',
        'scope' => 'https://www.googleapis.com/auth/drive.file',
        'access_type' => 'offline',
        'prompt' => 'consent'
    ];
    $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    header('Location: ' . $url);
    exit;
}
