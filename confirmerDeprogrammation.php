<?php
session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['est_organisateur'])) {
    header("Location: connexion.php");
    exit;
}

$prog_id = intval($_POST['prog_id'] ?? 0);
$mission = $_POST['mission'] ?? '';

if ($prog_id <= 0) {
    header("Location: gestionProgramme.php");
    exit;
}

include 'app/View/header-footer/header.php';
?>

<main class="page-prestations">
    <div class="center">
        <h1>Confirmation de déprogrammation</h1>
    </div>
    <div class="form-container">
        <div class="alert-error confirm-modal-msg">
            ⚠️ Êtes-vous sûr de vouloir retirer la prestation <strong>« <?= htmlspecialchars($mission) ?> »</strong> du programme ?
        </div>
        <form method="post" action="gestionProgramme.php" class="space-form">
            <input type="hidden" name="action" value="deprogrammer">
            <input type="hidden" name="prog_id" value="<?= $prog_id ?>">
            <div class="form-actions">
                <button type="submit" class="btn-primary btn-danger">Confirmer la déprogrammation</button>
                <a href="gestionProgramme.php" class="btn-primary btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</main>

<?php include 'app/View/header-footer/footer.php'; ?>
