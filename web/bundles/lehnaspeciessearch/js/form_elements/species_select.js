/*
 * This file is part of the SpeciesSearchBundle.
 *
 * Authors : see information concerning authors of GOTIT project in file AUTHORS.md
 *
 * SpeciesSearchBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * SpeciesSearchBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with SpeciesSearchBundle.  If not, see <https://www.gnu.org/licenses/>
 * 
 * Author : Louis Duchemin <ls.duchemin@gmail.com>
 */


/** SpeciesSelector
 * Class to handle dynamic loading of species on genus selection in forms
 */
export class SpeciesSelector {

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
