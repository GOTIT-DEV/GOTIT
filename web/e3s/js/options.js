/*
 * This file is part of the E3sBundle.
 *
 * Copyright (c) 2018 Philippe Grison <philippe.grison@mnhn.fr>
 *
 * E3sBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * E3sBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with E3sBundle.  If not, see <https://www.gnu.org/licenses/>
 * 
 * Author : Louis Duchemin <ls.duchemin@gmail.com>
 */

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