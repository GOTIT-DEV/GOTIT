<template>
  <div class="map-container">
    <leaflet-map
      ref="map"
      :data="data"
      :marker-settings="markerSettings"
      :add-sliders="sliders"
    >
      <template #controls>
        <l-control position="topleft" class="leaflet-control leaflet-bar">
          <button
            class="btn btn-sm btn-light btn-map-control"
            :title="$t('show_motus')"
            @click="filterMotuDisplay(null)"
          >
            <a class="fas fa-eye" />
          </button>
        </l-control>
      </template>

      <l-layer-group
        v-for="(motu, motu_id, index) in indexedData"
        :key="motu_id"
        :visible.sync="motu.visible"
        layer-type="overlay"
        :name="layerName(motu_id, index)"
      >
        <shape-marker
          v-for="(site, site_id) in motu.sites"
          :key="site_id"
          :lat-lng="[site.latitude, site.longitude]"
          :radius="markerRadius(site.sequences.length)"
          :opacity="markerSettings.opacity"
          :fill-opacity="markerSettings.opacity"
          v-bind="markerStyle(index)"
        >
          <site-popup
            :site="site"
            @show-seq-modal="showSequences(site)"
            @filter-display="filterMotuDisplay($event)"
            @fit-motu="fitMotu($event)"
          />
        </shape-marker>
      </l-layer-group>
    </leaflet-map>

    <sequence-modal ref="seqModal" static />
  </div>
</template>

<i18n>
{
  "fr": {
    "show_motus": "Afficher tout",
    "show_seqs": "Lister séquences",
    "filter_motu" : "Filtrer MOTU",
    "fit_motu" : "Cadrer MOTU"
  },
  "en": {
    "show_motus": "Show all",
    "show_seqs": "Show sequences",
    "filter_motu" : "Filter MOTU",
    "fit_motu" : "Fit view to MOTU"
  }
}
</i18n>

<script>
import chroma from "chroma-js";
import { LControl, LLayerGroup } from "vue2-leaflet";

import LeafletMap from "~Components/maps/LeafletMap";
import ShapeMarker from "~Components/maps/ShapeMarker";
import { generateLegend } from "~Components/maps/ShapeMarkerLegend.vue";

import SequenceModal from "./SequenceModal";
import SitePopup from "./SitePopup.vue";

export default {
  name: "MotuDistributionMap",
  components: {
    LControl,
    LeafletMap,
    ShapeMarker,
    LLayerGroup,
    SequenceModal,
    SitePopup,
  },
  props: {
    data: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      indexedData: {},
      colorBrewer: chroma.brewer.Set1,
      shapes: ["circle", "triangle", "square", "diamond"],
      markerSettings: {
        radius: 6,
        opacity: 1,
        grow: 1,
      },
      sliders: {
        grow: {
          label: "Grow",
          min: 1,
          max: 5,
          interval: 0.5,
          adsorb: true,
          tooltipFormatter: (val) => `×${val}`,
        },
      },
    };
  },
  computed: {
    mapObject() {
      return this.$refs.map.mapObject;
    },
    nShapes() {
      return this.shapes.length;
    },
    nColors() {
      return this.colorBrewer.length;
    },
    nScale() {
      return this.nShapes * this.nColors;
    },
    maxGrowth() {
      function maxLengthReducer(acc, motu_data) {
        return Math.max(
          acc,
          ...Array.from(Object.values(motu_data.sites)).map(
            (station) => station.sequences.length
          )
        );
      }
      const motus = Object.values(this.indexedData);
      return Array.from(motus).reduce(maxLengthReducer, 0);
    },
  },
  watch: {
    data: function (newData, _) {
      this.indexedData = this.organizeByMotu(newData);
    },
  },
  methods: {
    trimSite({
      site_code,
      municipality,
      country,
      motu,
      latitude,
      longitude,
      altitude,
    }) {
      return {
        site_code,
        municipality,
        country,
        motu,
        latitude,
        longitude,
        altitude,
      };
    },
    layerName(motu_id, index) {
      const shape = this.markerShape(index);
      const color = this.markerColor(index);
      const markerStyle =
        index < this.nScale
          ? {
              stroke: "black",
              fill: color,
              "stroke-width": 1,
            }
          : {
              stroke: color,
              fill: "transparent",
              "stroke-width": 3,
            };
      return generateLegend(`Motu ${motu_id}`, shape, markerStyle);
    },
    markerShape(layerIndex) {
      return this.shapes[layerIndex % this.nShapes];
    },
    markerColor(layerIndex) {
      return this.colorBrewer[Math.floor(layerIndex / this.nShapes)];
    },
    markerRadius(scale) {
      const grow = this.markerSettings.grow;
      return (
        this.markerSettings.radius * (1 + (scale / this.maxGrowth) * (grow - 1))
      );
    },
    markerStyle(layerIndex) {
      const color = this.markerColor(layerIndex);
      const shape = this.markerShape(layerIndex);
      return layerIndex < this.nScale
        ? {
            color: "black",
            shape,
            weight: 1,
            fill: true,
            fillColor: color,
          }
        : {
            color,
            shape,
            weight: 3,
            fill: false,
          };
    },
    filterMotuDisplay(motuFilter) {
      Object.entries(this.indexedData).forEach(([motu, motuData]) => {
        motuData.visible = motu == motuFilter || motuFilter === null;
      });
    },
    showSequences(site) {
      this.$refs.seqModal.$bvModal.show("modal-sequences");
      this.$refs.seqModal.site = site;
    },
    fitMotu(motu) {
      const sites = Object.values(this.indexedData[motu].sites);
      this.mapObject.closePopup();
      this.$refs.map.fitBounds(Array.from(sites), 0.1);
    },
    organizeByMotu(data) {
      function reduceOrganizer(motuDict, item) {
        if (!(item.motu in motuDict))
          motuDict[item.motu] = {
            visible: true,
            sites: {},
          };

        if (!(item.site_id in motuDict[item.motu]["sites"]))
          motuDict[item.motu]["sites"][item.site_id] = {
            sequences: [],
            latitude: parseFloat(item.latitude),
            longitude: parseFloat(item.longitude),
            ...item,
          };

        delete item.sequences;
        motuDict[item.motu]["sites"][item.site_id].sequences.push(item);
        return motuDict;
      }
      return data.reduce(reduceOrganizer, {});
    },
  },
};
</script>

<style lang="less" scoped>
.map-container {
  width: 100%;
  height: 85vh;
}
</style>
