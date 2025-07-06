<?php
/**
 * Interfaccia per oggetti che implementano l'esecuzione di query SQL.
 * 
 * Le classi che estendono questa classe presentano diversi modi di comunicare con il DB sql.
 *
 * @property Query $query La query che verrà eseguita.
 */
abstract class QueryExecutor
{
    protected readonly Query $query;

    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * Esegue la query con i parametri forniti.
     * 
     * Nel caso in cui la query/stmt sia di tipo select o simili restituisce un mysqli_result.
     * Nel caso in cui la query/stmt sia di tipo insert o simili restituisce un bool. 
     * 
     * @param array $parameters Array associativo contenente i parametri per la query.
     * @return mysqli_result|bool Risultato dell'esecuzione della query.
     * 
     * @throws mysqli_sql_exception Nel caso in qui qualche chiamata dall' API fallisca.
     */
    abstract public function execute(array $parameters): mysqli_result|bool;

    /**
     * Pulisce i risultati che sono appena stati usati
     * 
     * chiama la funzione che eventualmente pulisce anche i result rimasti nella connesione.
     * @return void
     */
    public function freeUsedResult() {
        //chiamo la pulizia della connessione.
        global $DB;
        $DB->freePendingResults();
    }
}