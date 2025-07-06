<?php
/**
 * Funzione che utility che date 2 stringhe che descrivono un erorre incontrato,
 * stampano su output un ResultFail e terminano l' esecuzione
 * 
 * @param mixed $additional_info
 * @param mixed $description
 * @return void
 */
function outputError(?string $additional_info, ?string $description) {
    JSONResult::createFail($additional_info, $description)->outputToClient();
}

/* ------------------------------ 
    Inizio il BOOTSTRAP per la gestione della richiesta
    Includo il minimo indispensabile per fare un controllo sintattico sulla richiesta
   ------------------------------ */
try {
    require_once "../BOOTSTRAP/backend/initializer_1.php"; //includo tutto il necessario
} catch (Throwable $t) {
    log_error($t);

    //SE DEBUG  MODE,mostro tutto in output
    outputError("INTERNAL FAIL", DEBUG_MODE ? $t : null);
}
/* ------------------------------ 
    Verifico che i dati forniti dall' utente siano del formato corretto.
    -   $service    (obbligatorio, di tipo stringa)
    -   $parameters (opzionale, di tipo array indicizzato, default [])
    -   $verbose    (opzionale, di tipo booleano, di default false)
   ------------------------------ */

//definisco tutte e sole le chiavi accettate
$accepted_keys = ['service', 'parameters', 'verbose'];

//verifica che non ci siano chiavi non accettate in $_REQUEST
$unexpected_keys = array_diff(array_keys($_REQUEST), $accepted_keys);
if (!empty($unexpected_keys))
    outputError("INVALID SYNTAX REQUEST", "Invalid parameters passed: " . toString($unexpected_keys) . ".Only " . toString($accepted_keys) . " accepted.");

//$service deve essere essere indicato.
if (!isset($_REQUEST["service"]))
    outputError("INVALID SYNTAX REQUEST", 'Argument "service" must be defined');

//$service non può essere vuota.
if (empty($service = $_REQUEST["service"]))
    outputError("INVALID SYNTAX REQUEST", 'Argument "service" cannot be empty');

//$service deve essere una stringa.
if (!is_string($service))
    outputError("INVALID SYNTAX REQUEST", 'Argument "service" must be defined as string,'.gettype($service).' given');

//$service deve essere massimo 128 caratteri.
if (strlen($service) > MAX_SERVICE_SIZE)
    outputError("INVALID SYNTAX REQUEST", 'Argument "service" must be defined as string of maximum ' . MAX_SERVICE_SIZE . ' characters,'. strlen($service). 'given');



//$args deve essere un array
if (!isset($_REQUEST["parameters"]))
    $args = [];                                   //se non sono indicati, sono array vuoto
else if (is_scalar($_REQUEST["parameters"]))
    $args = [$_REQUEST["parameters"]];            //se è indicato un solo valore, lo rendo un singoletto
else if (isAssociativeArray($_REQUEST["parameters"]))
    $args = array_values($_REQUEST["parameters"]); //se è un array associativo, lo rendo un array indicizzato
else
    $args = $_REQUEST["parameters"];              //se è un array indicizzato,è tutto ok


if (count($args) > MAX_ARGUMENTS_NUMBER)
    outputError("INVALID SYNTAX REQUEST", "Argument Parameters exceeds max lenght of ". MAX_ARGUMENTS_NUMBER);

//Quando il client invia un valore null viene convertito in "", quindi lo ritrasformo in null per evitare problemi.
foreach ($args as $i => $val) {
    if (!is_string($val))
        outputError("INVALID SYNTAX REQUEST", "Argument Parameters must be defined as monodimensional array or as a scalar");
    if ($val === "") $args[$i] = null;//riconverto
    else if (strlen($val) > MAX_ARGUMENTS_SIZE)
        outputError("INVALID SYNTAX REQUEST", "Argument Parameters[{$i}] exceeds max lenght of ".MAX_ARGUMENTS_SIZE);
}
//$verbose deve essere un booleano (se indicato)
if (!isset($_REQUEST["verbose"])) 
    $verbose = false; //se non è indicato
else 
    $verbose = filter_var($_REQUEST["verbose"], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
if (is_null($verbose)) //vale null se non é un booleano
    outputError("INVALID SYNTAX REQUEST", "Argument \"verbose\" can only be defined as bool");


/* ------------------------------ 
    Completo il BOOTSTRAP per la gestione della richiesta.
    In caso di eccezione, la notifico.
   ------------------------------ */

try {
    require_once "../BOOTSTRAP/backend/initializer_2.php"; //includo tutto il necessario
} catch (UnreachableDBException $e) {                      //connessione al db fallita
    log_error($e);

    //SE DEBUG  MODE,mostro tutto in output
    outputError("DB NOT AVAIABLE", DEBUG_MODE ? $e->getConfig() : null);
} catch (Throwable $t) { //errore generico
    log_error($t);

    //SE DEBUG  MODE,mostro tutto in output
    outputError("INTERNAL FAIL", DEBUG_MODE ? $t : null);
}

/* ------------------------------ 
    Inoltro la richiesta al gestore dei servizi.
    Se viene alzata un eccezione, viene notificato il tutto.
    Si noti come le eccezioni eventuali sono causate dall' infrastruttura che utilizza il servizio, e non dallo stesso.
   ------------------------------ */
try {
    $result = $SERVICES_HANDLER->callService($service, $args, $verbose);
} catch (Throwable $t) {//qualsiasi errore/eccezione sia alzato
    log_error($t);

    outputError("SOMETHING WENT UNEXPECTEDLY WRONG FULFILLING THE REQUEST", DEBUG_MODE ? $t : null);
}

/* ------------------------------ 
    Dopo aver soddisfatto il servizio, stampo in output il risultato.
    -   Se di classe Result viene convertito in JSON.
    -   Se DownloadableResult é un download automatico.
   ------------------------------ */

//se necessazio, nasconde le infomazioni che descrivono i fail.
if (!DEBUG_MODE && Result::isFAIL($result)) 
    $result->resetValue();

//stampo in output il risultato: se di classe JSONResult é sottoforma di stringa, se DownloadableResult é un download
$result->outputToClient();