<?php

/**
 * Classe che avvolge le informazioni su una query e gestisce l'inizializzazione lazy dell'oggetto Query.
 * 
 * Questa classe è progettata per ritardare la creazione effettiva di un oggetto Query fino a quando non è necessario.
 * Le informazioni sulla query sono inizialmente memorizzate in un array, e l'oggetto Query viene creato solo quando viene chiamato `getQuery()`.
 *
 * @property array|null $query_info Array associativo contenente le informazioni necessarie per creare l'oggetto Query.
 * @property Query|null $query_object L'oggetto Query, inizializzato su richiesta (lazy loading).
 *
 * Si noti come i  campi siano esclusivi, per ottimizzare l' uso della memoria (una volta creato l' oggetto, non è più necessario l' array descrittivo).
 */
class QueryWrapper
{
    private ?array $query_info;
    private ?Query $query_object = null;

    /**
     * Costruttore della classe QueryWrapper.
     *
     * @param array $query_info Array associativo con le informazioni della query, necessarie per creare un oggetto Query.
     */
    public function __construct(array $query_info)
    {
        $this->query_info = $query_info;
    }

    /**
     * Restituisce l'oggetto Query associato, inizializzandolo se necessario.
     *
     * Questo metodo implementa il lazy loading, creando l'oggetto Query solo al momento della richiesta.
     *
     * @return Query L'oggetto Query associato alle informazioni memorizzate.
     */
    public function getQuery(): Query
    {
        //se l' oggetto non è ancora stato creato, lo creo
        if (!$this->query_object) {
            $this->query_object = new Query($this->query_info);
            $this->query_info = null; //elimino le informazioni (ora inutili)
        }

        return $this->query_object;
    }

    /**
     * Restituisce una rappresentazione in stringa dell'oggetto QueryWrapper.
     *
     * Se l'oggetto Query è stato inizializzato, restituisce la rappresentazione in stringa dell'oggetto Query.
     * Altrimenti, restituisce la rappresentazione in JSON dell'array di informazioni della query.
     *
     * @return string Rappresentazione in stringa dell'oggetto QueryWrapper.
     */
    public function __toString(): string
    {
        if ($this->query_object)
            return PHP_EOL.$this->query_object->__toString();

        return PHP_EOL.json_encode($this->query_info, JSON_PRETTY_PRINT);
    }

    /**
     * Crea una collezione di oggetti QueryWrapper da un insieme di file di interfaccia.
     *
     * @param array $interface_files Array di percorsi dei file JSON contenenti le definizioni delle query.
     * @param mysqli $connection L'oggetto mysqli per la connessione al database.
     * @return array<QueryWrapper> Array di oggetti QueryWrapper creati a partire dai file di interfaccia.
     */
    public static function createCollection(array $interface_files, mysqli $connection): array
    {
        $queries = [];
        foreach ($interface_files as $file_path) {                      //ciclo su ogni file
            $file_content = file_get_contents(absolutePath($file_path));//ottengo il contenuto
            $array = json_decode($file_content, true);                  //lo converto in array
            foreach ($array as $query_name => $query_info) {            //ciclo su ogni array
                $query_info_extended = array_merge(                     //aggiungo altre informazioni all' array che comporrà la query
                    $query_info,
                    [
                        'name' => $query_name,
                        'origin_file' => $file_path,
                        'connection' => $connection
                    ]
                );
                $queries[$query_name] = new QueryWrapper($query_info_extended);//costruisco un oggetto wrapper e lo aggiungo alla collezione
            }
        }

        return $queries;//restituisco l' array crato.
    }
}
