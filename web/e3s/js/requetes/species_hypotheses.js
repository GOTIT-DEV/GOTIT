/* **************************
 *  Document ready
 **************************** */
$(document).ready(function () {

  let speciesSelector = new SpeciesSelector("#main-form")
  let methodSelector = new MethodSelector("#main-form")

  $('input[name="reference"]')
    .click(toggleFormSelect)
    .filter(':checked')
    .trigger('click')

  $("#target-dataset").on('loaded.bs.select', event => {
    $(event.target).parent().tooltip({
      title: $(event.target).data('originalTitle'),
      placement: 'auto top'
    })
  })

  uiWaitResponse()


  // Synchronisation des select dataset reference vs target
  $("#main-form select#dataset").change(event => {
    $("#target-dataset").val(event.target.value).selectpicker('refresh')
  }).trigger('change')

  // Wait for active selector  to be ready
  switch ($('input[name="reference"]:checked').val()) {
    case "0":
      initDataTable("#result-table-recto")
      initDataTable("#result-table-verso")
    case "1":
      $.when(speciesSelector.promise).done(_ => {
        initDataTable("#result-table-recto")
        initDataTable("#result-table-verso")
      })
      break;
    case "2":
      $.when(methodSelector.promise).done(_ => {
        initDataTable("#result-table-recto")
        initDataTable("#result-table-verso")
      })
      break;
    default:
      console.error("Invalid value for reference radio input : " +
        $('input[name="reference"]:checked').val())
      break;
  }
})

function uiWaitResponse() {
  $("#main-form").find("button[type='submit']").button('loading')
  $(".nav-tabs li")
    .addClass("disabled")
    .find('a')
    .attr('data-toggle', '')
}

function uiReceivedResponse() {
  $("#main-form").find("button[type='submit']").button('reset')
  $(".nav-tabs li")
    .removeClass("disabled")
    .find('a')
    .attr('data-toggle', 'tab')
}

function toggleFormSelect(event) {
  $(event.target).tooltip('hide')
  switch (event.target.value) {
    case "0":
      $(".method-select").prop('disabled', true)
      $(".taxa-select").prop('disabled', true)
      $("#target-dataset").prop('disabled', false)
      break

    case "1":
      $(".method-select").prop('disabled', true)
      $(".taxa-select").prop('disabled', false)
      $("#target-dataset").prop('disabled', false)
      break

    case "2":
      $(".method-select").prop('disabled', false)
      $(".taxa-select").prop('disabled', true)
      $("#target-dataset")
        .prop('disabled', true)
        .val(
          $("select#dataset").val()
        )
      break
  }
  $(".selectpicker").selectpicker('refresh')
}

/* **************************
 *  Initialize datatable
 **************************** */

function initDataTable(tableId) {
  if (!$.fn.DataTable.isDataTable(tableId)) {
    $.ajax({
      url: Routing.generate("user_current"),
      type: "GET"
    }).done(user => {
      let dtbuttons = user.role === 'ROLE_INVITED' ? [] : dtconfig.buttons
      const table = $(tableId)
      const side = table.data('target')
      let barplot = new BarPlot(table.data('barplot'))
      $('.nav-tabs li a').on('shown.bs.tab', event => {
        barplot.resize()
      })
      var dataTable = table.DataTable({
        responsive: true,
        autoWidth: false,
        ajax: {
          "url": Routing.generate('species-hypotheses-query'),
          "dataSrc": side,
          "type": "POST",
          "data": _ => {
            return $("#main-form").serialize()
          }
        },
        language: dtconfig.language[$("html").attr("lang")],
        dom: 'lf<"clear pull-right"B>rtip',
        buttons: dtbuttons,
        order: [1, 'asc'],
        columns: [{
          data: "methode"
        }, {
          data: "libelle_motu",
        }, {
          data: "match"
        }, {
          data: "split"
        }, {
          data: "lump"
        }, {
          data: "reshuffling"
        }, {
          data: 'nb_seq'
        }, {
          data: 'nb_sta'
        }],
        drawCallback: _ => {
          $('[data-toggle="tooltip"]').tooltip()
        }
      })

      dataTable.on('xhr', _ => {
        let response = dataTable.ajax.json()
        barplot.refresh(response[side])
        uiReceivedResponse()
      })

      /****************
       * Submit form handler
       */
      $("#main-form").submit(event => {
        event.preventDefault();
        uiWaitResponse()
        table.DataTable().ajax.reload()
      });
    })
  }
}