<?php

/**
 * Classe che descrive un servizio definendone l'interfaccia.
 *
 * Contiene una dettagliata descrizione della sua signature.
 * Fornisce diversi controlli per verificare la compatibilità con dei parametri forniti dall' utente.
 * Tutte le informazioni sono fornite direttamente dall' engine PHP tramite ReflectionMethod.
 *
 * @property string $service_name Nome del servizio
 * @property array<ServiceParameter> $params Descrive quali parametri sono definiti nella signature.
 * 
 * @see ServiceParameter Descrive i singoli parametri in input al servizio.
 */
final class ServiceInterface
{
    public readonly string $service_name;
    public readonly array $params; //array<ServiceParameter>

    /**
     * Costruttore della classe.
     *
     * @param string $service_name Nome del servizio.
     * @param ReflectionMethod $reflectionMethod Oggetto ReflectionMethod che descrive il metodo del servizio.
     */
    public function __construct(string $service_name, ReflectionMethod $reflectionMethod)
    {
        $this->service_name = $service_name;
        //creo l' array dei parametri richiesti
        $parameters = [];
        foreach ($reflectionMethod->getParameters() as $arg)
            $parameters[] = new ServiceParameter($arg);

        $this->params = $parameters;
    }

    /**
     * Restituisce il numero massimo di argomenti richiesti dal servizio.
     *
     * @return int Numero massimo di argomenti richiesti.
     */
    public function maxArgsRequired()
    {
        return count($this->params);
    }

    /**
     * Verifica se il numero di argomenti forniti è superiore al numero massimo consentito.
     *
     * @param int $n_params Numero di parametri forniti.
     * @throws InvalidServiceBindException Se il numero di parametri supera il massimo consentito.
     */
    public function checkTooManyArgs(int $n_params)
    {
        if ($n_params > $this->maxArgsRequired())
            throw new InvalidServiceBindException($this, "Too many arguments");
    }

    /**
     * Restituisce il numero minimo di argomenti richiesti dal servizio.
     * 
     * Ovvero un parametro è obbligatorio se non ha definito un valore di default.
     *
     * @return int Numero minimo di argomenti richiesti.
     */
    public function minArgsRequired()
    {
        $count = 0;
        foreach ($this->params as $param) if (!$param->has_default) $count++;
        return $count;
    }

    /**
     * Verifica se il numero di argomenti forniti è inferiore al numero minimo richiesto.
     *
     * @param int $n_params Numero di parametri forniti.
     * @throws InvalidServiceBindException Se il numero di parametri è inferiore al minimo richiesto.
     */
    public function checkTooFewArgs(int $n_params)
    {
        if ($n_params < $this->minArgsRequired())
            throw new InvalidServiceBindException($this, "Too few arguments");
    }

    /**
     * Verifica la compatibilità dei tipi degli argomenti forniti con quelli richiesti dal servizio.
     * 
     * per ogni parametro passatao in input:
     *  - se é null
     *      - se il parametro richiesto accetta i null                          -> lo lascia null.
     *      - se il parametro richiesto non accetta i null e ha un deafault     -> lo modifica e lo fa diventare il default.
     *      - se il parametro richiesto non accetta i null e non ha un deafault -> alza ECCEZIONE.
     *  - se non é null
     *      - se il tipo fornito é compatibile con quello richiesto             -> lo lascia cosí.
     *      - se il tipo fornito non é compatibile con quello richiesto         -> alza ECCEZIONE.
     *
     * @param array $params_given Array di valori degli argomenti forniti. (VIENE MODIFICATO)
     * @throws InvalidServiceBindException Se uno degli argomenti forniti non è convertibile nel tipo richiesto.
     */
    public function checkArgsCompatibility(array &$params_given): array
    {
        //itero su tutti i parametri
        for ($i = 0; $i < count($params_given); ++$i) {
            if (is_null($params_given[$i])) {
                if ($this->params[$i]->isNullable) continue; //non faccio nulla
                if ($this->params[$i]->has_default) {        //aggiorno il valore
                    $params_given[$i] = $this->params[$i]->default;
                    continue;
                }
                throw new InvalidServiceBindException($this, ($i + 1) . "-nth given argument cannot be NULL (requested {$this->params[$i]->type})");
            }

            //se il tipo dell'argomento fornito non é compatibile con quello richiesto
            if (!self::isConvertible($params_given[$i], $this->params[$i]->type)) {
                $type_given = gettype($params_given[$i]);
                throw new InvalidServiceBindException($this, ($i + 1) . "-nth given argument fails typechecking (given {$type_given},requested {$this->params[$i]->type})");
            }
        }

        //ritorno i nuovi parametri (eventualmente "aggiustati" dei valori mancanti);
        return $params_given;
    }

