import { initSearchSelect } from "./field-suggestions";
import { getSelectedCode } from "./forms";

$(() => {
  const $form = $("form[name='chromatogram']");
  const $pcr = $form.find("#chromatogram_pcrFk");
  const $yas = $form.find("#chromatogram_numYas");
  const $primer = $form.find("#chromatogram_primerChromatoVocFk");

  const $code = $form.find("#chromatogram_codeChromato");

  initSearchSelect($pcr, "pcr_search");

  if ($form.data("action") == "new") {
    $yas.keyup(updateCodeChromato);
    $primer.change(updateCodeChromato);
    updateCodeChromato();
  }

  function updateCodeChromato() {
    const code = generateCodeChromato(
      $yas.val() || undefined,
      getSelectedCode($primer)
    );
    $code.val(code);
    return code;
  }

  function generateCodeChromato(YAS = "{#YAS}", primer = "{PRIMER}") {
    return `${YAS}|${primer}`;
  }
});
