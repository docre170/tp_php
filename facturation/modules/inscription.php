<?php

declare(strict_types=1);

require_once __DIR__ . '/../auth/session.php';
require_login();

$user = current_user();
if (($user['role'] ?? '') !== 'admin') {
    set_flash('error', 'Seul un admin peut ajouter un compte.');
    redirect_to('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = post_string('action');
    if ($action === 'delete') {
        $usernameToDelete = post_string('username_to_delete');
        if ($usernameToDelete === '') {
            set_flash('error', 'Compte invalide.');
            redirect_to('modules/inscription.php');
        }

        if ($usernameToDelete === ($user['username'] ?? '')) {
            set_flash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            redirect_to('modules/inscription.php');
        }

        $users = read_json_file(USERS_FILE);
        $updatedUsers = [];
        $deleted = false;

        foreach ($users as $existingUser) {
            if (($existingUser['username'] ?? '') === $usernameToDelete) {
                $deleted = true;
                continue;
            }
            $updatedUsers[] = $existingUser;
        }

        if (!$deleted) {
            set_flash('error', 'Compte introuvable.');
            redirect_to('modules/inscription.php');
        }

        if (!write_json_file(USERS_FILE, $updatedUsers)) {
            set_flash('error', 'Impossible de supprimer le compte pour le moment.');
            redirect_to('modules/inscription.php');
        }

        set_flash('success', 'Compte supprime avec succes.');
        redirect_to('modules/inscription.php');
    }

    $nom = post_string('nom');
    $username = post_string('username');
    $password = post_string('password');
    $role = post_string('role');

    $rolesAutorises = ['admin', 'caissier', 'client'];
    if ($nom === '' || $username === '' || $password === '' || !in_array($role, $rolesAutorises, true)) {
        set_flash('error', 'Veuillez remplir correctement tous les champs.');
        redirect_to('modules/inscription.php');
    }

    $users = read_json_file(USERS_FILE);
    foreach ($users as $existingUser) {
        if (($existingUser['username'] ?? '') === $username) {
            set_flash('error', "Le nom d'utilisateur existe deja.");
            redirect_to('modules/inscription.php');
        }
    }

    $users[] = [
        'username' => $username,
        'password' => $password,
        'nom' => $nom,
        'role' => $role,
    ];

    if (!write_json_file(USERS_FILE, $users)) {
        set_flash('error', 'Impossible de creer le compte pour le moment.');
        redirect_to('modules/inscription.php');
    }

    set_flash('success', 'Compte ajoute avec succes.');
    redirect_to('modules/inscription.php');
}

$users = read_json_file(USERS_FILE);
require_once __DIR__ . '/../includes/header.php';
?>
<section class="card">
    <h2 class="card-header">Ajouter un compte</h2>
    <form method="post" class="grid grid-2 card-body">
        <div class="form-group">
            <label for="nom">Nom complet</label>
            <input id="nom" name="nom" required>
        </div>
        <div class="form-group">
            <label for="username">Nom d'utilisateur</label>
            <input id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input id="password" type="password" name="password" minlength="4" required>
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="caissier">Caissier</option>
                <option value="admin">Admin</option>
                <option value="client">Client</option>
            </select>
        </div>
        <button class="btn btn-success" type="submit">Creer le compte</button>
    </form>
</section>

<section class="card">
    <h2 class="card-header">Comptes existants</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Utilisateur</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $existingUser): ?>
            <tr>
                <td><?= e((string) ($existingUser['nom'] ?? '')); ?></td>
                <td><?= e((string) ($existingUser['username'] ?? '')); ?></td>
                <td><?= e((string) ($existingUser['role'] ?? '')); ?></td>
                <td>
                    <?php if (($existingUser['username'] ?? '') === ($user['username'] ?? '')): ?>
                        <span>Compte courant</span>
                    <?php else: ?>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="username_to_delete" value="<?= e((string) ($existingUser['username'] ?? '')); ?>">
                            <button class="btn btn-secondary" type="submit">Supprimer</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
