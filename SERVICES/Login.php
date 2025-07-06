<?php
//open
trait Login {
    /**
     * SERVIZIO che permette il login tramite utilizzo di un token.
     * 
     * Utilizzare il servizio getAllCookiesDescription() per ottenere descrizioni dettagliate sull' utilizzo del cookie.
     * Nel caso in cui il token non sia passato come parametro, viene i automatico preso dai cookie.
     * 
     * @param string $token il token da controllare [optional, default: $_COOKIE['token']].
     * @return JSONResult
     *   - [ERROR, NO TOKEN]       se non è stato indicato il token (nè come parametro nè come cookie).
     *   - [ERROR, INVALID]        se il token non è valido per il login.
     *   - [OK, LOGIN, $user_info] se è stato effettuato il login per la sessione corrente.
     */
    public function loginToken(string $token = null): JSONResult
    {
        //oggetto che gestisce i cookie
        $virtual_cookie = new CookieManager("REMEMBER_ME_TOKEN");
        
        //se non ho mandato un token e non ce neppure nei cookie
        if (!$token && !$token = $virtual_cookie->obtainCookie())
            return JSONResult::createError("NO TOKEN");

        //ottengo la row che descrive l' user
        $row = $this->DB->executeQuery("verify_token", [$token]);
        if (is_null($row)) //non ho trovato nel DB
            return JSONResult::createError("INVALID");

        //rimuovo le informazioni sulla password e salvo il resto in sessione
        unset($row['password']);
        $this->doLogin($row['email'], $row['role']);
        $row[$virtual_cookie->getName()] = $token; //in output aggiungo anche il token valutato
        
        return JSONResult::createOK("LOGIN", $row);
    }


    /**
     * SERVIZIO che permette il login tramite utilizzo di credenziali.
     * 
     * Verifica che le credenziali siano compatibili con un' utente registrato in precedenza.
     *
     * @param string $email dell'utente.
     * @param string $pwd   dell'utente.
     * @return JSONResult
     *   - [ERROR, WRONG]          se le credenziali di accesso sono errate.
     *   - [OK, LOGIN, $user_info] se è stato effettuato il login per la sessione corrente.
     */
    public function loginCredentials(string $email, string $password): JSONResult
    {
        //ottengo i dati dell' user
        $row = $this->DB->executeQuery("get_user_info", [$email]);

        /*si noti come si usi | poiche non vogliamo la logica cortocircuitale
            in questo modo valuto sempre l' hash e quindi evito TIMING ATTACK*/
        if (is_null($row)                                            //se non esiste utente associato
            | !password_verify($password, @$row['password'] ?? ""))  //se la password non coincide
            return JSONResult::createError("WRONG");

        //rimuovo le informazioni sulla password e salvo il resto in sessione
        unset($row['password']);
        $this->doLogin($row['email'], $row['role']);
        //effettuo il login
        return JSONResult::createOK("LOGIN", $row);
    }


    /**
     * METODO PROTECTED che effettua un autenticazione per la sessione corrente.
     * 
     * Si limita a salvare in sessione la mail dell' utente e il ruolo che ricopre.
     * 
     * 
     * @param string $email La email che definisce univocamente un utente.
     * @param string $role  Il ruolo con cui l' utente viene autenticato.
     */
    protected  function doLogin(string $email,string $role = 'user') : void
    {
        session_start();       //acquisisco il lock per scrivere sulla sessione
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $role;
        session_write_close(); //rilascio il lock per scrivere sulla sessione
    }    

}
