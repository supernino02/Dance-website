$(document).ready(function () {

    modalReviewManager();

    //aspetto che siano caricate le recensioni
    $.when(
        uploadReviews(productObj.id_product)
    ).done(function () {
        //verifico che possa effettuare la recensione
        if (isLogged) checkReviewableProduct(productObj.id_product);
    });

});

var LOADED_REVIEWS = [];
function uploadReviews(productId) {
    return callService('getAllProductReviews', [productId], 
        function (response) {
            LOADED_REVIEWS = response.value;
            uploadReviewDiv(LOADED_REVIEWS);
        }, function (response) {//EMPTY 
            noReviews(
                $('<p id="noReviewsAlert">')
                .text('Non ci sono recensioni per questo prodotto')
            );
        }, function (response) {
            createToast("FAIL", "Impossibile caricare le recensioni", response.additional_info);
            
            noReviews(
                $('<p>').addClass('text-danger fw-bold mt-2 error-message')
                .prepend($('<i>').addClass('fa-solid fa-circle-exclamation me-2'))
                .text('Impossibile caricare le recensioni')
            )
            
        });
}

function uploadReviewDiv(reviews) {
    var $productReviews = $('#reviews').empty();
    var reviewCount = [0, 0, 0, 0, 0];
    var ratingAverage = 0;

    //bottoni
    var $icon = $('#toggleReviews i.bi-chevron-down');
    $icon.css('transform', 'rotate(0deg)'); // Freccia giù all'inizio

    var $collapse = $('#reviewsContainer');
    var $dropdownMenu = $('#orderByDiv');

    $collapse.on('shown.bs.collapse', function () {
        $icon.css('transform', 'rotate(180deg)'); // Ruota in su quando aperto
        $dropdownMenu.css('display', 'inline').stop(true, true).fadeIn(200); // Fade in with 200ms duration
    });

    $collapse.on('hidden.bs.collapse', function () {
        $icon.css('transform', 'rotate(0deg)'); // Riporta in giù quando chiuso
        $dropdownMenu.stop(true, true).fadeOut(200, function () {
            $(this).css('display', 'none'); // Nasconde completamente l'elemento dopo l'animazione
        }); // Fade out with 200ms duration
    });

    //bottoni per ordinare le recensioni
    $("#btnOrderDate").click(function (e) {
        e.preventDefault();
        $("#btnOrderDate").addClass("active");
        $("#btnOrderEval").removeClass("active");
        seeAllReviews();
        sortReviewsByDate();
    });

    $("#btnOrderEval").click(function (e) {
        e.preventDefault();
        $("#btnOrderEval").addClass("active");
        $("#btnOrderDate").removeClass("active");
        seeAllReviews();
        sortReviewsByEvaluation();
    });
    //fine bottoni

    reviews.forEach((review, index) => {
        //mostro le prime 5 recensioni, le altre le nascondo
        var $reviewItem = $('<div>').addClass('review-item mb-4 border rounded card')
            .attr('data-id_purchase', review.id_purchase)
            .attr('data-evalutation', review.star_evaluation)
            .attr('data-date', review.date);
        if (index >= 5) {
            $reviewItem.addClass('d-none'); // Nascondi le recensioni oltre la quinta
        }
        var $header = $('<div>').addClass('card-header p-2')

        var $reviewTitle = $('<h5>').addClass('review-title d-flex justify-content-between align-items-center');
        var $titleText = $('<span>').text(review.name);
        var $reviewDate = $('<span>').addClass('review-date text-muted').text(formatDate(review.date)).css('font-size', '0.875em'); // Data in grigio e dimensione testo ridotta
        var $starContainer = $('<div>').addClass('review-stars mb-2').append(createStarCounter(review.star_evaluation));

        if (review.note) {
            var $body = $('<div>').addClass('card-body p-2 m-2')
            var $reviewText = $('<p>').addClass('review-text').html(review.note);
            $body.append($reviewText);
        }

        $header.append($reviewTitle, $starContainer);
        $reviewTitle.append($titleText, $reviewDate); // Aggiungi il titolo e la data allo stesso elemento
        $reviewItem.append($header, $body);
        $productReviews.append($reviewItem);

        reviewCount[Math.ceil(review.star_evaluation) - 1]++; // "ceil" approssima per eccesso
        ratingAverage += review.star_evaluation;
    });

    ratingAverage /= reviewCount.reduce((a, b) => a + b, 0); // Calcola la media
    uploadAverageReviews(reviewCount, isNaN(ratingAverage) ? 0 : ratingAverage.toFixed(2));

    // Aggiungi il pulsante "Vedi tutte" se ci sono più di 5 recensioni
    if (reviews.length > 5) {
        var $showAllButton = $('<button>')
            .text('Vedi tutte')
            .addClass('btn btn-purple mt-3')
            .attr('id', 'seeAllBtn')
            .on('click', seeAllReviews);
        $productReviews.append($showAllButton);
    }

    $('#loadingSpinnerReviews').hide();//tolgo il plcaholder
}

