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

import { initDataTable } from './results.js'
import { initMap } from './map.js'
import { scrollToElement } from '../utils.js'

import MotuDistributionForm from "./MotuDistributionForm"

import Vue from "vue"
import i18n from '../i18n'

const vue_form = new Vue({
  el: "#distribution-form",
  i18n,
  ...MotuDistributionForm
})

let map = initMap("motu-geo-map")

$(document).ready(_ => {
  // Update map size when displaying map tab
  $("#geolocation-tab a ").on('shown.bs.tab', _ => {
    scrollToElement('#results-title', 500)
    map.invalidateSize()
  })

  // Init form elements

  // When selectors are initialized and user info are retrieved : init result table
  vue_form.ready.then(() => initDataTable("#result-table", uiReceivedResponse))
})



/**
 * Toggle result tab containing geographical map
 * @param {bool} activeMap 
 */
function toggleTabs(activeMap) {
  activeMap ?
    $("#geolocation-tab a").tab("show") :
    $("#table-tab a").tab('show')

  $("#geolocation-tab")
    .toggleClass('disabled', !activeMap)
    .find("a")
    .attr('data-toggle', activeMap ? 'tab' : '')
    .toggleClass('disabled', !activeMap)
}
/**
 * Toggle UI loading done
 * @param {Object} response JSON response
 */
function uiReceivedResponse(response) {
  vue_form.loading=false
  let showGeo = ('taxname' in response.query && response.rows.length)
  map.resetMarkers()
  let plotParams = map.prepareGeoMarkers(response.rows)
  map.updateMarkers(plotParams.markers)
  map.updateBounds(plotParams.bounds)
  toggleTabs(showGeo)
}
