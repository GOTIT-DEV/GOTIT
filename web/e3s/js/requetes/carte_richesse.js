class CarteRichesse {
  constructor(formId, tableId, mapContainerId) {
    this.form = $(formId)
    this.table = $(tableId)
    this.mapContainer = $(mapContainerId)

    initSwitchery(".switchbox")
    $('.switchbox')
      .change(toggleTaxonForm('select'))
      .trigger('change')

    this.urls = {
      refTaxon: this.table.find("th#col-taxname").data('linkUrl'),
      station: this.table.find("th#col-station").data('linkUrl')
    }
    this.seqTypes = {
      interne: this.table.data('vocabSeqInt'),
      externe: this.table.data('vocabSeqExt'),
    }

    this.uiWaitResponse()
    this.geoPlot = new MotuGeoPlot(mapContainerId)
    this.speciesSelector = new SpeciesSelector(formId, true)
    this.methodSelector = new MethodSelector(formId)
    this.getAvailableMethods()
    // Formulaires prêts : initialiser datatables
    $.when(this.speciesSelector.promise, this.methodSelector.promise)
      .done(_ => {
        this.initDataTable()
      })

  }

  getAvailableMethods() {
    let self = this
    $.ajax({
      type: "GET",
      url: self.table.data('urlMethodes'),
      data: {},
      success: response => {
        self.methodes = response
      }
    })
  }

  get formActive() {
    return this.form.find('.switchbox').is(':checked')
  }

  /**
   * Active le mode attente / loading
   */
  uiWaitResponse() {
    this.form.find("button[type='submit']").button('loading')
    this.disableTabs()
  }

  /**
   * Désactive le mode attente ; mettre à jour les onglets
   * @param {Object} response réponse JSON
   */
  uiReceivedResponse(response) {
    this.form.find("button[type='submit']").button('reset')
    let showGeo = (this.formActive && response.geo.length)
    if (showGeo) {
      this.updateMap(response)
    }
    this.enableTabs(showGeo)
  }


  disableTabs() {
    $("#table-tab a").tab('show')
    $("#result-tabs>li")
      .addClass('disabled')
      .find("a")
      .removeAttr('data-toggle')
      .addClass('disabled')
  }

  enableTabs(both = false) {
    let target = both ? "#result-tabs>li" : "#table-tab"
    $(target)
      .removeClass('disabled')
      .find('a')
      .attr('data-toggle', 'tab')
      .removeClass('disabled')
  }

  /**
   * Met à jour la carte avec les données JSON
   * @param {Object} response réponse JSON
   */
  updateMap(response) {
    let self = this
    // Update title
    $("#geo-title").html(Mustache.render($("#geo-title-template").html(), {
      taxname: response.geo[0]['taxname'],
      code_methode: response.methode.code,
      dataset: Date.parse(response.methode.date_dataset.date).toString('yyyy')
    }));
    // Plot data
    self.geoPlot.plot(response.geo)
    // Overlay et événements changement d'onglet
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


  get datatableColumns() {
    let self = this
    const renderNumber = $.fn.dataTable.render.number('', '.', 3)
    let columns = [
      dtconfig.expandColumn, {
        data: "taxname",
        render: linkify(self.urls.refTaxon, 'id', true)
      }, {
        data: 'code',
        render: (data, type, row) => {
          let lookUpAttr = row.type ? 'urlExt' : 'urlInt'
          let baseUrl = self.table.find("#col-code-seq").data(lookUpAttr)
          return linkify(baseUrl, 'id', true)(data, type, row)
        }
      }, {
        data: "type_seq",
        render: data => {
          return data ? self.seqTypes.externe : self.seqTypes.interne
        }
      },
      {
        data: "accession_number",
        render: linkify('https://www.ncbi.nlm.nih.gov/nuccore/', 'accession_number', false)
      }
    ]
    this.methodes.forEach(element => {
      columns.push({
        data: element.code.toLowerCase() + "_" + Date.parse(element.date_dataset.date).toString('yyyy'),
        defaultContent: "-"
      })
    })
    columns.push.apply(columns, [{
        data: "latitude",
        render: renderNumber,
      },
      {
        data: "longitude",
        render: renderNumber,
        defaultContent: "-"
      },
      {
        data: "code_station",
        render: linkify(self.urls.station, 'id', true)
      },
      {
        data: "commune"
      },
      {
        data: "pays"
      }
    ])
    return columns
  }

  /**
   * Initialise datatables pour remplir la table *
   * en utilisant les données du formulaire
   */
  initDataTable() {
    let self = this
    if (!$.fn.DataTable.isDataTable("#" + self.table.attr('id'))) {
      self.dataTable = self.table.DataTable({
        autoWidth: false,
        responsive: true,
        ajax: {
          "url": self.form.data("url"),
          "dataSrc": "rows",
          "type": "POST",
          "data": _ => {
            return self.form.serialize()
          }
        },
        dom: "lfrtipB",
        buttons: dtconfig.buttons,
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
}



/**
 * DOCUMENT READY
 */
$(document).ready(function () {
  let pageHandler = new CarteRichesse("#main-form", "#result-table", "#motu-geo-map")
})