<template>
  <div class="map-container">
    <l-map
      ref="map"
      :zoom.sync="zoom"
      :center.sync="center"
      :options="mapOptions"
      :min-zoom="minZoom"
      :max-zoom="maxZoom"
      :max-bounds="maxBounds"
      :bounds.sync="bounds"
      @update:zoom="onZoomUpdate"
    >
      <l-control-fullscreen
        position="topleft"
        :options="{ title: { false: $t('fullscreen'), true: $t('windowed') } }"
      />

      <l-control-layers position="topright" />

      <l-control position="topleft" class="leaflet-control leaflet-bar">
        <button
          class="btn btn-sm btn-light btn-map-control"
          @click="fitBounds(data)"
        >
          <a class="fas fa-crosshairs fa-1x" />
        </button>
      </l-control>

      <slot name="controls" />

      <leaflet-map-settings
        position="bottomright"
        :sliders="settingSliders"
        :settings.sync="markerSettings"
      />

      <l-tile-layer
        v-bind="tileProviders[0]"
        :subdomains="['server', 'services']"
      />
      <l-tile-layer
        v-bind="tileProviders[1]"
        layer-type="overlay"
        :subdomains="['server', 'services']"
      />

      <slot />
    </l-map>
  </div>
</template>

<i18n>
{
  "fr": {
    "show_motus": "Afficher tout",
    "fit_bounds": "Cadrer la vue sur le contenu",
    "fullscreen": "Plein écran",
    "windowed": "Fenêtré"
  },
  "en": {
    "show_motus": "Show all",
    "fit_bounds": "Fit view to content",
    "fullscreen": "Fullscreen",
    "windowed": "Windowed"
  }
}
</i18n>

<script>
import L from "leaflet";

delete L.Icon.Default.prototype._getIconUrl;
import icon from "leaflet/dist/images/marker-icon.png";
import iconShadow from "leaflet/dist/images/marker-shadow.png";
L.Icon.Default.mergeOptions({
  iconUrl: icon,
  shadowUrl: iconShadow,
});

import { LMap, LTileLayer, LControl, LControlLayers } from "vue2-leaflet";
import LControlFullscreen from "vue2-leaflet-fullscreen";
import { Util } from "esri-leaflet";
import "leaflet-gesture-handling";

import LeafletMapSettings from "./LeafletMapSettings";
import "leaflet-gesture-handling/dist/leaflet-gesture-handling.css";

import Vue from "vue";
import ShapeMarkerLegend from "./ShapeMarkerLegend";
const LegendShape = Vue.extend(ShapeMarkerLegend);

