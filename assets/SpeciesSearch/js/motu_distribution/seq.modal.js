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

import { fetchCurrentUser } from '../utils.js'
import { dtconfig, linkify } from '../datatables_utils.js'


/**
 * Initialize modal table to list sequences from a given station
 * @param {String} tableId DOM result table ID
 */
export function initModalTable(tableId) {
  return fetchCurrentUser()
    .then(response => response.json())
    .then(user => {
      let dtbuttons = (user.role === 'ROLE_INVITED') ? [] : dtconfig.buttons
      return $(tableId).DataTable({
        autoWidth: false,
        responsive: true,
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
          data: 'accession_number',
          render: linkify('https://www.ncbi.nlm.nih.gov/nuccore/',
            { col: 'accession_number', ellipsis: false, generateRoute: false })
        },
        {
          data: 'type',
          render: seqType => {
            return seqType ?
              Translator.trans("entity.seq.type.externe") :
              Translator.trans("entity.seq.type.interne")
          }
        }, {
          data: 'motu'
        }],
        dom: "lfrtipB",
        buttons: dtbuttons,
        drawCallback: _ => { $('[data-toggle="tooltip"]').tooltip() }
      })
    })
}