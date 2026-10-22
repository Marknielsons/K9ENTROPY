<?php

declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if (str_starts_with($path, '/api/')) {
    require __DIR__ . '/api.php';
    return true;
}
if ($path === '/') {
    require __DIR__ . '/index.php';
    return true;
}
$full = realpath(__DIR__ . $path);
if ($full !== false && str_starts_with($full, __DIR__ . DIRECTORY_SEPARATOR) && is_file($full)) {
    return false;
}
http_response_code(404);
header('Content-Type: text/plain; charset=utf-8');
echo 'Not found';
return true;
