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
  initMunicipalityCodeGeneration("#modal-station_newMunicipality")

  $modal.find("form").off("submit").submit(function (event) {
    event.preventDefault()
    modalFormSubmitCallback(event, modalCallback)
  })

  function modalCallback(_, response) {
    $modalCountry = $modal.find("select#commune_paysFk")
    $modalRegion = $modal.find("input#commune_nomRegion")
    $modalName = $modal.find("input#commune_nomCommune")
    $modalCode = $modal.find("input#commune_codeCommune")
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
