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
import { initMap } from './map.js'
import { scrollToElement } from '../utils.js'

const formId = "#main-form"
let map = initMap("motu-geo-map")

$(document).ready(_ => {
  // Update map size when displaying map tab
  $("#geolocation-tab a ").on('shown.bs.tab', _ => {
    scrollToElement('#results-title', 500)
    map.invalidateSize()
  })

  // Init form elements
  let speciesSelector = new SpeciesSelector(formId, "#taxa-filter")
  let methodSelector = new MethodSelector(formId)

  // When selectors are initialized and user info are retrieved : init result table
  Promise.all([speciesSelector.promise, methodSelector.promise])
    .then(responses => initDataTable("#result-table", uiReceivedResponse))
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
  $(formId).find("button[type='submit']").button('reset')
  let showGeo = ('taxname' in response.query && response.rows.length)
  map.resetMarkers()
  let plotParams = map.prepareGeoMarkers(response.rows)
  map.updateMarkers(plotParams.markers)
  map.updateBounds(plotParams.bounds)
  toggleTabs(showGeo)
}
