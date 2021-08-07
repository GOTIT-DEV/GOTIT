import Vue from "vue";
import MotuSearch from "./MotuSearch";

const app = new Vue({
  el: "#motus-vue",
  ...MotuSearch,
});
