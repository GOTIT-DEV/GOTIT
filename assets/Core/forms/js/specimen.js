import { initSearchSelect } from "./field-suggestions";

$(() => {
  const $form = $("form[name='specimen']");
  const $biomat = $("#specimen_internalLot");
  const $taxon = $("#specimen_taxonIdentifications_0_taxon");
  const $tube = $("#specimen_tubeCode");
  const $spMolNumber = $("#specimen_molecularNumber");

  const $codeMorpho = $("#specimen_morphologicalCode");
  const $codeMol = $("#specimen_molecularCode");

  initSearchSelect($biomat, "internal_lot_search");

  if ($form.data("action") == "new") {
    $biomat.change(updateSpecimenMorphoCode);
    $taxon.change(updateSpecimenMorphoCode);
    $tube.keyup(updateSpecimenMorphoCode);
    updateSpecimenMorphoCode();
  } else if ($codeMol.data("generate") && $form.data("action") == "edit") {
    $taxon.change(updateSpecimenMolCode);
    $biomat.change(updateSpecimenMolCode);
    $spMolNumber.keyup(updateSpecimenMolCode);
  }

  function updateSpecimenMorphoCode() {
    const code = generateSpecimenMorphoCode(
      $taxon.val() ? $taxon.find("option:selected").text() : undefined,
      $biomat.val() ? $biomat.find("option:selected").text() : "",
      $tube.val() || undefined
    );
    $codeMorpho.val(code);
    return code;
  }
  function updateSpecimenMolCode() {
    const molNumber = $spMolNumber.val();

    const code = molNumber
      ? generateSpecimenMolCode(
          $taxon.val()
            ? $taxon.find("option:selected").data("code")
            : undefined,
          $biomat.val() ? $biomat.find("option:selected").text() : "",
          molNumber
        )
      : "";

    $codeMol.val(code);
    return code;
  }

  function generateSpecimenMolCode(taxon, biomatCode, molNumber) {
    const samplingCode = extractSamplingCode(biomatCode);
    return `${taxon}_${samplingCode}_${molNumber}`;
  }

  function generateSpecimenMorphoCode(
    taxon = "{TAXON}",
    biomatCode = "",
    tubeCode = "{TUBE}"
  ) {
    const samplingCode = extractSamplingCode(biomatCode);
    return `${taxon}|${samplingCode}[${tubeCode}]`;
  }

  function extractSamplingCode(bioMatCode) {
    return bioMatCode.split("|").pop() || "{SAMPLING}";
  }
});
