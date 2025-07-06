/*COSTANTI DI CONFIGURAZIONE*/
var GATEWAY_URL="https://saw21.dibris.unige.it/~S5175710/GATEWAY.php";
var SESSION_COOKIE = "SESSION_ID"; //da utilizzare
var TOKEN_LOGIN = "__Host-LoginToken"; //da utilizzare

function callServiceDownloader(
  serviceName,
  parameters,
  notifyStart  = function (name)         {createToast("OK",  "Iniziato il download",                         "Potrebbe volerci un pó, mettiti comodo ☕",15000)},
  notifyMiddle = function ()             {createToast("OK",  "Download in corso...",                         "Grazie per la pazienza, eccoti un'altra tazza☕", 15000) },
  notifyEnd    = function (name,$button) {createToast("OK", name+" Scaricato correttamente " ,              "💃Tempo di Ballare💃", 0);                    $button.prop("disabled", false);},
  notifyFail   = function ($button)      {createToast("FAIL","Sembra ci sia qualche problema con il server", "Contatta un admin per maggiori informazioni");$button.prop("disabled", false);},
  $button = $() //di default nessuno
) {
  var request_data = {
    service: serviceName,
    parameters: parameters
  }
  // Inizializza una variabile per l'ID dell'intervallo
  let pollingIntervalId;
  let pollingTime = 15000;

  $.ajax({
    url: GATEWAY_URL,
    method: 'POST', // Usa il metodo POST
    data: request_data, // Converti i dati in una stringa JSON
    xhrFields: {
      responseType: 'blob' // Richiedi la risposta come Blob per verificare l'intestazione
    },
    beforeSend: function () {
      notifyStart();
      // Avvia il polling ogni 15 secondi
      pollingIntervalId = setInterval(notifyMiddle, pollingTime);
    },
    success: function (blob, textStatus, xhr) {
      try {
        // Intercetta il filename dall'intestazione Content-Disposition
        const disposition = xhr.getResponseHeader('Content-Disposition');
        let filename = 'downloaded_file'; // Nome predefinito

        if (disposition) {
          const filenameMatch = disposition.match(/filename="(.+)"/);
          if (filenameMatch && filenameMatch[1]) {
            filename = filenameMatch[1];
          }
        }

        // Gestisci i dati binari
        const blobUrl = URL.createObjectURL(blob);

        // Crea un elemento link temporaneo
        const link = document.createElement('a');
        link.href = blobUrl;
        link.download = filename; // Usa il nome del file estratto
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Pulisci l'URL del Blob
        //URL.revokeObjectURL(blobUrl);
        
        // Ferma il polling una volta completato il download
        console.log("File " + filename + " scaricato");
        notifyEnd(filename, $button)
        clearInterval(pollingIntervalId);
      } catch (e) {
        // Gestione degli errori
        notifyFail();
        clearInterval(pollingIntervalId);
      }
    },
    error: function (xhr, textStatus, errorThrown) {
      // Gestione degli errori
      notifyFail($button);
      clearInterval(pollingIntervalId);
    }
  });
}

function callService(serviceName, parameters,
  successOK = function () {},
  successERROR = function () {},
  successFAIL = function () {},
  requestVerbose = false
) {
  var request_data = {
    service: serviceName,
    parameters: parameters,
    verbose: requestVerbose
  }

  return $.ajax({
    url: GATEWAY_URL,
    type: "POST",
    data: request_data,
    dataType: "json",
    success: function (response) {
      console.log({ request: request_data, response: response });

      // Gestione delle risposte in base al risultato
      if (response.result === "OK") {
        successOK(response);
      } else if (response.result === "ERROR") {
        successERROR(response);
      } else { // response.result === FAIL
        successFAIL(response);
      }
    },
    error: function (xhr, status, error) {
      console.error(xhr.responseText);
      createToast("ERROR", "AJAX FAIL", "Si è verificato un errore nella chiamata al servizio.<br>Riprova più tardi.");
    }
  });
}

