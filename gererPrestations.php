<?php

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

$user_id        = $_SESSION['user_id'];
$erreurs        = [];
$erreurs_champs = [];
$message_succes = "";

$ancien_intitule    = "";
$ancienne_desc      = "";
$ancienne_categorie = "";

// --- SUPPRESSION via POST (UC-C.3) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'supprimer') {
    $id_a_supprimer = intval($_POST['supprimer_id']);
    if (!Prestation::belongsTo($id_a_supprimer, $user_id)) {
        $erreurs[] = "Action non autorisée.";
    } elseif (Prestation::isProgrammee($id_a_supprimer)) {
        $erreurs[] = "Impossible de supprimer cette prestation car elle fait partie du programme officiel. Veuillez contacter l'organisateur.";
    } else {
        if (Prestation::delete($id_a_supprimer, $user_id)) {
            $message_succes = "La prestation a été retirée de votre catalogue.";
        }
    }
}

// --- AJOUT via POST (UC-C.1) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'ajouter') {
    $titre       = trim($_POST['intitule']);
    $description = trim($_POST['description']);
    $categorie_id = intval($_POST['categorie_id']);

    $ancien_intitule    = $titre;
    $ancienne_desc      = $description;
    $ancienne_categorie = $categorie_id;

    if (empty($titre))       $erreurs_champs['intitule']    = "Le titre est obligatoire.";
    if (!$categorie_id)      $erreurs_champs['categorie_id'] = "Veuillez sélectionner une catégorie.";
    if (empty($description)) $erreurs_champs['description']  = "La description est obligatoire.";

    // UC-C.1 : Gestion de l'image
    $image_filename = 'default.png';
    if (!empty($_FILES['image']['name'])) {
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $erreurs_champs['image'] = "Erreur lors du téléchargement de l'image.";
        } else {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                $erreurs_champs['image'] = "Format non valide (jpg, png, gif, webp).";
            } else {
                $image_filename = uniqid('presta_') . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'],
                    __DIR__ . '/img/prestations/' . $image_filename);
            }
        }
    }

    if (empty($erreurs_champs)) {
        if (Prestation::create($titre, $description, $image_filename, $categorie_id, $user_id)) {
            $message_succes = "Nouvelle prestation ajoutée avec succès à votre catalogue !";
            $ancien_intitule = $ancienne_desc = $ancienne_categorie = "";
        }
    }
}

$prestations = Prestation::findByArtiste($user_id);
$categories  = Categorie::findAll();

include 'app/View/header-footer/header.php';
?>

<main class="page-prestations">
    <div class="center">
        <a href="dashboard.php" class="back-link">← Retour au tableau de bord</a>
        <h1>Gestion de mon Catalogue</h1>
        <p>Gérez vos modules de formation et conférences.</p>
    </div>

    <div class="alert-container">
        <?php if (!empty($erreurs)): ?>
            <div class="alert-error"><ul><?php foreach ($erreurs as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?></ul></div>
        <?php endif; ?>
        <?php if (!empty($message_succes)): ?>
            <div class="alert-success"><?= htmlspecialchars($message_succes) ?></div>
        <?php endif; ?>
    </div>

    <div class="prestations-grid prestations-wrapper">
        <?php if (count($prestations) > 0): ?>
            <?php foreach ($prestations as $presta): ?>
                <article class="prestation-card catalog">
                    <div class="card-header">
                        <span class="category-badge security"><?= htmlspecialchars($presta['nom_categorie']) ?></span>
                        <?php $img_url = Prestation::getImageUrl($presta); ?>
                        <?php if ($img_url): ?>
                            <img src="<?= htmlspecialchars($img_url) ?>" alt="<?= htmlspecialchars($presta['intitule']) ?>" class="prestation-img-real">
                        <?php else: ?>
                            <div class="prestation-img-placeholder bg-gradient-catalog">
                                <code><?= htmlspecialchars(substr($presta['intitule'], 0, 3)) ?></code>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h3><?= htmlspecialchars($presta['intitule']) ?></h3>
                        <p class="description"><?= htmlspecialchars($presta['description']) ?></p>
                    </div>
                    <div class="card-footer card-actions">
                        <!-- Suppression sans JavaScript : formulaire POST avec page de confirmation PHP -->
                        <form method="post" action="confirmerSuppression.php">
                            <input type="hidden" name="type" value="prestation">
                            <input type="hidden" name="id" value="<?= $presta['pid'] ?>">
                            <input type="hidden" name="retour" value="gererPrestations.php">
                            <input type="hidden" name="message" value="Êtes-vous sûr de vouloir supprimer la prestation « <?= htmlspecialchars($presta['intitule']) ?> » ? Cette action est irréversible.">
                            <button type="submit" class="btn-primary btn-danger btn-small">🗑️ Supprimer</button>
                        </form>
                        <a href="modifierPrestation.php?id=<?= $presta['pid'] ?>" class="btn-primary btn-outline btn-small">✏️ Modifier</a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="catalog-empty">
                <p>Votre catalogue est vide. Ajoutez votre première prestation ci-dessous !</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-container">
        <h2 class="form-title">Ajouter une nouvelle prestation</h2>
        <form action="gererPrestations.php" method="post" enctype="multipart/form-data" class="space-form">
            <input type="hidden" name="action" value="ajouter">

            <div class="form-group <?= isset($erreurs_champs['intitule']) ? 'field-error' : '' ?>">
                <label for="intitule">Titre de la prestation *</label>
                <input type="text" id="intitule" name="intitule"
                       value="<?= htmlspecialchars($ancien_intitule) ?>" required>
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
                            <?= ($ancienne_categorie == $cat['cid']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['intitule']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($erreurs_champs['categorie_id'])): ?>
                    <span class="error-message"><?= htmlspecialchars($erreurs_champs['categorie_id']) ?></span>
                <?php endif; ?>
            </div>

            <!-- UC-C.1 : Champ Image -->
            <div class="form-group <?= isset($erreurs_champs['image']) ? 'field-error' : '' ?>">
                <label for="image">Image de la prestation *</label>
                <input type="file" id="image" name="image" accept="image/*" required>
                <?php if (isset($erreurs_champs['image'])): ?>
                    <span class="error-message"><?= htmlspecialchars($erreurs_champs['image']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group <?= isset($erreurs_champs['description']) ? 'field-error' : '' ?>">
                <label for="description">Description détaillée *</label>
                <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($ancienne_desc) ?></textarea>
                <?php if (isset($erreurs_champs['description'])): ?>
                    <span class="error-message"><?= htmlspecialchars($erreurs_champs['description']) ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-primary">Ajouter au catalogue 🚀</button>
        </form>
    </div>
</main>

<?php include 'app/View/header-footer/footer.php'; ?>
