function uploadDropdowns() {
    $('#ordersContentCollapse').on('show.bs.collapse', function () {
        $('#ordersDropdownIcon').removeClass('bi-chevron-down').addClass('bi-chevron-up');
    });

    $('#ordersContentCollapse').on('hide.bs.collapse', function () {
        $('#ordersDropdownIcon').removeClass('bi-chevron-up').addClass('bi-chevron-down');
    });

    $('#productsContentCollapse').on('show.bs.collapse', function () {
        $('#productsDropdownIcon').removeClass('bi-chevron-down').addClass('bi-chevron-up');
    });

    $('#productsContentCollapse').on('hide.bs.collapse', function () {
        $('#productsDropdownIcon').removeClass('bi-chevron-up').addClass('bi-chevron-down');
    });
}

var PRODUCTS = [];
var UPDATED_PURCHASES = 0;
var PRODUCT_PROMISES = [];
var TOTAL_PURCHASES;

function uploadUserOrders() {

    var $spinner = $("#ordersLoadingSpinner");
    var $ordersContent = $("#ordersContent");
    var counterPurchase = 1;

    // Ottengo gli id degli ordini dell'utente
    callService("getAllPurchaseIds", [],
        function (response) {
            //quanti purchase ci sono in totale
            TOTAL_PURCHASES = response.value.length;

            response.value.forEach(function (order) { // Per ogni ordine

                // Crea il div order-item in modalitá placeholder
                var $orderItem = $("<div class='order-item mb-4 p-3 border rounded d-flex flex-column'>").addClass("placeholder-glow bg-secondary");
                $ordersContent.append($orderItem);
                var $h5 = $("<h5 class='order-title d-flex justify-content-between align-items-center'>");
                $orderItem.append($h5);
                var $spanCounter = $("<span>Ordine #" + (counterPurchase++) + "</span>")
                $h5.append($spanCounter);
                var $spanDate = $("<span class='order-date text-muted'>").addClass("placeholder bg-secondary");
                $h5.append($spanDate);

                // Aggiungi totale ordine
                var $div = $("<div class='d-flex justify-content-between align-items-center'>");
                $orderItem.append($div);

                // Aggiungi lista prodotti in modalitá placeholder
                var $orderProducts = $("<div class='order-products mb-4'>");
                $orderItem.append($orderProducts);

                // Aggiungi totale e bottone ricevuta
                var $divBtn = $("<div class='mt-auto d-flex justify-content-between'>");
                $orderItem.append($divBtn);
                var $p = $("<p class='order-total mb-0'>").addClass("placeholder bg-secondary");
                $divBtn.append($p);
                var $button = $("<button class='btn btn-purple btn-purple-focus-purple'>")
                    .addClass(" placeholder bg-secondary")
                    .prop('disabled', true) //disabilitato all' inizio
                    .attr('title', "Stampa la ricevuta")
                    .tooltip({
                        placement: 'left',
                        show: { effect: "fadeIn", duration: 300 }, // Effetto di visualizzazione
                        hide: { effect: "fadeOut", duration: 300 }  // Effetto di nascondimento
                    });
                $divBtn.append($button);
                var $i = $("<i class='fas fa-receipt mx-2'>");
                $button.append($i)


                // Ottengo i dettagli dell'ordine
                callService("getPurchaseInfo", [order.id_purchase],
                    function (response) { //OK
                        //definisco data e prezzo totale
                        $spanDate.html(formatDate(response.value.date_time));
                        $p.html("<b>Totale: " + response.value.total_price + "€</b>");

                        //rimuovo placeholder dell'acquisto
                        $orderItem.removeClass("placeholder-glow bg-secondary");
                        $spanDate.removeClass("placeholder bg-secondary");
                        $p.removeClass("placeholder bg-secondary");
                        $button.removeClass("placeholder bg-secondary");

                        //aggiungo una lista con una riga per ogni prodotto acquistato
                        var $ul = $("<ul class='list-group list-group-flush'>");
                        $orderProducts.append($ul);

                        //per ogni prodotto, aggiungo una riga
                        response.value.products.forEach(function (product) {
                            var $li = $("<li class='list-group-item' data-id_product='" + product.id_product + "'>").addClass("placeholder bg-secondary w-50");
                            $ul.append($li);
                        });

                        //attivo la richiesta della ricevuta
                        $button.on('click', function () { downloadReceipt(order.id_purchase, $button) }).prop('disabled', false);

                        //per ognuno, richiedo di completare i prodotti
                        response.value.products.forEach(function (product) {
                            // Ottengo il nome del prodotto
                            PRODUCT_PROMISES.push(
                                callService("getProduct", [product.id_product],
                                    function (response) { //OK e ERROR sono analoghi
                                        addPurchaseProduct($ul, product.quantity, response);
                                    }, function (response) { //EXPIRED
                                        addPurchaseProduct($ul, product.quantity, response);
                                    }, function (response) {//FAIL
                                        //trovo la riga associata
                                        var $li = $ul.find("li[data-id_product='" + product.id_product + "']");
                                        //aggiorno il contenuto
                                        var $i = $('<i class="btn-danger fa-solid fa-circle-exclamation loading-product-error-icon"></i>')
                                        $li.prepend($i).append("Errore nel caricamento del prodotto");

                                        //rimuovo il placeholder
                                        $li.removeClass("placeholder bg-secondary w-50")
                                            .css({
                                                'background-color': 'var(--bs-danger-bg-subtle)',
                                                'border': '3px solid var(--bs-danger-border-subtle)'
                                            })
                                            .addClass("border rounded");

                                        //notifico errore
                                        createToast("FAIL", "Errore nel caricamento del prodotto", response.additional_info);
                                    }
                                )
                            );

                            // 
                        });
                        checkAllPurchaseLoaded();

                    }, function (response) {//ERROR non può succedere
                    }, function (response) { //FAIL
                        $orderItem.removeClass("placeholder-glow bg-secondary border").addClass();
                        $spanDate.removeClass("placeholder bg-secondary");
                        $p.removeClass("placeholder bg-secondary");
                        $i.removeClass("fas fa-receipt mx-2").addClass("btn-danger fa-solid fa-circle-exclamation").css("color", "#dc3545");;
                        $divBtn.append($i);
                        $button.remove();
                        $orderItem.css({
                            'background-color': 'var(--bs-danger-bg-subtle)',
                            'border': '3px solid var(--bs-danger-border-subtle)'
                        });
                        $div.append($("<p>Errore nel caricamento dell'ordine</p>"))

                        createToast("FAIL", "Errore nel caricamento dell'ordine", response.additional_info);
                        checkAllPurchaseLoaded();
                    }
                )
                //MOSTRO LA SEZIONE CON GLI ORDINI
                $spinner.hide();
                $ordersContent.show();
            })
        }, function (response) { // ERROR (empty)   
            insertErrorOrders($("<h4>").append(
                $("<p>Non sono presenti ordini</p>")
            ));

            //mostro errore nei purchase
            insertErrorProducts($("<h4>").append(
                $("<p>Non sono presenti prodotti acquistati</p>")
            ));
        }, function (response) { // FAIL (altri errori)
            //mostro errore nei prodotti
            insertErrorOrders($("<h3>").append(
                $("<p>Errore nel caricamento degli ordini (FAIL)</p>"),
                $("<p>Contattare un admin se il problema persiste</p>")
            ));

            //mostro errore nei purchase
            insertErrorProducts($("<h3>").append(
                $("<p>Errore nel caricamento degli ordini (FAIL)</p>"),
                $("<p>Contattare un admin se il problema persiste</p>")
            ));
            createToast("FAIL", "Impossibile visualizzare i propri acquisti", response.additional_info);
        }
    );
}


