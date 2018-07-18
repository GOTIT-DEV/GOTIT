/* **************************
 *  Document ready
 **************************** */
$(document).ready(function() {
  var showGeo = false
  initSwitchery(".switchbox");
  $('#taxaFilter').change(function() {
    if (this.checked) {
      $(".taxa-select").prop('disabled', false);
      $("#method-form select").prop('disabled', false);
    } else {
      $(".taxa-select").prop('disabled', true);
      $("#method-form select").prop('disabled', true);
    }
  }).trigger('change');

  uiWaitResponse()

  let speciesSelector = new SpeciesSelector("#main-form", true)
  let methodSelector = new MethodSelector("#main-form")

  $.when(speciesSelector.promise, methodSelector.promise)
    .done(function() {
      initDataTable("#result-table")
    })

});


function uiWaitResponse() {
  $("#main-form").find("button[type='submit']").button('loading')

  $("#result-tab a").addClass("disabled");
  $("#result-tab a").removeAttr("data-toggle");

  if ($('#taxaFilter').is(':checked')) {
    showGeo = true
    $("#geolocation-tab a").attr("data-toggle", "tab")
    $("#geolocation-tab a").removeClass('disabled')
  } else {
    showGeo = false
    $("#geolocation-tab a").removeAttr("data-toggle");
    $("#geolocation-tab").addClass("disabled");
    $("#result-tab a").tab("show");
  }

}

function uiReceivedResponse() {
  $("#main-form").find("button[type='submit']").button('reset')
  $("#result-tab a").attr("data-toggle", "tab")
  $("#result-tab a").removeClass('disabled')
  $(".geo-overlay").show();
}

/* **************************
 *  Initialize datatable
 **************************** */

function initDataTable(tableId) {
  if (!$.fn.DataTable.isDataTable(tableId)) {
    const table = $(tableId)
    const urls = {
      refTaxon: table.find("th#col-taxname").data('linkUrl')
    }
    const renderNumber = $.fn.dataTable.render.number('', '.', 3);

    var dataTable = table.DataTable({
      autoWidth: false,
      responsive: true,
      ajax: {
        "url": $("#main-form").data("url"),
        "dataSrc": "rows",
        "type": "POST",
        "data": function(d) {
          return $("#main-form").serialize()
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
          render: function(data, type, row) {
            let lookUpAttr = row.type ? 'urlExt' : 'urlInt'
            let baseUrl = table.find("#col-code-seq").data(lookUpAttr)
            return linkify(baseUrl, 'id', true)(data, type, row)
          }
        }, {
          data: "type_seq",
          render: function(data, type, row) {
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
      drawCallback: function() {
        $('[data-toggle="tooltip"]').tooltip()
      }
    })

    dataTable.on('xhr', function() {
      if (showGeo) {
        var response = dataTable.ajax.json()
        if (response.geo.length) {
          $("#geo-title").html(Mustache.render($("#geo-title-template").html(), {
            taxname: response.geo[0]['taxname'],
            code_methode: response.methode.code,
            date_methode: Date.parse(response.methode.date_methode.date).toString('yyyy')
          }));
          gd = motuGeoPlot(response.geo);
          Plotly.Plots.resize(gd).then(function() {
            $(".geo-overlay").hide();
          });
          $(".nav-tabs li").removeClass("disabled");
          $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            scrollTo('#resultats', 500);
            $(".geo-overlay").hide();
          });
          $("#geolocation-tab a ").on('shown.bs.tab', function(e) {
            scrollTo('#resultats', 500);
            $(".geo-overlay").show();
            Plotly.Plots.resize(gd).then(function() {
              $(".geo-overlay").hide();
            });
          });
        } else {
          $("#geolocation-tab a").removeAttr("data-toggle");
          $("#geolocation-tab").addClass("disabled");
          $("#result-tab a").tab("show");
        }
      }
      uiReceivedResponse()
    });

    /*******************************
     * Submit form handler
     ***************************** */
    $("#main-form").submit(function(event) {
      event.preventDefault();
      uiWaitResponse()
      var results = table.DataTable()
      results.ajax.reload()
    });
  }
}