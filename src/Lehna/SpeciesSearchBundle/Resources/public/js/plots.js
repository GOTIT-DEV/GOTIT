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


/**
 * Extract values of target key from an array of JSON objects
 * @param {Object} json array of JSON objects
 * @param {any} key target key
 */
function unpack(json, key) {
  return json.map(row => row[key])
}

/**
 * This is the base plotting class used to plot maps and graphics in GOTIT
 * Requires plotly.js 
 */
class BasePlot {
  constructor(containerId) {
    this.container = $(containerId)
    this.container.html('')
    this.d3 = Plotly.d3

    this.gd3 = this.d3.select(containerId) // assign graph to container
    this.gd = this.gd3.node(); // gd : graphical object

    this.plot = this.plot.bind(this)
    this.resize = this.resize.bind(this)

    $(window).resize(this.resize)
  }

  resize() {
    if (this.container.hasClass('collapse in') ||
      !this.container.hasClass('collapse'))
      Plotly.Plots.resize(this.gd)
  }

  plot() { }
}

/**
 * Bar plot class for Species Hypotheses query 
 */
class BarPlot extends BasePlot {
  constructor(containerId) {
    super(containerId)
    this.layout = {
      // X axis
      xaxis: {
        title: Translator.trans("methode.label", {}, 'queries'),
        titlefont: {
          family: 'sans serif',
          size: 18,
          color: '#7f7f7f'
        },
        fixedrange: true
      },
      // Y axis
      yaxis: {
        title: Translator.trans('queries.specieshypotheses.short', {}, 'queries'),
        titlefont: {
          family: 'sans serif',
          size: 18,
          color: '#7f7f7f'
        },
        fixedrange: true
      },
      // Text layout
      font: {
        family: 'sans serif',
        size: 14
      },
      showlegend: true,
      // Grouped bar chart
      barmode: 'group',
    }
  }

  refresh(json) {
    if (json.length) {
      if (this.container.hasClass('collapse in')) {
        this.plot(json)
      } else {
        this.container.on('shown.bs.collapse', event => {
          this.plot(json)
        })
        this.show()
      }
    } else {
      this.hide()
    }
  }

  show() {
    $(".plot-overlay").collapse('hide')
    this.container.collapse('show')
  }

  hide() {
    $(".plot-overlay").collapse('show')
    this.container.collapse('hide')
  }

  /** Plots JSON response as bar plot */
  plot(json) {
    let data = {
      match: [],
      split: [],
      lump: [],
      reshuffling: [],
      methode: []
    }
    json.reduce((currentData, row) => {
      currentData.match.push(row.match)
      currentData.split.push(row.split)
      currentData.lump.push(row.lump)
      currentData.reshuffling.push(row.reshuffling)
      currentData.methode.push(row.methode)
      return currentData
    }, data)

    let traces = Object.keys(data)
      .filter(key => {
        return key != 'methode'
      })
      .map(key => {
        return {
          x: data.methode, // method label
          y: data[key], // counts
          name: key, // counters.key
          type: 'bar',
        }
      })

    // Build plot
    Plotly.newPlot(
      this.gd, // plotly object
      traces, // data
      this.layout, // layout options
      {
        scrollZoom: false,
        displaylogo: false,
        modeBarButtonsToRemove: [ // remove unwanted control buttons 
          'sendDataToCloud', 'box', 'lasso2d', 'select2d', 'pan2d',
          'zoom2d', 'zoomIn2d', 'zoomOut2d', 'autoScale2d', 'resetScale2d'
        ]
      })
    this.resize()
  }
}

export { BarPlot }