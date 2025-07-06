<?php
include "../BOOTSTRAP/frontend/initializer.php";
rememberVisit();

//sono le variabili che passerò al js
$PRODUCT; //obj
$PURCHASABLE; //bool

if (!loadProduct())
  redirect_ERROR_PAGE("for some reason the server cannot retrieve product informations");
//a questo punto le 2 variabili sono inizializzate

/**
 * CARICO IL PRODOTTO, in base a cosa mi ha chiesto l' utente.
 * Il prodotto viene poi stampato e passato al JS per continuare con l' hidratation della pagina
 * @return bool
 */
function loadProduct()
{
  global $PRODUCT, $PURCHASABLE, $SERVICES_HANDLER;

  $virtual_cookie = new CookieManager("ID_PRODUCT");

  //lo provo a prendere da url
  if (isset($_REQUEST['id']) && !empty($id_product = $_REQUEST['id'])) {
    //provo a ottenere le info dal server
    $product_result = $SERVICES_HANDLER->callService("getProduct", [$id_product, true]);
    if (Result::isOK($product_result)) { //esiste ed è acquistabile
      //!crea il cookie
      $virtual_cookie->defineCookie($id_product);

      $PRODUCT = $product_result->getValue();
      $PURCHASABLE = true;
      return true;
    }
    if (Result::isERROR($product_result)) { //esiste, ma non è acquistabile
      $PRODUCT = $product_result->getValue();
      $PURCHASABLE = false;
      return true;
    }
  }

  //se il cokie è indicato e non é null
  if ($id_product = $virtual_cookie->obtainCookie()) {

    //provo a ottenere le info dal server
    $product_result = $SERVICES_HANDLER->callService("getProduct", [$id_product, true]);
    if (Result::isOK($product_result)) { //esiste ed è acquistabile
      $PRODUCT = $product_result->getValue();
      $PURCHASABLE = true;
      return true;
    }
    if (Result::isERROR($product_result)) { //esiste, ma non è acquistabile
      $virtual_cookie->deleteCookie();

      $PRODUCT = $product_result->getValue();
      $PURCHASABLE = false;
      return true;
    }
  }
  //a questo punto provo a prendere le info dai best seller
  $product_result = $SERVICES_HANDLER->callService("getBestSellingProducts", [1, 'ALL', true]);
  if (Result::isOK($product_result)) {
    $PRODUCT = $product_result->getValue();
    $PURCHASABLE = true;
    return true;
  }
  //se per qualche motivo non riesco a caricare il valore
  return false;
}

?>

<!DOCTYPE html>
<html lang="it">

<?php ComponentManager::includeComponent("head"); ?>

<body class="service-details-page">
  <script>
    //! FACCIO OTTENERE AL JS I DATI SUL PRODOTTO
    var productObj = <?= json_encode($PRODUCT, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES) ?>;
    var isPurchasable = <?= $PURCHASABLE ? "true" : "false" ?>;
    var isLogged = <?= userIsLogged() ? "true" : "false" ?>;
  </script>

  <?php ComponentManager::includeComponent("header"); ?>

  <main class="main">

    <?php
    $PAGE_TITLE = "<h1>{$PRODUCT['name']}</h1>";
    ComponentManager::includeComponent("page_title");
    ?>
    <!-- Service Details Section -->
    <section id="products" class="products section">

      <div class="container">

        <div class="row gy-5">

          <!--carosello e informazioni-->
          <?php ComponentManager::includeComponent("product_infos"); ?>

          <!--Review-->
          <?php ComponentManager::includeComponent("product_reviews"); ?>


          <!-- Prodotti correlati -->
          <?php ComponentManager::includeComponent("product_recommended"); ?>

        </div>
      </div>


    </section><!-- /Service Details Section -->

  </main>

  <?php ComponentManager::includeComponent("footer"); ?>

<?php
ComponentManager::includeAllJsFiles();
ComponentManager::includeAllCssFiles();
 ?>
</body>


</html>