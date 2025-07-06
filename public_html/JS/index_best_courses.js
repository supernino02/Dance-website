function uploadTopCourses(limit) {
    // Con una richiesta AJAX ottengo i corsi migliori
    callService("getBestSellingProducts", [limit, 'COURSES'], function (response) { // OK
        var $coursesGrid = $('#products-container');
        $coursesGrid.empty(); // Pulisce eventuali elementi esistenti

        // Aggiungo alla griglia un elemento per ogni corso
        response.value.forEach(function (product) {
            var $productItem = createProductItem(product);
            $coursesGrid.append($productItem);
        });

        // Nasconde il caricamento e mostra il contenuto dei prodotti
        $("#loadingSpinnerProducts").hide();
        $("#products-container").show();
    }, function (response) { // ERROR
        createErrorProductItem(response, "Non sono stati trovati prodotti");
        createToast(response.result, "Impossibile ottenere i migliori corsi", response.additional_info, 0);

    }, function (response) { // FAIL
        createErrorProductItem(response, "Impossibile ottenere i migliori corsi");
        createToast(response.result, "Impossibile ottenere i corsi migliori", response.additional_info, 0);

    }); 
}

// Appena la pagina è pronta, creo il div per
$(document).ready(function () {
    uploadTopCourses(3);
});