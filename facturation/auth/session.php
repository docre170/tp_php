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
    return $user !== null && ($user['role'] ?? '') === $role;
}

function require_role(string $role): void
{
    require_login();
    if (!has_role($role)) {
        set_flash('error', 'Acces refuse.');
        redirect_to('index.php');
    }
}

