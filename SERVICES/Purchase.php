<?php
//user
trait Purchase
{

    /**
     * SERVIZIO che crea un acquisto con associato solamente il prodotto indicato.
     * 
     * @param int $id_product id del prodotto da acquistare.
     * @param int $quantity quantità da acquistare [optional, default:1].
     * @param string $user email dell' utente [optional, default:$SESSION['email']].
     * @return JSONResult
     *   - [FAIL, INVALID ID]              se il prodotto non esiste.
     *   - [ERROR, EXPIRED]                se il prodotto non si può più acquistare.
     *   - [ERROR, DECLINED BY DB,$errors] se alcuni campi non avevano valori adeguati; $errors descrive i problemi.
     *   - [OK, PURCHASED, $id_purchase]   se l' acquisto è andato a buon fine.
     *  
     */
    public function purchaseProduct(int $id_product, int $quantity = 1, string $user = null): JSONResult
    {
        $user = $this->checkIdentityConsistency($user); //controllo che l' user sia valido; se è null lo inizializzo

        //chiamo il servizio per verificare ci sia il prodotto
        $get_product = $this->getProduct($id_product);
        if (!Result::isOK($get_product)) //o non esiste o è scaduto, errore/fail
            return $get_product;

        //ottengo i dati del prodotto
        $product = $get_product->getValue();

        //calcolo il prezzo scontato
        $unitary_price = $this->evaluateDiscount($product['total_price'], $product['discount']);
        $total_price = $unitary_price * $quantity;

        //verifico che i campi siano validi
        $this->DB->evaluateCheckers(
            ['total_price', "price", $total_price],
            ['quantity', "quantity", $quantity],
            ['unitary_price', "price", $unitary_price]
        );


        //gestisco tutto in modo atomico
        $this->DB->beginTransaction();

        $id_purchase = $this->DB->executeQuery("create_purchase", [$user, $total_price], true);

        $this->DB->executeQuery("add_product_purchased", [$id_purchase, $id_product, $quantity, $unitary_price], true);

        $this->DB->commit();
        return JSONResult::createOK("PURCHASED", $id_purchase);
    }


    /**
     * SERVIZIO che acquista tutto il carrello associato a un utente.
     * 
     * Si assume che i prodotti nel carrello siano acquistabili (cioè non expired), e l' acquisto viene fatto in modo atomico.
     * Nel caso qualche prodotto dia dei problemi, non viene creato l' acquisto.
     * Se tutto termina correttamente, il carrello viene svuotato.
     * 
     * @param string $user email dell' utente [optional, default:$SESSION['email']].
     * @return JSONResult
     *   - [ERROR, EMPTY]                se non sono presenti prodotti nel carrello.
     *   - [OK, PURCHASED, $id_purchase] se l' acquisto è andato a buon fine.
     */
    function purchaseCart(string $user = null): JSONResult
    {
        $user = $this->checkIdentityConsistency($user);

        //chiamo il servizio per ottenere il carrello
        $result_get_cart = $this->getCart($user);
        if (!Result::isOK($result_get_cart)) //se vuoto o altri problemi, error/fail
            return $result_get_cart;

        $total_price = 0.0;

        //gestisco tutto in modo atomico
        $this->DB->beginTransaction();
        //svuoto il carrello
        $this->DB->executeQuery("empty_cart", [$user], true);

        //creo l'aquisto (con costo iniziale 0)
        $id_purchase = $this->DB->executeQuery("create_purchase", [$user, 0.0], true);

        //aggiungo i prodotti (e incremento il costo)
        foreach ($result_get_cart->getValue() as $product) {
            //estraggo e elaboro le informazioni nel carrello
            $id_product = $product['id_product'];
            $unitary_price = $this->evaluateDiscount($product['total_price'], $product['discount']);
            $quantity = $product['quantity'];
            $total_price += $unitary_price * $quantity;

            //verifico che i campi siano validi
            $this->DB->evaluateCheckers(
                ['quantity', "quantity", $quantity],
                ['unitary_price', "price", $unitary_price]
            );

            $this->DB->executeQuery("add_product_purchased", [$id_purchase, $id_product, $quantity, $unitary_price], true);
        }

        //verifico che i campi siano validi
        $this->DB->evaluateCheckers(
            ['total_price', "price", $total_price]
        );

        $this->DB->executeQuery("update_purchase_total", [$total_price, $id_purchase], true);

        //se tutto ok
        $this->DB->commit();

        return JSONResult::createOK("PURCHASED", $id_purchase);
    }

