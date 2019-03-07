/*
 * This file is part of the SpeciesSearchBundle.
 *
 * Authors : see information concerning authors of GOTIT project in file AUTHORS.md
 *
 * SpeciesSearchBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * E3sBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with SpeciesSearchBundle.  If not, see <https://www.gnu.org/licenses/>
 * 
 * Author : Louis Duchemin <ls.duchemin@gmail.com>
 */


/** SpeciesSelector
 * Class to handle dynamic loading of species on genus selection in forms
 */
class SpeciesSelector {

  /**
   * Constructor
   * @param {string} formId Form selector in DOM
   * @param {string} toggleId Checkbox selector to toggle genus/species selection
   */
  constructor(formId, toggleId = null) {
    this.form = $(formId)

    this.urls = {
      species: Routing.generate('species-in-genus'),
      taxname: Routing.generate('taxname-search')
    }

    // Main container
    this.selector = this.form.find(".species-selector")
    // Select inputs
    this.genus = this.selector.find('.genus-select')
    this.species = this.selector.find('.species-select')
    this.taxname = this.selector.find('.taxname-select')
    // Init tooltips
    this.selector.find("select").on('loaded.bs.select', event => {
      $(event.target).parent().tooltip({
        title: $(event.target).data('originalTitle'),
        placement: 'auto'
      })
    })
    // Bind 'this' object on event handlers
    this.onGenusSelected = this.onGenusSelected.bind(this)
    this.onSpeciesSelected = this.onSpeciesSelected.bind(this)
    this.toggleActive = this.toggleActive.bind(this)

    // Checkbox to toggle active/inactive inputs
    this.toggleBtn = toggleId ? $(toggleId) : null
    if (this.toggleBtn) {
      this.toggleBtn
        .change(this.toggleActive)
        .trigger('change')
      this.toggleBtn.parent().tooltip({
        title: this.toggleBtn.attr('title'),
        placement: 'auto'
      })
    }
    // withTaxname : add input to select taxname (genus + species combination)
    this.withTaxname = (this.taxname.length) ? true : false



    // Promise  : resolved on receiving AJAX response
    this.promise = this.initInputs()
  }

  /**
   * Handle Promise and spinners visibility during AJAx querying
   * @param {boolean} waiting True while AJAX query running
   */
  toggleWaitingResponse(waiting, promise = null) {
    if (waiting) { // waiting AJAX response : create Promise
      this.promise = promise
      this.form.find(".taxon-spinner").removeClass("hidden")
    } else {
      this.selector.find("select").selectpicker('refresh')
      this.form.find(".taxon-spinner").addClass("hidden")
    }
  }

  get genusSpecies() {
    return {
      genus: this.genus.val(),
      species: this.species.val()
    }
  }

  /**
   * Init select inputs options
   */
  initInputs() {
    let self = this
    return this.fetchSpecies()
      .then(self.displaySpecies(self.species))
      .then(taxonObj => {
        return self.withTaxname ?
          self.fetchTaxnames(taxonObj).then(self.displayTaxnames(self.taxname)) :
          Promise.resolve(taxonObj)
      })
      .then(_ => {
        this.genus.change(this.onGenusSelected)
        this.species.change(this.onSpeciesSelected)
        self.toggleWaitingResponse(false)
        return Promise.resolve(true)
      })
  }

  fetchSpecies() {
    this.toggleWaitingResponse(true)
    return fetch(this.urls.species, {
      method: 'POST',
      body: JSON.stringify({ genus: this.genus.val() }),
      credentials: 'include',
      headers: new Headers({ 'Content-Type': 'application/json' })
    })
      .then(response => { return response.json() })
  }

  displaySpecies(speciesSelect) {
    let self = this
    return function (data) {
      // Format options
      let species = data.map(row =>
        Mustache.render('<option value={{species}}>{{species}}</option>', row))
      // Insert options in select
      speciesSelect.html($.makeArray(species))
      return Promise.resolve(self.genusSpecies)
      // Trigger species change
      // speciesSelect.trigger('change')
    }
  }



  fetchTaxnames(taxonObj) {
    return fetch(this.urls.taxname, {
      method: 'POST',
      body: JSON.stringify(taxonObj),
      credentials: 'include',
      headers: new Headers({ 'Content-Type': 'application/json' })
    })
      .then(response => { return response.json() })
  }

  displayTaxnames(taxnameSelect) {
    return function (data) {
      let taxa = data.map(
        row => Mustache.render('<option value={{id}}>{{taxname}}</option>', row))
      taxnameSelect.html(taxa)
      return Promise.resolve(taxa[0])
    }
  }

  /**
 * On genus selection change
 */
  onGenusSelected() {
    let self = this
    this.fetchSpecies().then(self.displaySpecies(self.species)).then(
      _ => self.species.trigger("change")
    )
  }

  /**
   * On species selection
   */
  onSpeciesSelected() {
    let self = this
    // Query for taxname set in species if form element includes taxnames
    if (self.withTaxname === true) {
      self.fetchTaxnames(self.genusSpecies)
        .then(self.displayTaxnames(self.taxname))
        .then(_ => {
          self.toggleWaitingResponse(false)
        })
    } else {
      self.toggleWaitingResponse(false)
    }
  }

  /**
   * Toggle SpeciesSelector form element
   * 
   * @param {Object} event event triggered from this.toggle
   */
  toggleActive(event) {
    this.selector.find('select')
      .prop('disabled', !event.target.checked)
      .selectpicker('refresh')
  }
}



