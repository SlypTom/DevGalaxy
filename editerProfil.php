<?php
session_start();

// SÉCURITÉ : Vérification de la connexion
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/app/Model/Utilisateur.php';
$user_id = $_SESSION['user_id'];
$erreurs_champs = [];
$message_succes = "";

// RÉCUPÉRATION DES DONNÉES ACTUELLES
$stmt = $pdo->prepare("SELECT * FROM web2026_Utilisateur WHERE uid = :uid");
$stmt->execute(['uid' => $user_id]);
$user = $stmt->fetch();

// Valeurs affichées dans le formulaire (repopulation)
$nom         = $user['nom'];
$prenom      = $user['prenom'];
$nom_artiste = $user['nom_artiste'];
$email       = $user['email'];
$description = $user['description'];

// TRAITEMENT DU FORMULAIRE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom         = trim($_POST['nom']);
    $prenom      = trim($_POST['prenom']);
    $nom_artiste = trim($_POST['nom_artiste']);
    $email       = trim($_POST['email']);
    $description = trim($_POST['description']);
    $nouveau_mdp = $_POST['nouveau_mdp'];
    $conf_mdp    = $_POST['conf_mdp'];

    // --- VALIDATION PAR CHAMP ---
    if (empty($nom)) {
        $erreurs_champs['nom'] = "Le nom est obligatoire.";
    }
    if (empty($prenom)) {
        $erreurs_champs['prenom'] = "Le prénom est obligatoire.";
    }
    if (empty($email)) {
        $erreurs_champs['email'] = "L'adresse e-mail est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs_champs['email'] = "Le format de l'adresse e-mail n'est pas valide.";
    } elseif ($email !== $user['email']) {
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM web2026_Utilisateur WHERE email = :email AND uid != :uid");
        $stmt_check->execute(['email' => $email, 'uid' => $user_id]);
        if ($stmt_check->fetchColumn() > 0) {
            $erreurs_champs['email'] = "Cette adresse e-mail est déjà utilisée par un autre compte.";
        }
    }
    if (!empty($nouveau_mdp) && $nouveau_mdp !== $conf_mdp) {
        $erreurs_champs['conf_mdp'] = "Les nouveaux mots de passe ne correspondent pas.";
    }

    // MISE À JOUR DANS LA BASE DE DONNÉES
    if (empty($erreurs_champs)) {
        if (!empty($nouveau_mdp)) {
            // Stockage en clair (selon CDC)
            $sql_update = "UPDATE web2026_Utilisateur SET nom = :nom, prenom = :prenom, nom_artiste = :nom_artiste, email = :email, description = :description, mot_passe_hashe = :mdp WHERE uid = :uid";
            $stmt_update = $pdo->prepare($sql_update);
            $succes = $stmt_update->execute([
                    'nom' => $nom, 'prenom' => $prenom, 'nom_artiste' => $nom_artiste,
                    'email' => $email, 'description' => $description, 'mdp' => $nouveau_mdp, 'uid' => $user_id
            ]);
        } else {
            $sql_update = "UPDATE web2026_Utilisateur SET nom = :nom, prenom = :prenom, nom_artiste = :nom_artiste, email = :email, description = :description WHERE uid = :uid";
            $stmt_update = $pdo->prepare($sql_update);
            $succes = $stmt_update->execute([
                    'nom' => $nom, 'prenom' => $prenom, 'nom_artiste' => $nom_artiste,
                    'email' => $email, 'description' => $description, 'uid' => $user_id
            ]);
        }

        if ($succes) {
            $message_succes = "Votre profil a été mis à jour avec succès.";
            $stmt->execute(['uid' => $user_id]);
            $user = $stmt->fetch();
        } else {
            $erreurs[] = "Une erreur est survenue lors de la mise à jour.";
        }
    }
}

include 'app/View/header-footer/header.php';
?>

    <main class="page-prestations">
        <div class="center profile-edit-header">
            <a href="dashboard.php" class="back-link">← Retour au tableau de bord</a>
            <h1 class="profile-edit-title">Éditer mon profil</h1>
            <p class="profile-edit-subtitle">Mettez à jour vos informations de pilote.</p>
        </div>

        <div class="alert-container">
            <?php if (!empty($message_succes)): ?>
                <div class="alert-success">
                    <?= htmlspecialchars($message_succes) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-container">
            <form action="editerProfil.php" method="post" class="space-form">

                <div class="form-group <?= isset($erreurs_champs['nom']) ? 'field-error' : '' ?>">
                    <label for="nom">Nom *</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>" required>
                    <?php if (isset($erreurs_champs['nom'])): ?>
                        <span class="error-message"><?= htmlspecialchars($erreurs_champs['nom']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group <?= isset($erreurs_champs['prenom']) ? 'field-error' : '' ?>">
                    <label for="prenom">Prénom *</label>
                    <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($prenom) ?>" required>
                    <?php if (isset($erreurs_champs['prenom'])): ?>
                        <span class="error-message"><?= htmlspecialchars($erreurs_champs['prenom']) ?></span>
                    <?php endif; ?>
                </div>

                <?php if (empty($_SESSION['est_organisateur'])): ?>
                    <div class="form-group">
                        <label for="nom_artiste">Nom d'artiste / Pseudo</label>
                        <input type="text" id="nom_artiste" name="nom_artiste" value="<?= htmlspecialchars($nom_artiste) ?>">
                    </div>
                <?php else: ?>
                    <input type="hidden" name="nom_artiste" value="">
                <?php endif; ?>

                <div class="form-group <?= isset($erreurs_champs['email']) ? 'field-error' : '' ?>">
                    <label for="email">Email de liaison *</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                    <?php if (isset($erreurs_champs['email'])): ?>
                        <span class="error-message"><?= htmlspecialchars($erreurs_champs['email']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="description">Biographie / Description</label>
                    <textarea id="description" name="description" rows="5"><?= htmlspecialchars($description) ?></textarea>
                </div>

                <h3 class="password-section-title">Sécurité</h3>
                <span class="helper-text">Laissez ces champs vides si vous ne souhaitez pas modifier votre mot de passe actuel.</span>

                <div class="form-group">
                    <label for="nouveau_mdp">Nouveau mot de passe</label>
                    <input type="password" id="nouveau_mdp" name="nouveau_mdp" placeholder="********">
                </div>

                <div class="form-group <?= isset($erreurs_champs['conf_mdp']) ? 'field-error' : '' ?>">
                    <label for="conf_mdp">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="conf_mdp" name="conf_mdp" placeholder="********">
                    <?php if (isset($erreurs_champs['conf_mdp'])): ?>
                        <span class="error-message"><?= htmlspecialchars($erreurs_champs['conf_mdp']) ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-primary">Mettre à jour mes informations</button>
            </form>
        </div>
    </main>

<?php include 'app/View/header-footer/footer.php'; ?>