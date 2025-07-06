<?php

/**
 * Classe ResultVerbose che estende un Result, con alcune informazioni aggiuntive.
 *
 * Implementa il pattern Decorator per decorare un oggetto Result e aggiungere ulteriori dettagli
 * verbosi come il ruolo, il nome della richiesta e i suoi argomenti. Questi dettagli
 * vengono utilizzati per fornire una rappresentazione più completa del risultato.
 *
 * @property string $role Ruolo dell'utente o del contesto in cui è avvenuta la richiesta.
 * @property string $request_name Nome della richiesta eseguita.
 * @property array $request_args Argomenti passati alla richiesta.
 * @property Result $decorated_result L'oggetto Result decorato, i cui metodi e proprietà sono delegati.
 */
class ResultVerbose extends Result
{
    private string $role;
    private string $request_name;
    private array $request_args;
    private Result $decorated_result;

    /***GETTER NUOVI***/
    public function getRole(): string
    {
        return $this->role;
    }

    /***GETTER CON DELEGA***/
    public function getResult(): ?string
    {
        return $this->decorated_result->result;
    }

    public function getAdditionalInfo(): ?string
    {
        return $this->decorated_result->additional_info;
    }

    public function getValue(): mixed
    {
        return $this->decorated_result->value;
    }

    /***CONSTRUCTOR***/
    /**
     * Costruttore della classe ResultVerbose.
     *
     * Inizializza la classe decorata aggiungendo dettagli aggiuntivi verbosi.
     *
     * @param Result $decorated_result Il risultato da decorare.
     * @param string|null $role Il ruolo dell'utente o del contesto della richiesta (opzionale).
     * @param string $request_name Il nome della richiesta (opzionale, default vuoto).
     * @param array $request_args Argomenti passati alla richiesta (opzionale, default vuoto).
     */
    public function __construct(Result $decorated_result, ?string $role = null, string $request_name = "", array $request_args = [])
    {
        $this->decorated_result = $decorated_result;
        $this->role = $role;
        $this->request_name = $request_name;
        $this->request_args = $request_args;
    }

    /**
     * Metodo per restituire il risultato al client.
     * Se il risultato decorato è di tipo JSON, restituisce il JSON al client. Altrimenti, delega la logica al risultato decorato.
     *
     * @return never Termina l'esecuzione del programma (attraverso `die`).
     */
    public function outputToClient(): never
    {
        //se è json stampo tutte i campi dell' oggetto 
        if ($this->decorated_result instanceof JSONResult) {
            header('Content-Type: application/json');
            die($this->__toString());
        }
        //else
        $this->decorated_result->outputToClient();        
    }

    /**
     * Restituisce le variabili appartenenti all'oggetto ResultVerbose, escludendo l'oggetto decorato.
     *
     * @return array Array delle variabili appartenenti all'oggetto ResultVerbose.
     */
    private function getVerboseVars(): array
    {
        $vars = get_object_vars($this);
        unset($vars['decorated_result']); // Rimuove l'oggetto decorato dai risultati
        return $vars;
    }

    /**
     * Restituisce la rappresentazione della classe sotto forma di stringa JSON.
     * Combina le proprietà dell'oggetto verboso con quelle dell'oggetto decorato.
     *
     * @return string Rappresentazione in formato JSON dell'oggetto.
     */
    public function __toString(): string
    {
        $merged = array_merge(
            $this->getVerboseVars(),                 // proprietà del verbose
            get_object_vars($this->decorated_result) // proprietà del decorato
        );
        return json_encode($merged, Result::$JSON_FLAGS); // Converti l'array unito in JSON
    }
}
