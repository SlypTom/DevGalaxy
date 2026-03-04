<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// On récupère le nom du fichier en cours de lecture
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>DevGalaxy</title>
    <link rel="stylesheet" href="../css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<header>
    <div class="space-background">
        <!-- Diagonal lines -->
        <div class="diagonal-lines">
            <div class="line-1"></div>
            <div class="line-2"></div>
        </div>

        <!-- Stars (80 stars with random positions) -->
        <div class="star" style="left: 23%; top: 12%; width: 2px; height: 2px; opacity: 0.8;"></div>
        <div class="star" style="left: 67%; top: 5%; width: 1.5px; height: 1.5px; opacity: 0.6;"></div>
        <div class="star" style="left: 45%; top: 89%; width: 2.5px; height: 2.5px; opacity: 0.9;"></div>
        <div class="star" style="left: 82%; top: 23%; width: 1px; height: 1px; opacity: 0.5;"></div>
        <div class="star" style="left: 8%; top: 56%; width: 2px; height: 2px; opacity: 0.7;"></div>
        <div class="star" style="left: 91%; top: 78%; width: 1.5px; height: 1.5px; opacity: 0.8;"></div>
        <div class="star" style="left: 34%; top: 34%; width: 2px; height: 2px; opacity: 0.6;"></div>
        <div class="star" style="left: 56%; top: 67%; width: 1px; height: 1px; opacity: 0.9;"></div>
        <div class="star" style="left: 12%; top: 90%; width: 2.5px; height: 2.5px; opacity: 0.7;"></div>
        <div class="star" style="left: 78%; top: 45%; width: 2px; height: 2px; opacity: 0.5;"></div>
        <div class="star" style="left: 43%; top: 21%; width: 1.5px; height: 1.5px; opacity: 0.8;"></div>
        <div class="star" style="left: 65%; top: 82%; width: 2px; height: 2px; opacity: 0.6;"></div>
        <div class="star" style="left: 29%; top: 58%; width: 1px; height: 1px; opacity: 0.9;"></div>
        <div class="star" style="left: 87%; top: 14%; width: 2.5px; height: 2.5px; opacity: 0.7;"></div>
        <div class="star" style="left: 5%; top: 73%; width: 2px; height: 2px; opacity: 0.5;"></div>
        <div class="star" style="left: 52%; top: 38%; width: 1.5px; height: 1.5px; opacity: 0.8;"></div>
        <div class="star" style="left: 71%; top: 91%; width: 2px; height: 2px; opacity: 0.6;"></div>
        <div class="star" style="left: 19%; top: 27%; width: 1px; height: 1px; opacity: 0.9;"></div>
        <div class="star" style="left: 94%; top: 52%; width: 2.5px; height: 2.5px; opacity: 0.7;"></div>
        <div class="star" style="left: 37%; top: 8%; width: 2px; height: 2px; opacity: 0.5;"></div>
        <div class="star" style="left: 61%; top: 64%; width: 1.5px; height: 1.5px; opacity: 0.8;"></div>
        <div class="star" style="left: 25%; top: 48%; width: 2px; height: 2px; opacity: 0.6;"></div>
        <div class="star" style="left: 83%; top: 31%; width: 1px; height: 1px; opacity: 0.9;"></div>
        <div class="star" style="left: 48%; top: 76%; width: 2.5px; height: 2.5px; opacity: 0.7;"></div>
        <div class="star" style="left: 7%; top: 19%; width: 2px; height: 2px; opacity: 0.5;"></div>
        <div class="star" style="left: 73%; top: 55%; width: 1.5px; height: 1.5px; opacity: 0.8;"></div>
        <div class="star" style="left: 32%; top: 93%; width: 2px; height: 2px; opacity: 0.6;"></div>
        <div class="star" style="left: 88%; top: 42%; width: 1px; height: 1px; opacity: 0.9;"></div>
        <div class="star" style="left: 54%; top: 11%; width: 2.5px; height: 2.5px; opacity: 0.7;"></div>
        <div class="star" style="left: 16%; top: 69%; width: 2px; height: 2px; opacity: 0.5;"></div>
        <div class="star" style="left: 76%; top: 26%; width: 1.5px; height: 1.5px; opacity: 0.8;"></div>
        <div class="star" style="left: 41%; top: 84%; width: 2px; height: 2px; opacity: 0.6;"></div>
        <div class="star" style="left: 63%; top: 37%; width: 1px; height: 1px; opacity: 0.9;"></div>
        <div class="star" style="left: 22%; top: 61%; width: 2.5px; height: 2.5px; opacity: 0.7;"></div>
        <div class="star" style="left: 85%; top: 7%; width: 2px; height: 2px; opacity: 0.5;"></div>
        <div class="star" style="left: 49%; top: 50%; width: 1.5px; height: 1.5px; opacity: 0.8;"></div>
        <div class="star" style="left: 11%; top: 88%; width: 2px; height: 2px; opacity: 0.6;"></div>
        <div class="star" style="left: 69%; top: 33%; width: 1px; height: 1px; opacity: 0.9;"></div>
        <div class="star" style="left: 35%; top: 72%; width: 2.5px; height: 2.5px; opacity: 0.7;"></div>
        <div class="star" style="left: 92%; top: 17%; width: 2px; height: 2px; opacity: 0.5;"></div>
        <div class="star" style="left: 58%; top: 59%; width: 1.5px; height: 1.5px; opacity: 0.8;"></div>
        <div class="star" style="left: 27%; top: 95%; width: 2px; height: 2px; opacity: 0.6;"></div>
        <div class="star" style="left: 80%; top: 44%; width: 1px; height: 1px; opacity: 0.9;"></div>
        <div class="star" style="left: 44%; top: 15%; width: 2.5px; height: 2.5px; opacity: 0.7;"></div>
        <div class="star" style="left: 9%; top: 66%; width: 2px; height: 2px; opacity: 0.5;"></div>
        <div class="star" style="left: 66%; top: 29%; width: 1.5px; height: 1.5px; opacity: 0.8;"></div>
        <div class="star" style="left: 31%; top: 81%; width: 2px; height: 2px; opacity: 0.6;"></div>
        <div class="star" style="left: 89%; top: 36%; width: 1px; height: 1px; opacity: 0.9;"></div>
        <div class="star" style="left: 53%; top: 70%; width: 2.5px; height: 2.5px; opacity: 0.7;"></div>
        <div class="star" style="left: 17%; top: 24%; width: 2px; height: 2px; opacity: 0.5;"></div>
        <div class="star" style="left: 74%; top: 57%; width: 1.5px; height: 1.5px; opacity: 0.8;"></div>
        <div class="star" style="left: 38%; top: 92%; width: 2px; height: 2px; opacity: 0.6;"></div>
        <div class="star" style="left: 96%; top: 41%; width: 1px; height: 1px; opacity: 0.9;"></div>
        <div class="star" style="left: 51%; top: 9%; width: 2.5px; height: 2.5px; opacity: 0.7;"></div>
        <div class="star" style="left: 14%; top: 63%; width: 2px; height: 2px; opacity: 0.5;"></div>
        <div class="star" style="left: 77%; top: 20%; width: 1.5px; height: 1.5px; opacity: 0.8;"></div>
        <div class="star" style="left: 40%; top: 77%; width: 2px; height: 2px; opacity: 0.6;"></div>
        <div class="star" style="left: 62%; top: 32%; width: 1px; height: 1px; opacity: 0.9;"></div>
        <div class="star" style="left: 26%; top: 54%; width: 2.5px; height: 2.5px; opacity: 0.7;"></div>
        <div class="star" style="left: 84%; top: 10%; width: 2px; height: 2px; opacity: 0.5;"></div>
        <div class="star" style="left: 46%; top: 68%; width: 1.5px; height: 1.5px; opacity: 0.8;"></div>
        <div class="star" style="left: 13%; top: 85%; width: 2px; height: 2px; opacity: 0.6;"></div>
        <div class="star" style="left: 70%; top: 39%; width: 1px; height: 1px; opacity: 0.9;"></div>
        <div class="star" style="left: 33%; top: 75%; width: 2.5px; height: 2.5px; opacity: 0.7;"></div>
        <div class="star" style="left: 90%; top: 16%; width: 2px; height: 2px; opacity: 0.5;"></div>
        <div class="star" style="left: 55%; top: 62%; width: 1.5px; height: 1.5px; opacity: 0.8;"></div>
        <div class="star" style="left: 20%; top: 97%; width: 2px; height: 2px; opacity: 0.6;"></div>
        <div class="star" style="left: 79%; top: 47%; width: 1px; height: 1px; opacity: 0.9;"></div>
        <div class="star" style="left: 42%; top: 13%; width: 2.5px; height: 2.5px; opacity: 0.7;"></div>
        <div class="star" style="left: 6%; top: 71%; width: 2px; height: 2px; opacity: 0.5;"></div>
        <div class="star" style="left: 64%; top: 28%; width: 1.5px; height: 1.5px; opacity: 0.8;"></div>
        <div class="star" style="left: 30%; top: 86%; width: 2px; height: 2px; opacity: 0.6;"></div>
        <div class="star" style="left: 86%; top: 35%; width: 1px; height: 1px; opacity: 0.9;"></div>
        <div class="star" style="left: 50%; top: 74%; width: 2.5px; height: 2.5px; opacity: 0.7;"></div>
        <div class="star" style="left: 15%; top: 22%; width: 2px; height: 2px; opacity: 0.5;"></div>
        <div class="star" style="left: 72%; top: 60%; width: 1.5px; height: 1.5px; opacity: 0.8;"></div>
        <div class="star" style="left: 36%; top: 94%; width: 2px; height: 2px; opacity: 0.6;"></div>
        <div class="star" style="left: 93%; top: 43%; width: 1px; height: 1px; opacity: 0.9;"></div>

        <!-- Plus signs -->
        <div class="decorative plus" style="left: 12%; top: 15%; width: 20px; height: 20px;"></div>
        <div class="decorative plus" style="left: 18%; top: 45%; width: 16px; height: 16px;"></div>
        <div class="decorative plus" style="left: 88%; top: 65%; width: 20px; height: 20px;"></div>

        <!-- Cross (4 squares) -->
        <div class="decorative cross" style="left: 10%; top: 10%; width: 24px; height: 24px;">
            <div class="cross-square"></div>
            <div class="cross-square"></div>
            <div class="cross-square"></div>
            <div class="cross-square"></div>
        </div>

        <!-- Squares -->
        <div class="decorative square" style="left: 15%; top: 30%; width: 12px; height: 12px;"></div>
        <div class="decorative square" style="left: 60%; top: 70%; width: 12px; height: 12px;"></div>
        <div class="decorative square" style="left: 95%; top: 88%; width: 8px; height: 8px;"></div>

        <!-- Circles -->
        <div class="decorative circle" style="left: 14%; top: 45%; width: 6px; height: 6px;"></div>
        <div class="decorative circle" style="left: 95%; top: 35%; width: 8px; height: 8px;"></div>

        <!-- Lines groups -->
        <div class="decorative lines-group" style="left: 28%; top: 15%;">
            <div class="line-h" style="width: 32px;"></div>
            <div class="line-h" style="width: 24px;"></div>
            <div class="line-h" style="width: 32px;"></div>
        </div>
        <div class="decorative lines-group" style="left: 70%; top: 68%;">
            <div class="line-h" style="width: 32px;"></div>
            <div class="line-h" style="width: 24px;"></div>
            <div class="line-h" style="width: 32px;"></div>
        </div>

        <!-- Text -->
        <div class="decorative text-element" style="left: 3%; top: 35%;">5 9</div>
    </div>
    <div class="logoTitre">
        <img src="img/logo.png" alt="Logo officiel de la convention DevGalaxy">
        <h2>Dev Galaxy</h2>
    </div>
    <nav aria-label="Menu principal">
        <ul>
            <li>
                <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Accueil</a>
            </li>
            <li>
                <a href="artistes.php" class="<?php echo ($current_page == 'artistes.php') ? 'active' : ''; ?>">Artistes</a>
            </li>
            <li>
                <a href="prestations.php" class="<?php echo ($current_page == 'prestations.php') ? 'active' : ''; ?>">Prestations</a>
            </li>
            <li>
                <a href="contact.php" class="<?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">Contact</a>
            </li>
            <?php if (isset($_SESSION['user_id'])): // SI L'UTILISATEUR EST CONNECTÉ ?>
                <li><a href="dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">Mon Espace</a></li>
                <li><a href="deconnexion.php" class="btn-inscription">Déconnexion</a></li>
            <?php else: // SI L'UTILISATEUR N'EST PAS CONNECTÉ ?>
                <li><a href="connexion.php" class="<?= ($current_page == 'connexion.php') ? 'active' : '' ?>">Connexion</a></li>
                <li><a href="inscription.php" class="btn-inscription <?= ($current_page == 'inscription.php') ? 'active' : '' ?>">Inscription</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>