import { initSearchSelect } from "./field-suggestions";

$(() => {
  const $form = $("form[name='bbees_e3sbundle_slide']");
  const $specimen = $form.find("#bbees_e3sbundle_slide_individuFk");

  initSearchSelect($specimen, "individu_search_by_codeindmorpho");
});
