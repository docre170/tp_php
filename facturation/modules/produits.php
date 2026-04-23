<?php

declare(strict_types=1);

require_once __DIR__ . '/../auth/session.php';
require_login();

$user = current_user();
if (($user['role'] ?? '') !== 'admin') {
    set_flash('error', 'Seul un admin peut modifier les produits.');
    redirect_to('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = post_string('code');
    $nom = post_string('nom');
    $prix = post_float('prix');
    $stock = post_int('stock');

    if ($code === '' || $nom === '' || $prix <= 0 || $stock < 0) {
        set_flash('error', 'Veuillez remplir correctement tous les champs.');
        redirect_to('modules/produits.php');
    }

    $produits = read_json_file(PRODUCTS_FILE);
    $found = false;

    foreach ($produits as &$produit) {
        if (($produit['code'] ?? '') === $code) {
            $produit['nom'] = $nom;
            $produit['prix'] = $prix;
            $produit['stock'] = $stock;
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
        <div class="form-group">
            <label for="code">Code-barres</label>
            <input id="code" name="code" required>
        </div>
        <div class="form-group">
            <label for="nom">Nom</label>
            <input id="nom" name="nom" required>
        </div>
        <div class="form-group">
            <label for="prix">Prix unitaire</label>
            <input id="prix" name="prix" type="number" min="0.01" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="stock">Stock</label>
            <input id="stock" name="stock" type="number" min="0" step="1" required>
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
                <th>Prix</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($produits as $produit): ?>
            <tr>
                <td><?= e((string) ($produit['code'] ?? '')); ?></td>
                <td><?= e((string) ($produit['nom'] ?? '')); ?></td>
                <td><?= e(number_format((float) ($produit['prix'] ?? 0), 2)); ?> $</td>
                <td><?= e((string) ($produit['stock'] ?? 0)); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

