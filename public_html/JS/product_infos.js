function updateUrlId(newId) {
    // Crea un oggetto URL utilizzando l'URL corrente
    const url = new URL(window.location.href);

    // Ottieni i parametri di ricerca
    const params = url.searchParams;

    // Modifica il valore del parametro 'id'
    params.set('id', newId);

    // Ricostruisci l'URL con i nuovi parametri
    const newUrl = `${url.origin}${url.pathname}?${params.toString()}`;

    // Aggiorna l'URL del browser senza ricaricare la pagina
    window.history.pushState({}, '', newUrl);
}


function uploadProductDetails(product) {productDetails
    var $productDetails = $('#productDetails');


    var $h4 = $('#productDetails > h4').removeClass('placeholder col-8');
    $('#productDescription').removeClass('placeholder-glow').empty(); //RIMUOVO IL PLACEHOLDER

    //se è scaduto, lo segnalo
    if (!isPurchasable) 
        $h4.html("Prodotto Scaduto in data " + formatDate(product.expiration_date)).addClass("text-danger")
    else if (exp_str = checkExpiration(product.expiration_date))
        $h4.append(
            $("<span>").html(checkExpiration(product.expiration_date))
                .addClass("text-danger position-absolute end-0 px-2 rounded")   
        );
    //
    //DETTAGLI 
    var $row = $('<dl>').addClass('row bottom-line'); // Crea un container per la griglia

    // Calcola il prezzo scontato, se esiste
    if (product.discount) {
        var originalPrice = product.total_price;
        product.total_price *= (1 - product.discount / 100);
        var $priceTag = $('<dd>').addClass('col-6').addClass('product-info-price-tag').html('<span class="cancelled-price-tag">' + originalPrice + '€</span> <span class="real-price-tag">' + product.total_price + '€</span>');
    } else
        var $priceTag = $('<dd>').addClass('col-6').text(product.total_price + '€');

    $row.append(
        $('<dt>').addClass('col-6').html('<strong>Prezzo:</strong>'),
        $priceTag
    );

    if (product.discipline)
        $row.append(
            $('<dt>').addClass('col-6').html('<strong>Disciplina:</strong>'),
            $('<dd>').addClass('col-6').append(
                $('<a>').append(capitalizeFirstLetter(product.discipline))
                    .attr("href", product.type == "Eventi" ? "events.php" : "courses.php")
                    .on('click', function () {
                        resetFilters('Discipline', product.discipline);
                    })
            )
        );

    if (product.level)
        $row.append(
            $('<dt>').addClass('col-6').html('<strong>Livello:</strong>'),
            $('<dd>').addClass('col-6').append(
                $('<a>')
                    .append(capitalizeFirstLetter(product.level))
                    .attr("href", product.type == "Eventi" ? "events.php" : "courses.php")
                    .on('click', function () {
                        resetFilters('Level', product.level);
                    })
            )
        );

    if (product.type)
        $row.append(
            $('<dt>').addClass('col-6').html('<strong>Tipo:</strong>'),
            $('<dd>').addClass('col-6').append(
                $('<a>')
                    .append(capitalizeFirstLetter(product.type))
                    .attr("href", product.type == "Eventi" ? "events.php" : "courses.php")
                    .on('click', function () {
                        resetFilters('Type', product.type);
                    })
            )
        );

    if (product.location) {
        if (product.location_link)
            var $content = $("<a>")
                .attr("href", product.location_link)
                .attr("target", "_blank")
                .html(product.location);
        else
            var $content = product.location


        $row.append(
            $('<dt>').addClass('col-6').html('<strong>Location:</strong>'),
            $('<dd>').addClass('col-6').append($content)
        );
    }


    // Aggiungi la descrizione alla sezione dedicata
    $('#productDescription').append(product.description);

    // Aggiungi la riga al container dei dettagli
    $productDetails.append($row);
}

