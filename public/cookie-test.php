<?php
declare(strict_types=1);

// Laravel'den tamamen bağımsız saf PHP cookie testi

// Cookie ayarla
setcookie('php_test', 'value_' . time(), [
    'expires'  => time() + 3600,
    'path'     => '/',
    'secure'   => true,
    'httponly'  => true,
    'samesite' => 'Lax',
]);

// Manuel header ile cookie ayarla
header('Set-Cookie: manual_test=works_' . time() . '; Path=/; Secure; HttpOnly; SameSite=Lax', false);

// PHP'nin göndereceği tüm header'ları listele
$allHeaders = headers_list();

header('Content-Type: application/json');

echo json_encode([
    'test'              => 'Pure PHP cookie test (no Laravel)',
    'cookies_received'  => $_COOKIE,
    'headers_being_sent' => $allHeaders,
    'php_version'       => PHP_VERSION,
    'sapi'              => php_sapi_name(),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
