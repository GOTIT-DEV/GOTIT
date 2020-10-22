const typeahead = {
  get provider() {
    const geneElement = this.geneElement
    return new Bloodhound({
      datumTokenizer: Bloodhound.tokenizers.whitespace,
      queryTokenizer: Bloodhound.tokenizers.whitespace,
      remote: {
        url: Routing.generate("individu_search_with_gene", {
          'query': "QUERY",
          'gene': "GENE_ID"
        }),
        wildcard: 'QUERY',
        replace(url, query) {
          return url
            .replace("QUERY", query)
            .replace("GENE_ID", geneElement.val() || -1)
        }
      }
    })
  },

  selection: { id: null, code: '' },

  geneElement: null,
  element: null, // typeahead input element

  init(selector, geneSelector) {
    this.element = $(selector)
    this.geneElement = $(geneSelector)
    const specimenProvider = this.provider

    this.geneElement.change(event => {
      this.element.prop('disabled', !event.target.value).val('')
    }).change()

    this.element
      .typeahead(
        {
          hint: true,
          highlight: true, // Enable substring highlighting 
          minLength: 1 // Specify minimum characters required for showing result
        },
        {
          name: 'individus',
          source: specimenProvider,
          displayKey: "code",
          limit: 40
        })
      .bind('typeahead:select', this.setSpecimen.bind(this))
      .bind('typeahead:autocomplete', this.setSpecimen.bind(this))
      .bind('typeahead:close', this.updateSpecimenInputs.bind(this))
  },

  updateSpecimenInputs() {
    this.element.val(this.selection.code)
    document.getElementById(this.element.data('target-id'))
      .value = this.selection.id
  },

  setSpecimen(event, item) {
    this.selection = item
    this.updateSpecimenInputs()
  }
}

if ($('form[name="gene_specimen_form"]').data('action') != 'show')
  typeahead.init('.typeahead-individu', '#gene_specimen_form_geneVocFk')


// Sequence code generation ----------

const sequenceForm = document.querySelector("form[name='sequence_assemblee']")

if (sequenceForm) {
  const action = sequenceForm.dataset.action

  if (action == 'new') {
    updateSequenceCode()
    $('#sequence_assemblee_statutSqcAssVocFk').change(updateSequenceCode)
    $("#wrapper_sequence_assemblee_estAligneEtTraites").change(_ => {
      $("#wrapper_sequence_assemblee_estAligneEtTraites select")
        .change(updateSequenceCode)
      updateSequenceCode()
    }).change()
  }
}


function generateSequenceCode(
  status = '{{STATUS}}',
  specimenCode = '{{SPECIMEN}}',
  chromatoCode = '{{CHROMATO}}'
) {
  return status.includes('VALID')
    ? `${specimenCode}_${chromatoCode}`
    : `${status}_${specimenCode}_${chromatoCode}`
}

function updateSequenceCode() {
  const specimen = $("#gene_specimen_form_individuTypeahead").val()
  const statusInput = document
    .getElementById("sequence_assemblee_statutSqcAssVocFk")
  const status = statusInput.options[statusInput.selectedIndex].text
  const chromatos = document
    .getElementById("wrapper_sequence_assemblee_estAligneEtTraites")
    .querySelectorAll("select")
  const chromatoCodes = Array.from(chromatos)
    .map(c => c.options[c.selectedIndex].text)
    .join('-')

  const code = generateSequenceCode(
    status || undefined,
    specimen || undefined,
    chromatoCodes || undefined
  )

  $("#sequence_assemblee_codeSqcAss").val(code)
}