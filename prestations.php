<?php
session_start();
require_once 'config/db.php';

// --- 1. RÉCUPÉRATION DES DONNÉES POUR LES LISTES DÉROULANTES ---
$artistes = $pdo->query("SELECT uid, nom, prenom, nom_artiste FROM web2026_Utilisateur WHERE est_organisateur = 0")->fetchAll();
$categories = $pdo->query("SELECT * FROM web2026_Categorie")->fetchAll();

// --- 2. GESTION DES FILTRES ---
// Remplacement du Null Coalescing Operator (??) par des isset() classiques
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filtre_artiste = isset($_GET['artiste_id']) ? $_GET['artiste_id'] : '';
$filtre_categorie = isset($_GET['categorie_id']) ? $_GET['categorie_id'] : '';
$uniquement_programmees = isset($_GET['programme']);

// --- 3. CONSTRUCTION DYNAMIQUE DE LA REQUÊTE SQL ---
$sql = "
    SELECT p.*, c.intitule AS nom_categorie, u.nom_artiste, u.prenom, u.nom,
           prog.heure_debut, s.nom_scene
    FROM web2026_Prestation p
    JOIN web2026_Categorie c ON p.categorie_id = c.cid
    JOIN web2026_Utilisateur u ON p.artiste_id = u.uid
    LEFT JOIN web2026_Programmation prog ON p.pid = prog.prestation_id
    LEFT JOIN web2026_Scene s ON prog.scene_id = s.sid
    WHERE 1=1
";

// Remplacement des crochets [] par array() pour faire plaisir à ton éditeur
$params = array();

if (!empty($search)) {
    $sql .= " AND (p.intitule LIKE :search OR p.description LIKE :search)";
    $params['search'] = '%' . $search . '%';
}

if (!empty($filtre_artiste)) {
    $sql .= " AND p.artiste_id = :artiste_id";
    $params['artiste_id'] = $filtre_artiste;
}

if (!empty($filtre_categorie)) {
    $sql .= " AND p.categorie_id = :categorie_id";
    $params['categorie_id'] = $filtre_categorie;
}

if ($uniquement_programmees) {
    // Si la case est cochée, on ne prend que celles qui ont une entrée dans la table Programmation
    $sql .= " AND prog.prog_id IS NOT NULL";
}

$sql .= " ORDER BY p.intitule ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$prestations = $stmt->fetchAll();

include 'header-footer/header.php';
?>

<main class="page-prestations">
    <div class="catalog-header">
        <h1 class="catalog-title">Catalogue des Missions</h1>
        <p>Explorez toutes les conférences, ateliers et modules proposés par nos pilotes.</p>
    </div>

    <section class="search-section">
        <form action="prestations.php" method="get" class="filter-form">

            <div class="filter-group">
                <label for="search">Recherche par mot-clé</label>
                <input type="text" id="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Ex: Javascript, IA...">
            </div>

            <div class="filter-group">
                <label for="artiste_id">Filtrer par Pilote</label>
                <select id="artiste_id" name="artiste_id">
                    <option value="">Tous les pilotes</option>
                    <?php foreach ($artistes as $art):
                        $nom_affich = !empty($art['nom_artiste']) ? $art['nom_artiste'] : $art['prenom'] . ' ' . $art['nom'];
                        $selected = ($filtre_artiste == $art['uid']) ? 'selected' : '';
                        ?>
                        <option value="<?= $art['uid'] ?>" <?= $selected ?>><?= htmlspecialchars($nom_affich) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="categorie_id">Filtrer par Catégorie</label>
                <select id="categorie_id" name="categorie_id">
                    <option value="">Toutes les catégories</option>
                    <?php foreach ($categories as $cat):
                        $selected = ($filtre_categorie == $cat['cid']) ? 'selected' : '';
                        ?>
                        <option value="<?= $cat['cid'] ?>" <?= $selected ?>><?= htmlspecialchars($cat['intitule']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-checkbox">
                <input type="checkbox" id="programme" name="programme" value="1" <?= $uniquement_programmees ? 'checked' : '' ?>>
                <label for="programme">Afficher uniquement les missions programmées</label>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-primary">Filtrer</button>
                <a href="prestations.php" class="btn-primary btn-outline btn-small">Réinitialiser</a>
            </div>
        </form>
    </section>

    <div class="prestations-grid prestations-wrapper">
        <?php if (count($prestations) > 0): ?>
            <?php foreach ($prestations as $presta):
                // Gestion du nom du pilote
                $nom_pilote = !empty($presta['nom_artiste']) ? $presta['nom_artiste'] : $presta['prenom'] . ' ' . $presta['nom'];

                // --- GESTION DYNAMIQUE DES COULEURS ---
                // On récupère l'ID de la catégorie (1, 2, 3 ou 4)
                $cat_id = $presta['categorie_id'];

                // On vérifie si on a bien prévu une couleur pour cet ID, sinon on met "default"
                if ($cat_id >= 1 && $cat_id <= 4) {
                    $classe_badge = 'badge-cat-' . $cat_id;
                    $classe_bg = 'bg-cat-' . $cat_id;
                } else {
                    $classe_badge = 'badge-cat-default';
                    $classe_bg = 'bg-cat-default';
                }
                ?>
                <a href="detailsPrestation.php?id=<?= $presta['pid'] ?>" class="card-link">
                    <article class="prestation-card catalog">
                        <div class="card-header">
                            <span class="category-badge <?= $classe_badge ?>"><?= htmlspecialchars($presta['nom_categorie']) ?></span>

                            <div class="prestation-img-placeholder <?= $classe_bg ?>">
                                <?php
                                // On découpe l'intitulé par les espaces
                                $mots = explode(' ', $presta['intitule']);
                                // On récupère le premier élément du tableau (le premier mot)
                                $premier_mot = $mots[0];
                                ?>
                                <code><?= htmlspecialchars($premier_mot) ?></code>
                            </div>
                        </div>
                        <div class="card-body">
                            <h3><?= htmlspecialchars($presta['intitule']) ?></h3>

                            <div class="prestation-meta">
                                <span class="meta-artist">👤 <?= htmlspecialchars($nom_pilote) ?></span>

                                <p class="description"><?= htmlspecialchars($presta['description']) ?></p>

                                <?php if (!empty($presta['heure_debut'])): ?>
                                    <span class="meta-schedule">📅 Prévu à <?= htmlspecialchars(substr($presta['heure_debut'], 0, 5)) ?> (<?= htmlspecialchars($presta['nom_scene']) ?>)</span>
                                <?php else: ?>
                                    <span class="meta-not-scheduled">En attente de planification</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results">
                <p>Aucune mission ne correspond à vos critères de recherche. Essayez de modifier vos filtres.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'header-footer/footer.php'; ?>