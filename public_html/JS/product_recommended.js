$(document).ready(function () {

    uploadRelatedProducts(productObj.id_product);
});


function uploadRelatedProducts(productId) {
    callService("getRelatedProducts", [productId, 3],
        addRelatedProducts,//OK
        addRelatedProducts,//ERROR (ne ho chiesti 3 , ne ha trovati di meno ma completa con i best seller)
        function (response) { //FAIL nel caricamento
            createToast("FAIL", "Impossibile caricare i prodotti correlati",response.additional_info);
            var $relatedProducts = $('#relatedProducts').empty(); // Pulisce il placeholder
            //creo una riga che notifica l' errore
            var $a = $('<a>').css("background-color","var(--bs-danger-bg-subtle)").attr("disabled", true).css("pointer-events", "none");
            var $i = $('<i>').addClass("btn-danger fa-solid fa-circle-exclamation").css("color","#dc3545");
            var $span = $('<span>').text("Errore durante il caricamento dei prodotti correlati");

            // Appendi gli elementi al DOM
            $a.append($i);
            $a.append($span);
            $relatedProducts.append($a);
        }
    );
}

function addRelatedProducts(response) {
    var $relatedProducts = $('#relatedProducts').empty(); // Pulisce il placeholder

    // Usa un ciclo per elaborare i prodotti
    response.value.forEach(product => {
        // Crea gli elementi DOM
        var $a = $('<a>').attr('href', 'product.php?id=' + product.id_product);
        var $i = $('<i>').addClass('bi bi-arrow-right-circle');
        var $span = $('<span>').text(product.name);

        // Appendi gli elementi al DOM
        $a.append($i);
        $a.append($span);
        $relatedProducts.append($a);
    });
}