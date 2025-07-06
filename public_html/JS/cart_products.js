function checkDiscount(product) {
    if (!product.discount) return product.total_price + "€";
    var final_price = product.total_price - (product.total_price * product.discount / 100);
    return final_price + "€ <span class='text-muted text-decoration-line-through'>" + product.total_price + "€</span>";
}

function uploadCart() {

    $productsContainer = $('#productsContainer').empty().append('<h3 class="mb-5 pt-2 text-center fw-bold">I Tuoi Prodotti</h3>');

    callService('getCart', [], function (response) {
        subtotal = 0;
        discount = 0;
        total = 0;

        response.value.forEach(function (product) {
            var exp_string = checkExpiration(product.expiration_date);
            var exp_full_string = exp_string ? `<h6 class="float-start cart-item-expiration-date text-danger">` + checkExpiration(product.expiration_date) + `</h6>` : "";

            $item = $(`<div data-id_product="`+ product.id_product + `" class="cart-product d-flex flex-column flex-md-row align-items-center mb-5">
                <div class="flex-shrink-0 mb-3 mb-md-0">
                    <a href="product.php?id=`+ product.id_product + `">
                        <img src="`+ product.poster_path + `"
                            class="img-fluid" alt="`+ product.name + `" style="width: 150px;">
                    </a>
                </div>
                <div data-id_product="`+ product.id_product + `"class="flex-grow-1 ms-md-3 text-center text-md-start">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="product.php?id=`+ product.id_product + `"><h5 class="text-start">` + product.name + `</h5></a>` +
                            exp_full_string +
                        `</div>
                            <a class="float-end cart-item-x-sign">
                                <i data-id_product="`+ product.id_product + `" class="fas fa-times fa-lg mx-2 ms-4"></i>
                            </a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <p data-total_price="`+ product.total_price + `" data-discount="`+ product.discount + `" class="price fw-bold mb-0 me-5 pe-3">`+ checkDiscount(product) + `</p>
                        <div class="d-flex align-items-center">
                            <button data-id_product="`+ product.id_product + `" class="lessBtn btn btn-outline-primary btn-sm me-2" type="button">-</button>
                            <input type="number" tabindex="-1" data-id_product="`+ product.id_product + `" class="quantityField form-control form-control-sm text-center" min="0" value="` + product.quantity + `" readonly>
                            <button data-id_product="`+ product.id_product + `" class="moreBtn btn btn-outline-primary btn-sm ms-2" type="button">+</button>
                        </div>
                    </div>
                </div>
            </div>`);

            $productsContainer.append($item);
        });

        reloadTotal();
        uploadButtons(); // aggiungo i listener ai bottoni

    }, function (response) { // ERROR (empty)
        //svuota il cart e disabilita il bottone
        cartEmpty('Il carrello è vuoto');
    }, function (response) { //FAIL 
        cartEmpty('Errore nel caricamento');
        createToast(response.result, "Errore nel caricamento del carrello", response.additional_info);
    });

}

function reloadTotal() {
    var subtotal = 0;
    var discount = 0;
    var total = 0;

    // Per ogni div del carrello
    $('#productsContainer .cart-product').each(function () {
        var quantity = $(this).find('.quantityField').val();
        var total_price = $(this).find('.price').data('total_price');
        var prod_discount = $(this).find('.price').data('discount');

        // Calcolo il prezzo totale del prodotto
        var rowPrice = total_price * quantity;
        subtotal += rowPrice;
        discount += rowPrice * prod_discount / 100;
        total += rowPrice - (rowPrice * prod_discount / 100);
    });

    $('#subtotal').empty().text(subtotal + "€");
    $('#discount').empty().text(discount + "€");
    $('#total').empty().text(total + "€");
}

