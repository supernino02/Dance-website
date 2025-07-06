<?php


/**
 * Classe che gestisce il logging tramite SQL per i servizi e le query richieste.
 *
 * Questa classe utilizza un file JSON per caricare le query SQL necessarie per il logging.
 * Rispetto alle query effettuate tramite la classe db_interface, in questa classe risultano molto piú verbose e meno immediate. 
 * Questo ovviamente poiché ú un sistema di logging di livello inferiore e quindi utilizza con meno controlli i metodi forniti dagli oggetti mysqli.
 * L'ID dell'entry appena creata nel log viene memorizzato nella proprietà $ID_SERVICE_ENTRY.
 * 
 * @property array $queries Sono le query necessarie al funzionamento della classe.
 * @property int $ID_SERVICE_ENTRY Salva con un id univoco la richiesta del servizio (generata dal DB). 
 * 
 * @see LOG/queries.json per vedere come comunica col DB
 */
class LogSQL implements LogHandler
{
    private array $queries;    //un array che contiene le query per comunicare col DB per il LOG

    private int $ID_SERVICE_ENTRY;

    /**
     * Costruttore della classe LogSQL.
     *
     * @param string $path Percorso del file JSON contenente le query SQL per il logging.
     */
    public function __construct(string $path)
    {
        $this->queries = json_decode(file_get_contents($path), true);
        $this->ID_SERVICE_ENTRY = 0;//valore invalido
    }


    /**
     * Registra nel log la richiesta di un servizio.
     *
     * @param ServicesHandler $service_handler Gestore del servizio.
     * @param string $service_name Nome del servizio richiesto.
     * @param array $params Parametri passati al servizio (opzionale).
     */
    public function log_service_request(ServicesHandler $service_handler, string $service_name, array $params = []): void
    {
        $conn = $service_handler->DB->getConnection();

        $user = $service_handler->getServicesProvider()->getUserLogged(); //user (eventualmente)
        $role = $service_handler->getServicesProviderName(); //privilegio attuale
        $session_id = session_id();

        //SERVIZIO RICHIESTO
        $service_call_query = $this->queries['service_call'];
        $service_call_stmt = $conn->prepare($service_call_query);

        $service_call_stmt->bind_param(
            "ssss",
            $service_name,
            $user,
            $role,
            $session_id
        );
        $service_call_stmt->execute();

        $this->ID_SERVICE_ENTRY = $conn->insert_id; // ID APPENA INSERITO

        //se necessario, mi salvo i parametri passati
        if ($params == []) return;

        $service_args_query = $this->queries['service_parameters'];
        $service_args_stmt = $conn->prepare($service_args_query);

        $param_string = json_encode($params);

        $service_args_stmt->bind_param(
            "is",
            $this->ID_SERVICE_ENTRY,
            $param_string
        );
        $service_args_stmt->execute();
    
    }

    /**
     * Registra nel log la risposta di un servizio.
     *
     * @param ServicesHandler $service_handler Gestore del servizio.
     * @param Result $result Risultato ottenuto dal servizio.
     */
    public function log_service_response(ServicesHandler $service_handler, ResultVerbose $result): void
    {
        $conn = $service_handler->DB->getConnection();
        //SERVIZIO RICHIESTO
        $service_response_query = $this->queries['service_response'];
        $service_response_stmt = $conn->prepare($service_response_query);

        $result_type = $result->getResult();
        $additional_info = $result->getAdditionalInfo();

        $service_response_stmt->bind_param(
            "iss",
            $this->ID_SERVICE_ENTRY,
            $result_type,
            $additional_info
        );
        $service_response_stmt->execute();
    }

    /**
     * Registra nel log la richiesta di una query.
     *
     * @param mysqli $conn Connessione al database.
     * @param string $name Nome della query richiesta.
     * @param array $parameters Parametri passati alla query (opzionale).
     */
    public function log_query_request(mysqli $conn, string $name, array $parameters = []): void
    {

        $query_call_query = $this->queries['query_call'];
        $query_call_stmt = $conn->prepare($query_call_query);

        $args_list = json_encode($parameters); //cast to string

        $query_call_stmt->bind_param(
            "iss",
            $this->ID_SERVICE_ENTRY,
            $name,
            $args_list
        );
        $query_call_stmt->execute();
    }
}
