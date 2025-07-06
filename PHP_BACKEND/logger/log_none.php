<?php
/**
 * Interfaccia che definisce i metodi necessari per la gestione del logging di servizi e query.
 */
interface LogHandler
{
    public function log_service_request(ServicesHandler $service_handler, string $service_name, array $params = []): void; //LOG della richiesta del servizio
    public function log_service_response(ServicesHandler $service_handler, ResultVerbose $result): void;  //LOG della risposta del servizio
    public function log_query_request(mysqli $conn, string $name, array $parameters = []): void;  //LOG di una query
}


/**
 * Classe Implementazione utilizzata evetntualmente come "fantoccio" per il logging.
 * 
 * Di fatto non esegue alcuna operazione.
 * Per utilizzarla, indicarla nel campo LOG_SERVICES in LOG/config.json
 * 
 * @see Modificare LOG/config.json per (dis)attivarla.
 */
class LogNone implements LogHandler
{
    //LOG della richiesta del servizio
    public function log_service_request(ServicesHandler $service_handler, string $service_name, array $params = []): void
    {
    }

    //LOG della risposta del servizio
    public function log_service_response(ServicesHandler $service_handler, ResultVerbose $result): void
    {
    }

    //LOG di una query
    public function log_query_request(mysqli $conn, string $name, array $parameters = []): void
    {
    }
}

