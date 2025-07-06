<?php
/**
 * Eccezione generata durante la creazione della connessione al DB fisico.
 * 
 * Viene alzata quando non è possibile comunicare con il DB.
 * 
 * @param array $config Array associativo che contiene le informazioni sulla connessione al DB.
 * 
 * @see DB/config.json è il file dove viene configurata la connessione.
 */
final class UnreachableDBException extends Exception {

    private readonly array $config;

    public function getConfig() {return $this->config;}

    public function __construct(array $config)
    {
        $this->config = $config;
        parent::__construct();
    }

    public function __toString() :string {
        return get_class($this) . ": Impossible to open CONNECTION with configuration: ".PHP_EOL.
            json_encode($this->config, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES);
    }

}

/** 
 * Eccezione generata quando al DB é richiesta una query che non é tra quelle predefinite 
 * 
 * @property string $query_name Nome della query richiesta.
 * @property VirtualDB $DB DB virtuale a cui viene chiesta la query.
 * */
final class InvalidRequestedQueryException extends Exception
{
    private readonly string $query_name;
    private readonly VirtualDB $DB;
    public function __construct(string $query_name, VirtualDB $DB)
    {
        $this->query_name = $query_name;
        $this->DB = $DB;
        parent::__construct();
    }

    public function __toString(): string
    {
        return get_class($this) . ": '{$this->query_name}' is not defined in DB" . PHP_EOL . $this->DB;
    }
}

/**
 * Eccezione alzata quando dei vincoli non sono rispettati.
 * 
 * @property-read array $errors Array associativo che per ogni campo con errori, descrive il problema.
 */
final class CheckersException extends Exception
{
    private readonly array $errors;

    /**
     * Ritorno l' array che descrive i valori e il relativo problema.
     * 
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct();
    }

    public function __tostring(): string {
        return get_class($this) . ": Some elements does not have correct values"
        .PHP_EOL.json_encode($this->errors,JSON_PRETTY_PRINT);

    }
}
