<?php
session_start();
require_once 'config/db.php';

// 1. Récupération de l'ID de l'artiste
$artiste_id = isset($_GET['id']) ? $_GET['id'] : 0;

if ($artiste_id <= 0) {
    header("Location: prestations.php");
    exit;
}

// 2. Récupération des infos de l'artiste
$sql_user = "SELECT nom, prenom, nom_artiste, description FROM web2026_Utilisateur WHERE uid = :id AND est_organisateur = 0";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute(array('id' => $artiste_id));
$artiste = $stmt_user->fetch();

if (!$artiste) {
    die("Pilote introuvable dans les registres de la mission.");
}

$nom_complet = !empty($artiste['nom_artiste']) ? $artiste['nom_artiste'] : $artiste['prenom'] . ' ' . $artiste['nom'];

// 3. Récupération de ses prestations
$sql_presta = "
    SELECT p.*, c.intitule AS nom_categorie 
    FROM web2026_Prestation p
    JOIN web2026_Categorie c ON p.categorie_id = c.cid
    WHERE p.artiste_id = :id
    ORDER BY p.intitule ASC";
$stmt_presta = $pdo->prepare($sql_presta);
$stmt_presta->execute(array('id' => $artiste_id));
$ses_prestations = $stmt_presta->fetchAll();

include 'header-footer/header.php';
?>

<main class="center">
    <section class="artist-profile-header">
        <div class="artist-avatar-large">👤</div>
        <h1 class="details-title"><?= htmlspecialchars($nom_complet) ?></h1>
        <p class="meta-artist">Pilote Officiel de la Fédération</p>
    </section>

    <section class="artist-bio-section">
        <h3>À propos du pilote</h3>
        <p>
            <?= !empty($artiste['description'])
                    ? nl2br(htmlspecialchars($artiste['description']))
                    : "Ce pilote n'a pas encore rempli son journal de bord." ?>
        </p>
    </section>

    <section class="artist-missions">
        <h2 class="artist-missions-title">Missions proposées par <?= htmlspecialchars($nom_complet) ?></h2>

        <div class="prestations-grid">
            <?php if (count($ses_prestations) > 0): ?>
                <?php foreach ($ses_prestations as $presta):
                    $premier_mot = explode(' ', $presta['intitule'])[0];
                    $cat_id = $presta['categorie_id'];
                    $classe_badge = "badge-cat-$cat_id";
                    $classe_bg = "bg-cat-$cat_id";
                    ?>
                    <a href="detailsPrestation.php?id=<?= $presta['pid'] ?>">
                        <article class="prestation-card">
                            <div class="card-header">
                                <span class="category-badge <?= $classe_badge ?>"><?= htmlspecialchars($presta['nom_categorie']) ?></span>
                                <div class="prestation-img-placeholder <?= $classe_bg ?>">
                                    <code><?= htmlspecialchars($premier_mot) ?></code>
                                </div>
                            </div>
                            <div class="card-body">
                                <h3><?= htmlspecialchars($presta['intitule']) ?></h3>
                                <p class="description"><?= htmlspecialchars(substr($presta['description'], 0, 80)) ?>...</p>
                            </div>
                        </article>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune mission enregistrée pour le moment.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'header-footer/footer.php'; ?>