//notifico che il purchase é stato caricato. se era l'ultimo, inizio a scaricare i prodotti.
function checkAllPurchaseLoaded() {
    UPDATED_PURCHASES++;
    //contorllo se ho finito di fare l'ultimo purchase
    //a questo punto so che tutte le promises sono state messe nell'array 
    if (UPDATED_PURCHASES == TOTAL_PURCHASES) {
        //a questo punto aspetto tutte le richieste salvate siano soddisfatte, cioé tutti i purchase e tutti i prodotti 
        $.when.apply($, PRODUCT_PROMISES).done(function () {
            uploadUserProducts();
        });
    }
}

function insertErrorOrders($elem) {
    //mostro il valore
    $("#ordersLoadingSpinner").hide();
    $("#ordersContent").show().append($elem);
    $('#ordersContentCollapse').collapse('show');
}

function insertErrorProducts($elem) {
    //mostro il valore
    $("#productsLoadingSpinner").hide();
    $("#productsContent").show().append($elem);
    $('#productsContentCollapse').collapse('show');
}

//dato un prodotto, lo aggiungo al purchase
function addPurchaseProduct($ul, quantity, response) {
    var product_complete = response.value;

    var $a = $("<a>").attr("href", "product.php?id=" + product_complete.id_product).append(quantity + "x " + product_complete.name);

    //trovo la riga associata
    var $li = $ul.find("li[data-id_product='" + product_complete.id_product + "']");
    //aggiorno il contenuto
    $li.append($a);

    //rimuovo il placeholder
    $li.removeClass("placeholder bg-secondary w-50");

    if (!PRODUCTS.some(p => p.id_product == product_complete.id_product))
        // Se non è presente, lo aggiunge all'array
        PRODUCTS.push(product_complete);
}

