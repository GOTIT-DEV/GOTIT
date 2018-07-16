/* **************************
 *  Document ready
 **************************** */
$(document).ready(function() {
  $("select.concordance")
    .change(updateChoiceColor)
    .trigger('change')
  initDataTable("#result-table")

  // $("#main-form").submit(function(event) {
  //   const formData = $(this).serialize()
  //   console.log(formData)
  //   event.preventDefault();

  //   $.ajax({
  //     type: "POST",
  //     data: formData,
  //     url: "http://localhost:8000/requetes/concordance/search",
  //     success: function(response) {
  //       console.log(response)
  //     }
  //   })
  // })

})

/**
 * Change la couleur des selecteurs pour visualiser les contraintes 
 * 
 * @param {Object} event l'objet d'événement jquery
 */
function updateChoiceColor(event) {
  const target = $(event.target)
  target.removeClass("typeA typeB typeC unassigned no-constraints")
  console.log(target)
  switch (target.val()) {
    case "A":
      target.addClass("typeA")
      break
    case "B":
      target.addClass("typeB")
      break
    case "C":
      target.addClass("typeC")
      break
    case "0":
      target.addClass("no-constraints")
      break
    case "1":
      target.addClass("unassigned")
      break
  }
}

/* **************************
 *  Initialize datatable
 **************************** */

function initDataTable(tableId) {
  if (!$.fn.DataTable.isDataTable(tableId)) {
    const table = $(tableId)
    const urls = {
      refTaxon: table.find("th#col-taxname").data('linkUrl'),
      geocoords: table.find("th#col-details").data('linkUrl')
    }
    const renderNumber = $.fn.dataTable.render.number('', '.', 3)

    table.DataTable({
        autoWidth: false,
        responsive: true,
        ajax: {
          "url": $("#main-form").data("url"),
          "dataSrc": "rows",
          "type": "POST",
          "data": function(d) {
            return $("#main-form").serialize()
          }
        },
        dom: "lfrtipB",
        buttons: [
          'copy', 'csv', 'excel'
        ],
        columns: [{
          data: "code_lm",
          render: $.fn.dataTable.render.ellipsis(20, true)
            // render: function(data, type, row) {
            //   return "<a href='" + urls.refTaxon + row.id + "'>" + data + "</a>"
            // }
        }, {
          data: "taxname_lm",
          render: $.fn.dataTable.render.ellipsis(20, true)
        }, {
          data: "critere_lm"
        }, {
          data: "code_biomol",
          render: $.fn.dataTable.render.ellipsis(20, true)
        }, {
          data: "code_tri_morpho",
          render: $.fn.dataTable.render.ellipsis(20, true)
        }, {
          data: "taxname_indiv",
          render: $.fn.dataTable.render.ellipsis(20, true)
        }, {
          data: "critere_indiv"
        }, {
          data: "code_seq",
          defaultContent: "-",
          render: $.fn.dataTable.render.ellipsis(20, true)
        }, {
          data: "taxname_seq",
          render: $.fn.dataTable.render.ellipsis(20, true),
          defaultContent: "-"
        }, {
          data: "critere_seq",
          defaultContent: "-"
        }],
        drawCallback: function(settings) {
            $("#main-form").find("button[type='submit']").button('reset')
          } // drawCallback
      }) // datatables

    /****************
     * Submit form handler
     */
    $("#main-form").submit(function(event) {
      event.preventDefault()
      $(this).find("button[type='submit']").button('loading')
      var results = table.DataTable()
      results.ajax.reload()
    })
  }
}