<?php
include 'header-footer/header.php';
?>
<main class="page-artistes">

  <div class="center">
    <h1>Nos Pilotes Experts</h1>
    <p>Découvrez les esprits brillants qui vont vous guider à travers la galaxie du code.</p>
  </div>

  <div class="filter-section">
    <label class="checkbox-container">
      <input type="checkbox" id="filter-programmed">
      <span class="checkmark"></span>
      Afficher uniquement les pilotes programmés
    </label>
  </div>

  <div class="artists-grid">

    <a href="detailsArtiste.php" class="card-link">
      <article class="artist-card">
        <div class="artist-photo">
          <div class="avatar-placeholder big">SC</div>
        </div>
        <div class="artist-content">
          <h3>Sarah "Loop" Connor</h3>
          <span class="artist-role">Lead Security Engineer</span>
          <p class="artist-bio">Ancienne hackeuse, elle traque les failles comme des aliens. Experte en Python et Cryptographie.</p>

          <div class="artist-schedule">
            <h4>📅 Missions planifiées :</h4>
            <ul>
              <li>
                <span class="schedule-time">10:30</span>
                <span class="schedule-room">Salle Jupiter</span>
                <span class="schedule-title">Injections SQL</span>
              </li>
              <li>
                <span class="schedule-time">16:00</span>
                <span class="schedule-room">Salle Mars</span>
                <span class="schedule-title">Débat : Tabs vs Spaces</span>
              </li>
            </ul>
          </div>
        </div>
      </article>
    </a>

    <a href="detailsArtiste.php" class="card-link">
      <article class="artist-card">
        <div class="artist-photo">
          <div class="avatar-placeholder big" style="border-color: #f97316;">DB</div>
        </div>
        <div class="artist-content">
          <h3>Dave "Pixel" Bowman</h3>
          <span class="artist-role">Creative Director</span>
          <p class="artist-bio">Architecte visuel. Il ne voit pas le monde en atomes mais en Flexbox et CSS Grid.</p>

          <div class="artist-schedule">
            <h4>📅 Missions planifiées :</h4>
            <ul>
              <li>
                <span class="schedule-time">11:45</span>
                <span class="schedule-room">Salle Mars</span>
                <span class="schedule-title">CSS Grid : Alignement</span>
              </li>
            </ul>
          </div>
        </div>
      </article>
    </a>

    <a href="detailsArtiste.php" class="card-link">
      <article class="artist-card">
        <div class="artist-photo">
          <div class="avatar-placeholder big" style="border-color: #ef4444;">HAL</div>
        </div>
        <div class="artist-content">
          <h3>H.A.L. 9000</h3>
          <span class="artist-role">IA Autonome</span>
          <p class="artist-bio">Intelligence artificielle devenue conférencière. Elle promet de ne pas fermer les portes...</p>

          <div class="artist-schedule">
            <h4>📅 Missions planifiées :</h4>
            <ul>
              <li>
                <span class="schedule-time">14:30</span>
                <span class="schedule-room">Salle Jupiter</span>
                <span class="schedule-title">L'IA et vous</span>
              </li>
            </ul>
          </div>
        </div>
      </article>
    </a>

    <a href="detailsArtiste.php" class="card-link">
      <article class="artist-card not-programmed">
        <div class="artist-photo">
          <div class="avatar-placeholder big" style="border-color: #94a3b8; color: #94a3b8;">JR</div>
        </div>
        <div class="artist-content">
          <h3>Junior The Explorer</h3>
          <span class="artist-role">Stagiaire de l'Espace</span>
          <p class="artist-bio">Passionné par le HTML, il cherche encore sa voie lactée. En attente d'affectation.</p>

          <div class="no-schedule">
            <em>🚫 Aucune mission planifiée pour le moment.</em>
          </div>
        </div>
      </article>
    </a>

  </div>
</main>
<?php
include 'header-footer/footer.php';
?>