    /**
     * Funzione che dato un prezzo e la sua percentuale di sconto, ritorna il prezzo aggiornato.
     * 
     * Lo sconto é una percentuale [0,100] dove 0 indica che non cé sconto e 100 che il prodotto é gratis.
     * 
     * @param float $price
     * @param float $perc_discount
     * @throws InvalidArgumentException
     * @return float
     */
    protected function evaluateDiscount(float $price, float $perc_discount): float
    {
        if ($perc_discount < 0 || $perc_discount > 100)
            throw new InvalidArgumentException("invalid discount percentage: {$perc_discount}%");
        return $price * (100 - $perc_discount) / 100.0;
    }

    /**
     * SERVIZIO che ritorna tutti gli id degli acquisti che ha effettuato un l' utente.
     * 
     * @param string $user email dell' utente [optional, default:$SESSION['email']].
     * @return JSONResult
     *  - [ERROR, EMPTY]           se  non sono stati effettuati degli acquisti. 
     *  - [OK, PURCHASE IDS, $ids] se sono stati effettuati degli acquisti. $ids è un array di interi.
     */
    public function getAllPurchaseIds(string $user = null): JSONResult
    {
        $user = $this->checkIdentityConsistency($user);

        if ($ids = $this->DB->executeQuery('get_all_purchase_ids', [$user]))
            return JSONResult::createOK('PURCHASE IDS', $ids);
        else
            return JSONResult::createError('EMPTY');
    }

    /**
     * SERVIZIO che restituisce tutte le informazioni relative a un acquisto di un utente.
     * 
     * @param int $id_purchase id dell'acquisto.
     * @param string $user email dell' utente [optional, default:$SESSION['email']].
     * @return JSONResult
     *  - [FAIL,INVALID ID PURCHASE]          se l' acquisto non esiste o non è associato a $user.
     *  - [OK, PURCHASE INFO, $purchase_info] se l' acquisto è stato effettuato da $user.
     */
    public function getPurchaseInfo(int $id_purchase, string $user = null): JSONResult
    {
        $user = $this->checkIdentityConsistency($user);

        //ottengo le informazioni sull' acquisto effettuato
        if (!$purchase_info = $this->DB->executeQuery('get_purchase_info', [$id_purchase, $user]))
            return JSONResult::createFail('INVALID ID PURCHASE');

        //ci aggiungo le informazioni sui prodotti associati
        $purchase_info['products'] = $this->DB->executeQuery('get_products_purchased', [$id_purchase], true); //se me ne ritorna 0 è un problema, cè sempre almeno un prodotto

        //restituisco 
        return JSONResult::createOK('PURCHASE INFO', $purchase_info);
    }

    /**
     * SERVIZIO che restituisce un file (usato come scontrino) per un acquisto effettuato da $user. 
     * 
     * @param int $id_purchase id dell'acquisto.
     * @param string $user email dell' utente [optional, default:$SESSION['email']].
     * @return JSONResult
     *  - [FAIL,INVALID ID PURCHASE] se l' acquisto non esiste o non è associato a $user.
     *  - FILE Ricevuta.html         se il file è stato inviato correttamente.
     */
    public function downloadReceipt(int $id_purchase, string $user = null): Result
    {
        $user = $this->checkIdentityConsistency($user);

        //se l' acquisto non esiste/non appartiene a $user, notifico ed esco
        if (!Result::isOK($info_result = $this->getPurchaseInfo($id_purchase, $user)))
            return $info_result;

        //ottengo le informazioni di base sull' aquisto
        $purchase_info = $info_result->getValue();

        //aggiungo informazioni sull' utente
        $purchase_info['user'] = $this->getUserInfo($user)->getValue();

        //aggiungo le informazioni, per ogni prodotto associato
        foreach ($purchase_info['products'] as &$product) {
            $product_details = $this->getProduct($product['id_product'])->getValue();
            //sostituisco l' id con un oggetto che descrive il prodotto
            unset($product['id_product']);
            $product = array_merge($product, ['product' => $product_details]);
        }

        //creo il file temporaneo
        $sub_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid();
        $path_html =  "{$sub_path}.html";

        //salvo in un file la ricevuta ottenuta
        file_put_contents($path_html, $this->generateReceiptHtml($purchase_info));

        return TEMPORARY_FILEResult::create($path_html, 'Ricevuta');
    }

    /***FUNZIONE PROTECTED CHE CREA LA RICEVUTA***/
    protected function generateReceiptHtml($receipt)
    {
        //variabili necessarie per la corretta formattazione della ricevuta
        $user = $receipt['user']['email'];
        $nome = $receipt['user']['name'];
        $cognome = $receipt['user']['surname'];
        $date_time = $receipt['date_time'];
        $total_price = $receipt['total_price'];
        $products = $receipt['products'];

        //bufferizzo un include per ottenere un file in cui il php é valutato
        //salco in variabile la pagina inclusa
        ob_start();
        require_once absolutePath("PHP_COMPONENTS/ricevuta.php");

        return ob_get_clean();
    }
}
