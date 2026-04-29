<?php

declare(strict_types=1);

require_once __DIR__ . '/../auth/session.php';
require_login();

$user = current_user();
$role = (string) ($user['role'] ?? '');
if ($role !== 'admin' && $role !== 'manager' && $role !== 'super_administrateur') {
    set_flash('error', 'Seul un Manager ou Super Administrateur peut modifier les produits.');
    redirect_to('index.php');
}

// Récupérer les données saisies précédemment en cas d'erreur
$form_data = [
    'code' => $_POST['code'] ?? '',
    'nom' => $_POST['nom'] ?? '',
    'prix' => $_POST['prix'] ?? '',
    'stock' => $_POST['stock'] ?? '',
    'date_expiration' => $_POST['date_expiration'] ?? ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = post_string('code');
    $nom = post_string('nom');
    $prix = post_float('prix');
    $stock = post_int('stock');
    $date_expiration = post_string('date_expiration');

    // Validation du format de date MM-JJ-AAAA
    $date_valid = preg_match('/^(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])-\d{4}$/', $date_expiration) === 1;

    if ($code === '' || $nom === '' || $prix <= 0 || $stock < 0 || !$date_valid) {
        set_flash('error', 'Veuillez remplir correctement tous les champs. Date au format MM-JJ-AAAA.');
        $_SESSION['form_error_data'] = [
            'code' => $code,
            'nom' => $nom,
            'prix' => $_POST['prix'] ?? '',
            'stock' => $_POST['stock'] ?? '',
            'date_expiration' => $date_expiration
        ];
        redirect_to('modules/produits.php');
    }

    $produits = read_json_file(PRODUCTS_FILE);
    $found = false;

    foreach ($produits as &$produit) {
        if (($produit['code'] ?? '') === $code) {
            $produit['nom'] = $nom;
            $produit['prix'] = $prix;
            $produit['stock'] = $stock;
            $produit['date_expiration'] = $date_expiration;
            $found = true;
            break;
        }
    }
    unset($produit);

    if (!$found) {
        $produits[] = [
            'code' => $code,
            'nom' => $nom,
            'prix' => $prix,
            'stock' => $stock,
            'date_expiration' => $date_expiration,
        ];
    }

    write_json_file(PRODUCTS_FILE, $produits);
    set_flash('success', $found ? 'Produit mis a jour.' : 'Produit ajoute.');
    redirect_to('modules/produits.php');
}

$produits = read_json_file(PRODUCTS_FILE);
require_once __DIR__ . '/../includes/header.php';
?>
<section class="card">
    <h2 class="card-header">Gestion des produits</h2>
    <form method="post" class="grid grid-2 card-body">
        <?php 
        // Récupérer les données en cas d'erreur précédente
        $error_data = $_SESSION['form_error_data'] ?? null;
        if ($error_data) {
            unset($_SESSION['form_error_data']);
        } else {
            $error_data = $form_data;
        }
        ?>
        <div class="form-group">
            <label for="code">Code-barres</label>
            <input id="code" name="code" value="<?= e($error_data['code']); ?>" required>
        </div>
        <div class="form-group">
            <label for="nom">Nom</label>
            <input id="nom" name="nom" value="<?= e($error_data['nom']); ?>" required>
        </div>
        <div class="form-group">
            <label for="prix">Prix unitaire (CDF)</label>
            <input id="prix" name="prix" type="number" min="0.01" step="0.01" value="<?= e($error_data['prix']); ?>" required>
        </div>
        <div class="form-group">
            <label for="stock">Quantité initiale en stock</label>
            <input id="stock" name="stock" type="number" min="0" step="1" value="<?= e($error_data['stock']); ?>" required>
        </div>
        <div class="form-group">
            <label for="date_expiration">Date d'expiration (MM-JJ-AAAA)</label>
            <input id="date_expiration" name="date_expiration" type="text" placeholder="MM-JJ-AAAA" pattern="(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])-\d{4}" value="<?= e($error_data['date_expiration']); ?>" required>
        </div>
        <button class="btn btn-success" type="submit">Enregistrer</button>
    </form>
</section>

<section class="card">
    <h2 class="card-header">Inventaire</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Nom</th>
                <th>Prix (CDF)</th>
                <th>Stock</th>
                <th>Date d'expiration</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($produits as $produit): ?>
            <tr>
                <td><?= e((string) ($produit['code'] ?? '')); ?></td>
                <td><?= e((string) ($produit['nom'] ?? '')); ?></td>
                <td><?= e(number_format((float) ($produit['prix'] ?? 0), 2)); ?> CDF</td>
                <td><?= e((string) ($produit['stock'] ?? 0)); ?></td>
                <td><?= e((string) ($produit['date_expiration'] ?? 'N/A')); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

