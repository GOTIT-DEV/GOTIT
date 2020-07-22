import { getPrecisionOf } from "./date-mask"
import moment from "moment"

const urlParams = new URLSearchParams(window.location.search);

$(() => {
  if ($("form :first").data('action') === "new" &&
    urlParams.get('idFk') > 0) {
    $(".typeahead-station").prop('readonly', true)
  }

  $("form:first .typeahead-station.tt-input").change('typeahead:change', updateSamplingCode)
  $("form:first .date-autoformat").keyup(updateSamplingCode)
  $("form:first .date-precision").change(updateSamplingCode)
  updateSamplingCode()
})




function updateSamplingCode() {
  const station = document.querySelector(".typeahead-station.tt-input").value
  const precision = getPrecisionOf($("form:first .date-precision:first").get(0))
  const date_str = $("form:first .date-autoformat").val()

  $('.sampling-code').val(generateSamplingCode(station, date_str, precision))
}

function generateSamplingCode(station, date_str, precision) {
  let dateCode = '{{DATE}}'
  if (station == '')
    station = '{{STATION}}'
  let date = moment(date_str, 'd-mm-Y')
  if (precision === 3)
    dateCode = '0'.repeat(6)
  else if (date.isValid()) {
    if (precision === 2)
      dateCode = date.format('Y') + '00'
    else
      dateCode = date.format('Ymm')
  }
  return `${station}_${dateCode}`
}