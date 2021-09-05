import { initSearchSelect } from "./field-suggestions";
import { getSelectedCode } from "./forms";

$(() => {
  const $form = $("form[name='pcr']");
  const $dna = $form.find("#pcr_dnaFk");
  const $primerStart = $("#pcr_primerStartVocFk");
  const $primerEnd = $("#pcr_primerEndVocFk");
  const $number = $("#pcr_number");

  const $pcrCode = $("#pcr_code");

  initSearchSelect($dna, "dna_search");

  if ($form.data("action") == "new") {
    updatePcrCode();
    $dna.change(updatePcrCode);
    $primerStart.change(updatePcrCode);
    $primerEnd.change(updatePcrCode);
    $numPcr.keyup(updatePcrCode);
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
