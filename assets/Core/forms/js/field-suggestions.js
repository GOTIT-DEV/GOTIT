import 'select2'

export function initSearchSelect(
  $element,
  route,
  urlGenerator = function (route) {
    return params => Routing.generate(route, { q: params.term })
  }
) {
  $element.select2({
    theme: 'bootstrap4',
    ajax: {
      delay: 250,
      url: urlGenerator(route),
      dataType: 'json',
      processResults: data => {
        return {
          results: data.map(({ id, code }) => {
            return { id, text: code }
          })
        }
      }
    }
  })
}