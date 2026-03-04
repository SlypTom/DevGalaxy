<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/db.php';

include 'header-footer/header.php';

?>
<main>
    <section class="presentation">
        <div class="center">
            <img src="img/logo.png" alt="Logo officiel de la convention DevGalaxy">
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
                <table class="events-table">
                    <caption style="position: absolute; width: 1px; height: 1px; overflow: hidden;">Programme détaillé des conférences et ateliers</caption>
                    <thead>
                    <tr>
                        <th scope="col">Horaire</th>
                        <th scope="col">Mission</th>
                        <th scope="col">Pilote (Speaker)</th>
                        <th scope="col">Lieu</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql_programme = "SELECT p.heure_debut, pr.intitule AS mission, pr.description AS description_prestation, u.nom_artiste AS pilote, s.nom_scene AS lieu FROM web2026_Programmation p JOIN web2026_Prestation pr ON p.prestation_id = pr.pid JOIN web2026_Utilisateur u ON pr.artiste_id = u.uid JOIN web2026_Scene s ON p.scene_id = s.sid ORDER BY p.heure_debut ASC";

                    $stmt_prog = $pdo->query($sql_programme);
                    $programme = $stmt_prog->fetchAll();

                    if (count($programme) > 0):
                        foreach ($programme as $event):
                            $heure = substr($event['heure_debut'], 0, 5);

                            // --- GESTION DYNAMIQUE DES COULEURS DES BADGES ---
                            $badge_class = 'badge-jupiter'; // Par défaut
                            $lieu_minuscule = strtolower($event['lieu']);

                            if (strpos($lieu_minuscule, 'mars') !== false) {
                                $badge_class = 'badge-mars';
                            } elseif (strpos($lieu_minuscule, 'saturne') !== false) {
                                $badge_class = 'badge-saturne';
                            }
                            // ---------------------------------------------------
                            ?>

                            <tr>
                                <td class="time"><?= htmlspecialchars($heure) ?></td>
                                <td>
                                    <strong class="event-title">
                                        <a href="detailsPrestation.php">
                                            <?= htmlspecialchars($event['mission']) ?>
                                        </a>
                                    </strong>
                                    <span class="event-desc"><?= htmlspecialchars($event['description_prestation']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($event['pilote']) ?></td>

                                <td><span class="badge <?= $badge_class ?>"><?= htmlspecialchars($event['lieu']) ?></span></td>
                            </tr>

                        <?php
                        endforeach;
                    else:
                        ?>

                        <tr>
                            <td colspan="4">
                                Le programme est en cours d'élaboration. Revenez bientôt !
                            </td>
                        </tr>

                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>
<?php
include 'header-footer/footer.php';
?>