function uploadUserProducts() {

    $("#productsLoadingSpinner").hide();
    $("#productsContent").show();

    var $productsContent = $("#productsContent");

    PRODUCTS.forEach(function (product) {
        var $productItem = $("<div class='product-item mb-4 p-3 border rounded d-flex flex-column'>");
        var $h5 = $("<h5 class='product-title d-flex justify-content-between align-items-center'>");
        var $spanCounter = $("<a href='product.php?id=" + product.id_product + "'>" + product.name + "</a>");

        $h5.append($spanCounter);
        $productItem.append($h5);

        callService("getAllPurchasablesFiles", [product.id_product],
            function (response) { // OK
                addProductFiles($productItem, response.value, product.id_product);
            },
            function (response) { // ERROR
                addProductFiles($productItem, [], product.id_product);
            },
            function(response) { //FAIL
                $productItem.append($("<h6>Errore durante il caricamento</h6>"));
                $productItem.addClass("alert alert-danger");
                createToast(response.result, "Errore nel caricamento dei file del prodotto", response.additional_info);
            }
        );

        $productsContent.append($productItem);
    });
}

function addProductFiles($productItem, files, productId) {

    if (files.length > 0) {
        var $productFiles = $("<div class='product-files mb-4'>");
        var $ul = $("<ul class='list-group list-group-flush'>");

        files.forEach(function (file) {
            var $li = $("<li class='list-group-item'>");
            var $itemDiv = $("<div class='d-flex justify-content-between'>");
            var $p = $("<p>" + file.name + "</p>");
            var $button = $("<button class='btn btn-purple btn-purple-focus-purple'>")
                .attr('title', "Scarica allegato")
                .tooltip({
                    placement: 'left',
                    show: { effect: "fadeIn", duration: 300 },
                    hide: { effect: "fadeOut", duration: 300 }
                });
            var $i = $("<i class='fas fa-download mx-2'>");

            //scarico il file, il bottone é disattivato finche non finisce o erro
            $button.on('click', function () {
                $button.prop("disabled", true);
                callServiceDownloader("downloadPurchasables", [file.id_product, file.n_file],
                    undefined,undefined,undefined,undefined, $button
                );
            });

            $itemDiv.append($p);
            $itemDiv.append($button.append($i));
            $li.append($itemDiv);
            $ul.append($li);
        });

        $productFiles.append($ul);
        $productItem.append($productFiles);
    } else {
        var $p = $("<p> Non ci sono allegati scaricabili per questo prodotto </p>");
        $productItem.append($p);
    }

    addReviewSection($productItem, productId, files.length > 1);
}

