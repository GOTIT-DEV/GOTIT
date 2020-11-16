import { modalFormSubmitCallback } from "./forms"

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
        $("#station_communeFk").empty().append(options).selectpicker('refresh')
      })
  })


  const $modal = $("#modal-station_newMunicipality")
  let $modalCountry = $modal.find("select#commune_paysFk")
  let $modalRegion = $modal.find("input#commune_nomRegion")
  let $modalName = $modal.find("input#commune_nomCommune")
  let $modalCode = $modal.find("input#commune_codeCommune")

  $modalRegion.keyup(updateMunicipalityCode)
  $modalName.keyup(updateMunicipalityCode)
  $modalCountry.change(updateMunicipalityCode)

  $modal.on('show.bs.modal', (event) => {
    const country = $countryInput.val()
    $modalCountry.val(country).selectpicker("refresh")
    updateMunicipalityCode()
  })

  $modal.find("form").off("submit").submit(function (event) {
    event.preventDefault()
    modalFormSubmitCallback(event, modalCallback)
  })

  function modalCallback(response) {
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

  function generateMunicipalityCode(
    municipality = "{ NAME }",
    region = "{ REGION }",
    country = "{ COUNTRY }"
  ) {
    return `${municipality}|${region}|${country}`
  }

  function updateMunicipalityCode() {
    const code = generateMunicipalityCode(
      $modalName.val() || undefined,
      $modalRegion.val() || undefined,
      $modalCountry.val() ? $modalCountry.find('option:selected').text() : undefined
    )
    $modalCode.val(code)
  }
})
