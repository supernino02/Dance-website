<?php

/**
 * Classe JSONResult che implementa un Result di un servizio che ritorna una risposta testuale.
 *
 * Rappresenta un risultato da inviare al client in formato JSON.
 * Utilizza stati predefiniti ("OK", "ERROR", "FAIL") per gestire diverse risposte.
 *
 * @property string|null $result Stato del risultato (OK, FAIL, ERROR).
 * @property string|null $additional_info Informazioni aggiuntive sul risultato.
 * @property mixed $value Valore associato al risultato.
 */
class JSONResult extends Result
{
    /***CREATE***/
    //creano diversi tipi di Result in base alla funzione richiesta.
    public static function createOK(string $additional_info, $value = null): self
    {
        return new self("OK", $additional_info, $value);
    }
    public static function createError(string $additional_info, $value = null): self
    {
        return new self("ERROR", $additional_info, $value);
    }
    public static function createFail(string $additional_info, $value = null): self
    {
        return new self("FAIL", $additional_info, $value);
    }

    /**
     * Output del risultato in formato JSON al client e termina l'esecuzione.
     * 
     * Invio un header che ne descrive il risultato, e stampo in output lì oggetto in formato JSON
     *
     * @return never
     */
    public function outputToClient(): never
    {
        header('Content-Type: application/json');
        die($this->__toString());
    }
}
/**
 * Classe FILEResult che implementa un Result di un servizio che restituisce un file al client.
 *
 * Utilizza stati predefiniti ("OK", "ERROR", "FAIL").
 * Nel caso sia OK allora restituisce un file; altrimenti restituisce un JSON che descrive il risultato
 *
 * @property string|null $result Stato del risultato (OK, FAIL, ERROR).
 * @property string|null $additional_info Informazioni aggiuntive sul risultato.
 * @property string|null $value Percorso del file associato al risultato.
 */
class FILEResult extends Result
{
    /***CREATE***/
    //creano diversi tipi di Result in base alla funzione richiesta.
    public static function createOK(string $path = null): self
    {
        return new self("OK", "DOWNLOAD", $path);
    }
    public static function createError(string $path = null): self
    {
        return new self("ERROR", "DOWNLOAD", "Something went wrong downloading '" . basename($path) . "'");
    }
    public static function createFail(string $path): self
    {
        return new self("FAIL", "DOWNLOAD", "File '" . basename($path) . "' cannot be downloaded");
    }

    /**
     * Invia il file al client se lo stato è "OK". In caso contrario, restituisce un messaggio di errore in formato JSON.
     *
     * Se il risultato è "FAIL" o "ERROR", viene inviato un JSON con l'errore, altrimenti invia il file per il download.
     *
     * @return never
     */
    public function outputToClient(): never
    {
        // Se lo stato non è OK, restituisci un errore in formato JSON
        if (!Result::isOK($this)) {
            header('Content-Type: application/json');
            die($this);
        }

        // Se lo stato è OK, invia il file al client
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($this->value) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($this->value));
        readfile($this->value);
        die();
    }
}

/**
 * Classe TEMPORARY_FILEResult, estende la classe FILEResult e descrive FILE TEMPORANEI da inviare al client. 
 * 
 * I risultati di questa classe possono essere solamente di tipo "OK".
 * Questa classe è utilizzata per descrivere risultati di file temporanei (esempio file .zip).
 * I file vengono cancellati dal sistema dopo essere stati inviati al client.
 *
 * @property string $real_name Il nome con cui viene mostrato il file sul client, incluso con l'estensione.
 */
class TEMPORARY_FILEResult extends FILEResult
{
    public string $real_name;

    /**
     * Crea un'istanza di TEMPORARY_FILEResult con stato "OK", destinata all'invio di un file temporaneo.
     * 
     * @param string|null $path Il percorso del file da inviare (opzionale).
     * @param string $real_name Il nome reale del file che viene inviato, senza estensione.
     * @return TEMPORARY_FILEResult
     */
    public static function create(string $path = null, string $real_name): TEMPORARY_FILEResult
    {
        $result = new TEMPORARY_FILEResult("OK", "DOWNLOAD", $path);
        $extension = TEMPORARY_FILEResult::getFileExtension($path);
        $result->real_name = "{$real_name}.{$extension}";
        return $result;
    }

    /**
     * Recupera l'estensione del file dato il percorso.
     * 
     * @param string $path Il percorso del file.
     * @return string L'estensione del file in minuscolo.
     */
    static private function getFileExtension(string $path): string
    {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }

    /**
     * Invia il file al client e lo elimina dal filesystem una volta completato l'invio.
     * 
     * Se il file ha estensione .zip, invia i relativi header per il trasferimento del file ZIP.
     * Al termine dell'invio, il file viene eliminato dal percorso specificato.
     * 
     * @return never
     */
    public function outputToClient(): never
    {
        header('Content-Description: File Transfer');

        // Imposta l'header in base all'estensione del file
        if (TEMPORARY_FILEResult::getFileExtension($this->value) == 'zip') {
            header('Content-Type: application/zip');
        } else {
            header('Content-Type: application/octet-stream');
        }

        // Imposta il nome del file per il download
        header("Content-Disposition: attachment; filename=\"{$this->real_name}\"");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($this->value));

        // Legge e invia il file al client
        readfile($this->value);

        // Cancella il file dopo il trasferimento
        unlink($this->value);
        die();
    }
}
