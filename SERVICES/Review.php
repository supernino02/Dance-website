<?php

/*Open */
trait ReviewPublic
{
  /**
   * SERVIZIO che dato un id di un prodotto, restituisce le review relative ad esso.   * 
   *
   * @param int $id_product codice univoco del prodotto.
   * @return JSONResult
   *   - [FAIL, INVALID ID]      se non esiste prodotto associato
   *   - [ERROR, EMPTY]          se il prodotto esiste, ma non ha recensioni.
   *   - [OK, REVIEWS, $reviews] se il prodotto esiste ed ha recensioni. $reviews è un array di tuple.
   */
  public function getAllProductReviews(int $id_product): JSONResult
  {
    // controllo che il prodotto esista
    if (is_null($this->DB->executeQuery("get_product", [$id_product])))
      return JSONResult::createFAIL("INVALID ID");

    // ottengo le review
    $reviews = $this->DB->executeQuery("get_product_reviews", [$id_product]);

    // se non ne trovo nessuna
    if (empty($reviews))
      return JSONResult::createERROR("EMPTY");

    return JSONResult::createOK("REVIEWS", $reviews);
  }
}

//User
trait ReviewPrivate
{

  /**
   * SERVIZIO che restituisce la review effettuata sa $user su $id_product.
   * 
   * Ogni utente può effettuare solamente una review per prodotto, indipendentemente da quante copie ne acquista.
   * Nel caso in cui la review non sia stata effettuate, restituisce un $id_purchase valido per poterne effettuare una.
   * 
   * @param int $id_product id del prodotto.
   * @param string $user email dell' utente [optional, default:$SESSION['email']].
   * @return JSONResult
   *  - [FAIL, INVALID ID]               se il prodotto non esiste.
   *  - [FAIL, NOT PURCHASED]            se il prodotto non è stato mai acquistato da $user.
   *  - [ERROR, NO REVIEW, $id_purchase] se il prodotto non è mai stato recensito. $id_purchase è un acquisto che ne permette la recensione.
   *  - [OK, EXISTING REVIEW, $review]   se la review esiste. $review è una tupla che la descrive.
   */
  public function getPersonalReview(int $id_product, string $user = null): JSONResult
  {
    $user = $this->checkIdentityConsistency($user); // controllo che l'utente sia valido

    // controllo che il prodotto esista
    if (!$this->DB->executeQuery("get_product", [$id_product]))
      return JSONResult::createFAIL("INVALID ID");

    // controllo che l'utente abbia acquistato il prodotto
    if (!$id_purchase = $this->DB->executeQuery("check_is_purchased", [$id_product, $user]))
      return JSONResult::createERROR("NOT PURCHASED");

    if (!$review = $this->DB->executeQuery("get_review", [$id_product, $user]))
      return JSONResult::createERROR("NO REVIEW", $id_purchase);

    return JSONResult::createOK("EXISTING REVIEW", $review);
  }

  /**
   * SERVIZIO che dato un di un prodotto, una star evaluation e una nota, registra una recensione del prodotto.
   * 
   *
   * @param int $id_product codice univoco del prodotto.
   * @param float $star_evaluation valore stelle da attribuire alla recensione.
   * @param string $note commento (corpo) della recensione.
   * @param string $user email dell' utente [optional, default:$SESSION['email']].
   * @return JSONResult
   *  - [FAIL, INVALID ID]              se il prodotto non esiste.
   *  - [FAIL, NOT PURCHASED]           se il prodotto non è stato mai acquistato da $user.
   *  - [FAIL, EXISTING REVIEW]         se il prodotto è già stato recensito da $user.
   *  - [ERROR, DECLINED BY DB,$errors] se alcuni campi non avevano valori adeguati; $errors è un array associativo che descrive i problemi .
   *  - [OK, REVIEWED,$review]          se la recensione è stata registrata correttamente. $review é la recensione appena inserita.
   */
  public function createProductReview(int $id_product, float $star_evaluation, string $note = null, string $user = null): JSONResult
  {
    $user = $this->checkIdentityConsistency($user); // controllo che l'utente sia valido

    // controllo che l'utente possa recensire il prodotto
    if (!(JSONResult::isERROR($reviewable_result = $this->getPersonalReview($id_product, $user)) && $reviewable_result->getAdditionalInfo() == "NO REVIEW"))
      return JSONResult::createFAIL($reviewable_result->getAdditionalInfo());

    $id_purchase = $reviewable_result->getValue();

    //verifico sia un valore valido
    $this->DB->evaluateCheckers(
      ['star_evaluation', "star_evaluation", $star_evaluation]
    );

    // sanifico il commento
    $note = htmlspecialchars($note, ENT_QUOTES, 'UTF-8');

    // inserisco la recensione
    $this->DB->executeQuery("create_product_review", [$id_purchase, $id_product, $star_evaluation, $note], true);

    //ritorna la review appena inserita
    $review = $this->getPersonalReview($id_product, $user)->getValue();

    return JSONResult::createOK("REVIEWED",$review);
  }
}
