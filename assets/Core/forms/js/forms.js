import "bootstrap-select/dist/css/bootstrap-select.min.css";
import "../css/forms.less";

import "bootstrap-select";
import "select2";
require("select2/src/scss/core.scss");
require("@ttskch/select2-bootstrap4-theme/dist/select2-bootstrap4.css");

import { initDateMask } from "./date-mask";

/**
 * Form initialization
 */
$(() => {
  document.querySelectorAll("form").forEach(initDateMask);

  $("button.btn-entry-add").on("click", addEntryBtnCallback);
  $("button.btn-entry-delete").on("click", deleteEntryBtnCallback);

  // Init collection wrappers if it's edit/create context
    if ($("form").attr("data-action")!= "show") {  
        $(".collection-wrapper").each(function () {
          if ($(this).data("index") === 0) initEmptyCollection($(this));
          toggleDeleteButtons($(this));
        });
   }
  
  // Hide delete button if show context
    if ($("form").attr("data-action")== "show") {
        $(".collection-wrapper").each(function () {
                hideDeleteButtons($(this));
        });
    }
    
  // Init modal forms
  $(".modal-dialog form").on("submit", modalFormSubmitCallback);
});

function getEntries($wrapper) {
  return $wrapper.children(".entry-list").children();
}

/**
 * Toggles display of delete buttons in form collections
 * Depends on whether the field is required and the collection content
 * @param {JQuery element} $wrapper a collection wrapper (class .collection-wrapper)
 */
function toggleDeleteButtons($wrapper) {
  const $entries = getEntries($wrapper);
  $entries
    .children(".delete-btn-container")
    .find(".btn-entry-delete")
    .toggleClass(
      "invisible",
      $wrapper.hasClass("required") && $entries.length === 1
    )
    .off()
    .click(deleteEntryBtnCallback);
    // if it's a disabled context hide delete button
    if ($wrapper.find("input").first().attr('disabled') == "disabled" || 
            $wrapper.find("select").first().attr('disabled') == "disabled" ) {
          hideDeleteButtons($wrapper);
    }
}

/**
 * Hide display of delete buttons in form collections
 * Depends on whether the field is required and the collection content
 * @param {JQuery element} $wrapper a collection wrapper (class .collection-wrapper)
 */
function hideDeleteButtons($wrapper) {
    const $entries = getEntries($wrapper);
    $entries
      .children(".delete-btn-container")
      .find(".btn-entry-delete")
      .toggle()  
      .off() 
}

/**
 * Initialize a nested form for collection inside a collection wrapper
 * @param {JQuery element} $wrapper a collection wrapper (class .collection-wrapper)
 */
function initEmptyCollection($wrapper) {
  $wrapper.change(() => {
    toggleCollectionContent($wrapper);
  });
  // Initialize first entry if collection field is required
  if ($wrapper.hasClass("required")) addEntry($wrapper);
  else toggleCollectionContent($wrapper);
}

/**
 * Show/hide wrapper body depending on collection emptyness
 * @param {JQuery element} $wrapper a collection wrapper (class .collection-wrapper)
 */
function toggleCollectionContent($wrapper) {
  const index = $wrapper.data("index");
  $wrapper.find(".card-body").toggle(index > 0);
}

/**
 * Event callback to trigger adding an entry to a collection wrapper
 * @param {Event} event
 */
function addEntryBtnCallback(event) {
  let wrapper_id = $(event.currentTarget).data("target");
  let $wrapper = $(document.getElementById(wrapper_id));
  addEntry($wrapper);
  $wrapper.trigger("change");
}

/**
 * Event callback to trigger deleting an entry in a collection wrapper
 * @param {Event} event
 */
