function loadCookieBanner(cookies_description) {
    //se possibile prendo le vecchie preferenze un array, altrimenti lo metto a null
    var cookie_preferences_old = getCookie("cookie_preferences");
    cookie_preferences_old = cookie_preferences_old ? JSON.parse(cookie_preferences_old) : []

    var $cookie_name_container = $("#cookie-toggler-container");
    var $cookies_description_container = $("#cookie-description-collapsable-container")

    //salvo nell' oggetto i query
    cookies_description.forEach(cookie => {
        var description_body = "";
        cookie.description_body.forEach(row => {
            description_body += row + "<br>";
        });

        //verifioc sia gia stato definito 
        var selected = true;
        if (typeof cookie_preferences_old[cookie.name] !== 'undefined'
            && cookie_preferences_old[cookie.name] == false)
            selected = false;

        $cookie_name_container.append(`
                    <div class="row col-12 collapse-cookie-info d-flex align-items-center cookie-selector-card" data-target="#cookie-${cookie.name}">
                        <div class="mb-2 col-auto">
                            <span>${cookie.name}</span>
                        </div>
                        <div class="form-check form-switch ms-auto col-auto">
                            <input id="check-cookie-${cookie.name}" class="form-check-input cookie-checkbox btn-purple" type="checkbox" role="switch" data-cookie-target="${cookie.name}" data-cookie-type="${cookie.type}" 
                                ${cookie.type == "essential" ? "disabled checked" : ""} 
                                ${selected ? "checked" : ""}
                                >
                        </div>
                    </div>`);

        $cookies_description_container.append(`
                    <div class="card cookie-description" id="cookie-${cookie.name}">  
                        <div class="card-header d-flex justify-content-between">
                            ${cookie.name} 
                            <span class="col-auto fw-bold">${capitalizeFirstLetter(cookie.type)}</span></div> 
                        <div class="card-body">
                            <h5 class="card-title">${cookie.description_header}</h5>
                            <p class="card-text">${description_body}</p>
                        </div>
                    </div>`);

        // Imposta l'evento onclick per gestire il fade e nascondi tutti gli altri
        $cookie_name_container.on('click', `.cookie-selector-card`, function () {
            // Seleziona l'elemento target dal data-target
            var $targetDescription = $($(this).data('target'));
            // Nascondi tutte le descrizioni tranne quella target
            $('.cookie-description').not($targetDescription).hide();
            $targetDescription.show().addClass("active");

            $(".cookie-selector-card").removeClass("active");
            $(this).addClass("active");
        });
    });

    // Ascolta l'evento "change" su ogni checkbox con classe .cookie-checkbox
    $('.cookie-checkbox').on('change', function () {
        // Ottieni il valore del data attribute `data-cookie-type` dell'elemento che ha scatenato l'evento
        var cookieType = $(this).data('cookie-type');

        // Controlla se tutte le checkbox con questo `data-cookie-type` sono selezionate o deselezionate
        var allChecked = true;  // Presuppone che tutte siano checked
        var allUnchecked = true;  // Presuppone che tutte siano unchecked

        $(`.cookie-checkbox[data-cookie-type="${cookieType}"]`).each(function () {
            var is_checked = $(this).is(":checked");
            if (!is_checked) allChecked = false;  // Se una checkbox non è selezionata, non sono tutte checked
            if (is_checked) allUnchecked = false;  // Se una checkbox è selezionata, non sono tutte unchecked
        });

        // Se tutte le checkbox sono checked o unchecked, aggiorna anche .check-cookie-type
        if (allChecked || allUnchecked) {
            $(`.check-cookie-type[data-cookie-type="${cookieType}"]`).prop('checked', allChecked);
        }
    });

    //rendo selected il primo cookie, mostro la sua descrizione
    $(".cookie-selector-card").first().addClass("active").click();
}

function sendUpdatedCookiePreferencies(button) {
    var $btns = $(".accept-cookies");
    $btns.prop("disabled", true);
    // Nuovo oggetto per coppie nome-checked
    var new_preferences = {};

    //prendo tutti i cookie che non sono essenziali
    $('.cookie-checkbox:not([data-cookie-type="essential"])').each(function () {
        var cookieName = $(this).data('cookie-target');
        new_preferences[cookieName] = $(this).is(":checked");
    });

    new_preferences = JSON.stringify(new_preferences);

    callService("setCookiePreferences", [new_preferences],
        function (response) { //OK
            $("#cookie-icon i")
                .removeClass("fa-cookie")
                .addClass("fa-cookie-bite");
            $("#close-modal").click();
            $btns.prop("disabled", false);
        },
        function (response) { //ERROR
            createToast(response.result, "Impossibile aggiornare le preferenze sui Cookie",
                "Puoi provare ad <a href='under_construction.php' target='blank'>aggiornarli manualmente</a>"
            );
            $("#close-modal").click();
            $btns.prop("disabled", false);
        },
        function (response) { //FAIL
            createToast(response.result, "Impossibile aggiornare le preferenze sui cookie", response.additional_info);
            $("#close-modal").click();
            $btns.prop("disabled", false);
        }
    );
}

