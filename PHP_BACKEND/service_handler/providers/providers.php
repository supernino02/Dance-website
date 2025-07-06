<?php
/**
 * Provider Open, garantito anche agli utenti che non sono loggati.
 * 
 * Eredita da AbstractServicesProvider tutte le funzioni di utility.
 * 
 * @see ServicesHandler::getRoleCheckers() Per vedere quando si possono chiamare i suoi servizi (sempre).
 * @see AbstractServicesProvider Per vedere quali metodi eredita.
 */
class OpenServicesProvider extends AbstractServicesProvider
{
    use Utilities;           //ottenere elenco e descrizione dei servizi chiamabili.
    use Cookie_preferences;  //gestire le preferenze dei cookie.
    use Registration;        //registrazione di un nuovo utente.
    use Login;               //login con credenziali o token.
    use Product;             //ottenere informazioni sui prodotti.
    use ProductTypes;        //otterene informazioni sui tipi di prodotti.
    use ReviewPublic;        //ottenere reviews (create da altri utenti) dei prodotti.
}


/**
 * Provider User, garantito per gli utenti che hanno effettuato il login nella sessione corrente (con credenziali o token).
 * 
 * Eredita da AbstractServicesProvider tutte le funzioni di utility.
 * Eredita da OpenServicesProvider tutti i servizi forniti.
 * 
 * @see ServicesHandler::getRoleCheckers() Per vedere quando si possono chiamare i suoi servizi ($_SESSION['role'] == 'user').
 * @see OpenServicesProvider Per vedere quali servizi eredita.
 */
class UserServicesProvider extends OpenServicesProvider {

    /**
     * Controllo che impedisce a un utente di impersonificarne un altro
     * 
     * Nel caso in cui non sia dato in input alcun utente, restituisce l' utente loggato.
     * 
     * @param  string $user se indicato, verifica coincida con l' utente loggato
     * @return string Utente loggato che ha il permesso di eseguire il servizio.
     * 
     * @throws AccessViolationException se viene richiesto un servizio "fingendosi" qualcun'altro.
     */
    protected function checkIdentityConsistency(?string $user = null) :string {
        //se ti ho dato null, ti ritorno il tuo user
        if (is_null($user)) return $this->user_logged;
        //es l' utente a non puo chiedere di vedere il carrello di b
        if ($user !== $this->user_logged)
            throw new AccessViolationException($user,$this->user_logged);
        return $user;
    }

    use PersonalArea;     //gestire i dati inseriti durante la registrazione sul sito.
    use Logout;           //logout (cancella sessione e relativi cookie dal client).
    use Token;            //creare/cancellare i token di sessione dal DB sul server/client del cookie.
    use Cart;             //gestire il carrello corrente.
    use Purchase;         //creare/visualizzare gli acquisti effettuati (e scaricare le ricevute).
    use FilesPurchasable; //gestire e scaricare i file appartenenti ai prodotti acquistati.
    use ReviewPrivate;    //creare/visualizzare le recensioni scritte per i prodotti acquistati.
}

/**
 * Provider Admin, garantito per gli utenti che hanno effettuato il login nella sessione corrente (con credenziali o token).
 * 
 * Eredita da AbstractServicesProvider tutte le funzioni di utility.
 * Eredita da OpenServicesProvider tutti i servizi forniti.
 * Eredita da UserServicesProvider tutti i servizi forniti.
 * 
 * @see ServicesHandler::getRoleCheckers() Per vedere quando si possono chiamare i suoi servizi ($_SESSION['role'] == 'admin').
 * @see UserServicesProvider Per vedere quali servizi eredita.
 */
class AdminServicesProvider extends UserServicesProvider
{
    /**
     * Controllo che impedisce a un admin di impersonificarne un utente che non esiste.
     * 
     * Nel caso in cui non sia dato in input alcun utente, restituisce l' admin loggato.
     * 
     * @param  string $user se indicato, verifica coincida con un utente del DB.
     * @return string Utente impersonificato che ha il permesso di eseguire il servizio.
     * 
     * @throws AccessViolationException se viene richiesto un servizio "fingendosi" qualcuno che non esiste.
     */
    protected function checkIdentityConsistency(?string $user = null): string
    {
        //di default è l' admin loggato
        if (is_null($user)) return $this->user_logged;
        //altrimenti, controllo che esista la persona nel database
        if (!$this->DB->executeQuery("get_user_info",[$user]))
            throw new AccessViolationException($user, $this->user_logged); //tentativo di simulare una persona che non esiste
        return $user;
    }

    use LogAnalysis;          //permette un'analisi sommaria delle azioni effettuate dal server.
    use FileConsistency;      //verifica che sia mantenuta la corrispondenza tra DB<->FileSystem.
    use PrivilegesManagement; //gestisce i privilegi degli utenti (e di se stesso).
}