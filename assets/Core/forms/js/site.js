import 'leaflet/dist/leaflet.css';
import 'leaflet-fullscreen/dist/leaflet.fullscreen.css';
import 'leaflet-easybutton/src/easy-button.css';
import "~SpeciesSearch/css/leaflet-maps.less"

import "bootstrap-vue/dist/bootstrap-vue.css";



import { modalFormSubmitCallback } from "./forms"
import { initMunicipalityCodeGeneration } from "./municipality"
import Vue from "vue"
import SiteForm from "../components/SiteForm"
import { BootstrapVue } from "bootstrap-vue"
import i18n from "~SpeciesSearch/js/i18n"
import VueI18n from 'vue-i18n'

Vue.use(BootstrapVue)
Vue.use(VueI18n);
Vue.mixin({
  i18n,
  methods: {
    generateRoute(route, args) {
      return Routing.generate(route, args)
    }
  }
})

const modal_map = new Vue({
  el: "#map-modal",
  ...SiteForm
})

$(() => {

    const $latitude = $("#station_latDegDec")
    const $longitude = $("#station_longDegDec")
    const $modalBtn = $('button#station_showNearbySites')
    const $countryInput = $("#station_paysFk")
    const $municipality = $("#station_communeFk")

    $latitude
      .change(toggleProximitySitesBtn)
      .keyup(toggleProximitySitesBtn)
      .change()
    $longitude
      .change(toggleProximitySitesBtn)
      .keyup(toggleProximitySitesBtn)
      .change()

    $modalBtn.click(() => {
      modal_map.showProximityMap(
        parseFloat($latitude.val()),
        parseFloat($longitude.val())
      )
    })

    function toggleProximitySitesBtn() {
      $modalBtn.prop('disabled', !$latitude.val() | !$longitude.val())
    }

    //
    let Site = {

        init : function() {
            $( document ).on( 'change', '#station_paysFk', Site.refresh );
            $( document ).on( 'change', '#station_communeFk', Site.refresh );
            Site.refresh();
        },

        refresh : function() {

            // const $countryInput = $("#station_paysFk")
            // const $municipality = $("#station_communeFk")
            $countryInput.change(event => {
              const country = event.target.value
              fetch(Routing.generate('country_municipalities', { id: country }))
                .then(response => response.json())
                .then(json => {
                  const options = json.map(item =>
                    `<option value="${item.id}">${item.codeCommune}</option>`
                  )
                  $("#station_communeFk").empty()
                    .append(options).val('')
                    .selectpicker('refresh')
                })
            })

        }

    };

    Site.init();
            
    // Municipality modal
    const modalId = "#modal-station_newMunicipality"
    initMunicipalityCodeGeneration(modalId)

    const $modal = $(modalId)
    $modal.find("form").off("submit").submit(function (event) {
      event.preventDefault()
      modalFormSubmitCallback(event, modalCallback)
    })

    function modalCallback(_, response) {
      const $modalCountry = $modal.find("select#commune_paysFk")
      $countryInput.val($modalCountry.val()).selectpicker('refresh')
      $municipality
        .append($('<option>', {
          value: response.select_id,
          text: response.select_name
        }))
        .val(response.select_id)
        .selectpicker('refresh')

    }
})
