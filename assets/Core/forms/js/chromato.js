import { initSearchSelect } from "./field-suggestions";
import { getSelectedCode } from "./forms";

$(() => {
  const $form = $("form[name='chromatogramme']");
  const $pcr = $form.find("#chromatogramme_pcrFk");
  const $yas = $form.find("#chromatogramme_numYas");
  const $primer = $form.find("#chromatogramme_primerChromatoVocFk");

  const $code = $form.find("#chromatogramme_codeChromato");

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
