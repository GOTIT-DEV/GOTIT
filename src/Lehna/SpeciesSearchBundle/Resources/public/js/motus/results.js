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

import { ids } from "./main.js"
import { linkify, dtconfig, fetchCurrentUser } from '../queries.js'


let lastQuery = {}
let detailsTable = null
let dtbuttons = null
let detailsFormData = null

export function initDataTable(tableId) {
  uiWaitResponse()
  // Don't try to initialize if already init
  if (!$.fn.DataTable.isDataTable(tableId)) {
    fetchCurrentUser()
      .then(response => response.json())
      .then(user => {
        dtbuttons = (user.role === 'ROLE_INVITED') ? [] : dtconfig.buttons
        // Init DataTable
        const table = $(tableId)
        let dataTable = table.DataTable({
          autoWidth: false,
          responsive: true,
          ajax: {
            "url": Routing.generate("motu-query"),
            "dataSrc": "rows",
            "type": "POST",
            "data": _ => {
              return $(ids.form).serialize()
            }
          },
          language: dtconfig.language[table.data('locale')],
          dom: "lfrtipB",
          buttons: dtbuttons,
          columns: [{
            data: "taxname",
            render: linkify("referentieltaxon_show", {
              col: 'id',
              _locale: table.data('locale')
            })
          }, {
            data: "methode"
          }, {
            data: "libelle_motu",
          }, {
            data: "nb_seq"
          }, {
            data: "nb_motus"
          }, {
            data: "id",
            render: (data, type, row) =>
              Mustache.render($("#details-form-template").html(), row)
          }],
          drawCallback: _ => {
            // Toggle UI loading done
            uiReceivedResponse()
            // Init tooltips
            $('[data-toggle="tooltip"]').tooltip()
            // Init detail forms
            $(".details-form").on('submit', event => {
              event.preventDefault();
              detailsFormData = $(event.target).serializeArray()
              lastQuery.criteres.forEach(crit => {
                detailsFormData.push({
                  name: 'criteres[]',
                  value: crit
                })
              })
              detailsFormData.push({
                name: 'niveau',
                value: lastQuery.niveau
              })
              // Init details table if it is not already done
              if (!$.fn.DataTable.isDataTable(ids.details))
                detailsTable = initModalTable()
              else
                detailsTable.ajax.reload()
              $("#modal-container .modal").modal('show');
            });
          }
        }).on('xhr', _ => {
          // Keep track of last query parameters
          lastQuery = dataTable.ajax.json().query
        })

        // Init form submit event
        $(ids.form).submit(event => {
          event.preventDefault()
          uiWaitResponse()
          dataTable.ajax.reload()
        })
      })
  }
}

/**
   * Initialize datatable on modal table
   */
function initModalTable(formData) {
  return $(ids.details).DataTable({
    autoWidth: false,
    responsive: true,
    ajax: {
      type: 'POST',
      url: Routing.generate("motu-modal"),
      dataSrc: '',
      data: _ => {
        return detailsFormData
      }
    },
    language: dtconfig.language[$("html").attr("lang")],
    columns: [{
      data: 'code',
      render: (data, type, row) => {
        let route = row.type ?
          'sequenceassembleeext_show' :
          'sequenceassemblee_show'
        return linkify(route,
          { col: 'id', placement: 'right' })(data, type, row)
      }
    }, {
      data: 'acc',
      render: linkify('https://www.ncbi.nlm.nih.gov/nuccore/',
        { col: 'acc', ellipsis: false, generateRoute: false })
    }, {
      data: 'gene'
    }, {
      data: 'type',
      render: seqType => {
        return seqType ?
          Translator.trans("entity.seq.type.externe") :
          Translator.trans("entity.seq.type.interne")
      }
    }, {
      data: 'motu'
    }, {
      data: 'critere'
    }],
    dom: "lfrtipB",
    buttons: dtbuttons,
    drawCallback: _ => { $('[data-toggle="tooltip"]').tooltip() }
  })
}

/**
 * Toggle UI loading mode
 */
function uiWaitResponse() {
  $(ids.form).find("button[type='submit']").button('loading')
}

/**
 * Toggle UI loading done
 */
function uiReceivedResponse() {
  $(ids.form).find("button[type='submit']").button('reset')
}
