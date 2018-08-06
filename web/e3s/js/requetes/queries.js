/** SpeciesSelector
 * Classe pour gérer le chargement dynamique des espèces en fonction du genre
 * dans les <select> des formulaires
 */
class SpeciesSelector {

  /**
   * Constructeur
   * @param {string} formId Identifiant du formulaire dans le DOM
   * @param {string} toggleId Identifiant de l'élément checkbox pour activer les champs
   * @param {Function} callback Callback à éxecuter après la réponse AJAX
   */
  constructor(formId, toggleId = null, callback = function () {}) {
    this.form = $(formId)

    // Conteneur des inputs
    this.selector = this.form.find(".species-selector")

    // Select inputs
    this.genus = this.selector.find('.genus-select')
    this.species = this.selector.find('.species-select')
    this.taxname = this.selector.find('.taxname-select')

    // Bind 'this' object on event handlers
    this.onGenusSelected = this.onGenusSelected.bind(this)
    this.onSpeciesSelected = this.onSpeciesSelected.bind(this)
    this.toggleActive = this.toggleActive.bind(this)

    // Toggle checkbox pour activer/désactiver les champs
    this.toggle = toggleId ? $(toggleId) : null
    if (this.toggle) {
      this.toggle
        .change(this.toggleActive)
        .trigger('change')
    }
    // Paramètres
    this.withTaxname = (this.taxname.length) ? true : false
    this.callback = callback



    // Promise : résolue quand r&ponse AJAX reçue
    this.promise = new $.Deferred()

    // Déclencher les requêtes AJAX quand changement de champ dans le formulaire
    this.genus
      .change(this.onGenusSelected)
      .trigger('change');
    this.species
      .change(this.onSpeciesSelected);
  }

  /**
   * Gère la promise et l'affichage des spinners pendant les requêtes AJAX
   * @param {boolean} waiting True pendant qu'une requête AJAX est en cours
   */
  toggleWaitingResponse(waiting) {
    if (waiting) { // Attente réponse AJAX : nouvelle promise
      this.promise = new $.Deferred()
      this.form.find(".taxon-spinner").removeClass("hidden")
    } else { // Réponse reçue : résolution promise
      this.promise.resolve()
      this.selector.find("select").selectpicker('refresh')
      this.form.find(".taxon-spinner").addClass("hidden")
      this.callback()
    }
  }

  /**
   * Evénement : changement du genre sélectionné
   */
  onGenusSelected() {
    let self = this

    // Nouvelle requête AJAX
    self.toggleWaitingResponse(true)
    $.post(self.selector.data('url'), {
      genus: self.genus.val()
    }, response => {
      // Format options
      let data = response.data.map(makeOption)
      // Insert options in select
      self.species.html($.makeArray(data))
      // Trigger species change
      self.species.trigger('change')
    })

    function makeOption(data) {
      return Mustache.render(
        '<option value={{species}}>{{species}}</option>',
        data)
    }
  }

  /**
   * Evénement : changement d'espèce sélectionnée
   */
  onSpeciesSelected() {
    let self = this
    // Formulaire inclut taxname : nouvelle requête
    if (self.withTaxname === true) {
      $.post(self.taxname.data('url'), {
          species: self.species.val(),
          genus: self.genus.val()
        },
        response => {
          let data = response.data.map(makeOption)
          self.taxname.html($.makeArray(data))
          self.toggleWaitingResponse(false)
        })
    } else { // Requêtes terminées
      self.toggleWaitingResponse(false)
    }

    function makeOption(data) {
      return Mustache.render(
        '<option value={{id}}>{{taxname}}</option>',
        data)
    }
  }

  /**
   * Active/désactive le formulaire en écoutant un événement
   * 
   * @param {Object} event événement lancé par this.toggle
   */
  toggleActive(event) {
    this.selector.find('select')
      .prop('disabled', !event.target.checked)
      .selectpicker('refresh')
  }
}



/** MethodSelector
 * Classe pour gérer le chargement dynamique des méthodes en fonction du
 * dataset dans les <select> des formulaires
 */
class MethodSelector {
  constructor(formId, mode = "select") { // mode : 'select' or 'checkbox'
    this.form = $(formId)
    this.selector = this.form.find('.method-selector')
    this.mode = mode
    if (this.mode == 'checkbox') {
      this.container = this.selector.find('#method-container')
      this.checkboxTemplate = this.selector.find('#method-form-checkbox')
    }
    this.datasets = this.selector.find('select[name="dataset"]')
    this.methods = this.selector.find('select[name="methode"]')

    // Promise resolved when ready
    this.promise = new $.Deferred()

    this.onDateMotuSelected = this.onDateMotuSelected.bind(this)

    this.datasets.change(this.onDateMotuSelected).trigger('change')
  }