/** MethodSelector
 * Class to handle dynamic loading of methods in a selected MOTU dataset
 */
class MethodSelector {
  constructor(formId, mode = "select") { // mode : 'select' or 'checkbox'
    this.form = $(formId)

    this.urls = {
      datasets: Routing.generate("methodsindate"),
    }

    // Main container
    this.selector = this.form.find('.method-selector')
    this.mode = mode
    if (this.mode == 'checkbox') {
      this.container = this.selector.find('#method-container')
      this.checkboxTemplate = this.selector.find('#method-form-checkbox')
    }
    this.datasets = this.selector.find('select[name="dataset"]')
    this.methods = this.selector.find('select[name="methode"]')

    this.selector.find("select").on('loaded.bs.select', event => {
      $(event.target).parent().tooltip({
        title: $(event.target).data('originalTitle'),
        placement: 'auto'
      })
    })
    this.onDateMotuSelected = this.onDateMotuSelected.bind(this)

    // Promise resolved when ready
    this.promise = this.fetchMethods(this.datasets.val())
      .then(this.displayMethods())
      .then(_ => {
        this.datasets.change(this.onDateMotuSelected())
        return Promise.resolve(true)
      })
  }

  /**
   * 
   * @param {boolean} waiting 
   */
  toggleWaitingResponse(waiting) {
    this.form.find("button[type='submit']").prop("disabled", waiting);
    if (waiting) {
      $(".method-spinner").removeClass("hidden");
    } else {
      $(".method-spinner").addClass("hidden");
      this.selector.find("select").selectpicker('refresh')
    }
  }

  fetchMethods(dataset) {
    this.toggleWaitingResponse(true)
    return fetch(this.urls.datasets, {
      method: "POST",
      body: JSON.stringify({ dataset: dataset }),
      credentials: 'include',
      headers: new Headers({ 'Content-Type': 'application/json' })
    }).then(response => response.json())
  }

  displayMethods() {
    let self = this
    if (self.mode == 'select') {
      return data => {
        let methods = data.map(
          row => Mustache.render('<option value={{id}}>{{code}}</option>', row))
        self.methods.html(methods)
        self.toggleWaitingResponse(false)
        return Promise.resolve(true)
      }
    } else if (self.mode == 'checkbox') {
      return data => {
        let methods = data.map(
          row => Mustache.render(self.checkboxTemplate.html(), row))
        self.container.html(methods);
        self.toggleWaitingResponse(false)
        return Promise.resolve(true)
      }
    }
  }

  onDateMotuSelected() {
    let self = this
    return function () {
      self.fetchMethods(this.value)
        .then(self.displayMethods())
    }
  }
}

/**
 * Fetch current user informations
 */
function fetchCurrentUser() {
  return fetch(Routing.generate("user_current"), { method: "GET", credentials: 'include' })
}

/**
 * Convenience fonction for jquery auto scrolling 
 * @param {string} elt_id target element selector
 * @param {int} time animation time in ms
 */
function scrollTo(elt_id, time = 1000) {
  $('html, body').animate({
    scrollTop: $(elt_id).offset().top
  }, time);
}


/**
 * Renderer to truncate strings in datatable 
 * @param {int} cutoff max characters in string
 * @param {boolean} wordbreak allow word break
 * @param {boolean} escapeHtml escape string
 * @param {string} placement tooltip position
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
      '<span class="ellipsis" data-toggle="tooltip" \
      data-placement="{{placement}}" \
      title="{{title}}">{{shortText}}&#8230;</span>', {
        placement: placement,
        title: esc(d),
        shortText: shortened
      })
  };
};

/**
 * Renderer for URL links in datatables
 * @param {string} url base URL
 * @param {string} col name of JSON attributes to complete URL
 * @param {boolean} ellipsis truncate link representation
 */
function linkify(url,
  { col = null, ellipsis = true, placement = "top", generateRoute = true } =
    { col: null, ellipsis: true, placement: "top", generateRoute: true }) {
  return function (data, type, row) {
    if (data === null) {
      return data
    }
    let path = url
    if (generateRoute === true) {
      path = col === null ?
        Routing.generate(url, { _locale: $("html").attr("lang") }) :
        Routing.generate(url, { id: row[col], _locale: $("html").attr("lang") })
    } else {
      if (col != null) {
        path += row[col]
      }
    }

    let linkText = ellipsis === true ?
      $.fn.dataTable.render.ellipsis(20, true, true, placement)(data, type, row)
      : data

    return Mustache.render("<a href='{{path}}'>{{{text}}}</a>", {
      path: path,
      text: linkText
    })
  }
}


/**
 * Datatables configuration
 */
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
    'en': {
      "sProcessing": "<i class='fa fa-spin fa-spinner'></i>&nbsp;Processing...",
      "sLoadingRecords": "<i class='fa fa-spin fa-spinner'></i>&nbsp;Loading...",
    },
    'fr': {
      "sProcessing": "<i class='fa fa-spin fa-spinner'></i>&nbsp;Traitement en cours...",
      "sSearch": "Rechercher&nbsp;:",
      "sLengthMenu": "Afficher _MENU_ &eacute;l&eacute;ments",
      "sInfo": "Affichage de l'&eacute;l&eacute;ment _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
      "sInfoEmpty": "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ment",
      "sInfoFiltered": "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
      "sInfoPostFix": "",
      "sLoadingRecords": "<i class='fa fa-spin fa-spinner'></i>&nbsp;Chargement en cours...",
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