export default {
  name: "LeafletMap",
  components: {
    LMap,
    LTileLayer,
    LControl,
    LControlFullscreen,
    LControlLayers,
    LeafletMapSettings,
  },
  props: {
    data: {
      type: Array,
      required: true,
    },
    markerSettings: {
      type: Object,
      default: () => {
        return {
          radius: 6,
          opacity: 1,
        };
      },
    },
    addSliders: {
      type: Object,
      default: () => ({}),
    },
    minZoom: {
      type: Number,
      default: 2,
    },
    maxZoom: {
      type: Number,
      default: 12,
    },
    regions: {
      type: Boolean,
      default: false,
    },
    disableAutoFit: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      tileProviders: [
        {
          name: "Base Layer",
          visible: true,
          opacity: 0.9,
          attribution: "",
          attributionUrl: "https://static.arcgis.com/attribution/World_Imagery",
          url: "https://{s}.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
        },
        {
          name: "Regions",
          visible: this.regions,
          opacity: 0.75,
          attribution: "",
          url: "https://{s}.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}",
        },
      ],
      zoom: 1,
      center: [0, 0],
      bounds: L.latLngBounds(L.latLng(90, -360), L.latLng(-90, 360)),
      maxBounds: L.latLngBounds(L.latLng(90, -360), L.latLng(-90, 360)),
      mapOptions: {
        // center: [0, 0],
        // zoom: 10,
        gestureHandling: true,
        worldCopyJump: true,
        wheelPxPerZoomLevel: 100,
        zoomSnap: 0.5,
      },
    };
  },
  computed: {
    mapObject() {
      return this.$refs.map.mapObject;
    },
    settingSliders() {
      return {
        ...this.addSliders,
        ...LeafletMapSettings.defaultSliders,
      };
    },
  },
  watch: {
    data: function (newData, _) {
      if (newData && !this.disableAutoFit) this.fitBounds(newData);
    },
  },
  mounted() {
    Util.setEsriAttribution(this.mapObject);
    this._getAttributionData(this.tileProviders[0].attributionUrl);
    let attr = this._updateMapAttribution();
  },
  methods: {
    onZoomUpdate(zoom) {
      this._updateMapAttribution();
    },
    generateLegend(label, shape = null, markerStyle = null) {
      if (shape === null) {
        return label;
      } else {
        const legendShape = new LegendShape({
          inheritAttrs: false,
          propsData: {
            label,
            shape,
            markerStyle,
          },
        });
        legendShape.$mount();
        return legendShape.$el.outerHTML;
      }
    },
    fitBounds(dataset, pad = 0) {
      const minMaxCoords = dataset.reduce((acc, item) => {
        return acc === null
          ? {
              lat: [item.latitude, item.latitude],
              lon: [item.longitude, item.longitude],
            }
          : {
              lat: [
                Math.min(acc.lat[0], item.latitude),
                Math.max(acc.lat[1], item.latitude),
              ],
              lon: [
                Math.min(acc.lon[0], item.longitude),
                Math.max(acc.lon[1], item.longitude),
              ],
            };
      }, null);
      if (minMaxCoords) {
        const bounds = this.padBounds(
          [
            [minMaxCoords.lat[0], minMaxCoords.lon[0]],
            [minMaxCoords.lat[1], minMaxCoords.lon[1]],
          ],
          pad
        );
        this.bounds = L.latLngBounds(bounds);
      }
    },
    padBounds([bMin, bMax], perc) {
      const padLon = (bMax[1] - bMin[1]) * perc;
      const padLat = (bMax[0] - bMin[0]) * perc;
      return [
        [bMin[0] - padLat, bMin[1] - padLon],
        [bMax[0] + padLat, bMax[1] + padLon],
      ];
    },
    boundsRadius(radiusDeg) {
      return L.latLngBounds(
        [this.center.lat - radiusDeg, this.center.lng - radiusDeg],
        [this.center.lat + radiusDeg, this.center.lng + radiusDeg]
      );
    },
    _updateMapAttribution() {
      var oldAttributions = this.mapObject._esriAttributions;

      if (oldAttributions) {
        var wrappedBounds = L.latLngBounds(
          this.bounds.getSouthWest().wrap(),
          this.bounds.getNorthEast().wrap()
        );
        var zoom = this.mapObject.getZoom();

        const attribs = oldAttributions
          .filter((attribution) => {
            return (
              attribution.bounds.intersects(wrappedBounds) &&
              zoom >= attribution.minZoom &&
              zoom <= attribution.maxZoom
            );
          })
          .map((attribution) => attribution.attribution);
        this.tileProviders[0].attribution = [...new Set(attribs)].join(", ");
      }
    },
    _getAttributionData(url) {
      function reducer(acc, contrib) {
        contrib.coverageAreas.forEach((area) => {
          acc.push({
            attribution: contrib.attribution,
            score: area.score,
            minZoom: area.zoomMin,
            maxZoom: area.zoomMax,
            bounds: L.latLngBounds(
              L.latLng(area.bbox[0], area.bbox[1]),
              L.latLng(area.bbox[2], area.bbox[3])
            ),
          });
        });
        return acc;
      }
      fetch(url)
        .then((response) => response.json())
        .then((attributions) => {
          this.mapObject._esriAttributions = attributions.contributors
            .reduce(reducer, [])
            .sort((a, b) => b.score - a.score);
          this._updateMapAttribution();
        });
    },
  },
};
</script>

<style lang="less">
.map-container {
  width: 100%;
  height: 85vh;
}
.leaflet-control-layers-selector {
  cursor: pointer;
}
.btn-map-control {
  padding: 0;
}
</style>
