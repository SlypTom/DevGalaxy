<?php
// 1. SÉCURITÉ : On vérifie que l'utilisateur est bien connecté
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

// 2. Connexion à la base de données
require_once 'config/db.php';

// 3. Récupération des informations de l'utilisateur connecté
$user_id = $_SESSION['user_id'];
$stmt_user = $pdo->prepare("SELECT * FROM web2026_Utilisateur WHERE uid = :uid");
$stmt_user->execute(['uid' => $user_id]);
$user = $stmt_user->fetch();

$nom_affichage = !empty($user['nom_artiste']) ? $user['nom_artiste'] : $user['prenom'];
$initiales = strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1));

// 4. Récupération de sa programmation personnelle
$sql_planning = "
    SELECT p.heure_debut, pr.intitule, s.nom_scene 
    FROM web2026_Programmation p
    JOIN web2026_Prestation pr ON p.prestation_id = pr.pid
    JOIN web2026_Scene s ON p.scene_id = s.sid
    WHERE pr.artiste_id = :uid
    ORDER BY p.heure_debut ASC
";
$stmt_planning = $pdo->prepare($sql_planning);
$stmt_planning->execute(['uid' => $user_id]);
$planning = $stmt_planning->fetchAll();

include 'header-footer/header.php';
?>

    <main class="inscription-section">
        <div class="container-contact dashboard-container">

            <div class="artist-photo dashboard-photo">
                <div class="avatar-placeholder big-profile dashboard-avatar"><?= htmlspecialchars($initiales) ?></div>
            </div>

            <h1 class="dashboard-title">Bienvenue à bord, <?= htmlspecialchars($nom_affichage) ?> !</h1>
            <p class="dashboard-subtitle">Ceci est votre tableau de bord personnel.</p>

            <div class="artist-schedule dashboard-schedule">
                <h2>📅 Votre Planning Officiel</h2>

                <?php if (count($planning) > 0): ?>
                    <ul class="dashboard-schedule-list">
                        <?php foreach ($planning as $mission):
                            $heure = substr($mission['heure_debut'], 0, 5);
                            ?>
                            <li class="dashboard-schedule-item">
                                <div class="mission-details">
                                    <strong><?= htmlspecialchars($mission['intitule']) ?></strong>
                                    <span>📍 <?= htmlspecialchars($mission['nom_scene']) ?></span>
                                </div>
                                <span class="time mission-time"><?= htmlspecialchars($heure) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="no-schedule-box">
                        <p>Vous n'êtes pas encore dans le programme officiel. L'organisateur met le planning à jour régulièrement !</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="dashboard-actions">
                <a href="gererPrestations.php" class="btn-primary">Gérer mon catalogue</a>
                <a href="editerProfil.php" class="btn-primary btn-outline">Éditer mon profil</a>
            </div>

        </div>
    </main>

<?php
include 'header-footer/footer.php';
?>