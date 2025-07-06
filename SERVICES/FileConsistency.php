<?php
//admin
trait FileConsistency {
    /**
     * SERVIZIO che verifica che ogni PATH indicato nel DB esista la controparte fisica.
     * 
     * Estrae tutti i path da diverse tabella nel DB, e per ognuno controlla esista tale file.
     * I prodotti acquistabili sono privati, mentre le foto pubbliche sono nella cartella MULTIMEDIA.
     *
     * @return JSONResult
     *   - [ERROR,DANGLING PATHS, $paths] dove $paths è un array di tuple che descrivono il file mancante.
     *   - [OK,CONSISTENCY]               se non sono rilevate inconsistenze.
     */
    public function verifyPathsConsistency(): JSONResult
    {
        $errors = [];
        //verifico i purchasables
        foreach ($this->DB->executeQuery("get_all_purchasables") as $row)
            if (!file_exists($row["path"] = PURCHASABLES_PATH . $row["path"]))
                $errors[] = $row;

        //verifico i poster
        foreach ($this->DB->executeQuery("get_all_posters") as $row)
            if (!file_exists($row["path"] = PUBLIC_PATH . $row["path"]))
                $errors[] = $row;

        //verifico i file pubblici
        foreach ($this->DB->executeQuery("get_all_public_files") as $row)
            if (!file_exists($row["path"] = PUBLIC_PATH . $row["path"]))
                $errors[] = $row;

        //verifico le icone dei tipi di corso
        foreach ($this->DB->executeQuery("get_all_product_types_icons") as $row)
            if (!file_exists($row["path"] = PUBLIC_PATH . $row["icon_path"]))
                $errors[] = $row;
        
        if (empty($errors)) return JSONResult::createOK("CONSISTENCY");
        else return JSONResult::createERROR("DANGLING PATHS", $errors);
    }
}