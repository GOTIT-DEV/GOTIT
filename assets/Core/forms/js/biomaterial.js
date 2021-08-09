import { initSearchSelect } from "./field-suggestions";

$(() => {
  const $form = $("form[name='internal_lot']");
  const $sampling = $("#internal_lot_samplingFk");
  const $taxon = $("#internal_lot_taxonIdentifications_0_taxonFk");
  const $code = $("#internal_lot_code");

  initSearchSelect($sampling, "sampling_search");

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
