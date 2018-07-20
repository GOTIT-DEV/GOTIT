/**
 * Renvoie un tableau des valeurs d'une clé d'un objet JSON.
 * @param {Object} json objet JSON
 * @param {any} key clé à cibler
 */
function unpack(json, key) {
  return json.map(function (row) { return row[key] })
}

class BasePlot {
  constructor(containerId) {
    // Construction objet plotly
    this.container = $(containerId)
    this.container.html('')
    //this.container.html('') // vidage du container
    this.d3 = Plotly.d3
    
    this.gd3 = this.d3.select(containerId) // assignation du d3 au container
    this.gd = this.gd3.node(); // gd : objet graphique

    this.plot = this.plot.bind(this)
    this.resize = this.resize.bind(this)

    $(window).resize(this.resize)
  }

  resize() {
    let self = this
    Plotly.Plots.resize(self.gd)
  }

  plot() { }
}

class BarPlot extends BasePlot {
  constructor(containerId) {
    super(containerId)
    this.layout = {
      // Axe des X
      xaxis: {
        title: this.container.data('vocabXlabel'),
        titlefont: {
          family: 'sans serif',
          size: 18,
          color: '#7f7f7f'
        },
        fixedrange: true
      },
      // Axe des Y
      yaxis: {
        title: this.container.data('vocabYlabel'),
        titlefont: {
          family: 'sans serif',
          size: 18,
          color: '#7f7f7f'
        },
        fixedrange: true
      },
      // Affichage du texte
      font: {
        family: 'sans serif',
        size: 14
      },
      showlegend: true,
      barmode: 'group', // affichage des barres côte à côte
    }
  }

  plot(json) {

    let data = {
      match: [],
      split: [],
      lump: [],
      reshuffling: [],
      label: []
    }
    json.reduce((currentData, row) => {
      currentData.match.push(row.match)
      currentData.split.push(row.split)
      currentData.lump.push(row.lump)
      currentData.reshuffling.push(row.reshuffling)
      currentData.label.push(row.label)
      return currentData
    }, data)

    let traces = []
    // Constitution des données à afficher
    for (var key in data) { // iteration
      if (data.hasOwnProperty(key) && key != 'label')
        traces.push({
          x: data.label, // label de méthode
          y: data[key], // comptages
          name: key, // counters.key
          type: 'bar',
        });
    }

    // Déclaration du plot
    Plotly.newPlot(
      this.gd, // objet plotly
      traces, // données à construire
      this.layout, // paramètres d'affichage
      {
        scrollZoom: false, // non zoomable
        displaylogo: false, // pas de logo, merci
        modeBarButtonsToRemove: [ // supprimer les boutons de contrôle superflus
          'sendDataToCloud', 'box', 'lasso2d', 'select2d', 'pan2d',
          'zoom2d', 'zoomIn2d', 'zoomOut2d', 'autoScale2d', 'resetScale2d'
        ]
      })
    this.resize()
  }
}

class BaseGeoPlot extends BasePlot {
  constructor(containerId) {
    super(containerId)
    this.layout = {
      font: {
        family: 'Droid Serif, serif',
        size: 14
      },
      titlefont: {
        size: 16
      },
      height: 600,
      margin: {
        l: 0,
        r: 0,
        t: 15,
        b: 0
      },
      showlegend: true,
      geo: { // carte geographique
        scope: 'world',
        resolution: 30,
        projection: {
          type: 'miller'
        },
        showrivers: true,
        rivercolor: 'lightblue',
        showlakes: true,
        lakecolor: 'lightblue',
        showland: true,
        landcolor: '#E0F8F7',
        countrycolor: 'grey',
        countrywidth: 1,
        subunitcolor: '#d3d3d3',
        showocean: true,
        oceancolor: 'lightblue',
        showframe: true,
        framecolor: '#000',
        framewidth: 2,
        bgcolor: 'lightgrey'
      }
    }
  }

  resize() {
    let self = this
    self.container.css("visibility", "hidden")
    $(".geo-overlay").show()
    Plotly.Plots
      .resize(self.gd)
      .then(function () {
        self.container.css("visibility", "visible")
        $(".geo-overlay").hide()
      }
      )
  }
}

class MotuGeoPlot extends BaseGeoPlot {
  constructor(containerId) {
    super(containerId)
  }

  plot(json) {
    let self = this
    let motu_map = {}
    json.reduce((currentData, row) => {
      currentData[row.motu] = {
        [row.id_sta]: row
      }
      return currentData
    }, motu_map)

    let traces = []
    let i = 0
    for (var motu in motu_map) {
      i += 1
      if (motu_map.hasOwnProperty(motu)) {
        traces.push(self.build_station_data(
          Object.values(motu_map[motu]), {
            marker: {
              symbol: 5 * Math.floor(i / 10),
              size: 8
            },
            opacity: 0.7
          }))
      }
    }

    Plotly.newPlot(this.gd, traces, this.layout, {
      displaylogo: false,
      modeBarButtonsToRemove: ['sendDataToCloud', 'box', 'lasso2d', 'select2d', 'pan2d']
    })
    this.resize()
  }



