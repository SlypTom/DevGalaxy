<?php
// UC-D.4 : L'organisateur gère le profil et le catalogue d'un artiste
session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['est_organisateur'])) {
    header("Location: connexion.php");
    exit;
}

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/app/Model/Utilisateur.php';
require_once __DIR__ . '/app/Model/Prestation.php';
require_once __DIR__ . '/app/Model/Categorie.php';

$artiste_uid = intval(isset($_GET['id']) ? $_GET['id'] : 0);
if ($artiste_uid <= 0) { header("Location: gererArtistes.php"); exit; }

$stmt_user = $pdo->prepare("SELECT * FROM web2026_Utilisateur WHERE uid = :uid AND est_organisateur = 0");
$stmt_user->execute(['uid' => $artiste_uid]);
$artiste = $stmt_user->fetch();
if (!$artiste) { header("Location: gererArtistes.php"); exit; }

$message_succes = "";
$message_erreur = "";
$erreurs_profil = [];
$erreurs_presta = [];

// Variables repopulation profil
$nom         = $artiste['nom'];
$prenom      = $artiste['prenom'];
$nom_artiste = $artiste['nom_artiste'];
$email       = $artiste['email'];
$description = $artiste['description'];

// === MODIFIER LE PROFIL ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'editer_profil') {
    $nom         = trim($_POST['nom']);
    $prenom      = trim($_POST['prenom']);
    $nom_artiste = trim($_POST['nom_artiste']);
    $email       = trim($_POST['email']);
    $description = trim($_POST['description']);

    if (empty($nom))    $erreurs_profil['nom']    = "Le nom est obligatoire.";
    if (empty($prenom)) $erreurs_profil['prenom'] = "Le prénom est obligatoire.";
    if (empty($email)) {
        $erreurs_profil['email'] = "L'email est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs_profil['email'] = "Format d'email invalide.";
    } elseif ($email !== $artiste['email']) {
        $s = $pdo->prepare("SELECT COUNT(*) FROM web2026_Utilisateur WHERE email = :e AND uid != :uid");
        $s->execute(['e' => $email, 'uid' => $artiste_uid]);
        if ($s->fetchColumn() > 0) $erreurs_profil['email'] = "Email déjà utilisé par un autre compte.";
    }

    if (empty($erreurs_profil)) {
        $pdo->prepare("UPDATE web2026_Utilisateur SET nom=:n, prenom=:p, nom_artiste=:na, email=:e, description=:d WHERE uid=:uid")
            ->execute(['n'=>$nom,'p'=>$prenom,'na'=>$nom_artiste,'e'=>$email,'d'=>$description,'uid'=>$artiste_uid]);
        $message_succes = "Profil de l'artiste mis à jour avec succès.";
        $stmt_user->execute(['uid' => $artiste_uid]);
        $artiste = $stmt_user->fetch();
    }
}

// === AJOUTER UNE PRESTATION ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter_presta') {
    $titre_p = trim($_POST['intitule']);
    $desc_p  = trim($_POST['description_presta']);
    $cat_p   = intval($_POST['categorie_id']);

    if (empty($titre_p)) $erreurs_presta['intitule']          = "Le titre est obligatoire.";
    if (empty($desc_p))  $erreurs_presta['description_presta'] = "La description est obligatoire.";
    if (!$cat_p)         $erreurs_presta['categorie_id']       = "La catégorie est obligatoire.";

    if (empty($erreurs_presta)) {
        $pdo->prepare("INSERT INTO web2026_Prestation (intitule, description, image, categorie_id, artiste_id) VALUES (:t,:d,'default.png',:c,:uid)")
            ->execute(['t'=>$titre_p,'d'=>$desc_p,'c'=>$cat_p,'uid'=>$artiste_uid]);
        $message_succes = "Prestation ajoutée avec succès.";
    }
}

// === SUPPRIMER UNE PRESTATION ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'supprimer_presta') {
    $pid = intval($_POST['presta_id']);
    $s = $pdo->prepare("SELECT COUNT(*) FROM web2026_Programmation WHERE prestation_id = :pid");
    $s->execute(['pid' => $pid]);
    if ($s->fetchColumn() > 0) {
        $message_erreur = "Impossible : cette prestation fait partie du programme officiel.";
    } else {
        $pdo->prepare("DELETE FROM web2026_Prestation WHERE pid = :pid AND artiste_id = :uid")
            ->execute(['pid'=>$pid,'uid'=>$artiste_uid]);
        $message_succes = "Prestation supprimée.";
    }
}

