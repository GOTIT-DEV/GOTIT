import { initSearchSelect } from "./field-suggestions";

$(() => {
  const $form = $("form[name='external_lot']");
  const $sampling = $("#external_lot_collecteFk");
  const $taxon = $("#external_lot_taxonIdentifications_0_referentielTaxonFk");
  const $code = $("#external_lot_codeLotMaterielExt");

  initSearchSelect($sampling, "collecte_search");

  if ($form.data("action") == "new") {
    $sampling.change(updateBiomatCode);
    $taxon.change(updateBiomatCode);
    updateBiomatCode();
  }

  function updateBiomatCode() {
    const code = generateBiomatCode(
      $taxon.val() ? $taxon.find("option:selected").text() : undefined,
      $sampling.val() ? $sampling.find("option:selected").text() : undefined
    );
    $code.val(code);
    return code;
  }

  function generateBiomatCode(taxon = "{TAXON}", samplingCode = "{SAMPLING}") {
    return `${taxon}|${samplingCode}`;
  }
});
