<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);

// UC-A.1 : Chargement du pseudo et de la photo depuis la session
$nom_affichage_nav = $_SESSION['nom_affichage'] ?? '';
$photo_nav         = $_SESSION['photo_url']     ?? '';
$initiales_nav     = $_SESSION['initiales']     ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>DevGalaxy</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<header>
    <div class="space-background">
        <div class="diagonal-lines">
            <div class="line-1"></div>
            <div class="line-2"></div>
        </div>
        <div class="star" style="left:23%;top:12%;width:2px;height:2px;opacity:.8"></div>
        <div class="star" style="left:67%;top:5%;width:1.5px;height:1.5px;opacity:.6"></div>
        <div class="star" style="left:45%;top:89%;width:2.5px;height:2.5px;opacity:.9"></div>
        <div class="star" style="left:82%;top:23%;width:1px;height:1px;opacity:.5"></div>
        <div class="star" style="left:8%;top:56%;width:2px;height:2px;opacity:.7"></div>
        <div class="star" style="left:91%;top:78%;width:1.5px;height:1.5px;opacity:.8"></div>
        <div class="star" style="left:34%;top:34%;width:2px;height:2px;opacity:.6"></div>
        <div class="star" style="left:56%;top:67%;width:1px;height:1px;opacity:.9"></div>
        <div class="star" style="left:78%;top:45%;width:2px;height:2px;opacity:.5"></div>
        <div class="star" style="left:43%;top:21%;width:1.5px;height:1.5px;opacity:.8"></div>
        <div class="star" style="left:65%;top:82%;width:2px;height:2px;opacity:.6"></div>
        <div class="decorative plus" style="left:12%;top:15%;width:20px;height:20px"></div>
        <div class="decorative square" style="left:15%;top:30%;width:12px;height:12px"></div>
        <div class="decorative circle" style="left:95%;top:35%;width:8px;height:8px"></div>
    </div>
    <div class="logoTitre">
        <img src="assets/img/logo.png" alt="Logo DevGalaxy">
        <h2>Dev Galaxy</h2>
    </div>
    <nav aria-label="Menu principal">
        <ul>
            <li><a href="index.php" class="<?= ($current_page=='index.php')?'active':'' ?>">Accueil</a></li>
            <li><a href="artistes.php" class="<?= ($current_page=='artistes.php')?'active':'' ?>">Artistes</a></li>
            <li><a href="prestations.php" class="<?= ($current_page=='prestations.php')?'active':'' ?>">Prestations</a></li>

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (!empty($_SESSION['est_organisateur'])): ?>
                    <li><a href="gestionProgramme.php" class="<?= ($current_page=='gestionProgramme.php')?'active':'' ?>">Programme</a></li>
                    <li><a href="gererArtistes.php" class="<?= ($current_page=='gererArtistes.php')?'active':'' ?>">Gérer Artistes</a></li>
                    <li><a href="editerProfil.php" class="<?= ($current_page=='editerProfil.php')?'active':'' ?>">Mon profil</a></li>
                <?php else: ?>
                    <li><a href="dashboard.php" class="<?= ($current_page=='dashboard.php')?'active':'' ?>">Mon Espace</a></li>
                <?php endif; ?>

                <!-- UC-A.1 : Pseudo + photo à la place du bouton de connexion -->
                <li class="nav-user-info">
                    <?php if ($photo_nav): ?>
                        <img src="<?= htmlspecialchars($photo_nav) ?>"
                             alt="Photo de profil"
                             class="nav-avatar-img">
                    <?php else: ?>
                        <span class="nav-avatar-initiales"><?= htmlspecialchars($initiales_nav) ?></span>
                    <?php endif; ?>
                    <span class="nav-pseudo"><?= htmlspecialchars($nom_affichage_nav) ?></span>
                </li>
                <li><a href="deconnexion.php" class="btn-inscription">Déconnexion</a></li>

            <?php else: ?>
                <li><a href="connexion.php" class="<?= ($current_page=='connexion.php')?'active':'' ?>">Connexion</a></li>
                <li><a href="inscription.php" class="btn-inscription <?= ($current_page=='inscription.php')?'active':'' ?>">Inscription</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
