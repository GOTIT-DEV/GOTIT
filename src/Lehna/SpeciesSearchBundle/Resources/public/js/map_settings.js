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

function initBaseMap(dom_id) {
  let map = L.map(dom_id, init_settings)

  map.baseLayer = L.esri.basemapLayer("Imagery").addTo(map)
  map.labelsLayer = L.esri.basemapLayer('ImageryLabels')

  map.resetZoomBtn = L.easyButton('fa-crosshairs', _ => _).addTo(map)

  map.sliderControls = L.control.styleControl({ position: 'bottomright' }).addTo(map)

  return map
}

function radiusToDasharray(radius, n = 10) {
  let length = 2 * Math.PI * radius / n
  return `${length},${length}`
}

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

export { init_settings, initBaseMap, radiusToDasharray, updateBounds }