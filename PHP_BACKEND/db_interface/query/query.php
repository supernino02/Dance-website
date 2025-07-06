<?php

/**
 * Classe finale che rappresenta una query SQL.
 *
 * In base ai parametri utilizzati nel costruttore, utilizza oggetti diversi per comunicare con il DB e estrarne i risultati.
 * 
 * @property-read string $name Il nome della query.
 * @property-read string $origin_file Il path assoluto del file in cui è definita la query.
 * @property-read string $query_string La stringa SQL della query.
 * @property-read string $parameters_required I tipi di dati richiesti per i parametri della query.
 * @property-read QueryExecutor $executor L'oggetto che esegue effettivamente la query.
 * @property-read QueryResultExtractor $extractor L'oggetto che estrae i risultati della query.
 * @property-read mysqli $connection Oggetto mysqli per la connessione al database.
 */
final class Query
{
    private readonly string $origin_file;
    private readonly string $name;
    private readonly string $query_string;
    private readonly string $parameters_required;
    private readonly QueryExecutor $executor;
    private readonly QueryResultExtractor $extractor;
    protected readonly mysqli $connection;

    /***GETTERS***/
    public function getQueryString(): string
    {
        return $this->query_string;
    }

    public function getParametersRequired(): string
    {
        return $this->parameters_required;
    }

    public function getConnection(): mysqli
    {
        return $this->connection;
    }

    /**
     * Costruttore della classe Query.
     * 
     * Inizializza i campi della query.
     * Sceglie il giusto esecutore (PreparedStmtExecutor o PlainQueryExecutor).
     * Sceglie il giusto estrattore dei risultati.
     * 
     *
     * @param array $query_info Array associativo che descrive la query, nel formato usato nel file JSON delle query.
     * @see Query::isStmt() Per vedere quale QueryExecutor viene utilizzato.
     */
    public function __construct(array $query_info)
    {
        //inizializzo i campi
        $this->name = $query_info["name"];
        $this->origin_file = $query_info["origin_file"];
        $this->query_string = $query_info["query"];
        $this->connection = $query_info['connection'];
        $this->parameters_required = $query_info["parameters_required"] ?? "";

        //definisco l' oggetto esecutore
        $this->executor = self::isStmt($query_info) ?
            new PreparedStmtExecutor($this) :  // Se ci sono parametri, è uno statement
            new PlainQueryExecutor($this);     // Se non ci sono parametri, è una plain query

        //definisco l' oggetto estrattore
        $this->extractor = match ($query_info["return"]) {
            "AFFECTED_ROWS"        => new ExtractorAffectedRows($this),
            "INSERT_AUTOINCREMENT" => new ExtractorInsertAutoincrement($this),
            "SCALAR"               => new ExtractorScalar($this),
            "ROW"                  => new ExtractorRow($this),
            "TABLE"                => new ExtractorTable($this),
            default                => throw new QueryFormatException($this->name),
        };
    }

    /**
     * Determina se la query è uno statement.
     * 
     * Il controllo è basato sulla presenza di parametri richiesti.
     *
     * @param array $query_info Array associativo che descrive la query.
     * @return bool True se la query richiede parametri, false altrimenti.
     */
    public static function isStmt(array $query_info): bool
    {
        return !empty($query_info["parameters_required"]);
    }

    /**
     * Esegue la query e ne restituisce il risultato, del tipo coerente con quanto indicato nel file di configurazione.
     *
     * Delega l' esecuzione al suo relativo QueryExecutor.
     * Delega l' estrazione al suo realtivo QueryResultExtractor.
     * 
     * @param array $parameters Array associativo contenente i parametri per la query.
     * @param bool $check_result Indica se controllare il risultato della query e sollevare un'eccezione in caso di errori (opzionale, predefinito false).
     * @return mixed Risultato della query (il tipo di ritorno varia in base alla classe del suo extractor).
     * 
     * @throws QueryResultException Se il controllo $check_result è abilitato e il risultato non soddisfa la condizione.
     */
    public function execute(array $parameters = [], bool $check_result = false): mixed
    {
        //eseguo la query
        $result = $this->executor->execute($parameters);

        //estraggo i risultati 
        try {
            $output = $this->extractor->extractResult($result, $check_result); 
        } catch (QueryResultException $e) {
            // Nel caso in cui sia alzata eccezione sul risultato, aggiungo informazioni per descriverla meglio. 
            $e->setArguments($parameters);
            throw $e;
        }

        //delego all' esecutore la pulizia dei risultati sql che non servono più
        $this->executor->freeUsedResult();
        return $output;
    }

    /**
     * Restituisce una rappresentazione in stringa dell'oggetto Query.
     *
     * Questo metodo viene chiamato di default quando l'oggetto viene stampato o concatenato a una stringa.
     * Delego a $this->executor e $this->extractor la relativa rappresentazione in stringa.
     *
     * @return string Rappresentazione in stringa dell'oggetto Query.
     */
    public function __toString(): string
    {
        return
            "nome: " .         $this->name                . PHP_EOL .
            "path: " .         $this->origin_file         . PHP_EOL .
            "query: " .        $this->query_string        . PHP_EOL .
            "parametri: " .    $this->parameters_required . PHP_EOL .
            "executor: "  .    $this->executor            . PHP_EOL .
            "extractor: " .    $this->extractor;
    }
}
