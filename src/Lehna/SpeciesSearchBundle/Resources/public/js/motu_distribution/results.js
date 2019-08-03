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

import { dtconfig, linkify, fetchCurrentUser } from '../queries.js'
import { updateMap } from './map.js'

const form = $("#main-form")
const renderNumber = $.fn.dataTable.render.number('', '.', 3)
const columns = [
  dtconfig.expandColumn, {
    data: "taxname",
    render: linkify("referentieltaxon_show", { col: 'taxon_id' })
  }, {
    data: 'code',
    render: (data, type, row) => {
      let route = row.type_seq ?
        'sequenceassembleeext_show' :
        'sequenceassemblee_show'
      return linkify(route, { col: 'id' })(data, type, row)
    }
  }, {
    data: "type_seq",
    render: isExternal => {
      return isExternal ?
        Translator.trans('entity.seq.type.externe') :
        Translator.trans('entity.seq.type.interne')
    }
  }, {
    data: "accession_number",
    render: linkify('https://www.ncbi.nlm.nih.gov/nuccore/', {
      col: 'accession_number', ellipsis: false, generateRoute: false
    })
  }, {
    data: 'motu'
  }, {
    data: "latitude",
    render: renderNumber,
  }, {
    data: "longitude",
    render: renderNumber,
    defaultContent: ""
  }, {
    data: "code_station",
    render: linkify("station_show", { col: 'id_sta' })
  }, {
    data: "commune"
  }, {
    data: "pays"
  }
]

export function initDataTable(tableId) {
  if (!$.fn.DataTable.isDataTable(tableId)) {
    fetchCurrentUser().then(user => {
      let dtbuttons = (user.role === "ROLE_INVITED") ? [] : dtconfig.buttons
      let dataTable = $(tableId).DataTable({
        autoWidth: false,
        responsive: true,
        ajax: {
          "url": Routing.generate('distribution-query'),
          "dataSrc": "rows",
          "type": "POST",
          "data": _ => {
            return form.serialize()
          }
        },
        language: dtconfig.language[$("html").attr("lang")],
        dom: "lfrtipB",
        buttons: dtbuttons,
        order: [1, 'asc'],
        columns: columns,
        drawCallback: _ => {
          $('[data-toggle="tooltip"]').tooltip()
        }
      })

      dataTable.on('xhr', _ => {
        let response = dataTable.ajax.json()
        uiReceivedResponse(response)
      });

      /*******************************
       * Submit form handler
       ***************************** */
      form.submit(event => {
        event.preventDefault();
        uiWaitResponse()
        dataTable.ajax.reload()
      })
    })

  }
}


/**
 * Toggle result tab containing geographical map
 * @param {bool} activeMap 
 */
function toggleTabs(activeMap) {
  if (!activeMap) $("#table-tab a").tab('show')

  $("#geolocation-tab")
    .toggleClass('disabled', !activeMap)
    .find("a")
    .attr('data-toggle', activeMap ? 'tab' : '')
    .toggleClass('disabled', !activeMap)
}

/**
* Toggle UI loading mode
*/
function uiWaitResponse() {
  form.find("button[type='submit']").button('loading')
}

/**
 * Toggle UI loading done
 * @param {Object} response JSON response
 */
function uiReceivedResponse(response) {
  form.find("button[type='submit']").button('reset')
  let showGeo = ('taxname' in response.query && response.rows.length)
  if (showGeo) {
    updateMap(response)
  }
  toggleTabs(showGeo)
}