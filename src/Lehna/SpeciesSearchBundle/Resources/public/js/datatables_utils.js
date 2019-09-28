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
 * Renderer to truncate strings in datatable 
 * @param {int} cutoff max characters in string
 * @param {boolean} wordbreak allow word break
 * @param {boolean} escapeHtml escape string
 * @param {string} placement tooltip position
 */
jQuery.fn.dataTable.render.ellipsis = function (cutoff, wordbreak, escapeHtml = true, placement = 'top') {
  var esc = function (t) {
    return t
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  };

  return function (d, type, row) {
    // Order, search and type get the original data
    if (type !== 'display') {
      return d;
    }
    if (typeof d !== 'number' && typeof d !== 'string') {
      return d;
    }
    d = d.toString(); // cast numbers
    if (d.length <= cutoff) {
      return d;
    }
    var shortened = d.substr(0, cutoff - 1);
    // Find the last white space character in the string
    if (wordbreak) {
      shortened = shortened.replace(/\s([^\s]*)$/, '');
    }
    // Protect against uncontrolled HTML input
    if (escapeHtml) {
      shortened = esc(shortened);
    }
    return Mustache.render(
      '<span class="ellipsis" data-toggle="tooltip" \
      data-placement="{{placement}}" \
      title="{{title}}">{{shortText}}&#8230;</span>', {
        placement: placement,
        title: esc(d),
        shortText: shortened
      })
  };
};

/**
 * Renderer for URL links in datatables
 * @param {string} url base URL
 * @param {string} col name of JSON attributes to complete URL
 * @param {boolean} ellipsis truncate link representation
 */
function linkify(url,
  { col = null, ellipsis = true, placement = "top", generateRoute = true } =
    { col: null, ellipsis: true, placement: "top", generateRoute: true }) {
  return function (data, type, row) {
    if (data === null) {
      return data
    }
    let path = url
    if (generateRoute === true) {
      path = col === null ?
        Routing.generate(url, { _locale: $("html").attr("lang") }) :
        Routing.generate(url, { id: row[col], _locale: $("html").attr("lang") })
    } else {
      if (col != null) {
        path += row[col]
      }
    }

    let linkText = ellipsis === true ?
      $.fn.dataTable.render.ellipsis(20, true, true, placement)(data, type, row)
      : data

    return Mustache.render("<a href='{{path}}'>{{{text}}}</a>", {
      path: path,
      text: linkText
    })
  }
}


/**
 * Datatables configuration
 */
const dtconfig = {
  expandColumn: {
    data: null,
    defaultContent: "",
    "className": "control",
    "orderable": false,
  },
  responsiveRenderOptions: {
    "display": $.fn.dataTable.render.ellipsis(20, true),
    "responsive": null,
    "_": null,
  },
  buttons: [{
    extend: 'copy',
    exportOptions: {
      orthogonal: null
    }
  }, {
    extend: "csv",
    exportOptions: {
      orthogonal: null
    }
  }, {
    extend: 'excel',
    exportOptions: {
      orthogonal: null
    }
  }],
  language: {
    'en': {
      "sProcessing": "<i class='fas fa-spin fa-spinner'></i>&nbsp;Processing...",
      "sLoadingRecords": "<i class='fas fa-spin fa-spinner'></i>&nbsp;Loading...",
    },
    'fr': {
      "sProcessing": "<i class='fas fa-spin fa-spinner'></i>&nbsp;Traitement en cours...",
      "sSearch": "Rechercher&nbsp;:",
      "sLengthMenu": "Afficher _MENU_ &eacute;l&eacute;ments",
      "sInfo": "Affichage de l'&eacute;l&eacute;ment _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
      "sInfoEmpty": "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ment",
      "sInfoFiltered": "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
      "sInfoPostFix": "",
      "sLoadingRecords": "<i class='fas fa-spin fa-spinner'></i>&nbsp;Chargement en cours...",
      "sZeroRecords": "Aucun &eacute;l&eacute;ment &agrave; afficher",
      "sEmptyTable": "Aucune donn&eacute;e disponible dans le tableau",
      "oPaginate": {
        "sFirst": "Premier",
        "sPrevious": "Pr&eacute;c&eacute;dent",
        "sNext": "Suivant",
        "sLast": "Dernier"
      },
      "oAria": {
        "sSortAscending": ": activer pour trier la colonne par ordre croissant",
        "sSortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
      },
      "select": {
        "rows": {
          _: "%d lignes séléctionnées",
          0: "Aucune ligne séléctionnée",
          1: "1 ligne séléctionnée"
        }
      }
    }
  }
}

export { dtconfig, linkify }