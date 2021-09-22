import { initSearchSelect } from "./field-suggestions";
import { getSelectedCode } from "./forms";

$(() => {
  const $form = $("form[name='chromatogram']");
  const $pcr = $form.find("#chromatogram_pcr");
  const $yas = $form.find("#chromatogram_yasNumber");
  const $primer = $form.find("#chromatogram_primer");

  const $code = $form.find("#chromatogram_code");

  initSearchSelect($pcr, "pcr_search");

  if ($form.data("action") == "new") {
    $yas.keyup(updateCode);
    $primer.change(updateCode);
    updateCode();
  }

  function updateCode() {
    const code = generateCode(
      $yas.val() || undefined,
      getSelectedCode($primer)
    );
    $code.val(code);
    return code;
  }

  function generateCode(YAS = "{#YAS}", primer = "{PRIMER}") {
    return `${YAS}|${primer}`;
  }
});
