<?php

use Model\Utilisateur;

session_start();

// UC-A.1 : Si déjà connecté, rediriger vers le bon espace (pas index.php)
if (isset($_SESSION['user_id'])) {
    header("Location: " . (!empty($_SESSION['est_organisateur']) ? "gestionProgramme.php" : "dashboard.php"));
    exit;
}

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/app/Model/Utilisateur.php';

$email   = '';
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $mdp   = $_POST['mdp'];

    if (empty($email) || empty($mdp)) {
        $erreurs[] = "Veuillez remplir tous les champs.";
    }

    if (empty($erreurs)) {
        $utilisateur = Utilisateur::findByEmail($email);

        if ($utilisateur && $utilisateur['mot_passe_hashe'] === $mdp) {
            $_SESSION['user_id']          = $utilisateur['uid'];
            $_SESSION['est_organisateur'] = $utilisateur['est_organisateur'];

            // UC-A.1 : Stockage du pseudo et de la photo en session pour le header
            $_SESSION['nom_affichage'] = Utilisateur::getNomAffichage($utilisateur);
            $_SESSION['photo_url']     = Utilisateur::getPhotoUrl($utilisateur);
            $_SESSION['initiales']     = strtoupper(
                substr($utilisateur['prenom'], 0, 1) . substr($utilisateur['nom'], 0, 1)
            );

            header("Location: " . ($utilisateur['est_organisateur'] ? "gestionProgramme.php" : "dashboard.php"));
            exit;
        } else {
            $erreurs[] = "L'adresse e-mail ou le mot de passe est incorrect.";
        }
    }
}

include 'app/View/header-footer/header.php';
?>

<main class="inscription-section">
    <div class="container-contact">
        <div class="inscription-form-wrapper">
            <form action="connexion.php" method="post" class="space-form" aria-label="Formulaire de connexion">
                <fieldset class="fieldset-reset">
                    <legend class="sr-only">Connectez-vous à votre espace personnel</legend>
                    <h1 class="u-text-left u-mb-20">Connexion au Vaisseau</h1>

                    <?php if (!empty($erreurs)): ?>
                        <div class="alert">
                            <ul>
                                <?php foreach ($erreurs as $e): ?>
                                    <li><?= htmlspecialchars($e) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="email">Email de liaison *</label>
                        <input type="email" id="email" name="email"
                               placeholder="nom@domaine.com"
                               value="<?= htmlspecialchars($email) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="mdp">Mot de passe *</label>
                        <input type="password" id="mdp" name="mdp" placeholder="********" required>
                    </div>

                    <button type="submit" class="btn-primary">S'authentifier 🚀</button>
                </fieldset>
            </form>
        </div>
    </div>
</main>

<?php include 'app/View/header-footer/footer.php'; ?>
