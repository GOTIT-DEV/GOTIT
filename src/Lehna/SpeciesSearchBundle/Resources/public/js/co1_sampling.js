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


/* **************************
 *  Document ready
 **************************** */
// Init map
let stationMap = L.map('station-geo-map', {
  center: [40, 0],
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
})

stationMap.createPane("bioMatExtPane")
stationMap.getPane("bioMatExtPane").style.zIndex = 698

stationMap.createPane("co1Pane")
stationMap.getPane("co1Pane").style.zIndex = 699

// Tile Layer 
let basemap = L.esri.basemapLayer("Imagery").addTo(stationMap)
let labels = L.esri.basemapLayer('ImageryLabels')

// Legend control
let legend = L.control.layers(null).addTo(stationMap)

// let lmpLine =
let radius = 5
let outerStroke = radius * 3 / 5
let nDashes = 10
let markerStyles = {
  co1: {
    color: 'black',
    fillColor: 'lime',
    fillOpacity: 1,
    radius: radius - outerStroke / 2 - 0.5,
    opacity: 0.75,
    weight: 1,
    pane: "co1Pane"
  },
  bioMat: {
    color: "#ff4f09",
    fillOpacity: 0,
    radius: radius,
    dashArray: null
  },
  bioMatExt: {
    color: '#00b7ff',
    weight: outerStroke,
    fillOpacity: 0,
    radius: radius,
    dashArray: radiusToDasharray(radius, nDashes),
    lineJoin: 'bevel',
    lineCap: "butt",
    pane: "bioMatExtPane"
  },
  lmpLine: { color: 'lime', weight: 2, dashArray: '4,10' }
}
let layerGroups = {
  co1: L.layerGroup(),
  bioMat: L.layerGroup(),
  bioMatExt: L.layerGroup(),
  lmpLines: L.layerGroup()
}

let sliders = {
  radius: $("#marker-radius-slider").slider(),
  opacity: $("#marker-opacity-slider").slider()
}
let resetZoom = L.easyButton('fa-crosshairs', _ => _).addTo(stationMap);

$(document).ready(_ => {

  uiWaitResponse()

  $(".modal-dialog").css("width", "95vw")

  let speciesSelector = new SpeciesSelector("#main-form", "#taxa-filter")

  // Init species selector
  speciesSelector.promise.then(_ => {
    initDataTable("#result-table")
  })


})

/* **************************
 *  Initialize datatable
 **************************** */

function radiusToDasharray(radius, n = 10) {
  let length = 2 * Math.PI * radius / n
  return `${length},${length}`
}

function fetchSamplingCoords(formData) {
  return fetch(Routing.generate("co1-geocoords"), {
    method: 'POST',
    body: formData,
    credentials: 'include'
  })
    .then(response => { return response.json() })
}

function displayModal(json) {
  let plotParams = prepareGeoMarkers(json.stations);
  let markers = plotParams.markers,
    bounds = plotParams.bounds
  for (layerGroup in markers) {
    markers[layerGroup].addTo(stationMap)
  }


  bounds = [
    [bounds.lat.min, bounds.lon.min],
    [bounds.lat.max, bounds.lon.max]
  ]
  $("#detailsModal").on("shown.bs.modal", ev => {
    stationMap.invalidateSize()
    stationMap.fitBounds(bounds, { maxZoom: 10, padding: L.point(20, 20) })
  })
  resetZoom._states[0].onClick = function () {
    stationMap.fitBounds(bounds, { maxZoom: 10, padding: L.point(20, 20) })
  }


  let bioMatExtLegend = $("template#bio-mat-ext-legend").html()
  let overlayMarks = {
    [$("template#co1-legend").html()]: markers.co1,
    [$("template#bio-mat-legend").html()]: markers.bioMat,
    [$("template#bio-mat-ext-legend").html()]: markers.bioMatExt,
    [$("template#lmp-legend").html()]: markers.lmpLines,
    "Borders": labels
  }
  stationMap.removeControl(legend)
  legend = L.control
    .layers(null, overlayMarks)
    .addTo(stationMap)


  sliders.radius
    .on("change", (event) => {
      let radius = event.value.newValue
      let stroke = radius * 3 / 5
      markers.bioMat.invoke("setRadius", radius)
      markers.bioMatExt.invoke("setRadius", radius)
      markers.co1.invoke("setRadius", radius - stroke / 2 - 0.5)
      markers.bioMat.invoke("setStyle", { weight: stroke })
      markers.bioMatExt.invoke("setStyle", { weight: stroke })
    })

  sliders.opacity
    .on("change", (event) => {
      let opacity = event.value.newValue
      markers.bioMat.invoke("setStyle", { opacity: opacity })
      markers.bioMatExt.invoke("setStyle", { opacity: opacity })
      markers.co1.invoke("setStyle", { fillOpacity: opacity })
      markers.lmpLines.invoke("setStyle", { opacity: opacity })
    })

  $("#detailsModal").find(".modal-title").html(
    Mustache.render($("template#details-modal-title").html(), {
      taxname: json.taxname
    }))
  $(".geo-overlay").show()
  $("#detailsModal").modal('show')
}



