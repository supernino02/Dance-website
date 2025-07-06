<?php
//open
trait Registration
{

    /**
     * SERVIZIO che crea un nuovo utente, indicando le informazioni obbligatorie e opzionali.
     * 
     * In caso di errore viene ritornato un array associativo.
     * Ogni campo inadeguato è associato a una breve descrizione del problema.
     * 
     * @param string $name nome del nuovo utente.
     * @param string $surname cognome del nuovo utente.
     * @param string $email email del nuovo utente; è necessario sia univoca.
     * @param string $password password del nuovo utente.
     * @param string $phone_number numero di telefono del nuovo utente [optional,DEFAULT:null].
     * @param string $fiscal_code codice fiscale del nuovo utente [optional,DEFAULT:null].
     * @param string $gender genere identificativo del nuovo utente [optional,DEFAULT:null].
     * @param bool $do_login flag che se settata, dopo la registrazione effettua il login per la sessione corrente [optional,DEFAULT:false].
     * 
     * @return JSONResult
     *   - [ERROR, DECLINED BY DB,$errors] se alcuni campi non avevano valori adeguati; $errors è un array associativo che descrive i problemi .
     *   - [OK, CREATED, $row]             se tutto è andato a buon fine; $row descrive brevemente l' utente appena creato.
     *  
     */
    public function signUp(string $name, string $surname, string $email, string $password, string $phone_number = null, string $fiscal_code = null, string $gender = null, bool $do_login = false): JSONResult
    {
        //aggiusto i valori nei campi
        $name = ucfirst(trim($name));
        $surname = ucfirst(trim($surname));

        //verifico che i campi siano validi
        $this->DB->evaluateCheckers(
            ['name', "name", $name],
            ['surname', "surname", $surname],
            ['email', "email", $email],
            ['password', "raw_password", $password],
            ['phone_number', "phone_number", $phone_number],
            ['fiscal_code', "fiscal_code", $fiscal_code],
            ['gender', "gender", $gender]
        );

        //creo la hash
        $hash = password_hash($password, PASSWORD_DEFAULT);

        //faccio la richiesta, nel caso l' eccezione sará gestita dall' handler (se trigger automatici)
        $this->DB->executeQuery("create_user", [$name, $surname, $email, $hash, $phone_number, $fiscal_code, $gender]);

        if ($do_login) $this->doLogin($email);
        //se arrivo qui é tutto ok, rimando indietro delle informazioni su che utente ho creato
        return JSONResult::createOK("CREATED", ["email" => $email, "name" => $name, "surname" => $surname]);
    }

    /**
     * SERVIZIO che verifica che un indirizzo email sia valido e non ancora utilizzato nel DB. 
     * 
     * @param string $email stringa di cui confermarne la validità.
     * 
     * @return JSONResult
     *   - [ERROR, DECLINED BY DB,{email=>$error}] se la mail non è valida oppure in uso. $error descrive il problema.
     *   - [OK, UNUSED]                            se la mail è valida e non ancora in uso.
     *  
     */
    public function checkValidEmail(string $email): JSONResult
    {
        //verifico sia valida
        $this->DB->evaluateCheckers(
            ['email', "email", $email]
        );

        return JSONResult::createOK("UNUSED");
    }
}
