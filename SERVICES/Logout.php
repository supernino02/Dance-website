<?php
//User
trait Logout {
    /**
     * SERVIZIO che effettua il logout lato client e server.
     *
     * Lato client cancella il cookie di sessione e eventualmente il token per il login.
     * Lato server chiude ed elimina la sessione.
     *
     * @return JSONResult
     *   - [FAIL, SESSION NOT DELETED]         se la sessione non si è chiusa correttamente.
     *   - [ERROR, SESSION COOKIE NOT DELETED] se la sessione è chiusa correttamente, ma il cookie non è stato eliminato dal client.
     *   - [ERROR, TOKEN COOKIE NOT DELETED]   se la sessione è chiusa correttamente lato server e client, ma il token è ancora presente sul client.
     *   - [OK,LOGOUT]                         se effettuato correttamente il logout sia lato client che lato server.
     **/
    public function logout(): JSONResult {
        if (!session_start() || //avvio la sessione in modalità scrittura (di default è solo in lettura)
            !session_unset() || //svuoto i valori salvati  
            !session_destroy()) //elimino la sessione
            return JSONResult::createFAIL('SESSION NOT DELETED'); //errore cancellando la sessione lato server

        //!vedere documentazione per i dettagli, codice definito dagli standard PHP
        //elimino il cookie di sessione
        $params = session_get_cookie_params();
        $is_session_deleted = setcookie(
            session_name(),
            '',
            time() - 42000, //valore negativo arbitrario
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"] 
        );
        //!fine parte documentata
        

        if(!$is_session_deleted)
            return JSONResult::createERROR('SESSION COOKIE NOT DELETED');

        //provo a cancellare il cookie del token lato client
        $result_delete_cookie = $this->deleteCookieToken();
        if (!Result::isOK($result_delete_cookie))  //se qualcosa è andato storto, mostro l' errore
            return $result_delete_cookie;
        
        return JSONResult::createOK('LOGOUT');
    }
}