<?php

use Model\Programmation;
use Model\Scene;

session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['est_organisateur'])) {
    header("Location: connexion.php");
    exit;
}

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/app/Model/Programmation.php';
require_once __DIR__ . '/app/Model/Scene.php';

$message_succes = "";
$message_erreur = "";

// Gestion des messages de retour de planifier.php
if (isset($_GET['succes'])) $message_succes = "La prestation a été ajoutée au programme avec succès.";
if (isset($_GET['erreur']) && $_GET['erreur'] === 'conflit')
    $message_erreur = "Conflit de planification : l'artiste ou la scène n'était plus disponible. Veuillez réessayer.";

// --- DÉPROGRAMMATION (UC-D.2) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'deprogrammer') {
    $prog_id = intval($_POST['prog_id']);
    $titre   = Programmation::getTitreByProgId($prog_id);
    if (Programmation::delete($prog_id)) {
        $message_succes = "La prestation \"" . htmlspecialchars($titre) . "\" a été retirée du programme avec succès.";
    } else {
        $message_erreur = "Une erreur est survenue lors de la déprogrammation.";
    }
}

$scenes   = Scene::findAll();
$grille   = Programmation::buildGrille();
$heures_possibles = ['10:00:00','11:00:00','12:00:00','13:00:00','14:00:00','15:00:00','16:00:00','17:00:00','18:00:00'];
$creneaux_dispo   = Programmation::hasCreneauDispo($heures_possibles, count($scenes));

include 'app/View/header-footer/header.php';
?>

<main class="page-prestations">
    <div class="center">
        <h1>Gestion du Programme</h1>
        <p>Vue interactive du programme de la journée.</p>
    </div>

    <div class="alert-container">
        <?php if ($message_succes): ?>
            <div class="alert-success"><?= $message_succes ?></div>
        <?php endif; ?>
        <?php if ($message_erreur): ?>
            <div class="alert-error"><?= htmlspecialchars($message_erreur) ?></div>
        <?php endif; ?>
    </div>

    <div class="center org-actions">
        <?php if ($creneaux_dispo): ?>
            <a href="planifier.php" class="btn-primary">+ Planifier une prestation</a>
        <?php else: ?>
            <span class="btn-primary btn-disabled">Tous les créneaux sont complets</span>
        <?php endif; ?>
    </div>

    <div class="table-wrapper">
        <?php if (empty($grille)): ?>
            <p class="no-results">Aucune prestation n'est encore programmée. Utilisez le bouton ci-dessus pour commencer.</p>
        <?php else: ?>
            <table class="events-table schedule-grid">
                <caption class="sr-only">Grille horaire interactive du programme</caption>
                <thead>
                    <tr>
                        <th scope="col">Heure</th>
                        <?php foreach ($scenes as $scene): ?>
                            <th scope="col"><?= htmlspecialchars($scene['nom_scene']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($grille as $heure => $events_par_scene): ?>
                        <tr>
                            <td class="time"><strong><?= htmlspecialchars($heure) ?></strong></td>
                            <?php foreach ($scenes as $scene): ?>
                                <td class="schedule-cell">
                                    <?php if (isset($events_par_scene[$scene['sid']])): ?>
                                        <?php $e = $events_par_scene[$scene['sid']];
                                        $pilote = !empty($e['nom_artiste']) ? $e['nom_artiste'] : $e['prenom'].' '.$e['nom']; ?>
                                        <div class="schedule-event-admin">
                                            <strong><?= htmlspecialchars($e['mission']) ?></strong>
                                            <span><?= htmlspecialchars($pilote) ?></span>
                                            <!-- Déprogrammation sans JS : formulaire vers page de confirmation PHP -->
                                            <form method="post" action="confirmerDeprogrammation.php" class="schedule-cell-form">
                                                <input type="hidden" name="prog_id" value="<?= $e['prog_id'] ?>">
                                                <input type="hidden" name="mission" value="<?= htmlspecialchars($e['mission']) ?>">
                                                <button type="submit" class="btn-primary btn-danger btn-small">Déprogrammer</button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span class="cell-libre">—</span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</main>

<?php include 'app/View/header-footer/footer.php'; ?>
