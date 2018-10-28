/* **************************
 *  Document ready
 **************************** */
$(document).ready(_ => {

  uiWaitResponse()

  let speciesSelector = new SpeciesSelector("#main-form", "#taxa-filter")
  let geoPlot = new SamplingGeoPlot("#station-geo-map", "#result-table", "#detailsModal")
 
  speciesSelector.promise.then(_ => {
    initDataTable("#result-table", geoPlot)
  })
})

/* **************************
 *  Initialize datatable
 **************************** */

function initDataTable(tableId, geoPlotObject) {
  if (!$.fn.DataTable.isDataTable(tableId)) {
    $.ajax({
      url: Routing.generate("user_current"),
      type: "GET"
    }).done(user => {
      let dtbuttons = user.role === 'ROLE_INVITED' ? [] : dtconfig.buttons
      const table = $(tableId)
      // Render floats with precision 3
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
          "url": Routing.generate('co1-sampling-query'),
          "dataSrc": "rows",
          "type": "POST",
          "data": _ => {
            return $("#main-form").serialize()
          }
        },
        language: dtconfig.language[$("html").attr("lang")],
        dom: "lfrtipB",
        buttons: dtbuttons,
        order: [1, 'asc'],
        columns: [
          dtconfig.expandColumn,
          {
            data: "taxname",
            render: linkify("referentieltaxon_show", { col: 'id' })
          }, {
            data: "nb_sta"
          }, {
            data: "lmp",
            render: renderNumber,
            defaultContent: ""
          }, {
            data: "mle",
            render: renderNumber,
            defaultContent: ""
          }, {
            data: "nb_sta_co1"
          }, {
            data: "lmp_co1",
            render: renderNumber,
            defaultContent: ""
          }, {
            data: "mle_co1",
            render: renderNumber,
            defaultContent: ""
          }, {
            data: "id",
            render: function (data, type, row) {
              return Mustache.render(
                $("#details-form-template").html(),
                row)
            }
          }
        ],
        drawCallback: _ => {
          uiReceivedResponse()
          $('[data-toggle="tooltip"]').tooltip()
          $(".details-form").submit(event => {
            event.preventDefault()
            geoPlotObject.reload(event.target)
          }) // .details-form.submit
        } // drawCallback
      }) // datatables

      /****************************
       * Submit form handler
       ************************** */
      $("#main-form").submit(event => {
        event.preventDefault()
        $(this).find("button[type='submit']").button('loading')
        table.DataTable().ajax.reload()
      })
    })

  }
}

/**
 * Toggle UI loading mode
 */
function uiWaitResponse() {
  $("#main-form").find("button[type='submit']").button('loading')
}

/**
 * Toggle UI loading done
 * @param {Object} response réponse JSON
 */
function uiReceivedResponse(response) {
  $("#main-form").find("button[type='submit']").button('reset')
}