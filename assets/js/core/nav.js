// Navbar events and animations

import Cookies from "js-cookies";

function close_sub_menus() {
  $(".nav-left .menu-entry.active")
    .removeClass("active")
    .find('ul:first')
    .slideUp("fast")
}

$(".nav-left .menu-entry>a").click(event => {
  let target = $(event.currentTarget)
  let entry_elt = target.parent('li.menu-entry');
  
  if (target.attr("href") === "#") {
    event.preventDefault()
  }

  if (entry_elt.hasClass('active')) {
    entry_elt.removeClass('active active-sm')
    $('ul:first', entry_elt).slideUp()
  } else {
    close_sub_menus()
    entry_elt.addClass("active")
    $('ul:first', entry_elt).slideDown("fast")
  }
})


$('#menu-toggle').click(event => {
  $('.nav-left').toggleClass("nav-sm")
  if ($('.nav-left').hasClass("nav-sm"))
    Cookies.set("gotit-menu-layout", "small")
  else
    Cookies.set("gotit-menu-layout", undefined)
})