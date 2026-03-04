<?php
// 1. On démarre la session (toujours en tout premier !)
session_start();

// Si l'utilisateur est déjà connecté, on le renvoie vers l'accueil (ou son tableau de bord)
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// 2. On se connecte à la base de données
require_once 'config/db.php';

$email = '';
$erreurs = [];

// 3. Traitement du formulaire quand l'utilisateur clique sur "Connexion"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération des données
    $email = trim($_POST['email']);
    $mdp = $_POST['mdp'];

    // Vérification que les champs ne sont pas vides
    if (empty($email) || empty($mdp)) {
        $erreurs[] = "Veuillez remplir tous les champs."; // [cite: 325]
    }

    if (empty($erreurs)) {
        // On cherche l'utilisateur dans la base de données par son email
        $stmt = $pdo->prepare("SELECT * FROM web2026_Utilisateur WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $utilisateur = $stmt->fetch();

        // Si l'utilisateur existe ET que le mot de passe correspond
        // password_verify compare le mot de passe tapé en clair avec le mot de passe haché de la BDD
        if ($utilisateur && password_verify($mdp, $utilisateur['mot_passe_hashe'])) { //

            // On stocke les infos importantes dans la session
            $_SESSION['user_id'] = $utilisateur['uid'];
            $_SESSION['est_organisateur'] = $utilisateur['est_organisateur']; // [cite: 322]

            // Redirection vers le tableau de bord (ou accueil pour l'instant)
            header("Location: index.php?connexion=succes"); // [cite: 322]
            exit;

        } else {
            // Message d'erreur générique par sécurité (on ne dit pas si c'est l'email ou le mdp qui est faux)
            $erreurs[] = "L'adresse e-mail ou le mot de passe est incorrect."; // [cite: 328, 329]
        }
    }
}

// Seulement après le traitement, on inclut le header
include 'header-footer/header.php';
?>

    <main class="inscription-section">
        <div class="container-contact">

            <div class="inscription-form-wrapper">
                <form action="connexion.php" method="post" class="space-form" aria-label="Formulaire de connexion">
                    <fieldset style="border: none; padding: 0; margin: 0;">
                        <legend style="position: absolute; width: 1px; height: 1px; overflow: hidden;">Connectez-vous à votre espace personnel</legend>

                        <h1 style="text-align: left; color: #a855f7; margin-bottom: 20px;">Connexion au Vaisseau</h1>

                        <?php if (!empty($erreurs)): ?>
                            <div class="alert">
                                <ul>
                                    <?php foreach ($erreurs as $erreur): ?>
                                        <li><?= htmlspecialchars($erreur) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="email">Email de liaison *</label>
                            <input type="email" id="email" name="email" placeholder="nom@domaine.com" value="<?= htmlspecialchars($email) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="mdp">Mot de passe *</label>
                            <input type="password" id="mdp" name="mdp" placeholder="********" required>
                        </div>

                        <div style="margin-bottom: 20px; text-align: right;">
                            <a href="motDePasseOublie.php" style="color: #67e8f9; font-size: 0.85rem; text-decoration: underline;">Mot de passe oublié ?</a>
                        </div>

                        <button type="submit" class="btn-primary">S'authentifier 🚀</button>
                    </fieldset>
                </form>
            </div>

        </div>
    </main>

<?php
include 'header-footer/footer.php';
?>