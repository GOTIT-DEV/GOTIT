/*
 * This file file is part of the QueryBuilderBundle.
 *
 * It is a free software, included in a bigger project. You can use it and modify it under the terms of the GNU General Public License (Version 3 or later).
 * This software is distributed without any warranty.
 *
 * Authors : Thierno Diallo, Maud Ferrer and Elsa Mendes.
 */

import Mustache from "mustache"
import "jQuery-QueryBuilder"
import "./plugins.js";


let table_count = {}; // Object to keep track of the number of times is used to generate aliases dynamically

/**
 * Initializing the dropdown containing all tables
 * @param {Oject} init_data
 */
export function initFirstTable(init_data) {
  // Making sure we have a list of tables sorted by alphabetical order
  let sorted_table_list = Object.keys(init_data).sort();
  let initial_options = sorted_table_list.map((table_name) =>
    $("<option></option>").attr("value", table_name).text(table_name)
  );

  // Adding every single table to the dropdown
  $("#initial-table")
    .append(...initial_options)
    // Redbuilding the dropdown with the new info in it
    .selectpicker("refresh")
    // Init tooltip for the dropdown
    .parent()
    .tooltip({ title: "Select the Initial Table" });
}

/**
 * Initializing the first query builder
 */
export function initFirstQueryBuilder() {
  // Init the query builder for the initial block

  $("#initial-query-builder").queryBuilder({
    plugins: [
      // "bt-tooltip-errors",
      "bt-selectpicker", "date-inputmask"],
    filters: [
      {
        id: "empty",
        label: "empty",
        type: "integer",
      },
    ],
    lang: {
      delete_rule: " ",
      delete_group: " ",
    },
  });

  // Init the reset button for this query builder
  $("#initial-cc-reset").click((_) => {
    $("#initial-query-builder").queryBuilder("reset");
  });
}

/**
 * Initializing the dropdown containing the first fields and the first query builder according to the choice of initial table
 * @param {Object} init_data
 */
export function initFirstFields(init_data) {
  // Initialize selectpicker
  $("#initial-fields").selectpicker({
    actionsBox: true,
    selectedTextFormat: "count > 4",
    title: "None selected",
    width: "75%",
  });


  // What occurs when you choose a table and/or change it
  document.getElementById("initial-table").onchange = function (event) {
    let target_table = event.target.value;
    let table_data = init_data[target_table]; // Getting the fields of the chosen table from the init file

    // Init query-builder with fields and filters
    $("#initial-query-builder").queryBuilder(
      "setFilters",
      true,
      table_data.filters
    );

    // Init list of fields
    let items = table_data.filters.map((item) =>
      $("<option></option>")
        .attr("value", item.label)
        .attr("selected", true)
        .text(item.label)
    );

    // Init the dropdown containing the initial fields related to the chosen table
    $("#initial-fields")
      .empty()
      .append(...items)
      .selectpicker("refresh")
      .parent() // Init the tooltip for the initial table dropdown on the parent element
      .tooltip({ title: "Select the Fields (all selected by default)" });

    // Creating an alias for the initial table, keeping track of it with updated table_count
    let first_table = document.getElementById("initial-table");
    let init_table = first_table.options[first_table.selectedIndex].value;
    let initial_alias = "";

    if (Object.keys(table_count).length > 0) {
      // If we already have a chosen table in the object
      table_count = {}; // We re-init table_count and the alias
      initial_alias = "";
      table_count[init_table] = 1; // We update table_count with the new currently chosen table, and we create the alias
      initial_alias = init_table + "_" + 1;
    } else {
      // If table_count is empty, we directly fill it and create the alias
      table_count[init_table] = 1;
      initial_alias = init_table + "_" + 1;
    }
    document.getElementById("init-alias").value = initial_alias; // We set the value of the input text to the alias
    document.getElementById("init-alias").setAttribute("data-table", init_table);

    // Enables the plus button to add a join block and the submit button when the first table is chosen (Disabled by default)
    document.getElementById("add-join").disabled = false;
    document.getElementById("submit-button").disabled = false;
  };
}

/**
 * Function called when the "plus" button is clicked (using Mustache.js)
 * @param {Number} block_id containing the current join block id
 * @return {String}
 */
