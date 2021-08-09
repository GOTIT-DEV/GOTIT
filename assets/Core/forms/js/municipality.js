
$(() => {
  initMunicipalityCodeGeneration("form[name='municipality']")
})

export function initMunicipalityCodeGeneration(formSelector) {
  const $form = $("form[name='municipality']")

  if ($form.data('action') == 'new') {
    let $country = $form.find("select#municipality_countryFk")
    let $region = $form.find("input#municipality_region")
    let $name = $form.find("input#municipality_name")
    let $code = $form.find("input#municipality_code")

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