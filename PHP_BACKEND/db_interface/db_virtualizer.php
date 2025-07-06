<?php

/**
 * Classe che gestisce la connessione al database SQL e fornisce un'astrazione per l'esecuzione di query predefinite.
 *
 * @property-read array<QueryWrapper> $queries Array associativo che associa a ogni nome una istanza di QueryWrapper, che permette di creare (con lazy loading) ed eseguire un oggetto Query.
 * @property-read mysqli $connection La connessione al database fisico.
 * @property-read LogHandler $LOG Oggetto per la gestione del log delle query eseguite.
 * 
 * @see QueryWrapper Classe che avvolge un oggetto Query e fornisce un meccanismo di lazy loading.
 */
final class VirtualDB
{
    private readonly array $queries;
    private readonly mysqli $connection;
    private readonly LogHandler $LOG;

    /**
     * Restituisce l'oggetto di connessione al database.
     *
     * @return mysqli L'oggetto di connessione al database.
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Costruttore della classe VirtualDB.
     *
     * Inizializza la connessione al database, il gestore dei log e crea una collezione di oggetti QueryWrapper.
     *
     * @property mysqli $connection Oggetto di connessione al database.
     * @property LogHandler $LOG_HANDLER Oggetto per la gestione dei log.
     * @property array $interface_files Array di path di file relativi dei file JSON contenenti le definizioni delle query.
     * 
     * @see QueryWrapper::__construct() per capire come è implementata la creazione di oggetti Query
     */
    public function __construct(mysqli $connection, LogHandler $LOG_HANDLER, array $interface_files)
    {
        $this->connection = $connection;
        $this->LOG = $LOG_HANDLER;

        //utilizzo metodo statico fornito dai Wrapper
        $this->queries = QueryWrapper::createCollection($interface_files, $connection);
    }

    /**
     * Distruttore della classe VirtualDB.
     *
     * Assicura che la transazione corrente venga annullata se non è stata eseguita un'operazione di commit.
     */
    public function __destruct()
    {
        $this->rollBack();
    }

    /**
     * Recupera l'oggetto Query,utilizzando come chiave il nome
     *
     * @param string $name Nome della query da recuperare.
     * @return Query L'oggetto Query associato al nome fornito.
     * @throws InvalidRequestedQueryException Se la query con il nome specificato non viene trovata.
     * 
     * @see QueryWrapper per capire come è implementata la creazione di oggetti Query
     */
    private function getQuery(string $name): Query
    {
        //se non è indicata nella collezione, segnalo errore
        if (!isset($this->queries[$name]))
            throw new InvalidRequestedQueryException($name, $this);

        //ottengo la Query
        return $this->queries[$name]->getQuery();
    }

    /**
     * Esegue una query fornita dal DB.
     * 
     * In automatico esegue un eventuale parsing dei parametri e ritorna un risultato appropriato.
     *
     * @param string $name Nome della query da eseguire.
     * @param array $parameters Parametri da passare alla query (opzionale).
     * @param bool $check_result Se TRUE, controlla il risultato della query e genera un'eccezione QueryResultException in caso di errore.
     * @return mixed Il risultato della query eseguita.
     * 
     * @throws InvalidRequestedQueryException Se la query con il nome specificato non viene trovata.
     * @throws BindStmtException Se i parametri forniti allo stmt non sono compatibili.
     * @throws BindPlainQueryException Se sono indicati parametri (un oggeto PlainQuery non richiede parametri).
     * @throws QueryResultException Se la query fallisce il controllo opzionale sul risultato.
     * 
     * @see Query->execute() per vedere cosa ritorna.
     */
    public function executeQuery(string $name, array $parameters = [], bool $check_result = false): mixed
    {
        //ottengo l' oggetto Query
        $query = $this->getQuery($name);

        //eseguo il log della richiesta
        $this->LOG->log_query_request($this->connection, $name, $parameters);

        //eseguo la query
        return $query->execute($parameters, $check_result);
    }

    /**
     * Inizia una transazione sulla connessione al database.
     *
     * Deve essere seguita da un'operazione di commit o di rollBack.
     */
    public function beginTransaction()
    {
        $this->connection->begin_transaction();
    }

    /**
     * Esegue il commit della transazione corrente.
     *
     * Conferma tutte le modifiche apportate durante la transazione.
     */
    public function commit()
    {
        $this->connection->commit();
    }

    /**
     * Annulla la transazione corrente.
     *
     * Si assicura che non ci siano mysqli_result che non sono stati gestiti, e li pulisce.
     * Ripristina lo stato del database al punto precedente all'inizio della transazione.
     * Nel caso in cui sia stata preceduta da un commit(), non ha effetti sul DB.
     */
    public function rollBack()
    {
        $this->freePendingResults();
        $this->connection->rollback();
    }

    /**
     * Effettua una free dei result che non sono stati estratti
     * 
     * 
     * Evita che il server vada "Out of Sync";
     * è causato da query successive SENZA aver ripulito il buffer dei risultati.
     * SE RIMOSSO, CAUSA PROBLEMI RISOLVIBILI FACENDO CLOSE DELLO STMT OGNI VOLTA.
     * 
     * @see mysql_exception "command out of sync" sulla documentazione.
     * @return void
     */
    public function freePendingResults() {
        //!CODICE PRESO DA STACKOVERFLOW
        //!serve a rimuovere i result che non sono stati processati.
        //!di fatto non dovrebbe servire, ma alcuni problemi (di configurazione?) fanno eseguire il ciclo
        //!1 volta per ogni procedure chiamata dallo stmt/query.
        while ($this->connection->more_results()) {
            $result = $this->connection->use_result();
            if ($result) {
                $result->free(); // Libera il risultato
            }
            $this->connection->next_result(); // Avanza al prossimo risultato
        }
        //!FINE CODICE 
    }

    /**
     * Funzione che prende in input un numero arbitrario di triple:
     *  - nome del campo
     *  - nome del vincolo
     *  - valore del campo
     * 
     * Per ognuno, valuta il vincolo sul campo: se il controllo fallisce, allora lo aggiungo all' array degli errori 
     * I controlli sono query SCALAR che ritornano null in caso di successo e una stringa in caso di errore.
     * Nel caso in cui l' oggetto che raggruppa gli errori sia non nullo, viene alzata eccezione.
     *
     * @param array $params Array di triple che descrivono il vincolo da verificare.
     * @throws CheckersException   Se uno o più controlli falliscono.
     */
    public function evaluateCheckers(...$params)
    {
        $errors =  [];
        foreach ($params as $row) {
            $name = $row[0];
            $check = $row[1];
            $value_checked = $row[2];

            //valuto il vincolo check
            $check_result = $this->executeQuery("check_{$check}", [$value_checked]);

            //se il vincolo check restituisce un valore, è un errore e lo agigungo agli altri.
            if ($check_result)
            $errors[$name] = $check_result;
        }

        //se l' array che contiene gli errori non è vuoto, alzo eccezione
        if (!empty($errors))
            throw new CheckersException($errors);
    }

    /**
     * Restituisce una rappresentazione in stringa dell'oggetto VirtualDB.
     *
     * La stringa include lo stato della connessione e le informazioni di ogni query memorizzata.
     *
     * @return string Rappresentazione in stringa dell'oggetto VirtualDB.
     */
    public function __toString(): string
    {
        $result = "connection status: \n" . print_r($this->connection, true) . "\n";
        foreach ($this->queries as $query)
            $result .= $query . PHP_EOL;
        return $result;
    }
}
