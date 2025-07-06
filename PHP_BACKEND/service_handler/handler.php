<?php

/**
 * Classe utilizzata come GATEWAY per richiedere servizi forniti dal server.
 *
 * Questa classe gestisce l'accesso ai servizi attraverso un provider di servizi determinato dai permessi dell'utente.
 * Supporta la registrazione dei log delle richieste e delle risposte relative ai servizi utilizzati.
 *
 * @property VirtualDB $DB Oggetto per la gestione della connessione al database.
 * @property LogHandler $LOG Oggetto per la gestione dei log dei servizi.
 * @property AbstractServicesProvider $services_provider Provider di servizi corrente, determinato dai permessi dell'utente.
 */
final Class ServicesHandler {
    public readonly VirtualDB $DB;
    private readonly LogHandler $LOG;
    private AbstractServicesProvider $services_provider;

    /**
     * Restituisce un array di funzioni di controllo dei permessi.
     * 
     * Associa a ogni ruolo una funzione booleana che lo definisce.
     * Viene utilizzato per definire il concetto di "aver effettuato l'accesso", per ogni ruolo implementato.
     *
     * @return array Array associativo delle funzioni di controllo dei permessi.
     */
    static private function getRoleCheckers(): array
    {
        return [
            "Open" => function () {
                return true;
            },
            "User" => function () {
                return isset($_SESSION['role']) && $_SESSION['role'] == 'user';
            },
            "Admin" => function () {
                return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
            }
        ];
    }

    /**
     * Costruttore della classe.
     *
     * Inizializza la classe ServicesHandler con un oggetto DB per la connessione al database e un oggetto LogHandler per la gestione dei log.
     * Associa all' handler il provider con i permessi piu alti possibili dell' utente.
     * 
     * @param VirtualDB $DB Oggetto per la gestione della connessione al database.
     * @param LogHandler $LOG_HANDLER Oggetto per la gestione dei log dei servizi.
     * @see $this->refreshServicesProvider(); per capire come funziona l' assegnazione del provider.
     */

    public function __construct($DB,$LOG_HANDLER) {
        $this->DB = $DB;
        $this->LOG = $LOG_HANDLER;
        //scelgo il provider di privilegio + alto possibile
        $this->refreshServicesProvider();
    }

    /***GETTERS***/

    /**
     * Restituisce il nome del provider di servizi corrente in formato leggibile {Open, User, Admin}.
     *
     * @return string Nome formattato del provider di servizi corrente.
     */
    public function getServicesProviderName() :string {
        return $this->services_provider::getProviderName();
    }

    /**
     * Restituisce il provider di servizi corrente.
     *
     * @return AbstractServicesProvider Provider di servizi corrente.
     */
    public function getServicesProvider() :AbstractServicesProvider
    {
        return $this->services_provider;
    }

    /**
     * Restituisce un array dei servizi disponibili forniti dal provider corrente.
     *
     * @return array Array dei nomi dei servizi disponibili.
     */
    public function getAvaiableServicesArray() {return $this->services_provider->getAvailableServicesArray();}

    /**
     * Aggiorna il provider di servizi corrente in base ai permessi dell'utente.
     * 
     * Inizio dal provider con permessi maggiori, a scendere finchè non trovo un ruolo il quale checker sia soddisfatto
     * Come minimo ottiene il provider Open (è sempre disponibile).
     *
     * @return AbstractServicesProvider Nuovo provider di servizi corrente.
     * @see self::getRoleCheckers() per capire quali controlli sono fatti.
     */
    public function refreshServicesProvider() :AbstractServicesProvider {
        $max_role = "";
        //cerco il ruolo disponibile con priorità piu alta possibile
        foreach (array_reverse(self::getRoleCheckers()) as $provider => $isAvailable)
            if ($isAvailable()) {
                $max_role = $provider;
                break;
            }
        //es $max_role="User";
        $className = "{$max_role}ServicesProvider";
        return $this->services_provider = new $className($this->DB);
    }

    /**
     * Verifica l'esistenza di un servizio specifico fornito dal provider di servizi corrente.
     *
     * @param string $service_name Nome del servizio da verificare.
     * @throws InvalidServiceException Se il servizio non esiste nel provider attuale.
     */
    private function checkServiceExistance (string $service_name):void {
        $services_array = $this->getAvaiableServicesArray();
        if (!in_array($service_name, $services_array)) //se non è fornito
            throw new InvalidServiceException($this->services_provider, $service_name);
    }

    /**
     * Verifica che i parametri passati siano compatibili con quelli richiesti.
     * 
     * Effettua prima un controllo sommario sul numero di parametri richiesti, e poi un controllo sui tipi.
     * 
     * @param string $service_name Nome del servizio.
     * @param array $params_given Parametri forniti per il servizio.
     * @throws InvalidServiceBindException Se i parametri forniti non soddisfano quelli attesi dal servizio.
     */
    private function checkServiceBinding(string $service_name, array &$params_given):void {
        //ottengo l' intefaccia del servizio dal provider
        $service_interface = $this->services_provider->getServiceInterface($service_name);

        //controllo non siano passati troppi pochi argomenti
        $service_interface->checkTooFewArgs(count($params_given));

        //controllo non siano passati troppi argomenti
        $service_interface->checkTooManyArgs(count($params_given));

        //controllo che tutti i parametri passati abbiano il tipo giusto (o almeno compatibile con quello richiesto)
        $service_interface->checkArgsCompatibility($params_given);
    }

    /**
     * METODO PRINCIPALE PER RICHIEDERE SERVIZI
     * 
     * Chiama un servizio specifico con i parametri forniti e gestisce le eccezioni.
     * Verifica che il servizio sia disponibile e che i parametri siano compatibili.
     * Ritorna un tipo Result che racchiude la richiesta e la risposta ottenuta.
     * Se richiesto, salva in un file di log la richiesta, le query sql effettuate e la risposta fornita.
     *
     * @param string $service_name Nome del servizio da chiamare.
     * @param array $params Parametri da passare al servizio (opzionale).
     * @param bool $verbose [default false] Indica se alla risposta vanno aggiunte informazioni della richiesta (restituisce VerboseResult).
     * 
     * @return Result Risultato della chiamata al servizio.
     * @see il phpdocs del servizio specifico per capire cosa puo ritornare.
     */
    public function callService(string $service_name, array $params = [], bool $verbose = false): Result
    {
        //eseguo un refresh dell'oggetto provider (ottengo il permesso maggiore);
        $this->refreshServicesProvider(); 

        //salvo la richiesta fatta; rimuove informazionni sensibili come le password
        $this->LOG->log_service_request($this, $service_name, maskPassword($params));

        try {
            $this->checkServiceExistance($service_name);                   //alzo InvalidServiceException se non esiste.
            $this->checkServiceBinding($service_name, $params);            //alzo InvalidServiceBindException se non é compatibile.
            $result = $this->services_provider->$service_name(...$params); //passo i parametri al metodo richiesto.
        } catch (Throwable $t) {
            //ottengo un Result che descrive l' eccezione e chiamo un handler che le gestisce
            $result = self::describeThrowable($t);
        }

        //creo un ResultVerbose con tutte le informazioni
        $result_verbose = new ResultVerbose($result, $this->getServicesProviderName(), $service_name, $params);

        //salvo la risposta data (in modalitá verbose) con un sistema di log
        $this->LOG->log_service_response($this, $result_verbose);

        //restituisco il risultato richiesto (verbose o minimal)
        return $verbose ? $result_verbose : $result;
    }

    /**
     * Metodo statico che gestisce vari tipi di eccezioni e per ognuno crea un oggetto Result con messaggi di errore specifici
     * 
     * Se l'eccezione non è riconosciuta, viene loggata e viene creato un oggetto Result generico con informazioni sull'eccezione.
     * 
     * @param Throwable $t l' eccezione alzata.
     * @return JSONResult  il JSONResult che descrive l'eccezione.
     * 
     * @see vedere l' implementazione delle eccezioni per capire come vengono castate a string con __toString()
     */
    static private function describeThrowable(Throwable $t): JSONResult {
        // Gestione dei vari tipi di eccezioni
        if ($t instanceof InvalidServiceException)        return JSONResult::createFail( "INVALID SERVICE",     $t->__toString());  //mostra il nome del servizio e il privilegio attuale
        if ($t instanceof InvalidServiceBindException)    return JSONResult::createFail( "INVALID PARAMETERS",  $t->__toString());  //mostra il parametro che fallisce la bind e la signature del servizo richiesto
        if ($t instanceof AccessViolationException)       return JSONResult::createFail( "ACCESS VIOLATION",    $t->__toString());  //mostra l' utente impersonato, e l' utente loggato.
        if ($t instanceof CheckersException)              return JSONResult::createError("DECLINED BY DB",      $t->getErrors());   //mostra un oggetto che associa a ogni valore di un campo, la descrizione del rifiuto
        if ($t instanceof mysqli_sql_exception)           return JSONResult::createFail( "DB QUERY FAIL",       $t->getMessage());  //mostra il messaggio di errore alzato dal database fisico
        if ($t instanceof QueryResultException)           return JSONResult::createFail( "INVALID QUERY RESULT",$t->__toString());  //mostra la query il cui risultato non ha soddisfatto il vincolo ($check=true in executeQuery); vedere BACKEND/db_interface/query_types.php per le specifiche
        if ($t instanceof InvalidRequestedQueryException) return JSONResult::createFail( "INVALID QUERY",       $t->__toString());  //mostra la query richiesta, e le informazioni del DB virtuale (e tutte le query fornite)
        if ($t instanceof BindStmtException)              return JSONResult::createFail( "BIND FAIL ON STMT",   $t->__toString());  //mostra la query richiesta, e i parametri forniti (che hanno fallito la bind)
        if ($t instanceof BindPlainQueryException)        return JSONResult::createFail( "BIND FAIL ON QUERY",  $t->__tostring());  //mostra la query richiesta, e i parametri forniti (inaspettati poichè è una query non parametrica)
        
        //se non è nessuno di questi tipi, allora mi salvo l' errore in un file di log.
        log_error($t);
        //restituisco un Result di fail generico.
        return JSONResult::createFail("UNEXPECTED FAIL", $t->getMessage());
    }
    
}


