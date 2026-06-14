<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/app/Model/Programmation.php';
require_once __DIR__ . '/app/Model/Scene.php';

include 'app/View/header-footer/header.php';

?>
    <main>
        <section class="presentation">
            <div class="center">
                <img src="assets/img/logo.png" alt="Logo officiel de la convention DevGalaxy">
                <h1>Dev Galaxy</h1>
                <p>Codez au-delà de l'atmosphère. L'avenir du développement web commence ici.</p>
                <p>Une odyssée technologique de 24h pour explorer les frontières du Backend, du Frontend et de l'Intelligence Artificielle.</p>
                <p>Bienvenue à bord de DevGalaxy 2025, la convention ultime pour les développeurs qui refusent de garder les pieds sur terre. <br>Dans un écosystème numérique en constante expansion, il ne suffit plus de connaître son langage ; il faut comprendre l'univers qui l'entoure.

                    <br>Cette année, nous transformons le Space Center en un véritable vaisseau amiral de l'innovation. <br>Que vous soyez un architecte de données naviguant dans le cloud ou un designer sculptant des interfaces en apesanteur, DevGalaxy est votre station de ravitaillement.</p>
            </div>
        </section>

        <section class="programme-section">
            <div class="center">
                <h2>Programme de vol</h2>

                <div class="table-wrapper">
                    <?php
                    // Récupération de toutes les scènes (colonnes)
                    $scenes = $pdo->query("SELECT * FROM web2026_Scene ORDER BY sid ASC")->fetchAll();

                    // Récupération du programme complet
                    $sql_programme = "
                    SELECT prog.heure_debut, prog.scene_id, prog.prestation_id,
                           pr.pid, pr.intitule AS mission,
                           u.nom_artiste, u.prenom, u.nom
                    FROM web2026_Programmation prog
                    JOIN web2026_Prestation pr ON prog.prestation_id = pr.pid
                    JOIN web2026_Utilisateur u ON pr.artiste_id = u.uid
                    ORDER BY prog.heure_debut ASC
                ";
                    $programme = $pdo->query($sql_programme)->fetchAll();

                    // Construction de la grille 2D : $grille[heure][scene_id] = données
                    $grille = [];
                    foreach ($programme as $event) {
                        $heure = substr($event['heure_debut'], 0, 5);
                        if (!isset($grille[$heure])) {
                            $grille[$heure] = [];
                        }
                        $grille[$heure][$event['scene_id']] = $event;
                    }
                    ?>

                    <?php if (empty($grille)): ?>
                        <p class="no-results">Le programme est en cours d'élaboration. Revenez bientôt !</p>
                    <?php else: ?>
                        <table class="events-table schedule-grid">
                            <caption class="sr-only">Programme de la journée par scène et par horaire</caption>
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
                                                <a href="detailsPrestation.php?id=<?= $e['pid'] ?>" class="schedule-event">
                                                    <strong><?= htmlspecialchars($e['mission']) ?></strong>
                                                    <span><?= htmlspecialchars($pilote) ?></span>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>
<?php
include 'app/View/header-footer/footer.php';
?>