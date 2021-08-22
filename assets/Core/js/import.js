import Vue from "vue";
import ImportCsvForm from "~Components/ImportCsvForm";
import DnaImport from "~Components/entity/dna/DnaImport";

const mountEl = document.querySelector("#csv-form");

const component = mountEl.dataset.component;
Vue.component("DnaImport", DnaImport);
new Vue({
  el: "#csv-form",
  ...component,
  render: (createElement) => {
    const context = {
      props: { ...mountEl.dataset },
    };
    return createElement(component, context);
  },
});
