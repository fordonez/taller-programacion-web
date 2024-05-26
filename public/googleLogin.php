<?php

require_once 'config.php';

use Google\Auth\CredentialsLoader;
global $db;

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST'
    || !isset($_POST['credential'], $_POST['g_csrf_token'])
    || trim($_POST['credential']) === ''
    || trim($_POST['g_csrf_token']) === ''
) {
    header('Location: /');
    exit();
}

$_credential = trim($_POST['credential']);
$_token = trim($_POST['g_csrf_token']);
$auth = CredentialsLoader::makeCredentials($_credential, [], $_token);

if (!$auth) {
    header('Location: /?error=invalid_credentials');
    exit();
}

$credentials =  base64_url_decode(explode('.', $_credential)[1]);

$userId = $credentials['sub'];
$name = $credentials['name'];
$email = $credentials['email'];

$user = $db->selectOne('users', ['google_id' => $userId]);
if ($user) {
    if (!$db->update('users', ['google_id' => $userId], [
        'google_name' => $name,
        'google_email' => $email
    ])) {
        header('Location: /?error=db_error');
        exit();
    }
} else {
    if (!$db->insert('users', [
        'google_id' => $userId,
        'google_name' => $name,
        'google_email' => $email
    ])) {
        header('Location: /?error=db_error');
        exit();
    }
}
header('Location: /success.php');
