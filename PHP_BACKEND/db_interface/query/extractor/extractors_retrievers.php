<?php

/**
 * Classe che rappresenta una query di tipo SELECT che estrae UN SOLO VALORE SCALARE.
 *
 * Il controllo opzionale verifica che SOLO UNA ROW SIA STATA ESTRATTA, E ABBIA UN SOLO CAMPO.
 * 
 * @return int|string É il valore ottenuto.
 * @throws QueryResultException Se il controllo richiesto fallisce.
 */
class ExtractorScalar extends QueryResultExtractor {
    public function extractResult(mysqli_result|bool $result, bool $check_result = false): mixed
    {
        //se ottiene più rows, errore
        if ($check_result && $result->num_rows !== 1) 
            throw new QueryResultException($this->query);

        //ottengo la prima row
        $row = $result->fetch_assoc();

        //se la row é invalida
        if( is_null($row) ||
            empty($row) ||
            count($row) !== 1) {
            if ($check_result) //se necessaria alza eccezione
                throw new QueryResultException($this->query);
            return null;       //altrimenti ritorna null
        }

        $result->free();

        //prendo il primo valore della row
        return array_values($row)[0]; 
    }
}

/**
 * Classe che rappresenta una query di tipo SELECT che estrae UN SOLO ARRAY MONODIMENSIONALE (cioé una tupla).
 *
 * Il controllo opzionale verifica che SOLO UNA ROW SIA STATA ESTRATTA.
 *  
 * @return array É la tupla ottenuta (come array associativo).
 * @throws QueryResultException Se il controllo richiesto fallisce.
 */
class ExtractorRow extends QueryResultExtractor {
    public function extractResult(mysqli_result|bool $result, bool $check_result = false): mixed
    {
        //se ci sono più righe, errore
        if ($check_result && $result->num_rows != 1)
            throw new QueryResultException($this->query);

        $row = $result->fetch_assoc(); // Restituisce la tupla come array associativo

        $result->free();

        return $row;
    }
}

/**
 * Classe che rappresenta una query di tipo SELECT che estrae MOLTEPLICI ARRAY MONODIMENSIONALI (cioé una tabella).
 *
 * Il controllo opzionale verifica che SOLO ALMENO UNA ROW SIA STATA ESTRATTA.
 * 
 * @return array<array> É la tabella ottenuta (come array di array associativi)
 * @throws QueryResultException Se il controllo richiesto fallisce. * 
 */
class ExtractorTable extends QueryResultExtractor {
    public function extractResult(mysqli_result|bool $result, bool $check_result = false): mixed
    {
        //se non ci sono righe, errore
        if ($check_result && $result->num_rows < 1) 
            throw new QueryResultException($this->query);
        
        // Restituisce l'intera tabella come array di array associativi
        $table =  $result->fetch_all(MYSQLI_ASSOC); 

        $result->free();
        return $table;
    }
}
