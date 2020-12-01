<template>
  <plotly
    :data="data"
    :layout="layout"
    :scrollZoom="false"
    :displaylogo="false"
    :modeBarButtonsToRemove="[
      'sendDataToCloud',
      'box',
      'lasso2d',
      'select2d',
      'pan2d',
      'zoom2d',
      'zoomIn2d',
      'zoomOut2d',
      'autoScale2d',
      'resetScale2d',
    ]"
  />
</template>

<i18n>
{
  "en": {
    "ylabel": "Correspondences"
  },
  "fr": {
    "ylabel": "Correspondances"
  }
}
</i18n>

<script>
import { Plotly } from "vue-plotly";
// import VuePlotly from "./Plotly";

const axisConfig = {
  titlefont: {
    family: "sans serif",
    size: 18,
    color: "#7f7f7f",
  },
  fixedrange: true,
};

export default {
  components: { Plotly },
  props: {
    results: {
      type: Array,
      required: true,
    },
  },
  computed: {
    data() {
      const data = this.results.reduce(
        (currentData, row) => {
          currentData.match.push(row.match);
          currentData.split.push(row.split);
          currentData.lump.push(row.lump);
          currentData.reshuffling.push(row.reshuffling);
          currentData.methode.push(row.methode);
          return currentData;
        },
        {
          match: [],
          split: [],
          lump: [],
          reshuffling: [],
          methode: [],
        }
      );

      return Object.keys(data)
        .filter((key) => key != "methode")
        .map((key) => {
          return {
            x: data.methode, // method label
            y: data[key], // counts
            name: key, // counters.key
            type: "bar",
            textposition: "auto",
          };
        });
    },
  },
  data() {
    return {
      layout: {
        height: 300,
        xaxis: {
          title: this.$t("queries.methode.label"),
          ...axisConfig,
        },
        yaxis: {
          title: this.$t("ylabel"),
          ...axisConfig,
        },
        margin: { t: 5 },
        font: { family: "sans serif", size: 14 },
        showlegend: true,
        legend: { x: 1, y: 0.5 },
        barmode: "group",
      },
    };
  },
};
</script>

<style lang="less" scoped>
</style>