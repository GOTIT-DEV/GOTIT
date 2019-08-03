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

import { fetchCurrentUser, dtconfig } from '../queries.js'
import { BarPlot } from '../plots.js'


export function initDataTable(tableId) {
  if (!$.fn.DataTable.isDataTable(tableId)) {
    fetchCurrentUser().then(response => response.json())
      .then(user => {
        const dtbuttons = (user.role === 'ROLE_INVITED') ? [] : dtconfig.buttons
        const table = $(tableId)
        const side = table.data('target')

        // Init barplot 
        let barplot = new BarPlot(table.data('barplot'))
        $('.nav-tabs li a').on('shown.bs.tab', _ => {
          barplot.resize()
        })

        // Init result table
        let dataTable = table.DataTable({
          responsive: true,
          autoWidth: false,
          ajax: {
            "url": Routing.generate('species-hypotheses-query'),
            "dataSrc": side,
            "type": "POST",
            "data": _ => {
              return $("#main-form").serialize()
            }
          },
          language: dtconfig.language[$("html").attr("lang")],
          dom: 'lf<"clear pull-right"B>rtip',
          buttons: dtbuttons,
          order: [1, 'asc'],
          columns: [{
            data: "methode"
          }, {
            data: "libelle_motu",
          }, {
            data: "match"
          }, {
            data: "split"
          }, {
            data: "lump"
          }, {
            data: "reshuffling"
          }, {
            data: 'nb_seq'
          }, {
            data: 'nb_sta'
          }],
          drawCallback: _ => {
            $('[data-toggle="tooltip"]').tooltip()
          }
        })

        dataTable.on('xhr', _ => {
          let response = dataTable.ajax.json()
          barplot.refresh(response[side])
          uiReceivedResponse()
        })

        /****************
         * Submit form handler
         */
        $("#main-form").submit(event => {
          event.preventDefault();
          uiWaitResponse()
          table.DataTable().ajax.reload()
        });
      })
  }
}

function uiWaitResponse() {
  $("#main-form").find("button[type='submit']").button('loading')
  tabsDisabled(true)
}

function uiReceivedResponse() {
  $("#main-form").find("button[type='submit']").button('reset')
  tabsDisabled(false)
}

function tabsDisabled(stateDisabled) {
  $(".nav-tabs li")
    .toggleClass("disabled", stateDisabled)
    .find('a')
    .attr('data-toggle', stateDisabled ? '' : 'tab')
}