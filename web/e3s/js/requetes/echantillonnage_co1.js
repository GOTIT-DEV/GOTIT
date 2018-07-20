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

  geoPlot = new SamplingGeoPlot("#station-geo-map", "#result-table", "#detailsModal")
  speciesSelector.promise.then(function () {
    initDataTable("#result-table", geoPlot)
  })
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

function initDataTable(tableId, geoPlotObject) {
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
          geoPlotObject.reload(event.target)
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


