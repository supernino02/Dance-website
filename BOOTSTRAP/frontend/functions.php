<?php
/**
 * Ridireziono a una nuova pagina pubblica.
 * 
 * Opzionalmente invia anche un parametro che descrive la pagina del prossimo redirect.
 * 
 * @param string $path            Nuova pagina di arrivo.
 * @param bool $redirect          Indico se voglio settare il parametro "redirect" [DEFAULT:false].
 * @param mixed $redirection_page Valore del parametro inviato "redirect" [DEFAULT:$_SERVER['PHP_SELF']]. 
 * @return never                  La funzione termina lo script.
 */
function redirect(string $path, bool $redirect = false, ?string $redirection_page = null)
{
    //ridireziona ma passando il parametro redirect
    if ($redirect) {
        //se non ho detto la pagina, è la pagina da cui arrivo
        $redirection_page = $redirection_page ?? $_SERVER['PHP_SELF'];
        header("Location: {$path}&redirect={$redirection_page}");
    } else
        header("Location: {$path}");
    exit();
}

/**
 * Ridireziona alla pagina ERROR_PAGE, eventualmente definendo un messaggio.
 *  
 * 
 * @param mixed $message Stringa inviata a ERROR_PAGE come parametro con nome "error".
 * @return never         La funzione termina lo script.
 */
function redirect_ERROR_PAGE(?string $message)
{
    if ($message) {
        //se non ho detto la pagina, è la pagina da cui arrivo
        $redirection_page = $redirection_page ?? $_SERVER['PHP_SELF'];
        header("Location: " . ERROR_PAGE . "?error=" . urlencode($message));
    } else
        header("Location: " . ERROR_PAGE);
    exit();
}


/**
 * Funzione che verifica che un utente sia loggato sul server.
 * 
 * Chiama un refresh del provider di $SERVICES_HANDLER e controlla il nome di quello ottenuto.
 *
 * @return bool True se Admin o User, false se Open.
 */
function userIsLogged()
{
    global $SERVICES_HANDLER;
    //faccio refresh del provider, e verifico che sia loggato qualcuno
    return $SERVICES_HANDLER->refreshServicesProvider()->getProviderName()  != "Open";
}


/**
 * Funzione che si salva in un cookie il nome della pagina richiesta.
 * 
 * SOLO se è l' utilizzo è permesso nelle preferenze del client. * 
 * @return void
 * @see CookieManager Oggetto che gestisce l' utilizzo dei cookie.
 */
function rememberVisit() {
    $cookie_virtual = new CookieManager("VISITED_PAGE");
    //ci metto l' url richiesta
    $cookie_virtual->defineCookie($_SERVER['REQUEST_URI']);
}