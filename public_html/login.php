<?php
include "../BOOTSTRAP/frontend/initializer.php";

//if (userIsLogged()) redirect("index.php");

/*** FORGET PSW BUTTON ***/
// Definisco i valori precompilati
$to = "fbslatinempire@gmail.com";
$encoded_subject = rawurlencode("Ho dimenticato la password 🤦‍♂️");
$encoded_body = rawurlencode(
  "Caro Admin,

    Sembra che la mia memoria abbia deciso di prendersi una pausa  👁👄👁 
    e non riesco proprio a ricordare la mia password.

    Mi faresti il grande favore di ridirmela?👉👈🤭
    Prometto di non dimenticarla di nuovo (almeno fino alla prossima volta)! 😉

    Grazie mille e buona giornata!👁👅👁  
    Saluti, da un utente che non si può più loggare🥴"
);

// Creazione del link mailto
$forget_psw_href = "mailto:{$to}?subject={$encoded_subject}&body={$encoded_body}";

?>

<!DOCTYPE html>
<html lang="it">

<?php ComponentManager::includeComponent("head"); ?>

<body class="login-page">

  <?php ComponentManager::includeComponent("header"); ?>

  <main class="main">


    <!-- Page Title -->
    <?php
    $PAGE_TITLE = "
      <h1>Accesso</h1>
      <p class='mb-0'>Inserisci le tue credenziali per accedere</p>
    ";
    ComponentManager::includeComponent("page_title");
    ?>
    <!-- End Page Title -->

    <!-- Login Section -->
    <section id="login" class="login section">

      <div class="container">
        <div class="row justify-content-center">

          <div class="col-lg-6 col-md-8 col-sm-12" data-aos="fade-up" data-aos-delay="100">
            <div class="service-box">
              <h4>Accedi</h4>

              <form id="loginForm">
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="text" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-3">
                  <label for="password" class="form-label">Password</label>
                  <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="row align-items-center justify-content-between mb-3 pt-3">
                  <!-- Submit button column -->
                  <div class="col-6 d-flex align-items-center">
                    <button id="loginButton" type="submit" class="btn btn-purple btn-purple-focus-purple w-100">Accedi</button>
                  </div>
                  <!-- Remember me column -->
                  <div class="col-6 d-flex align-items-center">
                    <input type="checkbox" class="form-check-input me-2" id="rememberMe" name="rememberMe">
                    <label class="form-check-label" for="rememberMe">Remember Me</label>
                  </div>
                </div>

                <div class="text-start mb-4">
                  <a href="<?= $forget_psw_href ?>" target="_blank">Password dimenticata?</a>
                </div>

                <div class="text-center mb-3">
                  <span>Oppure</span>
                </div>

                <div class="text-center">
                  <a href="registration.php" class="btn btn-outline-secondary w-100">Registrati</a>
                </div>
              </form>

              <div id="loginResult" class="mt-3"></div>
            </div>

          </div>
          <!-- End Login Box -->

        </div>
      </div>

    </section>
    <!-- End Login Section -->
  </main>

  <?php ComponentManager::includeComponent("footer"); ?>
  <?php
  ComponentManager::includeAllJsFiles();
  ComponentManager::includeAllCssFiles();
  ?>
</body>


</html>