function noReviews($msg){
    var $productReviews = $('#reviews');
    $('#toggleReviews').removeAttr('data-bs-toggle');
    // Nascondi il bottone di toggle
    $('#toggleReviews').hide();
    $('#reviewsContainer').removeClass('collapse');

    $productReviews.append($msg)

    uploadAverageReviews([0, 0, 0, 0, 0], 0);
    $('#loadingSpinnerReviews').hide();//tolgo il plcaholder
}

function uploadAverageReviews(reviewCount, ratingAverage) {
    var duration = 2000;
    animatePercentage($('#ratingAverage'), $('#ratingAverage').text, ratingAverage, duration, 2);
    $('#starsAverage').empty().append(createStarCounter(ratingAverage));

    var $reviewsCount = $('#reviewsCount').empty();
    var totalReviews = reviewCount.reduce((a, b) => a + b, 0);
    var reviewsCountText = totalReviews + ' recensioni';
    $reviewsCount.text(reviewsCountText);


    for (var i = 0; i < 5; i++) {
        var percentage = ((reviewCount[i] / totalReviews) * 100).toFixed(0);
        $('.progress-bar').eq(i)
            //.attr('style', 'width: ' + percentage + '%')  // Colore viola
            .animate({ width: percentage + '%' }, duration)  // Animate width change
            .attr('aria-valuenow', reviewCount[i])
            .attr('aria-valuemax', totalReviews);

        if (isNaN(percentage)) percentage = 0;
        var $perc = $('.perc_reviews').eq(i).children('.text-end');
        animatePercentage($perc, $perc.text, percentage, duration);
    }
}

var MY_REVIEW = $(); //mi salvo come variabile globale
function checkReviewableProduct(productId) {
    callService('getPersonalReview', [productId],
        function (response) {  //se l'ho trovata
            highlightMyReview(response.value.id_purchase);
        },
        function (response) { //se non ho una recensione associata
            //carico il bottone
            var $btnReview = $('<button>')
                .attr('id', 'btnReview')
                .addClass('btn')
                .append('<i class="bi bi-star mx-2"></i>');
            $('#btnReviewDiv').append($btnReview);

            //se non l'ho acquistato, lo disattivo
            if (response.additional_info == "NOT PURCHASED") 
                $btnReview
                    .addClass('btn-secondary')
                    .attr('title', "Procedi all' acquisto per poter scrivere la tua recensione")
                    .tooltip({
                        placement: 'left',
                        show: { effect: "fadeIn", duration: 300 }, // Effetto di visualizzazione
                        hide: { effect: "fadeOut", duration: 300 }  // Effetto di nascondimento
                    });
            else //NO REVIEW
                $btnReview
                    .attr('title', "Lascia la tua Recensione!")
                    .attr('data-placement',"top")
                    .addClass("btn-edit btn-purple-focus-purple")
                    .tooltip({
                        placement: 'left',
                        show: { effect: "fadeIn", duration: 300 }, // Effetto di visualizzazione
                        hide: { effect: "fadeOut", duration: 300 }  // Effetto di nascondimento
                    }).on('click', function (event) {
                    event.preventDefault();
                    leaveReview(productId);
                });
        },
        function (response) { //FAIL
            createToast("FAIL", "Impossibile caricare la tua recensione", response.additional_info);
        }
    );
}