function deleteEntryBtnCallback(event) {
  // Get target of the button that fired the event
  let entry_wrapper_id = $(event.currentTarget).data("target");
  let entry_wrapper = document.getElementById(entry_wrapper_id);
  // Get the collection wrapper
  let $wrapper = $(event.currentTarget).closest(".collection-wrapper");
  let $separator = $(entry_wrapper).prev("hr");
  // Remove entry in collection and separator
  if ($separator.length) $separator.remove();
  else $(entry_wrapper).next("hr").remove();
  $(entry_wrapper).remove();
  // Update remaining wrapper content
  resetIndexes($wrapper);
  toggleDeleteButtons($wrapper);
  $wrapper.trigger("change");
}

/**
 * Event callback after creating a new entity using a modal form
 * @param {jQuery element} $form the form in modal pop-up
 * @param {Object} response the new option to add in the main form <select> options
 */
function modalFormResponseCallback($form, response) {
  // Find the target collection wrapper for the modal form
  let $wrapper = $($form.closest(".modal-container").data("target"));
  // Add the new option as an entry in the collection
  addEntryForNewRecord($wrapper, response.select_id, response.select_name);
}

/**
 * Event callback on modal form submit
 * @param {jQuery element} event the modal form `submit` event
 * @param {Function} responseCallback the handler function for a successful response
 */
export function modalFormSubmitCallback(
  event,
  responseCallback = modalFormResponseCallback
) {
  event.preventDefault();
  let $form = $(event.target);
  let $submitBtn = $form.find("button[type='submit']");

  // Update UI
  $submitBtn.prop("disabled", true);
  $form
    .find(".form-status")
    .addClass("fa-spin fa-spinner")
    .removeClass("fa-check-circle");
  $form.find(".errors").html("");

  // Send request
  $.ajax({
    type: $form.attr("method"),
    url: $form.attr("action"),
    data: $form.serialize(),
    error: function (jqXHR, textStatus, errorThrown) {
      $form.find(".errors").append(
        `<p>Server error ${jqXHR.status} : ${errorThrown}<br/>
            Details logged in console</p>`
      );
      console.warn(jqXHR);
    },
    success: function (response) {
      if (response.valid === false) {
        // submitted form is invalid : response contains new form template with error feedback
        let $newForm = $(response.form);
        $newForm.find("select").selectpicker();
        $newForm.submit((event) => {
          event.preventDefault();
          modalFormSubmitCallback(event, responseCallback);
        });
        // Replace current form with received form to display feedback to user
        $form.replaceWith($newForm);
      } else if (response.exception === true) {
        // Ajax or server error
        $form.find(".form-status").addClass("fa-spin fa-times-circle");
        $form
          .find(".errors")
          .append(`<p>Database error : ${response.exception_message}</p>`);
      } else {
        // Everything is fine
        $form.find(".form-status").addClass("fa-check-circle");
        // Handle the response, typically propagate back to main form
        responseCallback($form, response);
        // Get back to main form
        setTimeout(() => {
          $(".modal").modal("hide");
          $form.get(0).reset();
          $form.find(".form-status").removeClass("fa-check-circle");
        }, 1000);
      }
    },
    complete: function () {
      $submitBtn.prop("disabled", false);
      $form.find(".form-status").removeClass("fa-spin fa-spinner");
    },
  });
}

/**
 * Add a newly created item as a collection entry
 * @param {jQuery element} $wrapper
 * @param {String|Number} id
 * @param {String} name
 */
function addEntryForNewRecord($wrapper, id, name) {
  let $optionElt = $(`<option value=${id}>${name}</option>`);
  // update wrapper prototype used to create new collection entries
  updatePrototype($wrapper, $optionElt);
  const $entries = getEntries($wrapper);
  // update existing collection entries in wrapper
  $entries.each((index, entry) => {
    $(entry).find("select option").eq(1).before($optionElt);
  });
  // Update existing select element with new value if no current value is defined
  if ($entries.length === 1 && !$entries.first().find("select").val()) {
    $entries.first().find("select").val(id).selectpicker("refresh");
  } else {
    addEntry($wrapper, id);
  }
  $wrapper.change();
}

