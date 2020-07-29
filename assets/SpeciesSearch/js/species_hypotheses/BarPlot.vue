<template>
  <Plotly
    :data="data"
    :layout="layout"
    :display-mode-bar="false"
    scrollZoom="false"
    displaylogo="false"
    modeBarButtonsToRemove="[
          'sendDataToCloud', 'box', 'lasso2d', 'select2d', 'pan2d',
          'zoom2d', 'zoomIn2d', 'zoomOut2d', 'autoScale2d', 'resetScale2d'
        ]"
  ></Plotly>
</template>

<script>
import { Plotly } from "vue-plotly";
import {layout} from "./barplot-config"
export default {
  components: { Plotly },
  data() {
    return {
      data: [],
      layout: layout,
    };
  },
  methods: {
    setData(json) {
      let data = {
        match: [],
        split: [],
        lump: [],
        reshuffling: [],
        methode: [],
      };
      json.reduce((currentData, row) => {
        currentData.match.push(row.match);
        currentData.split.push(row.split);
        currentData.lump.push(row.lump);
        currentData.reshuffling.push(row.reshuffling);
        currentData.methode.push(row.methode);
        return currentData;
      }, data);

      this.data = Object.keys(data)
        .filter((key) => {
          return key != "methode";
        })
        .map((key) => {
          return {
            x: data.methode, // method label
            y: data[key], // counts
            name: key, // counters.key
            type: "bar",
          };
        });
    },
  },
};
</script>

<style lang="less" scoped>
</style>