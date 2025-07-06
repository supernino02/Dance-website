<?php

/**
 * CookieManager gestisce i cookie con lazy loading e supporto per le preferenze utente sui cookie.
 * 
 * Questa classe permette di caricare le descrizioni dei cookie da un file JSON, ottenere le informazioni su un cookie,
 * e gestire le operazioni di lettura, scrittura e cancellazione dei cookie.
 * Supporta le preferenze degli utenti, in cui i cookie non essenziali devono essere esplicitamente permessi.
 * 
 * @property array $cookie_info Array che contiene le informazioni su un singolo cookie (name, options, default_expiration_days, etc.).
 * 
 * Proprietà statiche:
 * - `static private ?string $file_description_path`: Percorso del file JSON con la descrizione dei cookie.
 * - `static private ?array $cookies_description`: Descrizione dei cookie caricata dal file JSON.
 * - `static private ?CookieManager $COOKIE_PREFERENCES`: Istanza di CookieManager che gestisce il cookie delle preferenze dell'utente.
 *
 */
class CookieManager
{
    /***********INIZIO PARTE STATIC**********/
    //campi statici caricati con lazy loading
    static private ?string $file_description_path = null;
    static private ?array $cookies_description = null;
    static private ?CookieManager $COOKIE_PREFERENCES = null;

    /**
     * Carica la descrizione di tutti i cookie dal file e la restituisce.
     * 
     * @return array Ritorna l'array con le descrizioni di tutti i cookie.
     */
    static public function getAllCookieDescriptions(): array
    {
        CookieManager::loadCookiesDescription();
        return self::$cookies_description;
    }

    /**
     * Restituisce il cookie delle preferenze dell'utente.
     * 
     * Questo cookie contiene un array di preferenze che specifica quali cookie possono essere utilizzati.
     * 
     * @return CookieManager Istanza di CookieManager per il cookie delle preferenze.
     */
    static public function getPreferencesCookie(): CookieManager
    {
        CookieManager::loadCookiesDescription();
        return CookieManager::$COOKIE_PREFERENCES;
    }

    /**
     * Imposta il percorso del file JSON che descrive i cookie.
     * 
     * @param string $path Il percorso del file JSON.
     */
    static public function setCookiesDescriptionFile(string $path)
    {
        self::$file_description_path = $path;
    }

    /**
     * Carica la descrizione dei cookie dal file JSON se non è già stata caricata.
     * 
     * Se la descrizione è già stata caricata, la funzione non fa nulla. In caso di errore nel caricamento,
     * viene lanciata un'eccezione InvalidCookiesDescription.
     * 
     * @throws InvalidCookiesDescription Se non è possibile caricare il file JSON con le descrizioni dei cookie.
     */
    static public function loadCookiesDescription()
    {
        //se l' array che descrive i cookie è già caricato, non faccio nulla
        if (!is_null(self::$cookies_description)) return;

        try {
            self::$cookies_description = json_decode(file_get_contents(self::$file_description_path), true);
        } catch (Throwable $t) {
            throw new InvalidCookiesDescription(self::$file_description_path);
        }

        //definisco il campo static come oggetto che descrive le preferenze dei cookie
        self::$COOKIE_PREFERENCES = new CookieManager("COOKIE_PREFERENCES");
    }

    /***********FINE PARTE STATIC**********/

    //campi che descrivono il cookie
    private array $cookie_info;

    /**
     * Restituisce il nome del cookie.
     * 
     * @return string Il nome del cookie.
     */
    public function getName(): string
    {
        return $this->cookie_info['name'];
    }

    /**
     * Costruttore della classe CookieManager.
     * 
     * Inizializza l'istanza con le informazioni relative a un cookie specifico.
     * Se il cookie richiesto non esiste nella descrizione, viene lanciata un'eccezione.
     * 
     * @param string $cookie_id Identificatore del cookie da caricare.
     * 
     * @throws UnknownCookieException Se il cookie non è presente nella descrizione.
     */
    public function __construct(string $cookie_id)
    {
        //se necessario, leggo il file delle descrizioni
        self::loadCookiesDescription();

        //se il cookie non ha informazioni associate
        if (!array_key_exists($cookie_id, self::$cookies_description))
            throw new UnknownCookieException($cookie_id, self::$cookies_description, self::$file_description_path);

        //definisco l' array con le informazioni
        $this->cookie_info = self::$cookies_description[$cookie_id];
    }

    /**
     * Ottiene il valore del cookie dal client, se esiste.
     * 
     * @return mixed Il valore del cookie se esiste, altrimenti null.
     */
    public function obtainCookie(): mixed
    {
        if (isset($_COOKIE[$this->cookie_info['name']]))
            return $_COOKIE[$this->cookie_info['name']];

        return null;
    }

    /**
     * Verifica se un cookie può essere definito in base alle preferenze dell'utente.
     * 
     * I cookie essenziali sono sempre permessi, mentre gli altri richiedono il consenso esplicito dell'utente.
     * 
     * @return bool Ritorna true se il cookie può essere utilizzato, false altrimenti.
     */
    private function checkCookiePreference(): bool
    {
        //se è essential è a priori permesso
        if ($this->cookie_info['type'] == 'essential') return true;

        //prendo dal cookie delle preferenze il contenuto
        $preferences_string = self::$COOKIE_PREFERENCES->obtainCookie();

        //se non è indicato, allora non cè il permesso
        if (!$preferences_string) return false;

        //lo converto da stringa js a array
        $preferences_array = json_decode($preferences_string, true);

        //se non è indicato nelle preferenze, analogo a fosse false
        $cookie_name = $this->getName();
        if (!array_key_exists($cookie_name, $preferences_array))
            return false;

        //se cè,ritorno la preferenza indicata
        return $preferences_array[$cookie_name];
    }

    /**
     * Definisce un nuovo cookie con una durata specificata o predefinita.
     * 
     * Se il cookie non è permesso dalle preferenze dell'utente, non verrà definito.
     * 
     * @param mixed $value Il valore da assegnare al cookie.
     * @param ?int $default_expiration_days Numero di giorni per l'espirazione del cookie, se specificato.
     * 
     * @return bool Ritorna true se il cookie è stato impostato correttamente, false altrimenti.
     */
    public function defineCookie(mixed $value, ?int $default_expiration_days = null): bool
    {
        //verifico che il cookie si possa utilizzare (indicato nelle preferenze dei cookie)
        if (!$this->checkCookiePreference()) return false;
        //se ho passato il parametro, altrimenti é il default
        $days = $default_expiration_days ?? $this->cookie_info['default_expiration_days'];
        //metto l'opzione per l'expiration 
        $this->cookie_info['options']['expires'] = time() + 60 * 60 * 24 * $days;

        //se non è stringa, lo converto
        $cookie_value = is_string($value) ? $value : json_encode($value);

        return setcookie(
            $this->cookie_info['name'],
            $cookie_value,
            $this->cookie_info['options']
        );
    }


    /**
     * Cancella il cookie corrente, se esiste, impostando la sua scadenza nel passato.
     * 
     * @return bool Ritorna true se il cookie è stato cancellato o non esisteva, false in caso di errore.
     */
    public function deleteCookie(): bool
    {
        //se non é settao non faccio nulla
        if (!isset($_COOKIE[$this->cookie_info['name']])) return true;

        return $this->defineCookie("",-365);//setto il tempo un anno nel passato
    }
}
