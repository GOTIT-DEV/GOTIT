import { initSearchSelect } from "./field-suggestions"

$(() => {

  const $form = $("form[name='bbees_e3sbundle_lotmateriel']")
  const $sampling = $("#bbees_e3sbundle_lotmateriel_collecteFk")
  const $taxon = $("#bbees_e3sbundle_lotmateriel_especeIdentifiees_0_referentielTaxonFk")
  const $code = $("#bbees_e3sbundle_lotmateriel_codeLotMateriel")

  initSearchSelect($sampling, "collecte_search")

  if ($form.data('action') == 'new') {
    $sampling.change(updateBiomatCode)
    $taxon.change(updateBiomatCode)
    updateBiomatCode()
  }

  function updateBiomatCode() {
    const code = generateBiomatCode(
      $taxon.val() ? $taxon.find('option:selected').text() : undefined,
      $sampling.val() ? $sampling.find('option:selected').text() : undefined
    )
    $code.val(code)
    return code
  }

  function generateBiomatCode(taxon = "{TAXON}", samplingCode = "{SAMPLING}") {
    return `${taxon}|${samplingCode}`
  }
})

