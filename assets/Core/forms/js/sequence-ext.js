import { initSearchSelect } from "./field-suggestions";

$(() => {
  const $form = $("form[name='sequenceassembleeext']");
  const $sampling = $form.find("#sequenceassembleeext_collecteFk");

  initSearchSelect($sampling, "collecte_search");

  const $taxon = $(
    "#sequenceassembleeext_taxonIdentifications_0_referentielTaxonFk"
  );
  const $specimen = $("#sequenceassembleeext_specimenMolecularNumber");
  const $accession = $("#sequenceassembleeext_accessionNumberSqcAssExt");
  const $origin = $("#sequenceassembleeext_origineSqcAssExtVocFk");
  const $status = $("#sequenceassembleeext_statutSqcAssVocFk");

  const $assemblyCode = $("#sequenceassembleeext_codeSqcAssExt");
  const $alignmentCode = $("#sequenceassembleeext_codeSqcAssExtAlignement");

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
