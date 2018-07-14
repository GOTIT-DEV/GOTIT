/* **************************
 *  Document ready
 **************************** */
$(document).ready(function() {
  $('input[name="reference"]')
    .click(toggleFormSelect)
    .filter(':checked')
    .trigger('click')

  uiWaitResponse()

  let speciesSelector = new SpeciesSelector("#main-form", true)
  let methodSelector = new MethodSelector("#main-form")

  // Wait for active selector  to be ready
  switch ($('input[name="reference"]:checked').val()) {
    case "0":
      initDataTable("#result-table-recto")
      initDataTable("#result-table-verso")
    case "1":
      $.when(speciesSelector.promise).done(function() {
        initDataTable("#result-table-recto")
        initDataTable("#result-table-verso")
      })
      break;
    case "2":
      $.when(methodSelector.promise).done(function() {
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
  switch (event.target.value) {
    case "0":
      $(".method-select").prop('disabled', true);
      $(".taxa-select").prop('disabled', true);
      break;

    case "1":
      $(".method-select").prop('disabled', true);
      $(".taxa-select").prop('disabled', false);
      break;

    case "2":
      $(".method-select").prop('disabled', false);
      $(".taxa-select").prop('disabled', true);
      break;
  }
}

/* **************************
 *  Initialize datatable
 **************************** */

function initDataTable(tableId) {
  if (!$.fn.DataTable.isDataTable(tableId)) {
    const table = $(tableId)
    const side = table.data('target')
    var dataTable = table.DataTable({
      responsive: true,
      autoWidth: false,
      ajax: {
        "url": $("#main-form").data("url"),
        "dataSrc": side,
        "type": "POST",
        "data": function(d) {
          return $("#main-form").serialize()
        }
      },
      buttons: {
        dom: {
          container: {
            tag: 'aside'
          }
        }
      },
      dom: 'lf<"clear pull-right"B>rtip',
      buttons: [
        'copy', 'csv', 'excel'
      ],
      columns: [{
        data: "methode"
      }, {
        data: "date_motu",
        //width: "15%",
        render: function(data, type, row) {
          return Date.parse(row.date_motu.date).toString('MMM yyyy');
        },
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
      }]
    })

    dataTable.on('xhr', function() {
      var response = dataTable.ajax.json()
      var barplot = table.data('barplot')
      var gd = barPlot(barplot, response[side])
      uiReceivedResponse()
      $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        Plotly.Plots.resize(gd)
      });
    });

    /****************
     * Submit form handler
     */
    $("#main-form").submit(function(event) {
      event.preventDefault();
      uiWaitResponse()
      let results = table.DataTable()
      results.ajax.reload()
    });
  }
}