import { modalFormSubmitCallback } from "./forms"

import { initMunicipalityCodeGeneration } from "./municipality"

$(() => {
  const $countryInput = $("#station_paysFk")
  const $municipality = $("#station_communeFk")
  $countryInput.change(event => {
    const country = event.target.value
    fetch(Routing.generate('country_municipalities', { id: country }))
      .then(response => response.json())
      .then(json => {
        const options = json.map(item =>
          `<option value="${item.id}">${item.codeCommune}</option>`
        )
        $("#station_communeFk").empty().append(options).val('').selectpicker('refresh')
      })
  })


  // Municipality modal 
  const modalId = "#modal-station_newMunicipality"
  initMunicipalityCodeGeneration(modalId)

  const $modal = $(modalId)
  $modal.find("form").off("submit").submit(function (event) {
    event.preventDefault()
    modalFormSubmitCallback(event, modalCallback)
  })

  function modalCallback(_, response) {
    const $modalCountry = $modal.find("select#commune_paysFk")
    $countryInput.val($modalCountry.val()).selectpicker('refresh')
    $municipality
      .append($('<option>', {
        value: response.select_id,
        text: response.select_name
      }))
      .val(response.select_id)
      .selectpicker('refresh')

  }
})
