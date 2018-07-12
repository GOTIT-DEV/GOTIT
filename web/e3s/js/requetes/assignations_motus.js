/* **************************
 *  Document ready
 **************************** */
$(document).ready(function() {
  initSwitchery('.switchbox');
  $("#main-form").find("button[type='submit']").button('loading')
  let speciesSelector = new SpeciesSelector("#main-form", false)
  let methodSelector = new MethodSelector("#main-form", 'checkbox')
    // Wait for both selectors to be ready
  $.when(speciesSelector.promise, methodSelector.promise).done(function() {
    initDataTable("#result-table")
  });

  var niveau = 0;
  var criteres = {};

  $('#taxaFilter').change(function() {
    if (this.checked) {
      $(".taxa-select").prop('disabled', false);
    } else {
      $(".taxa-select").prop('disabled', true);
    }
  }).trigger('change')
})

/* **************************
 *  Initialize datatable
 **************************** */

function initDataTable(tableId) {
  if (!$.fn.DataTable.isDataTable(tableId)) {
    const table = $(tableId)
    const urls = {
      refTaxon: table.find("th#col-taxname").data('linkUrl'),
      detailsModal: table.find("th#col-details").data('linkUrl'),
    }

    table.DataTable({
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
        data: "methode"
      }, {
        data: "date_methode",
        render: function(data, type, row) {
          return Date.parse(row.date_motu.date).toString('MMM yyyy');
        }
      }, {
        data: "nb_seq"
      }, {
        data: "nb_motus"
      }, {
        data: "id",
        render: function(data, type, row) {
          var template = $("#details-form-template").html();
          return Mustache.render(template, row);
        }
      }],
      drawCallback: function(settings) {

        $("#main-form").find("button[type='submit']").button('reset')
        $(".details-form").off('submit').on('submit', function(event) {
          event.preventDefault();

          var form_data = $(this).serializeArray(); // form in last cell of row
          for (var i = 0; i < criteres.length; i++) {
            form_data.push({ name: 'criteres[]', value: criteres[i] });
          }
          form_data.push({ name: 'niveau', value: niveau });
          // End of data serialization console.log(form_data);
          $.ajax({ // prepare ajax request
            type: 'POST',
            url: urls.detailsModal,
            data: $.param(form_data),
            success: function(response) {
              var modal = $(response)
              modal.find("#details-table").DataTable({
                columnDefs: [{
                  targets: [1],
                  render: function(data, type, row) {
                    return Mustache.render(
                      "<a href='https://www.ncbi.nlm.nih.gov/nuccore/{{accession}}'>{{accession}}</a>", {
                        accession: data
                      }
                    );
                  }
                }],
                dom: "lfrtipB",
                buttons: [
                  'copy', 'csv', 'excel'
                ],
                initComplete: function(settings) {
                  modal.modal('show');
                }
              });
            }
          });
        });
      }
    }).on('xhr', function() {
      var response = table.DataTable().ajax.json()
      niveau = response.niveau;
      criteres = response.criteres;
    });

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