<?php
/**
 * Esecutore di query SQL semplici (Plain Queries).
 *
 * Questa classe esegue query SQL che non richiedono statement preparati né parametri placeholders.
 * 
 * @param Query $query Oggetto Query contenente le informazioni sulla query SQL.
 * @param mysqli_result $last_result Oggetto mysqli_result contenente le informazioni sull'ultima esecuzione della query SQL.
 */
class PlainQueryExecutor extends QueryExecutor
{
    public function __toString()
    {
        return get_class($this);
    }

    /**
     * Esegue direttamente la query.
     *
     * @param array $parameters Array associativo contenente i parametri per la query (deve essere vuoto).
     * @return mysqli_result Risultato dell'esecuzione della query.
     * 
     * @throws BindPlainQueryException Se vengono passati parametri a una query che non li supporta.
     */
    public function execute(array $parameters): mysqli_result|bool
    {
        //se vengono forniti parametri, si verifica un errore
        if (!empty($parameters))
            throw new BindPlainQueryException($this->query, $parameters);

        //ottengo le informazioni dall' oggetto query
        $connection = $this->query->getConnection();
        $query_string = $this->query->getQueryString();

        //Eseguo la query e ottengo un mysqli_result
        return $connection->query($query_string);
    }

    /**
     * Non è necessario pulire nulla in caso di utilizzo di query()
     * @return void
     */
    public function freePendingResults() {}
}