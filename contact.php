<?php
session_start();

// Importation des classes PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$message_status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $sujet = htmlspecialchars($_POST['sujet']);
    $message = htmlspecialchars($_POST['message']);

    $mail = new PHPMailer(true);

    try {
        // --- CONFIGURATION SERVEUR SMTP ---
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Active pour voir les erreurs détaillées
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';           // Remplace par ton serveur SMTP
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tombastin2005@gmail.com';      // Ton adresse email
        $mail->Password   = 'zexa caim yvat qknj';     // Ton mot de passe d'application
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // --- DESTINATAIRES ---
        $mail->setFrom('ton-email@gmail.com', 'Fédération Galactique');
        $mail->addAddress('ton-email@gmail.com');       // C'est toi qui reçois le mail
        $mail->addReplyTo($email, $nom);                // Pour répondre directement au visiteur

        // --- CONTENU ---
        $mail->isHTML(true);
        $mail->Subject = "Nouveau contact : $sujet";
        $mail->Body    = "<h3>Message de $nom ($email)</h3><p>$message</p>";

        $mail->send();
        $message_status = "success";
    } catch (Exception $e) {
        $message_status = "error";
    }
}

include 'header-footer/header.php';
?>

<main>
    <div class="container-contact">
        <div class="contact-info">
            <h1>Base de transmission</h1>
            <?php if ($message_status == "success"): ?>
                <div class="alert-success">🚀 Message envoyé avec succès ! Nos équipes vous répondront bientôt.</div>
            <?php elseif ($message_status == "error"): ?>
                <div class="alert-error">❌ Erreur lors de l'envoi. Veuillez réessayer.</div>
            <?php endif; ?>
            <p>Une question sur l'événement ? Canal ouvert 24/7.</p>

            <ul class="info-list">
                <li>📍 <strong>QG :</strong> Space Center, Salle Jupiter</li>
                <li>📧 <strong>Signal :</strong> contact@devgalaxy.com</li>
                <li>📡 <strong>Fréquence :</strong> +32 400 00 00 00</li>
                <li><strong>* :</strong> champs obligatoires</li>
            </ul>
        </div>

        <div class="contact-form-wrapper">
            <form action="contact.php" method="POST" class="form-contact">
                <fieldset class="fieldset-reset">
                    <legend class="sr-only">Envoyez un message à l'équipe DevGalaxy</legend>
                    <div class="form-group">
                        <label for="nom">Nom ou Pseudo *</label>
                        <input type="text" id="nom" name="nom" placeholder="Ex: Commander Shepard" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Votre Email *</label>
                        <input type="email" id="email" name="email" placeholder="nom@domaine.com" required>
                    </div>

                    <div class="form-group">
                        <label for="sujet">Objet de la mission *</label>
                        <input type="text" id="sujet" name="sujet" placeholder="Sujet" required>
                    </div>

                    <div class="form-group">
                        <label for="message">Votre Message *</label>
                        <textarea id="message" name="message" rows="6" placeholder="Votre message..." required></textarea>
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%;">Envoyer le signal 🚀</button>
                </fieldset>
            </form>
        </div>
    </div>
</main>

<?php include 'header-footer/footer.php'; ?>