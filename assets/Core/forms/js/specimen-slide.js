import { initSearchSelect } from "./field-suggestions"

$(() => {
  const $form = $("form[name='bbees_e3sbundle_individulame']")
  const $specimen = $form.find("#bbees_e3sbundle_individulame_individuFk")

  initSearchSelect($specimen, "individu_search_by_codeindmorpho")
})