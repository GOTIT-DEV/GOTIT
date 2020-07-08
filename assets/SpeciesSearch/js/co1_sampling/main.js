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

import { asCSV } from '../utils.js'
import { initMap } from './map.js'
import { initDataTable } from './results.js'

import Vue from "vue"
import Vuex from "vuex"
import TaxonomySelectPanel from "../components/taxonomy/TaxonomySelectPanel"
import TaxonomyModule from "../components/taxonomy/TaxonomyStore"

Vue.use(Vuex)

const store = new Vuex.Store({
  modules: {
    taxonomy: TaxonomyModule
  }
})

const vue_app = new Vue({
  el: '#species-select',
  template: '<TaxonomySelectPanel with-taxname/>',
  components: { TaxonomySelectPanel },
  store
})

// Init map
let stationMap = initMap('station-geo-map')

$(document).ready(_ => {

  // Init modal map container
  $(".modal-dialog").css("width", "95vw")
  $("#detailsModal").on("shown.bs.modal", _ => {
    stationMap.invalidateSize()
    stationMap.fitBounds(stationMap.bounds, { maxZoom: 10, padding: L.point(30, 30) })
  })

  // Init species selector
  // let speciesSelector = new SpeciesSelector("#main-form", "#taxa-filter")
  // speciesSelector.promise.then(_ => {
  vue_app.$store.state.taxonomy.ready.then(_ => {
    console.log("ready")
    initDataTable("#result-table", onResultsDraw)

  })
  // })
})


/**
 * Requests detailled sampling data for target species
 * @param {FormData} formData Species, MLE, LMP
 */
function fetchSamplingCoords(formData) {
  return fetch(Routing.generate("co1-geocoords"), {
    method: 'POST',
    body: formData,
    credentials: 'include'
  })
    .then(response => { return response.json() })
}

/**
 * Triggers adding markers to map and displays map in modal
 * @param {Object} json Station sampling JSON response
 */
function displayModal(json) {
  let plotParams = stationMap.prepareGeoMarkers(json.stations)
  stationMap.updateMarkers(plotParams.markers)
  stationMap.updateBounds(plotParams.bounds)

  $("#detailsModal").find(".modal-title").html(
    Mustache.render($("template#details-modal-title").html(), {
      taxname: json.taxname
    }))
  $("#detailsModal").modal('show')
}

/**
 * Callback on datatables results drawn
 */
function onResultsDraw() {
  uiReceivedResponse()
  $('[data-toggle="tooltip"]').tooltip()

  $(".details-form").submit(event => {
    event.preventDefault()
    let formData = new FormData(event.target)
    stationMap.resetMarkers(formData)
    fetchSamplingCoords(formData)
      .then(json => displayModal(json))
  }) // .details-form.submit

  $(".download-details").submit(event => {
    event.preventDefault()
    let formData = new FormData(event.target)
    fetchSamplingCoords(formData).then(downloadSamplingDetails)
  })
}

/**
 * Download stations data as CSV for a given taxon
 * @param {Object} json 
 */
function downloadSamplingDetails(json) {
  let fileName = json.taxname + "_sampling.csv"
  let csv = asCSV(json.stations)
  let blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  let link = document.createElement("a");
  if (link.download !== undefined) { // feature detection
    // Browsers that support HTML5 download attribute
    var url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute("download", fileName);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }
}


/**
 * Toggle UI loading done
 * @param {Object} response JSON response
 */
function uiReceivedResponse(response) {
  $("#main-form").find("button[type='submit']").button('reset')
}


