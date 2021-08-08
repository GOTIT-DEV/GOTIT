import { initSearchSelect } from "./field-suggestions";

$(() => {
  const $form = $("form[name='slide']");
  const $specimen = $form.find("#slide_specimenFk");

  initSearchSelect($specimen, "specimen_search_by_codeindmorpho");
});
