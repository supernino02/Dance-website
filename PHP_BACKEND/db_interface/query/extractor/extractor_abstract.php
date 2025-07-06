<?php

/**
 * Classe astratta per l'estrazione dei risultati successivamente a una comunicazione con il DB.
 *
 * Questa classe fornisce una struttura base per le classi che si occupano di elaborare 
 * i risultati delle query SQL (o stmt) eseguite. 
 * Le classi concrete dovranno implementare il metodo `extractResult()` per gestire diversi tipi di risultati attesi.
 * 
 * 
 *
 * @property-read Query $query Oggetto Query contenente le informazioni sulla query SQL.
 * @throws QueryResultException Se il risultato ottenuto non rispetta alcune caratteristiche e il controllo è richiesto.
 * @see QueryResultExtractor::extractResult() per implementazione concrete.
 */
abstract class QueryResultExtractor
{
    protected readonly Query $query;

    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * Restituisce una rappresentazione in stringa della classe.
     *
     * Si limita a stampare il nome della classe concreta.
     *
     * @return string Nome della classe.
     */
    public function __toString()
    {
        return get_class($this);
    }

    /**
     * Estrae i risultati della query eseguita.
     *
     * Questo metodo astratto deve essere implementato dalle classi concrete per gestire
     * i risultati delle query specifiche.
     *
     * @param mysqli_result|bool $result Risultato della query eseguita.
     * @param bool $check_result Flag per verificare la validità del risultato (opzionale).
     * @return mixed Il tipo di ritorno varia in base alla query eseguita.
     * 
     * @throws QueryResultException Se il risultato ottenuto non rispetta alcune caratteristiche e il controllo è richiesto.
     */
    abstract public function extractResult(mysqli_result|bool $result, bool $check_result): mixed;
}
