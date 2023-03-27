import { initSearchSelect } from "./field-suggestions"

$(() => {

  const $form = $("form[name='bbees_e3sbundle_individu']")
  const $biomat = $("#bbees_e3sbundle_individu_lotMaterielFk")
  const $taxon = $("#bbees_e3sbundle_individu_especeIdentifiees_0_referentielTaxonFk")
  const $tube = $("#bbees_e3sbundle_individu_codeTube")
  const $spMolNumber = $("#bbees_e3sbundle_individu_numIndBiomol")
  

  const $codeMorpho = $("#bbees_e3sbundle_individu_codeIndTriMorpho")
  const $codeMol = $("#bbees_e3sbundle_individu_codeIndBiomol")
  
  const $taxon_default_name = $("#bbees_e3sbundle_individu_especeIdentifiees_taxon_default_name")
  const $taxon_default_code = $("#bbees_e3sbundle_individu_especeIdentifiees_taxon_default_code")

  initSearchSelect($biomat, "lotmateriel_search")

    //   // Initialize first entry if collection field is required

    let Specimen = {
        init: function () {
            $(document).on('load', '#wrapper_bbees_e3sbundle_individu_especeIdentifiees', Specimen.refresh);
            $(document).on('change', '#wrapper_bbees_e3sbundle_individu_especeIdentifiees', Specimen.refresh);
            Specimen.refresh();
        },
        refresh: function () {
            const $wrapper_bbees_e3sbundle_individu_especeIdentifiees = $("#wrapper_bbees_e3sbundle_individu_especeIdentifiees")
                console.log('load wrapper_bbees_e3sbundle_individu_especeIdentifiees');
                $('#wrapper_bbees_e3sbundle_individu_especeIdentifiees .collection-entry').each(function (index, element) {
                    let $element = $(element);
                    console.log(index);
                      // clear and hide idVocabularyOestrus
                      let $idreferentielTaxonFk = $('#bbees_e3sbundle_individu_especeIdentifiees_'+index+'_referentielTaxonFk');
                      $idreferentielTaxonFk.attr("required", true);
                      let $labelreferentielTaxonFk = $("label[for='bbees_e3sbundle_individu_especeIdentifiees_"+index+"_referentielTaxonFk']");
                      $labelreferentielTaxonFk.addClass('required text-danger');
                      // console.log("bbees_e3sbundle_individu[especeIdentifiees]["+index+"][critereIdentificationVocFk]");
                      let $idcritereIdentificationVocFk = $("[name='bbees_e3sbundle_individu[especeIdentifiees]["+index+"][critereIdentificationVocFk]']");
                      $idcritereIdentificationVocFk.attr("required", true);
                      let $iddatePrecisionVocFk = $("[name='bbees_e3sbundle_individu[especeIdentifiees]["+index+"][datePrecisionVocFk]']");
                      $iddatePrecisionVocFk.attr("required", true);
              });
        }       
    } 
    Specimen.init();  

  if ($form.data('action') == 'new') {
    console.log("new action");
    $taxon.change(updateSpecimenMorphoCode)
    $biomat.change(updateSpecimenMorphoCode)   
    $tube.keyup(updateSpecimenMorphoCode)
    updateSpecimenMorphoCode()
  } else if ($codeMol.data('generate') && $form.data('action') == 'edit') {
    $taxon.change(updateSpecimenMolCode)
    $biomat.change(updateSpecimenMolCode)
    $spMolNumber.keyup(updateSpecimenMolCode)
  }

  function updateSpecimenMorphoCode() {
    console.log('updateSpecimenMorphoCode : '+$taxon_default_name.val()+' taxon value = '+$taxon.val());
    if($taxon_default_name.val()  == '' ){
       var $taxonCode = $taxon.val() ? $taxon.find('option:selected').text() : undefined
    } else {
       var $taxonCode = $taxon.val() ? $taxon.find('option:selected').text() : $taxon_default_name.val()
    }
    const code = generateSpecimenMorphoCode(
      $taxonCode,
      $biomat.val() ? $biomat.find('option:selected').text() : "",
      $tube.val() || undefined
    )
    $codeMorpho.val(code)
    return code
  }
  function updateSpecimenMolCode() {
    const molNumber = $spMolNumber.val()
    console.log('updateSpecimenMolCode : '+$taxon_default_code.val());
    if($taxon_default_code.val()  == '' ){
       var $taxonCode = $taxon.val() ? $taxon.find('option:selected').data('code') : undefined
    } else {
       var $taxonCode = $taxon.val() ? $taxon.find('option:selected').data('code') : $taxon_default_code.val()
    }
    const code = molNumber ? generateSpecimenMolCode(
      $taxonCode,
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

