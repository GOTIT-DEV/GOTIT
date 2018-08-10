const plotlyconfig = {
  geo: {
    layout: {
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
}