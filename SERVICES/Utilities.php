<?php
//user
trait Utilities {
    /**
     * SERVIZIO che elenca tutti i servizi attualmente utilizzabili.
     * 
     * @return JSONResult
     *   - [OK, LIST, $services] lista dei servizi che si possono chiamare.
     **/
    public function listServices(): JSONResult
    {
        //sono i servizi che puo chiamare un utente
        $services = $this->getAvailableServicesArray();
        return JSONResult::createOK("LIST", $services);
    }

    /**
     * SERVIZIO che dato il nome di un servizio, ne descrive l'utilizzo.
     * 
     * @param string $service_name è il nome del servizio [optional,default="describeService"]
     * @return JSONResult
     *   - [FAIL, INVALID NAME] non esiste tale servizio (o non é attualmente chiamabile).
     *   - [ERROR, MISSING DESCRIPTION] se il servizio esiste ed é utilizzabile, ma non ha la descrizione.
     *   - [OK, DESCRIPTION, $phpdoc] mostra una descrizione PHPDOCS del servizio in cui spiega quali sono gl iinput e gli output forniti.
     */
    public function getServiceDescription(string $service_name = "getServiceDescription"): JSONResult
    {
        //ottengo la lista dei servizi chiamabili
        $allowed_services = $this->listServices()->getValue();
        if (!in_array($service_name, $allowed_services)) //servizio che non esiste
            return JSONResult::createFAIL("INVALID NAME");

        $reflection_method = new ReflectionMethod(get_class($this), $service_name);
        
        if ($php_doc = $reflection_method->getDocComment()) //se è presente
            return JSONResult::createOK("DESCRIPTION", $php_doc);
        else
            return JSONResult::createERROR("MISSING DESCRIPTION");
    }


    /**
     * SERVIZIO che dato il nome di un servizio, fornisce una rappresentazione rigorosa della sua signature.
     * 
     * Ovvero descrive le modalitá con cui si puó richiamare tale servizio.
     * 
     * @param string $service_name è il nome del servizio [optional,default="describeService"]
     * @return JSONResult
     *   - [FAIL, INVALID NAME] non esiste tale servizio
     *   - [OK, SIGNATURE, $params] restisuisce un array di array_associativi in forma {name,type,has_default,default}.
     */
    public function getServiceSignature(string $service_name): JSONResult
    {
        if (!in_array($service_name, $this->getAvailableServicesArray())) //servizio che non esiste
            return JSONResult::createFAIL("INVALID NAME");

        //in output mostro un array che descrive ogni parametro nella signature
        $parameters = $this->getServiceInterface($service_name)->params; //è un array
        return JSONResult::createOK("SIGNATURE", $parameters);
    }
}
