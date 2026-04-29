<?php

declare(strict_types=1);

require_once __DIR__ . '/../auth/session.php';
require_any_role(['caissier', 'manager', 'super_admin']);

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
                <th>Net a payer</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($factures as $facture): ?>
                <?php
                $totalHt = (float) ($facture['total_ht'] ?? $facture['total'] ?? 0);
                $tauxTva = (float) ($facture['taux_tva'] ?? 0.18);
                $montantTva = (float) ($facture['montant_tva'] ?? ($totalHt * $tauxTva));
                $netAPayer = (float) ($facture['net_a_payer'] ?? $facture['total_ttc'] ?? ($totalHt + $montantTva));
                ?>
                <tr>
                    <td><?= e((string) ($facture['numero'] ?? '')); ?></td>
                    <td><?= e((string) ($facture['date'] ?? '')); ?></td>
                    <td><?= e((string) ($facture['caissier'] ?? '')); ?></td>
                    <td><?= e(number_format($netAPayer, 2)); ?> CDF</td>
                    <td><a class="btn btn-secondary" href="<?= e(base_url('modules/factures.php?numero=' . urlencode((string) ($facture['numero'] ?? '')))); ?>">Detail</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php if ($factureSelectionnee !== null): ?>
    <?php
    $totalHt = (float) ($factureSelectionnee['total_ht'] ?? $factureSelectionnee['total'] ?? 0);
    $tauxTva = (float) ($factureSelectionnee['taux_tva'] ?? 0.18);
    $montantTva = (float) ($factureSelectionnee['montant_tva'] ?? ($totalHt * $tauxTva));
    $totalTtc = (float) ($factureSelectionnee['total_ttc'] ?? ($totalHt + $montantTva));
    $netAPayer = (float) ($factureSelectionnee['net_a_payer'] ?? $totalTtc);
    ?>
    <section class="card">
        <h2 class="card-header">Detail facture <?= e((string) $factureSelectionnee['numero']); ?></h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Designation</th>
                    <th>Prix unit. HT</th>
                    <th>Qte</th>
                    <th>Sous-total HT</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($factureSelectionnee['lignes'] ?? []) as $ligne): ?>
                    <?php
                    $prixHt = (float) ($ligne['prix_ht'] ?? $ligne['prix'] ?? 0);
                    $quantite = (int) ($ligne['quantite'] ?? 0);
                    $sousTotalHt = $prixHt * $quantite;
                    ?>
                    <tr>
                        <td><?= e((string) ($ligne['nom'] ?? '')); ?></td>
                        <td><?= e(number_format($prixHt, 2)); ?> CDF</td>
                        <td><?= e((string) $quantite); ?></td>
                        <td><?= e(number_format($sousTotalHt, 2)); ?> CDF</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="mt-2"><strong>Total HT:</strong> <?= e(number_format($totalHt, 2)); ?> CDF</p>
        <p><strong>TVA (18%):</strong> <?= e(number_format($montantTva, 2)); ?> CDF</p>
        <p><strong>Total TTC:</strong> <?= e(number_format($totalTtc, 2)); ?> CDF</p>
        <p><strong>Net a payer:</strong> <?= e(number_format($netAPayer, 2)); ?> CDF</p>
    </section>
<?php endif; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

