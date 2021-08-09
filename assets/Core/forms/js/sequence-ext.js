import { initSearchSelect } from "./field-suggestions";

$(() => {
  const $form = $("form[name='external_sequence']");
  const $sampling = $form.find("#external_sequence_samplingFk");

  initSearchSelect($sampling, "sampling_search");

  const $taxon = $("#external_sequence_taxonIdentifications_0_taxonFk");
  const $specimen = $("#external_sequence_specimenMolecularNumber");
  const $accession = $("#external_sequence_accessionNumberSqcAssExt");
  const $origin = $("#external_sequence_origineSqcAssExtVocFk");
  const $status = $("#external_sequence_statutSqcAssVocFk");

  const $assemblyCode = $("#external_sequence_codeSqcAssExt");
  const $alignmentCode = $("#external_sequence_codeSqcAssExtAlignement");

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
