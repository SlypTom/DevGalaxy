<?php
include 'header-footer/header.php';
?>
<main class="page-prestations">

    <div class="center">
        <h1>Catalogue des Missions</h1>
        <p>Explorez les modules de formation et les conférences disponibles.</p>
    </div>

    <div class="search-section">
        <form action="#" method="GET" class="search-form">

            <div class="form-group-inline">
                <label for="search">Mots-clés</label>
                <input type="text" id="search" name="search" placeholder="Ex: SQL, CSS...">
            </div>

            <div class="form-group-inline">
                <label for="artist-select">Pilote (Artiste)</label>
                <select id="artist-select" name="artist">
                    <option value="">-- Tous les pilotes --</option>
                    <option value="1">Sarah Connor</option>
                    <option value="2">Dave Bowman</option>
                    <option value="3">HAL 9000</option>
                </select>
            </div>

            <div class="form-group-inline">
                <label for="category-select">Type de mission</label>
                <select id="category-select" name="category">
                    <option value="">-- Toutes catégories --</option>
                    <option value="backend">Backend & Sécurité</option>
                    <option value="frontend">Frontend & Design</option>
                    <option value="ai">Intelligence Artificielle</option>
                </select>
            </div>

            <div class="form-group-inline checkbox-group">
                <label class="checkbox-container">
                    <input type="checkbox" id="filter-available">
                    <span class="checkmark"></span>
                    Missions confirmées uniquement
                </label>
            </div>

            <button type="submit" class="btn-search">Rechercher</button>
        </form>
    </div>

    <div class="prestations-grid">

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
                    <p class="author">Proposé par : <strong>Sarah "Loop" Connor</strong></p>
                    <p class="description">Découvrez comment protéger vos bases de données contre les attaques les plus modernes.</p>
                </div>
                <div class="card-footer">
                    <div class="status-indicator active">
                        <span class="icon">📅</span>
                        <span>10:30 - Salle Jupiter</span>
                    </div>
                </div>
            </article>
        </a>

        <a href="detailsPrestation.php" class="card-link">
            <article class="prestation-card programmed">
                <div class="card-header">
                    <span class="category-badge frontend">Frontend</span>
                    <div class="prestation-img-placeholder" style="background: linear-gradient(45deg, #431407, #7c2d12);">
                        <code>display: grid;</code>
                    </div>
                </div>
                <div class="card-body">
                    <h3>CSS Grid : Alignez vos planètes</h3>
                    <p class="author">Proposé par : <strong>Dave "Pixel" Bowman</strong></p>
                    <p class="description">Fini les float. Créez des layouts complexes qui défient la gravité.</p>
                </div>
                <div class="card-footer">
                    <div class="status-indicator active">
                        <span class="icon">📅</span>
                        <span>11:45 - Salle Mars</span>
                    </div>
                </div>
            </article>
        </a>

        <a href="detailsPrestation.php" class="card-link">
            <article class="prestation-card catalog">
                <div class="card-header">
                    <span class="category-badge security">Audit</span>
                    <div class="prestation-img-placeholder" style="background: linear-gradient(45deg, #022c22, #064e3b);">
                        <code>******</code>
                    </div>
                </div>
                <div class="card-body">
                    <h3>Audit de Sécurité Privé</h3>
                    <p class="author">Proposé par : <strong>Sarah "Loop" Connor</strong></p>
                    <p class="description">Une analyse complète de votre code pour détecter les failles critiques.</p>
                </div>
                <div class="card-footer">
                    <div class="status-indicator inactive">
                        <span class="icon">📡</span>
                        <span>Disponible sur demande</span>
                    </div>
                </div>
            </article>
        </a>

        <a href="detailsPrestation.php" class="card-link">
            <article class="prestation-card catalog">
                <div class="card-header">
                    <span class="category-badge frontend">Design</span>
                    <div class="prestation-img-placeholder" style="background: linear-gradient(45deg, #4c1d95, #6d28d9);">
                        <code>UI/UX</code>
                    </div>
                </div>
                <div class="card-body">
                    <h3>Review de Design System</h3>
                    <p class="author">Proposé par : <strong>Dave "Pixel" Bowman</strong></p>
                    <p class="description">Optimisation de vos composants pour une meilleure accessibilité.</p>
                </div>
                <div class="card-footer">
                    <div class="status-indicator inactive">
                        <span class="icon">📡</span>
                        <span>Disponible sur demande</span>
                    </div>
                </div>
            </article>
        </a>

    </div>
</main>
<?php
include 'header-footer/footer.php';
?>