function prepareGeoMarkers(json) {
  // Marker layers

  return json.reduce((plotParams, row) => {
    let lat = row.latitude,
      lon = row.longitude
    if(row.altitude === null) row.altitude = '-';
    if (row["co1"] === true) {
      plotParams.markers.co1
        .addLayer(L.circleMarker([lat, lon], markerStyles.co1)
          .bindPopup(
            Mustache.render($("template#leaflet-popup-template").html(), row)
          )
        )
    }
    if (row["lm_id"] === null) {
      plotParams.markers.bioMatExt
        .addLayer(L.circleMarker([lat, lon], markerStyles.bioMatExt)
          .bindPopup(
            Mustache.render($("template#leaflet-popup-template").html(), row)
          ))
    } else {
      plotParams.markers.bioMat
        .addLayer(L.circleMarker([lat, lon], markerStyles.bioMat)
          .bindPopup(
            Mustache.render($("template#leaflet-popup-template").html(), row)
          ))
    }

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
      markers: layerGroups,
      bounds: null
    })
}

function resetLayers(formData) {
  for (group in layerGroups) {
    layerGroups[group].clearLayers()
  }

  let lmp = {
    bioMat: formData.get("lmp_lm"),
    co1: formData.get("lmp_co1")
  }

  if (lmp.bioMat !== "")
    layerGroups.lmpLines.addLayer(
      L.polyline([
        [lmp.bioMat, -720], [lmp.bioMat, 720]
      ], markerStyles.lmpLine)
        .setStyle({ color: "orange" })
    )
  if (lmp.co1 !== "")
    layerGroups.lmpLines.addLayer(
      L.polyline([
        [lmp.co1, -720], [lmp.co1, 720]
      ], markerStyles.lmpLine)
        .setStyle({ color: "lime" })
    )
}

function initDataTable(tableId) {
  if (!$.fn.DataTable.isDataTable(tableId)) {
    fetchCurrentUser().then(response => response.json())
      .then(user => {
        const dtbuttons = user.role === 'ROLE_INVITED' ? [] : dtconfig.buttons
        const table = $(tableId)
        // Render floats with precision 3
        const renderNumber = $.fn.dataTable.render.number('', '.', 3)

        table.DataTable({
          autoWidth: false,
          responsive: {
            orthogonal: "responsive",
            details: {
              type: 'column'
            }
          },
          ajax: {
            "url": Routing.generate('co1-sampling-query'),
            "type": "POST",
            "dataSrc": "",
            "data": _ => {
              return $("#main-form").serialize()
            }
          },
          language: dtconfig.language[$("html").attr("lang")],
          dom: "lfrtipB",
          buttons: dtbuttons,
          order: [1, 'asc'],
          columns: [
            dtconfig.expandColumn,
            {
              data: "taxname",
              render: linkify("referentieltaxon_show", { col: 'id' })
            }, {
              data: "nb_sta"
            }, {
              data: "lmp",
              render: renderNumber,
              defaultContent: ""
            }, {
              data: "mle",
              render: renderNumber,
              defaultContent: ""
            }, {
              data: "nb_sta_co1"
            }, {
              data: "lmp_co1",
              render: renderNumber,
              defaultContent: ""
            }, {
              data: "mle_co1",
              render: renderNumber,
              defaultContent: ""
            }, {
              data: "id",
              render: (data, type, row) => Mustache.render($("#details-form-template").html(), row)
            }
          ],
          drawCallback: _ => {
            uiReceivedResponse()
            $('[data-toggle="tooltip"]').tooltip()
            $(".details-form").submit(event => {
              event.preventDefault()
              let formData = new FormData(event.target)
              resetLayers(formData)
              fetchSamplingCoords(formData)
                .then(json => displayModal(json))
              // geoPlotObject.reload(event.target)
            }) // .details-form.submit
          } // drawCallback
        }) // datatables

        /****************************
         * Submit form handler
         ************************** */
        $("#main-form").submit(event => {
          event.preventDefault()
          $(this).find("button[type='submit']").button('loading')
          table.DataTable().ajax.reload()
        })
      })
  }
}

/**
 * Toggle UI loading mode
 */
function uiWaitResponse() {
  $("#main-form").find("button[type='submit']").button('loading')
}

/**
 * Toggle UI loading done
 * @param {Object} response JSON response
 */
function uiReceivedResponse(response) {
  $("#main-form").find("button[type='submit']").button('reset')
}