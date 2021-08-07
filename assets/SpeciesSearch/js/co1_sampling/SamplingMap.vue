<template>
  <leaflet-map ref="map" :data="sites" :marker-settings="markerSettings">
    <l-feature-group ref="features">
      <sampling-site-popup :site="selectedSite" />
    </l-feature-group>
    <l-layer-group layer-type="overlay" :name="legend.CO1">
      <l-circle-marker
        v-for="site in sites.filter((site) => site.has_co1)"
        :key="site.site_id"
        :lat-lng="[site.latitude, site.longitude]"
        :opacity="markerSettings.opacity"
        :fill-opacity="markerSettings.opacity"
        :radius="markerSettings.radius"
        fill-color="lime"
        :fill="Boolean(site.has_co1)"
        :weight="1"
        color="#333"
        @click="openSitePopup(site)"
      />
    </l-layer-group>
    <l-layer-group layer-type="overlay" :name="legend.biomat.internal">
      <l-circle-marker
        v-for="site in sites.filter((site) => site.int_biomat)"
        :key="site.site_id"
        :lat-lng="[site.latitude, site.longitude]"
        :opacity="markerSettings.opacity"
        :radius="borderRadius"
        :fill="false"
        :weight="weight"
        color="orangered"
        @click="openSitePopup(site)"
      />
    </l-layer-group>
    <l-layer-group layer-type="overlay" :name="legend.biomat.external">
      <l-circle-marker
        v-for="site in sites.filter((site) => site.ext_biomat)"
        :key="site.site_id"
        :lat-lng="[site.latitude, site.longitude]"
        :opacity="markerSettings.opacity"
        :radius="borderRadius"
        :fill="false"
        :weight="weight"
        color="deepskyblue"
        :dash-array="
          site.int_biomat ? radiusToDashArray(markerSettings.radius) : null
        "
        line-join="bevel"
        line-cap="butt"
        @click="openSitePopup(site)"
      />
    </l-layer-group>
    <l-layer-group layer-type="overlay" :name="legend.LMP">
      <l-polyline
        v-if="lmpCo1 !== null"
        :lat-lngs="lmpCoords(lmpCo1)"
        color="lime"
        :weight="3"
        dash-array="5, 5"
      />
      <l-polyline
        v-if="lmpBiomat !== null"
        :lat-lngs="lmpCoords(lmpBiomat)"
        color="deepskyblue"
        :weight="3"
      />
      <l-polyline
        v-if="lmpBiomat !== null"
        :lat-lngs="lmpCoords(lmpBiomat)"
        color="orangered"
        :weight="3"
        dash-array="5, 10"
      />
    </l-layer-group>
  </leaflet-map>
</template>

<i18n>
{
  "en": {
    "biomat_int": "Biomaterial (internal)",
    "biomat_ext": "Biomaterial (external)"
  },
  "fr": {
    "biomat_int": "Lot mat. (interne)",
    "biomat_ext": "Lot mat. (externe)"
  }
}
</i18n>

<script>
import LeafletMap from "~Components/maps/LeafletMap";
import {
  LLayerGroup,
  LFeatureGroup,
  LCircleMarker,
  LPopup,
  LPolyline,
  LTooltip,
} from "vue2-leaflet";
import SamplingSitePopup from "./SamplingSitePopup";
import { generateLegend } from "~Components/maps/ShapeMarkerLegend.vue";

export default {
  components: {
    LeafletMap,
    LLayerGroup,
    LFeatureGroup,
    LCircleMarker,
    LPolyline,
    SamplingSitePopup,
  },
  props: {
    sites: {
      type: Array,
      required: true,
    },
    lmpCo1: {
      type: Number,
      default: null,
    },
    lmpBiomat: {
      type: Number,
      default: null,
    },
  },
  data() {
    return {
      selectedSite: null,
      markerSettings: {
        radius: 8,
        opacity: 0.8,
      },
    };
  },
  computed: {
    weight() {
      return this.markerSettings.radius / 2;
    },
    borderRadius() {
      return this.markerSettings.radius + this.weight / 2;
    },
    legend() {
      return {
        LMP: generateLegend("LMP", "line", {
          stroke: "white",
          "stroke-dasharray": "5,5",
          lineJoin: "bevel",
          lineCap: "butt",
          "stroke-width": 3,
        }),
        CO1: generateLegend("CO1", "circle", {
          stroke: "black",
          "stroke-width": 0.5,
          fill: "lime",
        }),
        biomat: {
          external: generateLegend(this.$t("biomat_ext"), "circle", {
            stroke: "deepskyblue",
            "stroke-width": 3,
            fill: "transparent",
          }),
          internal: generateLegend(this.$t("biomat_int"), "circle", {
            stroke: "orangered",
            "stroke-width": 3,
            fill: "transparent",
          }),
        },
      };
    },
  },
  methods: {
    openSitePopup(site) {
      this.selectedSite = site;
      this.$refs.features.mapObject.openPopup([site.latitude, site.longitude]);
    },
    siteColor(site) {
      if (!site.int_biomat && !site.ext_biomat) return "grey";
      else if (site.int_biomat && site.ext_biomat) return "orangered";
      else if (site.int_biomat) return "orangered";
      else return "deepskyblue";
    },
    radiusToDashArray(radius, n = 10) {
      let length = (2 * Math.PI * radius) / n;
      return `${length},${length}`;
    },
    lmpCoords(lmp) {
      return [
        [lmp, -720],
        [lmp, 720],
      ];
    },
  },
};
</script>

<style lang="less" scoped></style>
