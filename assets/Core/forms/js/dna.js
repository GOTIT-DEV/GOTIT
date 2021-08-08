import { initSearchSelect } from "./field-suggestions";

$(() => {
  const $form = $("form[name='dna']");
  const $specimen = $form.find("#dna_specimenFk");

  initSearchSelect($specimen, "specimen_search");
});
