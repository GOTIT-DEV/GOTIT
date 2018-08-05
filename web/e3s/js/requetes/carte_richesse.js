class CarteRichesse {
  constructor(formId, tableId, mapContainerId) {
    this.form = $(formId)
    this.table = $(tableId)
    this.mapContainer = $(mapContainerId)

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
    this.speciesSelector = new SpeciesSelector(formId, "#taxa-filter")
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

  /**
   * Active le mode attente / loading
   */
  uiWaitResponse() {
    this.form.find("button[type='submit']").button('loading')
    // this.disableTabs()
  }

  /**
   * Désactive le mode attente ; mettre à jour les onglets
   * @param {Object} response réponse JSON
   */
  uiReceivedResponse(response) {
    this.form.find("button[type='submit']").button('reset')
    let showGeo = ('taxname' in response.query && response.rows.length)
    if (showGeo) {
      this.updateMap(response)
    }
    this.toggleTabs(showGeo)
  }


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
   * Met à jour la carte avec les données JSON
   * @param {Object} response réponse JSON
   */
  updateMap(response) {
    let self = this
    // Update title
    $("#geo-title").html(Mustache.render($("#geo-title-template").html(), {
      taxname: response.rows[0]['taxname'],
      code_methode: response.methode.code,
      dataset: Date.parse(response.methode.date_dataset.date).toString('yyyy')
    }));
    // Plot data
    self.geoPlot.plot(response.rows)
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
      },
      {
        data: 'motu'
      }, {
        data: "latitude",
        render: renderNumber,
      },
      {
        data: "longitude",
        render: renderNumber,
        defaultContent: ""
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
    ]
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
        language: {
          "sProcessing": "Traitement en cours...",
          "sSearch": "Rechercher&nbsp;:",
          "sLengthMenu": "Afficher _MENU_ &eacute;l&eacute;ments",
          "sInfo": "Affichage de l'&eacute;l&eacute;ment _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
          "sInfoEmpty": "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ment",
          "sInfoFiltered": "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
          "sInfoPostFix": "",
          "sLoadingRecords": "Chargement en cours...",
          "sZeroRecords": "Aucun &eacute;l&eacute;ment &agrave; afficher",
          "sEmptyTable": "Aucune donn&eacute;e disponible dans le tableau",
          "oPaginate": {
            "sFirst": "Premier",
            "sPrevious": "Pr&eacute;c&eacute;dent",
            "sNext": "Suivant",
            "sLast": "Dernier"
          },
          "oAria": {
            "sSortAscending": ": activer pour trier la colonne par ordre croissant",
            "sSortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
          },
          "select": {
            "rows": {
              _: "%d lignes séléctionnées",
              0: "Aucune ligne séléctionnée",
              1: "1 ligne séléctionnée"
            }
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