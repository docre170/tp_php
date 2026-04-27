<?php

declare(strict_types=1);

require_once __DIR__ . '/../auth/session.php';

$user = current_user();
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME); ?></title>
    <link rel="stylesheet" href="<?= e(base_url('assets/css/style.css')); ?>">
</head>

<body>
    <header class="header">
        <div class="container header-content">
            <h1 class="logo">Supermarche - Facturation</h1>
            <nav class="nav">
                <ul>
                    <li>
                        <a class="btn btn-secondary" href="<?= e(base_url('index.php')); ?>">Tableau de bord</a>
                    </li>
                    <li>
                        <a class="btn btn-secondary" href="<?= e(base_url('modules/caisse.php')); ?>">Caisse</a>
                    </li>
                    <li>
                        <a class="btn btn-secondary" href="<?= e(base_url('modules/produits.php')); ?>">Produits</a>
                    </li>
                    <li>
                        <a class="btn btn-secondary" href="<?= e(base_url('modules/factures.php')); ?>">Factures</a>
                    </li>
                    <li>
                        <a class="btn btn-secondary" href="<?= e(base_url('rapports/rapport-journalier.php')); ?>">Rapport</a>
                    </li>
                    <?php if ($user !== null && ($user['role'] ?? '') === 'admin'): ?>
                        <li>
                            <a class="btn btn-secondary" href="<?= e(base_url('modules/inscription.php')); ?>">Comptes</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($user !== null): ?>
                        <li class="user-info"><?= e($user['nom']); ?> (<?= e($user['role']); ?>)</li>
                        <li><a class="btn btn-logout" href="<?= e(base_url('auth/logout.php')); ?>">Deconnexion</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="main-content container">
        <?php if ($flash !== null): ?>
            <div class="alert alert-<?= e($flash['type'] ?? 'info'); ?>">
                <?= e($flash['message'] ?? ''); ?>
            </div>
        <?php endif; ?>