$categories = $pdo->query("SELECT * FROM web2026_Categorie")->fetchAll();
$stmt_cat   = $pdo->prepare("
    SELECT p.*, c.intitule AS nom_categorie
    FROM web2026_Prestation p
    JOIN web2026_Categorie c ON p.categorie_id = c.cid
    WHERE p.artiste_id = :uid ORDER BY p.intitule
");
$stmt_cat->execute(['uid' => $artiste_uid]);
$catalogue = $stmt_cat->fetchAll();

$nom_affiche = !empty($artiste['nom_artiste']) ? $artiste['nom_artiste'] : $artiste['prenom'].' '.$artiste['nom'];

include 'app/View/header-footer/header.php';
?>

    <main class="page-prestations">
        <div class="center">
            <a href="gererArtistes.php" class="back-link">← Retour à la liste</a>
            <h1>Administration : <?= htmlspecialchars($nom_affiche) ?></h1>
        </div>

        <div class="alert-container">
            <?php if ($message_succes): ?>
                <div class="alert-success"><?= htmlspecialchars($message_succes) ?></div>
            <?php endif; ?>
            <?php if ($message_erreur): ?>
                <div class="alert-error"><?= htmlspecialchars($message_erreur) ?></div>
            <?php endif; ?>
        </div>

        <!-- ===== SECTION 1 : Profil ===== -->
        <div class="form-container form-container-spaced">
            <h2 class="form-title">Profil de l'artiste</h2>
            <form method="post" action="gererArtisteAdmin.php?id=<?= $artiste_uid ?>" class="space-form">
                <input type="hidden" name="action" value="editer_profil">

                <div class="form-group <?= isset($erreurs_profil['nom']) ? 'field-error' : '' ?>">
                    <label>Nom *</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($nom) ?>" required>
                    <?php if (isset($erreurs_profil['nom'])): ?>
                        <span class="error-message"><?= htmlspecialchars($erreurs_profil['nom']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group <?= isset($erreurs_profil['prenom']) ? 'field-error' : '' ?>">
                    <label>Prénom *</label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars($prenom) ?>" required>
                    <?php if (isset($erreurs_profil['prenom'])): ?>
                        <span class="error-message"><?= htmlspecialchars($erreurs_profil['prenom']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Nom d'artiste</label>
                    <input type="text" name="nom_artiste" value="<?= htmlspecialchars($nom_artiste) ?>">
                </div>

                <div class="form-group <?= isset($erreurs_profil['email']) ? 'field-error' : '' ?>">
                    <label>Email *</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                    <?php if (isset($erreurs_profil['email'])): ?>
                        <span class="error-message"><?= htmlspecialchars($erreurs_profil['email']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Biographie</label>
                    <textarea name="description" rows="4"><?= htmlspecialchars($description) ?></textarea>
                </div>

                <button type="submit" class="btn-primary">Mettre à jour le profil</button>
            </form>
        </div>

        <!-- ===== SECTION 2 : Catalogue ===== -->
        <div class="form-container">
            <h2 class="form-title">Catalogue de prestations</h2>

            <?php if (empty($catalogue)): ?>
                <p>Aucune prestation enregistrée pour cet artiste.</p>
            <?php else: ?>
                <?php foreach ($catalogue as $p): ?>
                    <div class="presta-admin-row">
                        <div>
                            <strong><?= htmlspecialchars($p['intitule']) ?></strong>
                            <span class="presta-cat-label"><?= htmlspecialchars($p['nom_categorie']) ?></span>
                        </div>
                        <div class="presta-admin-actions">
                            <a href="modifierPrestationAdmin.php?pid=<?= $p['pid'] ?>&uid=<?= $artiste_uid ?>"
                               class="btn-primary btn-outline btn-small">Modifier</a>
                            <form method="post" action="confirmerSuppressionPrestaAdmin.php" class="form-inline">
                                <input type="hidden" name="presta_id" value="<?= $p['pid'] ?>">
                                <input type="hidden" name="artiste_uid" value="<?= $artiste_uid ?>">
                                <input type="hidden" name="intitule" value="<?= htmlspecialchars($p['intitule']) ?>">
                                <button type="submit" class="btn-primary btn-danger btn-small">Supprimer</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <h3 class="section-add-title">Ajouter une prestation</h3>
            <form method="post" action="gererArtisteAdmin.php?id=<?= $artiste_uid ?>" class="space-form">
                <input type="hidden" name="action" value="ajouter_presta">

                <div class="form-group <?= isset($erreurs_presta['intitule']) ? 'field-error' : '' ?>">
                    <label>Titre *</label>
                    <input type="text" name="intitule" value="<?= htmlspecialchars($_POST['intitule'] ?? '') ?>">
                    <?php if (isset($erreurs_presta['intitule'])): ?>
                        <span class="error-message"><?= htmlspecialchars($erreurs_presta['intitule']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group <?= isset($erreurs_presta['categorie_id']) ? 'field-error' : '' ?>">
                    <label>Catégorie *</label>
                    <select name="categorie_id">
                        <option value="">-- Catégorie --</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['cid'] ?>"><?= htmlspecialchars($c['intitule']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($erreurs_presta['categorie_id'])): ?>
                        <span class="error-message"><?= htmlspecialchars($erreurs_presta['categorie_id']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group <?= isset($erreurs_presta['description_presta']) ? 'field-error' : '' ?>">
                    <label>Description *</label>
                    <textarea name="description_presta" rows="3"><?= htmlspecialchars($_POST['description_presta'] ?? '') ?></textarea>
                    <?php if (isset($erreurs_presta['description_presta'])): ?>
                        <span class="error-message"><?= htmlspecialchars($erreurs_presta['description_presta']) ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-primary btn-small">Ajouter</button>
            </form>
        </div>
    </main>

<?php include 'app/View/header-footer/footer.php'; ?>