// Funzione per verificare l'email sia corretta (lato server)
function verifyEmail() {
    let $inputElement = $(this);
    let emailValue = $inputElement.val();

    //se é vuota, non faccio nulla
    if (!emailValue) {
        removeError($inputElement);
        return;
    }

    // Invia ajax per controllare la mail
    callService("checkValidEmail", [emailValue],
        function (response) { // OK
            removeError($inputElement); // Rimuove eventuali errori precedenti
        },
        function (response) { // ERROR
            removeError($inputElement); // Rimuove eventuali errori precedenti
            addError($inputElement, response.value.email); // Aggiungi il messaggio di errore specifico accanto al campo
        },
        function (response) {//FAIL
            removeError($inputElement);
            addError($inputElement, "Verifica momentaneamente disabilitata"); // Aggiungi il messaggio di errore specifico accanto al campo
        }
    );
}

// Funzione per la registrazione
function handleRegistrationForm(event) {

    event.preventDefault();
    
    let name = capitalizeFirstLetter($("#name").val());
    let surname = capitalizeFirstLetter($("#surname").val());
    let email = $("#email").val();
    let password = $("#password").val();
    let phone_number = $("#phone_number").val();
    let fiscal_code = $("#fiscal_code").val();
    let gender = $("#gender").val();

    let $repeatpasswordtag = $("#repeatPassword");
    if (password !== $repeatpasswordtag.val()) {
        removeError($repeatpasswordtag);
        addError($repeatpasswordtag, "La password ripetuta non coincide");
        return;
    }

    // Rimuovo il messaggio di errore e tutti i segnali di is-invalid
    $('.error-message').remove();
    $('.is-invalid').removeClass('is-invalid');

    $registrationResult = $("#registrationResult");

    $("#btnSubmit").text("Verifica...");
    callService("signUp", [name, surname, email, password, phone_number, fiscal_code, gender, true],
        function (response) { // OK
            $("#btnSubmit").text("Ridirezione...");
            last_page = getCookie('last_page_visited');
            window.location.href = last_page ? last_page : "index.php";
        },
        function (response) { // ERROR :response. value é un json che associa ogni ogni campo errato al suo errore
            // Itera su ogni chiave e valore dell'oggetto response.value e segnala gli errori
            $.each(response.value, function (key, val) {
                let $inputElement = $('#' + key);
                addError($inputElement, val);
            });

            $registrationResult.html(
                "<div class='alert alert-danger'> Verifica i campi evidenziati e riprova. </div>"
            );
            scrollToElement($registrationResult);
            $("#btnSubmit").text("Registrati");
        },
        function (response) { // FAIL
            $($registrationResult).html("<div class='alert alert-danger'>Errore nella creazione di un nuovo profilo</div>");
            scrollToElement($registrationResult);
            createToast("FAIL", "Impossibile creare il profilo", response.value);
            $("#btnSubmit").text("Registrati");
        }
    );
}

//appena la pagina é pronta, aggiungo la funzione ai relativi tag
$(document).ready(function () {
    $('#email').on('input', debounce(verifyEmail, 500));
    $("#registrationForm").on('submit',handleRegistrationForm);
});
