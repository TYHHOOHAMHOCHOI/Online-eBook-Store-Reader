<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

/** Load variables from .env without external packages. */
function load_env(string $path): void
{
    if (!is_file($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if ((str_starts_with($value, '"') && str_ends_with($value, '"'))
            || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }

        if ($key !== '') {
            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }
    }
}

function env(string $key, ?string $default = null): ?string
{
    $value = $_ENV[$key] ?? getenv($key);

    return $value === false ? $default : $value;
}

function config(string $file): array
{
    /** @var array $settings */
    $settings = require BASE_PATH . '/config/' . $file . '.php';

    return $settings;
}

function db(): PDO
{
    static $connection = null;

    if ($connection instanceof PDO) {
        return $connection;
    }

    $settings = config('database');
    $hostsToTry = array_unique([$settings['host'], 'db', '127.0.0.1', 'localhost']);
    $credsToTry = [
        ['user' => $settings['username'], 'pass' => $settings['password']],
        ['user' => 'ebook_user', 'pass' => 'ebook_password'],
        ['user' => 'root', 'pass' => 'root_password'],
        ['user' => 'root', 'pass' => ''],
    ];

    $lastException = null;

    foreach ($hostsToTry as $host) {
        foreach ($credsToTry as $cred) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                    $host,
                    $settings['port'],
                    $settings['database'],
                    $settings['charset']
                );

                $connection = new PDO($dsn, $cred['user'], $cred['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);

                return $connection;
            } catch (PDOException $e) {
                $lastException = $e;
            }
        }
    }

    throw $lastException ?? new PDOException('Could not connect to database');
}

load_env(BASE_PATH . '/.env');

