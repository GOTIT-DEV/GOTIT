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
import { SpeciesSelector } from '../form_elements/species_select.js'
import { MethodSelector } from '../form_elements/method_select.js'
import { initDataTable } from './results.js'

import Vue from "vue"
import Vuex from "vuex"
import TaxonomySelectPanel from "../components/taxonomy/TaxonomySelectPanel"
import TaxonomyModule from "../components/taxonomy/TaxonomyStore"

Vue.use(Vuex)

const store = new Vuex.Store({
  modules: {
    taxonomy: TaxonomyModule
  }
})

const vue_app = new Vue({
  el: '#species-select',
  template: '<TaxonomySelectPanel with-taxname/>',
  components: { TaxonomySelectPanel },
  store
})


export let ids = {
  form: "#main-form",
  table: "#result-table",
  details: "#details-table"
}


$(document).ready(_ => {
  // let speciesSelector = new SpeciesSelector(ids.form, "#taxa-filter")
  let methodSelector = new MethodSelector(ids.form, 'checkbox')
  // Setup tooltip for "Table" select menu
  $(ids.form).find("select#id-level").on('loaded.bs.select',
    event => {
      $(event.target).parent().tooltip({
        title: $(event.target).data('originalTitle'),
        placement: 'auto'
      })
    })
  /** When selectors are initialized and user info are retrieved : 
     *  init result table
     * */
  Promise.all([methodSelector.promise])
    .then(responses => initDataTable("#result-table"))
})
