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

import {scrollToElement} from '../queries.js'

/**
 * Update map content with JSON response
 * @param {Object} response JSON response
 */
export function updateMap(response) {
  // Update title
  $("#geo-title").html(Mustache.render($("#geo-title-template").html(), {
    taxname: response.rows[0]['taxname'],
    code_methode: response.methode.code,
    dataset: response.methode.libelle_motu
  }))
  // Plot data
  // self.geoPlot.plot(response.rows)
  // Loading overlay and tab switching events
  $('#table-tab a ').on('shown.bs.tab', _ => {
    scrollToElement('#resultats', 500)
    $(".geo-overlay").hide()
  })
  $("#geolocation-tab a ").on('shown.bs.tab', _ => {
    scrollToElement('#resultats', 500)
    // self.geoPlot.resize()
  })
  // self.geoPlot.resize()
}