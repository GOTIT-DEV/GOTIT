import "../css/dashboard.less"

import Mustache from 'mustache'

$(document).ready(() => {
  $("#last-updates-table").bootgrid({
    caseSensitive: false,
    padding: 2,
    rowCount: [5, 10, 25, 50, -1],
    formatters: {
      "showEntity": function (column, entities, value) {
        let template = '<a href="{{ dbName }}/{{id}}" class="btn btn-sm"><span class="glyphicon glyphicon-eye-open"></span></a>'
        return Mustache.render(template, entities)
      }
    }
  });
})
