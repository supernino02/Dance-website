<?php
//User
trait PersonalArea
{
    /**
     * SERVIZIO che data la mail di un utente, restituisce le informazioni associate.
     *
     * @param string $user email dell' utente [optional,default: $SESSION['email'].
     * @return JSONResult
     *   - [OK, USER INFO,$user_info] restituisce un array associato che contiene le informazioni dell' utente.
     */
     public function getUserInfo(string $user=null): JSONResult
    {
        $user = $this->checkIdentityConsistency($user);

        //estraggo le informazioni associate all' utente
        $row = $this->DB->executeQuery("get_user_info", [$user], true);
        unset($row['password']);//rimuovo la password

        return JSONResult::createOK("USER INFO", $row);
    }

    /**
     * SERVIZIO che modifica la password associata a un utente.
     * 
     * @param string $new_password nuova password.
     * @param string $old_password password presente nel DB.
     * @param ?string $user email dell' utente [optional,default: $SESSION['email'].
     * @return JSONResult
     *   - [ERROR, WRONG PASSWORD]         se la vecchia password non è corretta. 
     *   - [ERROR, DECLINED BY DB, $error] se il nuovo valore non è valido, dove $error è singoletto {password=>$description}.
     *   - [OK, UPDATED]                   se la password è stato modificato correttamente.
     */
    public function updateUserPassword(string $new_password, string $old_password, string $user = null): JSONResult {
        $user = $this->checkIdentityConsistency($user);

        //verifico si conosca la password dell'utente
        $row = $this->DB->executeQuery("get_user_info", [$user],true);

        if (!password_verify($old_password, $row['password'])) //la password vecchia non coincide
            return JSONResult::createError("WRONG PASSWORD");

        //alaza eccezione nel caso
        $this->DB->evaluateCheckers(
            ['password', "raw_password", $new_password]
        );
        
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        $this->DB->executeQuery("update_user_password",[$hash, $user],true);
        
        return JSONResult::createOK("UPDATED");
    }

    /**
     * SERVIZIO che modifica il nome associato a un utente.
     * 
     * @param string $new_name nuovo nome da inserire nel DB.
     * @param ?string $user email dell' utente [optional, default: $SESSION['email']].
     * @return JSONResult
     *   - [ERROR, DECLINED BY DB, $error] se il nuovo valore non è valido, dove $error è singoletto {password=>$description}.
     *   - [OK, UPDATED]                   se il valore è stato modificato correttamente.
     */
    public function updateUserName(string $new_name, string $user = null): JSONResult
    {
        return $this->updateUserField('name', $new_name, $user);
    }

    /**
     * SERVIZIO che modifica il cognome associato a un utente.
     * 
     * @param string $new_surname nuovo cognome da inserire nel DB.
     * @param ?string $user email dell' utente [optional, default: $SESSION['email']].
     * @return JSONResult
     *   - [ERROR, DECLINED BY DB, $error] se il nuovo valore non è valido, dove $error è singoletto {password=>$description}.
     *   - [OK, UPDATED]                   se il valore è stato modificato correttamente.
     */
    public function updateUserSurname(string $new_surname, string $user = null): JSONResult
    {
        return $this->updateUserField('surname', $new_surname, $user);
    }

    /**
     * SERVIZIO che modifica il numero di telefono associato a un utente.
     * 
     * @param ?string $new_phone_number nuovo numero di telefono da inserire nel DB [optional].
     * @param ?string $user email dell' utente [optional, default: $SESSION['email']].
     * @return JSONResult
     *   - [ERROR, DECLINED BY DB, $error] se il nuovo valore non è valido, dove $error è singoletto {password=>$description}.
     *   - [OK, UPDATED]                   se il valore è stato modificato correttamente.
     */
    public function updateUserPhoneNumber(string $new_phone_number = null, string $user = null): JSONResult
    {
        return $this->updateUserField('phone_number', $new_phone_number, $user);
    }

    /**
     * SERVIZIO che modifica il codice fiscale associato a un utente.
     * 
     * @param ?string $new_fiscal_code nuovo codice fiscale da inserire nel DB [optional].
     * @param ?string $user email dell' utente [optional, default: $SESSION['email']].
     * @return JSONResult
     *   - [ERROR, DECLINED BY DB, $error] se il nuovo valore non è valido, dove $error è singoletto {password=>$description}.
     *   - [OK, UPDATED]                   se il valore è stato modificato correttamente.
     */
    public function updateUserFiscalCode(string $new_fiscal_code = null, string $user = null): JSONResult
    {
        return $this->updateUserField('fiscal_code', $new_fiscal_code, $user);
    }

    /**
     * SERVIZIO che modifica il genere associato a un utente.
     * 
     * @param ?string $new_gender nuovo genere da inserire nel DB [optional].
     * @param ?string $user email dell' utente [optional, default: $SESSION['email']].
     * @return JSONResult
     *   - [ERROR, DECLINED BY DB, $error] se il nuovo valore non è valido, dove $error è singoletto {password=>$description}.
     *   - [OK, UPDATED]                   se il valore è stato modificato correttamente.
     */
    public function updateUserGender(string $new_gender = null, string $user = null): JSONResult
    {
        return $this->updateUserField('gender', $new_gender, $user);
    }

    /**
     * METODO PROTECTED utilizzato come generalizzazione della modifica di un campo associato a un user
     * 
     * @param string $field campo da aggiornare (e.g., 'name', 'surname', 'phone_number', 'fiscal_code').
     * @param mixed $new_value nuovo valore da inserire nel DB.
     * @param ?string $user email dell'utente [optional, default: $SESSION['email']].
     * @return JSONResult
     *   - [ERROR, DECLINED BY DB, $error] se il nuovo valore non è valido, dove $error è singoletto {password=>$description}.
     *   - [OK, UPDATED]                   se il valore è stato modificato correttamente.
     */
    protected function updateUserField(string $field, string $new_value = null, string $user = null): JSONResult
    {
        $user = $this->checkIdentityConsistency($user);

        //chiamo il checker associato
        $this->DB->evaluateCheckers(
            [$field, $field, $new_value]
        );

        $this->DB->executeQuery("update_user_{$field}", [$new_value, $user]);
        return JSONResult::createOK("UPDATED");
    }
}