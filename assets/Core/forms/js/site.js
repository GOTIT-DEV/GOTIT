import "leaflet/dist/leaflet.css";
import "leaflet-fullscreen/dist/leaflet.fullscreen.css";
import "leaflet-easybutton/src/easy-button.css";
import "~SpeciesSearch/css/leaflet-maps.less";

import "bootstrap-vue/dist/bootstrap-vue.css";

import { modalFormSubmitCallback } from "./forms";
import { initMunicipalityCodeGeneration } from "./municipality";
import Vue from "vue";
import SiteForm from "../components/SiteForm";
import { BootstrapVue } from "bootstrap-vue";
import i18n from "~Core/js/i18n";
import VueI18n from "vue-i18n";

Vue.use(BootstrapVue);
Vue.use(VueI18n);
Vue.mixin({
  i18n,
  methods: {
    generateRoute(route, args) {
      return Routing.generate(route, args);
    },
  },
});

const modal_map = new Vue({
  el: "#map-modal",
  ...SiteForm,
});

$(() => {
  const $latitude = $("#site_latDegDec");
  const $longitude = $("#site_longDegDec");
  const $modalBtn = $("button#site_showNearbySites");

  $latitude
    .change(toggleProximitySitesBtn)
    .keyup(toggleProximitySitesBtn)
    .change();
  $longitude
    .change(toggleProximitySitesBtn)
    .keyup(toggleProximitySitesBtn)
    .change();

  $modalBtn.click(() => {
    modal_map.showProximityMap(
      parseFloat($latitude.val()),
      parseFloat($longitude.val())
    );
  });

  function toggleProximitySitesBtn() {
    $modalBtn.prop("disabled", !$latitude.val() | !$longitude.val());
  }

  const $countryInput = $("#site_country");
  const $municipality = $("#site_municipality");
  $countryInput.change((event) => {
    const country = event.target.value;
    fetch(Routing.generate("country_municipalities", { id: country }))
      .then((response) => response.json())
      .then((json) => {
        const options = json.map(
          (item) => `<option value="${item.id}">${item.code}</option>`
        );
        $("#site_municipality")
          .empty()
          .append(options)
          .val("")
          .selectpicker("refresh");
      });
  });

  // Municipality modal
  const modalId = "#modal-site_newMunicipality";
  initMunicipalityCodeGeneration(modalId);

  const $modal = $(modalId);
  $modal
    .find("form")
    .off("submit")
    .submit(function (event) {
      event.preventDefault();
      modalFormSubmitCallback(event, modalCallback);
    });

  function modalCallback(_, response) {
    const $modalCountry = $modal.find("select#municipality_country");
    $countryInput.val($modalCountry.val()).selectpicker("refresh");
    $municipality
      .append(
        $("<option>", {
          value: response.select_id,
          text: response.select_name,
        })
      )
      .val(response.select_id)
      .selectpicker("refresh");
  }
});
