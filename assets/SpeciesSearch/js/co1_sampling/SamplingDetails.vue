<template>
  <div id="sampling-details">
    <b-tabs
      v-if="item.id"
      justified
      active-nav-item-class="font-weight-bold text-primary"
      content-class="mt-3"
    >
      <b-tab active @click="scrollToMap">
        <template #title>
          <i class="fas fa-map-marker"></i>
          {{ $t("map") }}
        </template>
        <sampling-map
          ref="map"
          :sites="sites"
          :lmp-co1="item.lmp_co1"
          :lmp-biomat="item.lmp"
        />
      </b-tab>
      <b-tab>
        <template #title>
          <i class="fas fa-th-list"></i>
          {{ $t("table") }}
        </template>
        <sampling-details-table :items="sites" :taxname="item.taxon_name" />
      </b-tab>
    </b-tabs>
    <b-alert v-else show variant="secondary">
      {{ $t("empty_selection_msg") }}
    </b-alert>
  </div>
</template>

<i18n>
{
  "en": {
    "map" : "Sampling coverage map",
    "table" : "Sampling data",
    "empty_selection_msg": "Select an item in the table above to see sampling details"
  },
  "fr": {
    "map" : "Carte d'échantillonnage",
    "table" : "Données d'échantillonnage",
    "empty_selection_msg": "Sélectionnez une entrée dans la table ci-dessus pour afficher les données d'échantillonnage"
  }
}
</i18n>

<script>
import Vue from "vue";
import SamplingDetailsTable from "./SamplingDetailsTable";
import SamplingMap from "./SamplingMap";
export default {
  components: { SamplingDetailsTable, SamplingMap },
  props: {
    item: {
      type: Object,
      required: true,
    },
    sites: {
      type: Array,
      required: true,
    },
  },
  methods: {
    scrollToMap() {
      Vue.nextTick(() =>
        this.$refs.map.$el.scrollIntoView({
          behavior: "smooth",
          block: "center",
          inline: "center",
        })
      );
    },
  },
  watch: {
    item: function (newItem) {
      this.scrollToMap();
    },
  },
};
</script>

<style lang="less" scoped>
</style>