import { initSearchSelect } from "./field-suggestions"

$(() => {
  const $form = $("form[name='dna']")
  const $specimen = $form.find("#dna_individuFk")

  initSearchSelect($specimen, "individu_search")
})