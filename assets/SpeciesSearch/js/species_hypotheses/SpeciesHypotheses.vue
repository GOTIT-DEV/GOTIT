<template>
  <div>
    <species-hypotheses-form
      @update:results="results = $event"
      @update:reversed="reversed = $event"
    />
    <hr />
    <h3>{{ $t("queries.label.resultats") }}</h3>
    <BarPlot :results="results[reversed ? 'verso' : 'recto']" />
    <b-data-table
      :fields="fields"
      :items="results[reversed ? 'verso' : 'recto']"
      exportFilename="species_hypotheses.csv"
    >
    </b-data-table>
  </div>
</template>

<script>
import SpeciesHypothesesForm from "./SpeciesHypothesesForm";
import BDataTable from "~Components/BDataTable";
import BarPlot from "./BarPlot";

export default {
  components: {
    SpeciesHypothesesForm,
    BDataTable,
    BarPlot,
  },
  data() {
    return {
      side: "recto",
      reversed: false,
      results: {
        recto: [],
        verso: [],
      },
      fields: [
        {
          key: "methode",
          label: this.$t("queries.columns.method"),
        },
        {
          key: "motu_title",
          label: this.$t("queries.columns.dataset"),
        },
        {
          key: "match",
        },
        {
          key: "split",
        },
        {
          key: "lump",
        },
        {
          key: "reshuffling",
        },
        {
          key: "nb_seq",
          label: this.$t("queries.columns.nbseq"),
        },
        {
          key: "nb_sta",
          label: this.$t("queries.columns.nbsta"),
        },
      ],
    };
  },
};
</script>

<style lang="less" scoped>
</style>