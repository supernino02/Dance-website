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
            <h2>Privacy Policy</h2>
            <p class='mb-0'>La presente informativa sulla privacy descrive le modalità di raccolta, utilizzo e protezione dei dati personali degli utenti del nostro sito. Utilizzando il nostro sito, acconsenti alle pratiche descritte in questa informativa.</p>
        ";
    ?>

    <section class="section privacy-policy-section aos-init aos-animate" data-aos="fade-up">
      <div class="container">
        <?php ComponentManager::includeComponent("page_title"); ?>

        <h4>1. Informazioni raccolte</h4>
        <p>
          Durante l'utilizzo del nostro sito web, potremmo raccogliere informazioni personali come nome, indirizzo
          email, numero di telefono e altre informazioni fornite volontariamente dall'utente tramite moduli di contatto
          o iscrizioni.
        </p>

        <h4>2. Modalità di utilizzo delle informazioni</h4>
        <p>
          Le informazioni raccolte possono essere utilizzate per migliorare l'esperienza utente, fornire assistenza,
          inviare comunicazioni di marketing o newsletter e migliorare i nostri servizi. Non condividiamo le
          informazioni personali con terze parti senza il consenso esplicito dell'utente, salvo quanto previsto dalla
          legge.
        </p>

        <h4>3. Conservazione dei dati</h4>
        <p>
          Conserviamo i dati personali degli utenti solo per il tempo necessario a fornire i servizi richiesti e per
          altri scopi legittimi, come il rispetto degli obblighi legali. Una volta che i dati non sono più necessari,
          verranno eliminati in modo sicuro.
        </p>

        <h4>4. Sicurezza dei dati</h4>
        <p>
          Utilizziamo misure di sicurezza adeguate per proteggere i dati personali da accessi non autorizzati,
          alterazioni, divulgazioni o distruzioni. Tuttavia, nessun metodo di trasmissione su Internet o di
          archiviazione elettronica è completamente sicuro, pertanto non possiamo garantire la sicurezza assoluta.
        </p>

        <h4>5. Cookie</h4>
        <p>
          Il nostro sito utilizza cookie per raccogliere informazioni sull'uso del sito, migliorare le prestazioni e
          personalizzare l'esperienza utente. Gli utenti possono disabilitare i cookie tramite il pulsante in basso a
          sinistra o alle impostazioni del browser, ma alcune funzionalità del sito potrebbero non funzionare
          correttamente senza di essi.
        </p>

        <h4>6. Diritti degli utenti</h4>
        <p>
          Gli utenti hanno il diritto di accedere, rettificare o cancellare i propri dati personali. Possono anche
          opporsi al trattamento dei dati o richiedere la limitazione del trattamento. Per esercitare questi diritti, è
          possibile <a href='index.php#contatti'>contattarci nell'apposita sezione</a>.
        </p>

        <h4>7. Modifiche alla Privacy Policy</h4>
        <p>
          Ci riserviamo il diritto di modificare questa informativa sulla privacy in qualsiasi momento. Le modifiche
          verranno pubblicate su questa pagina, quindi ti invitiamo a controllarla periodicamente per eventuali
          aggiornamenti.
        </p>

        <h4>8. Contatti</h4>
        <p>
          Per ulteriori informazioni sulla nostra politica sulla privacy o per eventuali domande, non esitare a <a
            href="index.php#contatti">contattarci</a>.
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