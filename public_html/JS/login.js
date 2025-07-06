function doLogin(e) {
    e.preventDefault();
    // disabilito il bottone e pulisco l'ultimo risultato
    $("#loginButton").prop("disabled", true).html("Attendere...").removeClass("btn-purple").addClass("btn-secondary");
    $("#loginResult").html("");

    email = $("#email").val();
    password = $("#password").val();
    rememberme = $('#rememberMe').is(':checked');

    $loginResult = $("#loginResult");

    callService("loginCredentials", [email, password],
        function (response) { // OK
            if (rememberme) 
                callService("rememberMe", [], 
                    function(response) {}, //TUTTO OK
                    function (response) {},//impossibile
                    function (response) {
                        createToast(response.result, "Non é stato possibile ottenere un token di logging", response.additional_info, 0);
                    }                      
                );
            //ridireziono
            last_page = getCookie('last_page_visited')
            window.location.href = last_page ? last_page :"index.php";
        },
        function (response) { // ERROR, creadenziali errate
            $loginResult.html("<div class='alert alert-danger'> Credenziali errate. </div>");
            scrollToElement($loginResult);
            $("#loginButton").prop("disabled", false).html("Accedi").removeClass("btn-secondary").addClass("btn-purple");
        },
        function (response) { // FAIL
            $("#loginButton").html("Errore").removeClass("btn-secondary").addClass("btn-danger");
            createToast(response.result, "Impossbile richiedere il login al server", response.additional_info, 0);
        }
    );
}

//appena la pagina é pronta, aggiungo la funzione al tag
$(document).ready(function () {
    $("#loginForm").submit(doLogin);
});