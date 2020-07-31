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
// import { SpeciesSelector } from '../form_elements/species_select.js'
// import { MethodSelector } from '../form_elements/method_select.js'
import { initDataTable } from './results.js'

import SpeciesHypothesesForm from './SpeciesHypothesesForm'

import Vue from "vue"
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import i18n from '../i18n'

// Install BootstrapVue
Vue.use(BootstrapVue)

const vue_form = new Vue({
  el: "#species-hypotheses-form",
  i18n,
  ...SpeciesHypothesesForm
  // ...MotuDistributionForm
})

/* **************************
 *  Document ready
 **************************** */
$(document).ready(function () {
  vue_form.ready.then(_ => {
    function ajaxCallback() {
      vue_form.loading = false;
    }
    initDataTable("#result-table-recto", ajaxCallback)
    initDataTable("#result-table-verso", ajaxCallback)
    $(".result-collapse.recto").addClass('show')
  })
})