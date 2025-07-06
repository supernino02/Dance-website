<?php
//Admin

trait PrivilegesManagement {

    /**
     * SERVIZIO CHE permette di "droppare" momentaneamente i privilegi da admin per sembrare un user
     * 
     * @return JSONResult
     *  - [OK,PERMISSION DROPPED] se nella sessione corrente vieni autenticato con privilegio user.
     */
    public function simulateBeingUser(): JSONResult
    {
        $this->doLogin($this->user_logged);
        return JSONResult::createOK("PERMISSION DROPPED");
    }

    /**
     * SERVIZIO che permette a di far diventare ADMIN un utente.
     * 
     * @param string $user_target utente che ricerverà i privilegi.
     * @return JSONResult
     *  - [FAIL,INVALID USER]       se l' utente non è presente nel database.
     *  - [ERROR,USER ALREDY ADMIN] se l' utente ha già i privilegi di admin.
     *  - [OK,PRIVILEGES GIVEN]     se l' utente ha ricevuto i privilegi di admin.
     */
    public function giveAdminPrivileges(string $user_target): JSONResult {
        $new_role = 'admin';
        //verifico esista
        if (!$user_row = $this->DB->executeQuery("get_user_info", [$user_target]))
            return JSONResult::createFAIL('INVALID USER');
        
        //verifico non sia già un admin
        if ($user_row['role'] == $new_role)
            return JSONResult::createERROR('USER ALREDY ADMIN');

        //aggiorno i privilegi
        $this->DB->executeQuery("change_privileges",[$new_role, $user_target],true);

        return JSONResult::createOK("PRIVILEGES GIVEN", $new_role);
    }
}

