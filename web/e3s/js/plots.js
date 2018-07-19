/**
 * Renvoie un tableau des valeurs d'une clé d'un objet JSON.
 * @param {Object} json objet JSON
 * @param {any} key clé à cibler
 */
function unpack(json, key) {
  return json.map(function (row) { return row[key] })
}

/**
 * Affiche le diagramme en baton des rearrangements entre méthodes
 * @param {string} target identifiant du contenant
 * @param {Object} json données JSON
 */
function barPlot(target, json) {
  // Extraction des données
  const methode = unpack(json, 'methode'),
    date_motu = unpack(json, 'date_motu'),
    labels = unpack(json, 'label')
  const counters = {
    match: unpack(json, 'match'),
    split: unpack(json, 'split'),
    lump: unpack(json, 'lump'),
    reshuffling: unpack(json, 'reshuffling'),
  }

  // Constitution des données à afficher
  var data = []
  for (var cnt in counters) { // iteration
    if (counters.hasOwnProperty(cnt))
      data.push({
        x: labels, // label de méthode
        y: counters[cnt], // comptages
        name: cnt, // counters.key
        type: 'bar',
      });
  }

  // Construction objet plotly
  var d3 = Plotly.d3;
  $(target).html(''); // vidage du container
  var gd3 = d3.select(target); // assignation du d3 au container
  var gd = gd3.node(); // gd : objet graphique

  // Paramètres d'affichage
  const layout = {
    // Axe des X
    xaxis: {
      title: "Méthode",
      titlefont: {
        family: 'sans serif',
        size: 18,
        color: '#7f7f7f'
      },
      fixedrange: true
    },
    // Axe des Y
    yaxis: {
      title: "Réarrangements",
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

  // Déclaration du plot
  Plotly.newPlot(
    gd, // objet plotly
    data, // données à construire
    layout, // paramètres d'affichage
    {
      scrollZoom: false, // non zoomable
      displaylogo: false, // pas de logo, merci
      modeBarButtonsToRemove: [ // supprimer les boutons de contrôle superflus
        'sendDataToCloud', 'box', 'lasso2d', 'select2d', 'pan2d',
        'zoom2d', 'zoomIn2d', 'zoomOut2d', 'autoScale2d', 'resetScale2d'
      ]
    });

  // Responsive
  Plotly.Plots.resize(gd);
  window.onresize = function () {
    Plotly.Plots.resize(gd);
  }
  return gd;
}




/**
 * Affichage des MOTUs sur la carte geographique
 * 
 * @param {Object} json 
 */
function motuGeoPlot(json) {
  /**
   * Fonction pour extraire les données JSON et construire un objet de données 
   * pour plotly
   * 
   * @param {Object} json données json
   * @param {Object} update données à ajouter
   */
  function build_station_data(json, update = {}) {
    const taxname = unpack(json, 'taxname'),
      code_station = unpack(json, 'code_station'),
      latitude = unpack(json, 'latitude'),
      longitude = unpack(json, 'longitude'),
      altitude = unpack(json, 'altitude'),
      commune = unpack(json, 'commune'),
      pays = unpack(json, 'pays'),
      motus = unpack(json, 'motu')

    var hoverText = []
    for (var i = 0; i < taxname.length; i++) {
      var stationText = [
        "MOTU " + motus[i],
        code_station[i],
        "Coords:" + latitude[i] + ";" + longitude[i],
        "Alt:" + altitude[i] + "m",
        commune[i],
        pays[i]
      ].join("<br>")
      hoverText.push(stationText)
    }

    var data = {
      type: 'scattergeo',
      lat: latitude,
      lon: longitude,
      hoverinfo: 'text',
      text: hoverText,
      marker: {
        size: 9,
        line: {
          width: 1,
        }
      },
      name: "MOTU " + json[0].motu,
    }

    $.extend(true, data, update)
    return data
  }

  // Trie le json par motu : idstation : jsondata
  var motu_map = {}
  for (var i = 0; i < json.length; i++) {
    $.extend(true, motu_map, {
      [json[i].motu]: {
        [json[i].id_sta]: json[i]
      }
    })
  }

  // Création d'une trace par motu : différentes couleurs
  var traces = []
  var i = 0
  for (var motu in motu_map) {
    i += 1
    if (motu_map.hasOwnProperty(motu)) {
      traces.push(build_station_data(
        Object.values(motu_map[motu]), {
          marker: {
            symbol: 5 * Math.floor(i / 10),
            size: 8
          },
          opacity: 0.7
        }))
    }
  }


  var d3 = Plotly.d3
  $("#motu-geo-map").html('')
  var gd3 = d3.select('#motu-geo-map')
  var gd = gd3.node()

  const layout = {
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
    geo: {
      scope: 'world',
      resolution: 50,
      projection: {
        type: 'miller'
      },
      showrivers: true,
      rivercolor: 'lightblue',
      showlakes: true,
      lakecolor: 'lightblue',
      showland: true,
      landcolor: '#eee',
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


  Plotly.newPlot(gd, traces, layout, {
    displaylogo: false,
    modeBarButtonsToRemove: ['sendDataToCloud', 'box', 'lasso2d', 'select2d', 'pan2d']
  })

  // Responsive mode
  Plotly.Plots.resize(gd)
  window.onresize = function () {
    $(".geo-overlay").show()
    Plotly.Plots.resize(gd).then(function () {
      $(".geo-overlay").hide()
    })
  }

  return gd // renvoi objet plotly
}

const plotlyConfig = {
  plotlyDefaultMapLayout: {
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
      resolution: 50,
      projection: {
        type: 'miller'
      },
      showrivers: true,
      rivercolor: '#fff',
      showlakes: true,
      lakecolor: '#fff',
      showland: true,
      landcolor: '#2bc',
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