/*
 * This file file is part of the QueryBuilderBundle.
 *
 * It is a free software, included in a bigger project. You can use it and modify it under the terms of the GNU General Public License (Version 3 or later).
 * This software is distributed without any warranty.
 *
 * Authors : Thierno Diallo, Maud Ferrer and Elsa Mendes.
 */


import Mustache from "mustache"


/**
 * Initializing the dropdown containing all tables
 * @param {Oject} init_data
 */
export function initFirstTable(init_data) {

  // Making sure we have a list of tables sorted by alphabetical order
  let sorted_table_list = Object.keys(init_data).sort()
  let initial_options = sorted_table_list.map(
    table_name => $("<option></option>").attr("value", table_name).text(table_name)
  )

  // Adding every single table to the dropdown
  $("#initial-table").append(...initial_options)
    // Redbuilding the dropdown with the new info in it
    .selectpicker("refresh")
    // Init tooltip for the dropdown
    .parent().tooltip({ title: "Select the Initial Table" });
}

/**
 * Initializing the first query builder
 */
export function initFirstQueryBuilder() {

  // Init the query builder for the initial block
  $("#initial-query-builder").queryBuilder({
    plugins: ["bt-tooltip-errors"],
    filters: [
      {
        id: "empty",
        label: "empty",
        type: "integer",
      },
    ],
    lang: {
      "delete_rule": " ",
      "delete_group": " ",
    }
  });

  // Init the reset button for this query builder
  $(".reset").click((_) => {
    let target = $(this).data("target");
    $(target).queryBuilder("reset");
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
    width: '75%'
  })

  // What occurs when you choose a table and/or change it
  document.getElementById("initial-table").onchange = function (event) {
    let target_table = event.target.value;
    let table_data = init_data[target_table];

    // Init query-builder with fields and filters
    $("#initial-query-builder").queryBuilder("setFilters", true, table_data.filters);

    // Init list of fields ( without the dateCre, userCre, dateMaj, userMaj)
    let items = table_data.filters
      .filter(
        field => !(field.label.endsWith("Cre") || field.label.endsWith("Maj"))
      )
      .map(
        item => $("<option></option>")
          .attr("value", item.label)
          .attr("selected", true)
          .text(item.label)
      )

    // Init the dropdown containing the initial fields related to the chosen table
    $("#initial-fields").empty().append(...items)
      .selectpicker("refresh")
      // Init the tooltip for the initial table dropdown
      .parent()
      .tooltip({ title: "Select the Fields (all selected by default)" });

    // Making sure the dropdown works
    // $("#initial-fields").multiselect("rebuild");
    // $("#initial-fields").multiselect("selectAll", false);
    // $("#initial-fields").multiselect("updateButtonText");

    // Enables the plus button to add a join block when the first table is chosen (Disabled by default)
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

  // creating the new block
  newBlock = $("#form-block-" + block_id);

  // Query builder initialization for join blocks
  newBlock.find(".collapsed-query-builder").queryBuilder({
    plugins: ["bt-tooltip-errors"],
    filters: [
      {
        id: "empty",
        label: "empty",
        type: "integer",
      },
    ],
    lang: {
      "delete_rule": " ",
      "delete_group": " ",
    }
  });

  // Reset button
  newBlock.find(".reset").click((_) => {
    let target = $(this).data("target");
    $(target).queryBuilder("reset");
  });

  // Init switchobox buttons
  $("[data-toggle='toggle']").bootstrapToggle("destroy");
  $("[data-toggle='toggle']").bootstrapToggle();
  $("[data-toggle='tooltip']").tooltip();

  // Targeting the collapsed block of query builder
  newBlock.find("#join-constraints-switchbox").change(
    () => {
      newBlock.find(".toggled-constraints").slideToggle("fast")
      newBlock.find("#join-cc-reset").slideToggle("fast")
    }
  );

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

  let initial_table = document.getElementById("initial-table").value;
  let available_tables = $(".adjacent-tables").get()
    .map(elt => elt.value)
    .filter(value => value !== "")
    .concat([initial_table])

  // Making sure we don't have any blank line added to the dropdown
  for (let i = 0; i < available_tables.length; i++) {
     if (available_tables[i] === undefined) {
      available_tables.splice(i, 1)
    }
  }

  // Remove duplicates

  console.log(available_tables);
  return [...new Set(available_tables)].sort();
}

let new_block_id = 0;
/**
 * Init a join block each time you click on the plus button.
 * @param {Array} joinType containing all the joins possible
 * @param {Object} init_data containing all the data in the form
 */
export function initJoinBlock(joinType, init_data) {

  //This solution leads to an error with join type being removed from query
  /* let join_options = joinType.map(
    join => $("<option></option>").attr("value", join).text(join)
  )
  console.log(join_options); */ 

  // After each time the user clicks on the add join button
  document.getElementById("add-join").onclick = function () {

    // Adding 1 at each click
    new_block_id += 1;

    // Adding a block of query
    let newBlock = addJoin(new_block_id);

    //This solution leads to an error with join type being removed from query
    /* // Filling the menu containing the possible joins
    newBlock.find("#join-type").empty()
      .append(...join_options)
      // Making sure the dropdown is initialized correctly
      .selectpicker("refresh")
      // Init tooltip
      .parent().tooltip({ title: "Choose a JOIN Type" }); */

    // Init the JOIN Type dropdown
    newBlock.find("#join-type").empty().prop("selectedIndex", 0);
    $.each(joinType, (_index, value) => {
      newBlock
        .find("#join-type")
        .append($("<option></option>").attr("value", value).text(value));
    });

    // Making sure the dropdown is initialized correctly
    newBlock.find("#join-type").selectpicker("refresh").parent().tooltip({ title: "Choose a JOIN Type" });


    // Init the dropdown when the add-join button is clicked
    newBlock.find("select.table-selects").selectpicker({
      actionsBox: true,
      selectedTextFormat: "count > 4",
      title: "None selected",
      width: '100%'
    })
      // Init tooltip
      .parent().tooltip({ title: "Select the Fields (none selected by default)" });

    // When the user selects an adjacent table
    newBlock.find(".adjacent-tables").change((event) => {
      let target_table = event.target.value;
      let table_data = init_data[target_table];

      // Init query-builder with the fields of the selected table and adequate filters
      newBlock.find(".collapsed-query-builder")
        .queryBuilder("setFilters", true, table_data.filters);

      // Init dropdown containing the fields related to the chosen adjacent table
      let items = table_data.filters
        .filter(
          field => !(field.label.endsWith("Cre") || field.label.endsWith("Maj"))
        )
        .map(
          item => $("<option></option>").attr("value", item.label).text(item.label)
        )
      newBlock.find("select.table-selects")
        .empty()
        .append(...items)
        // Making sure the dropdown is built correctly
        .selectpicker("refresh")

      // Initialize join path selection if necessary
      let formerTable = newBlock.find("#former-table").val();
      let relationsFromTo = init_data[formerTable].relations[target_table];

      if (relationsFromTo.length > 1) {
        let join_paths = relationsFromTo
          .map((relation) => {
            return $("<option></option>")
              .attr("value", relation.from)
              .attr("data-content",
                Mustache.render(
                  '{{from}} <i class="fas fa-long-arrow-alt-right"></i> {{to}}',
                  relation))
              .text(relation.from + "  ->  " + relation.to)
          })

        newBlock.find("select#source-fields").empty()
          .append(...join_paths)
          .selectpicker()
          // Init the tooltip for the initial table dropdown
          .parent().tooltip({
            title: "Select the join path (Source and Target fields)",
          });

        newBlock.find("#join-source-fields").show();
      } else newBlock.find("#join-source-fields").hide();

      // Making sure the buttons are enabled after an adjacent table is chosen
      document.getElementById("add-join").disabled = false;
      document.getElementById("submit-button").disabled = false;
    })
      // Init tooltip
      .parent().tooltip({
        title: "Select an Adjacent Table to the Former Table currently selected",
      });;


    // Init the dropdown containing all the previously chosen tables
    let table_options = getAvailableTables().map(
      table => $("<option></option>").attr("value", table).text(table)
    )
    console.log(table_options);
    newBlock.find("#former-table").empty()
      .append(...table_options)
      .selectpicker("refresh")
      // When you select or change the value of the previous table you want to select
      .change((event) => {
        let target_table = event.target.value;
        let table_data = init_data[target_table];

        // Making sure we have a list of adjacent tables sorted by alphabetical order
        let sorted_adj_tables_list = Object.keys(table_data.relations).sort()
        let adj_tables_options = sorted_adj_tables_list.map(
          table => $("<option></option>").attr("value", table).text(table)
        )
        newBlock.find("#adjacent-tables").empty()
          .append(adj_tables_options)
          .selectpicker("refresh")
          .change();

        if (newBlock.find(".highlight-div")) {
          newBlock.removeClass(" highlight-div");
        }
      })
      .change()
      // Init tooltips
      .parent().tooltip({ title: "Select a previously chosen Table" });

    // Remove Join button to give the user the possibility to remove a join block
    newBlock.find(".remove")
      .tooltip()
      .click((_) => {
        newBlock.find(".form-block").remove();
        if (newBlock.find(".highlight-div")) {
          newBlock.removeClass(" highlight-div");
        }

        // The alert is there to guide the user on why there are highlighted blocks
        alert(
          "Removed Join Block Successfully!\nBe warned : you might need to make changes in the higlighted blocks!"
        );
        let blockList = document.getElementsByClassName("form-block-id");
        let suppressedJoinId = newBlock.find(".form-block").prevObject[0].id;
        $.each(blockList, (item) => {
          if (blockList[item].id > suppressedJoinId) {
            console.log("ok")
            document.getElementById(blockList[item].id).className += " highlight-div";
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

  let table1 = document.getElementById("initial-table");
  let initialTable = table1.options[table1.selectedIndex].value;

  // Constraints
  if ($("#initial-constraints-switchbox").is(":checked") == true) {
    var constraintsTable1 = $(".collapsed-query-builder")
      .eq(0)
      .queryBuilder("getRules");
  } else {
    var constraintsTable1 = null;
  }

  // Checked inputs
  let fieldsSelected = $("#initial-fields").find("option:selected");
  let initialFields = [];
  fieldsSelected.each(function () {
    initialFields.push($(this).val());
  });

  return { initialTable, constraintsTable1, initialFields };
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
      let formerT = block.find("#former-table").val();
      let idJoin = block.find("#join-type").val();
      let selectedFields = block.find(".table-selects option:selected");
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
        var constraintsTable2 = null;
      }

      return {
        formerTable: formerT,
        join: idJoin,
        adjacent_table: adj_table,
        sourceField: sourceField,
        targetField: targetField,
        constraints: constraintsTable2,
        fields: fields,
      };
    })
    .toArray();

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
