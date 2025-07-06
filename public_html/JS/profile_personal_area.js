// Funzione per aprire il modal con un campo specificato e un suggerimento (placeholder)
function openModal(field, currentValue) {
    $('#newInput').val(''); // Lascia vuoto il campo di input
    $('#newInput').attr('placeholder', currentValue); // Imposta il placeholder con il valore corrente
    $('#editModalLabel').html("Modifica " + field);
    $('#editModalFieldLabel').html("Nuovo " + field);
    $('#editModal').modal('show');
}

// Funzione per aprire il modal con il valore corrente del genere
function openGenderModal(currentValue) {
    $('#genderSelectInput').val(''); // Imposta il valore corrente nel select
    $('#genderSelectInput').attr('placeholder', currentValue); // Imposta il placeholder con il valore corrente
    $('#editGenderModalLabel').html("Modifica Genere"); // Imposta il titolo del modal
    $('#editGenderModal').modal('show'); // Mostra il modal
}

//Funzione per aggiornare il valore di un campo
function updateValue(serviceName, inputId, updateElementClass, field, notNullRequired = false, tagModal) {
    var newValue = $(inputId).val();

    //dove mostro il risultato della richiesta
    var $divResult = $("#showprofileResult");

    var $tagModal = $(tagModal);

    // Controlla se il valore è richiesto e non è fornito
    if (notNullRequired && !newValue) {
        $tagModal.modal('hide');
        $(updateElementClass).html(newValue);
        $divResult.html("<div class='alert alert-danger'> Il campo non può essere vuoto </div>");
        scrollToElement($divResult);
        return;
    }

    // Chiamata al servizio per aggiornare il valore
    callService(serviceName, [newValue],
        function (response) { // OK
            $tagModal.modal('hide');
            $(updateElementClass).html(newValue);
            $divResult.html("<div class='alert alert-success'> Campo modificato con successo </div>");
            scrollToElement($divResult);
        },
        function (response) { // ERROR
            $tagModal.modal('hide');
            $divResult.html("<div class='alert alert-danger'> Errore nella modifica del campo:<br>" + response.value[field] + "</div>");
            scrollToElement($divResult);
        },
        function (response) { // FAIL
            $tagModal.modal('hide');
            createToast("FAIL", "Impossibile aggiornare il campo", response.value);
        }
    );
}

// Funzione per aggiornare la password dell'utente
function updatePassword() {
    var oldPassword = $('#oldPassword').val();
    var newPassword = $('#newPassword').val();
    var confirmPassword = $('#confirmPassword').val();

    var $divResult = $("#showprofileResult");
    var $tagModal = $('#passwordModal');

    // Controlla se le password corrispondono
    if (newPassword != confirmPassword) {
        $tagModal.modal('hide');
        $divResult.html("<div class='alert alert-danger'>Le password non corrispondono</div>");
        return;
    }

    // Controlla se la nuova password è vuota
    if (newPassword == "") {
        $tagModal.modal('hide');
        $divResult.html("<div class='alert alert-danger'>Devi indicare una nuova password</div>");
        scrollToElement($divResult);
        return;
    }

    // Chiamata al servizio per aggiornare la password
    callService("updateUserPassword", [newPassword, oldPassword],
        function (response) { // OK
            $tagModal.modal('hide');
            $divResult.html("<div class='alert alert-success'>Password modificata con successo</div>");
            scrollToElement($divResult);
        },
        function (response) { // ERROR
            var errorMessage = "Errore nella modifica della password: <br>";

            if (response.additional_info == "WRONG PASSWORD") 
                errorMessage = errorMessage + "La vecchia password non è corretta";
            else if (response.additional_info == "DECLINED BY DB") 
                errorMessage = errorMessage + response.value.password;
            
            $tagModal.modal('hide');
            $divResult.html("<div class='alert alert-danger'>" + errorMessage + "</div>");
            scrollToElement($divResult);
        },
        function (response) { // FAIL
            $tagModal.modal('hide');
            createToast("FAIL", "Impossibile aggiornare la password", response.value);
        }
    );
}

function becomeAdmin(event) {
    event.preventDefault();
    // Aggiungi il contenuto con la GIF e un pulsante per chiuderla
    $('#showprofileResult').html("<img src='https://i.giphy.com/media/v1.Y2lkPTc5MGI3NjExd2prNGVscW00eGZyZmQ2NWI4cXNwbHk2N3BqYWg5dDVyMDltNjZkZSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/NVqr9qwZLrEHVJ6Yms/giphy.gif' alt='Immagine scherzo quando si prova a diventare admin' id='adminGif'>");

    // Imposta un timer per rimuovere la GIF dopo la durata specificata (es. 5 secondi)
    setTimeout(function () {
        $('#adminGif').fadeOut('slow', function () {
            $(this).remove(); // Rimuove l'elemento dal DOM
        });
    }, 5000); // 5000 millisecondi = 5 secondi
}