function defaultFailMessage(response) {
  let message = "<div class='alert alert-danger'> Qualcosa è andato storto :( <br>"
    + response.additional_info;

  if (response.value !== null) message += "<br>" + response.value;
  return message + "</div>";
}


// Funzione per rimuovere i messaggi di errore
function removeError($inputElement) {
  $inputElement.removeClass('is-invalid');
  $inputElement.next('.error-message').remove();
}

// Funzione per aggiungere i messaggi di errore
function addError($inputElement, message) {
  $inputElement.addClass('is-invalid');
  $inputElement.after('<span class="error-message text-danger">' + message + '</span>');
}

// Funzione per tardare una funzione
function debounce(func, wait) {
  let timeout;
  return function () {
    const context = this, args = arguments;
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(context, args), wait);
  };
}

//dato un oggetto, scrolla la pagina fino a li (lo attacca in cima)
/*function scrollTo($tag) {
  $(window).scrollTop($tag.offset().top);
}*/

//dato un oggetto, scrolla la pagina fino a li (lo attacca in fondo)
function scrollToElement($tag) {
  var padding = 30;//valore scelto da me
  var elementTop = $tag.offset().top;
  var windowHeight = $(window).height();
  var elementHeight = $tag.outerHeight();
  var scrollPosition = elementTop - windowHeight + elementHeight + padding;

  $(window).scrollTop(scrollPosition);
}

function capitalizeFirstLetter(string) {
  if (!string) return string; // Gestisce il caso in cui la stringa sia vuota o null
  return string.charAt(0).toUpperCase() + string.slice(1);
}

// Funzione per ottenere la data odierna (in formato 2024-08-15)
function getToday() {
  return new Date().toISOString().split('T')[0];
}

// Funzione per ottenere la data odierna (in formato 2024-08-15) più un numero di giorni
function getTodayPlusDays(days) {
  var today = new Date();
  today.setDate(today.getDate() + days);
  return today.toISOString().split('T')[0];
}

// Funzione per ottenere la differenza in giorni tra due date
function getDaysDifference(date1, date2) {
  var diffTime = Math.abs(new Date(date2) - new Date(date1));
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
}

//se non ho trovaato prodotti in courses.php o events.php
function notFoundProductsError() {
  // Se non ci sono corsi del tipo selezionato
  var $coursesGrid = $('#products-container');
  $coursesGrid.empty(); // Pulisce eventuali elementi esistenti

  // Segnala che non ci sono corsi del tipo selezionato
  var $div = $('<div class="col-12"></div>');
  var $p = $('<h3></h3>')
    .text('Non ci sono prodotti disponibili per i filtri selezionati')
    .addClass('text-center text-danger font-weight-bold alert alert-warning');

  $div.append($p);
  $coursesGrid.append($div);

  // Applicare l'effetto di fade
  $div.hide(); // Inizialmente nascondere il div
  $div.fadeIn(200); // Mostrare il div con effetto fade di 0.2 secondi (200 millisecondi)
}




// Funzione per aggiornare il countdown in tempo reale
function createRealTimeCountdown(expirationDate) {
  // Crea l'elemento del countdown
  var $countdownDiv = $('<div class="product-icon countdown-icon expiration-icon"></div>');

  function updateCountdown() {
    var now = new Date();
    var endDate = new Date(expirationDate);
    var diffSeconds = Math.ceil((endDate - now) / 1000); // Differenza in secondi

    if (diffSeconds <= 0) {
      $countdownDiv.text('Expired :(');
      return; // Ferma l'aggiornamento quando scade
    }

    var days = Math.floor(diffSeconds / (60 * 60 * 24));
    var hours = Math.floor((diffSeconds % (60 * 60 * 24)) / (60 * 60));
    var minutes = Math.floor((diffSeconds % (60 * 60)) / 60);
    var seconds = diffSeconds % 60;

    $countdownDiv.text(days + 'd ' + hours + 'h ' + minutes + 'm ' + seconds + 's');
    setTimeout(updateCountdown, 1000); // Aggiorna ogni secondo
  }

  // Inizializza il countdown
  updateCountdown();

  return $countdownDiv;
}

