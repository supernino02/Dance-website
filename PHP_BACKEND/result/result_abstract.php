<?php

/**
 * Classe astratta per la gestione dei risultati di operazioni.
 *
 * Questa classe definisce una struttura base per rappresentare i risultati di un servizio.
 * Non può essere istanziata direttamente, ma deve essere estesa da classi concrete. 
 * Ogni risultato è classificato come 'OK', 'FAIL' o 'ERROR', e può includere informazioni aggiuntive 
 * e un valore associato.
 *
 * @property string|null $result Stato del risultato ('OK', 'FAIL', 'ERROR').
 * @property string|null $additional_info Informazioni aggiuntive facoltative sul risultato.
 * @property mixed $value Valore opzionale associato al risultato (può essere qualsiasi tipo di dato).
 */
abstract class Result
{
    
    //Enum per i possibili stati del risultato.
    static public array $result_enum = array('FAIL', 'ERROR', 'OK');

    
    //Flag utilizzati per la codifica JSON dei risultati.
    static protected int $JSON_FLAGS = JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES;

    //campi del result
    protected ?string $result = null;
    protected ?string $additional_info = null;
    protected mixed $value = null;

    /***GETTERS***/
    public function getResult(): ?string
    {
        return $this->result;
    }
    public function getAdditionalInfo(): ?string
    {
        return $this->additional_info;
    }
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Resetta il valore associato al risultato.
     * 
     * Imposta il valore associato a null.
     * Viene utilizzato quando a posteriori si vogliono nascondere alcune informazioni.
     */
    public function resetValue(): void
    {
        $this->value = null;
    }

    /***CONSTRUCTOR***/
        /**
     * Costruttore della classe Result.
     *
     * Questo costruttore è protetto poiché la classe è astratta e non può essere istanziata direttamente.
     *
     * @param string $result Stato del risultato ('OK', 'FAIL', 'ERROR').
     * @param string|null $additional_info Informazioni aggiuntive facoltative.
     * @param mixed $value Valore opzionale associato al risultato.
     */
    protected function __construct(string $result, string $additional_info = null, mixed $value = null)
    {
        Result::checkValidResult($result);
        $this->result = $result;
        $this->additional_info = $additional_info;
        $this->value = $value;
    }

    /**
     * Verifica se il valore fornito è uno stato valido del risultato.
     *
     * @param string $result Stato del risultato da verificare.
     * @throws InvalidArgumentException Se il valore non è valido (non è 'OK', 'FAIL', o 'ERROR').
     */
    protected static function checkValidResult(string $result): void
    {
        if (!in_array($result, Result::$result_enum)) {
            throw new InvalidArgumentException("Invalid Result_enum value");
        }
    }

    /***VERIFICA DEI RISULTATI***/
    public static function isFAIL(Result $result): bool
    {
        return $result->result == 'FAIL';
    }
    public static function isERROR(Result $result): bool
    {
        return $result->result == 'ERROR';
    }
    public static function isOK(Result $result): bool
    {
        return $result->result == 'OK';
    }

    /**
     * Restituisce una rappresentazione in stringa dell'oggetto Result in formato JSON.
     *
     * @return string La rappresentazione in formato JSON dell'oggetto Result.
     */
    public function __toString(): string
    {
        return json_encode(get_object_vars($this), Result::$JSON_FLAGS);
    }

    /**
     * Metodo astratto per inviare l'output al client.     * 
     *
     * Questo metodo deve essere implementato nelle sottoclassi per gestire l'output dei risultati.
     * @return never Poichè questa funzione termina l' esecuzione dello script.
     */
    abstract public function outputToClient(): never;
}
