<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function base_url(string $path = ''): string
{
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $base = '/facturation';
    $pos = strpos($scriptName, '/facturation');
    if ($pos !== false) {
        $base = substr($scriptName, 0, $pos) . '/facturation';
    }

    if ($path === '') {
        return $base;
    }

    return $base . '/' . ltrim($path, '/');
}

function redirect_to(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

function read_json_file(string $filePath): array
{
    if (!file_exists($filePath)) {
        return [];
    }

    $content = file_get_contents($filePath);
    if ($content === false || trim($content) === '') {
        return [];
    }

    $decoded = json_decode($content, true);
    return is_array($decoded) ? $decoded : [];
}

function write_json_file(string $filePath, array $data): bool
{
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }

    return file_put_contents($filePath, $json) !== false;
}

function post_string(string $key): string
{
    $value = $_POST[$key] ?? '';
    return trim((string) $value);
}

function post_float(string $key): float
{
    $value = str_replace(',', '.', post_string($key));
    return is_numeric($value) ? (float) $value : 0.0;
}

function post_int(string $key): int
{
    return (int) post_string($key);
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return is_array($flash) ? $flash : null;
}