function addJoin(block_id) {
  // Making template's block with mustache.js
  let newBlock = Mustache.render($("#form-block-template").html(), {
    id: block_id,
  });

  // Pushing the new block to the div containing all the blocks
  $("#add-constraints").append(newBlock);

  // Creating the new block
  newBlock = $("#form-block-" + block_id);

  // Query builder initialization for join blocks
  newBlock.find(".collapsed-query-builder").queryBuilder({
    plugins: [
      // "bt-tooltip-errors",
      "bt-selectpicker", "date-inputmask"],
    filters: [
      {
        id: "empty",
        label: "empty",
        type: "integer",
      },
    ],
    lang: {
      delete_rule: " ",
      delete_group: " ",
    },
  });

  // Reset button
  newBlock.find("#join-cc-reset").click((_) => {
    let target = newBlock.find("#join-cc-reset").data("target");
    $(target).queryBuilder("reset");
  });

  // Init switchbox buttons
  $("[data-toggle='toggle']").bootstrapToggle("destroy");
  $("[data-toggle='toggle']").bootstrapToggle();
  $("[data-toggle='tooltip']").tooltip();

  // Targeting the collapsed block of query builder
  newBlock.find("#join-constraints-switchbox").change(() => {
    newBlock.find(".toggled-constraints").slideToggle("fast");
    newBlock.find("#join-cc-reset").slideToggle("fast");
  });

  // Making sure those are disabled on reload
  document.getElementById("add-join").disabled = true;
  document.getElementById("submit-button").disabled = true;

  return newBlock;
}

/**
 * Previously selected tables
 * available when choosing a source table to make joins
 * @return {Set}
 */
function getAvailableTables() {
  let initialAliasElt = $("#init-alias")
  let initial = {
    alias: initialAliasElt.val(),
    table: initialAliasElt.data('table')
  }

  // Creating an Array with all the previously chosen tables (using the aliases since we can choose one table multiple times)
  let available_tables = $("input.alias")
    .get()
    .map((elt) => {
      return {
        alias: elt.value,
        table: $(elt).data("table")
      }
    })
    .concat([initial]) // We add the initial table
  let uniqueAliases = new Set(available_tables.map(t => t.alias))

  return available_tables
    .filter((t) => {
      if (uniqueAliases.has(t.alias)) {
        uniqueAliases.delete(t.alias)
        return t.table !== "" && t.table !== undefined
      } else {
        return false
      }
    })
    .sort((a, b) => a.table - b.table)

}

let new_block_id = 0;
/**
 * Init a join block each time you click on the plus button.
 * @param {Array} joinType containing all the joins possible
 * @param {Object} init_data containing all the data in the form
 */
