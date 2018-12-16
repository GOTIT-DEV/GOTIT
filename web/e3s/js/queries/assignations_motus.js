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


/* **************************
 *  Document ready
 **************************** */
$(document).ready(_ => {
  let pageHandler = new AssignMotu("#main-form", "#result-table", "#details-table")
})


/**
 * AssignMotu class
 * Controls form interactions and results display
 */
class AssignMotu {
  constructor(formId, tableId, detailsTableId) {
    // Used to keep details queries consistent with main query
    this.lastQuery = {}
    this.detailsFormData = []

    // Page elements 
    this.form = $(formId)
    this.table = $(tableId)
    this.details = $(detailsTableId)

    // Toggle UI loading state
    this.uiWaitResponse()

    // Init form selector components
    this.speciesSelector = new SpeciesSelector(formId, "#taxa-filter")
    this.methodSelector = new MethodSelector(formId, 'checkbox')

    // Setup tooltip for "Table" select menu
    this.form.find("select#id-level").on('loaded.bs.select', event => {
      $(event.target).parent().tooltip({
        title: $(event.target).data('originalTitle'),
        placement: 'auto'
      })
    })


    // Get current user pulbic infos
    let userAjaxCall = $.ajax({
      url: Routing.generate("user_current"),
      type: "GET"
    })

    /** When selectors are initialized and user info are retrieved : 
     *  init result table
     * */
    $.when(this.speciesSelector.promise, this.methodSelector.promise, userAjaxCall)
      .done((sp, meth, user) => {
        // Disable result export for invited users
        this.dtbuttons = user[0].role === "ROLE_INVITED" ? [] : dtconfig.buttons
        this.initDataTable()
      })
  }

  /**
   * Init result table as DataTable
   * 
   */
  initDataTable() {
    let self = this
    // Don't try to initialize if already init
    if (!$.fn.DataTable.isDataTable("#" + self.table.attr('id'))) {

      // Init DataTable
      self.dataTable = self.table.DataTable({
        autoWidth: false,
        responsive: true,
        ajax: {
          "url": Routing.generate("motu-query"),
          "dataSrc": "rows",
          "type": "POST",
          "data": _ => {
            return self.form.serialize()
          }
        },
        language: dtconfig.language[self.table.data('locale')],
        dom: "lfrtipB",
        buttons: self.dtbuttons,
        columns: [{
          data: "taxname",
          render: linkify("referentieltaxon_show", { col: 'id' })
        },
        {
          data: "methode"
        },
        {
          data: "libelle_motu",
        },
        {
          data: "nb_seq"
        },
        {
          data: "nb_motus"
        },
        {
          data: "id",
          render: (data, type, row) => {
            var template = $("#details-form-template").html();
            return Mustache.render(template, row);
          }
        }
        ],
        drawCallback: _ => {
          // Toggle UI loading done
          self.uiReceivedResponse()
          // Init tooltips
          $('[data-toggle="tooltip"]').tooltip()
          // Init detail forms
          $(".details-form").on('submit', event => {
            event.preventDefault();
            self.detailsFormData = $(event.target).serializeArray()
            self.detailsDataTable.ajax.reload()
            $("#modal-container .modal").modal('show');
          });
        }
      }).on('xhr', _ => {
        let response = self.dataTable.ajax.json()
        // Keep track of last query parameters
        self.lastQuery = response.query
        // Init details table if it is not already done
        if (!$.fn.DataTable.isDataTable("#" + self.details.attr('id'))) {
          self.initModalTable()
        }
      })

      // Init form submit event
      self.form.submit(event => {
        event.preventDefault()
        self.uiWaitResponse()
        self.dataTable.ajax.reload()
      });
    }
  }

  /**
   * Shortcut to build the ajax query parameters for details table
   */
  get ajaxData() {
    let self = this
    let data = self.detailsFormData
    self.lastQuery.criteres.forEach(crit => {
      data.push({
        name: 'criteres[]',
        value: crit
      })
    })
    data.push({
      name: 'niveau',
      value: self.lastQuery.niveau
    });
    return data
  }

  /**
   * Initialize datatable on modal table
   * 
   * @param {string} tableId ID for table element in DOM
   */
  initModalTable() {
    let self = this
    self.detailsDataTable = self.details.DataTable({
      autoWidth: false,
      responsive: true,
      ajax: {
        type: 'POST',
        url: Routing.generate("motu-modal"),
        dataSrc: 'rows',
        data: _ => {
          return self.ajaxData
        }
      },
      language: dtconfig.language[$("html").attr("lang")],
      columns: [{
        data: 'code',
        render: (data, type, row) => {
          let route = row.type ? 'sequenceassembleeext_show' : 'sequenceassemblee_show'
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
      buttons: self.dtbuttons,
      drawCallback: _ => {
        $('[data-toggle="tooltip"]').tooltip()
      }
    })
  }


  /**
   * Toggle UI loading mode
   */
  uiWaitResponse() {
    this.form.find("button[type='submit']").button('loading')
  }

  /**
   * Toggle UI loading done
   */
  uiReceivedResponse() {
    this.form.find("button[type='submit']").button('reset')
  }

} // class AssignMotu