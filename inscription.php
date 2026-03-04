<?php
// 1. On démarre la session (indispensable pour connecter l'utilisateur plus tard)
session_start();

// 2. On se connecte à la base de données
require_once 'config/db.php';

// Variables pour pré-remplir le formulaire en cas d'erreur
$nom = $prenom = $pseudo = $email = $description = '';
$erreurs = [];

// 3. Traitement du formulaire quand l'utilisateur clique sur "Envoyer"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération et nettoyage des données tapées
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $pseudo = trim($_POST['pseudo']);
    $description = trim($_POST['message']); // Tu avais appelé ce champ "message"
    $email = trim($_POST['email']);
    $mdp = $_POST['mdp'];
    $conf_mdp = $_POST['conf_mdp'];

    // --- SÉRIE DE VÉRIFICATIONS ---
    if (empty($nom) || empty($prenom) || empty($pseudo) || empty($email) || empty($description) || empty($mdp) || empty($conf_mdp)) {
        $erreurs[] = "Tous les champs obligatoires doivent être remplis.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "Le format de l'adresse e-mail n'est pas valide.";
    }

    if ($mdp !== $conf_mdp) {
        $erreurs[] = "Les mots de passe ne correspondent pas.";
    }

    // Vérifier si l'email existe déjà dans la base
    if (empty($erreurs)) {
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM web2026_Utilisateur WHERE email = ?");
        $stmt_check->execute([$email]);
        if ($stmt_check->fetchColumn() > 0) {
            $erreurs[] = "Cette adresse e-mail est déjà utilisée par un autre pilote.";
        }
    }

    // --- INSCRIPTION DANS LA BASE DE DONNÉES ---
    if (empty($erreurs)) {
        // Hachage du mot de passe (Cryptage de sécurité)
        $mdp_hashe = password_hash($mdp, PASSWORD_DEFAULT);

        // Préparation de la requête d'insertion
        $sql_insert = "INSERT INTO web2026_Utilisateur (nom, prenom, nom_artiste, email, mot_passe_hashe, description, est_organisateur) 
                       VALUES (:nom, :prenom, :pseudo, :email, :mdp, :desc, 0)";

        $stmt_insert = $pdo->prepare($sql_insert);
        $succes = $stmt_insert->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'pseudo' => $pseudo,
                'email' => $email,
                'mdp' => $mdp_hashe,
                'desc' => $description
        ]);

        if ($succes) {
            // Connexion automatique de l'utilisateur (On retient son ID en session)
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['est_organisateur'] = 0;

            // Redirection vers l'accueil (ou vers le futur tableau de bord)
            header("Location: index.php?inscription=succes");
            exit;
        } else {
            $erreurs[] = "Une erreur est survenue lors de l'inscription. L'anomalie a été signalée.";
        }
    }
}

// Seulement après le traitement PHP (et les redirections), on charge le visuel (header)
include 'header-footer/header.php';
?>

<main class="inscription-section">
    <div class="container-contact">

        <div class="inscription-form-wrapper">
            <form action="inscription.php" method="post" class="space-form" aria-label="Création de votre compte">
                <fieldset class="fieldset-reset">
                    <legend class="sr-only">Création de votre compte Artiste</legend>

                    <h1>Inscription Pilote</h1>

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
                        <label for="nom">Nom *</label>
                        <input type="text" id="nom" name="nom" placeholder="Ex: Shepard" value="<?= htmlspecialchars($nom) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="prenom">Prénom *</label>
                        <input type="text" id="prenom" name="prenom" placeholder="Ex: John" value="<?= htmlspecialchars($prenom) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="pseudo">Nom d'artiste (Pseudo) *</label>
                        <input type="text" id="pseudo" name="pseudo" placeholder="Ex: Commander Shepard" value="<?= htmlspecialchars($pseudo) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="message">Biographie / Description *</label>
                        <textarea id="message" name="message" rows="5" placeholder="Votre spécialité, votre parcours..." required><?= htmlspecialchars($description) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="email">Email de liaison *</label>
                        <input type="email" id="email" name="email" placeholder="nom@domaine.com" value="<?= htmlspecialchars($email) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="mdp">Mot de passe *</label>
                        <input type="password" id="mdp" name="mdp" placeholder="********" required>
                    </div>

                    <div class="form-group">
                        <label for="conf_mdp">Confirmation mot de passe *</label>
                        <input type="password" id="conf_mdp" name="conf_mdp" placeholder="********" required>
                    </div>

                    <button type="submit" class="btn-primary">S'enrôler dans l'équipage 🚀</button>
                </fieldset>
            </form>
            <br><p>* : champs obligatoires</p>
        </div>

    </div>
</main>

<?php
include 'header-footer/footer.php';
?>