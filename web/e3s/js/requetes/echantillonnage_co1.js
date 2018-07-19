/* **************************
 *  Document ready
 **************************** */
$(document).ready(function () {
  initSwitchery('.switchbox')
  $('#taxaFilter')
    .change(toggleTaxonForm('.taxa-select'))
    .trigger('change')

  uiWaitResponse()

  let speciesSelector = new SpeciesSelector("#main-form", false)

  speciesSelector.promise.then(function () {
    initDataTable("#result-table")
  })

  window.onresize = function () {
    $(".geo-overlay").show()
    Plotly.Plots
      .resize(gd)
      .then($(".geo-overlay").hide)
  }
})


/**
 * Active le mode attente / loading
 */
function uiWaitResponse() {
  $("#main-form").find("button[type='submit']").button('loading')
}

/**
 * Désactive le mode attente
 * @param {Object} response réponse JSON
 */
function uiReceivedResponse(response) {
  $("#main-form").find("button[type='submit']").button('reset')
}

/* **************************
 *  Initialize datatable
 **************************** */

function initDataTable(tableId) {
  if (!$.fn.DataTable.isDataTable(tableId)) {
    const table = $(tableId)
    const urls = {
      refTaxon: table.find("th#col-taxname").data('linkUrl'),
      geocoords: table.find("th#col-details").data('linkUrl')
    }
    const renderNumber = $.fn.dataTable.render.number('', '.', 3)

    table.DataTable({
      autoWidth: false,
      responsive: {
        orthogonal: "responsive",
        details: {
          type: 'column'
        }
      },
      ajax: {
        "url": $("#main-form").data("url"),
        "dataSrc": "rows",
        "type": "POST",
        "data": function (d) {
          return $("#main-form").serialize()
        }
      },
      dom: "lfrtipB",
      buttons: dtconfig.buttons,
      order: [1, 'asc'],
      columns: [
        dtconfig.expandColumn,
        {
          data: "taxname",
          render: linkify(urls.refTaxon, 'id', true)
        }, {
          data: "nb_sta"
        }, {
          data: "lmp",
          render: renderNumber,
          defaultContent: "-"
        }, {
          data: "mle",
          render: renderNumber,
          defaultContent: "-"
        }, {
          data: "nb_sta_co1"
        }, {
          data: "lmp_co1",
          render: renderNumber,
          defaultContent: "-"
        }, {
          data: "mle_co1",
          render: renderNumber,
          defaultContent: "-"
        }, {
          data: "id",
          render: function (data, type, row) {
            var template = $("#details-form-template").html()
            return Mustache.render(template, row)
          }
        }
      ],
      drawCallback: function (settings) {
        uiReceivedResponse()
        $('[data-toggle="tooltip"]').tooltip()
        $(".details-form").submit(function (event) {
          event.preventDefault()
          var taxid = $(this).find("input[name='taxon']").val()
          var lmp = $(this).find("input[name='lmp_lm']").val()
          var lmp_co1 = $(this).find("input[name='lmp_co1']").val()
          var data = $(this).serialize()
          $(".geo-overlay").show()
          $.ajax({
            type: 'POST',
            data: data,
            url: urls.geocoords,
            success: function (response) {
              gd = geoPlot(response.no_co1, response.with_co1, lmp, lmp_co1)
              $("#detailsModal .modal-title").html(
                Mustache.render($("template#details-modal-title").html(), {
                  taxname: response.taxname
                }))
              $('#detailsModal').on('shown.bs.modal', function (e) {
                Plotly.Plots.resize(gd).then(function () {
                  $(".geo-overlay").hide();
                })
              })
              $("#detailsModal").modal('show')
            } // success callback
          }) // ajax
        }) // .details-form.submit
      } // drawCallback
    }) // datatables

    /****************************
     * Submit form handler
     ************************** */
    $("#main-form").submit(function (event) {
      event.preventDefault()
      $(this).find("button[type='submit']").button('loading')
      var results = table.DataTable()
      results.ajax.reload()
    })
  }
}

