<?php

declare(strict_types=1);

require_once __DIR__ . '/../auth/session.php';
require_any_role(['caissier', 'manager', 'super_admin']);

function generate_invoice_number(array $factures): string
{
    $existing = [];
    foreach ($factures as $facture) {
        $numero = (string) ($facture['numero'] ?? '');
        if ($numero !== '') {
            $existing[$numero] = true;
        }
    }

    do {
        $numero = 'FAC-' . date('Ymd-His') . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
    } while (isset($existing[$numero]));

    return $numero;
}

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
            set_flash('error', 'Produit inconnu. Veuillez demander au manager de l enregistrer avant la vente.');
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
            'prix_ht' => (float) ($produit['prix'] ?? 0),
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
        $totalHt = 0.0;
        foreach ($cart as $item) {
            $totalHt += ((float) $item['prix_ht']) * ((int) $item['quantite']);
        }
        $tvaRate = 0.18;
        $montantTva = $totalHt * $tvaRate;
        $totalTtc = $totalHt + $montantTva;

        $factures = read_json_file(INVOICES_FILE);
        $numero = generate_invoice_number($factures);
        $facture = [
            'numero' => $numero,
            'date' => date('Y-m-d H:i:s'),
            'caissier' => current_user()['username'] ?? 'n/a',
            'lignes' => $cart,
            'total_ht' => round($totalHt, 2),
            'taux_tva' => $tvaRate,
            'montant_tva' => round($montantTva, 2),
            'total_ttc' => round($totalTtc, 2),
            'net_a_payer' => round($totalTtc, 2),
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
$totalHtPanier = 0.0;
foreach ($cart as $item) {
    $totalHtPanier += ((float) $item['prix_ht']) * ((int) $item['quantite']);
}
$tvaRate = 0.18;
$montantTvaPanier = $totalHtPanier * $tvaRate;
$totalTtcPanier = $totalHtPanier + $montantTvaPanier;

require_once __DIR__ . '/../includes/header.php';
?>
<section class="card">
    <h2 class="card-header">Caisse</h2>
    <form method="post" class="grid grid-2 card-body">
        <input type="hidden" name="action" value="add">
        <div class="form-group">
            <label for="code-input">Code-barres</label>
            <input id="code-input" name="code" required>
            <small id="product-preview" style="display:block;margin-top:6px;">Produit: - | Prix HT: -</small>
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
                <th>Designation</th>
                <th>Prix unit. HT</th>
                <th>Quantite</th>
                <th>Sous-total HT</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($cart as $item): ?>
            <tr>
                <td><?= e((string) $item['code']); ?></td>
                <td><?= e((string) $item['nom']); ?></td>
                <td><?= e(number_format((float) $item['prix_ht'], 2)); ?> CDF</td>
                <td><?= e((string) $item['quantite']); ?></td>
                <td><?= e(number_format(((float) $item['prix_ht']) * ((int) $item['quantite']), 2)); ?> CDF</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <p class="mt-2"><strong>Total HT:</strong> <?= e(number_format($totalHtPanier, 2)); ?> CDF</p>
    <p><strong>TVA (18%):</strong> <?= e(number_format($montantTvaPanier, 2)); ?> CDF</p>
    <p><strong>Total TTC:</strong> <?= e(number_format($totalTtcPanier, 2)); ?> CDF</p>
    <p><strong>Net a payer:</strong> <?= e(number_format($totalTtcPanier, 2)); ?> CDF</p>
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
<script>
window.productByCode = <?= json_encode($produitsByCode, JSON_UNESCAPED_UNICODE); ?>;
document.addEventListener('DOMContentLoaded', function () {
    const codeInput = document.getElementById('code-input');
    const preview = document.getElementById('product-preview');
    if (!codeInput || !preview) {
        return;
    }

    const updatePreview = function () {
        const code = codeInput.value.trim();
        if (code === '' || !window.productByCode || !window.productByCode[code]) {
            preview.textContent = 'Produit: - | Prix HT: -';
            return;
        }

        const produit = window.productByCode[code];
        const nom = String(produit.nom || '-');
        const prix = Number(produit.prix || 0).toFixed(2);
        preview.textContent = 'Produit: ' + nom + ' | Prix HT: ' + prix + ' CDF';
    };

    codeInput.addEventListener('input', updatePreview);
    updatePreview();
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

