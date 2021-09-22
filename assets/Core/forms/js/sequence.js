import { initSearchSelect } from "./field-suggestions";

$(() => {
  // Gene specimen form
  const $geneSpecimenForm = $('form[name="gene_specimen_form"]');
  const $gene = $geneSpecimenForm.find("#gene_specimen_form_gene");
  const $specimen = $geneSpecimenForm.find("#gene_specimen_form_specimen");

  initSearchSelect($specimen, "specimen_search_with_gene", function (route) {
    return (params) =>
      Routing.generate(route, {
        query: params.term,
        gene: $gene.val() || -1,
      });
  });

  $gene.change((ev) => $specimen.val("").change());

  // Sequence code generation ----------

  const $sequenceForm = $("form[name='internal_sequence']");

  if ($sequenceForm.length) {
    const $status = $("#internal_sequence_status");
    const chromatoWrapper = document.getElementById(
      "wrapper_internal_sequence_assemblies"
    );
    const $code = $("#internal_sequence_code");

    if ($sequenceForm.data("action") == "new") {
      $status.change(updateSequenceCode);
      $(chromatoWrapper).change((_) => {
        $(chromatoWrapper).find("select").change(updateSequenceCode);
        updateSequenceCode();
      });
      updateSequenceCode();
    }

    function generateSequenceCode(
      status = "{STATUS}",
      specimenCode = "{SPECIMEN}",
      chromatoCode = "{CHROMATO}"
    ) {
      return status.substr(0, 5) === "VALID"
        ? `${specimenCode}_${chromatoCode}`
        : `${status}_${specimenCode}_${chromatoCode}`;
    }

    function getChromatoCodes(chromatoWrapper) {
      const chromatos = chromatoWrapper.querySelectorAll("select");
      return Array.from(chromatos)
        .map((c) => c.options[c.selectedIndex].text)
        .join("-");
    }

    function updateSequenceCode() {
      const code = generateSequenceCode(
        $status.val() ? $status.find("option:selected").text() : undefined,
        $specimen.val() ? $specimen.find("option:selected").text() : undefined,
        getChromatoCodes(chromatoWrapper) || undefined
      );

      $code.val(code);
      return code;
    }
  }
});
