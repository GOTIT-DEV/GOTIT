import "bootstrap-select/dist/css/bootstrap-select.min.css";
import "../css/forms.less";

import "bootstrap-select";
import "select2";
require("select2/src/scss/core.scss");
require("@ttskch/select2-bootstrap4-theme/dist/select2-bootstrap4.css");

import { initDateMask } from "./date-mask";

$(() => {
  document.querySelectorAll("form").forEach(initDateMask);

  $("button.btn-entry-add").click(addEntryBtnCallback);
  $("button.btn-entry-delete").click(deleteEntryBtnCallback);

  // Init collection wrappers
  $(".collection-wrapper").each(function () {
    if ($(this).data("index") === 0) initEmptyCollection($(this));
    toggleDeleteButtons($(this));
  });

  // Init modal forms
  $(".modal-dialog form").submit(modalFormSubmitCallback);
});

function toggleDeleteButtons($wrapper) {
  const $entries = $wrapper.children(".entry-list").children();
  if ($wrapper.hasClass("required")) {
    $entries
      .children(".delete-btn-container")
      .find(".btn-entry-delete")
      .toggleClass("invisible", $entries.length === 1)
      .off()
      .click(deleteEntryBtnCallback);
  }
}

function initEmptyCollection($wrapper) {
  $wrapper.change(() => {
    toggleCollectionContent($wrapper);
  });
  if ($wrapper.hasClass("required")) addEntry($wrapper);
  else toggleCollectionContent($wrapper);
}

function toggleCollectionContent($wrapper) {
  const index = $wrapper.data("index");
  $wrapper.find(".card-body").toggle(index > 0);
}

function addEntryBtnCallback(event) {
  let wrapper_id = $(event.currentTarget).data("target");
  let $wrapper = $(document.getElementById(wrapper_id));
  addEntry($wrapper);
  $wrapper.trigger("change");
}

function deleteEntryBtnCallback(event) {
  let entry_wrapper_id = $(event.currentTarget).data("target");
  let entry_wrapper = document.getElementById(entry_wrapper_id);
  let $wrapper = $(event.currentTarget).closest(".collection-wrapper");
  $(entry_wrapper).prev("hr").remove();
  $(entry_wrapper).remove();

  // $wrapper.find(".btn-entry-delete").prop('disabled', true)
  resetIndexes($wrapper);
  toggleDeleteButtons($wrapper);
  // $wrapper.find(".btn-entry-delete").prop('disabled', false)
  // const currentIndex = $wrapper.data('index')
  // $wrapper.data('index', currentIndex - 1)
  $wrapper.trigger("change");
}

function modalFormResponseCallback($form, response) {
  let $wrapper = $($form.closest(".modal-container").data("target"));
  addEntryForNewRecord($wrapper, response.select_id, response.select_name);
}

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
        // Invalid form
        let $newForm = $(response.form);
        $newForm.find("select").selectpicker();
        $newForm.submit((event) => {
          event.preventDefault();
          modalFormSubmitCallback(event, responseCallback);
        });
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
        responseCallback($form, response);
        setTimeout(() => {
          $(".modal").modal("hide");
          $form.get(0).reset();
          $form.find(".form-status").removeClass("fa-check-circle");
        }, 1000);
      }
    },
    complete: function (htmlResponse) {
      $submitBtn.prop("disabled", false);
      $form.find(".form-status").removeClass("fa-spin fa-spinner");
    },
  });
}

//  function to add a embeded form
function addEntryForNewRecord($wrapper, id, name) {
  let optionElt = `<option value=${id}>${name}</option>`;
  updatePrototype($wrapper, optionElt);
  $wrapper.find(".entry-list select").append(optionElt).selectpicker("refresh");
  let mandatoryInput = $wrapper.find(".entry-list select:first");
  if (mandatoryInput.val() == "")
    mandatoryInput.val(id).selectpicker("refresh");
  else addEntry($wrapper, id);
}

function updatePrototype($wrapper, optionElt) {
  let newPrototype = $wrapper
    .data("prototype")
    .replace(/(<select[^>]*>)/, `$1${optionElt}`);
  $wrapper.data("prototype", newPrototype);
}

function createEntry(prototype, index, value = undefined, required = true) {
  prototype =
    prototype.match(/__name__/) !== null
      ? prototype.replace(/__name__/g, index)
      : prototype.replace(/__name_inner__/g, index);

  let $newForm = $(prototype);
  $newForm.attr("data-index", index);

  let $btn = $($newForm.find("template.delete-btn-template").html());
  $btn.attr("data-index", index);
  $newForm.find(".delete-btn-container").append($btn);

  // Init plugins
  $newForm.find(".selectpicker").selectpicker();
  initDateMask($newForm.get(0));
  $newForm.find("button.btn-entry-add").click(addEntryBtnCallback);
  $newForm.find(".collection-wrapper[data-index=0]").each(function () {
    initEmptyCollection($(this));
  });

  // Set initial value
  if (value !== undefined)
    $newForm.find(":input:first").val(value).selectpicker("refresh");

  return $newForm;
}

function addEntry($wrapper, value = undefined) {
  let index = $wrapper.data("index");
  let prototype = $wrapper.data("prototype");
  let $newForm = createEntry(
    prototype,
    index,
    value,
    $wrapper.hasClass("required")
  );
  let $form_container = $wrapper.find(".card-body.entry-list").first();
  const wrapperIsEmpty = $form_container.find(".collection-entry").length === 0;
  if ($newForm.find(".form-group").length > 1 && !wrapperIsEmpty) {
    $form_container.append("<hr>");
  }
  $form_container.append($newForm);
  $wrapper.data("index", index + 1);
  toggleDeleteButtons($wrapper);
}

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
