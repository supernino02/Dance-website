<?php
phpinfo();
include "../BOOTSTRAP/frontend/initializer.php";
rememberVisit();
?>

<!DOCTYPE html>
<html lang="it">

<?php ComponentManager::includeComponent("head"); ?>

<body class="index-page">

    <?php ComponentManager::includeComponent("header"); ?>

    <main class="main">

        <!-- Home Section -->
        <?php ComponentManager::includeComponent("index_banner"); ?>

        <!-- Corsi Section -->
        <?php ComponentManager::includeComponent("index_courses_intro"); ?>

        <hr class="border-5 purple" data-aos="fade-up">

        <!-- Prodotti Section -->
        <?php ComponentManager::includeComponent("index_best_courses"); ?>

        <!-- Collaboratori Section -->
        <?php ComponentManager::includeComponent("index_collaborators"); ?>

        <!-- Eventi Section -->
        <?php ComponentManager::includeComponent("index_closest_events"); ?>

        <!-- Membri Section -->
        <?php ComponentManager::includeComponent("index_members"); ?>

        <!-- Contact Section -->
        <?php ComponentManager::includeComponent("index_contacts"); ?>

    </main>

    <?php ComponentManager::includeComponent("footer"); ?>

    <?php
    ComponentManager::includeAllJsFiles();
    ComponentManager::includeAllCssFiles();
    ?>
</body>

</html>