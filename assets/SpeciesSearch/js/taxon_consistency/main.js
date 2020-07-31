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

import { initDataTable } from './results.js'

/* **************************
 *  Document ready
 **************************** */
$(document).ready(function () {
  $("select.concordance")
    .change(updateChoiceColor)
    .trigger('change')
  initDataTable("#result-table")
})

/**
 * Change <select> inputs background color to show query constraints
 * 
 * @param {Object} event jquery event object
 */
function updateChoiceColor(event) {
  const target = $(event.target)
  target.removeClass("typeA typeB typeC unassigned no-constraints")
  switch (target.val()) {
    case "A":
      target.selectpicker('setStyle', 'btn-info btn-success btn-danger', 'remove')
        .selectpicker('setStyle', 'btn-info')
      break
    case "B":
      target.selectpicker('setStyle', 'btn-info btn-success btn-danger', 'remove')
        .selectpicker('setStyle', 'btn-success')
      break
    case "C":
      target.selectpicker('setStyle', 'btn-info btn-success btn-danger', 'remove')
        .selectpicker('setStyle', 'btn-danger')
      break
    case "0":
      target.selectpicker('setStyle', 'btn-info btn-success btn-danger', 'remove')
        .selectpicker('setStyle', 'btn-light border')
      break
    case "1":
      target.selectpicker('setStyle', 'btn-info btn-success btn-danger', 'remove')
        .selectpicker('setStyle', 'btn-light border')
      break
  }

}
