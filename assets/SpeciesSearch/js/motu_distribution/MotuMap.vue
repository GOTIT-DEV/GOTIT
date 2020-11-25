<template>
  <div class="map-container">
    <leaflet-map
      :data="data"
      :markerSettings="markerSettings"
      :addSliders="sliders"
      ref="map"
    >
      <template v-slot:controls>
        <l-control position="topleft" class="leaflet-control leaflet-bar">
          <button
            class="btn btn-sm btn-light btn-map-control"
            :title="$t('show_motus')"
            @click="filterMotuDisplay(null)"
          >
            <a class="fas fa-eye"></a>
          </button>
        </l-control>
      </template>

      <l-layer-group
        v-for="(motu, motu_id, index) in indexedData"
        :key="motu_id"
        :visible.sync="motu.visible"
        layerType="overlay"
        :name="layerName(motu_id, index)"
      >
        <shape-marker
          v-for="(site, site_id) in motu.sites"
          :key="site_id"
          :lat-lng="[site.latitude, site.longitude]"
          :radius="markerRadius(site.sequences.length)"
          :opacity="markerSettings.opacity"
          :fillOpacity="markerSettings.opacity"
          v-bind="markerStyle(index)"
        >
          <site-popup
            :site="site"
            :options="{ permanent: true }"
            @show-seq-modal="showSequences(site)"
            @filter-display="filterMotuDisplay($event)"
            @fit-motu="fitMotu($event)"
          />
        </shape-marker>
      </l-layer-group>
    </leaflet-map>

    <sequence-modal ref="seqModal" />
  </div>
</template>

<i18n>
{
  "fr": {
    "show_motus": "Afficher tout"
  },
  "en": {
    "show_motus": "Show all"
  }
}
</i18n>

<script>
import Vue from "vue";
import L from "leaflet";
import chroma from "chroma-js";
import { LControl, LLayerGroup } from "vue2-leaflet";

import LeafletMap from "../../../components/LeafletMap";

import SequenceModal from "./SequenceModal";
import ShapeMarker from "../../../components/ShapeMarker";
import ShapeMarkerLegend from "../../../components/ShapeMarkerLegend";
import SitePopup from "./SitePopup.vue";

const LegendShape = Vue.extend(ShapeMarkerLegend);

export default {
  name: "MotuDistributionMap",
  components: {
    LControl,
    LeafletMap,
    ShapeMarker,
    LLayerGroup,
    SequenceModal,
    ShapeMarker,
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
    layerName(motu_id, index) {
      const shape = this.markerShape(index);
      const color = this.markerColor(index);
      const legendShape = new LegendShape({
        inheritAttrs: false,
        propsData: {
          label: `Motu ${motu_id}`,
          shape: shape,
          markerStyle:
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
                },
        },
      });
      legendShape.$mount();
      return legendShape.$el.outerHTML;
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
    showSequences(station) {
      this.$refs.seqModal.station = station;
      this.$refs.seqModal.$bvModal.show("modal-sequences");
    },
    fitMotu(motu) {
      const sites = Object.values(this.indexedData[motu].sites);
      this.mapObject.closePopup();
      this.$refs.map.fitBounds(Array.from(sites), 0.1);
    },
    organizeByMotu(data) {
      function reduceOrganizer(motuDict, item) {
        item.station_url = Routing.generate("station_show", {
          id: item.id_sta,
          _locale: Translator.locale,
        });

        item.seq_url = Routing.generate(
          item.seq_type
            ? "sequenceassembleeext_show"
            : "sequenceassemblee_show",
          { id: item.id, _locale: Translator.locale }
        );

        if (item.altitude === null) item.altitude = "-";

        if (!(item.motu in motuDict))
          motuDict[item.motu] = {
            visible: true,
            sites: {},
          };

        if (!(item.id_sta in motuDict[item.motu]["sites"]))
          motuDict[item.motu]["sites"][item.id_sta] = {
            sequences: [],
            latitude: parseFloat(item.latitude),
            longitude: parseFloat(item.longitude),
            ...item,
          };

        delete item.sequences;
        motuDict[item.motu]["sites"][item.id_sta].sequences.push(item);
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
  height: 80vh;
}
</style>