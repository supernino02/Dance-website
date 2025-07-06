<?php

/**
 * Converte una stringa che definisce un path asssoluto.
 * 
 * Il path assoluto è "relativo" alla cartella ROOT_DIR.
 * Utilizzando questa funzione, si "astrae" la posizione del codice che richiede il file. 
 * 
 * @param string $path path definito in modo relativo al parent di BOOTSTAP
 * @return string path assoluto, ovvero mettendo come prefisso il path fino a ROOT_DIR
 */
function absolutePath(string $path) {
    if ($path[0] != "/") $path = DIRECTORY_SEPARATOR .$path;//la forzo a iniziare con /
    return ROOT_DIR.$path; 
}


/**
 * Restituisce una copia dell'array dato, mascherando i valori le cui chiavi contengono la parola "password".
 *
 * Ogni elemento dell'array la cui chiave contiene (case-insensitive) la stringa "password"
 * avrà il suo valore sostituito con la stringa "*****".
 * Tutti gli altri elementi rimarranno invariati.
 *
 * @param array $params Array associativo di parametri da analizzare.
 * @return array Una copia dell'array con i valori mascherati dove appropriato.
 */
function maskPassword(array $params = []): array {
    $masked = $params;

    foreach ($masked as $key => $value) {
        if (stripos($key, 'password') !== false) {
            $masked[$key] = '*****';
        }
    }

    return $masked;
}

/**
 * Funzione utilizzata per salvare in un file una stringa
 * 
 * La stringa viene messa in append su un file di log.
 * Viene preceduta da una breve descrizione della richiesta effettuata al server
 * 
 * @param mixed $val   Stringa (o oggetto) da scrivere sul file.
 * @param string $path Posizione del file [DEFAULT:LOG_ERRORS_PATH]
 * @return void
 */
function log_error(mixed $val,string $path = null):void {
    $path ??= LOG_ERRORS_PATH; //se non lo indico
    //scrivo in append le informazioni indicate 
    file_put_contents($path,request_summary().PHP_EOL.$val.PHP_EOL.PHP_EOL, FILE_APPEND); 
}

/**
 * Restituisce una stringa che descrive la richiesta fatta dal client verso il server
 * 
 * @return string
 */
function request_summary()
{
    return extended_current_date_time() . " " .
        session_id()                 . " " .
        $_SERVER['PHP_SELF']         . " " .
        get_client_ip();
}

/**
 * Funzione che restituisce il datetime, inclusi i millisecondi.
 * 
 * @return string
 */
function extended_current_date_time(): string
{
    $microtime = microtime(true);
    $milliseconds = sprintf("%03d", ($microtime - floor($microtime)) * 1000);
    return date('Y/m/d H:i:s', $microtime) . '.' . $milliseconds;
}

/**
 * Funzione che restituisce l'indirizzo IP del client.
 * 
 * @return string
 */
function get_client_ip(): string
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))           //IP dall'header HTTP_CLIENT_IP
        return $_SERVER['HTTP_CLIENT_IP'];
    else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) //IP dall'header HTTP_X_FORWARDED_FOR
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    else                                              //IP dall'header REMOTE_ADDR
        return $_SERVER['REMOTE_ADDR'];
}

/**
 * Funzione che converte un array in una stringa.
 * 
 * @param array $array l'array che voglio traformare.
 * @return string      la stringa che descrive l'array.
 */
function toString(array $array = null): string
{
    if (is_null($array)) return '[]';

    //mappa l'array sostituendo le stringhe vuote con ""
    $processedArray = array_map(function ($item) {
        return $item === '' ? '""' : $item;
    }, $array);

    //unisce gli elementi dell'array in una stringa separata da ", "
    return "[".implode(", ", $processedArray)."]";
}

/**
 * Creo una stringa casuale lunga un numero arbitrario di caratteri
 * 
 * @param int $num_chars Lunghezza richiesta.
 * @return string        Stringa generata.
 */
function random_chars(int $num_chars) :string {
    return bin2hex(random_bytes($num_chars/2));
}

/**
 * Dato un oggetto, verifica che sia un array associativo
 * 
 * Ovvero i valori delle sue chiavi NON sono SOLO in range [0,n-1]
 * 
 * @param mixed $array da verificare
 * @return bool
 */
function isAssociativeArray($array)
{
    if (!is_array($array)) return false; //non è un array
    return array_keys($array) !== range(0, count($array) - 1);
}