export function initJoinBlock(joinType, init_data) {

  // After each time the user clicks on the add join button
  document.getElementById("add-join").onclick = function () {
    // Adding 1 at each click
    new_block_id += 1;

    // Adding a block of query
    let newBlock = addJoin(new_block_id);

    // Init the JOIN Type dropdown
    newBlock.find("#join-type").empty().prop("selectedIndex", 0);
    $.each(joinType, (_index, value) => {
      newBlock
        .find("#join-type")
        .append($("<option></option>").attr("value", value).text(value));
    });

    // Making sure the dropdown is initialized correctly
    newBlock
      .find("#join-type")
      .selectpicker("refresh")
      .parent()
      .tooltip({ title: "Choose a JOIN Type" });

    // Init the dropdown when the add-join button is clicked
    newBlock
      .find("select.table-selects")
      .selectpicker({
        actionsBox: true,
        selectedTextFormat: "count > 4",
        title: "None selected",
        width: "100%",
      })
      // Init tooltip
      .parent()
      .tooltip({ title: "Select the Fields (none selected by default)" });

    let prev = newBlock.find(".adjacent-tables")[0].value; // Creating prev to keep track of changes between adjacent tables

    // When the user selects an adjacent table
    newBlock
      .find(".adjacent-tables")
      .change((event) => {
        let target_table = event.target.value;
        let table_data = init_data[target_table];

        if (prev === "") {
          // If we select an adjacent table for the first time
          // Update the table_count object with chosen table
          if (table_count.hasOwnProperty(target_table)) {
            // If the table is already in the object, we increment its value
            table_count[target_table] += 1;
          } else {
            // If not, we create it
            table_count[target_table] = 1;
          }
        } else {
          // If we choose a new table
          if (table_count.hasOwnProperty(prev) && table_count[prev] > 1) {
            // If the table is in the object
            table_count[prev] -= 1; // We decrement the value of the previously seelcted adj table
            if (table_count.hasOwnProperty(target_table)) {
              // If the currently selected table is in the object
              table_count[target_table] += 1;
            } else {
              table_count[target_table] = 1;
            }
          } else if (
            // If the previously selected table has been chosen only once, or never
            (table_count.hasOwnProperty(prev) && table_count[prev] === 1) ||
            !table_count.hasOwnProperty(prev)
          ) {
            delete table_count[prev]; // We completetly remove the property if it has been seen once
            if (table_count.hasOwnProperty(target_table)) {
              // If the currently selected table is in the object
              table_count[target_table] += 1;
            } else {
              table_count[target_table] = 1;
            }
          }
        }
        prev = target_table; // We set the previously chosen adj table to the currently chosen one

        newBlock
          .find("input.alias")
          .val(target_table + "_" + table_count[target_table]); // We set the value of the input text to the alias
        newBlock.find("input.alias").attr("data-table", target_table);

        // Init query-builder with the fields of the selected table and adequate filters
        newBlock
          .find(".collapsed-query-builder")
          .queryBuilder("setFilters", true, table_data.filters);

        // Init dropdown containing the fields related to the chosen adjacent table
        let items = table_data.filters.map((item) =>
          $("<option></option>").attr("value", item.label).text(item.label)
        );
        newBlock
          .find("select.table-selects")
          .empty()
          .append(...items)
          // Making sure the dropdown is built correctly
          .selectpicker("refresh");

        // Initialize join path selection if necessary
        let formerTableObject = newBlock.find("#former-table");
        let formerTable = formerTableObject[0].options[
          formerTableObject[0].selectedIndex
        ].getAttribute("data-table");
        let relationsFromTo = init_data[formerTable].relations[target_table];

        if (relationsFromTo.length > 1) {
          let join_paths = relationsFromTo.map((relation) => {
            return $("<option></option>")
              .attr("value", relation.from)
              .attr(
                "data-content",
                Mustache.render(
                  '{{from}} <i class="fas fa-long-arrow-alt-right"></i> {{to}}',
                  relation
                )
              )
              .text(relation.from + "  ->  " + relation.to);
          });

          newBlock
            .find("select#source-fields")
            .empty()
            .append(...join_paths)
            .selectpicker()
            // Init the tooltip for the initial table dropdown
            .parent()
            .tooltip({
              title: "Select the join path (Source and Target fields)",
            });

          newBlock.find("#join-source-fields").show();
        } else newBlock.find("#join-source-fields").hide(); // No need to show the menu if not necessary

        // Making sure the buttons are enabled after an adjacent table is chosen
        document.getElementById("add-join").disabled = false;
        document.getElementById("submit-button").disabled = false;
      })
      // Init tooltip
      .parent()
      .tooltip({
        title:
          "Select an Adjacent Table to the Former Table currently selected",
      });

    // Init the dropdown containing all the previously chosen tables, with the alias as the value and making sure the user can choose the alias he wants
    let table_options = getAvailableTables().map((t) =>
      $("<option></option>")
        .attr("value", t.alias)
        .attr('data-table', t.table)
        .text(t.table + " | " + t.alias)
    );

    newBlock
      .find("#former-table")
      .empty()
      .append(...table_options)
      .selectpicker("refresh")
      // When you select or change the value of the previous table you want to select
      .change((event) => {
        let target_table = event.target.options[
          event.target.selectedIndex
        ].getAttribute("data-table");
        let table_data = init_data[target_table];

        // Making sure we have a list of adjacent tables sorted by alphabetical order
        let sorted_adj_tables_list = Object.keys(table_data.relations).sort();
        let adj_tables_options = sorted_adj_tables_list.map((table) =>
          $("<option></option>").attr("value", table).text(table)
        );
        newBlock
          .find("#adjacent-tables")
          .empty()
          .append(adj_tables_options)
          .selectpicker("refresh")
          .change();

        if (newBlock.find(".highlight-div")) {
          newBlock.removeClass(" highlight-div");
        }
      })
      .change()
      // Init tooltips
      .parent()
      .tooltip({ title: "Select a previously chosen Table" });

    // Remove Join button to give the user the possibility to remove a join block
    newBlock
      .find(".remove")
      .tooltip()
      .click((_) => {
        let joinAlias = newBlock.find("input.alias").val();
        $(`option[value=${joinAlias}]`).remove();
        $(".selectpicker").selectpicker("refresh");
        newBlock.find(".form-block").remove();
        if (newBlock.find(".highlight-div")) {
          newBlock.removeClass(" highlight-div");
        }

        // The alert is there to guide the user on why there are highlighted blocks
        alert(
          "Removed Join Block Successfully!\nBe warned : you might need to make changes in the highlighted blocks!"
        );
        let blockList = document.getElementsByClassName("form-block-id");
        let suppressedJoinId = newBlock.find(".form-block").prevObject[0].id;
        $.each(blockList, (item) => {
          if (blockList[item].id > suppressedJoinId) {
            document.getElementById(blockList[item].id).className +=
              " highlight-div";
            // $(item).addClass(" highlight-div");
          }
        });
      });
  };
}