function highlightMyReview(id_purchase) {
    var $item = $('.review-item[data-id_purchase="' + id_purchase + '"]');
    MY_REVIEW = $item;
    $item.addClass('highlighted-review');

    // Lo faccio diventare primo child
    $item.prependTo($item.parent());

    // Ci aggiungo un toggle
    $item
        .attr('title', 'La tua recensione')
        .tooltip({
            show: { effect: "fadeIn", duration: 300 }, // Effetto di visualizzazione
            hide: { effect: "fadeOut", duration: 300 }, // Effetto di nascondimento
        });
}


function leaveReview(id_product) {
    //aggiungo la recensione
    var $modal = $('#reviewModal');
    $modal.modal('show');
    $('#sendReview').on('click', function (event) {
        event.preventDefault();
        var star_evaluation = $('#starValue').val();
        var note = $('#reviewText').val();

        callService('createProductReview', [id_product, star_evaluation, note],
        function (response) {
            createToast("OK", "Recensione creata", "Grazie per la tua opinione😋");

            $modal.modal('hide');
            $("#btnReview").remove();//rimuovo il bottone per le recensioni
            LOADED_REVIEWS.push(response.value); //aggiungo la nuova recensinoe a quelle caricate
            uploadReviewDiv(LOADED_REVIEWS); //aggiorno il div
            highlightMyReview(response.value.id_purchase);//metto in rilievo la mia recensione
            $("#toggleReviews").children('div').first().click(); //motro il toggle
        },
        function (response) {// INVALID PARAMETERS
            $modal.modal('hide');
            createToast("FAIL", "Alcuni campi non sono validi", response.value);
        },
        function (response) {
            $modal.modal('hide');
            createToast("FAIL", "Impossibile lasciare la recensione", response.additional_info);
        },
    );
    });
}

function animatePercentage(element, startValue, endValue, duration, decimals = 0) {
    let startTime = Date.now();
    $({ value: startValue }).animate({ value: endValue }, {
        duration: duration,
        step: function (now) {
            element.text(parseFloat(now.toFixed(decimals)));
        },
        // Optional: callback when animation completes
        complete: function () {
            element.text(endValue);
        }
    });
}

/*bottone per mettere in ordine le reviews*/
    function seeAllReviews() {
        $('.review-item.d-none').removeClass('d-none'); // Mostra tutte le recensioni nascoste
        $("#seeAllBtn").remove(); // Rimuovi il pulsante dopo aver mostrato tutte le recensioni
    }



    // Function to sort reviews by date with ghosting effect
    function sortReviewsByDate() {
        var $reviews = $('#reviews .review-item');

        // Fade out all reviews first
        $reviews.fadeOut(400, function () {
            // Sort reviews by date
            $reviews.sort(function (a, b) {
                var dateA = new Date($(a).data('date'));
                var dateB = new Date($(b).data('date'));
                return dateB - dateA; // Descending order (latest first)
            });

            // Append sorted reviews back to the container
            $('#reviews').html($reviews);

            // Fade in the reviews after sorting
            $reviews.fadeIn(400);

            // la mia recensione la faccio diventare primo child
            MY_REVIEW.prependTo(MY_REVIEW.parent());
        });
    }

    // Function to sort reviews by evaluation with ghosting effect
    function sortReviewsByEvaluation() {
        var $reviews = $('#reviews .review-item');

        // Fade out all reviews first
        $reviews.fadeOut(400, function () {
            // Sort reviews by evaluation
            $reviews.sort(function (a, b) {
                var evalA = parseFloat($(a).data('evalutation'));
                var evalB = parseFloat($(b).data('evalutation'));
                return evalB - evalA; // Descending order (highest rating first)
            });

            // Append sorted reviews back to the container
            $('#reviews').html($reviews);

            // Fade in the reviews after sorting
            $reviews.fadeIn(400);

            // la mia recensione la faccio diventare primo child
            MY_REVIEW.prependTo(MY_REVIEW.parent());
        });
    }
