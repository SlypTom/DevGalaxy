<?php
include 'header-footer/header.php';
?>
<main class="page-artistes"> <div class="center">
  <a href="artistes.php" class="btn-back">← Retour à la liste</a>
</div>

  <section class="artist-profile-full">
    <div class="profile-header">
      <div class="avatar-placeholder big-profile">SC</div>

      <div class="profile-info">
        <h1>Sarah <span class="neon-text">"Loop"</span> Connor</h1>
        <h2 class="artist-role">Lead Security Engineer</h2>

        <p class="bio-full">
          Ancienne White Hat reconvertie dans la protection des infrastructures critiques, Sarah a passé les dix dernières années à sécuriser des banques et des gouvernements.
          <br><br>
          Elle est connue pour avoir stoppé l'attaque "Skynet" en 2021 avec un simple script Python écrit en 3 minutes. Elle ne croit pas aux pare-feux, elle croit à l'architecture blindée.
        </p>

        <ul class="profile-stats">
          <li>📅 <strong>Présence :</strong> Samedi 15 Nov.</li>
          <li>📍 <strong>Origine :</strong> Los Angeles, USA</li>
        </ul>
      </div>
    </div>
  </section>

  <section class="artist-prestations-list">
    <div class="center">
      <h3>Ses Missions Planifiées</h3>
      <p>Cliquez pour voir les détails techniques.</p>
    </div>

    <div class="prestations-grid" style="margin-top: 20px;">

      <a href="detailsPrestation.php" class="card-link">
        <article class="prestation-card programmed">
          <div class="card-header">
            <span class="category-badge backend">Backend</span>
            <div class="prestation-img-placeholder" style="background: linear-gradient(45deg, #1e1b4b, #312e81);">
              <code>SQL_INJECT</code>
            </div>
          </div>
          <div class="card-body">
            <h3>Injections SQL en Apesanteur</h3>
            <p class="description">Protéger vos bases de données contre les attaques.</p>
          </div>
          <div class="card-footer">
            <div class="status-indicator active">
              <time datetime="10:30">📅 10:30 - Salle Jupiter</time>
            </div>
          </div>
        </article>
      </a>

      <a href="detailsPrestation.php" class="card-link">
        <article class="prestation-card programmed">
          <div class="card-header">
            <span class="category-badge security">Débat</span>
            <div class="prestation-img-placeholder" style="background: linear-gradient(45deg, #7c2d12, #431407);">
              <code>VS</code>
            </div>
          </div>
          <div class="card-body">
            <h3>Débat : Tabs vs Spaces</h3>
            <p class="description">Le combat final pour trancher la question.</p>
          </div>
          <div class="card-footer">
            <div class="status-indicator active">
              <time datetime="16:00">📅 16:00 - Salle Mars</time>
            </div>
          </div>
        </article>
      </a>

    </div>
  </section>

</main>
<?php
include 'header-footer/footer.php';
?>