<?php

use Model\Programmation;
use Model\Utilisateur;

session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/app/Model/Utilisateur.php';
require_once __DIR__ . '/app/Model/Programmation.php';

$uniquement_programmes = isset($_GET['programme']);

// UC-A.5 : Chargement des artistes selon le filtre
$artistes = $uniquement_programmes
    ? Utilisateur::findArtistesProgrammes()
    : Utilisateur::findAllArtistes();

include 'app/View/header-footer/header.php';
?>
<main class="page-artistes">

    <div class="center">
        <h1>Nos Pilotes Experts</h1>
        <p>Découvrez les esprits brillants qui vont vous guider à travers la galaxie du code.</p>
    </div>

    <!-- Filtre sans JavaScript : bouton de soumission explicite -->
    <div class="filter-section">
        <form method="get" action="artistes.php">
            <label class="checkbox-container">
                <input type="checkbox" name="programme" value="1"
                       <?= $uniquement_programmes ? 'checked' : '' ?>>
                <span class="checkmark"></span>
                Afficher uniquement les pilotes programmés
            </label>
            <button type="submit" class="btn-primary btn-small btn-filter">Filtrer</button>
        </form>
    </div>

    <div class="artists-grid">
        <?php if (count($artistes) === 0): ?>
            <p class="no-results">Aucun artiste ne correspond à votre recherche.</p>
        <?php endif; ?>

        <?php foreach ($artistes as $artiste):
            $initiales    = strtoupper(substr($artiste['prenom'], 0, 1) . substr($artiste['nom'], 0, 1));
            $nom_affiche  = Utilisateur::getNomAffichage($artiste);
            $photo_url    = Utilisateur::getPhotoUrl($artiste);

            // UC-A.5 : missions planifiées pour cet artiste
            $missions     = Programmation::findByArtiste($artiste['uid']);
            $est_programme = count($missions) > 0;
            $classe_carte  = $est_programme ? 'artist-card' : 'artist-card not-programmed';

            // Couleur de l'avatar selon la première scène
            $couleur_avatar = '#94a3b8'; // gris par défaut (non programmé)
            if ($est_programme) {
                $premiere_salle = strtolower($missions[0]['nom_scene']);
                if (strpos($premiere_salle, 'mars')    !== false) $couleur_avatar = '#f97316';
                elseif (strpos($premiere_salle, 'saturne') !== false) $couleur_avatar = '#10b981';
                else $couleur_avatar = '#a855f7';
            }
        ?>
            <a href="detailsArtiste.php?id=<?= $artiste['uid'] ?>" class="card-link">
                <article class="<?= $classe_carte ?>">

                    <div class="artist-photo">
                        <?php if ($photo_url): ?>
                            <img src="<?= htmlspecialchars($photo_url) ?>"
                                 alt="Photo de <?= htmlspecialchars($nom_affiche) ?>"
                                 class="avatar-img-real">
                        <?php else: ?>
                            <div class="avatar-placeholder big"
                                 style="border-color:<?= $couleur_avatar ?>;color:<?= $couleur_avatar ?>">
                                <?= htmlspecialchars($initiales) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="artist-content">
                        <h2><?= htmlspecialchars($nom_affiche) ?></h2>
                        <p class="artist-bio"><?= htmlspecialchars($artiste['description'] ?? '') ?></p>

                        <?php if ($est_programme): ?>
                            <div class="artist-schedule">
                                <h3>Missions planifiées :</h3>
                                <ul>
                                    <?php foreach ($missions as $mission): ?>
                                        <li>
                                            <time class="schedule-time">
                                                <?= htmlspecialchars(substr($mission['heure_debut'], 0, 5)) ?>
                                            </time>
                                            <span class="schedule-room"><?= htmlspecialchars($mission['nom_scene']) ?></span>
                                            <span class="schedule-title"><?= htmlspecialchars($mission['intitule']) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php else: ?>
                            <div class="no-schedule">
                                <em>Aucune mission planifiée pour le moment.</em>
                            </div>
                        <?php endif; ?>
                    </div>

                </article>
            </a>

        <?php endforeach; ?>
    </div>
</main>

<?php include 'header-footer/footer.php'; ?>
