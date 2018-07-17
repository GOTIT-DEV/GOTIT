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
    initDataTable("#result-table", "#details-table")
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


/* **********************************************
 *  Initialize datatable
 ********************************************** */
function initDataTable(tableId, detailsId) {
  if (!$.fn.DataTable.isDataTable(tableId)) {
    const table = $(tableId)
    const urls = {
      refTaxon: table.find("th#col-taxname").data('linkUrl'),
    }
    let details = undefined
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
        render: linkify(urls.refTaxon, 'id', true)
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
        $('[data-toggle="tooltip"]').tooltip()
        $(".details-form").off('submit').on('submit', function(event) {
          event.preventDefault();
          $(this).addClass('submitted')
          details.ajax.reload()
          $("#modal-container .modal").modal('show');
          $(this).removeClass('submitted')
        });


      }
    }).on('xhr', function() {
      var response = table.DataTable().ajax.json()
      niveau = response.niveau;
      criteres = response.criteres;
      if (!$.fn.DataTable.isDataTable(detailsId)) {
        details = initModalTable(detailsId)
      }
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

function initModalTable(tableId) {
  let detailsTable = $(tableId)
  let detailsDataTable = detailsTable.DataTable({
    autoWidth: false,
    responsive: true,
    ajax: {
      type: 'POST',
      url: detailsTable.data('url'),
      dataSrc: 'rows',
      data: function(d) {
        let form = $('.details-form.submitted')
        let form_data = form.serializeArray();
        for (var i = 0; i < criteres.length; i++) {
          form_data.push({ name: 'criteres[]', value: criteres[i] });
        }
        form_data.push({ name: 'niveau', value: niveau });
        return $.param(form_data)
      }
    },
    columns: [
      { data: 'id' },
      {
        data: 'code',
        render: function(data, type, row) {
          let lookUpAttr = row.type ? 'urlExt' : 'urlInt'
          let baseUrl = detailsTable.find("#col-code-seq").data(lookUpAttr)
          return linkify(baseUrl, 'id', true)(data, type, row)
        }
      }, {
        data: 'acc',
        render: linkify('https://www.ncbi.nlm.nih.gov/nuccore/', 'acc', false)
      }, {
        data: 'gene'
      }, {
        data: 'type',
        render: function(data, type, row) {
          return data ? "Externe" : "Interne"
        }
      }, {
        data: 'motu',
      }, {
        data: 'critere'
      }
    ],
    dom: "lfrtipB",
    buttons: dtconfig.buttons,
    drawCallback: function() {
      $('[data-toggle="tooltip"]').tooltip()
    }
  });
  return detailsDataTable
}