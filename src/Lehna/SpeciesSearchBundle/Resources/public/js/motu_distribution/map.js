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

import { initBaseMap, radiusToDasharray, updateBounds } from '../map_settings.js'

let radius = 5
// let outerStroke = radius * 3 / 5
let nDashes = 10
let markerStyles = {
  co1: {
    color: 'black',
    fillColor: 'lime',
    fillOpacity: 1,
    radius: radius,
    opacity: 0.75,
    strokeOpacity: 1,
    weight: 1
  },
  bioMat: {
    color: "#ff4f09",
    fillOpacity: 0,
    radius: radius,
    dashArray: null
  },
  bioMatExt: {
    color: '#00b7ff',
    // weight: outerStroke,
    fillOpacity: 0,
    radius: radius,
    dashArray: radiusToDasharray(radius, nDashes),
    lineJoin: 'bevel',
    lineCap: "butt",
    pane: "bioMatExtPane"
  },
  lmpLine: { color: 'lime', weight: 2, dashArray: '4,10' }
}


export function initMap(dom_id) {
  let map = initBaseMap(dom_id)

  map.markerLayers = {}

  /**
* Builds markers to be displayed on map from a JSON object
* @param {Object} json Station sampling response
*/
  map.prepareGeoMarkers = function (json) {

    this.markerLayers = {}
    // Marker layers
    return json.reduce((plotParams, row) => {
      let lat = row.latitude,
        lon = row.longitude,
        motu = row.motu
      row.station_url = Routing.generate("station_show", { id: row.station_id, _locale: $("html").attr("lang") })

      if (row.altitude === null) row.altitude = '-'

      if (!(motu in plotParams.markers)) {
        plotParams.markers[motu] = L.layerGroup()
      }
      plotParams.markers[motu].addLayer(
        L.circleMarker([lat, lon], markerStyles.co1).bindPopup("Hey")
      )

      if (plotParams.bounds === null) {
        plotParams.bounds = {
          lat: {
            min: lat,
            max: lat
          },
          lon: {
            min: lon,
            max: lon
          }
        }
      }

      plotParams.bounds = {
        lat: {
          min: Math.min(plotParams.bounds.lat.min, lat),
          max: Math.max(plotParams.bounds.lat.max, lat)
        },
        lon: {
          min: Math.min(plotParams.bounds.lon.min, lon),
          max: Math.max(plotParams.bounds.lon.max, lon),
        }
      }
      return plotParams
    }, {
        markers: this.markerLayers,
        bounds: null
      })
  }

  map.updateMarkers = function (markers) {
    for (let layerGroup in markers) {
      markers[layerGroup].addTo(map)
    }

    // this.updateLegend(markers)

    L.DomEvent.on(this.sliderControls.radiusSlider, "input",
      (event) => {
        let radius = event.target.value
        let stroke = radius * 3 / 5
        for (let layerGroup in markers)
          markers[layerGroup].invoke("setStyle", { radius: radius, weight: stroke })
      })

    L.DomEvent.on(this.sliderControls.opacitySlider, "input",
      (event) => {
        let opacity = event.target.value
        for (let layerGroup in markers)
          markers[layerGroup].invoke("setStyle", { fillOpacity: opacity })
      })
  }

  map.updateBounds = updateBounds(map)

  return map
}
