<?php
//Open
trait Product
{
    /**
     * SERVIZIO che dato un id di un prodotto, restituisce la relativa tupla che lo descrive. 
     * 
     * Nel caso in cui il prodotto sia scaduto, viene restituito un ERROR.
     * Se indicato, restituisce anche una tabella che descrive i file "pubblici"associati, ovvero la sua vetrina.
     *
     * @param int $id_product codice univoco del prodotto.
     * @param bool $get_public_files flag che indica se si vogliono anche i file pubblici associati al prodotto [optional,DEFAULT = false].
     * @return JSONResult
     *   - [FAIL, INVALID ID]         se non esiste prodotto associato
     *   - [ERROR, EXPIRED, $product] se il prodotto esiste, ma non è più acquistabile. $product è un array associativo che lo descrive.
     *   - [OK, PRODUCT, $product]    se il prodotto esiste ed è acquistabile. $product è un array associativo che lo descrive.
     */
    public function getProduct(int $id_product, bool $get_public_files = false): JSONResult
    {
        //non esiste
        if (is_null($product = $this->DB->executeQuery("get_product", [$id_product])))
            return JSONResult::createFAIL("INVALID ID");
        
        //se richiesto, aggiungo le informazioni per i file di vetrina
        if ($get_public_files) 
        $product['public_files'] = $this->DB->executeQuery("get_public_files", [$id_product]);

        //se non puó piú essere acquistato, error
        if (!$this->DB->executeQuery("check_product_not_expired", [$id_product]))
            return JSONResult::createError("EXPIRED", $product);
    
        return JSONResult::createOK("PRODUCT", $product);
    }

    /**
     * SERVIZIO che restituisce la TOP dei prodotti ACQUISTABILI migliori, prendendo in considerazione il numero delle vendite.
     * 
     * Il parametro filter permette di limitare a una categoria la ricerca dei migliori prodotti.
     * Se indicato, restituisce anche una tabella che descrive i file "pubblici" associati, ovvero la sua vetrina.
     * Se richiesto un solo elemento, è ritornato sottoforma di $row (e non tabella mono riga).
     *
     * @param int $total_items numero di prodotti da restituire [optional,default:3]
     * @param bool $get_public_files flag che indica se si vogliono anche i file pubblici associati al prodotto [optional,DEFAULT:false].
     * @return JSONResult
     *  - [FAIL, INVALID FILTER]      se $filter non è tra ['ALL','COURSES','EVENTS'].
     *  - [FAIL, INVALID NUMBER]      se il $total_item è non positivo.
     *  - [ERROR, EMPTY]              se la ricerca non trova prodotti.
     *  - [ERROR, NOT ENOUGH, $table] se non sono stati trovati abbastanza prodotti. $table è una array di tuple.
     *  - [OK, PRODUCT,$product]      se viene richiesto un solo prodotto. $product è un array associativo che lo descrive.
     *  - [OK, PRODUCTS, $table]      dove $table è un' array di tuple, lungo $total_items.
     */
    public function getBestSellingProducts(int $total_items = 3, string $filter = 'ALL',bool $get_public_files = false): JSONResult
    {
        //se ne richiedo un numero invalido di item
        if ($total_items <= 0)
            return JSONResult::createFAIL("INVALID NUMBER");

        //la procedure accetta solo questi 3 filtri (esclude le altre tuple)
        if (!in_array($filter,['ALL','COURSES','EVENTS']))
            JSONResult::createFAIL("INVALID FILTER");

        //se non trovo alcun prodotto
        $products_ids = $this->DB->executeQuery("get_best_products_ids", [$total_items, $filter]);
        if (empty($products_ids))
            return JSONResult::createERROR("EMPTY");
        
        //se solo 1, ritorno singoletto
        if ($total_items == 1) {
            $id = $products_ids[0]['id_product'];
            return JSONResult::createOK("PRODUCT", $this->getProduct($id, $get_public_files)->getValue());
        }

        //per ogni ids, ottengo le info sul prodotto
        $table =[];
        foreach ($products_ids as $product)
            $table[] = $this->getProduct($product['id_product'], $get_public_files)->getValue();

        if (count($table) < $total_items)
            return JSONResult::createERROR("NOT ENOUGH", $table);
        
        return JSONResult::createOK("PRODUCTS", $table);
    }

