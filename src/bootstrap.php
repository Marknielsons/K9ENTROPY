<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $prefix = 'K9Entropy\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    if (str_contains($relative, '..') || str_contains($relative, '\\')) {
        return;
    }
    $path = __DIR__ . '/' . $relative . '.php';
    if (is_file($path)) {
        require_once $path;
    }
});
