<?php

//User
trait Token {
    /**
     * SERVIZIO che crea un nuovo token, lo salva nel cookie "__Host-LoginToken" e lo restituisce al client.
     * 
     * @param int $expiration_days del token, fino a un massimo di 90 [optional, default: 30].
     * @param ?string $user utente a cui associare il token  [optional,default: $SESSION['email']].
     * @return JSONResult
     *   - [FAIL,COOKIE ERROR] se il cookie associato non è stato correttamente inviato.
     *   - [OK,TOKEN,$token] se il cookie è stato creato; $token è la stringa contenuta nel cookie.
     */
    public function rememberMe(int $expiration_days = 30, string $user = null): JSONResult
    {
        $user = $this->checkIdentityConsistency($user);
        //prendo le info del cookie che sto per creare
        $virtual_cookie = new CookieManager("REMEMBER_ME_TOKEN");

        $expiration_days = max(0, $expiration_days); //mi assicuro il valore passato sia positivo
        $expiration_days = min($expiration_days, 90); //default 90 days

        $token = random_chars(32); //i token nel DB sono grandi 32 chars

        //aggiorno il token al DB
        //!se per caso viene creato un duplicato, viene alzato errore dall' handler e non viene comunicato all' user
        //!probabilitá minima
        $this->DB->executeQuery("create_token", [$user, $token, $expiration_days], true);

        $virtual_cookie->defineCookie($token,$expiration_days);

        //se il cookie non viene creato, dal DB verrà rimosso in automatico dopo la sua scadenza
        if (!$virtual_cookie->defineCookie($token,$expiration_days)) 
            return JSONResult::createFAIL("COOKIE ERROR");

        return JSONResult::createOK("TOKEN", $token);
    }

    /**
     * SERVIZIO che elimina un token  specifico associato a  un utente.
     * 
     * 
     * @param ?string $token token [default: $_COOKIE['token']].
     * @param ?string $user utente associato al token  [optional,default: $SESSION['email']].
     * @return JSONResult
     *   - [ERROR, NO TOKEN] Se $token non é stato passato come parametro e non é nemmeno nei cookie.
     *   - [ERROR, INVALID] se non esiste $token associato a $user.
     *   - [ERROR, TOKEN COOKIE NOT DELETED] se il token é stato cancellato lato server, ma non lato client.
     *   - [OK,DELETED] se $token é stato rimosso lato client e server.
     *  
     */
    public function forgetMe(string $token=null, string $user = null): JSONResult
    {
        $user = $this->checkIdentityConsistency($user);

        //prendo le informazioni sul cookie
        $virtual_cookie = new CookieManager("REMEMBER_ME_TOKEN");

        //se non ho mandato un token e non ce neppure nei cookie
        if (!$token && !$token = $virtual_cookie->obtainCookie())
            return JSONResult::createError("NO TOKEN");

        //provo a eliminare il token dal database
        if (!$this->DB->executeQuery("delete_token", [$user, $token])) 
            return JSONResult::createERROR("INVALID");

        //provo a cancellare il cookie del token lato client
        $result_delete_cookie = $this->deleteCookieToken();
        if (!Result::isOK($result_delete_cookie))//se qualcosa è andato storto, mostro l' errore
            return $result_delete_cookie;

        return JSONResult::createOK("DELETED");
    }

    /**
     * SERVIZIO che elimina TUTTI i token associati a un utente.
     * 
     *
     * @param ?string $user utente di cui vogliamo cancellare i token  [optional,default: $SESSION['email']].
     * @return JSONResult
     *   - [ERROR, NO TOKENS ASSOCIATED] Se $token non é stato passato come parametro e non é nemmeno nei cookie.
     *   - [ERROR, TOKEN COOKIE NOT DELETED] se il cookie del token non é stato cancellato dal client.
     *   - [OK,DELETED, $n_deleted] se ho cancellato $n_deleted token dal server e (eventualmente) il cookie dal client.
     *  
     */
    public function forgetMeGlobally(string $user = null) : JSONResult {
        $user = $this->checkIdentityConsistency($user);

        $n_deleted = $this->DB->executeQuery("delete_associated_tokens", [$user]);//cancello tutti i token si $user
        if ($n_deleted == 0)//se non ne cancello nemmeno uno
            return JSONResult::createERROR("NO TOKENS ASSOCIATED");

        //cancello eventualmente il cookie dal client
        $result_delete_cookie = $this->deleteCookieToken();
        if (!Result::isOK($result_delete_cookie)) //se qualcosa è andato storto, mostro l' errore
            return $result_delete_cookie;

        return JSONResult::createOK("DELETED",$n_deleted);

    }


    /**
     * SERVIZIO che elimina il cookie del token dal client (se presente).
     * 
     * @return JSONResult
     *   - [ERROR, TOKEN COOKIE NOT DELETED] se il cookie del token non é stato cancellato dal client (ed é presente).
     *   - [OK, TOKEN COOKIE NOT USED] se il cookie non é utilizzato dal client.
     *   - [OK,TOKEN COOKIE DELETED] se il cookie del client é stato cancellato correttamente.
     *  
     */

    public function deleteCookieToken() : JSONResult{
        //se necessario, provo a eliminare il cookie del token
        $virtual_cookie = new CookieManager("REMEMBER_ME_TOKEN");

        if (!$virtual_cookie->obtainCookie())
            return JSONResult::createOK("TOKEN COOKIE NOT USED");

        return $virtual_cookie->deleteCookie()
            ? JSONResult::createOK("TOKEN COOKIE DELETED") 
            : JSONResult::createERROR('TOKEN COOKIE NOT DELETED');
    }
}