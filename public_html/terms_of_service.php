<?php 
  include "../BOOTSTRAP/frontend/initializer.php";
  rememberVisit();
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
            <h2>Termini di Servizio</h2>
            <p class='mb-0'>Questi termini e condizioni disciplinano l'uso di questo sito web. Utilizzando il nostro sito, accetti i seguenti termini e condizioni nella loro interezza. Se non sei d'accordo con qualsiasi parte di questi termini, ti preghiamo di non utilizzare il nostro sito.</p>
        ";
    ?>

    <section class="section terms-of-service-section aos-init aos-animate" data-aos="fade-up">
      <div class="container">
        <?php ComponentManager::includeComponent("page_title"); ?>

        <h4>1. Utilizzo del sito web</h4>
        <p>
          L'accesso al nostro sito è consentito su base temporanea e ci riserviamo il diritto di modificare o interrompere i servizi senza preavviso. Non siamo responsabili se per qualsiasi motivo il sito non è disponibile in qualsiasi momento o per qualsiasi periodo.
        </p>

        <h4>2. Diritti di proprietà intellettuale</h4>
        <p>
          Tutti i contenuti presenti su questo sito, inclusi ma non limitati a testi, grafica, loghi, immagini e software, sono protetti dalle leggi sul diritto d'autore e sui marchi. È vietata la riproduzione, distribuzione o modifica non autorizzata del contenuto senza il nostro esplicito consenso scritto.
        </p>

        <h4>3. Responsabilità</h4>
        <p>
          Non garantiamo che le informazioni sul nostro sito siano accurate o complete. Non saremo responsabili per danni diretti, indiretti o consequenziali derivanti dall'uso o dall'incapacità di utilizzare il sito o per qualsiasi materiale in esso contenuto.
        </p>

        <h4>4. Modifiche ai termini</h4>
        <p>
          Ci riserviamo il diritto di modificare questi termini in qualsiasi momento. Le modifiche saranno efficaci dal momento in cui verranno pubblicate sul nostro sito. È tua responsabilità verificare periodicamente eventuali aggiornamenti.
        </p>

        <h4>5. Legge applicabile</h4>
        <p>
          Questi termini sono regolati dalla legge italiana. Qualsiasi controversia relativa a questi termini sarà soggetta alla giurisdizione esclusiva dei tribunali italiani.
        </p>

        <h4>6. Contatti</h4>
        <p>
          Se hai domande sui nostri termini di servizio, non esitare a <a href="index.php#contatti">contattarci</a>.
        </p>
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