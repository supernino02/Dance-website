<?php

/**
 * Classe che rappresenta una query di tipo INSERT, UPDATE o DELETE.
 *
 * Il controllo opzionale verifica che ALMENO UNA TUPLA SIA STATO MODIFICATA.
 * 
 * @return int Numero di tuple modificate. 
 * @throws QueryResultException Se il controllo richiesto fallisce.
 */
class ExtractorAffectedRows extends QueryResultExtractor
{
    public function extractResult(mysqli_result|bool $result, bool $check_result = false): mixed
    {
        // Otteniamo il numero di righe modificate dall'ultima operazione
        $affected_rows = $this->query->getConnection()->affected_rows;

        //se richiesto, verifico sia un risultato valido
        if ($check_result && $affected_rows < 1)
            throw new QueryResultException($this->query);

        return $affected_rows;
    }
}

/**
 * Classe che rappresenta una query di tipo INSERT AUTOINCREMENT.
 *
 * Il controllo opzionale verifica che ALMENO UNA TUPLA SIA STATA INSERITA.
 * 
 * @return int Id della tupla appena inserita (0 se non è stata inserita).
 * @throws QueryResultException Se il controllo richiesto fallisce.
 */
class ExtractorInsertAutoincrement extends QueryResultExtractor
{
    public function extractResult(mysqli_result|bool $result, bool $check_result = false): mixed
    {
        // Otteniamo l'ID dell'ultima riga inserita
        $insert_id = $this->query->getConnection()->insert_id;

        //se l' id è 0, allora non è stato inserito null
        if ($check_result && $insert_id == 0)
            throw new QueryResultException($this->query);

        return $insert_id;
    }
}