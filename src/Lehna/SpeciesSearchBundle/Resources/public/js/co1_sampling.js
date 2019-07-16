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
$(document).ready(_ => {

  uiWaitResponse()

  let speciesSelector = new SpeciesSelector("#main-form", "#taxa-filter")
  // let geoPlot = new SamplingGeoPlot("#station-geo-map", "#result-table", "#detailsModal")

  // Init species selector
  speciesSelector.promise.then(_ => {
    initDataTable("#result-table")
  })

  // Init map
  let stationMap = L.map('station-geo-map', {
    center: [40, 0],
    zoom: 10,
    worldCopyJump: true,
    wheelPxPerZoomLevel: 100,
    minZoom: 1
  })
  stationMap.createPane("bioMatExtPane")
  stationMap.getPane("bioMatExtPane").style.zIndex = 998

  stationMap.createPane("co1Pane")
  stationMap.getPane("co1Pane").style.zIndex = 999

  // Tile Layer 
  L.esri.basemapLayer("Imagery").addTo(stationMap);

  // Marker layers
  let radius = 5
  let outerStroke = radius*3/5
  let nDashes = 10
  let point = L.circleMarker([40, 0], {
    color: 'black',
    fillColor: 'lime',
    fillOpacity: 1,
    radius: radius - outerStroke/2 - 0.5,
    opacity: 0.75,
    weight: 1,
    pane: "co1Pane"
  })
  let co1Samples = L.layerGroup([point]).addTo(stationMap)

  let circle = L.circleMarker([40, 0], {
    color: "#ff4f09",
    fillOpacity: 0,
    radius: radius
  })
  let bioMat = L.layerGroup([circle]).addTo(stationMap)

  let bmExtMarker = L.circleMarker([40, 0], {
    color: '#00b7ff',
    weight: outerStroke,
    fillOpacity: 0,
    radius: radius,
    dashArray: radiusToDasharray(radius, nDashes),
    lineJoin: 'bevel',
    lineCap: "butt",
    pane: "bioMatExtPane"
  })
  let bioMatExt = L.layerGroup([bmExtMarker]).addTo(stationMap)
  let lmpLine = L.polyline([
    [40, -18000], [40, 18000]
  ], { color: 'lime', weight: 2, dashArray: '4,10' }
  ).addTo(stationMap)

  let bioMatExtLegend = $("template#dashed-circle-svg").html()
  let overlayMarks = {
    '<i class="fa fa-circle"></i> <span>CO1 sample</span>': co1Samples,
    '<i class="fa fa-circle-o"></i> <span>Biological Material (Int.)</span>': bioMat,
    ['<span style="vertical-align:middle">' + bioMatExtLegend + ' Biological Material (Ext.)</span>']: bioMatExt,
    '<span>LMP (CO1)</span>': lmpLine
  }
  L.control.layers(null, overlayMarks, { collapsed: true }).addTo(stationMap)

  $("#detailsModal").on("shown.bs.modal", ev => {
    stationMap.invalidateSize()
  })

  $("#marker-radius-slider").slider()
    .on("slide", event => {
      let radius = event.value,
        stroke = radius * 3 / 5
      bioMat.invoke("setRadius", radius)
      bioMatExt.invoke("setRadius", radius)
      co1Samples.invoke("setRadius", radius - stroke/2 - 0.5)
      bioMat.invoke("setStyle", { weight: stroke })
      bioMatExt.invoke("setStyle", {
        weight: stroke,
        dashArray: radiusToDasharray(radius, nDashes)
      })
    })
    $("#marker-opacity-slider").slider()
      .on("slide", event => {
        let opacity = event.value
        bioMat.invoke("setStyle", { opacity: opacity })
        bioMatExt.invoke("setStyle", {opacity: opacity})
        co1Samples.invoke("setStyle", {fillOpacity: opacity})
        lmpLine.setStyle({ opacity: opacity })
      })
})

/* **************************
 *  Initialize datatable
 **************************** */

function radiusToDasharray(radius, n){
  let length = 2*Math.PI*radius / n
  return `${length},${length}`
}

function fetchSamplingCoords(rowForm) {
  return fetch(Routing.generate("co1-geocoords"), {
    method: 'POST',
    body: new FormData(rowForm),
    credentials: 'include'
    // headers: new Headers({ 'Content-Type': 'application/json' })
  })
    .then(response => { return response.json() })
}

function displayModal(json) {

  $("#detailsModal").find(".modal-title").html(
    Mustache.render($("template#details-modal-title").html(), {
      taxname: json.taxname
    }))
  $(".geo-overlay").show()
  $("#detailsModal").modal('show')

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
              fetchSamplingCoords(event.target)
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