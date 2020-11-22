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

import 'bootstrap-vue/dist/bootstrap-vue.css'
import MotuDistribution from "./MotuDistribution"
import Vue from "vue"
import i18n from '../i18n'
import filters from '../vue-filters'

Object.entries(filters).forEach(([key, filter]) => {
  Vue.filter(key, filter)
})

const vue_form = new Vue({
  delimiters: ['{|', '|}'],
  components: { MotuDistribution },
  el: "#motu-distribution-vue",
  i18n,
})