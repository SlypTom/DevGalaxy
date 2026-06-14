<?php
// Page PHP de confirmation de suppression — remplace les confirm() JavaScript
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

$type    = $_POST['type']    ?? $_GET['type']    ?? '';
$id      = intval($_POST['id']      ?? $_GET['id']      ?? 0);
$retour  = $_POST['retour']  ?? $_GET['retour']  ?? 'index.php';
$message = $_POST['message'] ?? $_GET['message'] ?? 'Êtes-vous sûr de vouloir effectuer cette suppression ?';

// Sécurisation : seuls ces retours sont autorisés
$retours_autorises = ['gererPrestations.php','gestionProgramme.php','gererArtistes.php','gererArtisteAdmin.php'];
if (!in_array($retour, $retours_autorises)) {
    $retour = 'index.php';
}

include 'app/View/header-footer/header.php';
?>

<main class="page-prestations">
    <div class="center">
        <h1>Confirmation de suppression</h1>
    </div>
    <div class="form-container">
        <div class="alert-error" style="margin-bottom:1.5rem;">
            ⚠️ <?= htmlspecialchars($message) ?>
        </div>
        <form method="post" action="<?= htmlspecialchars($retour) ?>" class="space-form">
            <input type="hidden" name="action" value="supprimer">
            <input type="hidden" name="supprimer_id" value="<?= $id ?>">
            <div class="form-actions">
                <button type="submit" class="btn-primary btn-danger">Oui, supprimer définitivement</button>
                <a href="<?= htmlspecialchars($retour) ?>" class="btn-primary btn-outline">Non, annuler</a>
            </div>
        </form>
    </div>
</main>

<?php include 'app/View/header-footer/footer.php'; ?>
