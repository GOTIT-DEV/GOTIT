/* **************************
 *  Document ready
 **************************** */
$(document).ready(function () {
  // Initialiser l'activation/désactivation du filtrage taxon
  initSwitchery(".switchbox");
  $('#taxaFilter')
    .change(toggleTaxonForm('.taxa-select', '#method-form select'))
    .trigger('change');

  // taxaFilterActive : true si données géographiques disponibles dans la réponse
  var taxFilterActive = false
  // Passer l'UI en statut attente/loading
  uiWaitResponse()

  // Initialiser les deux selecteurs du formulaire
  let speciesSelector = new SpeciesSelector("#main-form", true)
  let methodSelector = new MethodSelector("#main-form")

  // Formulaires prêts : initialiser datatables
  $.when(speciesSelector.promise, methodSelector.promise)
    .done(function () {
      initDataTable("#result-table")
    })

});


/**
 * Active le mode attente / loading
 */
function uiWaitResponse() {
  $("#main-form").find("button[type='submit']").button('loading')
  taxaFilterActive = $('#taxaFilter').is(':checked')
  toggleMap(taxaFilterActive)
  toggleResults(false)

}

/**
 * Désactive le mode attente ; mettre à jour les onglets
 * @param {Object} response réponse JSON
 */
function uiReceivedResponse(response) {
  $("#main-form").find("button[type='submit']").button('reset')
  let showGeo = (taxaFilterActive && response.geo.length)
  if (showGeo) {
    updateMap(response)
  }
  toggleMap(showGeo)
  toggleResults(true)
  $(".geo-overlay").show();

}

/**
 * Active/désactive l'onglet résultats
 * @param {boolean} activate mode d'activation de l'onglet
 */
function toggleResults(activate) {
  if (activate) {
    $("#result-tab a").attr("data-toggle", "tab")
    $("#result-tab a").removeClass('disabled')
  } else {
    $("#result-tab a").addClass("disabled")
    $("#result-tab a").removeAttr("data-toggle")
  }
}

/**
 * Active/désactive l'onglet map
 * @param {Object} activate mode d'activation de l'onglet
 */
function toggleMap(activate) {
  if (activate) {
    $("#geolocation-tab a").attr("data-toggle", "tab")
    $("#geolocation-tab a").removeClass('disabled')
    $("#geolocation-tab").removeClass('disabled')
  } else {
    $("#geolocation-tab a").removeAttr("data-toggle")
    $("#geolocation-tab a").addClass("disabled")
    $("#geolocation-tab").addClass("disabled")
    $("#result-tab a").tab("show");
  }
}

/**
 * Met à jour la carte avec les données JSON
 * @param {Object} response réponse JSON
 */
function updateMap(response) {
  // Update title
  $("#geo-title").html(Mustache.render($("#geo-title-template").html(), {
    taxname: response.geo[0]['taxname'],
    code_methode: response.methode.code,
    date_methode: Date.parse(response.methode.date_methode.date).toString('yyyy')
  }));
  // Plot data
  let gd = motuGeoPlot(response.geo)
  // Overlay et événements changement d'onglet
  $('#result-tab a ').on('shown.bs.tab', function (e) {
    scrollTo('#resultats', 500)
    $(".geo-overlay").hide()
  })
  $("#geolocation-tab a ").on('shown.bs.tab', function (e) {
    scrollTo('#resultats', 500)
    $(".geo-overlay").show()
    Plotly.Plots.resize(gd).then($(".geo-overlay").hide)
  })
  // Show overlay on resize
  Plotly.Plots.resize(gd).then($(".geo-overlay").hide)
}

/**
 * Initialise datatables pour remplir la table *
 * en utilisant les données du formulaire
 * @param {string} tableId identifiant de la table dans le DOM
 * @param {string} formId identifiant du formulaire dans le DOM
 */
function initDataTable(tableId, formId = "#main-form") {
  if (!$.fn.DataTable.isDataTable(tableId)) {
    const table = $(tableId)
    const form = $(formId)
    const urls = {
      refTaxon: table.find("th#col-taxname").data('linkUrl')
    }
    const renderNumber = $.fn.dataTable.render.number('', '.', 3)

    var dataTable = table.DataTable({
      autoWidth: false,
      responsive: true,
      ajax: {
        "url": form.data("url"),
        "dataSrc": "rows",
        "type": "POST",
        "data": function (d) {
          return form.serialize()
        }
      },
      dom: "lfrtipB",
      buttons: dtconfig.buttons,
      order: [1, 'asc'],
      columns: [
        dtconfig.expandColumn, {
          data: "taxname",
          render: linkify(urls.refTaxon, 'id', true)
        }, {
          data: 'code',
          render: function (data, type, row) {
            let lookUpAttr = row.type ? 'urlExt' : 'urlInt'
            let baseUrl = table.find("#col-code-seq").data(lookUpAttr)
            return linkify(baseUrl, 'id', true)(data, type, row)
          }
        }, {
          data: "type_seq",
          render: function (data, type, row) {
            return data ? "Externe" : "Interne"
          }
        },
        {
          data: "accession_number",
          render: linkify('https://www.ncbi.nlm.nih.gov/nuccore/', 'accession_number', false)
        },
        {
          data: "th_2013",
          defaultContent: "-"
        },
        {
          data: "gmyc_2013",
          defaultContent: "-"
        },
        {
          data: "bptp_2017",
          defaultContent: "-"
        },
        {
          data: "ptp_2017",
          defaultContent: "-"
        },
        {
          data: "th_2017",
          defaultContent: "-"
        },
        {
          data: "latitude",
          render: renderNumber,
        },
        {
          data: "longitude",
          render: renderNumber,
          defaultContent: "-"
        },
        {
          data: "code_station"
        },
        {
          data: "commune"
        },
        {
          data: "pays"
        },
      ],
      drawCallback: function () {
        $('[data-toggle="tooltip"]').tooltip()
      }
    })

    dataTable.on('xhr', function () {
      let response = dataTable.ajax.json()
      uiReceivedResponse(response)
    });

    /*******************************
     * Submit form handler
     ***************************** */
    form.submit(function (event) {
      event.preventDefault();
      uiWaitResponse()
      dataTable.ajax.reload()
    });
  }
}