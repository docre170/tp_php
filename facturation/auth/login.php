<?php

declare(strict_types=1);

require_once __DIR__ . '/session.php';

if (is_logged_in()) {
    redirect_to(role_home_path());
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = post_string('username');
    $password = post_string('password');

    $users = read_json_file(USERS_FILE);

    foreach ($users as $user) {
        if (($user['username'] ?? '') !== $username) {
            continue;
        }

        $storedPassword = (string) ($user['password'] ?? '');
        $isValid = false;
        if ($storedPassword !== '' && password_verify($password, $storedPassword)) {
            $isValid = true;
        } elseif ($storedPassword !== '' && hash_equals($storedPassword, $password)) {
            // Compatibilite legacy: mots de passe en clair.
            $isValid = true;
        }

        if ($isValid) {
            $role = normalize_role((string) ($user['role'] ?? ''));
            $_SESSION['user'] = [
                'username' => $user['username'],
                'nom' => $user['nom'],
                'role' => $role,
            ];
            $_SESSION['cart'] = [];
            set_flash('success', 'Connexion reussie.');
            redirect_to(role_home_path($role));
        }
    }

    $error = 'Identifiants invalides.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Supermarche</title>
    <link rel="stylesheet" href="<?= e(base_url('assets/css/style.css')); ?>">
</head>
<body>
    <main class="container main-content">
        <section class="card" style="max-width: 450px; margin: 40px auto;">
            <h1 class="card-header">Connexion</h1>
            <?php if ($error !== ''): ?>
                <div class="alert alert-error"><?= e($error); ?></div>
            <?php endif; ?>
            <form method="post" class="card-body">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input id="password" type="password" name="password" required>
                </div>
                <button class="btn btn-primary" type="submit">Se connecter</button>
            </form>
            <p class="mt-2">
                Demo super admin: <code>admin / admin123</code><br>
                Demo manager: <code>manager / manager123</code><br>
                Demo caissier: <code>caisse / caisse123</code>
            </p>
        </section>
    </main>
</body>
</html>

