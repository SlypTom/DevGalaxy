<?php

use Model\Utilisateur;

session_start();

// AA5 : Bloquer l'accès si déjà connecté
if (isset($_SESSION['user_id'])) {
    header("Location: " . (!empty($_SESSION['est_organisateur']) ? "gestionProgramme.php" : "dashboard.php"));
    exit;
}

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/app/Model/Utilisateur.php';

$nom = $prenom = $pseudo = $email = $description = '';
$erreurs_champs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom         = trim($_POST['nom']);
    $prenom      = trim($_POST['prenom']);
    $pseudo      = trim($_POST['pseudo']);
    $description = trim($_POST['message']);
    $email       = trim($_POST['email']);
    $mdp         = $_POST['mdp'];
    $conf_mdp    = $_POST['conf_mdp'];

    // Validation par champ
    if (empty($nom))    $erreurs_champs['nom']    = "Le nom est obligatoire.";
    if (empty($prenom)) $erreurs_champs['prenom'] = "Le prénom est obligatoire.";
    if (empty($pseudo)) $erreurs_champs['pseudo'] = "Le nom d'artiste est obligatoire.";

    if (empty($email)) {
        $erreurs_champs['email'] = "L'adresse e-mail est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs_champs['email'] = "Le format de l'adresse e-mail n'est pas valide.";
    } elseif (Utilisateur::emailExists($email)) {
        $erreurs_champs['email'] = "Cette adresse e-mail est déjà utilisée par un autre pilote.";
    }

    if (empty($mdp))     $erreurs_champs['mdp']      = "Le mot de passe est obligatoire.";
    if (empty($conf_mdp)) $erreurs_champs['conf_mdp'] = "La confirmation est obligatoire.";
    elseif (!empty($mdp) && $mdp !== $conf_mdp) $erreurs_champs['conf_mdp'] = "Les mots de passe ne correspondent pas.";

    // UC-A.2 : Photo de profil (champ obligatoire)
    $photo_filename = null;
    if (empty($_FILES['photo']['name'])) {
        $erreurs_champs['photo'] = "La photo de profil est obligatoire.";
    } elseif ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        $erreurs_champs['photo'] = "Erreur lors du téléchargement de la photo.";
    } else {
        $ext_photo = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext_photo, ['jpg','jpeg','png','gif','webp'])) {
            $erreurs_champs['photo'] = "Format de photo non valide (jpg, png, gif, webp).";
        } else {
            $photo_filename = uniqid('profil_') . '.' . $ext_photo;
        }
    }

    if (empty($erreurs_champs)) {
        // Déplacement de la photo
        if ($photo_filename) {
            move_uploaded_file($_FILES['photo']['tmp_name'],
                __DIR__ . '/img/profil/' . $photo_filename);
        }

        $uid = Utilisateur::create($nom, $prenom, $pseudo, $email, $mdp, $description, $photo_filename);

        if ($uid) {
            $user = Utilisateur::findById($uid);
            $_SESSION['user_id']          = $uid;
            $_SESSION['est_organisateur'] = 0;
            $_SESSION['nom_affichage']    = Utilisateur::getNomAffichage($user);
            $_SESSION['photo_url']        = Utilisateur::getPhotoUrl($user);
            $_SESSION['initiales']        = strtoupper(substr($prenom,0,1).substr($nom,0,1));

            header("Location: dashboard.php");
            exit;
        } else {
            $erreurs_champs['global'] = "Une erreur est survenue lors de l'inscription.";
        }
    }
}

include 'app/View/header-footer/header.php';
?>

<main class="inscription-section">
    <div class="container-contact">
        <div class="inscription-form-wrapper">
            <form action="inscription.php" method="post" enctype="multipart/form-data"
                  class="space-form" aria-label="Création de votre compte">
                <fieldset class="fieldset-reset">
                    <legend class="sr-only">Création de votre compte Artiste</legend>
                    <h1>Inscription Pilote</h1>

                    <?php if (isset($erreurs_champs['global'])): ?>
                        <div class="alert"><p><?= htmlspecialchars($erreurs_champs['global']) ?></p></div>
                    <?php endif; ?>

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

                    <div class="form-group <?= isset($erreurs_champs['pseudo']) ? 'field-error' : '' ?>">
                        <label for="pseudo">Nom d'artiste (Pseudo) *</label>
                        <input type="text" id="pseudo" name="pseudo" value="<?= htmlspecialchars($pseudo) ?>" required>
                        <?php if (isset($erreurs_champs['pseudo'])): ?>
                            <span class="error-message"><?= htmlspecialchars($erreurs_champs['pseudo']) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- UC-A.2 : Champ Photo obligatoire -->
                    <div class="form-group <?= isset($erreurs_champs['photo']) ? 'field-error' : '' ?>">
                        <label for="photo">Photo de profil *</label>
                        <input type="file" id="photo" name="photo" accept="image/*" required>
                        <?php if (isset($erreurs_champs['photo'])): ?>
                            <span class="error-message"><?= htmlspecialchars($erreurs_champs['photo']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="message">Biographie / Description</label>
                        <textarea id="message" name="message" rows="4"><?= htmlspecialchars($description) ?></textarea>
                    </div>

                    <div class="form-group <?= isset($erreurs_champs['email']) ? 'field-error' : '' ?>">
                        <label for="email">Email de liaison *</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                        <?php if (isset($erreurs_champs['email'])): ?>
                            <span class="error-message"><?= htmlspecialchars($erreurs_champs['email']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group <?= isset($erreurs_champs['mdp']) ? 'field-error' : '' ?>">
                        <label for="mdp">Mot de passe *</label>
                        <input type="password" id="mdp" name="mdp" required>
                        <?php if (isset($erreurs_champs['mdp'])): ?>
                            <span class="error-message"><?= htmlspecialchars($erreurs_champs['mdp']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group <?= isset($erreurs_champs['conf_mdp']) ? 'field-error' : '' ?>">
                        <label for="conf_mdp">Confirmation mot de passe *</label>
                        <input type="password" id="conf_mdp" name="conf_mdp" required>
                        <?php if (isset($erreurs_champs['conf_mdp'])): ?>
                            <span class="error-message"><?= htmlspecialchars($erreurs_champs['conf_mdp']) ?></span>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn-primary">S'enrôler dans l'équipage 🚀</button>
                </fieldset>
            </form>
            <br><p>* : champs obligatoires</p>
        </div>
    </div>
</main>

<?php include 'app/View/header-footer/footer.php'; ?>
