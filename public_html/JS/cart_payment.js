$(document).ready(function () {
  //aggiungo il listener al form per il pagamento
  $('#paymentForm').on('submit', function (event) {
    event.preventDefault(); // Impedisce il reroll della pagina
    var $button = $("#buyBtn").attr("disabled",true);

    callService('purchaseCart', [], 
      function (response) {
        //scarico la ricevuta
        var id_purchase = response.value;
        callServiceDownloader("downloadReceipt", [id_purchase],
          function () { createToast("OK", "Acquisto in corso", "Stiamo verificando l'acquisto...");},
          function () { },
          function () {createToast("OK", "Acquisto completato", "Grazie per averci scelto!");}
        );

      //svuota il cart e disabilita il bottone tramite css
      $("#buyBtn").attr("disabled", false);
      cartEmpty('Acquisto completato!');
      $('#cart-badge').hide();
    },
    function(response){ //ERROR
      if (response.additional_info == "EMPTY")
        createToast(response.result, "Impossibile procedere all' acquisto","Al momento il carrello é vuoto");
      else 
        createToast(response.result, "Impossibile procedere all' acquisto", response.additional_info);
      $("#buyBtn").attr("disabled", false);
    },
    function (response){//FAIL
      createToast(response.result, "Impossibile acquistare il carrello", response.additional_info);
      $("#buyBtn").attr("disabled", false);
    });
  });

  //aggiungo il listener al bottone per il pagamento
  $('#buyBtn').attr('title', "Verifica al pagamento al momento disabilitata")
    .tooltip({
        placement: 'top',
        show: { effect: "fadeIn", duration: 300 }, // Effetto di visualizzazione
        hide: { effect: "fadeOut", duration: 300 }  // Effetto di nascondimento
    })
  
});