/**
 * Get information contained in the initial block
 * @return {Object}
 */
export function get_form_initial() {
  // Geetting the initial table and the matching alias
  let table1 = document.getElementById("initial-table");
  let initialTable = table1.options[table1.selectedIndex].value;
  let initialAlias = document.getElementById("init-alias").value;

  // If the user used Constraints
  if ($("#initial-constraints-switchbox").is(":checked") == true) {
    var constraintsTable1 = $(".collapsed-query-builder")
      .eq(0)
      .queryBuilder("getRules");
  } else {
    var constraintsTable1 = {};
  }

  if (constraintsTable1 === null)
    return null

  // Getting the selected fields
  let fieldsSelected = $("#initial-fields").find("option:selected");
  let initialFields = [];
  fieldsSelected.each(function () {
    initialFields.push($(this).val());
  });

  return {
    initialTable,
    initialAlias,
    rules: constraintsTable1,
    initialFields,
  };
}

/**
 * Get the information contained in each join block
 * @param {Object} init_data
 * @return {Array}
 */
export function get_form_block_data(init_data) {
  let block_list = $(".form-block");
  let data = block_list
    .map(function () {
      let block = $(this);
      let adj_table = block.find("#adjacent-tables").val();
      let formerTabAlias = block.find("#former-table").val();
      let formerTObj = block.find("#former-table");
      let formerT = formerTObj[0].options[
        formerTObj[0].selectedIndex
      ].getAttribute("data-table");
      let idJoin = block.find("#join-type").val();
      let selectedFields = block.find(".table-selects option:selected");
      let alias = block.find("input.alias").val();
      let fields = [];
      selectedFields.each(function () {
        fields.push($(this).val());
      });
      // Obtaining the source field and target field
      let relationAdj = init_data[formerT].relations[adj_table];
      if (relationAdj.length > 1) {
        // If there is more than 1 foreign key linking the two tables
        var sourceField = block.find("#source-fields").val();
        var targetField = "";
        $.each(relationAdj, (item) => {
          let textOption = block.find("#source-fields option:selected")[0]
            .firstChild.data;
          let str = textOption.split(" ");
          if (relationAdj[item].to == str[4]) {
            targetField = relationAdj[item].to;
          }
        });
      } else {
        var sourceField = relationAdj[0].from;
        var targetField = relationAdj[0].to;
      }

      // Constraints
      if (block.find("#join-constraints-switchbox").is(":checked") == true) {
        var constraintsTable2 = block
          .find(".collapsed-query-builder")
          .queryBuilder("getRules");
      } else {
        var constraintsTable2 = {};
      }

      return {
        formerTable: formerT,
        formerTableAlias: formerTabAlias,
        join: idJoin,
        adjacent_table: adj_table,
        sourceField: sourceField,
        alias: alias,
        targetField: targetField,
        rules: constraintsTable2,
        fields: fields,
      };
    })
    .toArray();

  // Checks that all blocks are validated
  if (data.some(block => block.rules === null))
    return null;

  return data;
}

/**
 * Displaying the "scroll to the top" button
 */
export function scrollFunction() {
  if (document.body.scrollTop > 30 || document.documentElement.scrollTop > 30) {
    document.getElementById("myBtn").style.display = "block";
  } else {
    document.getElementById("myBtn").style.display = "none";
  }
}

/**
 * Scrolling to the top of the form
 */
export function topFunction() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}
