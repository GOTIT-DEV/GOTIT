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



/* **************************
 *  Document ready
 **************************** */
$(document).ready(function () {

  let speciesSelector = new SpeciesSelector("#main-form")
  let methodSelector = new MethodSelector("#main-form")

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

  uiWaitResponse()


  // Sync target and reference dataset <select> inputs
  $("#main-form select#dataset").change(event => {
    $("#target-dataset").val(event.target.value).selectpicker('refresh')
  }).trigger('change')

  // Wait for active selector  to be ready
  switch ($('input[name="reference"]:checked').val()) {
    case "0":
      initDataTable("#result-table-recto")
      initDataTable("#result-table-verso")
      break;
    case "1":
      speciesSelector.promise.then(_ => {
        initDataTable("#result-table-recto")
        initDataTable("#result-table-verso")
      })
      break;
    case "2":
      methodSelector.promise.then(_ => {
        initDataTable("#result-table-recto")
        initDataTable("#result-table-verso")
      })
      break;
    default:
      console.error("Invalid value for reference radio input : " +
        $('input[name="reference"]:checked').val())
      break;
  }
})

function uiWaitResponse() {
  $("#main-form").find("button[type='submit']").button('loading')
  $(".nav-tabs li")
    .addClass("disabled")
    .find('a')
    .attr('data-toggle', '')
}

function uiReceivedResponse() {
  $("#main-form").find("button[type='submit']").button('reset')
  $(".nav-tabs li")
    .removeClass("disabled")
    .find('a')
    .attr('data-toggle', 'tab')
}

function toggleFormSelect(event) {
  $(event.target).tooltip('hide')
  switch (event.target.value) {
    case "0":
      $(".method-select").prop('disabled', true)
      $(".taxa-select").prop('disabled', true)
      $("#target-dataset").prop('disabled', false)
      break

    case "1":
      $(".method-select").prop('disabled', true)
      $(".taxa-select").prop('disabled', false)
      $("#target-dataset").prop('disabled', false)
      break

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

/* **************************
 *  Initialize datatable
 **************************** */

function initDataTable(tableId) {
  if (!$.fn.DataTable.isDataTable(tableId)) {
    $.ajax({
      url: Routing.generate("user_current"),
      type: "GET"
    }).done(user => {
      let dtbuttons = user.role === 'ROLE_INVITED' ? [] : dtconfig.buttons
      const table = $(tableId)
      const side = table.data('target')
      let barplot = new BarPlot(table.data('barplot'))
      $('.nav-tabs li a').on('shown.bs.tab', event => {
        barplot.resize()
      })
      var dataTable = table.DataTable({
        responsive: true,
        autoWidth: false,
        ajax: {
          "url": Routing.generate('species-hypotheses-query'),
          "dataSrc": side,
          "type": "POST",
          "data": _ => {
            return $("#main-form").serialize()
          }
        },
        language: dtconfig.language[$("html").attr("lang")],
        dom: 'lf<"clear pull-right"B>rtip',
        buttons: dtbuttons,
        order: [1, 'asc'],
        columns: [{
          data: "methode"
        }, {
          data: "libelle_motu",
        }, {
          data: "match"
        }, {
          data: "split"
        }, {
          data: "lump"
        }, {
          data: "reshuffling"
        }, {
          data: 'nb_seq'
        }, {
          data: 'nb_sta'
        }],
        drawCallback: _ => {
          $('[data-toggle="tooltip"]').tooltip()
        }
      })

      dataTable.on('xhr', _ => {
        let response = dataTable.ajax.json()
        barplot.refresh(response[side])
        uiReceivedResponse()
      })

      /****************
       * Submit form handler
       */
      $("#main-form").submit(event => {
        event.preventDefault();
        uiWaitResponse()
        table.DataTable().ajax.reload()
      });
    })
  }
}