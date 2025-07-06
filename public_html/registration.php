<?php include "../BOOTSTRAP/frontend/initializer.php"; ?>
<!DOCTYPE html>
<html lang="it">

<?php ComponentManager::includeComponent("head"); ?>

<body class="registration-page">

  <?php ComponentManager::includeComponent("header"); ?>

  <main class="main">

    <!-- Page Title -->
    <?php
    $PAGE_TITLE = '
      <h1>Registrazione</h1>
      <p class="mb-0">Inserisci le tue credenziali per registrarti</p>
    ';
    ComponentManager::includeComponent("page_title");
    ?>
    <!-- End Page Title -->

    <!-- Registration Section -->
    <section id="registration" class="registration section">

      <div class="container">
        <div class="row gy-5 d-flex justify-content-center">
          <div class="col-lg-12 col-md-12 col-sm-12" data-aos="fade-up" data-aos-delay="100">

            <div class="service-box">
              <h4>Registrati</h4>

              <form id="registrationForm">
                <div class="row">
                  <div class="col-md-6 mb-4">
                    <label for="name" class="form-label"><b>Nome</b></label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Inserisci il tuo nome"
                      required>
                  </div>

                  <div class="col-md-6 mb-4">
                    <label for="surname" class="form-label"><b>Cognome</b></label>
                    <input type="text" class="form-control" id="surname" name="surname"
                      placeholder="Inserisci il tuo cognome" required>
                  </div>

                  <div class="col-md-6 mb-4">
                    <label for="password" class="form-label"><b>Password</b></label>
                    <input type="password" class="form-control" id="password" name="password"
                      placeholder="Crea una password" required>
                  </div>

                  <div class="col-md-6 mb-4">
                    <label for="repeatPassword" class="form-label"><b>Ripeti Password</b></label>
                    <input type="password" class="form-control" id="repeatPassword" name="repeatPassword"
                      placeholder="Ripeti la password" required>
                  </div>

                  <div class="col-md-6 mb-4">
                    <label for="email" class="form-label"><b>Email</b></label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="esempio@dominio.com"
                      required>
                  </div>

                  <div class="col-md-6 mb-4">
                    <label for="phone_number" class="form-label"><b>Numero di Telefono (opzionale)</b></label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number"
                      placeholder="Inserisci il tuo numero di telefono">
                  </div>

                  <div class="col-md-6 mb-4">
                    <label for="fiscal_code" class="form-label"><b>Codice Fiscale (opzionale)</b></label>
                    <input type="text" class="form-control" id="fiscal_code" name="fiscal_code"
                      placeholder="Inserisci il tuo codice fiscale">
                  </div>

                  <div class="col-md-6 mb-4">
                    <label for="gender" class="form-label"><b>Genere (opzionale)</b></label>
                    <select class="form-control" id="gender" name="gender">
                      <option value="" disabled selected>Seleziona il tuo genere</option>
                      <option value="agender">Agender</option>
                      <option value="abimegender">Abimegender</option>
                      <option value="adamas">Adamas gender</option>
                      <option value="aerogender">Aerogender</option>
                      <option value="aesthetigender">Aesthetigender</option>
                      <option value="affectugender">Affectugender</option>
                      <option value="agenderflux">Agenderflux</option>
                      <option value="alexigender">Alexigender</option>
                      <option value="aliusgender">Aliusgender</option>
                      <option value="amaregender">Amaregender</option>
                      <option value="ambigender">Ambigender</option>
                      <option value="ambonec">Ambonec</option>
                      <option value="amicagender">Amicagender</option>
                      <option value="androgyne">Androgyne</option>
                      <option value="anesigender">Anesigender</option>
                      <option value="angenital">Angenital</option>
                      <option value="anogender">Anogender</option>
                      <option value="anongender">Anongender</option>
                      <option value="antegender">Antegender</option>
                      <option value="anxiegender">Anxiegender</option>
                      <option value="apagender">Apagender</option>
                      <option value="apconsugender">Apconsugender</option>
                      <option value="astergender">Astergender</option>
                      <option value="astralgender">Astral gender</option>
                      <option value="autigender">Autigender</option>
                      <option value="autogender">Autogender</option>
                      <option value="axigender">Axigender</option>
                      <option value="bigender">Bigender</option>
                      <option value="biogender">Biogender</option>
                      <option value="blurgender">Blurgender</option>
                      <option value="boyflux">Boyflux</option>
                      <option value="burstgender">Burstgender</option>
                      <option value="caelgender">Caelgender</option>
                      <option value="cassgender">Cassgender</option>
                      <option value="cassflux">Cassflux</option>
                      <option value="cavusgender">Cavusgender</option>
                      <option value="cendgender">Cendgender</option>
                      <option value="ceterogender">Ceterogender</option>
                      <option value="ceterofluid">Ceterofluid</option>
                      <option value="cisgender">Cisgender</option>
                      <option value="cloudgender">Cloudgender</option>
                      <option value="collgender">Collgender</option>
                      <option value="colorgender">Colorgender</option>
                      <option value="commogender">Commogender</option>
                      <option value="condigender">Condigender</option>
                      <option value="deliciagender">Deliciagender</option>
                      <option value="demifluid">Demifluid</option>
                      <option value="demiflux">Demiflux</option>
                      <option value="demigender">Demigender</option>
                      <option value="domgender">Domgender</option>
                      <option value="duragender">Duragender</option>
                      <option value="egogender">Egogender</option>
                      <option value="epicene">Epicene</option>
                      <option value="esspigender">Esspigender</option>
                      <option value="exgender">Exgender</option>
                      <option value="existigender">Existigender</option>
                      <option value="femfluid">Femfluid</option>
                      <option value="femgender">Femgender</option>
                      <option value="fluidflux">Fluidflux</option>
                      <option value="gemigender">Gemigender</option>
                      <option value="genderblank">Genderblank</option>
                      <option value="genderflow">Genderflow</option>
                      <option value="genderfluid">Genderfluid</option>
                      <option value="genderfuzz">Genderfuzz</option>
                      <option value="genderflux">Genderflux</option>
                      <option value="genderpuck">Genderpuck</option>
                      <option value="genderqueer">Genderqueer</option>
                      <option value="genderwitched">Genderwitched</option>
                      <option value="girlflux">Girlflux</option>
                      <option value="healgender">Healgender</option>
                      <option value="mirrorgender">Mirrorgender</option>
                      <option value="omnigender">Omnigender</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-12 mt-3 text-center">
                  <button type="submit" id="btnSubmit" class="btn btn-purple btn-purple-focus-purple text-center">Registrati</button>
                </div>

              </form>

              <div id="registrationResult" class="mt-3"></div>

            </div>
            <!-- End Registration Box -->

          </div>
        </div>
      </div>

    </section>
    <!-- End Registration Section -->

  </main>

  <?php ComponentManager::includeComponent("footer"); ?>

  <?php
  ComponentManager::includeAllJsFiles();
  ComponentManager::includeAllCssFiles();
  ?>
</body>


</html>