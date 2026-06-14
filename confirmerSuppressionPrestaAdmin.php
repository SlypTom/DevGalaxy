<?php
session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['est_organisateur'])) {
    header("Location: connexion.php");
    exit;
}
$presta_id   = intval($_POST['presta_id'] ?? 0);
$artiste_uid = intval($_POST['artiste_uid'] ?? 0);
$intitule    = $_POST['intitule'] ?? '';
if ($presta_id <= 0 || $artiste_uid <= 0) {
    header("Location: gererArtistes.php");
    exit;
}
include 'app/View/header-footer/header.php';
?>
<main class="page-prestations">
    <div class="center"><h1>Confirmation de suppression</h1></div>
    <div class="form-container">
        <div class="alert-error confirm-modal-msg">
            ⚠️ Supprimer la prestation <strong>« <?= htmlspecialchars($intitule) ?> »</strong> ?
        </div>
        <form method="post" action="gererArtisteAdmin.php?id=<?= $artiste_uid ?>" class="space-form">
            <input type="hidden" name="action" value="supprimer_presta">
            <input type="hidden" name="presta_id" value="<?= $presta_id ?>">
            <div class="form-actions">
                <button type="submit" class="btn-primary btn-danger">Confirmer la suppression</button>
                <a href="gererArtisteAdmin.php?id=<?= $artiste_uid ?>" class="btn-primary btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</main>
<?php include 'app/View/header-footer/footer.php'; ?>
