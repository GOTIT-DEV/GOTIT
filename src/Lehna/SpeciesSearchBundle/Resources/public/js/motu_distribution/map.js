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

import { initBaseMap, updateBounds } from '../map_settings.js'

let radius = 6
let markerStyle = {
  color: 'black',
  fillOpacity: 1,
  radius: radius,
  opacity: 1,
  strokeOpacity: 0,
  weight: 1
}

const markerShapes = ["circle", "diamond", "triangle", "square"]
const nbColorPerScale = 8


export function initMap(dom_id) {

  let map = initBaseMap(dom_id)
  let locale = $("html").attr("lang")

  map.markerLayers = {}
  map.sliderControls.opacitySlider.value = markerStyle.opacity


  map.sortByMotuStations = function (json) {
    return json.reduce((motuStations, row) => {
      let motu = row.motu
      let station = row.id_sta
      row.station_url = Routing.generate("station_show", { id: row.id_sta, _locale: locale })
      let seq_route = row.seq_type ? "sequenceassembleeext_show" : "sequenceassemblee_show"
      row.seq_url = Routing.generate(seq_route, { id: row.id, _locale: locale })
      if (row.altitude === null) row.altitude = '-'

      if (!(motu in motuStations))
        motuStations[motu] = {}
      if (!(station in motuStations[motu]))
        motuStations[motu][station] = []

      motuStations[motu][station].push(row)

      return motuStations
    }, {})
  }

  map.updateLegend = function (motuStyles) {
    if (this.legend)
      this.removeControl(this.legend)
    let overlayMarks = motuStyles.reduce((legendItems, layerStyling) => {
      let label = Mustache.render($("template#marker-legend").html(), layerStyling.style)
      legendItems[label] = layerStyling.layer
      return legendItems
    }, {})
    overlayMarks.Borders = map.labelsLayer
    this.legend = L.control
      .layers(null, overlayMarks)
      .addTo(this)
  }

  /**
* Builds markers to be displayed on map from a JSON object
* @param {Object} json Station sampling response
*/
  map.prepareGeoMarkers = function (json) {
    let dataset = map.sortByMotuStations(json)
    let motuCount = Object.keys(dataset).length
    this.markerLayers = {}
    let bounds = null
    let scale = chroma.scale('Spectral')
      .colors(Math.min(Math.max(2, motuCount), nbColorPerScale))
    let altScale = chroma.scale('Spectral')
      .colors(Math.max(2, motuCount % nbColorPerScale))
    let motuStyles = []
    let motuIdx = 0

    Object.entries(dataset).forEach(([motu, stations]) => {
      motuIdx += 1
      map.markerLayers[motu] = L.layerGroup()
      let shape = markerShapes[Math.floor(motuIdx / nbColorPerScale)]
      let color = scale[motuIdx % nbColorPerScale]
      let style = Object.assign({
        shape: shape,
        fillColor: color,
        motu: motu
      }, markerStyle)

      Object.entries(stations).forEach(([station, sequences]) => {
        let lat = sequences[0].latitude
        let lon = sequences[0].longitude
        map.markerLayers[motu].addLayer(
          L.shapeMarker([lat, lon], style)
            .bindPopup(Mustache.render($("template#leaflet-popup-template").html(), sequences[0]))
        )
        if (bounds === null) {
          bounds = {
            lat: {
              min: lat,
              max: lat
            },
            lon: {
              min: lon,
              max: lon
            }
          }
        } else {
          bounds = {
            lat: {
              min: Math.min(bounds.lat.min, lat),
              max: Math.max(bounds.lat.max, lat)
            },
            lon: {
              min: Math.min(bounds.lon.min, lon),
              max: Math.max(bounds.lon.max, lon),
            }
          }
        }
      })
      motuStyles.push({ style: style, layer: map.markerLayers[motu] })
    })

    map.updateLegend(motuStyles)
    return {
      markers: this.markerLayers,
      bounds: bounds
    }
  }

  map.updateMarkers = function (markers) {
    Object.entries(markers).forEach(([motu, layerGroup]) => {
      layerGroup.addTo(map)
    })

    L.DomEvent.on(this.sliderControls.radiusSlider, "input",
      (event) => {
        let radius = event.target.value
        let stroke = 2
        for (let layerGroup in markers)
          markers[layerGroup].invoke("setRadius", parseInt(radius))
      })

    L.DomEvent.on(this.sliderControls.opacitySlider, "input",
      (event) => {
        let opacity = event.target.value
        for (let layerGroup in markers)
          markers[layerGroup].invoke("setStyle", { fillOpacity: opacity })
      })
  }

  map.resetMarkers = function (formData) {
    Object.entries(this.markerLayers).forEach(([motu, layerGroup]) => {
      layerGroup.clearLayers()
    })
  }

  map.updateBounds = updateBounds(map)

  return map
}
