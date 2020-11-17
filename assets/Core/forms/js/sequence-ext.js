import { initSearchSelect } from "./field-suggestions"

$(() => {

  const $form = $("form[name='bbees_e3sbundle_sequenceassembleeext']")
  const $sampling = $form.find("#bbees_e3sbundle_sequenceassembleeext_collecteFk")

  initSearchSelect($sampling, "collecte_search")

  const $taxon = $("#bbees_e3sbundle_sequenceassembleeext_especeIdentifiees_0_referentielTaxonFk")
  const $specimen = $("#bbees_e3sbundle_sequenceassembleeext_numIndividuSqcAssExt")
  const $accession = $("#bbees_e3sbundle_sequenceassembleeext_accessionNumberSqcAssExt")
  const $origin = $("#bbees_e3sbundle_sequenceassembleeext_origineSqcAssExtVocFk")
  const $status = $("#bbees_e3sbundle_sequenceassembleeext_statutSqcAssVocFk")

  const $assemblyCode = $("#bbees_e3sbundle_sequenceassembleeext_codeSqcAssExt")
  const $alignmentCode = $("#bbees_e3sbundle_sequenceassembleeext_codeSqcAssExtAlignement")

  if ($form.data('action') == 'new') {
    [$taxon, $specimen, $accession, $origin, $status, $sampling]
      .forEach(el => el.change(updateSequenceCode))
    updateSequenceCode()

  }
  function updateSequenceCode() {
    const code = generateSequenceCode(
      $status.val() ? $status.find('option:selected').text() : undefined,
      $taxon.val() ? $taxon.find('option:selected').text() : undefined,
      $sampling.val() ? $sampling.find('option:selected').text() : undefined,
      $specimen.val() || undefined,
      $accession.val() || undefined,
      $origin.val() ? $origin.find('option:selected') : undefined
    )
    $assemblyCode.val(code)
    $alignmentCode.val(code)
    return code
  }

  function generateSequenceCode(
    status = '{Status}',
    taxon = '{Taxon}',
    sampling = '{Sampling}',
    specimen = '{Specimen #}',
    accession = '{Acc #}',
    origin = '{Origin}'
  ) {
    const code = `${taxon}_${sampling}_${specimen}_${accession}|${origin}`
    return status.substr(0, 5) === 'VALID' ? code : `${status}_${code}`
  }
})