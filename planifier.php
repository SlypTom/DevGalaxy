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

$heures_possibles = ['10:00:00','11:00:00','12:00:00','13:00:00','14:00:00','15:00:00','16:00:00','17:00:00','18:00:00'];
$scenes    = Scene::findAll();
$nb_scenes = count($scenes);

// Lecture depuis POST ou GET (GET utilisé pour les boutons "Retour")
$etape         = intval($_POST['etape']         ?? $_GET['etape']         ?? 1);
$heure_choisie = $_POST['heure']                ?? $_GET['heure']         ?? '';
$artiste_id    = intval($_POST['artiste_id']    ?? 0);
$scene_id      = intval($_POST['scene_id']      ?? 0);
$prestation_id = intval($_POST['prestation_id'] ?? 0);

// ===== ÉTAPE 4 : FINALISATION =====
if ($etape === 4) {
    $scene_prise  = Programmation::sceneOccupee($scene_id, $heure_choisie);
    $artiste_pris = Programmation::artisteOccupe($artiste_id, $heure_choisie);

    if ($scene_prise || $artiste_pris) {
        header("Location: gestionProgramme.php?erreur=conflit");
        exit;
    }

    Programmation::create($prestation_id, $scene_id, $heure_choisie);
    header("Location: gestionProgramme.php?succes=1");
    exit;
}

// ===== DONNÉES PAR ÉTAPE =====
$heures_disponibles  = [];
$artistes_libres     = [];
$scenes_libres       = [];
$prestations_artiste = [];
$nom_artiste_choisi  = '';
$nom_scene_choisie   = '';

if ($etape === 1) {
    $heures_disponibles = Programmation::getHeuresDisponibles($heures_possibles, $nb_scenes);
    if (empty($heures_disponibles)) {
        header("Location: gestionProgramme.php");
        exit;
    }
}

if ($etape === 2) {
    if (empty($heure_choisie)) {
        // Pas d'heure : retour étape 1
        header("Location: planifier.php");
        exit;
    }
    $scenes_libres   = Scene::findLibres($heure_choisie);
    $artistes_libres = Programmation::getArtistesLibres($heure_choisie);
}

if ($etape === 3) {
    if (empty($heure_choisie) || !$artiste_id || !$scene_id) {
        header("Location: planifier.php");
        exit;
    }
    $prestations_artiste = Programmation::getPrestationsByArtiste($artiste_id);
    $nom_artiste_choisi  = Programmation::getNomArtiste($artiste_id);
    $nom_scene_choisie   = Programmation::getNomScene($scene_id);
}

include 'app/View/header-footer/header.php';
?>

<main class="page-prestations">
    <div class="center">
        <h1>Assistant de Planification</h1>
    </div>

    <?php if ($etape === 1): ?>
    <!-- ===== ÉTAPE 1 : HEURE ===== -->
    <div class="form-container">
        <h2 class="form-title">Étape 1 / 3 : Choisissez un créneau horaire</h2>
        <form method="post" action="planifier.php" class="space-form">
            <input type="hidden" name="etape" value="2">
            <div class="form-group">
                <label for="heure">Heure disponible *</label>
                <select id="heure" name="heure" required>
                    <option value="">-- Sélectionnez un créneau --</option>
                    <?php foreach ($heures_disponibles as $h): ?>
                        <option value="<?= $h ?>"><?= substr($h, 0, 5) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary">Étape suivante →</button>
                <a href="gestionProgramme.php" class="btn-primary btn-outline">Annuler</a>
            </div>
        </form>
    </div>

    <?php elseif ($etape === 2): ?>
    <!-- ===== ÉTAPE 2 : ARTISTE ET SCÈNE ===== -->
    <div class="form-container">
        <h2 class="form-title">Étape 2 / 3 : Choisissez un artiste et une scène</h2>
        <div class="alert-success step-summary">
            Heure choisie : <strong><?= htmlspecialchars(substr($heure_choisie, 0, 5)) ?></strong>
        </div>

        <!-- Formulaire principal : va à l'étape 3 -->
        <form method="post" action="planifier.php" class="space-form">
            <input type="hidden" name="etape" value="3">
            <input type="hidden" name="heure" value="<?= htmlspecialchars($heure_choisie) ?>">

            <div class="form-group">
                <label for="artiste_id">Artiste disponible *</label>
                <select id="artiste_id" name="artiste_id" required>
                    <option value="">-- Sélectionnez un artiste --</option>
                    <?php foreach ($artistes_libres as $a): ?>
                        <option value="<?= $a['uid'] ?>">
                            <?= htmlspecialchars(!empty($a['nom_artiste']) ? $a['nom_artiste'] : $a['prenom'].' '.$a['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="scene_id">Scène disponible *</label>
                <select id="scene_id" name="scene_id" required>
                    <option value="">-- Sélectionnez une scène --</option>
                    <?php foreach ($scenes_libres as $s): ?>
                        <option value="<?= $s['sid'] ?>"><?= htmlspecialchars($s['nom_scene']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Étape suivante →</button>
                <!-- Retour : lien GET vers étape 1, sans form imbriquée -->
                <a href="planifier.php" class="btn-primary btn-outline">← Retour</a>
                <a href="gestionProgramme.php" class="btn-primary btn-outline">Annuler</a>
            </div>
        </form>
    </div>

    <?php elseif ($etape === 3): ?>
    <!-- ===== ÉTAPE 3 : PRESTATION + RÉSUMÉ ===== -->
    <div class="form-container">
        <h2 class="form-title">Étape 3 / 3 : Choisissez une prestation et confirmez</h2>
        <div class="alert-success step-summary">
            <strong>Résumé :</strong><br>
            ⏰ <?= htmlspecialchars(substr($heure_choisie, 0, 5)) ?>
            &nbsp;|&nbsp; 🎤 <?= htmlspecialchars($nom_artiste_choisi) ?>
            &nbsp;|&nbsp; 📍 <?= htmlspecialchars($nom_scene_choisie) ?>
        </div>

        <!-- Formulaire principal : confirme et insère -->
        <form method="post" action="planifier.php" class="space-form">
            <input type="hidden" name="etape" value="4">
            <input type="hidden" name="heure" value="<?= htmlspecialchars($heure_choisie) ?>">
            <input type="hidden" name="artiste_id" value="<?= $artiste_id ?>">
            <input type="hidden" name="scene_id" value="<?= $scene_id ?>">

            <div class="form-group">
                <label for="prestation_id">Prestation à programmer *</label>
                <select id="prestation_id" name="prestation_id" required>
                    <option value="">-- Sélectionnez une prestation --</option>
                    <?php foreach ($prestations_artiste as $p): ?>
                        <option value="<?= $p['pid'] ?>"><?= htmlspecialchars($p['intitule']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">✅ Confirmer et ajouter au programme</button>
                <!-- Retour : lien GET vers étape 2 en passant l'heure, sans form imbriquée -->
                <a href="planifier.php?etape=2&heure=<?= urlencode($heure_choisie) ?>"
                   class="btn-primary btn-outline">← Retour</a>
                <a href="gestionProgramme.php" class="btn-primary btn-outline">Annuler</a>
            </div>
        </form>
    </div>
    <?php endif; ?>
</main>

<?php include 'app/View/header-footer/footer.php'; ?>
