
import { fetchCurrentUser } from '../utils.js'
import { dtconfig, linkify } from '../datatables_utils.js'


// Render floats with precision 3
const renderNumber = $.fn.dataTable.render.number('', '.', 3)

// Result table columns definition
let columns = [
  dtconfig.expandColumn,
  {
    data: "taxname",
    render: linkify("referentieltaxon_show", { col: 'id' })
  }, {
    data: "nb_sta"
  }, {
    data: "lmp",
    render: renderNumber,
    defaultContent: ""
  }, {
    data: "mle",
    render: renderNumber,
    defaultContent: ""
  }, {
    data: "nb_sta_co1"
  }, {
    data: "lmp_co1",
    render: renderNumber,
    defaultContent: ""
  }, {
    data: "mle_co1",
    render: renderNumber,
    defaultContent: ""
  }, {
    title: "<i class='fas fa-map-marker' style='margin-left:7px'></i>",
    data: "id",
    orderable: false,
    render: (data, type, row) =>
      Mustache.render($("#details-form-template").html(), row)
  }
]

/**
 * Initializes Datatable on result table DOM element
 * @param {String} tableId result table DOM id
 */
function initDataTable(tableId, drawCallback) {
  
  const table = $(tableId)

  if (!$.fn.DataTable.isDataTable(tableId)) {
    fetchCurrentUser()
      .then(response => response.json())
      .then(user => {

        // Allow result table download to authorized users
        const dtbuttons = user.role === 'ROLE_INVITED' ? [] : dtconfig.buttons

        // Show download column to authorized users 
        if (user.role !== 'ROLE_INVITED')
          columns.push({
            title: "<i class='fas fa-download' style='margin-left:7px'></i>",
            orderable: false,
            data: "id",
            render: (data, type, row) =>
              Mustache.render($("#download-form-template").html(), row)
          })

        table.DataTable({
          autoWidth: false,
          responsive: {
            orthogonal: "responsive",
            details: {
              type: 'column'
            }
          },
          ajax: {
            "url": Routing.generate('co1-sampling-query'),
            "type": "POST",
            "dataSrc": "",
            "data": _ => {
              return $("#main-form").serialize()
            }
          },
          language: dtconfig.language[$("html").attr("lang")],
          dom: "lfrtipB",
          buttons: dtbuttons,
          order: [1, 'asc'],
          columns: columns,

          drawCallback: drawCallback
        }) // datatables

        /****************************
         * Submit form handler
         ************************** */
        $("#main-form").submit(event => {
          event.preventDefault()
          $(this).find("button[type='submit']").button('loading')
          table.DataTable().ajax.reload()
        })
      })
  }
}

export { initDataTable }