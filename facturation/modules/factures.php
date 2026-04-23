<?php

declare(strict_types=1);

require_once __DIR__ . '/../auth/session.php';
require_login();

$factures = read_json_file(INVOICES_FILE);
$numeroRecherche = trim((string) ($_GET['numero'] ?? ''));
$factureSelectionnee = null;

if ($numeroRecherche !== '') {
    foreach ($factures as $facture) {
        if (($facture['numero'] ?? '') === $numeroRecherche) {
            $factureSelectionnee = $facture;
            break;
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<section class="card">
    <h2 class="card-header">Liste des factures</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Numero</th>
                <th>Date</th>
                <th>Caissier</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($factures as $facture): ?>
                <tr>
                    <td><?= e((string) ($facture['numero'] ?? '')); ?></td>
                    <td><?= e((string) ($facture['date'] ?? '')); ?></td>
                    <td><?= e((string) ($facture['caissier'] ?? '')); ?></td>
                    <td><?= e(number_format((float) ($facture['total'] ?? 0), 2)); ?> $</td>
                    <td><a class="btn btn-secondary" href="<?= e(base_url('modules/factures.php?numero=' . urlencode((string) ($facture['numero'] ?? '')))); ?>">Detail</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php if ($factureSelectionnee !== null): ?>
    <section class="card">
        <h2 class="card-header">Detail facture <?= e((string) $factureSelectionnee['numero']); ?></h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Produit</th>
                    <th>Prix</th>
                    <th>Quantite</th>
                    <th>Sous-total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($factureSelectionnee['lignes'] ?? []) as $ligne): ?>
                    <tr>
                        <td><?= e((string) ($ligne['code'] ?? '')); ?></td>
                        <td><?= e((string) ($ligne['nom'] ?? '')); ?></td>
                        <td><?= e(number_format((float) ($ligne['prix'] ?? 0), 2)); ?> $</td>
                        <td><?= e((string) ($ligne['quantite'] ?? 0)); ?></td>
                        <td><?= e(number_format(((float) ($ligne['prix'] ?? 0)) * ((int) ($ligne['quantite'] ?? 0)), 2)); ?> $</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
<?php endif; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