  build_station_data(json, update = {}) {
    let coords = {
      latitude: [],
      longitude: [],
      hover: []
    }
    json.reduce((currentCoords, row) => {
      currentCoords.latitude.push(row['latitude']),
        currentCoords.longitude.push(row['longitude']),
        currentCoords.hover.push([
          "MOTU " + row["motu"],
          row['code_station'],
          "Coords:" + row['latitude'] + ";" + row['longitude'],
          "Alt:" + row['altitude'] + "m",
          row['commune'],
          row['pays']
        ].join("<br>"))
      return currentCoords
    }, coords)

    let data = {
      type: 'scattergeo',
      lat: coords.latitude,
      lon: coords.longitude,
      hoverinfo: 'text',
      text: coords.hover,
      // Doivent être remplacé par l'argument update
      marker: {
        size: 9,
        line: {
          width: 1
        }
      },
      name: "MOTU " + json[0].motu,
    }

    // changement options par l'argument update
    $.extend(true, data, update)
    return data
  }
}




class SamplingGeoPlot extends BaseGeoPlot {
  constructor(containerId, tableId, modalId, queryUrl = null) {
    super(containerId)
    this.table = $(tableId)
    this.modal = $(modalId)
    this.lmp = {
      co1: 0,
      lotmateriel: 0
    }
    this.formData = undefined
    this.url = queryUrl ? queryUrl : this.container.data('url')

    this.modal.on('shown.bs.modal', this.resize)

  }

  ajaxOptions() {
    let self = this
    return {
      type: 'POST',
      data: self.formData,
      url: self.url,
      success: self.plot
    }
  }

  reload(detailsForm) {
    this.lmp.lotmateriel = $(detailsForm).find("input[name='lmp_lm']").val()
    this.lmp.co1 = $(detailsForm).find("input[name='lmp_co1']").val()
    this.formData = $(detailsForm).serialize()
    $.ajax(this.ajaxOptions()) // ajax
  }


  plot(response) {
    let self = this
    let data = []
    data.push( // Données COI
      self.build_station_data(response.with_co1, {
        name: self.container.data('vocabStationCo1'),
        marker: {
          symbol: "triangle-up",
          color: "red"
        }
      })
    )
    // Données non COI
    let lotsMat = {
      interne: [],
      externe: []
    } 
    lotsMat = response.no_co1.reduce( (current, row ) => {
      if (row.lm_id != null){
        current.interne.push(row)
      } else {
        current.externe.push(row)
      }
      return current
    }, lotsMat)

    data.push(
      self.build_station_data(lotsMat.interne, {
        name: self.container.data('vocabStationLotmaterielInt'),
        marker: {
          symbol: "circle-open",
          size: 10,
          color: "orange",
          opacity: 1,
          line: {
            width: 1.5,
          }
        }
      })
    )
    data.push(
      self.build_station_data(lotsMat.externe, {
        name: self.container.data('vocabStationLotmaterielExt'),
        marker: {
          symbol: "circle-open",
          size: 8,
          color: "limegreen",
          opacity: 1,
          line: {
            width: 1.8,
          }
        }
      })
    )

    // Coordonnées de la ligne LMP
    if (self.lmp.lotmateriel) {
      data.push({
        type: 'scattergeo',
        lon: Array.from(new Array(360), (_, i) => -180 + i),
        lat: Array(360).fill(self.lmp.lotmateriel),
        hoverinfo: "none",
        mode: 'lines',
        line: {
          width: 1.5,
          color: 'orange',
          dash: 'dash'
        },
        name: "LMP"
      })
    }

    if (self.lmp.co1) {
      data.push({
        type: 'scattergeo',
        lon: Array.from(new Array(360), (_, i) => -180 + i),
        lat: Array(360).fill(self.lmp.co1),
        hoverinfo: "none",
        mode: 'lines',
        line: {
          width: 1.5,
          color: 'red',
          dash: 'dash'
        },
        name: "LMP (COI)"
      })
    }
    // Objet data complet : scatterplots + LMP + LMP COI
    Plotly.newPlot(self.gd, data, self.layout, {
      displaylogo: false, // pas de logo, enlever boutons de controle inutiles
      modeBarButtonsToRemove: ['sendDataToCloud', 'box', 'lasso2d', 'select2d', 'pan2d']
    })


    self.modal.find(".modal-title").html(
      Mustache.render($("template#details-modal-title").html(), {
        taxname: response.taxname
      }))
    $(".geo-overlay").show()
    self.modal.modal('show')
  }

  build_station_data(json, update = {}) {
    let coords = {
      latitude: [],
      longitude: [],
      hover: []
    }
    json.reduce((currentCoords, row) => {
      currentCoords.latitude.push(row['latitude']),
        currentCoords.longitude.push(row['longitude']),
        currentCoords.hover.push([
          row['code_station'],
          "Coords:" + row['latitude'] + ";" + row['longitude'],
          "Alt:" + row['altitude'] + "m",
          row['commune'],
          row['pays']
        ].join("<br>"))
      return currentCoords
    }, coords)

    let data = {
      type: 'scattergeo',
      lat: coords.latitude,
      lon: coords.longitude,
      hoverinfo: 'text',
      text: coords.hover,
      // Doivent être remplacé par l'argument update
      marker: {
        size: 8,
        line: {
          width: 1,
          color: 'grey'
        }
      },
      name: "Stations",
    }

    // changement options par l'argument update
    $.extend(true, data, update)
    return data
  }
}
