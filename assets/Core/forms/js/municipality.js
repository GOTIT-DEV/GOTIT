
$(() => {
  initMunicipalityCodeGeneration("form[name='commune']")
})

export function initMunicipalityCodeGeneration(formSelector) {
  const $form = $("form[name='commune']")

  if ($form.data('action') == 'new') {
    let $country = $form.find("select#commune_countryFk")
    let $region = $form.find("input#commune_nomRegion")
    let $name = $form.find("input#commune_nomCommune")
    let $code = $form.find("input#commune_codeCommune")

    function updateMunicipalityCode() {
      const code = generateMunicipalityCode(
        $name.val() || undefined,
        $region.val() || undefined,
        $country.val() ? $country.find('option:selected').text() : undefined
      )
      $code.val(code)
    }

    $region.keyup(updateMunicipalityCode)
    $name.keyup(updateMunicipalityCode)
    $country.change(updateMunicipalityCode)

    updateMunicipalityCode()
  }
}

export function generateMunicipalityCode(
  municipality = "{NAME}",
  region = "{REGION}",
  country = "{COUNTRY}"
) {
  return [municipality, region, country]
    .map(str => str.trim())
    .join('|')
    .replaceAll(/\s+/g, '_')
}