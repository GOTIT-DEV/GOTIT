/* **************************
 *  Document ready
 **************************** */
$(document).ready(function() {
  initSwitchery('.switchbox');
  $("#main-form").find("button[type='submit']").button('loading')

  let speciesSelector = new SpeciesSelector("#main-form", false)

  speciesSelector.promise.then(function() {
    initDataTable("#result-table")
  })

  window.onresize = function() {
    $(".geo-overlay").show();
    Plotly.Plots.resize(gd).then(function() {
      $(".geo-overlay").hide();
    });
  };

  $('#taxaFilter').change(function() {
    if (this.checked) {
      $(".taxa-select").prop('disabled', false);
    } else {
      $(".taxa-select").prop('disabled', true);
    }
  }).trigger('change')

});

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
    const renderNumber = $.fn.dataTable.render.number('', '.', 3);

    table.DataTable({
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
      buttons: [
        'copy', 'csv', 'excel'
      ],
      columns: [{
        data: "taxname",
        render: function(data, type, row) {
          return "<a href='" + urls.refTaxon + row.id + "'>" + data + "</a>"
        }
      }, {
        data: "nb_sta"
      }, {
        data: "lmp",
        render: renderNumber
      }, {
        data: "mle",
        render: renderNumber
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
        render: function(data, type, row) {
          var template = $("#details-form-template").html();
          return Mustache.render(template, row);
        }
      }],
      drawCallback: function(settings) {
        $("#main-form").find("button[type='submit']").button('reset')
        $(".details-form").submit(function(event) {
          event.preventDefault();
          var taxid = $(this).find("input[name='taxon']").val();
          var lmp = $(this).find("input[name='lmp_lm']").val();
          var lmp_co1 = $(this).find("input[name='lmp_co1']").val();
          var data = $(this).serialize();
          $(".geo-overlay").show();
          $.ajax({
            type: 'POST',
            data: data,
            url: urls.geocoords,
            success: function(response) {
              gd = geoPlot(response.no_co1, response.with_co1, lmp, lmp_co1);
              $("#detailsModal .modal-title").html(
                Mustache.render($("template#details-modal-title").html(), {
                  taxname: response.taxname
                }));
              $('#detailsModal').on('shown.bs.modal', function(e) {
                Plotly.Plots.resize(gd).then(function() {
                  $(".geo-overlay").hide();
                });
              })
              $("#detailsModal").modal('show');
            }
          });
        });
      }
    })

    /****************
     * Submit form handler
     */
    $("#main-form").submit(function(event) {
      event.preventDefault();
      $(this).find("button[type='submit']").button('loading')
      var results = table.DataTable()
      results.ajax.reload()

    });
  }
}