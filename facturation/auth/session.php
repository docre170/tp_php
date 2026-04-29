<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/helpers.php';

function current_user(): ?array
{
    if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
        return null;
    }
    return $_SESSION['user'];
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        set_flash('error', 'Veuillez vous connecter.');
        redirect_to('auth/login.php');
    }
}

function has_role(string $role): bool
{
    $user = current_user();
    if ($user === null) {
        return false;
    }

    $currentRole = normalize_role((string) ($user['role'] ?? ''));
    return $currentRole === normalize_role($role);
}

function require_role(string $role): void
{
    require_login();
    if (!has_role($role)) {
        set_flash('error', 'Acces refuse.');
        redirect_to('index.php');
    }
}

function normalize_role(string $role): string
{
    $map = [
        'admin' => 'super_admin',
        'super_administrateur' => 'super_admin',
        'super-admin' => 'super_admin',
        'manager' => 'manager',
        'caissier' => 'caissier',
        'client' => 'client',
    ];

    return $map[$role] ?? $role;
}

function has_any_role(array $roles): bool
{
    $user = current_user();
    if ($user === null) {
        return false;
    }

    $currentRole = normalize_role((string) ($user['role'] ?? ''));
    foreach ($roles as $role) {
        if ($currentRole === normalize_role((string) $role)) {
            return true;
        }
    }

    return false;
}

function require_any_role(array $roles): void
{
    require_login();
    if (!has_any_role($roles)) {
        set_flash('error', 'Acces refuse.');
        redirect_to('index.php');
    }
}

