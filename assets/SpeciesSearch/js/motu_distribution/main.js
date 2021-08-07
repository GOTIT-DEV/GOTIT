import MotuDistribution from "./MotuDistribution";
import Vue from "vue";

const app = new Vue({
  el: "#motu-distribution-vue",
  ...MotuDistribution,
});
