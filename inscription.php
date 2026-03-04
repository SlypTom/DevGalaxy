<?php
include 'header-footer/header.php';
?>
<section class="inscription-section">
  <div class="container-contact">

    <div class="inscription-form-wrapper">
      <form action="#" method="post" class="space-form">

        <h1>Inscription</h1>
        <div class="form-group">
          <label for="nom">Nom *</label>
          <input type="text" id="nom" name="nom" placeholder="Ex: Commander Shepard" required>
        </div>

        <div class="form-group">
          <label for="prenom">Prénom *</label>
          <input type="text" id="prenom" name="prenom" placeholder="Ex: Officer Louis" required>
        </div>

        <div class="form-group">
          <label for="pseudo">Pseudo *</label>
          <input type="text" id="pseudo" name="pseudo" placeholder="Ex: Commander Shepard" required>
        </div>

        <div class="form-group">
          <label for="message">Description *</label>
          <textarea id="message" name="message" rows="5" placeholder="Votre description..." required></textarea>
        </div>

        <div class="form-group">
          <label for="email">Email de liaison *</label>
          <input type="email" id="email" name="email" placeholder="nom@domaine.com" required>
        </div>

        <div class="form-group">
          <label for="mdp">Mot de passe *</label>
          <input type="text" id="mdp" name="mdp" placeholder="********" required>
        </div>

        <div class="form-group">
          <label for="conf_mdp">Confirmation mot de passe *</label>
          <input type="text" id="conf_mdp" name="mdp" placeholder="********" required>
        </div>

        <button type="submit" class="inscription-btn-submit">Envoyer le signal 🚀</button>
      </form>
        <br><p>* : champs obligatoires</p>
    </div>

  </div>
</section>
<?php
include 'header-footer/footer.php';
?>