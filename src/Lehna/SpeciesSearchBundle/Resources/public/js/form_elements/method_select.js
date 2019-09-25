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

/** MethodSelector
 * Class to handle dynamic loading of methods in a selected MOTU dataset
 */
export class MethodSelector {
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