import "select2";

export function defaultUrlGenerator(route) {
  return (params) => Routing.generate(route, { q: params.term });
}

export function apiUrlGenerator(route) {
  return (params) => Routing.generate(route, { filter: { code: params.term } });
}

export function resultsToItems(data) {
  return {
    results: data.map(({ id, code }) => {
      return { id, text: code };
    }),
  };
}

export function paginatedResultsToItems(data) {
  return resultsToItems(data.items);
}

export function initSearchSelect(
  $element,
  route,
  urlGenerator = defaultUrlGenerator,
  processResults = resultsToItems
) {
  $element.select2({
    theme: "bootstrap4",
    ajax: {
      delay: 250,
      url: urlGenerator(route),
      dataType: "json",
      processResults,
    },
  });
}
