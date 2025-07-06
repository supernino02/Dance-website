<?php
/* ------------------------------ 
   Importo le funzioni di utility del backend.
   ------------------------------ */
require_once "functions.php";

/* ------------------------------ 
   Definisco le costanti di configurazione per il front-end.
   Le costanti del front-end sono relative alla pagina public_html.
   ------------------------------ */
define("ERROR_PAGE",           "error.php");
define("NOT_IMPLEMENTED_PAGE", "under_construction.php");
define("JS_DIRECTORY",         "JS/");
define("CSS_DIRECTORY",        "CSS/");

/* ------------------------------ 
   Inizializzo il gateway, per poterlo utilizzare durante la creazione della pagina.
   ------------------------------ */
try {
  require_once "../BOOTSTRAP/backend/initializer_1.php";
  require_once "../BOOTSTRAP/backend/initializer_2.php";
} catch (Throwable $e) {
  log_error("error including the BOOTSTRAP for the backend".PHP_EOL.$e);
  redirect_ERROR_PAGE("The server is currently under some serious problems, please be patient :(");
}

/* ------------------------------ 
   Definisco la posizione delle componenti per le pagine php.
   Sono file PHP privati, che non possono essere direttamente richiesti dall' utente.
   ------------------------------ */
define("COMPONENTS_DIRECTORY", absolutePath("PHP_COMPONENTS/"));

/* ------------------------------ 
   Inizializzo il ComponentManager definendo
   - in che cartella deve prendere le componenti php.
   - in che cartella deve prendere eventuali file css.
   - in che cartella deve prendere eventuali file js.
   ------------------------------ */
ComponentManager::initialize();
ComponentManager::setcomponentsDirectory(COMPONENTS_DIRECTORY);
ComponentManager::setCssDirectory(CSS_DIRECTORY);
ComponentManager::setJsDirectory(JS_DIRECTORY);

/* ------------------------------ 
   Se l' utente che ha richiesto la pagina non è loggato, provo un eventuale login con token.
   ------------------------------ */
if (!userIsLogged())
  $SERVICES_HANDLER->callService("loginToken");

/* ------------------------------ 
    Ottengo tutti i tipi di corsi.
    Questa informazione viene utilizzata nella componente header.php.
   ------------------------------ */
if (!Result::isOK($result = $SERVICES_HANDLER->callService("getAllCourseTypes"))) {
  //se fallisce è perchè non ha trovato tuple
  log_error("getAllCourseTypes find 0 tuples");
  redirect_ERROR_PAGE("Somehow a very important page-component fail, please be patient :(");
}

$course_types = $result->getValue();
