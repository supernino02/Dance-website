<?php
//File che inizializza le costanti e l'autoloader.
//Rappresenta il "nucleo" minimo per il corretto funzionamento.

/* ------------------------------ 
   Definisco la root del server.
   Se in localhost, di default é:
      define("ROOT_DIR", $_SERVER['DOCUMENT_ROOT']);

   Definisco delle limitazioni per i valori ottenuti dal client nel  GATEWAY.
      - nome del servizio richiesto: massimo 64 caratteri.
      - valori dei parametri passati: massimo 256  caratteri.
      - numero dei parametri passati: massimo 8.

   Definisco se attivare o meno la modalità di debug.
      - Se disattivata, nasconde le motivazioni dei FAIL di GATEWAY.php.
      - I valori vengono comunque salvati nei file di log e di error_log.
   ------------------------------ */
define("ROOT_DIR", "/chroot/home/S5175710");

define("MAX_SERVICE_SIZE", 64);
define("MAX_ARGUMENTS_SIZE", 256);
define("MAX_ARGUMENTS_NUMBER",8);

define("DEBUG_MODE", true);
if (!DEBUG_MODE) error_reporting(0);  //se necessario, disabilito la notifica degli errori

/* ------------------------------ 
   Importo le funzioni di utility.

   Sono funzioni utilizzate dal GATEWAY per soddisfare le richieste.
   ------------------------------ */
require_once "functions.php";

/* ------------------------------ 
   Dichiaro costanti di configurazione per i PATH.
   ------------------------------ */
define("PURCHASABLES_PATH",  absolutePath("PURCHASABLES/"));                             //La cartella che contiene i file acquistabili relativi ai prodotti (es. lezioni registrate).
define("PUBLIC_PATH",        absolutePath("public_html/"));                              //La cartella che contiene i file pubblici che possono essere richiesti dall'utente.
define("COOKIE_FILE_PATH",   absolutePath("COOKIES/cookies_descriptions.json"));         //Il file in cui sono definite le informazioni sui cookie .
define("DB_CONFIG_PATH",     absolutePath('DB/config.json'));                            //Il file che contiene le informazioni per la connessione DB SQL.
define("LOG_CONFIG_PATH",    absolutePath('LOG/config.json'));                           //Il file che contiene le informazioni per la modalitá di LOGGING di errori e servizi.
define("AUTOLOADER_MAP_PATH",absolutePath("BOOTSTRAP/autoloader/locations_map.json"));   //Il file che contiene un oggetto che associa a ogni classe/trait il suo file di provenienza.

/* ------------------------------ 
    Dichiaro costanti di configurazione per il LOG.
    Sono definite nel file json LOG_CONFIG_PATH.
   ------------------------------ */
$log_config = json_decode(file_get_contents(LOG_CONFIG_PATH), true);

define("LOG_ERRORS_PATH", absolutePath($log_config['LOG_ERRORS_PATH']));   //Il file in cui salvo tutti i Fatal Errors incontrati.
define("LOG_FILE_PATH",   absolutePath($log_config['LOG_FILE_PATH']));     //Il file in cui salvo tutti i servizi richiesti e soddisfatti (se LOG_MODE='FILE').
define("LOG_QUERIES_PATH", absolutePath($log_config['LOG_QUERIES_PATH'])); //Il file in cui sono descritte le query necessarie per il logging tramite SQL (se LOG_MODE='SQL').

/* ------------------------------ 
   Importo un autoloader custom.
   Non devo esplicare gli include_once per ogni file che contiene le classi e i trait, 
   vengono caricati in modo autonomo SOLO quando sono necessari.
   
   Evito di importare file il cui contenuto è inutile, come ad esempio i trai per servizi di cui non ho il permesso, 
   o classi di eccezioni che non ho alzato.
   ------------------------------ */
require_once absolutePath("BOOTSTRAP/autoloader/autoloader.php");   