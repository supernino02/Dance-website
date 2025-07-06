<?php

/**
 * Classe astratta che fornisce funzionalità di base per la gestione dei servizi nel sistema.
 *
 * Questa classe contiene metodi comuni utilizzati dai provider di servizi per gestire
 * l'accesso, i dati sensibili degli utenti e i servizi disponibili per l'esecuzione.
 *
 * @property ?string $user_logged Email (eventualmente) indicata nella sessione.
 * @property VirtualDB $DB Oggetto per la gestione della connessione al database.
 */
class AbstractServicesProvider {
    protected ?string $user_logged; //puo essere null oppure string, in coerenza con quanto salvato nella sessione
    public readonly VirtualDB $DB;

    /***GETTERS***/
    public function getUserLogged()
    {
        return $this->user_logged;
    }

    /**
     * Restituisce un nome significativo del provider.
     * 
     * In pratica tronca la parte finale del nome della classe, e ottiene {Open,User,Admin}
     *
     * @return string Nome del provider di servizi.
     */
    static public function getProviderName(): string
    {
        //ovvero OpenServicesProvider => Open
        return str_replace("ServicesProvider", "", get_called_class());
    }

    /**
     * Ottiene un array dei nomi dei servizi chiamabili da questo provider.
     * 
     * Si noti come non solo contiene quelli definiti nella classe, ma anche nelle sottoclassi
     *
     * @return array Array contenente i nomi dei metodi pubblici del provider (servizi chiamabili).
     */
    public function getAvailableServicesArray() {
        //array dei metodi pubblici
        $public_methods = (new ReflectionClass($this))->getMethods(ReflectionMethod::IS_PUBLIC); 
        $result = [];
        foreach ($public_methods as $method) {           
            // Salta i metodi statici e quelli definiti nell'AbstractServicesProvider (non sono servizi, solo utilities)
            if ($method->isStatic() || method_exists('AbstractServicesProvider', $method->getName())) 
                continue;                                
            $result[] = $method->getName(); //allora é un servizio, lo aggiungo
        }
        //al termine ho un array con tutti e soli i metodi public NON definiti in AbstractServicesProvider
        return $result;
    }

    /**
     * Ottiene la descrizione degli argomenti richiesti da un servizio.
     * 
     * Utilizza un oggetto ServiceInterface per descrivere dettagliatamente il servizio.
     * 
     * @param string $name Nome del servizio.
     * @return ServiceInterface Descrive dettagliatamente la firma del servizio.
     * 
     * @throws ReflectionException Se il servizio non esiste.
     * 
     * @see ServiceInterface::__construct() Oggetto ritornato.
     */
    public function getServiceInterface(string $name) : ServiceInterface{
        //ottengo l'oggetto reflectionMethod associato
        $reflectionMethod = new ReflectionMethod($this, $name);
        //lo utilizzo per creare la descrizione dell'interfaccia
        return new ServiceInterface($name, $reflectionMethod);
    }

    /***COSTRUTTORE***/
    /**
     * Costruttore della classe.
     *
     * @param VirtualDB $DB Oggetto per la gestione della connessione al database.
     * @param string|null $user Utente attualmente loggato (opzionale).
     * Se non fornito e l'utente è loggato in sessione, utilizza l'email dell'utente dalla sessione.
     */
    public function __construct(VirtualDB $DB, ?string $user = null)
    {
        //se l'oggetto non é passato da parametro, lo cerco nella sessione.
        if (is_null($user) && isset($_SESSION['email']) && !empty($_SESSION['email'])) 
            $user = $_SESSION['email'];
        $this->user_logged = $user;
        $this->DB = $DB;
    }

}