//con un richiesta ajax chiedo al server i dati dell'utente
function uploadUserInfo() {
    var $showprofileResult = $("#showprofileResult");
    return callService("getUserInfo", [],
        function (response) { // OK
            $('#personalAreaContentCollapse').on('show.bs.collapse', function () {
                $('#personalAreaDropdownIcon').removeClass('bi-chevron-down').addClass('bi-chevron-up');
            });
        
            $('#personalAreaContentCollapse').on('hide.bs.collapse', function () {
                $('#personalAreaDropdownIcon').removeClass('bi-chevron-up').addClass('bi-chevron-down');
            });
            
            // Popola i campi con i dati dell'utente
            for (var key in response.value)
                $('.user_' + key).html(response.value[key]);

            // Controlla il ruolo dell'utente e mostra contenuti diversi
            if (response.role == 'Admin') {
                $showprofileResult.html(
                    "<div class='alert alert-info'>" +
                    "<strong>Sei un Dio del sito!</strong><br>" +
                    "Il sito intero si inchina al tuo potere e alla tua grandezza. 👑<br>" +
                    "Grazie per governare con saggezza e benevolenza! 🙌" +
                    "</div>"
                );
                return
            } //else

            // Crea il pulsante per mostrare la GIF
            $showprofileResult.html("<a class='btn btn-purple btn-purple-focus-purple text-center' href='#' id='adminLink'>Diventa Amministratore🥸</a>");

            // Gestisci il click sul pulsante per mostrare la GIF
            $('#adminLink').on("click", becomeAdmin);
        },
        function (response) {},//ERROR, non puó succedere
        function (response) { // FAIL
            $showprofileResult.html("<div class='alert alert-danger'>Errore nel caricamento dei dati personali</div>");
            $("#personalAreaContentCollapse").find("button").prop("disabled", true).removeClass("btn-purple btn-edit").addClass("btn-edit-gray");
            createToast("FAIL", "Impossibile visualizzare il profilo", response.value);
        },
        true
    );
}

$(document).ready(function () {
    // Chiamata al servizio per ottenere le informazioni dell'utente
    $.when(
        uploadUserInfo()
    ).done(function () {
        // Nasconde il caricamento e mostra il contenuto del profilo
        $("#showprofileLoadingSpinner").hide();
        $("#profileContent").show();

        // Aggiungi le funzioni ai rispettivi pulsanti con il valore corrente come placeholder
        $('#editName').click(function() {
            var currentValue = $('.user_name').first().text().trim(); 
            openModal("Nome", currentValue);
        });

        $('#editSurname').click(function() {
            var currentValue = $('.user_surname').first().text().trim();
            openModal("Cognome", currentValue);
        });

        $('#editPhoneNumber').click(function() {
            var currentValue = $('.user_phone_number').first().text().trim();
            openModal("Telefono", currentValue);
        });

        $('#editFiscalCode').click(function() {
            var currentValue = $('.user_fiscal_code').first().text().trim();
            openModal("Codice Fiscale", currentValue);
        });

        $('#editEmail').tooltip({ // è disabilitato
            show: { effect: "fadeIn", duration: 300 }, // Effetto di visualizzazione
            hide: { effect: "fadeOut", duration: 300 }  // Effetto di nascondimento
        });

        $('#editPassword').click(function () {
            $('#oldPassword').val('');
            $('#newPassword').val('');
            $('#confirmPassword').val('');
            $('#passwordModal').modal('show');
        });

        $('#editGender').click(function() {
            var currentValue = $('.user_gender').first().text().trim();
            openGenderModal("Gender", currentValue);
        });

        $('#saveBtn').click(function () {
            switch ($('#editModalLabel').html()) {
                case "Modifica Nome":
                    updateValue("updateUserName", "#newInput", ".user_name", "name", true, "#editModal");
                    break;
                case "Modifica Cognome":
                    updateValue("updateUserSurname", "#newInput", ".user_surname", "surname", true, "#editModal");
                    break;
                case "Modifica Telefono":
                    updateValue("updateUserPhoneNumber", "#newInput", ".user_phone_number", "phone_number", false, "#editModal");
                    break;
                case "Modifica Codice Fiscale":
                    updateValue("updateUserFiscalCode", "#newInput", ".user_fiscal_code", "fiscal_code", false, "#editModal");
                    break;
            }
        });

        $('#saveGenderBtn').click(function () {
            updateValue("updateUserGender", "#genderSelectInput", ".user_gender", "gender", false, "#editGenderModal");
        });

        $('#savePasswordBtn').click(updatePassword);
    });
});