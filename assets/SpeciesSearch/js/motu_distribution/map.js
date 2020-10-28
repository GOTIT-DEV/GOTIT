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
import Mustache from "mustache"
import "leaflet"
import "leaflet-fullscreen"
import "leaflet-svg-shape-markers"
import chroma from "chroma-js"

import { initBaseMap, updateBounds } from '../map_settings.js'
import { initModalTable } from './seq.modal.js'

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
let nbColorPerScale = 5

/**
 * Initialize leaflet map to display motu distribution
 * @param {String} dom_id container DOM id
 */
export function initMap(dom_id) {
  let modalTablePromise = initModalTable("#sequence-table")
  let map = initBaseMap(dom_id)
  let locale = $("html").attr("lang")

  map.markerLayers = {}
  map.resetFilterBtn = L.easyButton('fa-eye', _ => {
    Object.entries(map.markerLayers).forEach(([motu, layer]) => {
      if (!map.hasLayer(layer)) map.addLayer(layer)
    })
  }, Translator.trans("maps.controls.filter.reset")).addTo(map)

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
        motuStations[motu][station] = Object.assign(row, { sequences: [] })

      motuStations[motu][station].sequences.push(row)

      return motuStations
    }, {})
  }

  map.updateLegend = function (motuStyles) {
    if (this.legend)
      this.removeControl(this.legend)
    let overlayMarks = motuStyles.reduce((legendItems, layerStyling) => {
      let label = Mustache.render($(`template.marker-legend#${layerStyling.style.shape}`).html(), layerStyling.style)
      legendItems[label] = layerStyling.layer
      return legendItems
    }, {})
    overlayMarks[Translator.trans("maps.labels")] = map.labelsLayer
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
    nbColorPerScale = Math.max(3, Math.ceil(motuCount / markerShapes.length))
    this.markerLayers = {}
    let bounds = null
    let scale = chroma.scale('Spectral')
      .colors(Math.min(Math.max(2, motuCount), nbColorPerScale))
    // let altScale = chroma.scale('Spectral')
    //   .colors(Math.max(2, motuCount % nbColorPerScale))
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

      Object.entries(stations).forEach(([station, stationInfo]) => {
        let lat = stationInfo.latitude
        let lon = stationInfo.longitude
        let popupContent = L.DomUtil.create('div')
        popupContent.innerHTML = Mustache.render($("template#leaflet-popup-template").html(), stationInfo)
        let marker = L.shapeMarker([lat, lon], style).bindPopup(popupContent)
        map.markerLayers[motu].addLayer(marker)

        $(popupContent).find(".btn-marker-isolate").click(_ => {
          Object.entries(map.markerLayers).forEach(([targetMotu, layer]) => {
            if (motu !== targetMotu && map.hasLayer(layer))
              map.removeLayer(layer)
          })
        })

        modalTablePromise.then(modalTable => {
          $(popupContent).find(".btn-seq-modal").click(_ => {
            modalTable.clear()
            modalTable.rows.add(stationInfo.sequences)
            modalTable.draw()
            $("h4.modal-title").html(
              Mustache.render('<a href="{{station_url}}">{{station_code}}</a> // MOTU {{motu}}', stationInfo)
            )
            $("#modal-container .modal").modal('show');
          })
        })


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