// Funzione per aggiungere le icone di scadenza e sconto
function addExpirationAndDiscountIcons($container, row, limit) {
  var hasExpirationDate = (row.expiration_date !== null && row.expiration_date < getTodayPlusDays(limit));

  // Aggiungi l'icona di scadenza se necessario
  if (hasExpirationDate) {
    var $expirationIcon = createRealTimeCountdown(row.expiration_date);
    $container.append($expirationIcon);
  }

  // Aggiungi l'icona di sconto se necessario
  if (row.discount !== null && row.discount > 0) {
    var $discountIcon = $('<div class="product-icon discount-icon"></div>').text(row.discount + '% Off');
    $discountIcon.addClass(hasExpirationDate ? 'below-expiration' : 'top-left');
    $container.append($discountIcon);
  }
}




function formatDate(date) {
  // formato data "gg/mm/aaaa"
  date = new Date(date);
  date = date.getDate() + '/' + (date.getMonth() + 1) + '/' + date.getFullYear();
  return date;
}

/**
 * Ottieni il valore di un cookie dato il nome
 * @param {string} name - Il nome del cookie
 * @returns {string|null} - Il valore del cookie se esiste, altrimenti null
 */
function getCookie(name) {
  // Crea una stringa di ricerca per il cookie
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);

  if (parts.length === 2) {
    // Restituisce il valore del cookie decodificato
    return decodeURIComponent(parts.pop().split(';').shift());
  }

  // Restituisce null se il cookie non esiste
  return null;
}

/**
 * Elimina un cookie dato il nome
 * @param {string} name - Il nome del cookie
 * @param {string} path - Il percorso del cookie (opzionale)
 */
