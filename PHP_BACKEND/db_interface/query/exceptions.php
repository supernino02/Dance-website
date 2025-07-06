<?php
/**
 * Eccezione generata durante la creazione della Query per il DB a causa di un formato errato nel file JSON.
 * 
 * @property string $query_name Nome della query corrotta.
 * @see Query::create() Per vedere cosa richiede il construttore.
 */
final class QueryFormatException extends Exception
{
    private readonly string $query_name;

    public function __construct(string $query_name = "")
    {
        $this->query_name = $query_name;
        parent::__construct();
    }

    public function __toString(): string
    {
        return get_class($this) . ": Error parsing query: {$this->query_name} defined with invalid return type";
    }
}

//Eccezioni a tempo di ESECUZIONE (cioè relativi a COME uso le query, e non come le virtualizzo) su oggetti Query

/**
 * Eccezione generica relativa a errori durante l'esecuzione di una query.
 *
 * @property Query $query Oggetto Query associato all'errore
 * @property array $parameters Parametri passati alla query
 */
abstract class QueryException extends Exception
{
    protected readonly Query $query;
    protected array $parameters;

    public function __construct(Query $query, array $parameters = [])
    {
        $this->query = $query;
        $this->parameters = $parameters;
        parent::__construct();
    }
}

/**
 * Eccezione generata durante la fase di binding dei parametri allo stmt.
 * 
 * Si limita a stampare le informazioni sulla Query richiesta e quali parametri sono stati forniti.
 *
 * @see Query::__toString() Per capire come viene convertita in formato testuale.
 * @see PreparedStmtExecutor::checkParametersValidity() Per capire meglio in quali contesti viene generata
 */
final class BindStmtException extends QueryException
{

    public function __toString(): string
    {
        return get_class($this) . " on query:" .PHP_EOL.
        $this->query . PHP_EOL . 
        "forniti: " . PreparedStmtExecutor::getParametersType($this->parameters);
    }
}

/**
 * Eccezione generata durante l' esecuzione di una PlainQuery.
 * 
 * Viene alzata nel caso in cui vengano passati dei parametri (implementa chiamate non parametrizzate).
 * 
 * @see Query::__toString() Per capire come viene convertita in formato testuale.
 * @see PlainQueryExecutor::execute() Per vedere in quale punto viene alzata.
 */
final class BindPlainQueryException extends QueryException
{
    public function __toString(): string
    {
        return get_class($this) . " on query:" . PHP_EOL .
            $this->query . PHP_EOL . 
            "Non può soddisfare la richiesta con " . toString($this->parameters) . " forniti";
    }
}

/**
 * Eccezione generata in caso sia FALLITO il controllo sul valore di ritorno della query.
 *
 * Descrive la Query richiesta e i parametri passati
 * 
 * @see Query::__toString() Per capire come viene convertita in formato testuale.
 * @see Query::getResult() Per capire in quali casi viene generata. 
 */
final class QueryResultException extends QueryException
{
    public function setArguments(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function __toString(): string
    {
        return get_class($this) . " on query:" . PHP_EOL .
            $this->query . PHP_EOL . 
            "with given args: " . toString($this->parameters) . PHP_EOL . "return Invalid Value";
    }
}
