/*
 * This file is part of the SpeciesSearchBundle.
 *
 * Authors : see information concerning authors of GOTIT project in file AUTHORS.md
 *
 * SpeciesSearchBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * SpeciesSearchBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with SpeciesSearchBundle.  If not, see <https://www.gnu.org/licenses/>
 * 
 * Author : Louis Duchemin <ls.duchemin@gmail.com>
 */


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
 * Change <select> inputs background color to show query constraints
 * 
 * @param {Object} event jquery event object
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
    fetchCurrentUser().then(response => response.json())
      .then(user => {
        // Disable export buttons for invited users
        let dtbuttons = user.role === 'ROLE_INVITED' ? [] : dtconfig.buttons

        const table = $(tableId)
        let dataTable = table.DataTable({
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
 * Toggle loading mode on
 */
function uiWaitResponse() {
  $("#main-form").find("button[type='submit']").button('loading')
}

/**
 * Toggle loading mode off
 */
function uiReceivedResponse() {
  $("#main-form").find("button[type='submit']").button('reset')
}