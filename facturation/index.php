<?php
declare(strict_types=1);

require_once __DIR__ . '/auth/session.php';
require_login();
$user = current_user();
require_once __DIR__ . '/includes/header.php';
?>
<h2 class="mb-2">Tableau de bord</h2>
<div class="grid grid-2">
    <article class="card">
        <img src="<?= e(base_url('assets/images/scan.jpg')); ?>" alt="scan"
            style="width:100%;max-height:180px;object-fit:cover;">
        <h3 class="mt-2 mb-1">Scanner / Caisse</h3>
        <p class="mb-2">Lire un code-barres et ajouter des articles a la facture.</p>
        <a class="btn btn-primary" href="<?= e(base_url('modules/caisse.php')); ?>">Ouvrir la caisse</a>
    </article>
    <?php if (has_any_role(['manager', 'super_admin'])): ?>
        <article class="card">
            <img src="<?= e(base_url('assets/images/ajouter.jpg')); ?>" alt="ajout"
                style="width:100%;max-height:180px;object-fit:cover;">
            <h3 class="mt-2 mb-1">Ajouter des produits</h3>
            <p class="mb-2">Créer et mettre à jour l'inventaire en JSON.</p>
            <a class="btn btn-primary" href="<?= e(base_url('modules/produits.php')); ?>">Gerer les produits</a>
        </article>
    <?php endif; ?>
    <article class="card">
        <img src="<?= e(base_url('assets/images/facture.jpg')); ?>" alt="facture"
            style="width:100%;max-height:180px;object-fit:cover;">
        <h3 class="mt-2 mb-1">Factures</h3>
        <p class="mb-2">Consulter les factures creees.</p>
        <a class="btn btn-primary" href="<?= e(base_url('modules/factures.php')); ?>">Voir les factures</a>
    </article>
    <?php if (has_role('super_admin')): ?>
        <article class="card">
            <img src="<?= e(base_url('assets/images/inscription.jpg')); ?>" alt="inventaire"
                style="width:100%;max-height:180px;object-fit:cover;">
            <h3 class="mt-2 mb-1">Gestion des comptes</h3>
            <p class="mb-2">Creer et supprimer des comptes Caissier et Manager.</p>
            <a class="btn btn-primary" href="<?= e(base_url('modules/inscription.php')); ?>">Gerer les comptes</a>
        </article>
    <?php endif; ?>

</div>
<?php
require_once __DIR__ . '/includes/footer.php';
?>