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

import { styleControl } from './leaflet-style-controls.js'

L.control.styleControl = styleControl


let init_settings = {
  center: [0, 0],
  zoom: 10,
  worldCopyJump: true,
  wheelPxPerZoomLevel: 100,
  minZoom: 1,
  zoomSnap: 0.5,
  maxBounds: L.latLngBounds(
    L.latLng(90, -360),
    L.latLng(-90, 360)
  ),
  fullscreenControl: true,
}

/**
 * Initialize leaflet map in DOM container
 * @param {string} dom_id container DOM id
 * @param {object} settings optional settings
 */
function initBaseMap(dom_id, settings = {}) {
  let map = L.map(dom_id, Object.assign(init_settings, settings))

  // Base ESRI layer
  map.baseLayer = L.esri.basemapLayer("Imagery").addTo(map)
  // Administrative annotations layer
  map.labelsLayer = L.esri.basemapLayer('ImageryLabels')
  // Reset zoom button
  map.resetZoomBtn = L.easyButton('fa-crosshairs',
    _ => console.error("No callback set for reset zoom button"),
    Translator.trans("maps.controls.zoom.reset"))
    .addTo(map)
  // Slider controls for marker styles
  map.sliderControls = L.control.styleControl({
    position: 'bottomright'
  }).addTo(map)

  return map
}

/**
 * Function closure to assign an update function for bounds to a given map object
 * @param {L.map} map Leaflet map object
 */
function updateBounds(map) {
  return function (bounds) {
    map.bounds = [
      [bounds.lat.min, bounds.lon.min],
      [bounds.lat.max, bounds.lon.max]
    ]
    map.fitBounds(map.bounds, { maxZoom: 10, padding: L.point(30, 30) })
    map.resetZoomBtn._states[0].onClick = function () {
      map.fitBounds(map.bounds, { maxZoom: 10, padding: L.point(30, 30) })
    }
  }
}

export { init_settings, initBaseMap, updateBounds }