/**
 * Fonction d'affichage des graphiques d'échantillonnage COI
 * 
 * @param {Object} json_no_co1 
 * @param {Object} json_co1 
 * @param {number} lmp 
 * @param {number} lmp_co1 
 */
function geoPlot(json_no_co1, json_co1, lmp = undefined, lmp_co1 = undefined) {

  /**
   * Fonction pour extraire les données JSON et construire un objet de données 
   * pour plotly
   * 
   * @param {Object} json données json
   * @param {Object} update données à ajouter
   */
  function build_station_data(json, update = {}) {
    let coords = {
      latitude: [],
      longitude: [],
      hover: []
    }
    json.reduce( (currentCoords, row) => {
        currentCoords.latitude.push(row['latitude']),
        currentCoords.longitude.push(row['longitude']),
        currentCoords.hover.push([
          row['code_station'],
          "Coords:" + row['latitude'] + ";" + row['longitude'],
          "Alt:" + row['altitude'] + "m",
          row['commune'],
          row['pays']
        ].join("<br>"))
        return currentCoords
    }, coords)
    
    let data = {
      type: 'scattergeo',
      lat: coords.latitude,
      lon: coords.longitude,
      hoverinfo: 'text',
      text: coords.hover,
      // Doivent être remplacé par l'argument update
      marker: {
        size: 8,
        line: {
          width: 1,
          color: 'grey'
        }
      },
      name: "Stations",
    }

    // changement options par l'argument update
    $.extend(true, data, update)
    console.log(data)
    return data
  }

  // Init plotly
  let d3 = Plotly.d3
  $("#station-geo-map").html('')
  let gd3 = d3.select('#station-geo-map')
  let gd = gd3.node()

  console.log($("#station-geo-map").data('vocabStationCo1'))
  // Données de COI
  const data_co1 = build_station_data(json_co1, {
    name: $("#station-geo-map").data('vocabStationCo1'),
    marker: {
      symbol: "triangle-up",
      color: "red"
    }
  })

  // Données non COI
  const data_no_co1 = build_station_data(json_no_co1, {
    name: $("#station-geo-map").data('vocabStationLotmateriel'),
    marker: {
      symbol: "circle-open",
      size: 10,
      color: "orange",
      opacity: 0.8,
      line: {
        width: 2,
        color: "green",
      }
    }
  })

  // Objet data : contient les scatterplots
  let data = [
    data_co1,
    data_no_co1,
  ]

  // Coordonnées de la ligne LMP
  if (lmp) {
    data.push({
      type: 'scattergeo',
      lon: Array.from(new Array(360), (_, i) => -180 + i),
      lat: Array(360).fill(lmp),
      hoverinfo: "none",
      mode: 'lines',
      line: {
        width: 1.5,
        color: 'orange',
        dash: 'dash'
      },
      name: "LMP"
    })
  }

  // Coordonnées de la ligne LMP COI
  if (lmp_co1) {
    data.push({
      type: 'scattergeo',
      lon: Array.from(new Array(360), (_, i) => -180 + i),
      lat: Array(360).fill(lmp_co1),
      hoverinfo: "none",
      mode: 'lines',
      line: {
        width: 1.5,
        color: 'red',
        dash: 'dash'
      },
      name: "LMP (COI)"
    })
  }

  // Objet data complet : scatterplots + LMP + LMP COI

  // Paramètres d'affichage du graphique
  const layout = plotlyConfig.plotlyDefaultMapLayout

  Plotly.newPlot(gd, data, layout, {
    displaylogo: false, // pas de logo, enlever boutons de controle inutiles
    modeBarButtonsToRemove: ['sendDataToCloud', 'box', 'lasso2d', 'select2d', 'pan2d']
  })

  Plotly.Plots.resize(gd) // Remplir l'espace dans le DOM

  return gd // Renvoi objet plotly
}
