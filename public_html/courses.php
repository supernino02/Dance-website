<?php
include "../BOOTSTRAP/frontend/initializer.php";
rememberVisit();
?>
<!DOCTYPE html>
<html lang="it">

<?php ComponentManager::includeComponent("head"); ?>

<body class="service-details-page">

  <?php ComponentManager::includeComponent("header"); ?>

  <main class="main">

    <!-- Page Title -->
    <?php
    $PAGE_TITLE = "
      <h1>Tutti i nostri Corsi</h1>
      <p class='mb-0'>Acquista Lezioni Online, Private e Collettive</p>
    ";
    ComponentManager::includeComponent("page_title");
    ?>

    <!-- Service Details Section -->
    <section id="products" class="products section">
      <div class="container">
        <div class="row gy-5">
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
            <!-- Filter List -->
            <div class="service-box">
              <div id="filtro-tipo" class="filtro-products">
                <h4 id="titoloFiltroTipo" data-bs-toggle="collapse" data-bs-target="#collapseFiltroTipo" aria-expanded="true"
                  aria-controls="collapseFiltroTipo" tabindex="0">
                  Filtra per Tipo <i class="bi bi-chevron-down float-end"></i>
                  <span class="d-block text-start filter-value mt-2 capitalizeFirstLetter"></span>
                </h4>
                <div id="collapseFiltroTipo" class="collapse">
                  <?php
                  echo "
                            <a href='#'>
                                <i class='bi bi-arrow-right-circle'></i>
                                <span>Tutti</span>
                            </a>";

                  global $course_types;
                  foreach ($course_types as $course) {
                    $type = $course['type'];

                    echo "
                                <a href='#' data-filter='{$type}'>
                                    <i class='bi bi-arrow-right-circle'></i>
                                    <span>{$type}</span>
                                </a>";
                  }
                  ?>
                </div>
              </div>

              <br>
              <div id="filtro-livello" class="filtro-products">
                <h4 id="titoloFiltroLivello" data-bs-toggle="collapse" data-bs-target="#collapseFiltroLivello" aria-expanded="true"
                  aria-controls="collapseFiltroLivello" tabindex="0">
                  Filtra per Livello <i class="bi bi-chevron-down float-end"></i>
                  <span class="d-block text-start filter-value mt-2 capitalizeFirstLetter"></span>
                </h4>
                <div id="collapseFiltroLivello" class="collapse">
                  <div id="loadingSpinnerFilterLevel" class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                  <!-- i filtri livello verranno inseriti tramite ajax -->
                </div>
              </div>

              <br>
              <div id="filtro-disciplina" class="filtro-products">
                <h4 id="titoloFiltroDisciplina" data-bs-toggle="collapse" data-bs-target="#collapseFiltroDisciplina" aria-expanded="true"
                  aria-controls="collapseFiltroDisciplina" tabindex="0">
                  Filtra per Disciplina <i class="bi bi-chevron-down float-end"></i>
                  <span class="d-block text-start filter-value mt-2 capitalizeFirstLetter"></span>
                </h4>
                <div id="collapseFiltroDisciplina" class="collapse">
                  <div id="loadingSpinnerFilterDiscipline" class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                  <!-- i filtri disciplina verranno inseriti tramite ajax -->
                </div>
              </div>
            </div>
            <!-- End Filter List -->

            <!-- Help Box -->
            <div class="service-box d-flex flex-column justify-content-center align-items-center d-none d-lg-flex mt-4">
              <i class="bi bi-headset help-icon"></i>
              <h4>Hai domande?</h4>
              <a href="index.php#contatti" class="btn btn-purple btn-purple-focus-purple">
                <i class="bi bi-arrow-right"></i>
                Contattaci
                <i class="bi bi-arrow-left"></i>
              </a>
            </div>
            <!-- End Help Box -->
          </div>

          <!-- Prodotti Container -->
          <div class="col-lg-8 ps-lg-5" data-aos="fade-up" data-aos-delay="200">
            <div id="loadingSpinnerProducts" class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <div id="products-container" class="row gy-5">
              <!-- i corsi verranno inseriti tramite ajax -->
            </div>
          </div>
          <!-- End Prodotti Container -->
        </div>

        <!-- Help Box per dispositivi piccoli -->
        <div class="row d-lg-none">
          <div class="col-12 mt-4">
            <div class="service-box d-flex flex-column justify-content-center align-items-center">
              <i class="bi bi-headset help-icon"></i>
              <h4>Hai domande?</h4>
              <a href="index.php#contatti" class="btn btn-purple btn-purple-focus-purple">
                <i class="bi bi-arrow-right"></i>
                Contattaci
                <i class="bi bi-arrow-left"></i>
              </a>
            </div>
          </div>
        </div>
        <!-- End Help Box per dispositivi piccoli -->
      </div>
    </section>
    <!-- /Service Details Section -->

  </main>

  <?php ComponentManager::includeComponent("footer"); ?>

  <?php
  ComponentManager::includeAllJsFiles();
  ComponentManager::includeAllCssFiles();
  ?>
</body>

</html>