<?php
// Affichage des erreurs PHP (à retirer en production)
use Model\Utilisateur;

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['est_organisateur'])) {
    header("Location: connexion.php");
    exit;
}

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/app/Model/Utilisateur.php';

$message_succes = "";
$message_erreur = "";

// --- SUPPRESSION D'UN ARTISTE (UC-D.3) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'supprimer') {
    $uid_a_supprimer = intval($_POST['artiste_uid']);
    $confirmation    = isset($_POST['je_comprends']);

    if (!$confirmation) {
        $message_erreur = "Vous devez cocher la case de confirmation avant de supprimer.";
    } elseif ($uid_a_supprimer === intval($_SESSION['user_id'])) {
        $message_erreur = "Impossible de supprimer votre propre compte.";
    } else {
        if (Utilisateur::delete($uid_a_supprimer)) {
            $message_succes = "L'artiste et toutes ses données ont été supprimés avec succès.";
        } else {
            $message_erreur = "Une erreur est survenue lors de la suppression.";
        }
    }
}

try {
    $artistes = Utilisateur::findAllWithStats();
} catch (Exception $e) {
    die("Erreur base de données : " . $e->getMessage());
}

include 'app/View/header-footer/header.php';
?>

<main class="page-prestations">
    <div class="center">
        <h1>Gestion des Artistes</h1>
        <p>Liste complète des artistes inscrits sur la plateforme.</p>
    </div>

    <div class="alert-container">
        <?php if ($message_succes): ?>
            <div class="alert-success"><?= htmlspecialchars($message_succes) ?></div>
        <?php endif; ?>
        <?php if ($message_erreur): ?>
            <div class="alert-error"><?= htmlspecialchars($message_erreur) ?></div>
        <?php endif; ?>
    </div>

    <div class="table-wrapper">
        <?php if (empty($artistes)): ?>
            <p class="no-results">Aucun artiste inscrit pour le moment.</p>
        <?php else: ?>
            <table class="events-table admin-table">
                <thead>
                    <tr>
                        <th>Nom d'artiste</th>
                        <th>Nom complet</th>
                        <th>Prestations</th>
                        <th>Programmées</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($artistes as $a):
                        $nom_affiche = Utilisateur::getNomAffichage($a);
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($nom_affiche) ?></strong></td>
                        <td><?= htmlspecialchars($a['prenom'].' '.$a['nom']) ?></td>
                        <td><?= $a['nb_prestations'] ?></td>
                        <td><?= $a['nb_programmes'] ?></td>
                        <td class="td-actions">
                            <a href="gererArtisteAdmin.php?id=<?= $a['uid'] ?>"
                               class="btn-primary btn-outline btn-small">Gérer</a>
                            <!-- Suppression sans JS : formulaire vers page de confirmation PHP -->
                            <form method="post" action="confirmerSuppressionArtiste.php">
                                <input type="hidden" name="artiste_uid" value="<?= $a['uid'] ?>">
                                <input type="hidden" name="nom_artiste" value="<?= htmlspecialchars($nom_affiche) ?>">
                                <button type="submit" class="btn-primary btn-danger btn-small">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</main>

<?php include 'app/View/header-footer/footer.php'; ?>
