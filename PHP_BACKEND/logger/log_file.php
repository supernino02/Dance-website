<?php

/**
 * Classe Implementazione che utilizza un sistema di logging su file
 * 
 * Implementa l' utilizzo di un buffer per salvarsi tutto quello che succede.
 * Il buffer viene scritto su un file nel momento in cui lo script termina e viene chiamato il distruttore.
 * 
 * Per utilizzarla, indicarla nel campo LOG_SERVICES in LOG/config.json
 * Scrive su file le informazioni sui servizi richiesti e sulle query effettuate.
 * 
 * @property-read string $path Posizione del file di log. viene creato con LOG_FILE_PATH.
 * @property string $buffer Il buffer in cui vengono momentaneamente salvate le informazioni.
 * 
 * @see Modificare LOG/config.json per (dis)attivarla.
 */
class LogFile implements LogHandler
{
    private readonly string $path;

    private string $buffer = ""; 

    /**
     * Costruttore della classe LogFile.
     *
     * @param string $path Percorso del file su cui scrivere.
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Distruttore dell' oggetto.
     * 
     * Viene chiamato in automatico al termine dello script (sia esso generato in modo standard o anche per eccezioni/errori).
     * 
     * @return void
     */
    public function __destruct() {
        //se è vuoto il buffer non faccio nulla
        if (!$this->buffer) return;

        //verifico sia aperto il file
        if (!$file = fopen($this->path, 'a')) {
            log_error("ERROR LOGGING: CANNOT OPEN FILE {$this->path}");
            return;
        }

        //verifico sia ottenuto il lock
        if (!flock($file, LOCK_EX)) {
            log_error("ERROR LOGGING: CANNOT OBTAIN LOCK ON {$this->path}"); 
            return;
        }

        //copio il buffer sul file
        fwrite($file, 
            request_summary().PHP_EOL.
            $this->buffer           .PHP_EOL);
        
        //forzo la scrittura dei dati nel file
        fflush($file); 
        //rilascio il lock                       
        flock($file, LOCK_UN);  
        //chiudo il file              
        fclose($file);                        
    }

    
    //LOG della richiesta del servizio
    public function log_service_request(ServicesHandler $service_handler, string $service_name, array $params = []): void
    {
        $json_params = json_encode($params, JSON_UNESCAPED_SLASHES);
        //se è un utente loggato lo indico
        $user_logged = $service_handler->getServicesProvider()->getUserLogged();
        $string_logged = $user_logged ? "{$user_logged} : " : "";

        $this->buffer .= "{$string_logged}{$service_name}({$json_params})".PHP_EOL;
    }

    //LOG della risposta del servizio
    public function log_service_response(ServicesHandler $service_handler, ResultVerbose $result): void
    {
        $info = [$result->getResult(), $result->getAdditionalInfo(), $result->getRole()];
        $this->buffer .= json_encode($info, JSON_UNESCAPED_SLASHES).PHP_EOL;
    }

    //LOG di una query
    public function log_query_request(mysqli $conn, string $name, array $parameters = []): void
    {
        $json_params = json_encode($parameters, JSON_UNESCAPED_SLASHES);
        $this->buffer .= "\t{$name}({$json_params})".PHP_EOL;
    }
}