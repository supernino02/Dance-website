<?php

/**
 * Classe InvalidCookiesDescription
 * 
 * Viene lanciata quando si verifica un errore nel caricamento del file JSON delle descrizioni dei cookie.
 * 
 * @property string $path Il percorso del file JSON che ha generato l'errore.
 */
class InvalidCookiesDescription extends Exception
{
    private string $path;

    /**
     * Costruttore della classe InvalidCookiesDescription.
     * 
     * Inizializza l'eccezione con il percorso del file che ha causato l'errore.
     * 
     * @param string $path Il percorso del file JSON che ha causato l'errore.
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        parent::__construct();
    }

    public function __toString(): string
    {
        return get_class($this) . ": Some error occurs using '{$this->path}' as file for COOKIES descriptions";
    }
}

/**
 * Classe UnknownCookieException
 * 
 * Viene lanciata quando si tenta di accedere a un cookie non definito nelle descrizioni caricate.
 * 
 * @property string $id_cookie Identificatore del cookie che non è stato trovato.
 * @property array $descriptions Le descrizioni dei cookie caricate dal file JSON.
 * @property string $path Il percorso del file JSON che contiene le descrizioni dei cookie.
 */
class UnknownCookieException extends Exception
{
    private string $id_cookie;

    private array $descriptions;

    private string $path;

    /**
     * Costruttore della classe UnknownCookieException.
     * 
     * Inizializza l'eccezione con l'ID del cookie, le descrizioni caricate e il percorso del file JSON.
     * 
     * @param string $id_cookie Identificatore del cookie non trovato.
     * @param array $descriptions Array delle descrizioni dei cookie.
     * @param string $path Percorso del file JSON.
     */
    public function __construct(string $id_cookie, array $descriptions, string $path)
    {
        $this->id_cookie = $id_cookie;
        $this->path = $path;
        $this->descriptions = $descriptions;
        parent::__construct();
    }

    public function __toString(): string
    {
        return get_class($this) . ": Cookie {$this->id_cookie} id not defined in file {$this->path}:" . PHP_EOL .
            json_encode($this->descriptions);
    }
}
