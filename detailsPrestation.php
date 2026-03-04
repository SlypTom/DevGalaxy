<?php
include 'header-footer/header.php';
?>
<main class="center">
    <section class="detail-content" style="padding: 50px 0;">
        <h1>Détails de la Mission</h1>
        <div style="background: rgba(13, 17, 41, 0.8); padding: 30px; border-radius: 10px; border: 1px solid #334155; max-width: 800px; margin: 0 auto; text-align: left;">
            <h2 style="color: #67e8f9;">Titre de la Conférence / Atelier</h2>
            <p style="color: #94a3b8; font-style: italic;">Proposé par : Nom du Speaker</p>
            <hr style="border-color: #334155; margin: 20px 0;">
            <p>
                Voici le descriptif complet de cette prestation. Dans une version finale, ce texte changerait dynamiquement selon la carte cliquée.
                Pour ce MVP, toutes les grilles pointent ici pour valider la structure de navigation.
            </p>
            <ul style="margin-top: 20px; list-style: none;">
                <li>📅 <strong>Durée :</strong> 60 minutes</li>
                <li>📍 <strong>Lieu :</strong> Space Center</li>
                <li>🎓 <strong>Niveau :</strong> Intermédiaire</li>
            </ul>
            <br>
            <a href="prestations.php" class="btn-search" style="text-decoration: none; display: inline-block; text-align: center;">← Retour au catalogue</a>
        </div>
    </section>
</main>
<?php
include 'header-footer/footer.php';
?>