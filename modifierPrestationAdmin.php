<?php
// Modification d'une prestation par l'organisateur (UC-D.4)
session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['est_organisateur'])) {
    header("Location: connexion.php");
    exit;
}

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/app/Model/Prestation.php';
require_once __DIR__ . '/app/Model/Categorie.php';

$pid         = intval(isset($_GET['pid']) ? $_GET['pid'] : 0);
$artiste_uid = intval(isset($_GET['uid']) ? $_GET['uid'] : 0);

if ($pid <= 0 || $artiste_uid <= 0) {
    header("Location: gererArtistes.php");
    exit;
}

// La prestation doit appartenir à l'artiste indiqué
$stmt = $pdo->prepare("
    SELECT p.*, c.intitule AS nom_categorie
    FROM web2026_Prestation p
    JOIN web2026_Categorie c ON p.categorie_id = c.cid
    WHERE p.pid = :pid AND p.artiste_id = :uid
");
$stmt->execute(['pid' => $pid, 'uid' => $artiste_uid]);
$presta = $stmt->fetch();

if (!$presta) {
    header("Location: gererArtisteAdmin.php?id=" . $artiste_uid);
    exit;
}

$erreurs_champs = [];
$message_succes = "";

$titre        = $presta['intitule'];
$description  = $presta['description'];
$categorie_id = $presta['categorie_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre        = trim($_POST['intitule']);
    $description  = trim($_POST['description']);
    $categorie_id = $_POST['categorie_id'];

    if (empty($titre))        $erreurs_champs['intitule']    = "Le titre est obligatoire.";
    if (empty($categorie_id)) $erreurs_champs['categorie_id'] = "Veuillez sélectionner une catégorie.";
    if (empty($description))  $erreurs_champs['description']  = "La description est obligatoire.";

    if (empty($erreurs_champs)) {
        $pdo->prepare("
            UPDATE web2026_Prestation
            SET intitule = :t, description = :d, categorie_id = :c
            WHERE pid = :pid AND artiste_id = :uid
        ")->execute(['t' => $titre, 'd' => $description, 'c' => $categorie_id, 'pid' => $pid, 'uid' => $artiste_uid]);

        $message_succes = "La prestation a été mise à jour avec succès.";
        // Rechargement
        $stmt->execute(['pid' => $pid, 'uid' => $artiste_uid]);
        $presta = $stmt->fetch();
    }
}

$categories = $pdo->query("SELECT * FROM web2026_Categorie")->fetchAll();

include 'app/View/header-footer/header.php';
?>

<main class="page-prestations">
    <div class="center">
        <a href="gererArtisteAdmin.php?id=<?= $artiste_uid ?>" class="back-link">← Retour au profil de l'artiste</a>
        <h1>Modifier la prestation</h1>
        <p>Modification administrative de la prestation <em><?= htmlspecialchars($presta['intitule']) ?></em>.</p>
    </div>

    <div class="alert-container">
        <?php if ($message_succes): ?>
            <div class="alert-success"><?= htmlspecialchars($message_succes) ?></div>
        <?php endif; ?>
    </div>

    <div class="form-container">
        <form action="modifierPrestationAdmin.php?pid=<?= $pid ?>&uid=<?= $artiste_uid ?>" method="post" class="space-form">

            <div class="form-group <?= isset($erreurs_champs['intitule']) ? 'field-error' : '' ?>">
                <label for="intitule">Titre de la prestation *</label>
                <input type="text" id="intitule" name="intitule"
                       value="<?= htmlspecialchars($titre) ?>" required>
                <?php if (isset($erreurs_champs['intitule'])): ?>
                    <span class="error-message"><?= htmlspecialchars($erreurs_champs['intitule']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group <?= isset($erreurs_champs['categorie_id']) ? 'field-error' : '' ?>">
                <label for="categorie_id">Catégorie *</label>
                <select id="categorie_id" name="categorie_id" required>
                    <option value="">-- Sélectionnez une catégorie --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['cid'] ?>"
                            <?= ($categorie_id == $cat['cid']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['intitule']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($erreurs_champs['categorie_id'])): ?>
                    <span class="error-message"><?= htmlspecialchars($erreurs_champs['categorie_id']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group <?= isset($erreurs_champs['description']) ? 'field-error' : '' ?>">
                <label for="description">Description détaillée *</label>
                <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($description) ?></textarea>
                <?php if (isset($erreurs_champs['description'])): ?>
                    <span class="error-message"><?= htmlspecialchars($erreurs_champs['description']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Enregistrer les modifications</button>
                <a href="gererArtisteAdmin.php?id=<?= $artiste_uid ?>" class="btn-primary btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</main>

<?php include 'app/View/header-footer/footer.php'; ?>
