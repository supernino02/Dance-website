<?php
include "../BOOTSTRAP/frontend/initializer.php";
//se non sono loggato e non riesco a loggarmi col token di sessione (eventuale)
rememberVisit();
if (!userIsLogged())
    redirect("login.php");
?>
<!DOCTYPE html>
<html lang="it">

<?php ComponentManager::includeComponent("head"); ?>

<body>

    <?php ComponentManager::includeComponent("header"); ?>

    <main class="main">

        <!-- Page Title -->
    <?php
        $PAGE_TITLE = "
            <h1>Carrello</h1>
            <p class='mb-0'>Visiona e modifica il tuo carrello prima di procedere al pagamento</p>
        ";
        ComponentManager::includeComponent("page_title");
    ?>

        <!-- Cart Section -->
        <section id="cart" class="cart-section">
            <div class="container">
                <div class="row gy-5">
                    <!-- Products -->
                    <?php ComponentManager::includeComponent("cart_products"); ?>

                    <!-- Payment & Support -->
                    <?php ComponentManager::includeComponent("cart_payment"); ?>
                </div>

            </div>
        </section>


    </main>

    <?php ComponentManager::includeComponent("footer"); ?>

<?php
ComponentManager::includeAllJsFiles();
ComponentManager::includeAllCssFiles();
 ?>
</body>

</html>