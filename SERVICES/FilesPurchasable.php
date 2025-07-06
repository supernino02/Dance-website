<?php
//user
trait FilesPurchasable
{
    /**
     * SERVIZIO che dato un prodotto e un numero di file privato relativo, restituisce il file come attachment.
     * 
     * Il prodotto deve essere stato acquistato almeno una volta da $user, altrimenti non è possbile scaricare il file.
     * Nel caso in cui il numero di file non sia indicato, restituisce il primo file relativo al prodotto indicato.
     * 
     * @param int $id_product Id del prodotto relativo al file.
     * @param int $n_file Numero di file che si vuole scaricare [optional, default:1].
     * @param string $user email dell' utente [optional, default:$SESSION['email']].
     * @return Result
     *  - [FAIL,NOT PURCHASED]          se il prodotto non è stato mai acquistato da $user.
     *  - [FAIL,INVALID FILE NUMBER]    se il prodotto è stato acquistato, ma il numero di file non esiste.
     *  - [FAIL,DOWNLOAD,$description]  se il prodotto è stato acquistato e il numero di file è corretto, ma non esiste il file.
     *  - [ERROR,DOWNLOAD,$description] se il prodotto è stato acquistato e il numero di file è corretto e esiste il file, ma non può essere letto.
     *  - FILE                          se tutti i controlli sono passati.
     */
    public function downloadPurchasables(int $id_product, int $n_file = 1, string $user = null): Result
    {
        $user = $this->checkIdentityConsistency($user); //controllo che l' user sia valido; se è null lo inizializzo

        //se il prodotto non è stato acquistato, non posso vedere quali file ha associato
        if (!$this->DB->executeQuery("check_is_purchased", [$id_product, $user]))
            return JSONResult::createFail("NOT PURCHASED");

        //ottengo il path dalla row indicata
        if ($path = $this->DB->executeQuery("get_purchasable_path", [$id_product, $n_file]))
            return $this->downloadFileContent(PURCHASABLES_PATH . $path);

        return JSONResult::createFail("INVALID FILE NUMBER");
    }

    /**
     * SERVIZIO che dato un prodotto, restituisce tutti i file a lui associati.
     * 
     * Il prodotto deve essere acquistato dal relativo $user per poter essere visualizzato.
     * Ritorna un elenco di tuple che associano un nome di file scaricabile a un numero.
     * 
     * @param int $id_product id del prodotto di cui si vogliono i file.
     * @param string $user email dell' utente [optional, default:$SESSION['email']].
     * @return JSONResult
     *  - [FAIL,NOT PURCHASED]        se il prodotto non è stato mai acquistato da $user.
     *  - [ERROR,NO FILES ASSOCIATED] se il prodotto è stato acquistato, ma non ci sono file associati.
     *  - [OK,FILES,$tables]          restituisce una tabella in cui ogni row rappresenta un file associato al prodotto.
     */
    public function getAllPurchasablesFiles(int $id_product, string $user = null): JSONResult
    {
        $user = $this->checkIdentityConsistency($user); //controllo che l' user sia valido; se è null lo inizializzo

        //se il prodotto non è stato acquistato, non posso vedere quali file ha associato
        if (!$this->DB->executeQuery("check_is_purchased", [$id_product, $user]))
            return JSONResult::createFail("NOT PURCHASED");

        //ottengo tutti i file acquistabili relativi al prodotto
        if ($paths_table = $this->DB->executeQuery("get_purchasable_files", [$id_product]))
            return JSONResult::createOK("FILES", $paths_table);
        else
            return JSONResult::createERROR("NO FILES ASSOCIATED");
    }


    /**
     * SERVIZIO che dato un prosotto, restituisce un attachment con il file .zip che comprende tutti i file associati al prodotto. 
     * 
     * @param int $id_product id di cui si vogliono scaricare tutti i file.
     * @param string $user email dell' utente [optional, default:$SESSION['email']].
     * @return Result
     *  - [FAIL,NOT PURCHASED]           se il prodotto non è stato mai acquistato da $user.
     *  - [FAIL,ERROR CREATING ZIP FILE] se il prodotto è stato acquistato e ci sono file associati, ma ci sono problemi creando il file zip.
     *  - [ERROR,NO FILES ASSOCIATED]    se il prodotto è stato acquistato, ma non ci sono file associati.
     *  - FILE .ZIP                      se tutti i controlli sono passati.
     */
    public function downloadZipPurchasables(int $id_product, string $user = null): Result
    {
        $user = $this->checkIdentityConsistency($user); //controllo che l' user sia valido; se è null lo inizializzo

        //se il prodotto non è stato acquistato, non posso vedere quali file ha associato
        if (!$this->DB->executeQuery("check_is_purchased", [$id_product, $user]))
            return JSONResult::createFail("NOT PURCHASED");

        //ottengo tutti i paths del prodotto
        if (empty($paths_table = $this->DB->executeQuery("get_purchasable_files_paths", [$id_product])))
            return JSONResult::createERROR("NO FILES ASSOCIATED");

        //creo il file zip nella cartella temporanea
        $identifier = uniqid();//utilizzo un uid per evitare problemi di concorrenza di file omonimi
        $zipFileName = "{$identifier}.zip";
        $zipFilePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zipFileName;

        // Creare un nuovo archivio zip
        $zip = new ZipArchive();
        if (!$zip->open($zipFilePath, ZipArchive::CREATE)) {
            log_error("Impossible creation of file {$zipFilePath}");
            return JSONResult::createFAIL("ERROR CREATING ZIP FILE", "something went wrong...");
        }

        // Aggiungere i file allo zip
        foreach ($paths_table as $row) {
            $file = PURCHASABLES_PATH . $row['path'];
            if (file_exists($file))
                $zip->addFile($file, basename($file));
            else {
                log_error("Impossible creation of file {$zipFilePath}, $file does not exists");
                return JSONResult::createFAIL("ERROR CREATING ZIP FILE", "something went wrong with {$file}");
            }
        }

        //ottengo il nome del prodotto; parametro opzionale true poiche richiedo esista
        $name_product = $this->DB->executeQuery("get_product", [$id_product], true)['name'];

        return TEMPORARY_FILEResult::create($zipFilePath, $name_product);
    }

    /**
     * METODO PROTECTED che dato un file, restituisce un JSONResult.
     * 
     * Il tipo é coerente con lo stato del file nel FILESYSTEM.
     * 
     * @param string $path il file richiesto
     * @return JSONResult 
     */
    protected function printFileContent(string $path): JSONResult
    {
        //verifico il file esista
        if (!file_exists($path))
            return JSONResult::createFail("NOT EXISTS", $path);

        //altrimenti, un result standard
        $content = file_get_contents($path);
        if ($content === false) //verifico sia letto correttamente
            return JSONResult::createFail("CANNOT READ", $path);

        return JSONResult::createOk("CONTENT", $content);
    }

    /**
     * METODO PROTECTED che dato un file, restituisce un FILEResult.
     * 
     * Il tipo é coerente con lo stato del file nel FILESYSTEM.
     * 
     * @param string $path il file richiesto
     * @return FILEResult
     */
    protected function downloadFileContent(string $path): FILEResult
    {
        //verifico il file esista
        if (!file_exists($path))
            return FILEResult::createFail($path);

        //verifico si possa leggere
        if (!is_readable($path))
            return FILEResult::createError($path);

        return FILEResult::createOk($path);
    }
}
