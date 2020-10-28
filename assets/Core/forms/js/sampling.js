import { getPrecisionOf } from "./date-mask"
import moment from "moment"




const urlParams = new URLSearchParams(window.location.search);

$(() => {
  if (
    $("form :first").data('action') === "new" &&
    urlParams.get('idFk') > 0
  ) {
    $(".typeahead-station").prop('readonly', true)
  }

  $("form:first .typeahead-station.tt-input").change(updateSamplingCode)
  $("form:first .date-autoformat").keyup(updateSamplingCode)
  $("form:first .date-precision").change(updateSamplingCode)


  const stations = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.whitespace,
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
      url: Routing.generate("station_search", { 'q': "QUERY" }),
      wildcard: 'QUERY'
    }
  });
  // Initializing the typeahead fields
  var selectedTypeahead = 0; /* flag to know if there is a Typeahead selected value  */

  $('.typeahead-station').typeahead({
    hint: true, highlight: false, /* Enable substring highlighting */
    minLength: 1 /* Specify minimum characters required for showing result */
  }, {
    name: 'stations',
    source: stations,
    displayKey: "code",
    limit: 40
  }).on('keyup', this, function (event) {
    selectedTypeahead = 0;
  }).bind('typeahead:select', function (ev, item) {
    var $this = $(this);
    $('#' + $this.attr('data-target_id')).val(item.id);
    let $station = $this.parents('.form-group').find('input.bbees_e3sbundle_collecte[stationId]');
    if ($station.length === 1) {
      $station.val(item.id);
    }
    selectedTypeahead = 1;
  }).bind('typeahead:close', function (ev, item) {
    if (selectedTypeahead == 0) { /* if there is no Typeahead selected value fields are reinitialized */
      $('input.typeahead.tt-input').val($('input.typeahead').data('initial'))
      $('#bbees_e3sbundle_collecte_stationId').val($('#bbees_e3sbundle_collecte_stationId').data('initial'));
    }
    var $this = $(this);
    if ($.trim($this.val()) === '') {
      $this.val('');
      let $station = $this.parents('.form-group').find('input.bbees_e3sbundle_collecte[stationId]');
      if ($station.length === 1) {
        $station.val('');
      }
    }
  });

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