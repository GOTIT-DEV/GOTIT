import { initSearchSelect } from "./field-suggestions"

$(() => {

  const $form = $("form[name='bbees_e3sbundle_lotmateriel']")
  const $sampling = $("#bbees_e3sbundle_lotmateriel_collecteFk")
  const $taxon = $("#bbees_e3sbundle_lotmateriel_especeIdentifiees_0_referentielTaxonFk")
  const $taxon_default_name = $("#bbees_e3sbundle_lotmateriel_especeIdentifiees_taxon_default_name")
  const $taxon_default_code = $("#bbees_e3sbundle_lotmateriel_especeIdentifiees_taxon_default_code")
  const $code = $("#bbees_e3sbundle_lotmateriel_codeLotMateriel")

  initSearchSelect($sampling, "collecte_search")

    //
    let Biomaterial = {
        init: function () {
            $(document).on('load', '#wrapper_bbees_e3sbundle_lotmateriel_especeIdentifiees', Biomaterial.refresh);
            $(document).on('change', '#wrapper_bbees_e3sbundle_lotmateriel_especeIdentifiees', Biomaterial.refresh);
            Biomaterial.refresh();
        },
        refresh: function () {
            const $wrapper_bbees_e3sbundle_lotmateriel_especeIdentifiees = $("#wrapper_bbees_e3sbundle_lotmateriel_especeIdentifiees")
                      console.log('load wrapper_bbees_e3sbundle_lotmateriel_especeIdentifiees');
                      $('#wrapper_bbees_e3sbundle_lotmateriel_especeIdentifiees .collection-entry').each(function (index, element) {
                          let $element = $(element);
                          console.log(index);
                            // clear and hide idVocabularyOestrus
                            let $idreferentielTaxonFk = $('#bbees_e3sbundle_lotmateriel_especeIdentifiees_'+index+'_referentielTaxonFk');
                            $idreferentielTaxonFk.attr("required", true);
                            let $labelreferentielTaxonFk = $("label[for='bbees_e3sbundle_lotmateriel_especeIdentifiees_"+index+"_referentielTaxonFk']");
                            $labelreferentielTaxonFk.addClass('required text-danger');
                            // console.log("bbees_e3sbundle_lotmateriel[especeIdentifiees]["+index+"][critereIdentificationVocFk]");
                            let $idcritereIdentificationVocFk = $("[name='bbees_e3sbundle_lotmateriel[especeIdentifiees]["+index+"][critereIdentificationVocFk]']");
                            $idcritereIdentificationVocFk.attr("required", true);
                            let $iddatePrecisionVocFk = $("[name='bbees_e3sbundle_lotmateriel[especeIdentifiees]["+index+"][datePrecisionVocFk]']");
                            $iddatePrecisionVocFk.attr("required", true);

              });
        }
    }    

  if ($form.data('action') == 'new') {
    console.log("new action");
    $sampling.change(updateBiomatCode)
    $taxon.change(updateBiomatCode)
    updateBiomatCode()
  }

  function updateBiomatCode() {
    console.log('updateBiomatCode : '+$taxon_default_name.val());
    if($taxon_default_name.val()  == '' ){
       var $taxonCode = $taxon.val() ? $taxon.find('option:selected').text() : undefined
    } else {
       var $taxonCode = $taxon.val() ? $taxon.find('option:selected').text() : $taxon_default_name.val()
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
  
  Biomaterial.init();
      
})

