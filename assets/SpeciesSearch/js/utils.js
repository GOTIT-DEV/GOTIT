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
 * Fetch current user informations
 */
function fetchCurrentUser() {
  return fetch(Routing.generate("user_current"), { method: "GET", credentials: 'include' })
}

/**
 * Convenience fonction for jquery auto scrolling 
 * @param {string} elt_id target element selector
 * @param {int} time animation time in ms
 */
function scrollToElement(elt_id, time = 1000) {
  $('html, body').animate({
    scrollTop: $(elt_id).offset().top
  }, time);
}

/**
 * Export array of objects as CSV-like string
 * @param {Array} array array of objects having same structure
 */
function asCSV(array) {
  // Use first element to choose the keys and the order
  var keys = Object.keys(array[0]);

  // Build header
  var result = keys.join(",") + "\n";

  // Add the rows
  array.forEach(function (obj) {
    keys.forEach(function (k, ix) {
      if (ix) result += ",";
      if (obj[k] !== null) result += obj[k];
    });
    result += "\n";
  });

  return result;
}


export { fetchCurrentUser, asCSV, scrollToElement }