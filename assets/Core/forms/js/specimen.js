import { initSearchSelect } from "./field-suggestions"

$(() => {

  const $form = $("form[name='bbees_e3sbundle_individu']")
  const $biomat = $("#bbees_e3sbundle_individu_lotMaterielFk")
  const $taxon = $("#bbees_e3sbundle_individu_especeIdentifiees_0_referentielTaxonFk")
  const $tube = $("#bbees_e3sbundle_individu_codeTube")
  const $spMolNumber = $("#bbees_e3sbundle_individu_numIndBiomol")

  const $codeMorpho = $("#bbees_e3sbundle_individu_codeIndTriMorpho")
  const $codeMol = $("#bbees_e3sbundle_individu_codeIndBiomol")

  initSearchSelect($biomat, "lotmateriel_search")

  if ($form.data('action') == 'new') {
    $biomat.change(updateSpecimenMorphoCode)
    $taxon.change(updateSpecimenMorphoCode)
    $tube.keyup(updateSpecimenMorphoCode)
    updateSpecimenMorphoCode()
  } else if ($form.data('action') == 'edit') {
    $taxon.change(updateSpecimenMolCode)
    $biomat.change(updateSpecimenMolCode)
    $spMolNumber.keyup(updateSpecimenMolCode)
  }

  function updateSpecimenMorphoCode() {
    const code = generateSpecimenMorphoCode(
      $taxon.val() ? $taxon.find('option:selected').text() : undefined,
      $biomat.val() ? $biomat.find('option:selected').text() : "",
      $tube.val() || undefined
    )
    $codeMorpho.val(code)
    return code
  }
  function updateSpecimenMolCode() {
    const molNumber = $spMolNumber.val()

    const code = molNumber ? generateSpecimenMolCode(
      $taxon.val() ? $taxon.find('option:selected').text() : undefined,
      $biomat.val() ? $biomat.find('option:selected').text() : "",
      molNumber
    ) : ""

    $codeMol.val(code)
    return code
  }

  function generateSpecimenMolCode(taxon, biomatCode, molNumber) {
    const samplingCode = extractSamplingCode(biomatCode)
    return `${taxon}_${samplingCode}_${molNumber}`
  }


  function generateSpecimenMorphoCode(
    taxon = "{TAXON}",
    biomatCode = "",
    tubeCode = "{TUBE}"
  ) {
    const samplingCode = extractSamplingCode(biomatCode)
    return `${taxon}|${samplingCode}[${tubeCode}]`
  }

  function extractSamplingCode(bioMatCode) {
    return bioMatCode.split('|').pop() || "{SAMPLING}"
  }
})

