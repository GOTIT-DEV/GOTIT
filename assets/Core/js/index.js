import "bootstrap-vue/dist/bootstrap-vue.css";
import "bootstrap-select/dist/css/bootstrap-select.css";
import "~SpeciesSearch/css/multiselect.less";

import Vue from "vue";
import { BootstrapVue } from "bootstrap-vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

import filters from "~SpeciesSearch/js/vue-filters";
import i18n from "~Core/js/i18n";

import moment from "moment";
moment.locale(Translator.locale);

Vue.component("FontAwesomeIcon", FontAwesomeIcon);

Vue.use(BootstrapVue);
Vue.mixin({
  i18n,
  methods: {
    generateRoute(route, args) {
      return Routing.generate(route, args);
    },
  },
});

Object.entries(filters).forEach(([key, filter]) => {
  Vue.filter(key, filter);
});