function addReviewSection($productItem, productId, hasMultipleFiles) {
    var $footerDiv = $("<div class='d-flex justify-content-between'>");
    var $reviewButtonPlaceholder = $("<div class='review-button-placeholder'></div>");
    var $reviewPlaceholder = $("<div class='review-placeholder'></div>");
    var $downloadButtonPlaceholder = $("<div class='download-button-placeholder'></div>");

    callService("getPersonalReview", [productId], 
        function (response) { // OK
            createDivReview(response, $reviewPlaceholder);
        }, function (response) { // ERROR
            var $reviewButton = $("<button id='reviewButton' class='btn btn-purple btn-purple-focus-purple'>")
                .attr('title', "Lascia una recensione")
                .tooltip({
                    placement: 'top',
                    show: { effect: "fadeIn", duration: 300 },
                    hide: { effect: "fadeOut", duration: 300 }
                });
            var $iStar = $("<i class='bi bi-star mx-2'>");
            $reviewButton.html("Recensisci").append($iStar);
            $reviewButton.on('click', function () {
                var $modal = $('#reviewModal');
                $modal.modal('show');
                $('#sendReview').on('click', function (event) {
                    event.preventDefault();
                    var star_evaluation = $('#starValue').val();
                    var note = $('#reviewText').val();

                    callService('createProductReview', [productId, star_evaluation, note], 
                    function (response) {
                        $modal.modal('hide');
                        $reviewButton.remove();
                        createDivReview(response, $reviewPlaceholder);

                        createToast("OK", "Grazie per la recensione", "Ci sta a cuore la tua opinione❤️");
                    },
                    function (response) { //ERROR alcuni campi sono errati
                        $modal.modal('hide');
                        createToast("ERROR", "Campi errati nella recenzione", response.value);
                    },
                    function (response) { //ERROR alcuni campi sono errati
                        $modal.modal('hide');
                        createToast("FAIL", "Impossbile recensire il prodotto", response.value);
                    });
                });
            });
            $reviewButtonPlaceholder.prepend($reviewButton);
        },function (response) { //fail
            var $header = $('<div>').addClass('card-header p-2');
            var $reviewItem = $('<div>').addClass('review-item mb-4 highlighted-review mt-4 border rounded card');
            var $reviewTitle = $('<h5>')
                .addClass('review-title d-flex justify-content-between align-items-center text-danger')
                .html("Errore nel caricamento della recensione.");

            $header.append($reviewTitle);
            $reviewItem.append($header);

            $reviewPlaceholder.removeClass("review-placeholder");
            $reviewPlaceholder.append($reviewItem);
            $reviewPlaceholder.show();

            createToast(response.result, "Errore nel caricamento della recensione del prodotto", response.additional_info);
        });

    console.log(productId,hasMultipleFiles)
    var $downloadButton = $();
    if (hasMultipleFiles) {
        $downloadButton = $("<button class='btn btn-purple btn-purple-focus-purple'>")
            .attr('title', "Scarica tutti gli allegati in uno Zip unico")
            .tooltip({
                placement: 'top',
                show: { effect: "fadeIn", duration: 300 },
                hide: { effect: "fadeOut", duration: 300 }
            })
            .on('click', function () {
                $downloadButton.attr("disabled",true);
                callServiceDownloader("downloadZipPurchasables",[productId],
                    undefined, undefined, undefined, undefined, $downloadButton
                );
            }).html("Scarica tutti").append($("<i class='bi bi-file-zip mx-2'>"));

        $downloadButtonPlaceholder.removeClass("download-button-placeholder");
        $downloadButtonPlaceholder.append($downloadButton);
        $downloadButtonPlaceholder.show();
    }

    $footerDiv.append($reviewButtonPlaceholder, $downloadButtonPlaceholder);
    $productItem.append($footerDiv, $reviewPlaceholder);
}

function createDivReview(response, $reviewPlaceholder) {
    var review = response.value;

    var $header = $('<div>').addClass('card-header p-2');
    var $reviewItem = $('<div>').addClass('review-item mb-4 highlighted-review mt-4 border rounded card');
    var $reviewTitle = $('<h5>').addClass('review-title d-flex justify-content-between align-items-center');
    var $starContainer = $('<div>').addClass('review-stars mb-2').append(createStarCounter(review.star_evaluation));
    var $reviewDate = $('<span>').addClass('review-date text-muted ml-auto').text(formatDate(review.date)).css('font-size', '0.875em');

    if (review.note) {
        var $body = $('<div>').addClass('card-body p-2 m-2')
        var $reviewText = $('<p>').addClass('review-text').html(review.note);
        $body.append($reviewText);
    }


    $reviewTitle.append($starContainer, $reviewDate);
    $header.append($reviewTitle);
    $reviewItem.append($header, $body);

    $reviewItem.attr('title', 'La tua recensione')
        .tooltip({
            show: { effect: "fadeIn", duration: 300 },
            hide: { effect: "fadeOut", duration: 300 }
        });

    $reviewPlaceholder.append($reviewItem);
    $reviewPlaceholder.show();
}

function downloadReceipt(id_purchase, $button) {
    $button.prop("disabled",true);
    //se l' acquisto è andato a buon fine, scarico lo scontrino
    callServiceDownloader("downloadReceipt", [id_purchase],
        function (name) { },//inizio download
        function () { },   //ogni 30 secondi
        function (name) {//ricevuta scaricata
            $button.prop("disabled", false);
            createToast("OK", "Ricevuta scaricata",
                "La ricevuta scaricata è solo a scopo informativo."
                , 30000
            );
        },
        function () { //errore
            $button.prop("disabled", false);
            createToast("ERROR", "Impossibile scaricare lo scontrino", "<a href='index.php#contatti'>Contattaci</a> per maggiori informazioni", 0);
        },
    )
}

// Codice che viene eseguito quando la pagina è pronta
$(document).ready(function () {
    uploadDropdowns();
    uploadUserOrders();
    modalReviewManager();
});
