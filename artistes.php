<?php
require_once 'config/db.php';

include 'header-footer/header.php';
?>
<main class="page-artistes">

  <div class="center">
    <h1>Nos Pilotes Experts</h1>
    <p>Découvrez les esprits brillants qui vont vous guider à travers la galaxie du code.</p>
  </div>

  <div class="filter-section">
    <label class="checkbox-container">
      <input type="checkbox" id="filter-programmed">
      <span class="checkmark"></span>
      Afficher uniquement les pilotes programmés
    </label>
  </div>
    <div class="artists-grid">
        <?php
        // 1. On récupère tous les artistes (ceux qui ne sont pas organisateurs).
        $sql_artistes = "SELECT * FROM web2026_Utilisateur WHERE est_organisateur = 0";
        $stmt_artistes = $pdo->query($sql_artistes);
        $artistes = $stmt_artistes->fetchAll();

        foreach ($artistes as $artiste):
            // Création des initiales
            $initiales = strtoupper(substr($artiste['prenom'], 0, 1) . substr($artiste['nom'], 0, 1));

            // 2. On cherche les missions planifiées POUR CET ARTISTE UNIQUEMENT
            $sql_missions = "
            SELECT p.heure_debut, s.nom_scene, pr.intitule 
            FROM web2026_Programmation p
            JOIN web2026_Prestation pr ON p.prestation_id = pr.pid
            JOIN web2026_Scene s ON p.scene_id = s.sid
            WHERE pr.artiste_id = :artiste_id
            ORDER BY p.heure_debut ASC
        ";
            // On utilise prepare() car on injecte une variable (artiste_id) en toute sécurité
            $stmt_missions = $pdo->prepare($sql_missions);
            $stmt_missions->execute(['artiste_id' => $artiste['uid']]);
            $missions = $stmt_missions->fetchAll();

            // 3. L'artiste est-il programmé ? (A-t-il au moins une mission ?)
            $est_programme = (count($missions) > 0);

            // On définit la classe de la carte (transparente s'il n'est pas programmé).
            $classe_carte = $est_programme ? "artist-card" : "artist-card not-programmed";

            // 4. Gestion de la couleur de l'avatar (Le cercle)
            $style_avatar = "";
            if (!$est_programme) {
                // Gris s'il n'a pas de mission
                $style_avatar = 'style="border-color: #94a3b8; color: #94a3b8;"';
            } else {
                // Couleur basée sur sa première salle
                $premiere_salle = strtolower($missions[0]['nom_scene']);
                if (strpos($premiere_salle, 'mars') !== false) {
                    $style_avatar = 'style="border-color: #f97316;"'; // Orange Mars
                } elseif (strpos($premiere_salle, 'saturne') !== false) {
                    $style_avatar = 'style="border-color: #10b981;"'; // Vert Saturne
                } else {
                    $style_avatar = 'style="border-color: #a855f7;"'; // Violet Jupiter par défaut
                }
            }
            ?>

            <a href="detailsArtiste.php?id=<?= $artiste['uid'] ?>" class="card-link">
                <article class="<?= $classe_carte ?>">

                    <div class="artist-photo">
                        <div class="avatar-placeholder big" <?= $style_avatar ?>><?= htmlspecialchars($initiales) ?></div>
                    </div>

                    <div class="artist-content">
                        <h2><?= htmlspecialchars($artiste['nom_artiste'] ?: $artiste['prenom'].' '.$artiste['nom']) ?></h2>
                        <p class="artist-role"><?= htmlspecialchars($artiste['role'] ?: 'Pilote Expert') ?></p>
                        <p class="artist-bio"><?= htmlspecialchars($artiste['description']) ?></p>

                        <?php if ($est_programme): ?>
                            <div class="artist-schedule">
                                <h3>📅 Missions planifiées :</h3>
                                <ul>
                                    <?php foreach ($missions as $mission):
                                        $heure = substr($mission['heure_debut'], 0, 5);
                                        ?>
                                        <li>
                                            <time class="schedule-time"><?= htmlspecialchars($heure) ?></time>
                                            <span class="schedule-room"><?= htmlspecialchars($mission['nom_scene']) ?></span>
                                            <span class="schedule-title"><?= htmlspecialchars($mission['intitule']) ?></span>
                                        </li>

                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php else: ?>
                            <div class="no-schedule">
                                <em>🚫 Aucune mission planifiée pour le moment.</em>
                            </div>
                        <?php endif; ?>

                    </div>
                </article>
            </a>

        <?php endforeach; ?>
    </div>
</main>
<?php
include 'header-footer/footer.php';
?>