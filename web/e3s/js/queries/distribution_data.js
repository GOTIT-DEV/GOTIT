/*
 * This file is part of the E3sBundle.
 *
 * Copyright (c) 2018 Philippe Grison <philippe.grison@mnhn.fr>
 *
 * E3sBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * E3sBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with E3sBundle.  If not, see <https://www.gnu.org/licenses/>
 * 
 * Author : Louis Duchemin <ls.duchemin@gmail.com>
 */


/**
 * DOCUMENT READY
 */
$(document).ready(_ => {
  let pageHandler = new MotuDistribution("#main-form", "#result-table", "#motu-geo-map")
})

/**
 * Motu Distribution class
 * Controls form interactions and results display
 */
class MotuDistribution {
  constructor(formId, tableId, mapContainerId) {
    this.form = $(formId)
    this.table = $(tableId)
    this.mapContainer = $(mapContainerId)

    this.uiWaitResponse()
    this.geoPlot = new MotuGeoPlot(mapContainerId)
    this.speciesSelector = new SpeciesSelector(formId, "#taxa-filter")
    this.methodSelector = new MethodSelector(formId)

    // Get current user pulbic infos
    let userAjaxCall = fetch(Routing.generate("user_current"), { method: "GET" })

    /** When selectors are initialized and user info are retrieved : 
     *  init result table
     * */
    Promise.all([this.speciesSelector.promise, this.methodSelector.promise, userAjaxCall])
      .then(responses => responses[2].json())
      .then(this.formReady())
  }
  
  formReady() {
    let self = this
    return user => {
      // Disable result export for invited users
      self.dtbuttons = (user.role === "ROLE_INVITED" ? [] : dtconfig.buttons)
      self.initDataTable()
      return Promise.resolve(true)
    }
  }
  /**
   * Update map content with JSON response
   * @param {Object} response JSON response
   */
  updateMap(response) {
    let self = this
    // Update title
    $("#geo-title").html(Mustache.render($("#geo-title-template").html(), {
      taxname: response.rows[0]['taxname'],
      code_methode: response.methode.code,
      dataset: response.methode.libelle_motu
    }))
    // Plot data
    self.geoPlot.plot(response.rows)
    // Loading overlay and tab switching events
    $('#table-tab a ').on('shown.bs.tab', _ => {
      scrollTo('#resultats', 500)
      $(".geo-overlay").hide()
    })
    $("#geolocation-tab a ").on('shown.bs.tab', _ => {
      scrollTo('#resultats', 500)
      self.geoPlot.resize()
    })
    self.geoPlot.resize()
  }


  /**
   * Shortcut to define datatable columns
   */
  get datatableColumns() {
    let self = this
    // Rendering float with precision 3
    const renderNumber = $.fn.dataTable.render.number('', '.', 3)
    // Columns
    let columns = [
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
    return columns
  }

  /**
   * Init result table as datatable
   */
  initDataTable() {
    let self = this
    if (!$.fn.DataTable.isDataTable("#" + self.table.attr('id'))) {
      self.dataTable = self.table.DataTable({
        autoWidth: false,
        responsive: true,
        ajax: {
          "url": Routing.generate('distribution-query'),
          "dataSrc": "rows",
          "type": "POST",
          "data": _ => {
            return self.form.serialize()
          }
        },
        language: dtconfig.language[$("html").attr("lang")],
        dom: "lfrtipB",
        buttons: self.dtbuttons,
        order: [1, 'asc'],
        columns: self.datatableColumns,
        drawCallback: _ => {
          $('[data-toggle="tooltip"]').tooltip()
        }
      })

      self.dataTable.on('xhr', _ => {
        let response = self.dataTable.ajax.json()
        self.uiReceivedResponse(response)
      });

      /*******************************
       * Submit form handler
       ***************************** */
      self.form.submit(event => {
        event.preventDefault();
        self.uiWaitResponse()
        self.dataTable.ajax.reload()
      })
    }
  }

  /**
   * Toggle result tab containing geographical map
   * @param {bool} activeMap 
   */
  toggleTabs(activeMap) {
    if (activeMap) {
      $("#geolocation-tab")
        .removeClass('disabled')
        .find("a")
        .attr('data-toggle', 'tab')
        .removeClass('disabled')
    } else {
      $("#table-tab a").tab('show')
      $("#geolocation-tab")
        .addClass('disabled')
        .find('a')
        .removeAttr('data-toggle')
        .addClass('disabled')
    }
  }

  /**
  * Toggle UI loading mode
  */
  uiWaitResponse() {
    this.form.find("button[type='submit']").button('loading')
  }

  /**
   * Toggle UI loading done
   * @param {Object} response JSON response
   */
  uiReceivedResponse(response) {
    this.form.find("button[type='submit']").button('reset')
    let showGeo = ('taxname' in response.query && response.rows.length)
    if (showGeo) {
      this.updateMap(response)
    }
    this.toggleTabs(showGeo)
  }
}


