function uploadDanceDisciplines() {
  var $danceDisciplines = $('#collapseFiltroDisciplina');
  // Con una richiesta AJAX ottengo le discipline, in modo da usarle come filtro
  return callService("getAllDanceDisciplines", [], function (response) { // OK
    // Aggiungo alla lista un elemento per ogni disciplina
    $danceDisciplines.append($('<a href="#"><i class="bi bi-arrow-right-circle"></i><span>Tutte</span></a>'));
    response.value.forEach(function (row) {
      var $li = $('<a href="#" data-filter="' + row.type + '"><i class="bi bi-arrow-right-circle"></i><span class="capitalizeFirstLetter">' + row.type + '</span></a>');
      $danceDisciplines.append($li);
    });

    // Gestisce il clic sugli elementi <a>
    $danceDisciplines.on('click', 'a', function (event) {
      event.preventDefault(); // Previene il comportamento predefinito del link

      reloadFilterSummary($(this), "#filtro-disciplina");

      $danceDisciplines.find('a').removeClass('active');
      $(this).addClass('active');
      reloadProducts();
    });

    //rendo active il valore indicato in localstorage, altrimenti il primo tag che trovo
    var selectedProductDiscipline = localStorage.getItem('selectedProductDiscipline')
    //se ho trovato il tag corretto
    if (selectedProductDiscipline && ($tag = $danceDisciplines.find('a[data-filter="' + selectedProductDiscipline + '"]')).length > 0) {
      $tag.addClass('active');
      reloadFilterSummary($tag, "#filtro-disciplina");

    }else
      $danceDisciplines.find('a').first().addClass('active');

    // Nasconde il caricamento e mostra il contenuto dei corsi
    $("#loadingSpinnerFilterDiscipline").hide();
  }, undefined, //NON cé il caso di ERROR
  function(response) {
    //indico con errore
    var $a = $('<a id="erroreDisciplineEventi" href="#"></a>')
      .append($("<p>")).text("Il server non sembra rispondere correttamente")
      .on('click', function (event) {event.preventDefault();});
    $danceDisciplines.append($a);
    $("#loadingSpinnerFilterDiscipline").hide();
    createToast(response.result, "Impossibile ottenere le discipline dei prodotti", response.additional_info,0);
  });
}

function reloadProducts() {
  var discipline = $('#filtro-disciplina a.active').attr('data-filter');
  var level = undefined;

  if (discipline) localStorage.setItem('selectedProductDiscipline', discipline);
  else localStorage.removeItem('selectedProductDiscipline');

  // Con una richiesta AJAX ottengo i corsi del tipo selezionato
  callService("getPurchasablesEventsFiltered", [level, discipline], function (response) { // OK
    var $eventsGrid = $('#products-container');
    $eventsGrid.empty(); // Pulisce eventuali elementi esistenti

    var $items = []; // Array per tenere traccia degli elementi

    // Aggiungo alla griglia un elemento per ogni evento
    response.value.forEach(function (product) {
      var $div = createProductItem(product);
      $items.push($div); // Aggiungi l'elemento all'array
    });
    
    // Aggiungi tutti gli elementi alla griglia
    $eventsGrid.append($items);
    
    // Usa fadeIn per mostrare gradualmente gli elementi
    $items.forEach(function ($item, index) {
      // Ritarda leggermente l'effetto per ogni elemento successivo
      setTimeout(function () {
        $item.fadeIn(400); // Usa fadeIn per mostrare l'elemento
      }, index * 100); // Ritardo crescente per l'effetto di cascata
    });

    $("#loadingSpinnerProducts").hide();

  }, function (response) { // ERROR
    notFoundProductsError();
    $("#loadingSpinnerProducts").hide();
  }, function (response) { //FAIL
    notFoundProductsError();
    $("#loadingSpinnerProducts").hide();
    createToast(response.result, "Impossibile eseguire una ricerca filtrata sugli eventi", response.additional_info, 0);
  });
}

function addCollapsableIcons() {
  var $filtroDisciplinaHeader = $('#filtro-disciplina h4');
  var $filtroDisciplinaIcon = $filtroDisciplinaHeader.find('i.bi-chevron-down');
  var $collapseFiltroDisciplina = $('#collapseFiltroDisciplina');

  // Inizializza l'icona per partire chiusa
  $filtroDisciplinaIcon.css('transform', 'rotate(0deg)'); // Freccia giù all'inizio

  $collapseFiltroDisciplina.on('show.bs.collapse', function () {
    $filtroDisciplinaIcon.css('transform', 'rotate(180deg)'); // Ruota in su quando aperto
  });

  $collapseFiltroDisciplina.on('hide.bs.collapse', function () {
    $filtroDisciplinaIcon.css('transform', 'rotate(0deg)'); // Riporta in giù quando chiuso
  });
}

$(document).ready(function () {
  $.when(
    uploadDanceDisciplines()
  ).done(function () {
    reloadProducts();
    addCollapsableIcons();
  });


  // Simula il click con la tastiera (Enter) sul bottone dei corsi
  $('#titoloFiltroDisciplina').on('keydown', function (event) {
    if (event.key === 'Enter' || event.keyCode === 13) {
      $(this).click(); // Simula il click
      setTimeout(function () {
        $("#collapseFiltroDisciplina").focus();
      }, 400);
    }
  });
});
