<?php

declare(strict_types=1);

require_once __DIR__ . '/../auth/session.php';
require_login();

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$produits = read_json_file(PRODUCTS_FILE);
$produitsByCode = [];
foreach ($produits as $produit) {
    $code = (string) ($produit['code'] ?? '');
    if ($code !== '') {
        $produitsByCode[$code] = $produit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = post_string('action');

    if ($action === 'add') {
        $code = post_string('code');
        $quantite = max(1, post_int('quantite'));

        if (!isset($produitsByCode[$code])) {
            set_flash('error', 'Produit introuvable.');
            redirect_to('modules/caisse.php');
        }

        $produit = $produitsByCode[$code];
        $stock = (int) ($produit['stock'] ?? 0);
        $deja = (int) ($_SESSION['cart'][$code]['quantite'] ?? 0);

        if (($deja + $quantite) > $stock) {
            set_flash('error', 'Stock insuffisant pour ce produit.');
            redirect_to('modules/caisse.php');
        }

        $_SESSION['cart'][$code] = [
            'code' => $code,
            'nom' => (string) ($produit['nom'] ?? ''),
            'prix' => (float) ($produit['prix'] ?? 0),
            'quantite' => $deja + $quantite,
        ];
        set_flash('success', 'Produit ajoute au panier.');
        redirect_to('modules/caisse.php');
    }

    if ($action === 'clear') {
        $_SESSION['cart'] = [];
        set_flash('success', 'Panier vide.');
        redirect_to('modules/caisse.php');
    }

    if ($action === 'checkout') {
        if (count($_SESSION['cart']) === 0) {
            set_flash('error', 'Le panier est vide.');
            redirect_to('modules/caisse.php');
        }

        $cart = array_values($_SESSION['cart']);
        $total = 0.0;
        foreach ($cart as $item) {
            $total += ((float) $item['prix']) * ((int) $item['quantite']);
        }

        $factures = read_json_file(INVOICES_FILE);
        $numero = 'FAC-' . date('Ymd') . '-' . str_pad((string) (count($factures) + 1), 4, '0', STR_PAD_LEFT);
        $facture = [
            'numero' => $numero,
            'date' => date('Y-m-d H:i:s'),
            'caissier' => current_user()['username'] ?? 'n/a',
            'lignes' => $cart,
            'total' => round($total, 2),
        ];
        $factures[] = $facture;

        // Mise a jour stock
        foreach ($produits as &$produit) {
            $code = (string) ($produit['code'] ?? '');
            if (isset($_SESSION['cart'][$code])) {
                $produit['stock'] = max(0, (int) ($produit['stock'] ?? 0) - (int) $_SESSION['cart'][$code]['quantite']);
            }
        }
        unset($produit);

        write_json_file(INVOICES_FILE, $factures);
        write_json_file(PRODUCTS_FILE, $produits);
        $_SESSION['cart'] = [];
        set_flash('success', "Facture $numero enregistree.");
        redirect_to('modules/factures.php?numero=' . urlencode($numero));
    }
}

$cart = array_values($_SESSION['cart']);
$totalPanier = 0.0;
foreach ($cart as $item) {
    $totalPanier += ((float) $item['prix']) * ((int) $item['quantite']);
}

require_once __DIR__ . '/../includes/header.php';
?>
<section class="card">
    <h2 class="card-header">Caisse</h2>
    <form method="post" class="grid grid-2 card-body">
        <input type="hidden" name="action" value="add">
        <div class="form-group">
            <label for="code-input">Code-barres</label>
            <input id="code-input" name="code" required>
        </div>
        <div class="form-group">
            <label for="quantite">Quantite</label>
            <input id="quantite" name="quantite" type="number" min="1" step="1" value="1" required>
        </div>
        <button class="btn btn-primary" type="submit">Ajouter au panier</button>
    </form>
    <div class="mt-2">
        <button class="btn btn-secondary" type="button" id="start-scan">Scanner avec camera</button>
        <div id="scanner-container" class="mt-2 hidden">
            <div id="scanner-video"></div>
            <p class="mt-1">Pointez la camera vers le code-barres.</p>
        </div>
    </div>
</section>

<section class="card">
    <h2 class="card-header">Panier courant</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Nom</th>
                <th>Prix</th>
                <th>Quantite</th>
                <th>Sous-total</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($cart as $item): ?>
            <tr>
                <td><?= e((string) $item['code']); ?></td>
                <td><?= e((string) $item['nom']); ?></td>
                <td><?= e(number_format((float) $item['prix'], 2)); ?> $</td>
                <td><?= e((string) $item['quantite']); ?></td>
                <td><?= e(number_format(((float) $item['prix']) * ((int) $item['quantite']), 2)); ?> $</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <p class="mt-2"><strong>Total:</strong> <?= e(number_format($totalPanier, 2)); ?> $</p>
    <form method="post" class="mt-2" style="display:flex;gap:10px;">
        <input type="hidden" name="action" value="checkout">
        <button class="btn btn-success" type="submit">Valider la facture</button>
    </form>
    <form method="post" class="mt-1">
        <input type="hidden" name="action" value="clear">
        <button class="btn btn-error" type="submit">Vider le panier</button>
    </form>
</section>

<script src="https://unpkg.com/html5-qrcode" defer></script>
<script src="<?= e(base_url('assets/js/scanner.js')); ?>" defer></script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

