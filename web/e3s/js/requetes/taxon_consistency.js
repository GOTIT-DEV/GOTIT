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

    $.ajax({
      url: Routing.generate("user_current"),
      type: "GET"
    }).done(user => {
      let dtbuttons = user.role === 'ROLE_INVITED' ? [] : dtconfig.buttons
      const table = $(tableId)

      var dataTable = table.DataTable({
        autoWidth: false,
        responsive: {
          orthogonal: "responsive",
          details: {
            type: 'column'
          }
        },
        language: dtconfig.language[$("html").attr("lang")],
        ajax: {
          "url": Routing.generate("consistency-query"),
          "dataSrc": "rows",
          "type": "POST",
          "data": _ => {
            return $("#main-form").serialize()
          }
        },
        dom: "lfrtipB",
        buttons: dtbuttons,
        order: [1, 'asc'],
        columns: [
          dtconfig.expandColumn, {
            data: "code_lm",
            render: renderLinkify('id_lm', "lotmateriel_show")
          }, {
            data: "taxname_lm",
            render: renderLinkify('idtax_lm', "referentieltaxon_show")
          },
          {
            data: "critere_lm"
          },
          {
            data: "code_biomol",
            render: renderLinkify('id_indiv', "individu_show")
          },
          {
            data: "code_tri_morpho",
            render: renderLinkify('id_indiv', "individu_show")
          },
          {
            data: "taxname_indiv",
            render: renderLinkify('idtax_lm', "referentieltaxon_show")
          },
          {
            data: "critere_indiv"
          },
          {
            data: "code_seq",
            defaultContent: "",
            render: renderLinkify('id_seq', "sequenceassemblee_show")
          },
          {
            data: "taxname_seq",
            render: renderLinkify('idtax_lm', "referentieltaxon_show"),
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
    })
  }
}


/**
 * Generate URLs with ellipsis or not, depending of display mode in DataTable
 * @param {string} fieldName name of the field to use as ID in generated URL
 * @param {string} url route to generate base URL
 */
function renderLinkify(fieldName, url) {
  return {
    responsive: linkify(url, { col: fieldName, ellipsis: false }),
    _: null,
    display: linkify(url, { col: fieldName })
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