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

    // Init event handlers
    this.genus
      .change(this.onGenusSelected)
      .trigger('change');
    this.species
      .change(this.onSpeciesSelected);
  }

  toggleWaitingResponse(waiting) {

    this.form.find("input[type='submit']").prop("disabled", waiting);
    if (waiting) {
      $(".taxon-spinner").removeClass("hidden");
    } else {
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
      if (spSel.withTaxname) {
        spSel.onSpeciesSelected();
      } else {
        spSel.toggleWaitingResponse(false)
      }
    });

    function makeOption(data) {
      return '<option value=' + data.species + '>' + data.species + '</option>'
    }
  }

  onSpeciesSelected() {
    this.toggleWaitingResponse(true)
    var spSel = this
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

function scrollTo(elt_id, time = 1000) {
  $('html, body').animate({
    scrollTop: $(elt_id).offset().top
  }, time);
}

function initSwitchery(selector, size = 'small') {

  var elems = Array.prototype.slice.call(document.querySelectorAll(selector));
  elems.forEach(function(html) {
    return new Switchery(html, { size: size });
  });

}


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
    this.onDateMotuSelected = this.onDateMotuSelected.bind(this)

    this.datasets.change(this.onDateMotuSelected).trigger('change')
  }

  toggleWaitingResponse(waiting) {

    this.form.find("input[type='submit']").prop("disabled", waiting);
    if (waiting) {
      $(".method-spinner").removeClass("hidden");
    } else {
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
          console.log($.makeArray(data))
          methSel.container.html($.makeArray(data));
          console.log(methSel.container.html())
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

function onDateMotuSelected(dateFormModule, submitBtn, mode = "select") { // mode : 'select' or 'checkbox'
  var module = $(dateFormModule);
  var spinners = $(module.data('spinner'));
  var dateMotu = module.find("select[name='date_methode']");
  var methode = module.find("select[name='methode']");
  $(submitBtn).prop("disabled", true);
  $(spinners).removeClass("hidden");
  $.post(
    module.data('url'), { date_methode: dateMotu.val() },
    function(response) {
      if (mode == 'select') {
        methode.html('');
        for (i = 0; i < response.data.length; i++) {
          methode.append(
            Mustache.render('<option value={{id}}>{{code}}</option>', response.data[i]));
        }
      } else if (mode == 'checkbox') {
        var container = module.find('#methodes-container');
        var template = module.find("#method-form-checkbox").html();
        container.html('');
        for (i = 0; i < response.data.length; i++) {
          container.append(Mustache.render(template, response.data[i]));
        }
      }
      $(submitBtn).prop("disabled", false);
      $(spinners).addClass("hidden");
    });
}