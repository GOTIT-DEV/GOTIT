function unpack(rows, key) {
  return rows.map(function(row) { return row[key]; });
}


function barPlot(target, rows) {
  var methode = unpack(rows, 'methode'),
    date_motu = unpack(rows, 'date_motu'),
    labels = unpack(rows, 'label');
  var counters = {
    match: unpack(rows, 'match'),
    split: unpack(rows, 'split'),
    lump: unpack(rows, 'lump'),
    reshuffling: unpack(rows, 'reshuffling')
  }

  var data = [];
  for (var cnt in counters) {
    if (counters.hasOwnProperty(cnt))
      data.push({
        x: labels,
        y: counters[cnt],
        name: cnt,
        type: 'bar'
      });
  }

  var d3 = Plotly.d3;
  $(target).html('');
  var gd3 = d3.select(target);
  var gd = gd3.node();

  var layout = {
    xaxis: {
      title: "Méthode",
      titlefont: {
        family: 'sans serif',
        size: 18,
        color: '#7f7f7f'
      },
      fixedrange: true
    },
    yaxis: {
      title: "Réarrangements",
      titlefont: {
        family: 'sans serif',
        size: 18,
        color: '#7f7f7f'
      },
      fixedrange: true
    },
    font: {
      family: 'sans serif',
      size: 14
    },
    showlegend: true,
    barmode: 'group',
  };


  Plotly.newPlot(gd, data, layout, {
    scrollZoom: false,
    displaylogo: false,
    modeBarButtonsToRemove: [
      'sendDataToCloud', 'box', 'lasso2d', 'select2d', 'pan2d',
      'zoom2d', 'zoomIn2d', 'zoomOut2d', 'autoScale2d', 'resetScale2d'
    ]
  });

  Plotly.Plots.resize(gd);
  window.onresize = function() {
    Plotly.Plots.resize(gd);
  }
  return gd;
}



function geoPlot(rows_no_co1, rows_co1, lmp = false, lmp_co1 = false) {

  function build_station_data(rows, update = {}) {
    var taxon = unpack(rows, 'taxon_id'),
      taxname = unpack(rows, 'taxname'),
      code_station = unpack(rows, 'code_station'),
      latitude = unpack(rows, 'latitude'),
      longitude = unpack(rows, 'longitude'),
      altitude = unpack(rows, 'altitude'),
      commune = unpack(rows, 'commune'),
      pays = unpack(rows, 'pays');

    var hoverText = [],
      colors = [];
    for (var i = 0; i < taxname.length; i++) {
      var stationText = [
        code_station[i],
        "Coords:" + latitude[i] + ";" + longitude[i],
        "Alt:" + altitude[i] + "m",
        commune[i],
        pays[i]
      ].join("<br>");
      hoverText.push(stationText);
      //colors.push(color);
    }

    var data = {
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

    $.extend(true, data, update);
    return data;
  }
  var d3 = Plotly.d3;

  var WIDTH_IN_PERCENT_OF_PARENT = "100",
    HEIGHT_IN_PERCENT_OF_PARENT = "100";
  $("#station-geo-map").html('');
  var gd3 = d3.select('#station-geo-map');
  var gd = gd3.node();

  var data_co1 = build_station_data(rows_co1, {
    name: "Stations COI",
    marker: {
      symbol: "triangle-up",
      color: "red"
    }
  });

  var data_no_co1 = build_station_data(rows_no_co1, {
    name: "Stations Lot Mat.",
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
  });

  var data = [
    data_co1,
    data_no_co1
  ]
  if (lmp) {
    data.push({
      type: 'scattergeo',
      lon: Array.from(new Array(360), (_, i) => -180 + i),
      lat: Array(360).fill(lmp),
      hoverinfo: "none",
      mode: 'lines',
      line: {
        width: 1.5,
        color: 'orange',
        dash: 'dash'
      },
      name: "Lat. Mid-Point (LM)"
    });
  }

  if (lmp_co1) {
    data.push({
      type: 'scattergeo',
      lon: Array.from(new Array(360), (_, i) => -180 + i),
      lat: Array(360).fill(lmp_co1),
      hoverinfo: "none",
      mode: 'lines',
      line: {
        width: 1.5,
        color: 'red',
        dash: 'dash'
      },
      name: "Lat. Mid-Point (COI)"
    });
  }

  var layout = {
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
  };


  Plotly.newPlot(gd, data, layout, {
    displaylogo: false,
    modeBarButtonsToRemove: ['sendDataToCloud', 'box', 'lasso2d', 'select2d', 'pan2d']
  });

  Plotly.Plots.resize(gd);
  return gd;
}


function motuGeoPlot(rows) {

  function unpack(rows, key) {
    return rows.map(function(row) { return row[key]; });
  }

  function build_station_data(rows, update = {}) {
    var taxname = unpack(rows, 'taxname'),
      code_station = unpack(rows, 'code_station'),
      latitude = unpack(rows, 'latitude'),
      longitude = unpack(rows, 'longitude'),
      altitude = unpack(rows, 'altitude'),
      commune = unpack(rows, 'commune'),
      pays = unpack(rows, 'pays'),
      motus = unpack(rows, 'motu');

    var hoverText = [],
      colors = [];
    for (var i = 0; i < taxname.length; i++) {
      var stationText = [
        "MOTU " + motus[i],
        code_station[i],
        "Coords:" + latitude[i] + ";" + longitude[i],
        "Alt:" + altitude[i] + "m",
        commune[i],
        pays[i]
      ].join("<br>");
      hoverText.push(stationText);
      //colors.push(color);
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
      name: "MOTU " + rows[0].motu,
    }

    $.extend(true, data, update);
    return data;
  }


  var motu_map = {};

  for (var i = 0; i < rows.length; i++) {
    $.extend(true, motu_map, {
      [rows[i].motu]: {
        [rows[i].id_sta]: rows[i]
      }
    })
  }

  var traces = [];
  var i = 0;
  for (var motu in motu_map) {
    i += 1;
    if (motu_map.hasOwnProperty(motu)) {
      traces.push(build_station_data(
        Object.values(motu_map[motu]), {
          marker: {
            symbol: 5 * Math.floor(i / 10),
            size: 8
          },
          opacity: 0.7
        }));
    }
  }

  var d3 = Plotly.d3;

  var WIDTH_IN_PERCENT_OF_PARENT = "100",
    HEIGHT_IN_PERCENT_OF_PARENT = "100";


  $("#motu-geo-map").html('');
  var gd3 = d3.select('#motu-geo-map');
  var gd = gd3.node();

  var layout = {
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
  };


  Plotly.newPlot(gd, traces, layout, {
    displaylogo: false,
    modeBarButtonsToRemove: ['sendDataToCloud', 'box', 'lasso2d', 'select2d', 'pan2d']
  });

  Plotly.Plots.resize(gd);
  window.onresize = function() {
    $(".geo-overlay").show();
    Plotly.Plots.resize(gd).then(function() {
      $(".geo-overlay").hide();
    });
  };
  return gd;
}