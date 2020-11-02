import { initSearchSelect } from "./field-suggestions"

$(() => {
  const $form = $("form[name='bbees_e3sbundle_adn']")
  const $specimen = $form.find("#bbees_e3sbundle_adn_individuFk")

  initSearchSelect($specimen, "individu_search")
})