function uploadButtons() {

    //RIMUOVO l'emento dal carrello
    $('#productsContainer .fa-times').click(function () {
        var $btn = $(this).prop("disabled",true);
        var id_product = $(this).data("id_product");
        callService('modifyInCart', [id_product, 0],
        function (response) {
            //cancello il prodotto
            removeProduct(id_product);
            decrementCartBadge();
        },function (response) { //ERROR
            if (response.additional_info == "EXPIRED") 
                createToast(response.result, "Prodotto rimosso dal carrello", "Il prodotto non é piú acquistabile");
            else 
                createToast(response.result, "Prodotto rimosso dal carrello", response.additional_info);
            removeProduct(id_product);
        },function(response) { //FAIL
            createToast(response.result, "Errore nella rimozione dal carrello", response.additional_info);
            $btn.prop("disabled", false);
        }
    );
    });

    //DECREMENTO l'elemento nel carrello
    $('#productsContainer .lessBtn').click(function () {
        var $btn = $(this).prop("disabled", true);
        var id_product = $(this).data("id_product");
        $input = $(this).siblings('.quantityField');
        callService('modifyInCart', [id_product, -1],
        function (response) {
            if (response.value == 0) {//se l' ha rimosso
                removeProduct(id_product);
                //decremnto di 1 il totale di elementi nel carrello
                decrementCartBadge();
            } else {
                //metto il valore restituito
                $input.val(response.value);
                $btn.prop("disabled", false);
            }
            reloadTotal();

        }, function (response) { //ERROR
            if (response.additional_info == "EXPIRED")
                createToast(response.result, "Prodotto rimosso dal carrello", "Il prodotto non é piú acquistabile");
            else
                createToast(response.result, "Prodotto rimosso dal carrello", response.additional_info);
            removeProduct(id_product);
        }, function (response) { //FAIL
            createToast(response.result, "Errore nella rimozione dal carrello", response.additional_info);
            $btn.prop("disabled", false);
        }
        );
    });

    //INCREMENTO l'elemento nel carrello
    $('#productsContainer .moreBtn').click(function () {
        var $btn = $(this).prop("disabled", true);
        var id_product = $(this).data("id_product");
        $input = $(this).siblings('.quantityField');
        callService('modifyInCart', [id_product, 1], 
        function (response) {
            //aggiorno, metto il valore restituito
            $input.val(response.value);
            $btn.prop("disabled", false);
            reloadTotal();
        },function (response) {
            if (response.additional_info == "EXPIRED")
                createToast(response.result, "Prodotto rimosso dal carrello", "Il prodotto non é piú acquistabile");
            $btn.prop("disabled", false);
        }, function (response) { //FAIL
            createToast(response.result, "Errore nella gestione del carrello", response.additional_info);
            $btn.prop("disabled", false);
        });
    });
}

function removeProduct(id_product) {
    var $element = $('.cart-product[data-id_product="' + id_product + '"]');

    // Applicare un effetto di dissolvenza e poi rimuovere l'elemento
    $element.fadeOut(400, function () {
        $(this).remove();
        // Controlla se ci sono solo il titolo e nessun altro prodotto
        if ($('#productsContainer').children().length === 1)
            // Se il carrello è vuoto, chiama la funzione cartEmpty
            cartEmpty('Il carrello è vuoto');
    });

    reloadTotal();

}

function decrementCartBadge() {
    // Estrae il valore corrente del badge come numero intero
    var currentValue = parseInt($("#cart-badge").text(), 10);

    // Decrementa il valore
    var newval = currentValue - 1;

    // Controlla se il nuovo valore è maggiore di 0
    if (newval > 0)
        // Aggiorna il testo del badge con il nuovo valore
        $("#cart-badge").text(newval);
    else
        // Nascondo il tag
        $('#cart-badge').hide();
}

function cartEmpty(message) {
    //lascio h3,rimuovo il resto
    $('#productsContainer').children(':not(h3)').remove();
    $('#productsContainer').append('<h5 id="cartSubtitle" class="text-center">' + message + '</h5>');
    $btnDescription = 'Carrello vuoto';

    //aggiorno il bottone
    $('#buyBtn')
        .addClass('btn-secondary')
        .removeClass('btn-purple')
        .attr('title', $btnDescription)
        .attr('type', 'button')
        .tooltip({
            show: { effect: "fadeIn", duration: 300 }, // Effetto di visualizzazione
            hide: { effect: "fadeOut", duration: 300 }  // Effetto di nascondimento
        });

    $('#subtotal').empty().text(0);
    $('#discount').empty().text(0);
    $('#total').empty().text(0);
}

//carico il carrello
$(document).ready(function () {
    uploadCart();
});