  toggleWaitingResponse(waiting) {

    this.form.find("button[type='submit']").prop("disabled", waiting);
    if (waiting) {
      this.promise = new $.Deferred()
      $(".method-spinner").removeClass("hidden");
    } else {
      this.promise.resolve()
      $(".method-spinner").addClass("hidden");
      this.selector.find("select").selectpicker('refresh')
    }
  }

  onDateMotuSelected() {
    this.toggleWaitingResponse(true)
    var self = this
    $.post(
      this.selector.data('url'), {
        dataset: this.datasets.val()
      },
      response => {
        if (self.mode == 'select') {
          var data = response.data.map(makeOption)
          self.methods.html($.makeArray(data));
        } else if (self.mode == 'checkbox') {
          var data = response.data.map(makeCheckboxes)
          self.container.html($.makeArray(data));
        }
        self.toggleWaitingResponse(false)
      })

    function makeOption(data) {
      return Mustache.render('<option value={{id}}>{{code}}</option>', data);
    }

    function makeCheckboxes(data) {
      return Mustache.render(self.checkboxTemplate.html(), data);
    }
  }
}

/**
 * Raccourci pour animation scroll
 * 
 * @param {string} elt_id identifiant de l'élément à atteindre
 * @param {int} time temps d'animation en ms
 */
function scrollTo(elt_id, time = 1000) {
  $('html, body').animate({
    scrollTop: $(elt_id).offset().top
  }, time);
}


/**
 * Renderer pour tronquer les données trop longues dans datatables
 * @param {int} cutoff nombre de caractères max
 * @param {boolean} wordbreak coupure de mot autorisée
 * @param {boolean} escapeHtml échappement de caractères
 */
jQuery.fn.dataTable.render.ellipsis = function (cutoff, wordbreak, escapeHtml = true, placement = 'top') {
  var esc = function (t) {
    return t
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  };

  return function (d, type, row) {
    // Order, search and type get the original data
    if (type !== 'display') {
      return d;
    }
    if (typeof d !== 'number' && typeof d !== 'string') {
      return d;
    }
    d = d.toString(); // cast numbers
    if (d.length <= cutoff) {
      return d;
    }
    var shortened = d.substr(0, cutoff - 1);
    // Find the last white space character in the string
    if (wordbreak) {
      shortened = shortened.replace(/\s([^\s]*)$/, '');
    }
    // Protect against uncontrolled HTML input
    if (escapeHtml) {
      shortened = esc(shortened);
    }
    return Mustache.render(
      '<span class="ellipsis" data-toggle="tooltip" data-placement="{{placement}}" title="{{title}}">{{shortText}}&#8230;</span>', {
        placement: placement,
        title: esc(d),
        shortText: shortened
      })
  };
};

/**
 * Un renderer pour les liens dans datatables
 * @param {string} url URL de base à utiliser
 * @param {string} col nom de la colonne JSON à utiliser
 * @param {boolean} ellipsis rendu tronqué (donnée de grande taille)
 */
function linkify(url, col, ellipsis = true, placement = 'top') {
  return function (data, type, row) {
    if (data === null) {
      return data
    }
    let res = Mustache.render(
      "<a href='{{baseUrl}}{{id}}'>", {
        baseUrl: url,
        id: row[col],
      });
    if (ellipsis) res += $.fn.dataTable.render.ellipsis(20, true, true, placement)(data, type, row)
    else res += data
    res += "</a>"
    return res
  }
}



const dtconfig = {
  expandColumn: {
    data: null,
    defaultContent: "",
    "className": "control",
    "orderable": false,
  },
  responsiveRenderOptions: {
    "display": $.fn.dataTable.render.ellipsis(20, true),
    "responsive": null,
    "_": null,
  },
  buttons: [{
    extend: 'copy',
    exportOptions: {
      orthogonal: null
    }
  }, {
    extend: "csv",
    exportOptions: {
      orthogonal: null
    }
  }, {
    extend: 'excel',
    exportOptions: {
      orthogonal: null
    }
  }],
  language: {
    'en': {},
    'fr': {
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
    }
  }
}