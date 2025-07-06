<div id="preloader"></div>
<header id="header" class="header d-flex align-items-center fixed-top">
  <div id="headerDiv" class="container-fluid container position-relative d-flex align-items-center justify-content-between">

    <a href="index.php" class="logo d-flex align-items-center me-auto me-lg-0">
      <img src="MULTIMEDIA/imgs/logo.png" alt="logo FBS Latin empire">
      <h1 class="sitename">FBS Latin Empire</h1>
      <h1 class="sitename-small">FBS</h1>
    </a>

    <nav id="navmenu" class="navmenu">
      <ul tabindex="0">
        <li>
          <a href="index.php">Home</a>
        </li>

        <li class="dropdown">
          <a href="courses.php" class="course-type-btn" data-filter="Tutti">
            <span>Corsi </span>
            <i id="coursesCollapseIcon" class="bi bi-chevron-down toggle-dropdown"></i>
          </a>
          <ul>
            <?php
            global $course_types;
            foreach ($course_types as $course) {
              $type = $course['type'];
              // Genera i bottoni con l'evento click per salvare il tipo in localStorage
              //applico la funzione definita in header.js
              echo "
              <li>
                <a href='courses.php' class='course-type-btn' data-filter='{$type}' tabindedx='0'>{$type}</a>
              </li>" . PHP_EOL;
            }
            ?>
          </ul>
        </li>

        <li>
          <a href="events.php">Eventi</a>
        </li>

        <li>
          <a href="index.php#contatti">Contatti</a>
        </li>

        <!-- Start Optional Functionalities -->
        <?php if (userIsLogged()): ?>
          <li><a class="btn-navicon" href="profile.php"><i class="bi bi-person-circle"></i></a></li>
          <li><a id="btn-logout" href="#" class="btn-navicon"><i class="bi bi-box-arrow-left"></i></a></li>
          <li>
            <a class="btn-navicon position-relative" href="cart.php">
              <i class="bi bi-cart position-relative">
                <!-- Badge -->
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                  id="cart-badge">
                  0 <!-- Questo verrà aggiornato dinamicamente con jQuery -->
                </span>
              </i>
            </a>
          </li>
        <?php else: ?>
          <li><a class="btn-login" href="login.php">Accedi</a></li>
          <li><a class="btn-register" href="registration.php">Registrati</a></li>
        <?php endif; ?>
        <!-- End Optional Functionalities -->

        <!-- Motore di ricerca -->
        <li>
          <a id="btn-search" class="btn-navicon" href="#">
            <i class="bi bi-search"></i>
          </a>
        </li>

      </ul>
      <i id="navCollapsedIcon" class="mobile-nav-toggle d-xl-none bi bi-list" tabindex="0"></i>
    </nav>

  </div>
</header>



<!-- Barra di ricerca in sovrimpressione -->
<div id="searchOverlay" class="search-overlay">
  <div class="search-container">
    <div class="search-title text-center">
      <h1>Cerca Prodotti</h1>
    </div>
    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Cerca...">
      <button id="closeSearch" class="close-search"><i class="bi bi-x"></i></button>
    </div>
    <div id="searchResults" class="search-results">
      <!-- Risultati della ricerca caricati qui -->
    </div>
  </div>
</div>