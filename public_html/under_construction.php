<?php
include "../BOOTSTRAP/frontend/initializer.php";
?>

<!DOCTYPE html>
<html lang="it">

<?php ComponentManager::includeComponent("head"); ?>

<body class="index-page">

  <?php ComponentManager::includeComponent("header"); ?>

  <main class="main">

    <!-- Page Title -->
    <?php
    $PAGE_TITLE = "
            <h2>Work in Progress</h2>
            <p class='mb-0'>Stiamo lavorando per offrirti una nuova esperienza. Torna a visitarci presto!</p>
        ";
    ?>

    <section id="under-construction" class="section under-construction-section">
      <div class="container text-center">
        <?php ComponentManager::includeComponent("page_title"); ?>
        <img src="MULTIMEDIA/imgs/under_construction.png" alt="lavori in corso" class="img-fluid">
        <br>
        <a href="index.php" class="btn btn-purple btn-purple-focus-purple">Torna alla Home</a>
      </div>
    </section>

  </main>

  <?php
  ComponentManager::includeComponent("footer");
  ComponentManager::includeAllJsFiles();
  ComponentManager::includeAllCssFiles();
  ?>
</body>

</html>