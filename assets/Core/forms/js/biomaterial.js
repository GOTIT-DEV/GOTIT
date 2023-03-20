import { initSearchSelect } from "./field-suggestions"

$(() => {

  const $form = $("form[name='bbees_e3sbundle_lotmateriel']")
  const $sampling = $("#bbees_e3sbundle_lotmateriel_collecteFk")
  const $taxon = $("#bbees_e3sbundle_lotmateriel_especeIdentifiees_0_referentielTaxonFk")
  const $taxon_default_code = $("#bbees_e3sbundle_lotmateriel_especeIdentifiees_taxon_default_code")
  const $code = $("#bbees_e3sbundle_lotmateriel_codeLotMateriel")

  initSearchSelect($sampling, "collecte_search")


  if ($form.data('action') == 'new') {
    console.log("new action");
    $sampling.change(updateBiomatCode)
    $taxon.change(updateBiomatCode)
    updateBiomatCode()
  }

  function updateBiomatCode() {
    console.log($taxon_default_code.val());
    if($taxon_default_code.val()  == '' ){
       var $taxonCode = $taxon.val() ? $taxon.find('option:selected').text() : undefined
    } else {
       var $taxonCode = $taxon_default_code.val()
    }
    const code = generateBiomatCode(
      $taxonCode,
      $sampling.val() ? $sampling.find('option:selected').text() : undefined
    )
    $code.val(code)
    return code
  }

  function generateBiomatCode(taxon = "{TAXON}", samplingCode = "{SAMPLING}") {
    return `${taxon}|${samplingCode}`
  }
})

