<?php

/* ------------------------------ 
   Importo le variabili di sessione
      - Richiedo il lock per lettura e scrittura.
      - Rilascio il lock per la scrittura, posso solo leggerne il contenuto ma non modificarlo.

   Utilizzando la sessione in questo modo, 
   permetto di gestire un multithreading molteplici richieste dallo stesso client.
   ------------------------------ */
if (!session_start() || !session_write_close())
   throw new Exception("Cannot start SESSION correctly");

/* ------------------------------ 
   Definisco il file in cui gli oggetti CookieManager ricavano le informazioni sui cookie utilizzati.
   Forniscono un interfaccia per gestire i cookie scambiati con il client.
   ------------------------------ */
CookieManager::setCookiesDescriptionFile(COOKIE_FILE_PATH);

/* ------------------------------ 
    Definisco l'oggetto LogHandler che effettue il logging.
    Utilizza la metodologia indicata in LOG_MODE.
    Può utilizzare 3 metodi alternativi.
      - Log su file.
      - Log su sql.
      - Nessun log.
   ------------------------------ */
$LOG_HANDLER = match ($log_config['LOG_MODE']) {
   'NONE'  => new LogNone(),                 //Non salva nessuna informazione sui servizi forniti
   'SQL'   => new LogSQL(LOG_QUERIES_PATH),  //Salva informazioni utilizzando il DB
   'FILE'  => new LogFile(LOG_FILE_PATH),    //Salva informazioni utilizzando un file di testo
   default => throw new Exception("Invalid LogHandler {$log_config['LOG_MODE']} defined in " . LOG_CONFIG_PATH),
};

/* ------------------------------ 
    Dichiaro costanti di configurazione per il DB.
    Sono definite nel relativo file DB_CONFIG_PATH.
    Instauro una connessione con un server, non per forza in locale.
   ------------------------------ */
$conn_config = json_decode(file_get_contents(DB_CONFIG_PATH), true);

/* ------------------------------ 
    Inizializzo la connessione la server SQL.
    Rimuovo il report di errors warnings, per poter alzare eccezioni custom.
    La inizializzo in modo che in caso di warning o fail del DB, sia alzata un eccezione.
   ------------------------------ */
mysqli_report(MYSQLI_REPORT_OFF); //tolgo il report 
$CONN = @new mysqli(              // @ indica eventualmente di sopprimere i warning
   $conn_config["server_name"],
   $conn_config["user_name"],
   $conn_config["password"],
   $conn_config["db_name"],
   $conn_config["port"],        //non usato se in locale (default: null)
   $conn_config["socket"]       //non usato se in locale (default: null)
);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //metto il report di default per PHP 8.1

//verifico la connessione sia stata creata correttamente
if ($CONN->connect_error)
   throw new UnreachableDBException($conn_config);

/* ------------------------------ 
    Definisco l'oggetto DB che fornisce una facade sul DB.
    Utilizza la connessione creata in precedenza, e l' oggetto per il LOG.
    Definisco una costante SQL_INTERFACE_FILES che definisce i file che contengono le query chiamabili.
   ------------------------------ */
define("SQL_INTERFACE_FILES", $conn_config['INTERFACE_FILES']);  
$DB = new VirtualDB($CONN, $LOG_HANDLER, SQL_INTERFACE_FILES);

/* ------------------------------ 
    Definisco l'oggetto SERVICES_HANDLER che fornisce un interfaccia per i servizi.
    Utilizza l' interfaccie per il DB e l' oggetto di LOG.
   ------------------------------ */
$SERVICES_HANDLER = new ServicesHandler($DB, $LOG_HANDLER);
