<?php
//!SI NOTI COME QUESTO FILE NON VADA INDICATO IN location_maps.json, LO INCLUDE IN AUTOMATICO autoloader.php
//!questo perchè sono necessari al suo corretto funzionamento
/**
 * Classe base per le eccezioni di dipendenza alzate dal spl_autoload_register().
 * 
 * Nel normale funzionamento del sito in produzione, non dovrebbero essere mai alzate.
 * 
 * @property string $name É il nome della classe/trait richiesto.
 * @see spl_autoload_register() utilizza una funzione custom per la gestine delle classi e dei trait mancanti.
 */
class DependencyException extends Exception
{
    protected readonly string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
        parent::__construct();
    }

    public function __toString(): string
    {
        return get_class($this).": autoloader defined in " . AUTOLOADER_MAP_PATH . " is inconsistent.";
    }
}


/**
 * Eccezione alzata nel caso in cui l' oggetto cercato non sia indicato nella MAP caricata.
 * @see spl_autoload_register().
 */
class UndefinedDependencyException extends DependencyException
{
    public function __toString(): string
    {
        return parent::__toString() . PHP_EOL .
            "Undefined location for '{$this->name}'";
    }
}

/**
 * Eccezione alzata nel caso in cui l' oggetto cercato sia indicato nella MAP, ma il file non esiste.
 * @see spl_autoload_register().
 */
class MissingDependencyException extends DependencyException
{
    private string $path;

    public function __construct(string $name, string $path)
    {
        $this->path = $path;
        parent::__construct($name);
    }

    public function __toString(): string
    {
        return parent::__toString() . PHP_EOL .
            "Cannot find file '{$this->path}', location of {$this->name}";
    }
}