    /**  
     * Funzione che data una variabile e un tipo, verifica che la conversione sia "lossless".
     * 
     * In pratica prova a convertire $var nell'equivalente di tipo $type, e verifica che il valore non sia cambiato.
     * Es. float->int  LOSSLESS
     * Es. string->int NO LOSSLESS
     * 
     * @param mixed $var variabile di tipo generico.
     * @param string $type nuovo tipo di $var.
     * 
     * @return bool true é conversione loss less, false se la conversione non é possibile.
     */
    function isConvertible(mixed $var, string $type): bool
    {
        //mi salvo il valore originale
        $original = $var;
        //prova a convertire $var a $type e controlla se il risultato non sia cambiato
        if (settype($var, $type)) {
            return  $var == $original; //controllo il valore non sia cambiato
        }
        return false;
    }


    /**
     * Restituisce una rappresentazione testuale della firma del servizio.
     *
     * @return string Rappresentazione testuale della firma del servizio.
     */
    public function __toString(): string
    {
        //rimuovo la prima e ultima lettera, sono le []
        $signature = substr(toString($this->params),1,-1);
        return "{$this->service_name}({$signature})";
    }
}

/**
 * Classe che rappresenta un parametro di un servizio.
 * 
 * Definisce il nome del parametro (obbligatorio), il tipo, se è nullable, e se ha un valore di default.
 * 
 * @property-read string $name        Nome del parametro.
 * @property-read ?string $type       Tipo del parametro [opzionale].
 * @property-read bool $isNullable    Indica se il parametro è nullable.
 * @property-read bool $has_default   Indica se il parametro ha un valore di default.
 * @property-read mixed $default      Valore di default del parametro [opzionale].
 * 
 * @see ServiceInterface Per vedere dove viene utilizzata la classe.
 */
final class ServiceParameter
{    public readonly string $name;
    public readonly bool $isNullable;
    public readonly ?string $type;
    public readonly bool $has_default;
    public readonly mixed $default;

    /**
     * Costruttore della classe ServiceParameter.
     * 
     * Inizializza un oggetto di tipo ServiceParameter basato su un'istanza di ReflectionParameter.
     *
     * @param ReflectionParameter $paramater Oggetto ReflectionParameter che rappresenta il parametro del servizio.
     */
    public function __construct(ReflectionParameter $paramater)
    {
        $this->name = $paramater->getName();

        // Verifica se il parametro ha un tipo definito.
        if ($paramater->hasType())
            $this->type = $paramater->getType()->getName();
        else
            $this->type = null;

        // Verifica se il parametro è nullable.
        $this->isNullable = $paramater->getType()?->allowsNull() ?? false;

        // Verifica se il parametro ha un valore di default.
        if ($this->has_default = $paramater->isDefaultValueAvailable())
            $this->default = $paramater->getDefaultValue();
        else
            $this->default = null;
    }

    /**
     * Restituisce una rappresentazione testuale del parametro.
     * 
     * Questa descrizione del parametro è in un formato simile ai linguaggi di programmazione.
     * Ne mostra il tipo, il nome e il valore di default se presente.
     *
     * @return string Descrizione leggibile del parametro.
     */
    public function __toString(): string
    {
        $nullable_prefix = $this->isNullable ? '?' : '';
        if ($this->has_default) {
            if (is_null($this->default)) 
                $val_string = "null";
             elseif (is_numeric($this->default)) 
                $val_string = $this->default;
             else 
                $val_string = " '{$this->default}' ";
            

            return "{$nullable_prefix}{$this->type} {$this->name} = {$val_string}";
        }

        // Se non ha un valore di default.
        return "{$nullable_prefix}{$this->type} {$this->name}";
    }
}
