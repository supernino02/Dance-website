<?php
include "../BOOTSTRAP/frontend/initializer.php";

rememberVisit();
//se non sono loggato e non riesco a loggarmi col token di sessione (eventuale)
if (!userIsLogged())
    redirect("login.php");
?>
<!DOCTYPE html>
<html lang="it">

<?php ComponentManager::includeComponent("head"); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<body class="showprofile-page">

    <?php ComponentManager::includeComponent("header"); ?>

    <main class="main">
        <!-- Page Title -->
        <?php
        $PAGE_TITLE = '
            <h1>Profilo</h1>
            <p class="mb-0">Ciao <b class="user_name"></b>!</p>
            <p>In questa sezione potrai modificare le informazioni inserite in fase di registrazione.</p>
        ';
        ComponentManager::includeComponent("page_title");
        ?>
        <!-- End Page Title -->

        <!-- Show Profile Section -->
        <?php ComponentManager::includeComponent("profile_personal_area"); ?>


        <?php ComponentManager::includeComponent("profile_orders"); ?>
        <!-- End Orders Section -->

    </main>

    <?php ComponentManager::includeComponent("footer"); ?>
<?php
ComponentManager::includeAllJsFiles();
ComponentManager::includeAllCssFiles();
 ?>
</body>


</html>