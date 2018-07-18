/** SpeciesSelector
 * Classe pour gérer le chargement dynamique des espèces en fonction du genre
 * dans les <select> des formulaires
 */
class SpeciesSelector {
  constructor(formId, withTaxname = false, callback = function() {}) {
    this.form = $(formId)
    this.selector = this.form.find(".species-selector")
    this.genus = this.selector.find('.genus-select')
    this.species = this.selector.find('.species-select')
    this.withTaxname = withTaxname
    this.callback = callback

    this.onGenusSelected = this.onGenusSelected.bind(this)
    this.onSpeciesSelected = this.onSpeciesSelected.bind(this)
    this.toggleWaitingResponse = this.toggleWaitingResponse.bind(this)
      // Promise resolved when ready
    this.promise = new $.Deferred()
      // Init event handlers
    this.genus
      .change(this.onGenusSelected)
      .trigger('change');
    this.species
      .change(this.onSpeciesSelected);
  }

  toggleWaitingResponse(waiting) {

    //this.form.find("button[type='submit']").prop("disabled", waiting);
    if (waiting) {
      this.promise = new $.Deferred()
      $(".taxon-spinner").removeClass("hidden");
    } else {
      this.promise.resolve()
      $(".taxon-spinner").addClass("hidden");
      this.callback()
    }
  }

  onGenusSelected() {
    var spSel = this
    spSel.toggleWaitingResponse(true)
    $.post(spSel.selector.data('url'), {
      genus: spSel.genus.val()
    }, function(response) {
      var data = response.data.map(makeOption)
      spSel.species.html($.makeArray(data))
      console.log(spSel.withTaxname)
      if (spSel.withTaxname === true) {
        spSel.onSpeciesSelected();
        //spSel.toggleWaitingResponse(false)
      } else {
        spSel.toggleWaitingResponse(false)
      }
    });

    function makeOption(data) {
      return '<option value=' + data.species + '>' + data.species + '</option>'
    }
  }

  onSpeciesSelected() {
    var spSel = this
      //spSel.toggleWaitingResponse(true)
    var taxnameSel = spSel.selector.find('.taxname-select')
    $.post(taxnameSel.data('url'), {
        species: spSel.species.val(),
        genus: spSel.genus.val()
      },
      function(response) {
        var data = response.data.map(makeOption)
        taxnameSel.html($.makeArray(data))
        spSel.toggleWaitingResponse(false)
      });

    function makeOption(data) {
      return '<option value=' + data.id + '>' + data.taxname + '</option>'
    }
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
      console.log('checkbox')
    }
    this.datasets = this.selector.find('.date-motu-select')
    this.methods = this.selector.find('.method-select')

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
    }
  }

  onDateMotuSelected() {
    this.toggleWaitingResponse(true)
    var methSel = this
    $.post(
      this.selector.data('url'), { date_methode: this.datasets.val() },
      function(response) {

        if (methSel.mode == 'select') {
          var data = response.data.map(makeOption)
          methSel.methods.html($.makeArray(data));
        } else if (methSel.mode == 'checkbox') {
          var data = response.data.map(makeCheckboxes)
          methSel.container.html($.makeArray(data));
        }
        methSel.toggleWaitingResponse(false)
      });

    function makeOption(data) {
      return Mustache.render('<option value={{id}}>{{code}}</option>', data);
    }

    function makeCheckboxes(data) {
      return Mustache.render(methSel.checkboxTemplate.html(), data);
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
 * Raccourci d'initialisation des éléments switchery
 * 
 * @param {string} selector element.s à initialiser
 * @param {string} size taille du switch
 */
function initSwitchery(selector, size = 'small') {

  var elems = Array.prototype.slice.call(document.querySelectorAll(selector));
  elems.forEach(function(html) {
    return new Switchery(html, { size: size });
  });

}


jQuery.fn.dataTable.render.ellipsis = function(cutoff, wordbreak, escapeHtml) {
  var esc = function(t) {
    return t
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  };

  return function(d, type, row) {
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
    return '<span class="ellipsis" data-toggle="tooltip" data-placement="top" title="' + esc(d) + '">' + shortened + '&#8230;</span>';
  };
};

/**
 * Un renderer pour les liens dans datatables
 * @param {string} url URL de base à utiliser
 * @param {string} col nom de la colonne JSON à utiliser
 * @param {boolean} ellipsis rendu tronqué (donnée de grande taille)
 */
function linkify(url, col, ellipsis = true) {
  return function(data, type, row) {
    let res = Mustache.render(
      "<a href='{{baseUrl}}{{id}}'>", {
        baseUrl: url,
        id: row[col],
      });
    if (ellipsis) res += $.fn.dataTable.render.ellipsis(20, true)(data, type, row)
    else res += data
    res += "</a>"
    return res
  }
}

/**
 * Active/désactive des formulaires en écoutant un événement
 * 
 * @param {Object} event événement lancé par la switchbox taxon
 */
function toggleTaxonForm() {
  args = Array.from(arguments)
  return function (event) {
    let disable = !event.target.checked
    args.forEach(element => {
      $(element).prop('disabled', disable)
    });
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
}