<?php
session_start();
// Vérification de sécurité : il faut être connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

require_once 'config/db.php';
$user_id = $_SESSION['user_id'];
$erreurs = [];
$message_succes = "";

// --- 1. GESTION DE LA SUPPRESSION (UC-C.3) ---
if (isset($_GET['supprimer_id'])) {
    $id_a_supprimer = $_GET['supprimer_id'];

    // Vérification : La prestation est-elle déjà dans le programme ?
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM web2026_Programmation WHERE prestation_id = :pid");
    $stmt_check->execute(['pid' => $id_a_supprimer]);
    $est_programme = $stmt_check->fetchColumn();

    if ($est_programme > 0) {
        $erreurs[] = "Impossible de supprimer cette prestation car elle fait partie du programme officiel. Veuillez contacter l'organisateur.";
    } else {
        $stmt_delete = $pdo->prepare("DELETE FROM web2026_Prestation WHERE pid = :pid AND artiste_id = :uid");
        if ($stmt_delete->execute(['pid' => $id_a_supprimer, 'uid' => $user_id])) {
            $message_succes = "La prestation a été retirée de votre catalogue.";
        }
    }
}

// --- 2. GESTION DE L'AJOUT D'UNE PRESTATION (UC-C.1) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter') {
    $titre = trim($_POST['intitule']);
    $description = trim($_POST['description']);
    $categorie_id = $_POST['categorie_id'];

    if (empty($titre) || empty($description) || empty($categorie_id)) {
        $erreurs[] = "Tous les champs obligatoires doivent être remplis.";
    } else {
        $sql_insert = "INSERT INTO web2026_Prestation (intitule, description, image, categorie_id, artiste_id) 
                       VALUES (:titre, :desc, 'default.png', :cat_id, :uid)";
        $stmt_insert = $pdo->prepare($sql_insert);
        if ($stmt_insert->execute(['titre' => $titre, 'desc' => $description, 'cat_id' => $categorie_id, 'uid' => $user_id])) {
            $message_succes = "Nouvelle prestation ajoutée avec succès à votre catalogue !";
        }
    }
}

// --- 3. RÉCUPÉRATION DES DONNÉES POUR L'AFFICHAGE ---
$stmt_catalogue = $pdo->prepare("
    SELECT p.*, c.intitule AS nom_categorie 
    FROM web2026_Prestation p 
    JOIN web2026_Categorie c ON p.categorie_id = c.cid 
    WHERE p.artiste_id = :uid
");
$stmt_catalogue->execute(['uid' => $user_id]);
$prestations = $stmt_catalogue->fetchAll();

$categories = $pdo->query("SELECT * FROM web2026_Categorie")->fetchAll();

include 'header-footer/header.php';
?>

    <main class="page-prestations">
        <div class="center">
            <a href="dashboard.php" class="back-link">← Retour au tableau de bord</a>
            <h1>Gestion de mon Catalogue</h1>
            <p>Gérez vos modules de formation et conférences.</p>
        </div>

        <div class="alert-container">
            <?php if (!empty($erreurs)): ?>
                <div class="alert-error">
                    <ul>
                        <?php foreach ($erreurs as $erreur): ?>
                            <li><?= htmlspecialchars($erreur) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($message_succes)): ?>
                <div class="alert-success">
                    <?= htmlspecialchars($message_succes) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="prestations-grid prestations-wrapper">
            <?php if (count($prestations) > 0): ?>
                <?php foreach ($prestations as $presta): ?>
                    <article class="prestation-card catalog">
                        <div class="card-header">
                            <span class="category-badge security"><?= htmlspecialchars($presta['nom_categorie']) ?></span>
                            <div class="prestation-img-placeholder bg-gradient-catalog">
                                <code><?= htmlspecialchars(substr($presta['intitule'], 0, 3)) ?></code>
                            </div>
                        </div>
                        <div class="card-body">
                            <h3><?= htmlspecialchars($presta['intitule']) ?></h3>
                            <p class="description"><?= htmlspecialchars($presta['description']) ?></p>
                        </div>
                        <div class="card-footer card-actions">
                            <a href="gererPrestations.php?supprimer_id=<?= $presta['pid'] ?>"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette prestation ? Cette action est irréversible.');"
                               class="btn-primary btn-danger btn-small">
                                🗑️ Supprimer
                            </a>
                            <a href="#" class="btn-primary btn-outline btn-small">✏️ Modifier</a>
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

            <form action="gererPrestations.php" method="post" class="space-form">
                <input type="hidden" name="action" value="ajouter">

                <div class="form-group">
                    <label for="intitule">Titre de la prestation *</label>
                    <input type="text" id="intitule" name="intitule" placeholder="Ex: Atelier Javascript avancé" required>
                </div>

                <div class="form-group">
                    <label for="categorie_id">Catégorie *</label>
                    <select id="categorie_id" name="categorie_id" required>
                        <option value="">-- Sélectionnez une catégorie --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['cid'] ?>"><?= htmlspecialchars($cat['intitule']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description détaillée *</label>
                    <textarea id="description" name="description" rows="4" placeholder="Décrivez le contenu de votre mission..." required></textarea>
                </div>

                <button type="submit" class="btn-primary">Ajouter au catalogue 🚀</button>
            </form>
        </div>
    </main>

<?php include 'header-footer/footer.php'; ?>