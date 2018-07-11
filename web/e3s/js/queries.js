class SpeciesSelector {
  constructor(formId, withTaxname = false) {
    this.form = $(formId)
    this.selector = this.form.find(".species-selector")
    this.withTaxname = withTaxname

    this.onGenusSelected = this.onGenusSelected.bind(this)
    this.onSpeciesSelected = this.onSpeciesSelected.bind(this)

    this.selector.find('.genus-select')
      .change(this.onGenusSelected)
      .trigger('change');
    this.selector.find('.species-select')
      .change(this.onSpeciesSelected);
  }

  toggleWaitingResponse(waiting) {

    this.form.find("input[type='submit']").prop("disabled", waiting);
    if (waiting) {
      $(".taxon-spinner").removeClass("hidden");
    } else {
      $(".taxon-spinner").addClass("hidden");
    }
  }

  onGenusSelected() {
    var spSel = this
    spSel.toggleWaitingResponse(true)
    $.post(spSel.selector.data('url'), {
      genus: spSel.selector.find('.genus-select').val()
    }, function(response) {
      var data = response.data.map(makeOption)
      spSel.selector.find('.species-select').html($.makeArray(data))
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
        species: spSel.selector.find('.species-select').val(),
        genus: spSel.selector.find('.genus-select').val()
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
    var switchery = new Switchery(html, { size: size });
  });

}

function toggleFormSelect(event) {
  switch (event.target.value) {
    case 0:
      $(".methode-select").prop('disabled', true);
      $(".taxa-select").prop('disabled', true);
      break;

    case 1:
      $(".methode-select").prop('disabled', true);
      $(".taxa-select").prop('disabled', false);
      break;

    case 2:
      $(".methode-select").prop('disabled', false);
      $(".methode-select").prop('disabled', true);
      break;
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