function uploadProductButtons(product, logged, purchasable) {
    var $productDetails = $('#productDetails');

    var $div = $('<div>').addClass('d-flex justify-content-between mt-auto').attr('id', 'buttonsDiv');

    // Creazione del bottone "Aggiungi al carrello" con icona e link
    if (logged || !purchasable) {
        var $addCartBtn = $('<button>')
            .attr('id', 'btnAddToCart')
            .addClass('btn btn-purple btn-purple-focus-purple')
            .append($('<i>').addClass('bi bi-cart-plus h3')) // Aggiungi l'icona desiderata

        $div.append(
            $addCartBtn
                .attr('data-placement', "right")
                .on('click', function (event) {
                    event.preventDefault();
                    addToCart(product.id_product);
                })
        );

        //appena posso, eventualmente metto un tooltip
        isInCart(product.id_product).then(quantity => {
            if (quantity == 0)
                title = "Aggiungi al carrello";
            else
                title = "in carrello x" + quantity
            $addCartBtn
                .attr('data-value', quantity)
                .attr('title', title)
                .tooltip({
                    placement: 'right',
                    show: { effect: "fadeIn", duration: 300 }, // Effetto di visualizzazione
                    hide: { effect: "fadeOut", duration: 300 }  // Effetto di nascondimento
                });
        })

        // Creazione del bottone "Vai al carrello" con icona e link
        $purchaseBtn =
            $('<button>')
                .attr('id', 'purchaseProduct')
            .addClass('btn btn-purple btn-purple-focus-purple')
                .attr('data-placement', "left")
                //.append('Acquista ')
                .append($('<i>').addClass('bi bi-bag-fill h3')); // Aggiungi l'icona desiderata
    

        $div.append(
            $purchaseBtn
                .on('click', function (event) {
                    event.preventDefault();
                    purchaseProduct(product.id_product);
                })
        );
        //se il prodotto non è più acquistabile, disabilito i bottoni
        if (!purchasable) {
            $btnDescription = 'Non più acquistabile dal ' + formatDate(product.expiration_date);
            $addCartBtn
                .addClass('btn-secondary')
                .removeClass('btn-purple btn-purple-focus-purple')
                .attr('title', $btnDescription)
                .tooltip({
                    placement: 'right',
                    show: { effect: "fadeIn", duration: 300 }, // Effetto di visualizzazione
                    hide: { effect: "fadeOut", duration: 300 }  // Effetto di nascondimento
                });
            $purchaseBtn
                .addClass('btn-secondary')
                .removeClass('btn-purple btn-purple-focus-purple')
                .attr('title', $btnDescription)
                .tooltip({
                    placement: 'left',
                    show: { effect: "fadeIn", duration: 300 }, // Effetto di visualizzazione
                    hide: { effect: "fadeOut", duration: 300 }  // Effetto di nascondimento
                });
        } else {
            $purchaseBtn
                .attr('title', "Acquista e scarica la ricevuta")
                .tooltip({
                    placement: 'left',
                    show: { effect: "fadeIn", duration: 300 }, // Effetto di visualizzazione
                    hide: { effect: "fadeOut", duration: 300 }  // Effetto di nascondimento
                });
        }
    } else {
        //aggiungo il bottone per loggarsi
        $div.removeClass('justify-content-between').addClass('justify-content-center')
            .append(
                $('<a>').attr('href', 'login.php')
                    .addClass('btn btn-purple btn-purple-focus-purple')
                    .text('Effettua il login per acquistare')
            );

    }
    $productDetails.append($div);
}

function uploadCarousel(poster_path, public_files) {
    //contiene tutte le immagini (poster + file pubblici)
    var images = [];
    images[0] = {
        path: poster_path,
        description: "poster"
    };

    var $carouselIndicators = $('.carousel-indicators');

    if (public_files.length > 0) //se ci sono altre immagini, le aggiungo
        images = images.concat(public_files);
    else {           // Se c'è una sola immagine (il poster) nascondo i bottoni e l'indicatore del carosello
        $('.carousel-control-prev').hide();
        $('.carousel-control-next').hide();
        $carouselIndicators.hide();
    }

    // riempo il carosello
    var $carouselInner = $('.carousel-inner');

    images.forEach((file, index) => {
        var $div = $('<div>').addClass('carousel-item');
        if (index == 0) $div.addClass('active');
        var $div2 = $('<div>').addClass('image-container');
        var $img = $('<img>').addClass('carousel-image').attr('src', file.path).attr('alt', file.description);

        $div2.append($img);
        $div.append($div2);
        $carouselInner.append($div);

        var $button = $('<button>').attr('type', 'button').attr('data-bs-target', '#imagesCarousel').attr('data-bs-slide-to', index);
        if (index == 0) $button.addClass('active');

        $carouselIndicators.append($button);
    });

    //animo il carosello
    $('#imagesCarousel').carousel({
        interval: 3000,
        pause: "false"
    })
        .removeClass("d-none");
    $('#loadingSpinnerCarousel').addClass("d-none");


    // Zoom immagine del carosello
    $('.carousel-item').click(function () {
        var imgSrc = $(this).find('.carousel-image').attr('src');
        $('#fullscreenImg').attr('src', imgSrc);
        $('#fullscreenImage').removeClass('d-none'); // Show overlay
        $('body').addClass('overflow-hidden'); // Disable scroll
    });

    // Nascondi zoom al click
    $('#fullscreenImage').click(function () {
        $(this).addClass('d-none'); // Hide overlay
        $('body').removeClass('overflow-hidden'); // Enable scroll
    });
}




