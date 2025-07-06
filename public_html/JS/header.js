//chiama il servizio chee esegue il logout
function doLogout(e) {
    e.preventDefault(); // Previene il comportamento predefinito del link

    callService("logout", [],
        function (response) { // OK , ridireziona
            window.location.href = "index.php";
        },
        function (response) { // ERROR, login effettuato lato server ma non lato client (rimangono i cookie)
            deleteCookie('PHPSESSID'); //rimuovo il cookie di sessione
            callService("forgetMe", []); //provo richiedere il servizio per rimuovere il token del rememberme
            window.location.href = "index.php";
        }
    );
}

// quando il tasto di ricerca viene premuto, appare in sovraimpressione la barra di ricerca
function showSearchBar() {
    $('#btn-search').on('click', function (event) {
        event.preventDefault();
        $('#searchOverlay').addClass('active');
        $('body').css('overflow', 'hidden'); // Blocca lo scorrimento del body
    });

    $('#closeSearch').on('click', function (event) {
        $('#searchInput').val(''); // TODO: rendi queste 3 una funzione? Si ripetono anche sotto
        $('#searchResults').hide(); // TODO: rendi queste 3 una funzione? Si ripetono anche sotto
        $('#searchOverlay').removeClass('active'); // TODO: rendi queste 3 una funzione? Si ripetono anche sotto
        $('body').css('overflow', ''); // Ripristina lo scorrimento del body
    });

    // Chiude la barra di ricerca quando si clicca fuori
    $('#searchOverlay').on('click', function (event) {
        if (event.target === this) {
            $('#searchInput').val('');
            $('#searchResults').hide();
            $('#searchOverlay').removeClass('active');
            $('body').css('overflow', ''); // Ripristina lo scorrimento del body
            this.classList.remove('active');
        }
    });
}

function searchProducts() {
    let $inputElement = $(this);
    let searchValue = $inputElement.val();
    let $searchResults = $('#searchResults');
    $searchResults.empty();

    // se è vuoto, non faccio nulla
    if (!searchValue) {
        $searchResults.hide();
        return;
    }
    $searchResults.show();

    // Invia ajax per cercare i prodotti
    callService("searchPurchasableProducts", [searchValue],
        function (response) { // OK
            response.value.slice(0, 10).forEach(function (product, index) {
                let $div = $('<div>').addClass('search-result-item d-flex align-items-center mb-3');
                let $a = $('<a>').attr('href', 'product.php?id=' + product.id_product).addClass('d-flex align-items-center');
                let $div2 = $('<div>').addClass('search-result-image me-3');
                let $img = $('<img>').attr('src', product.poster_path).attr('alt', product.name).addClass('img-fluid');
                let $h5 = $('<h5>').addClass('result-title mb-0').text(product.name);

                $div2.append($img);
                $div.append($div2, $h5);
                $a.append($div); // Aggiungi sia l'immagine che il titolo all'interno del link
                $searchResults.append($a);

                // Aggiungi un separatore tra i risultati
                if (index < response.value.length - 1) {
                    $searchResults.append('<hr class="my-2">');
                }
            });
        },
        function (response) { // ERROR
            let $div = $('<div class="alert alert-danger"></div>').text("Nessun elemento trovato");
            $searchResults.append($div);
        },function (response) { //FAIL
            $searchResults.empty();
            let $div = $('<div class="alert alert-danger"></div>').text("Impossibile seguire la ricerca dei prodotti");
            $searchResults.append($div);
        }
    );
}

//posso farlo solo dopo che si è caricato il carrello, se non aspetto
//salvo come variabile globale
var CURRENT_CART = [];
var LOADED_CART = false;
var PROMISES_CART; //per verificare il carrello, aspetto sia caricato

// Funzione per controllare se un prodotto è nel carrello
function isInCart(id_product) {
    if (!LOADED_CART) 
        // Se il carrello non è caricato, aspetta che la promessa sia risolta
        return $.when(PROMISES_CART).then(function () {
            return search_cart(id_product);
        })
    else 
        // Se il carrello è già caricato, cerca direttamente
        return Promise.resolve(search_cart(id_product));
}

// Funzione per cercare un prodotto nel carrello
function search_cart(id_product) {
    let quantity = 0;
    CURRENT_CART.forEach(product => {
        if (product.id_product === id_product) {
            quantity = product.quantity;
        }
    });
    return quantity;
}


$(document).ready(function () {
    // i bottoni nell'header utilizzano localstorage e ridirezionano alla pagina di ricerca dei prodotti
    $('.course-type-btn').on('click', function () {
        // Preleva il tipo di corso dal data attribute
        var courseType = $(this).data('filter');
        // Salva il tipo di corso nel localStorage
        resetFilters('Type', courseType);
    });

    // attivo la funzionalitá del bottone di logout
    $('#btn-logout').on('click', doLogout);

    // attivo la funzionalitá del bottone di ricerca
    $('#btn-search').on('click', function (event) {
        event.preventDefault();
        $('#searchOverlay').addClass('active');
        $('body').css('overflow', 'hidden'); // Blocca lo scorrimento del body
    });

    // chiudo la barra di ricerca al click su close
    $('#closeSearch').on('click', function () {
        $('#searchInput').val('');
        $('#searchResults').hide();
        $('#searchOverlay').removeClass('active');
        $('body').css('overflow', ''); // Ripristina lo scorrimento del body
    });

    // Chiude la barra di ricerca quando si clicca fuori
    $('#searchOverlay').on('click', function (event) {
        if (event.target === this) {
            $('#searchInput').val('');
            $('#searchResults').hide();
            $('#searchOverlay').removeClass('active');
            $('body').css('overflow', ''); // Ripristina lo scorrimento del body
        }
    });

    $('#searchInput').on('input', debounce(searchProducts, 500));

    //se sono loggato (esiste il div del badge) allora aggiorno il suo valore
    if ($("#cart-badge").length > 0)
        PROMISES_CART = callService(
            'getCart', [], function (response) { // OK
                CURRENT_CART = response.value;
                LOADED_CART = true;
                $('#cart-badge').text(CURRENT_CART.length);
                $('#cart-badge').show();
            }, function (response) { // ERROR (empty)
                $('#cart-badge').hide();
            }, function (response) {
                createToast(response.result, "Impossibile ottenere il carrello corrente", response.additional_info,0);
            }
        );

    //ATTIVO I TOGGLE CON INVIO 

    // Simula il click con la tastiera (Enter) sul bottone del menu mobile
    $('#navCollapsedIcon').on('keydown', function (event) {
        if (event.key === 'Enter' || event.keyCode === 13) {
            $(this).click(); // Simula il click
        }
    });

    // Cambia aria-expanded quando il menu è attivato
    $('#navCollapsedIcon').on('click', function () {
        var expanded = $(this).attr('aria-expanded') === 'true' ? 'false' : 'true';
        $(this).attr('aria-expanded', expanded);
        //metto il focus
        setTimeout(function () {
            $("#navmenu > ul").focus();
        }, 400);
    });


    // Simula il click con la tastiera (Enter) sul bottone dei corsi
    $('#coursesCollapseIcon').on('keydown', function (event) {
        if (event.key === 'Enter' || event.keyCode === 13) {
            $(this).click(); // Simula il click
            setTimeout(function () {
                $(this).next().focus();
            }, 400);
        }
    });

});