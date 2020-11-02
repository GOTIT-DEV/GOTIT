/*
 * This file is part of the E3sBundle from the GOTIT project (Gene, Occurence and Taxa in Integrative Taxonomy)
 *
 * Authors : see information concerning authors of GOTIT project in file AUTHORS.md
 *
 * E3sBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * E3sBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with E3sBundle.  If not, see <https://www.gnu.org/licenses/>
 * 
 * Author : Philippe Grison  <philippe.grison@mnhn.fr>
 * 
 */

/**
 * Returns an array of the values ​​of a key of a JSON object.
 * @param {Object} json objet JSON
 * @param {any} key clé à cibler
 */
function unpack(json, key) {
  return json.map(function (row) { return row[key] })
}

/**
 * Display stations located in an area of ​​0.1x0.1 deg around a GPS point
 * @param {Object} json_stations  
 * @param {number} latGPS
 * @param {number} longGPS
 */
function stationsPlot(json_stations, latGPS = undefined, longGPS = undefined) {

  var longmin = (parseFloat(longGPS.replace(",", ".")) - 0.1).toFixed(6);
  var longmax = (parseFloat(longGPS.replace(",", ".")) + 0.1).toFixed(6);
  var latmin = (parseFloat(latGPS.replace(",", ".")) - 0.1).toFixed(6);
  var latmax = (parseFloat(latGPS.replace(",", ".")) + 0.1).toFixed(6);
  var latGPS = parseFloat(latGPS.replace(",", ".")).toFixed(6);
  var longGPS = parseFloat(longGPS.replace(",", ".")).toFixed(6);
  var latArray = [latGPS];
  var longArray = [longGPS];
  //alert(longmin.toString()+'-'+longmax+'-'+latmin+'-'+latmax+'-'+longGPS.toString()+'-'+latGPS.toString());

  function build_station_data(json, update = {}) {
    const latitude = unpack(json, 'station.latDegDec'),
      longitude = unpack(json, 'station.longDegDec'),
      code_station = unpack(json, 'station.codeStation'),
      nom_station = unpack(json, 'station.nomStation'),
      code_commune = unpack(json, 'commune.codeCommune')
    // Initialization of hover text
    var hoverText = []
    for (var i = 0; i < latitude.length; i++) {
      var difLat = parseFloat(latitude[i] - latGPS).toFixed(6);
      var difLong = parseFloat(longitude[i] - longGPS).toFixed(6);
      var stationText = [
        "Code: " + code_station[i],
        "Nom: " + nom_station[i],
        "Coords: " + latitude[i] + "  /  " + longitude[i],
        "Diff Coords: " + difLat + "  /  " + difLong,
        "Commune: " + code_commune[i]
      ].join("<br>")
      hoverText.push(stationText)
    }
    const data = {
      type: 'scattergeo',
      lat: latitude,
      lon: longitude,
      hoverinfo: 'text',
      text: hoverText,
      marker: {
        size: 8,
        line: {
          width: 1,
          color: 'grey'
        }
      },
      name: "Stations",
    }
    // Add data
    $.extend(true, data, update)
    return data
  }

  // Init plotly
  var d3 = Plotly.d3
  $("#station-geo-map").html('')
  var gd3 = d3.select('#station-geo-map')
  var gd = gd3.node()

  // Data
  const data_stations = build_station_data(json_stations, {
    name: "Stations BDD",
    marker: {
      symbol: "circle-open",
      size: 10,
      color: "orange",
      opacity: 0.8,
      line: {
        width: 2,
        color: "green",
      }
    }
  })

  const dataSelectedStation = {
    type: 'scattergeo',
    lat: latArray,
    lon: longArray,
    marker: {
      symbol: "triangle-up",
      size: 5,
      color: "red",
      opacity: 0.3,
      line: {
        width: 2,
        color: "red",
      }
    },
    name: "GPS : lat = " + latGPS + "  /  long = " + longGPS,
  }

  // Objet data : scatterplots
  var data = [
    data_stations,
    dataSelectedStation
  ]

  // Graph display settings
  const layout = $.extend(plotlyconfig.geo.layout, {
    geo: $.extend(plotlyconfig.geo.layout.geo, {
      lonaxis: {
        'range': [longmin, longmax]
      },
      lataxis: {
        'range': [latmin, latmax]
      },
      center: {
        'lon': longGPS,
        'lat': latGPS
      },
    })
  })

  Plotly.newPlot(gd, data, layout, {
    displaylogo: false, // no logo, remove unnecessary control buttons
    modeBarButtonsToRemove: ['sendDataToCloud', 'box', 'lasso2d', 'select2d', 'pan2d']
  })

  Plotly.Plots.resize(gd)

  return gd // Return objet plotly
}



