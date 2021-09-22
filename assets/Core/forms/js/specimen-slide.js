import { initSearchSelect } from "./field-suggestions";

$(() => {
  const $form = $("form[name='slide']");
  const $specimen = $form.find("#slide_specimen");

  initSearchSelect($specimen, "specimen_search_by_codeindmorpho");
});
