<?php 
//servizi di un admin, monitora il sistema di log
trait LogAnalysis {
    /**
     * Servizio che restituisce il file dei log degli errori.
     * 
     * @param bool $download se true esegue un download del file, se false restistuisce un Result standard     * 
     * @return Result
     *   - [FAIL,NOT EXISTS]  se il file indicato in LOG_ERRORS_PATH non esiste.
     *   - [FAIL,CANNOT READ] se il file indicato in LOG_ERRORS_PATH esiste, ma non si riesce a leggere.
     *   - [OK,CONTENT,...]   se il file si riesce a leggere.
     *   - FILE se il file esiste e $download è true
     */
    public function getLogErrorsFile(bool $download = false):Result {
        return $download
        ? $this->downloadFileContent(LOG_ERRORS_PATH)   
        : $this->printFileContent(LOG_ERRORS_PATH);
    }

    /**
     * Servizio che restituisce il file di LOG tutte le richieste a servizi, con le relative risposte.
     * 
     * Ovviamente al suo interno ci sono solo i servizi forniti nel momento in cui FileSQL era attivato.
     * Modificare le impostazioni per (dis)attivare il log.
     * 
     * @param bool $download se true esegue un download del file, se false restistuisce un Result standard    
     * @return Result
     *   - [FAIL,NOT EXISTS]  se il file indicato in LOG_FILE_PATH non esiste.
     *   - [FAIL,CANNOT READ] se il file indicato in LOG_FILE_PATH esiste, ma non si riesce a leggere.
     *   - [OK,CONTENT,...]   se il file si riesce a leggere.
     *   - FILE               se il file esiste e $download è true.
     */
    public function getLogFile(bool $download = false): Result
    {
        return $download
            ? $this->downloadFileContent(LOG_FILE_PATH)
            : $this->printFileContent(LOG_FILE_PATH);
    }

    /**
     * SERVIZIO che restituisce una tabella associativa che contiene tutte le richieste a servizi, con le relative risposte, nelle tabelle di LOG.
     * 
     * Ovviamente al suo interno ci sono solo i servizi forniti nel momento in cui LogSQL era attivato.
     * Modificare le impostazioni per (dis)attivare il log.
     * 
     * @param ?string $filter valore che devono avere i servizi richiesti. se non indicato, li mostra tutti. 
     * @return JSONResult
     *   - [FAIL,INVALID]      se $filter non é di un tipo di result possibile ['FAIL','ERROR','OK'].
     *   - [OK,SUMMARY,$table] dove $table è un array di tuple, se $filter é definito.
     */
    public function ServiceResultLookup(string $filter = null): JSONResult {
        $filter = strtoupper($filter);
        //verifico che il filtro (se cé) sia valido
        if ($filter && !in_array($filter,Result::$result_enum)) 
            return JSONResult::createFail("INVALID");

        $table = $this->DB->executeQuery("get_logged_services_by_result",[$filter]);
        return JSONResult::createOK("SUMMARY",$table);
    }

    /**
     * SERVIZIO che restituisce una tabella associativa che descrive sommariamente ogni sessione presente nelle tabelle usate da LogSQL.
     * 
     * Per ognuna, mostra il timestamp della prima e dell' ultima richiesta effettuata, oltre alle mail di tutti gli utenti loggati. 
     * Ovviamente al suo interno ci sono solo i servizi forniti nel momento in cui LogSQL era attivato.
     * Modificare le impostazioni per (dis)attivare il log.
     * 
     * @return JSONResult
     *   - [OK,SUMMARY,$table] Dove $table è un array di tuple.
     */
    public function trackSessions(): JSONResult
    {
        $table = $this->DB->executeQuery("get_sessions_summary");
        return JSONResult::createOK("SUMMARY", $table);
    }

}