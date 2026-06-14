<?php
// UC-C.2 : Modification d'une prestation par l'artiste connecté
use Model\Categorie;
use Model\Prestation;

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/app/Model/Prestation.php';
require_once __DIR__ . '/app/Model/Categorie.php';

$user_id = $_SESSION['user_id'];
$pid     = intval($_GET['id'] ?? 0);

if ($pid <= 0 || !Prestation::belongsTo($pid, $user_id)) {
    header("Location: gererPrestations.php");
    exit;
}

// Rechargement de la prestation de base
$presta = Prestation::findById($pid);

$erreurs_champs = [];
$message_succes = "";

$titre        = $presta['intitule'];
$description  = $presta['description'];
$categorie_id = $presta['categorie_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre        = trim($_POST['intitule']);
    $description  = trim($_POST['description']);
    $categorie_id = intval($_POST['categorie_id']);

    if (empty($titre))       $erreurs_champs['intitule']    = "Le titre est obligatoire.";
    if (!$categorie_id)      $erreurs_champs['categorie_id'] = "Veuillez sélectionner une catégorie.";
    if (empty($description)) $erreurs_champs['description']  = "La description est obligatoire.";

    // Gestion de la nouvelle image (optionnelle à la modification)
    $nouvelle_image = null;
    if (!empty($_FILES['image']['name'])) {
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $erreurs_champs['image'] = "Erreur lors du téléchargement.";
        } else {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                $erreurs_champs['image'] = "Format non valide (jpg, png, gif, webp).";
            } else {
                $nouvelle_image = uniqid('presta_') . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'],
                    __DIR__ . '/img/prestations/' . $nouvelle_image);
            }
        }
    }

    if (empty($erreurs_champs)) {
        Prestation::update($pid, $user_id, $titre, $description, $categorie_id, $nouvelle_image);
        $message_succes = "La prestation a été mise à jour avec succès.";
        $presta = Prestation::findById($pid);
    }
}

$categories = Categorie::findAll();

include 'app/View/header-footer/header.php';
?>

<main class="page-prestations">
    <div class="center">
        <a href="gererPrestations.php" class="back-link">← Retour au catalogue</a>
        <h1>Modifier la prestation</h1>
    </div>

    <div class="alert-container">
        <?php if ($message_succes): ?>
            <div class="alert-success"><?= htmlspecialchars($message_succes) ?></div>
        <?php endif; ?>
    </div>

    <div class="form-container">
        <form action="modifierPrestation.php?id=<?= $pid ?>" method="post"
              enctype="multipart/form-data" class="space-form">

            <div class="form-group <?= isset($erreurs_champs['intitule']) ? 'field-error' : '' ?>">
                <label for="intitule">Titre *</label>
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

            <div class="form-group <?= isset($erreurs_champs['image']) ? 'field-error' : '' ?>">
                <label for="image">Nouvelle image (laisser vide pour conserver l'actuelle)</label>
                <?php $img_url = Prestation::getImageUrl($presta); ?>
                <?php if ($img_url): ?>
                    <img src="<?= htmlspecialchars($img_url) ?>" alt="Image actuelle"
                         style="max-height:80px;display:block;margin-bottom:.5rem;border-radius:6px;">
                <?php endif; ?>
                <input type="file" id="image" name="image" accept="image/*">
                <?php if (isset($erreurs_champs['image'])): ?>
                    <span class="error-message"><?= htmlspecialchars($erreurs_champs['image']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group <?= isset($erreurs_champs['description']) ? 'field-error' : '' ?>">
                <label for="description">Description *</label>
                <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($description) ?></textarea>
                <?php if (isset($erreurs_champs['description'])): ?>
                    <span class="error-message"><?= htmlspecialchars($erreurs_champs['description']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Enregistrer les modifications</button>
                <a href="gererPrestations.php" class="btn-primary btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</main>

<?php include 'app/View/header-footer/footer.php'; ?>