/**
 * Function to plot stations on map
 * @param {Object} json_stations  
 * @param {number} latGPS
 * @param {number} longGPS
 */
function stationsMap(json_stations, latGPS = undefined, longGPS = undefined) {

  var longmin = (parseFloat(longGPS.replace(",", ".")) - 15).toFixed(6);
  var longmax = (parseFloat(longGPS.replace(",", ".")) + 15).toFixed(6);
  var latmin = (parseFloat(latGPS.replace(",", ".")) - 11).toFixed(6);
  var latmax = (parseFloat(latGPS.replace(",", ".")) + 11).toFixed(6);
  var latGPS = parseFloat(latGPS.replace(",", ".")).toFixed(6);
  var longGPS = parseFloat(longGPS.replace(",", ".")).toFixed(6);
  var latArray = [latGPS];
  var longArray = [longGPS];
  //alert(longmin.toString()+'-'+longmax+'-'+latmin+'-'+latmax+'-'+longGPS.toString()+'-'+latGPS.toString());

  function build_station_data(json, update = {}) {
    const latitude = unpack(json, 'station.latDegDec'),
      longitude = unpack(json, 'station.longDegDec')
    const data = {
      type: 'scattergeo',
      lat: latitude,
      lon: longitude,
      hoverinfo: 'none',
      marker: {
        size: 8,
        line: {
          width: 1,
          color: 'grey'
        }
      },
      name: "Stations",
    }
    // Add data 
    $.extend(true, data, update)
    return data
  }

  // Init plotly
  var d3 = Plotly.d3
  $("#station-geo-map").html('')
  var gd3 = d3.select('#station-geo-map')
  var gd = gd3.node()

  // Data
  const data_stations = build_station_data(json_stations, {
    name: "Stations BDD",
    marker: {
      symbol: "triangle-up",
      size: 3,
      color: "orange",
      opacity: 0.8,
      line: {
        width: 1,
        color: "green",
      }
    }
  })

  const dataSelectedStation = {
    type: 'scattergeo',
    lat: latArray,
    lon: longArray,
    marker: {
      symbol: "triangle-up",
      size: 5,
      color: "red",
      opacity: 0.3,
      line: {
        width: 2,
        color: "red",
      }
    },
    name: "GPS : lat = " + latGPS + "  /  long = " + longGPS,
  }

  // Objet data : scatterplots
  var data = [
    data_stations
  ]


  // Objet data complet : scatterplots 

  // graphic display Parameters 
  const layout = $.extend(plotlyconfig.geo.layout, {
    showlegend: false,
    margin: {
      t: 0,
      b: 0,
      l: 0,
      r: 0
    },
    height: 487,
    geo: $.extend(plotlyconfig.geo.layout.geo, {
      lonaxis: {
        'range': [longmin, longmax]
      },
      lataxis: {
        'range': [latmin, latmax]
      },
      center: {
        'lon': longGPS,
        'lat': latGPS
      }
    })
  })

  Plotly.newPlot(gd, data, layout, {
    displaylogo: false,
    modeBarButtonsToRemove: ['sendDataToCloud', 'box', 'lasso2d', 'select2d', 'pan2d'],
    staticPlot: true
  })

  Plotly.Plots.resize(gd)

  return gd // Return objet plotly
}