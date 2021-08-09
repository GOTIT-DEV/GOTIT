import { initSearchSelect } from "./field-suggestions";
import { getPrecisionOf } from "./date-mask";
import moment from "moment";

$(() => {
  const $form = $("form[name='sampling']");
  const $site = $("#sampling_stationFk");
  const $date_precision = $("form:first .date-precision:first");
  const $date = $("form:first .date-autoformat");

  initSearchSelect($site, "station_search");

  if ($form.data("action") == "new") {
    $site.change(updateSamplingCode);
    $date.keyup(updateSamplingCode);
    $date_precision.change(updateSamplingCode);
    updateSamplingCode();
  }

  function updateSamplingCode() {
    const code = generateSamplingCode(
      $site.val() ? $site.find("option:selected").text() : undefined,
      $date.val(),
      getPrecisionOf($date_precision.get(0))
    );
    $(".sampling-code").val(code);
  }

  function generateSamplingCode(station = "{Site}", date_str, precision) {
    let dateCode = "{Date}";
    let date = moment(date_str, "d-mm-Y");
    if (precision === 3) dateCode = "0".repeat(6);
    else if (date.isValid()) {
      if (precision === 2) dateCode = date.format("Y") + "00";
      else dateCode = date.format("Ymm");
    }
    return `${station}_${dateCode}`;
  }
});
