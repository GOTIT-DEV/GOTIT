import { initSearchSelect } from "./field-suggestions";
import { getSelectedCode } from "./forms";

$(() => {
  const $form = $("form[name='pcr']");
  const $dna = $form.find("#pcr_adnFk");
  const $primerStart = $("#pcr_primerPcrStartVocFk");
  const $primerEnd = $("#pcr_primerPcrEndVocFk");
  const $numPcr = $("#pcr_numPcr");

  const $pcrCode = $("#pcr_codePcr");

  initSearchSelect($dna, "adn_search");

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
      $numPcr.val() || undefined,
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
