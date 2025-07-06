<?php

/**
 * Eccezione alzata quando viene richiesto un servizio, ma i parametri forniti differiscono da quelli richiesti e non sono compatibili.
 */
class InvalidServiceBindException extends Exception
{
    private readonly ServiceInterface $service_requested;
    private string $error_type;

    public function __construct(ServiceInterface $service_requested, string $error_type) {
        $this->service_requested = $service_requested;
        $this->error_type = $error_type;
        parent::__construct();
    }

    public function __toString(): string {
        return "{$this->error_type} CALLING {$this->service_requested}";
    }
}

/**
 * Eccezione alzata quando viene richiesto un servizio correttamente, ma l'utente impersona in modo illecito qualcun'altro.
 * 
 * Nel caso in cui sia un utente normale, non puó fingersi una persona che non sia esso stesso (cioé con la stessa mail).
 * Nel caso in cui sia un admin, non puó fingersi una persona che non esiste nel database.
 *
 */
class AccessViolationException extends Exception {
    private readonly string $user_logged;
    private readonly string $user_requested;

    public function __construct(string $user_requested,string $user_logged = null) {
        //se non è passato come valore, lo prendo dalla sessione
        if (is_null($user_logged) && isset($_SESSION['email']) && !empty($_SESSION['email'])) $user_logged = $_SESSION['email'];
        $this->user_logged = $user_logged;
        $this->user_requested = $user_requested;
        parent::__construct();
    }

    public function __toString(): string
    {
        return "User logged as \"{$this->user_logged}\" CANNOT IMPERSONATE \"{$this->user_requested}\"";
    }
}

/**
 * Eccezione alzata quando viene richiesto un servizio, ma non si hanno i permessi necessari (oppure non esiste).
 */
class InvalidServiceException extends Exception {
    private readonly AbstractServicesProvider $provider;
    private readonly string $service_requested;
    public function __construct($provider, $service_requested) {
        $this->provider = $provider;
        $this->service_requested = $service_requested;
        parent::__construct();
    }

    public function __toString(): string
    {
        return "\"{$this->service_requested}\" cannot be requested as \"{$this->provider::getProviderName()}\"";
    }
} 