    /**
     * SERVIZIO che restituisce gli eventi più prossimi ACQUISTABILI, prendendo in considerazione la data di scadenza.
     * 
     * @param int $total_items numero di prodotti da restituire [optional,DEFAULT:3].
     * 
     * @return JSONResult
     *  - [ERROR, EMPTY]        se non esiste alcun evento acquistabile.
     *  - [OK, PRODUCTS,$table] restituisce un array di tuple con i primi $n prodotti più prossimi.
     */
    public function getClosestEvents(int $total_items=3): JSONResult
    {
        $products = $this->DB->executeQuery("get_closest_events", [$total_items]);
        if (empty($products)) //se non ne trovo nessuno
            return JSONResult::createERROR("EMPTY");
        return JSONResult::createOK("PRODUCTS", $products);
    }

    /**
     * SERVIZIO restituisce tutti i corsi ACQUISTABILI di un certo livello, tipo e disciplina.
     * 
     * Nel caso in cui un filtro non sia indicato, non viene considerato nella condizione di ricerca.
     *
     * @param ?string $level      Filtra i risultati: sono restituiti tutti e soli i prodotti con $type presente in campo "type".             [optinal,default:null]
     * @param ?string $type       Filtra i risultati: sono restituiti tutti e soli i prodotti con $type presente in campo "type".             [optinal,default:null] 
     * @param ?string $discipline Filtra i risultati: sono restituiti tutti e soli i prodotti con $discipline presente in campo "discipline". [optinal,default:null] 
     * 
     * @return JSONResult
     *   - [OK, PRODUCTS,$products] restituisce un array di tuple con tutti i prodotti filtrati.
     *   - [ERROR, EMPTY]           se non esiste alcun prodotto che soddisfi i criteri di ricerca.
     */
    public function getPurchasablesCoursesFiltered(?string $level=null, ?string $type=null, ?string $discipline=null): JSONResult
    {
        //filtro i prodotti
        $products = $this->DB->executeQuery("get_filtered_courses",[$level, $level, $type, $type, $discipline, $discipline]);

        if (empty($products)) //se non ne trovo nessuno
            return JSONResult::createERROR("EMPTY");
        return JSONResult::createOK("COURSES", $products);
    }

    /**
     * SERVIZIO restituisce tutti gli EVENTI ACQUISTABILI di un certo livello, tipo e disciplina.
     * 
     * Nel caso in cui un filtro non sia indicato, non viene considerato nella condizione di ricerca.
     *
     * @param ?string $level      Filtra i risultati: sono restituiti tutti e soli i prodotti con $type presente in campo "type".             [optinal,default:null]
     * @param ?string $discipline Filtra i risultati: sono restituiti tutti e soli i prodotti con $discipline presente in campo "discipline". [optinal,default:null] 
     * 
     * @return JSONResult
     *   - [OK, PRODUCTS,$products] restituisce un array di tuple con tutti i prodotti filtrati.
     *   - [ERROR, EMPTY]           se non esiste alcun prodotto che soddisfi i criteri di ricerca.
     */
    public function getPurchasablesEventsFiltered(?string $level = null, ?string $discipline = null): JSONResult
    {
        //filtro i prodotti
        $products = $this->DB->executeQuery("get_filtered_events", [$level, $level, $discipline, $discipline]);

        if (empty($products)) //se non ne trovo nessuno
            return JSONResult::createERROR("EMPTY");
        return JSONResult::createOK("EVENTS", $products);
    }