function deleteCookie(name, path = '/') {
  // Imposta la data di scadenza nel passato per eliminare il cookie
  document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=${path};`;
}

function setupDropdown(filterName) {
  var $header = $('#filtro-' + filterName + ' h4');
  var $icon = $header.find('i.bi-chevron-down');
  var $collapse = $('#collapseFiltro' + capitalizeFirstLetter(filterName));

  // Inizializza l'icona per partire chiusa
  $icon.css('transform', 'rotate(0deg)'); // Freccia giù all'inizio

  $collapse.on('show.bs.collapse', function () {
    $icon.css('transform', 'rotate(180deg)'); // Ruota in su quando aperto
  });

  $collapse.on('hide.bs.collapse', function () {
    $icon.css('transform', 'rotate(0deg)'); // Riporta in giù quando chiuso
  });
}

// Funzione per creare un elemento prodotto
function createProductItem(product) {
  var $div = $('<div class="col-lg-4 col-md-4 products-item"></div>');
  if (product.id_product) var $a = $('<a href="product.php?id=' + product.id_product + '" title="More Details" class="details-link"></a>');
  else var $a = $('<a href="#" title="More Details" class="details-link"></a>');
  var $imgContainer = $('<div class="image-container"></div>');
  if (!product.id_product) $imgContainer.css('border', '5px solid var(--bs-danger-border-subtle) !important');
  var $img = $('<img class="img-fluid img-corso">').attr('src', product.poster_path).attr('alt', product.name);
  var $div2 = $('<div class="products-info"></div>');
  var $a2 = $('<a tabindex"-1" href="product.php?id=' + product.id_product + '"></a>').text(product.name);

  // Utilizza la funzione per aggiungere le icone di scadenza e sconto
  addExpirationAndDiscountIcons($div, product, 30);

  $imgContainer.append($img);
  $div2.append($a2);
  $a.append($imgContainer);
  $div.append($a).append($div2);

  // restituisco il prodotto creato
  return $div;
}

// Funzione per creare un elemento prodotto dummy con messaggio di errore
function createErrorProductItem(response, errorMsg) {
  var $coursesGrid = $('#products-container');
  $coursesGrid.empty(); // Pulisce eventuali elementi esistenti

  // Crea elementi dummy per il messaggio di errore
  for (var i = 0; i < 3; i++) { // Crea 3 elementi dummy
      var dummyProduct = {
          id_product: null, // Nessun ID prodotto
          poster_path: 'MULTIMEDIA/imgs/no-icon.png', // Nessun percorso immagine
          name: 'Errore nel caricamento' // Nessun nome
      };
      var $dummyProductItem = createProductItem(dummyProduct);
      $coursesGrid.append($dummyProductItem);
  }

  // Nasconde il caricamento e mostra il contenuto dei products
  $("#loadingSpinnerProducts").hide();
  $("#products-container").show();

  // Mostra il toast di errore
  createToast(response.result, errorMsg, response.additional_info, 15000);
}



/**toast**/

function createToast(type, title, description, delay = 10000) {
  // Definire le classi di icona e bordi in base al tipo
  let iconClass;
  let toastClass;

  switch (type) {
    case 'OK':
      iconClass = 'fa-check-circle';
      toastClass = 'toast-ok';
      break;
    case 'ERROR':
      iconClass = 'fa-exclamation-circle';
      toastClass = 'toast-error';
      break;
    case 'FAIL':
      iconClass = 'fa-times-circle';
      toastClass = 'toast-fail';
      break;
    default:
      iconClass = 'fa-info-circle'; // Default icon
      toastClass = '';
  }

  // Creazione del contenitore del toast
  var toastElement = $('<div>').addClass('toast border').addClass(toastClass).attr({
    'role': 'alert',
    'aria-live': 'assertive',
    'aria-atomic': 'true'
  });

  // Creazione dell'header del toast
  var toastHeader = $('<div>').addClass('toast-header');

  // Creazione dell'icona usando FontAwesome
  var icon = $('<i>').addClass('fa ' + iconClass + ' me-2');

  var strong = $('<strong>').addClass('me-auto').text(title);
  var countdown = $('<small>').addClass('toast-countdown');
  var btnClose = $('<button>').attr({
    'type': 'button',
    'class': 'btn-close',
    'data-bs-dismiss': 'toast',
    'aria-label': 'Close'
  });

  // Assemblare l'header del toast
  toastHeader.append(icon, strong, countdown, btnClose);

  // Creazione del corpo del toast
  var toastBody = $('<div>').addClass('toast-body').html(description);

  // Assemblare il toast
  toastElement.append(toastHeader, toastBody);

  // Aggiungere il toast al contenitore
  $('#toastContainer').append(toastElement);

  // Mostrare il toast con la durata di scomparsa automatica
  option = {
    autohide: false
  }

  // Conto alla rovescia se il delay è maggiore di 0
  if (delay > 0) {
    var countdownTime = delay / 1000; // Convertire il delay in secondi
    var countdownInterval = setInterval(function () {
      countdownTime--;
      if (countdownTime <= 0) {
        clearInterval(countdownInterval);
        countdown.text('0s');
      } else {
        countdown.text(countdownTime + 's');
      }
    }, 1000);
    option = {
      autohide: true,
      delay: delay // Imposta il tempo di scomparsa automatico (in millisecondi)
    }
  }
  var toast = new bootstrap.Toast(toastElement[0], option);
  toast.show();
}

/***FUNZIONI COMUNI PER LE RECENSIONI***/
function createStarCounter(value) { // Restituisce un elemento del DOM che descrive in formato visivo una valutazione [0,5]
  var result = [];
  for (var i = 1; i <= 5; i++) {
    if (i <= Math.floor(value))
      result.push($('<i>').addClass('bi bi-star-fill').css('color', '#6f42c1')); // Stella piena viola
    else if (i === Math.ceil(value) && value % 1 !== 0)
      result.push($('<i>').addClass('bi bi-star-half').css('color', '#6f42c1')); // Mezza stella viola
    else
      result.push($('<i>').addClass('bi bi-star-fill').css('color', '#e4e5e9')); // Stella vuota
  }
  return result;
}

function updateStars(exactValue) { // Funzione per aggiornare le stelle nel modal delle review
  const starRating = $('#starRating');
  const starValue = $('#starValue');
  const roundedValue = exactValue < 0.5 ? 0.5 : Math.round(exactValue * 2) / 2; // arrotondamento a 0,5
  const stars = starRating.children();

  stars.each(function (index) {
    const starIndex = index + 1;
    if (starIndex <= roundedValue) {
      $(this).addClass('bi-star-fill').removeClass('bi-star bi-star-half').css('color', 'var(--accent-color)');;
    } else if (starIndex - roundedValue === 0.5) {
      $(this).addClass('bi-star-half').removeClass('bi-star bi-star-fill').css('color', 'var(--accent-color)');;
    } else {
      $(this).addClass('bi-star').removeClass('bi-star-fill bi-star-half').css('color', 'var(--accent-color)');;
    }
  });

  // Aggiorna il valore numerico
  starValue.val(roundedValue.toFixed(1));
}

function modalReviewManager() {
  const starRating = $('#starRating');
  const starValue = $('#starValue');
  let isSelected = false; // Variabile per tracciare se è stato fatto clic
  //inizio con 2,5 di default
  updateStars(2.5);

  // Evento mousemove per il drag delle stelle
  starRating.on('mousemove', function (e) {
    if (!isSelected) {
      const offset = $(this).offset();
      const width = $(this).width();
      const relX = e.pageX - offset.left;
      const percentage = relX / width;
      const exactValue = Math.min(5, Math.max(0, percentage * 5));

      updateStars(exactValue);
    }
  });

  // Evento click per selezionare le stelle
  starRating.on('click', function (e) {
    isSelected = true; // Registra che l'utente ha fatto clic
    const offset = $(this).offset();
    const width = $(this).width();
    const relX = e.pageX - offset.left;
    const percentage = relX / width;
    const exactValue = Math.min(5, Math.max(0, percentage * 5));

    updateStars(exactValue);
  });

  // Evento mouseleave per resettare la selezione se non si è cliccato
  starRating.on('mouseleave', function () {
    if (!isSelected) {
      const currentValue = parseFloat(starValue.val());
      updateStars(currentValue); // Ripristina la selezione precedente
    }
  });
}

/***filter di courses e svents***/
function resetFilters(name,value) {
  localStorage.removeItem('selectedProductType');
  localStorage.removeItem('selectedProductDiscipline');
  localStorage.removeItem('selectedProductLevel');
  localStorage.setItem('selectedProduct'+name, value);
}

//aggiorna il valore sul filtro della ricerca dei prodotti
function reloadFilterSummary($a, id) {
  //aggiorno la scritta sul filtro
  var val = $a.find("span").text();
  var $span = $(id + " > h4 > span");
  if (val == "Tutti")
    $span.fadeOut(300, function () {
      // La funzione di callback viene eseguita dopo che l'elemento è stato nascosto
      $span.text(""); // Opzionale: Cancella il testo se necessario
    });
  else
    $span.fadeOut(300, function () {
      // Modifica il testo e poi mostra l'elemento di nuovo
      $span.text(val);
      $span.fadeIn(300); // Mostra l'elemento con l'animazione in 300 millisecondi
    });
}

function checkExpiration(expiration) {
  if (expiration == null) return "";
  expiration = new Date(expiration);

  var days = Math.floor((expiration - new Date()) / (1000 * 60 * 60 * 24));

  if (days < 0)
    return "⚠️SCADUTO⚠️";
  else if (days == 0)
    return "⚠️SCADE OGGI⚠️";
  else if (days == 1)
    return "⚠️SCADE DOMANI⚠️";
  else if (days < 30)
    return "⚠️SCADE TRA " + days + " GIORNI⚠️";
  return "";
}