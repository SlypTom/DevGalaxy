<?php
include 'header-footer/header.php';
?>
<main>
    <section class="contact-section">
        <div class="container-contact">

            <div class="contact-info">
                <h1 >Base de transmission</h1>
                <p>Une question sur l'événement ? Canal ouvert 24/7.</p>

                <ul class="info-list">
                    <li>📍 <strong>QG :</strong> Space Center, Salle Jupiter</li>
                    <li>📧 <strong>Signal :</strong> contact@devgalaxy.com</li>
                    <li>📡 <strong>Fréquence :</strong> +32 400 00 00 00</li>
                    <li><strong>* :</strong> champs obligatoires</li>
                </ul>
            </div>

            <div class="contact-form-wrapper">
                <form action="#" method="post" class="space-form">
                    <fieldset style="border: none; padding: 0; margin: 0;">
                        <legend style="position: absolute; width: 1px; height: 1px; overflow: hidden;">Envoyez un message à l'équipe DevGalaxy</legend>

                        <div class="form-group">
                            <label for="nom">Nom de code *</label>
                            <input type="text" id="nom" name="nom" placeholder="Ex: Commander Shepard" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email de liaison *</label>
                            <input type="email" id="email" name="email" placeholder="nom@domaine.com" required>
                        </div>

                        <div class="form-group">
                            <label for="sujet">Sujet *</label>
                            <input type="text" id="sujet" name="sujet" placeholder="Sujet" required>
                        </div>

                        <div class="form-group">
                            <label for="message">Transmission *</label>
                            <textarea id="message" name="message" rows="5" placeholder="Votre message..." required></textarea>
                        </div>

                        <button type="submit" class="btn-primary">Envoyer le signal 🚀</button>
                    </fieldset>
                </form>
            </div>

        </div>
    </section>
</main>
<?php
include 'header-footer/footer.php';
?>