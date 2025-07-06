<?php
//user
trait Cookie_preferences
{

    /** 
     * SERVIZIO che dato un json che dscrive un array associativo, crea un cookie che descrive le preredenze sui cookie opzionali.
     * 
     * Le preferenze sui cookie Essential vengono indicate ma ignorate, in quanto non possono essere rifiutati.
     * Se non vengono passati parametri, vengono rimosse le preferenze attuali.
     * Le preferenze sono in formato {"nome_cookie":bool}.
     * Il nuovo valore $new_preferences prende in considerazione le preferenze pregresse, unite a quelle inviate al servizio.
     * 
     * @param string $json String json che descrive le scelte [optional, default:null].
     * @return 
     *  - [FAIL,INVALID PARAMETER]            se $json_preferences NON era un formato json valido.
     *  - [FAIL,NOT UPDATED,$new_preferences] se l' header set-cookie non è stato inviato correttamente con valore $new_preferences.
     *  - [OK,UPDATED,$new_preferences]       se il cookie è stato correttamente aggiornato con valore $new_preferences.
     * 
     * */
    public function setCookiePreferences(string $json_preferences = null): JSONResult
    {
        //se non passo nulla, elimino le preferenze
        if (is_null($json_preferences)) $new_preferences = null;
        //altrimenti le aggiorno
        else {
            //ottengo le preferenze attuali
            $preferences = $this->getCookiePreferences()->getValue();
            $decoded = json_decode($json_preferences, true);

            //verifico sia passato un array
            if (!is_array($decoded) || is_null($decoded))
                return JSONResult::createFAIL("INVALID PARAMETER");

            $new_preferences = array_merge($preferences, $decoded);
        }

        return CookieManager::getPreferencesCookie()->defineCookie($new_preferences) ?
            JSONResult::createOK("UPDATED", $new_preferences) :
            JSONResult::createFAIL("NOT UPDATED", $preferences);
    }

    /** 
     * SERVIZIO che restituisce le preferenze attuali sui cookie (definite in precedenza dall' utente), in formato array associativo.
     * 
     * L' array eventualmente ritornato è in formato {"nome_cookie":bool}.
     * 
     * @return 
     *  - [ERROR,EMPTY,[]]        se non sono presenti preferenze.
     *  - [OK,PREFERENCES,$array] ritorna un $array delle preferenze fornite in precedenza.
     * 
     * */
    public function getCookiePreferences(): JSONResult
    {
        //creo l' oggetto che gestisce i cookie
        $cookie_preferences = CookieManager::getPreferencesCookie();

        //ottengo le preferenze attuali
        $preferences = $cookie_preferences->obtainCookie();

        //se il cookie è vuoto
        if (!$preferences)
            return JSONResult::createERROR("EMPTY", []);

        //se non è vuoto
        return JSONResult::createOK("PREFERENCES", json_decode($preferences,true));
    }


    /**
     * SERVIZIO che restituisce un tabella descrittiva dei cookie utilizzati dal sito.
     * 
     * L' eventuale array ritornato è un array di oggetti, in cui ognuno definisce le caratteristiche di un cookie.
     * 
     * @return JSONResult
     *  - [FAIL,NO DESCRIPTIONS]        se non ci sono cookie utilizzati dal sito (o le descrizioni non sono state ricavate).
     *  - [COOKIES DESCRIPTIONS,$array] ritorna un array di oggetti che descrivono i cookie.
     */
    public function getAllCookiesDescription(): JSONResult
    {
        if ($descriptions = CookieManager::getAllCookieDescriptions())
            return JSONResult::createOK("COOKIES DESCRIPTIONS", array_values($descriptions)); //rimuovo gli ID nominativi dei cookie, sono informazioni private
        return JSONResult::createFail("NO DESCRIPTIONS");
    }
}
