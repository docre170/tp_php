<?php
declare(strict_types=1);

require_once __DIR__ . '/../auth/session.php';
require_login();

$factures = read_json_file(INVOICES_FILE);
$aujourdhui = date('Y-m-d');
$nbFactures = 0;
$totalJour = 0.0;

foreach ($factures as $facture) {
    $dateFacture = substr((string) ($facture['date'] ?? ''), 0, 10);
    if ($dateFacture === $aujourdhui) {
        $nbFactures++;
        $totalJour += (float) ($facture['total'] ?? 0);
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<section class="card">
    <h2 class="card-header">Rapport journalier</h2>
    <p><strong>Date:</strong> <?= e($aujourdhui); ?></p>
    <p><strong>Nombre de factures:</strong> <?= e((string) $nbFactures); ?></p>
    <p><strong>Montant total du jour:</strong> <?= e(number_format($totalJour, 2)); ?> $</p>
</section>

<section class="card">
    <h3 class="card-header">Factures du jour</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Numero</th>
                <th>Heure</th>
                <th>Caissier</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($factures as $facture): ?>
                <?php if (substr((string) ($facture['date'] ?? ''), 0, 10) === $aujourdhui): ?>
                    <tr>
                        <td><?= e((string) ($facture['numero'] ?? '')); ?></td>
                        <td><?= e(substr((string) ($facture['date'] ?? ''), 11)); ?></td>
                        <td><?= e((string) ($facture['caissier'] ?? '')); ?></td>
                        <td><?= e(number_format((float) ($facture['total'] ?? 0), 2)); ?> $</td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>