/**
 * Update wrapper prototype used to create new collection entries with additionnal options
 * @param {jQuery element} $wrapper a collection wrapper
 * @param {String} $optionElt an <option> tag to be added
 */
function updatePrototype($wrapper, $optionElt) {
  let prototypeElt = $.parseHTML($wrapper.data("prototype"));
  $(prototypeElt).find("select option").eq(1).before($optionElt);
  const newPrototype = $(prototypeElt).prop("outerHTML");
  $wrapper.data("prototype", newPrototype);
}

/**
 * Create a new entry from a prototype string
 * @param {String} prototype The entry prototype defined in the wrapper
 * @param {Number} index the index of the new entry in the wrapper
 * @param {String|Number} value the initial value of the newentry
 * @param {Boolean} required
 * @returns new entry as jQuery element
 */
function createEntry(prototype, index, value = undefined, required = true) {
  prototype =
    prototype.match(/__name__/) !== null
      ? prototype.replace(/__name__/g, index) // first depth level
      : prototype.replace(/__name_inner__/g, index); // second depth level

  // Create element from prototype
  let $newEntry = $(prototype);
  $newEntry.attr("data-index", index);

  // Setup delete button
  let $btn = $($newEntry.find("template.delete-btn-template").html());
  $btn.attr("data-index", index);
  $newEntry.find(".delete-btn-container").append($btn);

  // Initialize internals
  $newEntry.find(".selectpicker").selectpicker();
  initDateMask($newEntry.get(0));
  $newEntry.find("button.btn-entry-add").click(addEntryBtnCallback);
  $newEntry.find(".collection-wrapper[data-index=0]").each(function () {
    initEmptyCollection($(this));
  });

  // Set initial value
  if (value !== undefined)
    $newEntry.find(":input:first").val(value).selectpicker("refresh");

  return $newEntry;
}

/**
 * Add an entry in a wrapper
 * @param {jQuery element} $wrapper a collection wrapper
 * @param {*} value the entry value
 */
function addEntry($wrapper, value = undefined) {
  let index = $wrapper.data("index");
  let prototype = $wrapper.data("prototype");
  let $newEntry = createEntry(
    prototype,
    index,
    value,
    $wrapper.hasClass("required")
  );
  let $form_container = $wrapper.find(".card-body.entry-list").first();
  const wrapperIsEmpty = $form_container.find(".collection-entry").length === 0;

  // Add separator if necessary
  if ($newEntry.find(".form-group").length > 1 && !wrapperIsEmpty) {
    $form_container.append("<hr>");
  }
  $form_container.append($newEntry);
  $wrapper.data("index", index + 1);
  toggleDeleteButtons($wrapper);
}

/**
 * Sets the index of an entry inside a wrapper
 * @param {jQuery element} $entry a collection entry
 * @param {Number} index
 */
function setIndex($entry, index) {
  const currentIndex = $entry.data("index");
  const oldId = $entry.attr("id");
  const newId = oldId.replace(new RegExp(`_${currentIndex}$`), `_${index}`);
  $entry.data("index", index);
  $entry.attr("id", newId);
  $entry
    .find(`:input[id*=${oldId}]`)
    .attr("name", (pos, currentName) =>
      currentName
        ? currentName.replace(`[${currentIndex}]`, `[${index}]`)
        : currentName
    )
    .attr("id", (pos, id) => (id ? id.replace(oldId, newId) : id));
  $entry.find("button.btn-entry-delete").data("target", newId);
}

/**
 * Reset all entry indexes in the wrapper
 * @param {jQuery element} $wrapper a collection wrapper
 */
function resetIndexes($wrapper) {
  const $entryContainer = $wrapper.find(".card-body.entry-list");
  const $entries = $entryContainer.children();
  $entries.each((index, entry) => setIndex($(entry), index));
  $wrapper.data("index", $entries.length);
}

export function getSelectedCode($vocField) {
  return $vocField.val()
    ? $vocField.find("option:selected").data("code")
    : undefined;
}
