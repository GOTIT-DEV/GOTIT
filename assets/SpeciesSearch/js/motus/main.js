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
import Vue from "vue"
import MotuForm from "./MotuForm"
import { initDataTable } from './results.js'

const vue_form = new Vue({
  el: '#main-form',
  components: { "motuform": MotuForm },
  computed: {
    ready() {
      return this.$refs.motuForm.ready
    }
  }
})


export let ids = {
  form: "#main-form",
  table: "#result-table",
  details: "#details-table"
}

$(document).ready(_ => {
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
  vue_form.ready.then(() => initDataTable("#result-table", _ => vue_form.$refs.motuForm.loading = false))
})
