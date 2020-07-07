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
import { SpeciesSelector } from '../form_elements/species_select.js'
import { MethodSelector } from '../form_elements/method_select.js'
import { initDataTable } from './results.js'


/* **************************
 *  Document ready
 **************************** */
$(document).ready(function () {

  // Init form elements
  let speciesSelector = new SpeciesSelector("#main-form")
  let methodSelector = new MethodSelector("#main-form")

  // Radio button Reference event
  $('input[name="reference"]')
    .click(toggleFormSelect)
    .filter(':checked')
    .trigger('click')

  $("#target-dataset").on('loaded.bs.select', event => {
    $(event.target).parent().tooltip({
      title: $(event.target).data('originalTitle'),
      placement: 'auto top'
    })
  })

  // Sync target and reference dataset <select> inputs
  $("#main-form select#dataset").change(event => {
    $("#target-dataset").val(event.target.value).selectpicker('refresh')
  }).trigger('change')


  // Init datatable when form ready
  Promise.all([speciesSelector.promise, methodSelector.promise])
    .then(_ => {
      initDataTable("#result-table-recto")
      initDataTable("#result-table-verso")
    })
})

/**
 * Toggles form parts relative to current reference
 * @param {Object} event Reference radio button click event
 */
function toggleFormSelect(event) {
  $(event.target).tooltip('hide')
  switch (event.target.value) {
    // Morpho
    case "0":
      $(".method-select").prop('disabled', true)
      $(".taxa-select").prop('disabled', true)
      $("#target-dataset").prop('disabled', false)
      break
    // Taxon
    case "1":
      $(".method-select").prop('disabled', true)
      $(".taxa-select").prop('disabled', false)
      $("#target-dataset").prop('disabled', false)
      break
    // Molecular 
    case "2":
      $(".method-select").prop('disabled', false)
      $(".taxa-select").prop('disabled', true)
      $("#target-dataset")
        .prop('disabled', true)
        .val(
          $("select#dataset").val()
        )
      break
  }
  $(".selectpicker").selectpicker('refresh')
}