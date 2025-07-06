<?php
trait Cart
{
    /**
     * SERVIZIO che restituisce il carrello corrente dell' utente.
     * 
     * Il cart è salvato nel DB lato server, e perciò condiviso tra più dispositivi.
     *
     * @param string $user email dell' utente [optional, default:$SESSION['email']].
     * @return JSONResult
     *  - [ERROR, EMPTY]     se il carrello è vuoto.
     *  - [OK, CART, $table] restituisce un array di row, una per ogni prodotto diverso.
     */
    public function getCart(string $user = null): JSONResult
    {
        $user = $this->checkIdentityConsistency($user);

        $cart = $this->DB->executeQuery("get_cart", [$user]);

        if (empty($cart))
            return JSONResult::createERROR("EMPTY");

        return JSONResult::createOK("CART", $cart);
    }

    /**
     * SERVIZIO che permette di aggiungere/rimuovere dal carrello una quantità arbitraria di un prodotto.
     * 
     * Se la quantità da aggiungere è 0, allora invece rimuove l' elemento dal carrello.
     * 
     * @param int $id_product id del prodotto di cui vogliamo modificare la quantità nel carrello.
     * @param int $quantity quantità da aggiungere/rimuovere [optional, default:1].
     * @param string $user email dell' utente [optional, default:$SESSION['email']].
     * @return JSONResult
     *   - [FAIL, INVALID ID]             il prodotto non esiste
     *   - [ERROR, EXPIRED]               il prodotto non può più essere acquistato
     *   - [ERROR, NOT IN CART]           la quantity è negativa e il prodotto non è presente nel carrello
     *   - [ERROR,DECLINED BY DB,$errors] se non cè nel carrello e si passa come quantity 0
     *   - [OK, ADDED, $new_quantity]     aggiunto al carrello il prodotto
     *   - [OK, UPDATED, $new_quantity]   aggiornata la quantity nel carrello
     *   - [OK, DELETED, 0]               rimosso il prodotto dal carrello
     */
    public function modifyInCart(int $id_product, int $quantity = 1, string $user = null): JSONResult
    {
        $user = $this->checkIdentityConsistency($user);

        //controllo esista il prodotto chiamando un altro servizio
        if (!Result::isOK($result_check = $this->getProduct($id_product))) //o non esiste o è scaduto
            return $result_check;

        //inizio trnsizione
        $this->DB->beginTransaction();

        $quantity_in_cart = $this->DB->executeQuery("get_quantity_from_cart", [$user, $id_product]); //se non cè, è valutata 0

        //nuovo valore della tupla nel carrello
        $new_quantity = $quantity_in_cart + $quantity;
        //se non cé nel carrello
        if ($quantity_in_cart == 0)
            $result = $this->addInCart($user, $id_product, $quantity);
        else if ($new_quantity <= 0 || $quantity == 0)  //se quantity è 0, allora lo rimuovo
            $result = $this->deleteInCart($user, $id_product);
        else
            $result = $this->updateInCArt($user, $id_product, $new_quantity);

        $this->DB->commit();
        //fine transizione

        return $result;
    }

    /*** DEFINISCO METODI PROTECTED ***/
    /* sono chiamati da modifyInCart per inserire/modificare/rimuovere elementdi dal carrello */
    protected function addInCart(string $user, int $id_product, int $quantity): JSONResult
    {
        if ($quantity < 0)
            return JSONResult::createError("NOT IN CART");

        $this->DB->evaluateCheckers(
            ['quantity',"quantity", $quantity]
        );

        $this->DB->executeQuery("insert_into_cart", [$user, $id_product, $quantity], true);
        return JSONResult::createOK("ADDED TO CART", $quantity);
    }

    protected function updateInCart(string $user, int $id_product, int $new_quantity): JSONResult
    {

        $this->DB->evaluateCheckers(
            ['quantity', "quantity", $new_quantity]
        );


        $this->DB->executeQuery("update_cart", [$new_quantity, $user, $id_product], true);
        return JSONResult::createOK("UPDATED", $new_quantity);
    }

    protected function deleteInCart(string $user, int $id_product): JSONResult
    {
        $this->DB->executeQuery("remove_from_cart", [$user, $id_product], true);
        return JSONResult::createOK("DELETED FROM CART", 0);
    }
}
