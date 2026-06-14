<?php
session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['est_organisateur'])) {
    header("Location: connexion.php");
    exit;
}

$artiste_uid  = intval($_POST['artiste_uid'] ?? 0);
$nom_artiste  = $_POST['nom_artiste'] ?? '';

if ($artiste_uid <= 0) {
    header("Location: gererArtistes.php");
    exit;
}

include 'app/View/header-footer/header.php';
?>

<main class="page-prestations">
    <div class="center">
        <h1>Confirmation de suppression</h1>
    </div>
    <div class="form-container">
        <div class="alert-error confirm-modal-msg">
            ⚠️ Vous allez supprimer l'artiste <strong><?= htmlspecialchars($nom_artiste) ?></strong>.
            Cette action est <strong>irréversible</strong> et entraînera la suppression de toutes ses
            prestations et de ses programmations.
        </div>
        <form method="post" action="gererArtistes.php" class="space-form">
            <input type="hidden" name="action" value="supprimer">
            <input type="hidden" name="artiste_uid" value="<?= $artiste_uid ?>">
            <label class="confirm-modal-checkbox">
                <input type="checkbox" name="je_comprends" required>
                Je comprends que cette action est irréversible.
            </label>
            <div class="form-actions">
                <button type="submit" class="btn-primary btn-danger">Supprimer définitivement</button>
                <a href="gererArtistes.php" class="btn-primary btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</main>

<?php include 'app/View/header-footer/footer.php'; ?>
