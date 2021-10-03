import {
  initSearchSelect,
  apiUrlGenerator,
  paginatedResultsToItems,
} from "./field-suggestions";
import { getSelectedCode } from "./forms";

$(() => {
  const $form = $("form[name='pcr']");
  const $dna = $form.find("#pcr_dna");
  const $primerStart = $("#pcr_primerStart");
  const $primerEnd = $("#pcr_primerEnd");
  const $number = $("#pcr_number");

  const $pcrCode = $("#pcr_code");

  initSearchSelect($dna, "api_dnas_get_collection", apiUrlGenerator);

  if ($form.data("action") == "new") {
    updatePcrCode();
    $dna.on("change", updatePcrCode);
    $primerStart.on("change", updatePcrCode);
    $primerEnd.on("change", updatePcrCode);
    $number.on("keyup", updatePcrCode);
  }

  function updatePcrCode() {
    const code = generatePcrCode(
      $dna.val() ? $dna.find("option:selected").text() : undefined,
      $number.val() || undefined,
      getSelectedCode($primerStart),
      getSelectedCode($primerEnd)
    );
    $pcrCode.val(code);
    return code;
  }

  function generatePcrCode(
    dna = "{DNA code}",
    pcrNumber = "{PCR number}",
    primerStart = "{Primer start}",
    primerEnd = "{Primer end}"
  ) {
    return `${dna}_${pcrNumber}_${primerStart}_${primerEnd}`;
  }
});
