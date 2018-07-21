/* **************************
 *  Document ready
 **************************** */
$(document).ready(function () {
  initSwitchery('.switchbox')
  uiWaitResponse()
  let speciesSelector = new SpeciesSelector("#main-form", false)
  let methodSelector = new MethodSelector("#main-form", 'checkbox')
  // Wait for both selectors to be ready
  $.when(speciesSelector.promise, methodSelector.promise).done(function () {
    initDataTable("#result-table", "#details-table", "#main-form")
  })

  var niveau = 0
  var criteres = {}

  $('#taxaFilter')
    .change(toggleTaxonForm('.taxa-select'))
    .trigger('change')
})


/**
 * Initialise datatable en lien avec le formulaire et les éléments du DOM
 * 
 * @param {string} tableId ID de la table principale
 * @param {string} detailsId ID de la table dans la modal
 * @param {string} formId ID du formulaire
 */
function initDataTable(tableId, detailsId, formId) {
  if (!$.fn.DataTable.isDataTable(tableId)) {
    const table = $(tableId)
    const form = $(formId)
    const urls = {
      refTaxon: table.find("th#col-taxname").data('linkUrl'),
    }
    let details = undefined
    var dataTable = table.DataTable({
      autoWidth: false,
      responsive: true,
      ajax: {
        "url": form.data("url"),
        "dataSrc": "rows",
        "type": "POST",
        "data": _ => { return form.serialize() }
      },
      dom: "lfrtipB",
      buttons: dtconfig.buttons,
      columns: [{
        data: "taxname",
        render: linkify(urls.refTaxon, 'id', true)
      },
      { data: "methode" },
      {
        data: "date_methode",
        render: function (data, type, row) {
          return Date.parse(row.date_motu.date).toString('MMM yyyy');
        }
      },
      { data: "nb_seq" },
      { data: "nb_motus" },
      {
        data: "id",
        render: function (data, type, row) {
          var template = $("#details-form-template").html();
          return Mustache.render(template, row);
        }
      }],
      drawCallback: function (settings) {
        uiReceivedResponse()
        $('[data-toggle="tooltip"]').tooltip()
        $(".details-form").on('submit', function (event) {
          event.preventDefault();
          $(this).addClass('submitted')
          details.ajax.reload()
          $("#modal-container .modal").modal('show');
          $(this).removeClass('submitted')
        });
      }
    }).on('xhr', function () {
      var response = table.DataTable().ajax.json()
      niveau = response.niveau;
      criteres = response.criteres;
      if (!$.fn.DataTable.isDataTable(detailsId)) {
        details = initModalTable(detailsId)
      }
    });

    form.submit(function (event) {
      event.preventDefault();
      uiWaitResponse()
      dataTable.ajax.reload()
    });
  }
}

/**
 * Initialize datatable on modal table
 * 
 * @param {string} tableId ID for table element in DOM
 */
function initModalTable(tableId) {
  let detailsTable = $(tableId)
  let detailsDataTable = detailsTable.DataTable({
    autoWidth: false,
    responsive: true,
    ajax: {
      type: 'POST',
      url: detailsTable.data('url'),
      dataSrc: 'rows',
      data: _ => {
        let form = $('.details-form.submitted')
        let formData = form.serializeArray()
        criteres.forEach(crit => {
          formData.push({
            name: 'criteres[]',
            value: crit
          })
        })
        formData.push({
          name: 'niveau',
          value: niveau
        });
        return $.param(formData)
      }
    },
    columns: [{
      data: 'code',
      render: function (data, type, row) {
        let lookUpAttr = row.type ? 'urlExt' : 'urlInt'
        let baseUrl = detailsTable.find("#col-code-seq").data(lookUpAttr)
        return linkify(baseUrl, 'id', true)(data, type, row)
      }
    }, {
      data: 'acc',
      render: linkify('https://www.ncbi.nlm.nih.gov/nuccore/', 'acc', false)
    },
    { data: 'gene' },
    {
      data: 'type',
      render: function (data, type, row) {
        return data ? "Externe" : "Interne"
      }
    },
    { data: 'motu' },
    { data: 'critere' }
    ],
    dom: "lfrtipB",
    buttons: dtconfig.buttons,
    drawCallback: _ => {
      $('[data-toggle="tooltip"]').tooltip()
    }
  });
  return detailsDataTable
}

/**
 * Active le mode attente / loading
 */
function uiWaitResponse() {
  $("button[type='submit']").button('loading')
}

/**
 * Désactive le mode attente 
 */
function uiReceivedResponse() {
  $("button[type='submit']").button('reset')
}