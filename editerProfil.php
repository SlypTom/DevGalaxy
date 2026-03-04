<?php
session_start();

// SÉCURITÉ : Vérification de la connexion
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

require_once 'config/db.php';
$user_id = $_SESSION['user_id'];
$erreurs = [];
$message_succes = "";

// RÉCUPÉRATION DES DONNÉES ACTUELLES
$stmt = $pdo->prepare("SELECT * FROM web2026_Utilisateur WHERE uid = :uid");
$stmt->execute(['uid' => $user_id]);
$user = $stmt->fetch();

// TRAITEMENT DU FORMULAIRE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $nom_artiste = trim($_POST['nom_artiste']);
    $email = trim($_POST['email']);
    $description = trim($_POST['description']);
    $nouveau_mdp = $_POST['nouveau_mdp'];
    $conf_mdp = $_POST['conf_mdp'];

    // Vérifications de base
    if (empty($nom) || empty($prenom) || empty($email)) {
        $erreurs[] = "Les champs Nom, Prénom et Email sont obligatoires.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "Le format de l'adresse e-mail n'est pas valide.";
    }

    if ($email !== $user['email'] && empty($erreurs)) {
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM web2026_Utilisateur WHERE email = :email AND uid != :uid");
        $stmt_check->execute(['email' => $email, 'uid' => $user_id]);
        if ($stmt_check->fetchColumn() > 0) {
            $erreurs[] = "Cette adresse e-mail est déjà utilisée par un autre compte.";
        }
    }

    if (!empty($nouveau_mdp)) {
        if ($nouveau_mdp !== $conf_mdp) {
            $erreurs[] = "Les nouveaux mots de passe ne correspondent pas.";
        }
    }

    // MISE À JOUR DANS LA BASE DE DONNÉES
    if (empty($erreurs)) {
        if (!empty($nouveau_mdp)) {
            $mdp_hashe = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
            $sql_update = "UPDATE web2026_Utilisateur SET nom = :nom, prenom = :prenom, nom_artiste = :nom_artiste, email = :email, description = :description, mot_passe_hashe = :mdp WHERE uid = :uid";
            $stmt_update = $pdo->prepare($sql_update);
            $succes = $stmt_update->execute([
                'nom' => $nom, 'prenom' => $prenom, 'nom_artiste' => $nom_artiste,
                'email' => $email, 'description' => $description, 'mdp' => $mdp_hashe, 'uid' => $user_id
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

include 'header-footer/header.php';
?>

    <main class="page-prestations">
        <div class="center profile-edit-header">
            <a href="dashboard.php" class="back-link">← Retour au tableau de bord</a>
            <h1 class="profile-edit-title">Éditer mon profil</h1>
            <p class="profile-edit-subtitle">Mettez à jour vos informations de pilote.</p>
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

        <div class="form-container">
            <form action="editerProfil.php" method="post" class="space-form">

                <div class="form-group">
                    <label for="nom">Nom *</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars(isset($user['nom']) ? $user['nom'] : '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="prenom">Prénom *</label>
                    <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars(isset($user['prenom']) ? $user['prenom'] : '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="nom_artiste">Nom d'artiste / Pseudo</label>
                    <input type="text" id="nom_artiste" name="nom_artiste" value="<?= htmlspecialchars(isset($user['nom_artiste']) ? $user['nom_artiste'] : '') ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email de liaison *</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars(isset($user['email']) ? $user['email'] : '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Biographie / Description</label>
                    <textarea id="description" name="description" rows="5"><?= htmlspecialchars(isset($user['description']) ? $user['description'] : '') ?></textarea>
                </div>

                <h3 class="password-section-title">Sécurité</h3>
                <span class="helper-text">Laissez ces champs vides si vous ne souhaitez pas modifier votre mot de passe actuel.</span>

                <div class="form-group">
                    <label for="nouveau_mdp">Nouveau mot de passe</label>
                    <input type="password" id="nouveau_mdp" name="nouveau_mdp" placeholder="********">
                </div>

                <div class="form-group">
                    <label for="conf_mdp">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="conf_mdp" name="conf_mdp" placeholder="********">
                </div>

                <button type="submit" class="btn-primary">Mettre à jour mes informations</button>
            </form>
        </div>
    </main>

<?php include 'header-footer/footer.php'; ?>