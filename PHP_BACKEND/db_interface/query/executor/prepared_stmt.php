<?php
/**
 * Esecutore di query preparate (Prepared Statements).
 *
 * Questa classe esegue le query utilizzando statement preparati, che permettono l'uso di parametri placeholders.
 * I parametri vengono verificati per garantire la corrispondenza con i tipi di dati richiesti.
 * Nel momento in cui viene creata la relativa Query (e il relativo QueryExecutor) viene creato lo stmt.
 * 
 * A ogni esecuzione, vengono aggiornati i parametri nello stmt.
 * Lo stmt viene chiuso SOLO dal distruttore (al termine dello script).
 * 
 * @property-read Query $query L' oggetto che descrive la query che deve effettivamente eseguire.
 * @property-read mysqli_stmt $stmt Lo stmt, successivamente a bind_param(), pronto a essere eseguito.
 * @property array $stmt_parameters Un array contente i parametri parsati dallo stmt. Viene aggiornato ogni volta che viene chiamata la execute().
 * 
 * @see PreparedStmtExecutor::execute() Per i dettagli implementativi.
 */
class PreparedStmtExecutor extends QueryExecutor
{
    private readonly mysqli_stmt $stmt;
    private array $stmt_parameters;

    /**
     * Costruttore della classe PreparedStmtExecutor.
     *
     * Prepara lo statement SQL utilizzando i parametri placeholders.
     *
     * @param Query $query Oggetto Query contenente le informazioni sulla query SQL.
     */
    public function __construct(Query $query)
    {
        //aggiorno il campo query
        parent::__construct($query);

        // Prepara lo statement
        $connection = $query->getConnection();
        $query_string = $query->getQueryString();
        $this->stmt = $connection->prepare($query_string); 

        //creo un array placeholder lungo n, inizializzato con null
        $parameters_required = $query->getParametersRequired();
        $this->stmt_parameters = array_fill(0, strlen($parameters_required),null);
        
        // Creare un array di riferimenti verso le celle dell' array apena creato
        $refs = [];
        foreach ($this->stmt_parameters as &$value) $refs[] = &$value;

        //eseguo una bind, associando lo stmt con l' array
        $this->stmt->bind_param( 
            $parameters_required,
            ...$refs
        );
    }

    public function __toString()
    {
        return get_class($this).PHP_EOL.
        "   parameters: ".toString($this->stmt_parameters);
    }

    /**
     * Esegue lo statement preparato con i parametri forniti.
     *
     * Aggiorna i parametri dello statement e verifica che i tipi siano coerenti con quelli richiesti dalla query.
     *
     * @param array $parameters Array associativo contenente i parametri per la query.
     * @return mysqli_result|bool Risultato dell'esecuzione della query.
     * @throws BindStmtException Se i parametri forniti non corrispondono ai tipi di dati richiesti.
     */
    public function execute(array $parameters): mysqli_result|bool
    {
        //verifico i tipi dei parametri forniti
        $this->checkParametersValidity($parameters);

        // Aggiorna i valori dei parametri
        foreach ($parameters as $index => $value) 
            $this->stmt_parameters[$index] = $value;

        //esegue la query
        $this->stmt->execute();

        //mi salvo i risultati
        return $this->stmt->get_result();
    }

    /**
     * Verifica che i parametri forniti corrispondano ai tipi di dati richiesti dalla query.
     *
     * @param array $parameters Array associativo contenente i parametri per la query.
     * @throws BindStmtException Se i parametri forniti non corrispondono ai tipi di dati richiesti.
     */
    private function checkParametersValidity(array $parameters): void
    {
        //paragono i tipi dei parametri forniti ai tipi richiesti
        if (self::getParametersType($parameters) != $this->query->getParametersRequired()) 
            throw new BindStmtException($this->query, $parameters);
    }

    /**
     * Converte un array di parametri in una stringa che rappresenta i loro tipi di dati.
     * 
     * Viene utiliazzata per creare una rappresentazione coerente con quanto richiesto dalla bind.
     * I tipi di dati sono rappresentati come segue:
     * - `i` per integer
     * - `d` per double
     * - `s` per string
     * - `s` per NULL (trattato come stringa)
     * - `b` per blob (se il tipo di dato non è tra quelli sopra elencati)
     *
     * @param array $parameters Array di parametri.
     * @return string Stringa che rappresenta i tipi di dati dei parametri.
     */
    public static function getParametersType(array $parameters): string
    {
        $type_map = [
            'integer' => 'i',
            'double'  => 'd',
            'string'  => 's',
            'NULL'    => 's', // Trattiamo NULL come una stringa per semplicità
        ];

        $result = '';
        foreach ($parameters as $par) 
            $result .= $type_map[gettype($par)] ?? 'b'; // Default a 'b' se non è mappato

        return $result;
    }

    /**
     * Libero le informazioni sul mysqli_result ottenuto.
     * 
     * @return void
     * @see QueryExecutor::freePendingResults() PER OVVIARE A UN BUG
     */
    public function freeUsedResult() {
        $this->stmt->free_result();

        parent::freeUsedResult();
    }
}
