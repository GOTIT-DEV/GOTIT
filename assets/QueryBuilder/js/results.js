/*
 * This file file is part of the QueryBuilderBundle.
 *
 * It is a free software, included in a bigger project. You can use it and modify it under the terms of the GNU General Public License (Version 3 or later).
 * This software is distributed without any warranty.
 *
 * Authors : Thierno Diallo, Maud Ferrer and Elsa Mendes.
 */

import { dtconfig } from "../../SpeciesSearch/js/datatables_utils"
import { get_form_initial, get_form_block_data } from "./form.js";

/**
 * Init the results of the query in the result-container
 * @param {Object} data containing all the info of the form
 */
export function initResults(data) {
  $("#main-form").submit(event => {
    event.preventDefault();

    // Getting the data
    let data_initial = get_form_initial();
    let data_join_blocks = get_form_block_data(data);

    // Formatting the data
    let jsonData = { initial: data_initial, joins: data_join_blocks};

    // Enabling the "Get SQL" button after the query is successful 
    document.getElementById("getSqlButton").disabled = false;

    // Returning the elements of the query
    $.ajax({
      url: "query",
      type: "POST",
      data: jsonData,
      dataType: "json",
      success: (response) => {
        $("#contentModalQuery").html(response.dql);
        $("#contentModalQuerySql").html(response.sql);
        $("#result-container").html(response.results);
        $("#result-table").DataTable(
          Object.assign({ 
            dom: "lfrtipB",
            responsive: {
              orthogonal: "responsive"
            },
            autoWidth: false
          }, dtconfig)
        )
      }
    })
  })
}


/**
 * Copy the SQL query to the clipboard 
 */
export function copySQLFunction() {
  // Init a new hidden textarea
  let hiddenSQL = document.createElement("textarea"); 
  // Setting the value of the textarea to the SQL Query
  hiddenSQL.value = document.getElementById("contentModalQuerySql").innerHTML; 

  // Making sure we won't see the textarea on the page and making sure we cannot write in it anyway
  hiddenSQL.setAttribute("readonly", "");
  hiddenSQL.style.position = "absolute";
  hiddenSQL.style.left = "-9999px";

  document.querySelector("body").appendChild(hiddenSQL);
  // Selecting the text
  hiddenSQL.select(); 
  // Copying what is in the textarea
  document.execCommand("copy"); 
  document.body.removeChild(hiddenSQL);
   // Alert showing just to check we copied the right text
  alert("COPIED TO CLIPBOARD:\n" + hiddenSQL.value);
}
