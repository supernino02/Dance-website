function uploadCourseTypesButton() {
  var $courseType = $('#collapseFiltroTipo');

  //rendo active il valore indicato in localstorage, altrimenti il primo tag che trovo
  var selectedProductType = localStorage.getItem('selectedProductType')
  //se ho trovato il tag corretto
  if (selectedProductType && ($tag = $courseType.find('a[data-filter="' + selectedProductType + '"]')).length > 0) {
    $tag.addClass('active');
    reloadFilterSummary($tag, "#filtro-tipo");
  } else
    $courseType.find('a').first().addClass('active');

  $courseType.on('click', 'a', function (event) {
    event.preventDefault(); // Previene il comportamento predefinito del link
    
    reloadFilterSummary($(this),"#filtro-tipo");

    $courseType.find('a').removeClass('active');
    $(this).addClass('active');
    reloadProducts();
  });
}

function uploadCourseLevels() {

  var $courseLevel = $('#collapseFiltroLivello');
  // Con una richiesta AJAX ottengo i tipi di corso, in modo da usarle come filtro
  return callService("getAllCourseLevels", [], function (response) { // OK
    // Aggiungo alla lista un elemento per ogni tipo
    $courseLevel.append($('<a href="#" ><i class="bi bi-arrow-right-circle"></i><span>Tutti</span></a>'));
    response.value.forEach(function (row) {
      var $li = $('<a href="#" data-filter="' + row.name + '"><i class="bi bi-arrow-right-circle"></i><span class="capitalizeFirstLetter">' + row.name + '</span></a>');
      $courseLevel.append($li);
    });

    // Gestisce il clic sugli elementi <a>
    $courseLevel.on('click', 'a', function (event) {
      event.preventDefault(); // Previene il comportamento predefinito del link

      reloadFilterSummary($(this), "#filtro-livello");

      $courseLevel.find('a').removeClass('active');
      $(this).addClass('active');
      reloadProducts();
    });

    //rendo active il valore indicato in localstorage, altrimenti il primo tag che trovo
    var selectedProductLevel = localStorage.getItem('selectedProductLevel')
    //se ho trovato il tag corretto
    if (selectedProductLevel && ($tag = $courseLevel.find('a[data-filter="' + selectedProductLevel + '"]')).length > 0) {
      $tag.addClass('active');
      reloadFilterSummary($tag, "#filtro-livello");
    } else
      $courseLevel.find('a').first().addClass('active');

    // Nasconde il caricamento e mostra il contenuto dei corsi
    $("#loadingSpinnerFilterLevel").hide();
  },
    undefined, //NON cé il caso di ERROR
    function (response) {
      //indico con errore
      var $a = $('<a id="erroreLivelliCorsi" href="#"></a>')
        .append($("<p>")).text("Il server non sembra rispondere correttamente")
        .on('click', function (event) { event.preventDefault(); });
      $courseLevel.append($a);
      $("#loadingSpinnerFilterLevel").hide();
      createToast(response.result, "Impossibile ottenere i livelli dei prodotti", response.additional_info, 0);
    });
}

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
    } else
      $danceDisciplines.find('a').first().addClass('active');

    // Nasconde il caricamento e mostra il contenuto dei corsi
    $("#loadingSpinnerFilterDiscipline").hide();
  }, undefined, //NON cé il caso di ERROR
    function (response) {
      //indico con errore
      var $a = $('<a id="erroreDisciplineCorsi" href="#"></a>')
        .append($("<p>")).text("Il server non sembra rispondere correttamente")
        .on('click', function (event) { event.preventDefault(); });
      $danceDisciplines.append($a);
      $("#loadingSpinnerFilterDiscipline").hide();
      createToast(response.result, "Impossibile ottenere le discipline dei prodotti", response.additional_info, 0);
    });
}

function reloadProducts() {
  var level = $('#filtro-livello a.active').attr('data-filter');
  var type = $('#collapseFiltroTipo a.active').attr('data-filter');
  var discipline = $('#filtro-disciplina a.active').attr('data-filter');

  //aggiorno il localstorage; se undefined tolgo i valori
  if (level) localStorage.setItem('selectedProductLevel', level);
  else localStorage.removeItem('selectedProductLevel');

  if (type) localStorage.setItem('selectedProductType', type);
  else localStorage.removeItem('selectedProductType');

  if (discipline) localStorage.setItem('selectedProductDiscipline', discipline);
  else localStorage.removeItem('selectedProductDiscipline');

  // Con una richiesta AJAX ottengo i corsi del tipo selezionato
  callService("getPurchasablesCoursesFiltered", [level, type, discipline], function (response) { // OK
    var $coursesGrid = $('#products-container');
    $coursesGrid.empty(); // Pulisce eventuali elementi esistenti

    // Aggiungo alla griglia un elemento per ogni corso
    var $items = []; // Array per tenere traccia degli elementi

    response.value.forEach(function (product) {
      var $div = createProductItem(product);
      $items.push($div); // Aggiungi l'elemento all'array
    });

    // Aggiungi tutti gli elementi alla griglia
    $coursesGrid.append($items);

    // Usa fadeIn per mostrare gradualmente gli elementi
    $items.forEach(function ($item, index) {
      // Ritarda leggermente l'effetto per ogni elemento successivo
      setTimeout(function () {
        $item.fadeIn(400); // Usa fadeIn per mostrare l'elemento
      }, index * 2000); // Ritardo crescente per l'effetto di cascata
    });

    $("#loadingSpinnerProducts").hide();

  }, function (response) { // ERROR
    notFoundProductsError();
    $("#loadingSpinnerProducts").hide();
  }, function (response) { //FAIL
    notFoundProductsError();
    $("#loadingSpinnerProducts").hide();
    createToast(response.result, "Impossibile eseguire una ricerca filtrata sui corsi", response.additional_info, 0);

  });
}



$(document).ready(function () {
  setupDropdown('tipo');
  setupDropdown('livello');
  setupDropdown('disciplina');

  uploadCourseTypesButton();

  $.when(
    uploadCourseLevels(),
    uploadDanceDisciplines()
  ).done(function () { reloadProducts(); });

  //FACCIO CHE I DROPDOWN SONO UTILIZZABILI CON SOLO TASTIERA
  // Simula il click con la tastiera (Enter) sul bottone dei corsi
  $('#titoloFiltroTipo').on('keydown', function (event) {
    if (event.key === 'Enter' || event.keyCode === 13) {
      $(this).click(); // Simula il click
      setTimeout(function () {
        $("#collapseFiltroTipo").focus();
      }, 400);
    }
  });

  // Simula il click con la tastiera (Enter) sul bottone dei corsi
  $('#titoloFiltroLivello').on('keydown', function (event) {
    if (event.key === 'Enter' || event.keyCode === 13) {
      $(this).click(); // Simula il click
      setTimeout(function () {
        $("#collapseFiltroLivello").focus();
      }, 400);
    }
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
