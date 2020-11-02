/**
 * Main JS file for GOTIT Core application
 */
import '../css/core.less';

import $ from 'jquery';
global.jQuery = $;
global.$ = $;
import '@fortawesome/fontawesome-free';
import 'bootstrap';
import 'jquery-bootgrid/dist/jquery.bootgrid.js';

import './nav.js';
import './options.js';


/**
 * Bootgrid config to comply with bootstrap 4
 */
Object.assign($.fn.bootgrid.Constructor.defaults.css, {
  actions: "actions btn-group",
  icon: "icon fas",
  iconRefresh: "fa-sync-alt",
  iconSearch: "fa-search",
  iconColumns: "fa-list",
  dropDownMenuItems: "dropdown-menu dropdown-menu-right",
  paginationButton: "button",
  iconDown: "fa-chevron-down",
  iconUp: "fa-chevron-up",
})

Object.assign($.fn.bootgrid.Constructor.defaults.templates, {
  actionButton: '<button class="btn border" type="button" title="{{ctx.text}}">{{ctx.content}}</button>',
  actionDropDown: '<div class="{{css.dropDownMenu}}"><button class="btn border dropdown-toggle" type="button" data-toggle="dropdown"><span class="{{css.dropDownMenuText}}">{{ctx.content}}</span> <span class="caret"></span></button><ul class="{{css.dropDownMenuItems}}" role="menu"></ul></div>',
  search: `<div class="{{css.search}}"><div class="input-group">
          <div class="input-group-prepend"><span class="input-group-text"><i class="{{css.icon}} {{css.iconSearch}}"></i> </span></div>
          <input type="text" class="{{css.searchField}}" placeholder="{{lbl.search}}" />
          </div></div>`,
  pagination: `<div class="{{css.pagination}} btn-group btn-group-sm"></div>`,
  paginationItem: `<li class="btn border p-0 {{ctx.css}} btn-light" style="max-width: 40px;">
  <a data-page=\"{{ctx.page}}\" class="p-1 {{ctx.css}} {{css.paginationButton}}" style="display:inline-block;width: 100%; height: 100%;">
  {{ctx.text}}
  </a></li>`
})



// Progressbar
if ($(".progress .progress-bar")[0]) {
  $('.progress .progress-bar').progressbar();
}
$(document).ready(function () {
  $('[data-toggle="tooltip"]').tooltip({ container: 'body' });
});
