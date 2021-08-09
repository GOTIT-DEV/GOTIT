import { initSearchSelect } from "./field-suggestions";

$(() => {
  const $form = $("form[name='external_sequence']");
  const $sampling = $form.find("#external_sequence_samplingFk");

  initSearchSelect($sampling, "sampling_search");

  const $taxon = $("#external_sequence_taxonIdentifications_0_taxonFk");
  const $specimen = $("#external_sequence_specimenMolecularNumber");
  const $accession = $("#external_sequence_accessionNumber");
  const $origin = $("#external_sequence_originVocFk");
  const $status = $("#external_sequence_status");

  const $assemblyCode = $("#external_sequence_code");
  const $alignmentCode = $("#external_sequence_alignmentCode");

  if ($form.data("action") == "new") {
    [$taxon, $specimen, $accession, $origin, $status, $sampling].forEach((el) =>
      el.change(updateSequenceCode)
    );
    updateSequenceCode();
  }
  function updateSequenceCode() {
    const code = generateSequenceCode(
      $status.val() ? $status.find("option:selected").text() : undefined,
      $taxon.val() ? $taxon.find("option:selected").text() : undefined,
      $sampling.val() ? $sampling.find("option:selected").text() : undefined,
      $specimen.val() || undefined,
      $accession.val() || undefined,
      $origin.val() ? $origin.find("option:selected").text() : undefined
    );
    $assemblyCode.val(code);
    $alignmentCode.val(code);
    return code;
  }

  function generateSequenceCode(
    status = "{Status}",
    taxon = "{Taxon}",
    sampling = "{Sampling}",
    specimen = "{Specimen #}",
    accession = "{Acc #}",
    origin = "{Origin}"
  ) {
    const code = `${taxon}_${sampling}_${specimen}_${accession}|${origin}`;
    return status.substr(0, 5) === "VALID" ? code : `${status}_${code}`;
  }
});
