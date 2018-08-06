/* **************************
 *  Document ready
 **************************** */
$(document).ready(function () {
  $("select.concordance")
    .change(updateChoiceColor)
    .trigger('change')
  uiWaitResponse()
  initDataTable("#result-table")
})

/**
 * Change la couleur des selecteurs pour visualiser les contraintes 
 * 
 * @param {Object} event l'objet d'événement jquery
 */
function updateChoiceColor(event) {
  const target = $(event.target)
  console.log(target)
  target.removeClass("typeA typeB typeC unassigned no-constraints")
  switch (target.val()) {
    case "A":
      target.selectpicker('setStyle', 'btn-info btn-success btn-danger', 'remove')
        .selectpicker('setStyle', 'btn-info')
      break
    case "B":
      target.selectpicker('setStyle', 'btn-info btn-success btn-danger', 'remove')
        .selectpicker('setStyle', 'btn-success')
      break
    case "C":
      target.selectpicker('setStyle', 'btn-info btn-success btn-danger', 'remove')
        .selectpicker('setStyle', 'btn-danger')
      break
    case "0":
      target.selectpicker('setStyle', 'btn-info btn-success btn-danger', 'remove')
      break
    case "1":
      target.selectpicker('setStyle', 'btn-info btn-success btn-danger', 'remove')
      break
  }

}




/* **************************
 *  Initialize datatable
 **************************** */

function initDataTable(tableId) {
  if (!$.fn.DataTable.isDataTable(tableId)) {
    const table = $(tableId)
    const thead = table.find("thead")
    const urls = {
      refTaxon: thead.data('urlRefTaxon'),
      lotMateriel: thead.data('urlLotMateriel'),
      individu: thead.data('urlIndividu'),
      sequence: thead.data('urlSequence'),
      geocoords: table.find("th#col-details").data('linkUrl')
    }

    function renderLinkify(fieldName, url) {
      return {
        responsive: linkify(url, fieldName, false),
        _: null,
        display: linkify(url, fieldName)
      }
    }

    var dataTable = table.DataTable({
      autoWidth: false,
      responsive: {
        orthogonal: "responsive",
        details: {
          type: 'column'
        }
      },
      language: dtconfig.language[table.data('locale')],
      ajax: {
        "url": $("#main-form").data("url"),
        "dataSrc": "rows",
        "type": "POST",
        "data": _ => {
          return $("#main-form").serialize()
        }
      },
      dom: "lfrtipB",
      buttons: dtconfig.buttons,
      order: [1, 'asc'],
      columns: [
        dtconfig.expandColumn, {
          data: "code_lm",
          render: renderLinkify('id_lm', urls.lotMateriel)
        }, {
          data: "taxname_lm",
          render: renderLinkify('idtax_lm', urls.refTaxon)
        },
        {
          data: "critere_lm"
        },
        {
          data: "code_biomol",
          render: renderLinkify('id_indiv', urls.individu)
        },
        {
          data: "code_tri_morpho",
          render: renderLinkify('id_indiv', urls.individu)
        },
        {
          data: "taxname_indiv",
          render: renderLinkify('idtax_lm', urls.refTaxon)
        },
        {
          data: "critere_indiv"
        },
        {
          data: "code_seq",
          defaultContent: "",
          render: renderLinkify('id_seq', urls.sequence)
        },
        {
          data: "taxname_seq",
          render: renderLinkify('idtax_lm', urls.refTaxon),
          defaultContent: ""
        },
        {
          data: "critere_seq",
          defaultContent: ""
        }
      ],
      drawCallback: function (settings) {
        uiReceivedResponse()
        $('[data-toggle="tooltip"]').tooltip()
      } // drawCallback
    }) // datatables

    /****************
     * Submit form handler
     */
    $("#main-form").submit(function (event) {
      event.preventDefault()
      uiWaitResponse()
      dataTable.ajax.reload()
    })
  }
}

/**
 * Active le mode attente / loading
 */
function uiWaitResponse() {
  $("#main-form").find("button[type='submit']").button('loading')
}

/**
 * Désactive le mode attente 
 */
function uiReceivedResponse() {
  $("#main-form").find("button[type='submit']").button('reset')
}