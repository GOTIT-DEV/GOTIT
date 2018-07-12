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