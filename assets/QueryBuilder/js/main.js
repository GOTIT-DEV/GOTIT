/*
 * This file file is part of the QueryBuilderBundle.
 *
 * It is a free software, included in a bigger project. 
 * You can use it and modify it under the terms of the GNU General Public License (Version 3 or later).
 * This software is distributed without any warranty.
 *
 * Authors : Thierno Diallo, Maud Ferrer and Elsa Mendes.
 */

import "../../SpeciesSearch/js/species_search"

// Vendor imports
import "ez-plus"

// Internal imports
import "../css/main.less"

import { library } from "@fortawesome/fontawesome-svg-core";
import { fas } from "@fortawesome/free-solid-svg-icons";
library.add(fas);

import Vue from 'vue'
import VueHighlightJS from "vue-highlightjs";
import VueClipboard from "vue-clipboard2";

VueClipboard.config.autoSetContainer = true;
Vue.use(VueHighlightJS);
Vue.use(VueClipboard);

import QueryBuilder from './QueryBuilder'

const vue_form = new Vue({
  el: "#form-container",
  ...QueryBuilder
})

$((_) => {
  // Zoom in the image, scroll in / out to adjust zoom
  $("#logical-db-img").ezPlus({
    scrollZoom: true,
    responsive: true,
    zoomWindowWidth: 420,
    zoomWindowHeight: 345,
    zoomWindowPosition: 0,
    zoomWindowOffsetX: -725,
    zoomWindowOffsetY: 0,
    zoomLevel: 3,
    cursor: "crosshair"
  })

  // // To enable the copy SQL button after the search button is clicked
  // const copyBtn = document.getElementById("copySQL")
  // copyBtn.addEventListener('click', () => {
  //   const selection = window.getSelection()
  //   const range = document.createRange()
  //   const sqlElt = document.querySelector("#contentModalQuerySql")
  //   range.selectNodeContents(sqlElt)
  //   selection.removeAllRanges()
  //   selection.addRange(range)
  //   document.execCommand('copy')
  //   selection.removeAllRanges()

  //   // copyBtn.classList.remove('btn-light');
  //   copyBtn.classList.add("btn-outline-success")
  //   const btnText = copyBtn.querySelector("span")
  //   const original = btnText.textContent
  //   btnText.textContent = "Copied !"
  //   setTimeout(() => {
  //     btnText.textContent = original;
  //     copyBtn.classList.remove('btn-outline-success');
  //     // copyBtn.classList.add("btn-light")
  //   }, 1200);
  // })
});
