<?php
session_start();
require_once 'config/db.php';

// 1. Récupération de l'ID depuis l'URL
$id = isset($_GET['id']) ? $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: prestations.php");
    exit;
}

// 2. Requête SQL pour récupérer les détails de la prestation, l'artiste et la programmation
$sql = "
    SELECT p.*, c.intitule AS nom_categorie, u.uid AS artiste_id, u.nom_artiste, u.prenom, u.nom,
           prog.heure_debut, s.nom_scene
    FROM web2026_Prestation p
    JOIN web2026_Categorie c ON p.categorie_id = c.cid
    JOIN web2026_Utilisateur u ON p.artiste_id = u.uid
    LEFT JOIN web2026_Programmation prog ON p.pid = prog.prestation_id
    LEFT JOIN web2026_Scene s ON prog.scene_id = s.sid
    WHERE p.pid = :id
";

$stmt = $pdo->prepare($sql);
$stmt->execute(array('id' => $id));
$presta = $stmt->fetch();

// Si la prestation n'existe pas
if (!$presta) {
    die("Erreur : Cette mission est introuvable dans les archives de la fédération.");
}

// Préparation des variables d'affichage
$nom_pilote = !empty($presta['nom_artiste']) ? $presta['nom_artiste'] : $presta['prenom'] . ' ' . $presta['nom'];
$mots = explode(' ', $presta['intitule']);
$premier_mot = $mots[0];

// Gestion des classes de couleurs (comme sur la page catalogue)
$cat_id = $presta['categorie_id'];
$classe_badge = ($cat_id >= 1 && $cat_id <= 4) ? 'badge-cat-' . $cat_id : 'badge-cat-default';
$classe_bg = ($cat_id >= 1 && $cat_id <= 4) ? 'bg-cat-' . $cat_id : 'bg-cat-default';

include 'header-footer/header.php';
?>

<main class="page-prestations">
    <div class="center">
        <a href="prestations.php" class="back-link">← Retour au catalogue</a>
    </div>

    <div class="details-container">
        <div class="details-image-side">
            <div class="details-img-wrapper"> <div class="details-img-big <?= $classe_bg ?>">
                    <code><?= htmlspecialchars($premier_mot) ?></code>
                </div>
                <span class="category-badge details-category <?= $classe_badge ?>">
                <?= htmlspecialchars($presta['nom_categorie']) ?>
            </span>
            </div>

            <?php if (!empty($presta['heure_debut'])): ?>
                <div class="details-meta-box">
                    <h4>📅 Programmation</h4>
                    <p>Cette mission est planifiée à <strong><?= htmlspecialchars(substr($presta['heure_debut'], 0, 5)) ?></strong>.</p>
                    <p>Lieu : <strong><?= htmlspecialchars($presta['nom_scene']) ?></strong></p>
                </div>
            <?php else: ?>
                <div class="details-meta-box status-pending">
                    <h4>📅 Statut</h4>
                    <p>Cette mission n'est pas encore inscrite au programme officiel de la journée.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="details-info-side">
            <h1 class="details-title"><?= htmlspecialchars($presta['intitule']) ?></h1>

            <p>
                Proposé par le pilote :
                <a href="detailsArtiste.php?id=<?= $presta['artiste_id'] ?>" class="link-pilot">
                    <?= htmlspecialchars($nom_pilote) ?>
                </a>
            </p>

            <div class="details-description">
                <h3>Description de la mission</h3>
                <p><?= nl2br(htmlspecialchars($presta['description'])) ?></p>
            </div>
        </div>
    </div>
</main>

<?php include 'header-footer/footer.php'; ?>