/* BOTTONI PER GESTIRE CARRELLO, ACQUISTI E RECENSIONI */

function addToCart(id_product) {
    var $btn = $("#btnAddToCart").prop("disabled", true);
    //agiungo al carrello e ricarico la navbar
    callService('modifyInCart', [id_product],
        function (response) { //OK
            //incremento di 1 il valore indicato
            var quantity = parseInt($btn.attr('data-value'), 10) + 1;
            $btn.attr('data-value', quantity);
            //lo riattivo
            $btn.prop("disabled", false);

            //CI rimetto il tooltip nuovo
            var newTooltipText = "In carrello: x" + quantity;
            $btn.attr('title', newTooltipText);

            // Aggiorna il tooltip esistente
            var tooltip = bootstrap.Tooltip.getInstance($btn[0]); // Ottieni l'istanza esistente del tooltip
            if (tooltip) {
                tooltip.setContent({ '.tooltip-inner': newTooltipText });
                tooltip.update(); // Aggiorna il tooltip con il nuovo testo
                tooltip.show(); // Mostra il tooltip aggiornato
            }

            //se la quantità nuova è 1, allora l' oggeto è stato aggiunto al carrello
            //lo notifico nella navbar
            if (quantity == 1) {
                $("#cart-badge").text(function (_, text) {
                    return parseInt(text, 10) + 1;
                });
                $('#cart-badge').show();
            }

        }, function (response) {
            $btn.addClass("bg-danger").prop("disabled", false);
            createToast(response.result, "Errore nella gestione del carrello", "Il prodotto non è più acquistabile");
        }, function (response) {
            $btn.addClass("bg-danger").prop("disabled", false);
            createToast(response.result, "Errore nella gestione del carrello", response.additional_info);
        }
    );
}

function purchaseProduct(id_product) {
    var $btn = $("#purchaseProduct").prop("disabled", true);
    //agiungo al carrello e ricarico la navbar
    callService('purchaseProduct', [id_product],
        function (response) {//OK
            createToast("OK", "Acquisto effettuato", "Hai acquistato una copia di<br>" + productObj.name);
            downloadReceipt(response.value);//ottengo la ricevuta
            //attivo il bottone per la recensione
            $("#btnReview")
                .attr("title", 'Lascia una recensione!')
                .attr("data-bs-original-title", 'Lascia una recensione!')
                .addClass("btn-edit")
                .removeClass("btn-secondary")
                .on('click', function (event) {
                    event.preventDefault();
                    leaveReview(id_product);
                });
        },
        function (response) {//ERROR
            $btn.addClass("bg-danger").prop("disabled", false);
            if (response.additional_info == "EXPIRED")
                createToast(response.result, "Impossibile effettuare l' acquisto", "Il prodotto non è più acquistabile");
            else
                createToast(response.result, "Impossibile effettuare l' acquisto", response.value);
        },
        function (response) {//FAIL
            $btn.addClass("bg-danger").prop("disabled", false);
            createToast(response.result, "Impossibile effettuare l' acquisto", response.additional_info);
        }
    );
}


function downloadReceipt(id_purchase) {
    //se l' acquisto è andato a buon fine, scarico lo scontrino
    callServiceDownloader("downloadReceipt", [id_purchase],
        function (name) { },//inizio download
        function () { },   //ogni 30 secondi
        function (name) {//ricevuta scaricata
            createToast("OK", "Ricevuta scaricata",
                `La ricevuta scaricata è solo a scopo informativo.<br>
                Gestisci i tuoi acquisti nell' <a href='profile.php'>area personale</a>`
                , 30000
            );
            $("#purchaseProduct").prop("disabled", false);
        },
        function () { //errore
            createToast("ERROR", "Acquisto effettuato, ma senza ricevuta", "Gestisci i tuoi acquisti nell' <a href='profile.php'>area personale</a>", 0);
            $("#purchaseProduct").prop("disabled", false);
        },
    )
}

//è dato da user, e salvato in un cookie

$(document).ready(function () {
    updateUrlId(productObj.id_product);
    uploadCarousel(productObj.poster_path, productObj.public_files);
    uploadProductDetails(productObj);
    uploadProductButtons(productObj, isLogged, isPurchasable);
});