$(document).ready(function () {
    //carico l'icona
    var $cookieIcon = $('<a>', {
        id: 'cookie-icon',
        href: '#cookieModal',
        class: 'd-flex align-items-center justify-content-center',
        'data-bs-toggle': 'modal',
        'data-target': '#cookieModal'
    });

    //indico il cookie mangiato se ci sono gia preferenze
    var first_time = getCookie("cookie_preferences") == null;
    var icon = first_time ? "fa-cookie" : 'fa-cookie-bite';
    $cookieIcon.append($('<i class="fa-solid ' + icon+ '"></i>'));
    $('body').append($cookieIcon);

    //per ogni check sul tipo dei cookie, metto che setta tutti i cookie di quel tipo
    $(".check-cookie-type").on("click", function () {
        var type = $(this).data("cookie-type");
        var isChecked = $(this).is(":checked");
        //aggiorno i toggle dei singoli
        $(`.cookie-checkbox[data-cookie-type="${type}"]`).prop('checked', isChecked);
    });

    /***BOTTONI***/
    //accetta tutti i cookie
    $("#accept-all-cookie-large").on("click", function () {
        $('.cookie-checkbox').prop('checked', true);
        $(`.check-cookie-type`).prop('checked', true);
        sendUpdatedCookiePreferencies();
    });

    //rifiuta tutti i cookie
    $("#deny-all-cookie-large").on("click", function () {
        $('.cookie-checkbox').prop('checked', false);
        $(`.check-cookie-type`).prop('checked', false);
        sendUpdatedCookiePreferencies()
    });

    //accetta i cookie selezionati
    $("#accept-personalized-cookie-large").on("click", function () {
        sendUpdatedCookiePreferencies();
    });

    /***BOTTONI ICONE***/
    //accetta tutti i cookie
    $("#accept-all-cookie-icon").tooltip({
        show: { effect: "fadeIn", duration: 300 }, // Effetto di visualizzazione
        hide: { effect: "fadeOut", duration: 300 }  // Effetto di nascondimento
    }).on("click", function () {
        $('.cookie-checkbox').prop('checked', true);
        $(`.check-cookie-type`).prop('checked', true);
        sendUpdatedCookiePreferencies();
    });

    //rifiuta tutti i cookie
    $("#deny-all-cookie-icon").tooltip({
        show: { effect: "fadeIn", duration: 300 }, // Effetto di visualizzazione
        hide: { effect: "fadeOut", duration: 300 }  // Effetto di nascondimento
    }).on("click", function () {
        $('.cookie-checkbox').prop('checked', false);
        $(`.check-cookie-type`).prop('checked', false);
        sendUpdatedCookiePreferencies()
    });

    //accetta i cookie selezionati
    $("#accept-personalized-cookie-icon").tooltip({
        show: { effect: "fadeIn", duration: 300 }, // Effetto di visualizzazione
        hide: { effect: "fadeOut", duration: 300 }  // Effetto di nascondimento
    }).on("click", function () {
        sendUpdatedCookiePreferencies();
    });

    //animo le icone dei collpsable
    $('#cookie-dettagli-collapsable-container [data-bs-toggle="collapse"]').each(function () {
        // Crea le variabili per l'icona e il collapse
        var $toggleElement = $(this);
        var $icon = $toggleElement.find('i'); // Se l'icona è dentro il toggle element
        var $collapse = $($toggleElement.attr('href')); // Ottieni l'elemento collapse associato

        // Inizializza l'icona per partire chiusa
        $icon.css('transform', 'rotate(0deg)'); // Freccia giù all'inizio

        // Gestisci gli eventi di collapse
        $collapse.on('show.bs.collapse', function () {
            $icon.css('transform', 'rotate(180deg)'); // Ruota in su quando aperto
        });

        $collapse.on('hide.bs.collapse', function () {
            $icon.css('transform', 'rotate(0deg)'); // Riporta in giù quando chiuso
        });
    });

    //chiamo il servizio che riempe l'informativa
    callService("getAllCookiesDescription", [],
        function (response) {
            loadCookieBanner(response.value);
            //motro il bottone
            $("#cookie-icon").show();
            //se non ce il cookie delle preferenze, lo mostro
            if (first_time) $('#cookieModal').modal('show');
        }, //se ho risposte, creo banner
        function (response) { }, //ERROR :se non ci sono cookie, allora non mostro nulla
        function (response) { //FAIL
            createToast(response.result, "Impossibile ottenere informativa sui Cookie", response.additional_info);
        }
    );

})