

function uploadClosestEvents(limit) {
    // Con una richiesta AJAX ottengo i 3 corsi migliori
    callService("getClosestEvents", [limit], function (response) { // OK
        var $eventsGrid = $('#eventi-container');
        $eventsGrid.empty(); // Pulisce eventuali elementi esistenti

        // Aggiungo alla griglia un elemento per ogni corso
        response.value.forEach(function (row) {
            var $div = $('<div class="col-lg-4 col-md-6 d-flex justify-content-center" data-aos="fade-up" data-aos-delay="100"></div>');
            var $div2 = $('<div class="service-item position-relative"></div>');
            var $img = $('<img>').attr('src', row.poster_path).attr('alt', row.name).addClass('icon');
            var $a = $('<a href="product.php?id='+ row.id_product +'" class="stretched-link"></a>');
            var $h3 = $('<h3></h3>').text(row.name);
            var $p = $('<p>').append(row.description.substring(0, 128) + '...');

            // Utilizza la funzione per aggiungere le icone di scadenza e sconto
            addExpirationAndDiscountIcons($div2, row, 120);

            //$div2.append($img);
            $div.append($div2);
            $div2.append($img).append($a).append($p); 
            $a.append($h3);
            $eventsGrid.append($div);
        });

        // Nasconde il caricamento e mostra il contenuto dei prodotti
        $("#loadingSpinnerEvents").hide();
        $("#eventi-container").show();
    }, function (response) {
        $div = errorProductDiv();
        $("#loadingSpinnerEvents").hide();
        $("#products-container").show().append($div);
        createToast(response.result, "Impossibile ottenere gli eventi piú prossimi", response.additional_info, 0);
    }, function (response) {
        $div = errorProductDiv();
        $("#loadingSpinnerEvents").hide();
        $("#products-container").show().append($div);
        createToast(response.result, "Impossibile ottenere gli eventi piú prossimi", response.additional_info, 0);
    });
}

// Appena la pagina è pronta, creo il div per
$(document).ready(function () {
    uploadClosestEvents(3);
});