    /**
     * SERVIZIO restituisce tutti i prodotti ACQUISTABILI che soddisfano una certa stringa.
     * 
     * Nel caso in cui il filtro non sia definito, allora vengono restituiti tutti i prodotti con data di scadenza nel futuro.
     *
     * @param ?string $filter Filtra i risultati: sono restituiti tutti e soli i prodotti con $filter presente in un qualsiasi campo. [optinal,default:null] 
     * 
     * @return JSONResult
     *   - [OK, PRODUCTS,$products] restituisce un array di tuple con tutti i prodotti.
     *   - [ERROR, EMPTY]           se non esiste alcun prodotto che soddisfi i criteri di ricerca.
     */
    public function searchPurchasableProducts(string $filter = null): JSONResult
    {
        $filter ??= ""; //se il filtro è null, lo rendo stringa vuota (modifica necessaria per comunicare col DB)

        $products = $this->DB->executeQuery("search_purchasable_products", [$filter]);//prodotti ottenuti
        if (empty($products)) //se non ne trovo nessuno
            return JSONResult::createERROR("EMPTY");

        //non c'era alcun filtro
        if ($filter == "")
            return JSONResult::createOK("FIND ALL", $products);
        else
            return JSONResult::createOK("FIND FILTERED", $products);
    }


    /**
     * SERVIZIO che dato un prodotto, restituisce i migliori prodotti a lui simili.
     * 
     * I prodotti "simili" sono quelli che hanno lo stesso tipo e la stessa disciplina del prodotto in input.
     * Nel caso in cui non ci fossero abbastanza prodotti correlati, vengono proposti i best seller.
     * 
     * @param int $id_product  prodotto preso come esempio.
     * @param int $total_items numero di prodotti correlati richiesti [optional,default:3].
     * @return JSONResult
     *  - [FAIL,INVALID NUMBER]       se $total_items è non positivo.
     *  - [ERROR, INCOMPLETE, $table] se non ci sono abbastanza prodotti. $table è un array di tuple.
     *  - [ERROR, EXTENDED, $table]   se ho alcuni prodotti che sono correlati e altri che sono best seller.  $table è un array di tuple.
     *  - [OK, RELATED, $table]       se ho $total_items prodotti correlati esistenti.  $table è un array di tuple.
     */
    public function getRelatedProducts(int $id_product, int $total_items = 3) 
    {
        //se ne richiedo un numero invalido di item
        if ($total_items <= 0)
            return JSONResult::createFAIL("INVALID NUMBER");

        //controllo il prodotto sia valido
        if (Result::isFAIL($result_product = $this->getProduct($id_product)))
            return $result_product;

        //ottengo N prodotti correlati (diversi da quello in input)
        $related_products = $this->DB->executeQuery('get_related_products',[$id_product, $total_items]);

        //conto quanti ne servono: se ne servono 0 restituisco
        if ($total_items == count($related_products))
            return JSONResult::createOK("RELATED",$related_products);

        //ottengo N migliori best-seller
        if (Result::isFAIL($best_selling_result = $this->getBestSellingProducts($total_items+1)))
            //se la chiamata ha fatto fail, allora notifico che non bastano i prodotti
            return JSONResult::createERROR("INCOMPLETE", $related_products);

        // Estrai i best-seller
        $best_products = $best_selling_result->getValue();

        // Filtro dai best-seller il prodotto di input e i prodotti già inclusi nei correlati
        $best_products = array_filter($best_products, function ($product) use ($id_product, $related_products) {
            foreach ($related_products as $related) {
                if ($product['id_product'] == $related['id_product']) {
                    return false; // Escludi se il prodotto è già nei correlati
                }
            }
            return $product['id_product'] != $id_product; // Escludi il prodotto originale
        });

        // Unisco i prodotti correlati e i best-seller
        $final_products = array_merge($related_products, $best_products);

        // Restituisco solo i primi N prodotti, dando precedenza ai correlati
        return JSONResult::createOK("EXTENDED", array_slice($final_products, 0, $total_items));     
        
    }
}